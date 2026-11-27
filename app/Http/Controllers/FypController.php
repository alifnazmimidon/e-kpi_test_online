<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Department;
use App\Models\KpiSetting;
use App\Models\Task;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

//Import AI

use App\Services\AIAnalysisService;
use Illuminate\Support\Facades\Cache;

class FypController extends Controller
{
    // LOGIN
    public function login(Request $request)
    {
        $request->validate([
            'worker_id' => 'required',
            'password'  => 'required',
        ]);

        $user = User::where('worker_id', $request->worker_id)->first();

        if ($user && Hash::check($request->password, $user->password_hash)) {
            Auth::login($user);
            $request->session()->regenerate();

            if ($user->role === 'HR') {
                return redirect()->route('hr.dashboard');
            } elseif ($user->role === 'LEADER') {
                return redirect()->route('leader.dashboard');
            } else {
                return redirect()->route('worker.dashboard');
            }
        }

        return back()->with('error', 'Invalid Worker ID or Password!')->withInput();
    }

    // LOGOUT
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    // PENGIRAAN SKOR TUGASAN BULANAN (TERMASUK CARRY-OVER PENDING / IN_PROGRESS)
    private function calculateMonthlyTaskScore(string $workerId, int $evalYear, int $evalMonth): float
    {
        // 1. Tugasan yang berjaya disiapkan dalam bulan penilaian semasa
        $doneTasks = DB::table('tasks_etracker')
            ->where('worker_id', $workerId)
            ->whereRaw('UPPER(TRIM(status)) = ?', ['COMPLETED'])
            ->whereRaw('YEAR(COALESCE(completion_date, synced_at)) = ?', [$evalYear])
            ->whereRaw('MONTH(COALESCE(completion_date, synced_at)) = ?', [$evalMonth])
            ->count();

        // 2. Tugasan belum siap (PENDING / IN_PROGRESS) dari bulan semasa atau bulan sebelumnya
        $carryOverTasks = DB::table('tasks_etracker')
            ->where('worker_id', $workerId)
            ->whereRaw('UPPER(TRIM(status)) != ?', ['COMPLETED'])
            ->where(function ($q) use ($evalYear, $evalMonth) {
                $q->whereRaw('YEAR(COALESCE(synced_at, completion_date, NOW())) < ?', [$evalYear])
                  ->orWhere(function ($q2) use ($evalYear, $evalMonth) {
                      $q2->whereRaw('YEAR(COALESCE(synced_at, completion_date, NOW())) = ?', [$evalYear])
                         ->whereRaw('MONTH(COALESCE(synced_at, completion_date, NOW())) <= ?', [$evalMonth]);
                  });
            })
            ->count();

        $totalTasks = $doneTasks + $carryOverTasks;

        return $totalTasks > 0 ? round(($doneTasks / $totalTasks) * 40, 2) : 0.00;
    }

    private function ensureWorkerNotificationsTable(): void
    {
        if (!Schema::hasTable('worker_notifications')) {
            Schema::create('worker_notifications', function ($table) {
                $table->id();
                $table->string('worker_id', 50)->index();
                $table->string('category', 30)->default('general'); // leave, task, kpi, profile
                $table->string('title', 150);
                $table->text('message');
                $table->string('ref_key', 120)->nullable()->index();
                $table->boolean('is_read')->default(false);
                $table->timestamps();
            });
        }
    }

    private function pushWorkerNotification(
        string $workerId,
        string $category,
        string $title,
        string $message,
        ?string $refKey = null
    ): void {
        if ($workerId === '') return;

        $this->ensureWorkerNotificationsTable();

        // Elak notifikasi pendua jika peristiwa sama sudah direkodkan
        if ($refKey !== null) {
            $alreadyExists = DB::table('worker_notifications')
                ->where('worker_id', $workerId)
                ->where('ref_key', $refKey)
                ->exists();

            if ($alreadyExists) {
                return;
            }
        }

        DB::table('worker_notifications')->insert([
            'worker_id'  => $workerId,
            'category'   => $category,
            'title'      => $title,
            'message'    => $message,
            'ref_key'    => $refKey,
            'is_read'    => false,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function syncWorkerSmartNotifications($user, $jobs, array $emergencyLeaves, float $monthlyScore, float $deptTarget): void
    {
        $workerId = (string) ($user->worker_id ?? '');
        if ($workerId === '') return;

        $this->ensureWorkerNotificationsTable();
        $ymKey = date('Y-m');

        // 0. Notifikasi Keputusan Emergency Leave (Approved / Rejected)
        foreach ($emergencyLeaves as $lv) {
            $lvId   = (int) ($lv['id'] ?? 0);
            $status = strtoupper(trim((string) ($lv['status'] ?? 'PENDING')));
            $date   = $lv['leave_date'] ?? '';
            $reason = $lv['reason'] ?? 'Emergency Leave';

            if ($lvId > 0 && $status === 'REJECTED') {
                $this->pushWorkerNotification(
                    $workerId,
                    'leave',
                    'Emergency Leave Rejected (-5%)',
                    "Your {$reason} request for {$date} was rejected by HR (-5% KPI attendance deduction applied).",
                    "leave_status_{$lvId}_rejected"
                );
            } elseif ($lvId > 0 && $status === 'APPROVED') {
                $this->pushWorkerNotification(
                    $workerId,
                    'leave',
                    'Emergency Leave Approved',
                    "Your {$reason} request for {$date} was approved by HR (No KPI deduction).",
                    "leave_status_{$lvId}_approved"
                );
            }
        }

        // 1. Notifikasi Tugasan Baharu / Siap daripada tasks_etracker
        foreach ($jobs->take(5) as $job) {
            $jobId    = $job->task_id ?? $job->id ?? md5($job->project_name ?? $job->task_name ?? '');
            $jobTitle = $job->project_name ?? $job->task_name ?? $job->title ?? 'Assigned Project';
            $status   = strtoupper(trim((string) ($job->status ?? '')));

            if ($status === 'COMPLETED') {
                $this->pushWorkerNotification(
                    $workerId,
                    'task',
                    'Project Completed',
                    "Task \"{$jobTitle}\" has been marked as Completed and counted toward your KPI.",
                    "task_done_{$jobId}"
                );
            } else {
                $this->pushWorkerNotification(
                    $workerId,
                    'task',
                    'Task Assigned / Active',
                    "You have an active task: \"{$jobTitle}\" ({$job->status}).",
                    "task_active_{$jobId}_{$status}"
                );
            }
        }

        // 2. Notifikasi Pencapaian / Amaran Skor KPI Bulanan
        if ($monthlyScore >= 90) {
            $this->pushWorkerNotification(
                $workerId,
                'kpi',
                'Gold Tier KPI Achieved',
                "Great work! Your current KPI score is {$monthlyScore}% (above the 90% Gold Tier benchmark).",
                "kpi_gold_{$ymKey}"
            );
        } elseif ($monthlyScore < $deptTarget) {
            $this->pushWorkerNotification(
                $workerId,
                'kpi',
                'KPI Below Department Target',
                "Your current KPI score is {$monthlyScore}%, slightly below your department target ({$deptTarget}%).",
                "kpi_low_{$ymKey}"
            );
        }

        // 3. Notifikasi Jawatan Semasa
        if (!empty($user->position)) {
            $posSlug = strtolower(preg_replace('/[^a-zA-Z0-9]+/', '_', $user->position));
            $this->pushWorkerNotification(
                $workerId,
                'profile',
                'Current Role Verified',
                "Your active position is listed as {$user->position}.",
                "pos_{$posSlug}"
            );
        }
    }

    // PAPARAN DASHBOARD STAFF / WORKER
    public function workerProfile()
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login');
        }

        $evalYear       = (int) date('Y');
        $evalMonth      = (int) date('n');
        $currentQuarter = 'Q' . (int) ceil($evalMonth / 3);

        // Ambil semua sejarah tugasan pekerja dari tasks_etracker (tugasan belum siap dipaparkan di atas)
        $jobs = DB::table('tasks_etracker')
            ->where('worker_id', $user->worker_id)
            ->orderByRaw("CASE WHEN UPPER(TRIM(status)) = 'COMPLETED' THEN 1 ELSE 0 END ASC")
            ->orderByDesc(DB::raw('COALESCE(completion_date, synced_at)'))
            ->get()
            ->map(function ($job) {
                $normalized = strtoupper(str_replace('_', ' ', trim($job->status ?? '')));
                $job->status = match ($normalized) {
                    'COMPLETED'   => 'Completed',
                    'IN PROGRESS' => 'In Progress',
                    'PENDING'     => 'Pending',
                    default       => $job->status,
                };
                return $job;
            });

        $currentEval = DB::table('monthly_evaluations')
            ->where('worker_id', $user->worker_id)
            ->where('evaluation_month', $evalMonth)
            ->where('evaluation_year', $evalYear)
            ->first();

        $calcTaskScore    = round($this->calculateMonthlyTaskScore($user->worker_id, $evalYear, $evalMonth), 1);
        $monthlyUserScore = $currentEval
            ? (float) $currentEval->total_score
            : round($calcTaskScore + 20.0, 1);

        $deptTarget = Department::where('department_name', $user->department?->department_name ?? '')
            ->where('quarter', $currentQuarter)
            ->value('target_score') ?? ($user->department?->target_score ?? 80);

        $kpiScores = (object) [
            'user_score'    => $monthlyUserScore,
            'dept_score'    => $deptTarget,
            'company_score' => 85,
        ];

        $setting = KpiSetting::first();

        // Ambil sejarah permohonan Emergency Leave pekerja semasa
        $elMeta      = $this->getEmergencyLeaveMeta();
        $elPk        = $elMeta['pk'];
        $elDateCol   = $elMeta['dateCol'];
        $elStatusCol = $elMeta['statusCol'];
        $elNotesCol  = $elMeta['notesCol'];
        $elImpactCol = $elMeta['impactCol'];

        $emergencyLeaves = DB::table('emergency_leaves')
            ->where('worker_id', $user->worker_id)
            ->orderByDesc($elPk)
            ->get()
            ->map(function ($lv) use ($elPk, $elDateCol, $elStatusCol, $elNotesCol, $elImpactCol) {
                $rawDate     = ($elDateCol && !empty($lv->$elDateCol)) ? $lv->$elDateCol : '';
                $statusUpper = strtoupper(trim((string) ($lv->$elStatusCol ?? '')));
                if ($statusUpper === '') {
                    $statusUpper = 'PENDING';
                }

                $rawImpact = strtoupper(trim((string) ($elImpactCol ? ($lv->$elImpactCol ?? '') : '')));

                if ($statusUpper === 'PENDING') {
                    $impactVal = 'Pending HR Review';
                } elseif ($statusUpper === 'REJECTED' || str_contains($rawImpact, '5') || str_contains($rawImpact, 'DEDUCTED')) {
                    $impactVal = '-5% Deducted';
                } elseif ($statusUpper === 'APPROVED' || $rawImpact === 'NO_DEDUCTION' || $rawImpact === 'NO DEDUCT') {
                    $impactVal = 'No Deduction';
                } else {
                    $impactVal = ucwords(strtolower(str_replace('_', ' ', $rawImpact ?: 'Pending HR Review')));
                }

                return [
                    'id'         => (int) ($lv->$elPk ?? 0),
                    'leave_date' => substr((string) $rawDate, 0, 10),
                    'reason'     => $lv->reason ?? 'Emergency Leave',
                    'notes'      => $elNotesCol ? ($lv->$elNotesCol ?? '') : '',
                    'status'     => $statusUpper,
                    'impact'     => $impactVal,
                ];
            })
            ->values()
            ->all();

        // Segerakkan & ambil notifikasi peribadi pekerja
        $this->syncWorkerSmartNotifications($user, $jobs, $emergencyLeaves, (float) $monthlyUserScore, (float) $deptTarget);

        $workerNotifications = DB::table('worker_notifications')
            ->where('worker_id', $user->worker_id)
            ->orderByDesc('id')
            ->limit(15)
            ->get()
            ->map(function ($n) {
                return [
                    'id'       => (int) $n->id,
                    'category' => $n->category,
                    'title'    => $n->title,
                    'message'  => $n->message,
                    'is_read'  => (bool) $n->is_read,
                    'time_ago' => \Carbon\Carbon::parse($n->created_at)->diffForHumans(),
                ];
            })
            ->values()
            ->all();

        return view('worker', compact(
            'user',
            'kpiScores',
            'jobs',
            'setting',
            'emergencyLeaves',
            'workerNotifications'
        ));
    }

    // MARK AS READ

    public function markWorkerNotificationsRead(Request $request)
    {
        $user = Auth::user();
        if (!$user || empty($user->worker_id)) {
            return response()->json(['status' => 'error'], 401);
        }

        $this->ensureWorkerNotificationsTable();

        $query = DB::table('worker_notifications')
            ->where('worker_id', $user->worker_id)
            ->where('is_read', false);

        if ($request->filled('id')) {
            $query->where('id', (int) $request->input('id'));
        }

        $query->update([
            'is_read'    => true,
            'updated_at' => now(),
        ]);

        return response()->json(['status' => 'success']);
    }

    // PEKERJA HANTAR PERMOHONAN EMERGENCY LEAVE
    public function submitWorkerEmergencyLeave(Request $request)
    {
        $request->merge([
            'leave_date' => $request->input('leave_date') ?? $request->input('leaveDate') ?? $request->input('date'),
            'reason'     => trim((string) ($request->input('reason') ?? 'Emergency Leave')),
            'notes'      => trim((string) (
                $request->input('notes')
                ?? $request->input('other_reason')
                ?? $request->input('remarks')
                ?? $request->input('description')
                ?? ''
            )),
        ]);

        $validated = $request->validate([
            'leave_date' => 'required|date',
            'reason'     => 'required|string|max:100',
            'notes'      => 'nullable|string|max:300',
        ]);

        $user = Auth::user();
        if (!$user || empty($user->worker_id)) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Worker profile not found.'
            ], 403);
        }

        $meta      = $this->getEmergencyLeaveMeta();
        $columns   = $meta['columns'];
        $pk        = $meta['pk'];
        $dateCol   = $meta['dateCol'];
        $statusCol = $meta['statusCol'];
        $notesCol  = $meta['notesCol'];
        $impactCol = $meta['impactCol'];

        if ($dateCol) {
            $exists = DB::table('emergency_leaves')
                ->where('worker_id', $user->worker_id)
                ->where($dateCol, $validated['leave_date'])
                ->exists();

            if ($exists) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'You have already submitted an emergency leave request for this date.'
                ], 422);
            }
        }

        $payload = [
            'worker_id' => $user->worker_id,
            'reason'    => $validated['reason'],
            $statusCol  => 'PENDING',
        ];
        if ($dateCol)                         $payload[$dateCol]     = $validated['leave_date'];
        if ($notesCol)                        $payload[$notesCol]    = $validated['notes'] ?? '';
        if ($impactCol)                       $payload[$impactCol]   = 'Pending Review';
        if (in_array('created_at', $columns)) $payload['created_at'] = now();
        if (in_array('updated_at', $columns)) $payload['updated_at'] = now();

        $newId = (int) DB::table('emergency_leaves')->insertGetId($payload);

        $record = [
            'id'         => $newId,
            'leave_date' => $validated['leave_date'],
            'leaveDate'  => $validated['leave_date'],
            'reason'     => $validated['reason'],
            'notes'      => $validated['notes'] ?? '',
            'status'     => 'PENDING',
            'impact'     => 'Pending HR Review',
        ];

        $allLeaves = DB::table('emergency_leaves')
            ->where('worker_id', $user->worker_id)
            ->orderByDesc($pk)
            ->get()
            ->map(function ($lv) use ($pk, $dateCol, $statusCol, $notesCol, $impactCol) {
                $rawDate     = ($dateCol && !empty($lv->$dateCol)) ? $lv->$dateCol : '';
                $statusUpper = strtoupper(trim((string) ($lv->$statusCol ?? '')));
                if ($statusUpper === '') {
                    $statusUpper = 'PENDING';
                }

                $rawImpact = strtoupper(trim((string) ($impactCol ? ($lv->$impactCol ?? '') : '')));
                if ($statusUpper === 'PENDING') {
                    $impactVal = 'Pending HR Review';
                } elseif ($statusUpper === 'REJECTED' || str_contains($rawImpact, '5') || str_contains($rawImpact, 'DEDUCTED')) {
                    $impactVal = '-5% Deducted';
                } else {
                    $impactVal = 'No Deduction';
                }

                return [
                    'id'         => (int) ($lv->$pk ?? 0),
                    'leave_date' => substr((string) $rawDate, 0, 10),
                    'reason'     => $lv->reason ?? 'Emergency Leave',
                    'notes'      => $notesCol ? ($lv->$notesCol ?? '') : '',
                    'status'     => $statusUpper,
                    'impact'     => $impactVal,
                ];
            })
            ->values()
            ->all();

        return response()->json([
            'status'  => 'success',
            'message' => 'Emergency leave request submitted to HR.',
            'record'  => $record,
            'leave'   => $record,
            'leaves'  => $allLeaves,
        ]);
    }

    public function updateProfilePhoto(Request $request)
    {
        $request->validate([
            'photo' => 'required|image|mimes:jpeg,png,jpg,webp|max:3072',
        ]);

        $user = Auth::user();
        if (!$user) {
            return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 401);
        }

        $dir = public_path('uploads/profiles');
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        if ($user->profile_photo && file_exists(public_path($user->profile_photo))) {
            @unlink(public_path($user->profile_photo));
        }

        $ext      = $request->file('photo')->getClientOriginalExtension();
        $filename = $user->worker_id . '_' . time() . '.' . $ext;
        $request->file('photo')->move($dir, $filename);

        $relativePath = 'uploads/profiles/' . $filename;
        User::where('worker_id', $user->worker_id)->update([
            'profile_photo' => $relativePath,
        ]);

        return response()->json([
            'status'    => 'success',
            'photo_url' => asset($relativePath),
        ]);
    }

    // PAPARAN DASHBOARD HR
    public function hrDashboard(Request $request)
    {
        $avgKpiScore = round(User::avg('kpi_score') ?? 0, 1);

        $allDepts = Department::all();
        $deptNamesById = $allDepts->pluck('department_name', 'department_id');

        $unfinishedTasksByWorker = Schema::hasTable('tasks_etracker')
            ? DB::table('tasks_etracker')
                ->whereRaw('UPPER(TRIM(status)) != ?', ['COMPLETED'])
                ->select('worker_id', DB::raw('COUNT(*) as total'))
                ->groupBy('worker_id')
                ->pluck('total', 'worker_id')
            : collect();

        $kpiSetting   = KpiSetting::first();
        $tasksWeight  = (float) ($kpiSetting->tasks_weightage ?? 40);
        $attWeight    = (float) ($kpiSetting->attendance_weightage ?? 20);
        $leaderWeight = (float) ($kpiSetting->leader_weightage ?? 40);

        $rawOverloaded = User::whereIn(DB::raw('UPPER(TRIM(role))'), ['STAFF', 'WORKER'])
            ->where('kpi_score', '<', 75)
            ->orderBy('kpi_score', 'asc')
            ->get()
            ->map(function ($u) use ($deptNamesById, $unfinishedTasksByWorker, $tasksWeight, $attWeight, $leaderWeight) {
                $raw = $u->profile_photo;
                $photoUrl = null;
                if (!empty($raw)) {
                    $clean = ltrim($raw, '/');
                    $photoUrl = (str_starts_with($raw, 'http://') || str_starts_with($raw, 'https://') || str_starts_with($raw, '/'))
                        ? $raw
                        : (str_starts_with($clean, 'uploads/') || str_starts_with($clean, 'storage/')
                            ? asset($clean)
                            : asset('storage/' . $clean));
                }

                $deptName  = strtoupper(trim($deptNamesById[$u->department_id] ?? 'GENERAL'));
                $pending   = (int) ($unfinishedTasksByWorker[$u->worker_id] ?? 0);

                $latestEval = DB::table('monthly_evaluations')
                    ->where('worker_id', $u->worker_id)
                    ->orderByDesc('evaluation_year')
                    ->orderByDesc('evaluation_month')
                    ->first();

                $issueTag = 'KPI Below 75%';
                if ($pending >= 3 || ($latestEval && ((float) $latestEval->tasks_score / max(1, $tasksWeight)) < 0.70)) {
                    $issueTag = 'Low Task Completion';
                } elseif ($latestEval && ((float) $latestEval->attendance_score / max(1, $attWeight)) < 0.85) {
                    $issueTag = 'Attendance Deduction';
                } elseif ($latestEval && (float) $latestEval->leader_score > 0 && ((float) $latestEval->leader_score / max(1, $leaderWeight)) < 0.70) {
                    $issueTag = 'Low Leader Rating';
                }

                return [
                    'worker_id'        => $u->worker_id,
                    'full_name'        => $u->full_name,
                    'department'       => $deptName,
                    'kpi_score'        => round((float) ($u->kpi_score ?? 0), 1),
                    'unfinished_tasks' => $pending,
                    'issue_tag'        => $issueTag,
                    'profile_photo'    => $photoUrl,
                ];
            });

        $unhealthyStaffCount = $rawOverloaded->count();

        // Asingkan pekerja overload mengikut Jabatan + dapatkan info Leader jabatan
        $overloadedDepartments = $rawOverloaded->groupBy('department')->map(function ($staffGroup, $deptName) use ($allDepts) {
            $matchingDeptIds = $allDepts
                ->filter(fn($d) => strtoupper(trim($d->department_name)) === $deptName)
                ->pluck('department_id')
                ->all();

            $leader = User::whereIn('department_id', $matchingDeptIds)
                ->whereRaw('UPPER(TRIM(role)) = ?', ['LEADER'])
                ->first();

            return [
                'department'   => $deptName,
                'leader_id'    => $leader->worker_id ?? '',
                'leader_name'  => $leader->full_name ?? 'No Leader Assigned',
                'leader_email' => $leader->email ?? '',
                'staff_count'  => $staffGroup->count(),
                'staff'        => $staffGroup->values()->all(),
            ];
        })->values()->all();

        $evalYear       = (int) date('Y');
        $evalMonth      = (int) date('n');
        $qNum           = (int) ceil($evalMonth / 3);
        $currentQuarter = 'Q' . $qNum;
        $prevQuarter    = 'Q' . ($qNum === 1 ? 4 : $qNum - 1);
        $quarterMonths  = [
            1 => [1, 2, 3],
            2 => [4, 5, 6],
            3 => [7, 8, 9],
            4 => [10, 11, 12],
        ][$qNum];

        $targetAchievement = round(Department::where('quarter', $currentQuarter)->avg('target_score') ?? 0, 1);

        $prevQAvg = Department::where('quarter', $prevQuarter)->avg('target_score') ?? 0;
        $kpiTrendDiff = round($targetAchievement - $prevQAvg, 1);

        $departmentsPerformance = Department::where('quarter', $currentQuarter)->get()->map(function ($dept) use ($evalMonth, $evalYear, $quarterMonths) {
            $allDeptIdsWithSameName = Department::where('department_name', $dept->department_name)
                                                ->pluck('department_id');

            // Kira staf yang dinilai sahaja (STAFF / WORKER), tidak termasuk HR & LEADER
            $staffQuery = User::whereIn('department_id', $allDeptIdsWithSameName)
                            ->whereIn(DB::raw('UPPER(role)'), ['STAFF', 'WORKER']);

            $staffCount = (clone $staffQuery)->count();
            $workerIds  = (clone $staffQuery)->pluck('worker_id');

            // Ambil laporan bulanan yang dihantar oleh Leader ke HR dalam quarter semasa
            $deptSubmissions = DB::table('hr_submissions')
                ->where(DB::raw('UPPER(department_name)'), strtoupper(trim($dept->department_name)))
                ->where('evaluation_year', $evalYear)
                ->whereIn('evaluation_month', $quarterMonths)
                ->get();

            // Ambil rekod penilaian bulanan dalam quarter semasa
            $quarterEvals = DB::table('monthly_evaluations')
                ->whereIn('worker_id', $workerIds)
                ->where('evaluation_year', $evalYear)
                ->whereIn('evaluation_month', $quarterMonths)
                ->where('evaluation_status', '!=', 'PENDING')
                ->whereNotNull('submitted_by_leader')
                ->get();

            $currentMonthEvals = $quarterEvals->where('evaluation_month', $evalMonth);
            $hasCurrentSub     = $deptSubmissions->where('evaluation_month', $evalMonth)->isNotEmpty();

            $submittedCount = $hasCurrentSub
                ? $staffCount
                : $currentMonthEvals->pluck('worker_id')->unique()->count();
            $isComplete     = ($staffCount === 0) || ($submittedCount >= $staffCount);

            // Purata KPI Quarter berdasarkan laporan bulanan yang dihantar ke HR atau penilaian dalam quarter
            if ($deptSubmissions->isNotEmpty()) {
                $avgKpi = $deptSubmissions->avg('dept_avg_score');
            } elseif ($quarterEvals->isNotEmpty()) {
                $avgKpi = $quarterEvals->avg('total_score');
            } else {
                $avgKpi = (clone $staffQuery)->avg('kpi_score') ?? $dept->target_score;
            }

            // Kira penerima EOM (GOLD & SILVER) terkumpul sepanjang quarter semasa
            $goldCount   = $quarterEvals->where('eom_badge', 'GOLD')->count();
            $silverCount = $quarterEvals->where('eom_badge', 'SILVER')->count();

            // Cari Leader jabatan (jika status PENDING) atau staf skor terendah (jika prestasi rendah)
            $deptLeaderId = User::whereIn('department_id', $allDeptIdsWithSameName)
                ->whereRaw('UPPER(role) = ?', ['LEADER'])
                ->value('worker_id');

            $lowestStaffId = (clone $staffQuery)
                ->orderBy('kpi_score', 'asc')
                ->value('worker_id');

            $targetWorkerId = !$isComplete
                ? ($deptLeaderId ?? $lowestStaffId)
                : ($lowestStaffId ?? $deptLeaderId);

            return (object) [
                'department_name'   => $dept->department_name,
                'avg_kpi'           => round($avgKpi, 1),
                'total_staff'       => $staffCount,
                'submitted'         => $submittedCount,
                'total_submissions' => $staffCount,
                'status'            => $isComplete ? 'COMPLETE' : 'PENDING',
                'gold_badges'       => $goldCount,
                'silver_badges'     => $silverCount,
                'target_worker_id'  => $targetWorkerId,
            ];
        });

        

        $selectedDeptName = $request->get('dept_filter', 'WEBTECH');
        $matchingDeptIds = Department::where('department_name', 'LIKE', '%' . $selectedDeptName . '%')
                                    ->pluck('department_id');

        $badgeRankings = User::whereIn('department_id', $matchingDeptIds)
                            ->whereIn('role', ['STAFF', 'LEADER'])
                            ->orderBy('kpi_score', 'desc')
                            ->take(5)
                            ->get()
                            ->map(function ($user) {
                                $raw = $user->profile_photo;
                                $photoUrl = null;
                                if (!empty($raw)) {
                                    $clean = ltrim($raw, '/');
                                    $photoUrl = (str_starts_with($raw, 'http://') || str_starts_with($raw, 'https://') || str_starts_with($raw, '/'))
                                        ? $raw
                                        : (str_starts_with($clean, 'uploads/') || str_starts_with($clean, 'storage/')
                                            ? asset($clean)
                                            : asset('storage/' . $clean));
                                }

                                return (object) [
                                    'name'          => $user->full_name,
                                    'kpi_score'     => $user->kpi_score ?? 0,
                                    'profile_photo' => $photoUrl,
                                ];
                            });

        $quarters = ['Q1', 'Q2', 'Q3', 'Q4'];
        $kpiTrend = [];
        foreach ($quarters as $q) {
            $kpiTrend[] = round(Department::where('quarter', $q)->avg('target_score') ?? 0, 1);
        }

        $topWorker = User::orderBy('kpi_score', 'desc')->first();
        $topDept   = Department::where('quarter', $currentQuarter)->orderBy('target_score', 'desc')->first();

        // Simpan cache selama 6 jam supaya dashboard kekal laju tanpa tunggu API setiap kali refresh
        $aiPayload = Cache::remember('hr_ai_overview_insights', now()->addHours(6), function () {
            return app(AIAnalysisService::class)->analyzeHrOverview();
        });

        $aiSource = $aiPayload['source'] ?? 'fallback';

        // Padankan kunci JSON daripada Gemini (summary & recommendation) ke format kad UI (desc & stat)
        $aiInsights = collect($aiPayload['insights'] ?? [])->map(function ($item) {
            return [
                'tag'   => $item['tag'] ?? 'AI Insight',
                'title' => $item['title'] ?? 'Performance Update',
                'desc'  => $item['summary'] ?? ($item['desc'] ?? ''),
                'stat'  => !empty($item['recommendation'])
                    ? 'Action: ' . $item['recommendation']
                    : ($item['stat'] ?? ''),
            ];
        })->values()->all();

        if (empty($aiInsights)) {
            $aiInsights = [
                [
                    'tag'   => 'AI Recommendation',
                    'title' => ($topWorker->full_name ?? 'Staff') . ' (' . ($selectedDeptName) . ')',
                    'desc'  => 'Recommended for the Excellence Award based on the highest KPI score in the database.',
                    'stat'  => ($topWorker->kpi_score ?? 0) . '% Efficiency Rating',
                ],
            ];
        }

        return view('hr', compact(
            'avgKpiScore',
            'unhealthyStaffCount',
            'overloadedDepartments',
            'targetAchievement',
            'kpiTrendDiff',
            'departmentsPerformance',
            'badgeRankings',
            'selectedDeptName',
            'kpiTrend',
            'aiInsights',
            'aiSource'
        ));
    }

    // JANA SEMULA ANALISIS AI SECARA LANGSUNG (BUTANG REFRESH)
    public function refreshAiInsights(Request $request, AIAnalysisService $aiService)
    {
        $variation = (int) $request->input('variation', 0);
        if ($variation <= 0) {
            $variation = ((int) session('hr_ai_variation', 1)) + 1;
        }
        session(['hr_ai_variation' => $variation]);

        Cache::forget('hr_ai_overview_insights');

        $aiPayload = $aiService->analyzeHrOverview($variation);
        $ttl = ($aiPayload['source'] ?? '') === 'gemini-ai' ? now()->addHours(6) : now()->addSeconds(30);
        Cache::put('hr_ai_overview_insights', $aiPayload, $ttl);

        $aiSource = $aiPayload['source'] ?? 'fallback';

        $aiInsights = collect($aiPayload['insights'] ?? [])->map(function ($item) {
            return [
                'tag'     => $item['tag'] ?? 'AI Insight',
                'title'   => $item['title'] ?? 'Performance Update',
                'desc'    => $item['summary'] ?? ($item['desc'] ?? ''),
                'details' => $item['details'] ?? ($item['summary'] ?? ($item['desc'] ?? '')),
                'stat'    => !empty($item['recommendation'])
                    ? 'Action: ' . $item['recommendation']
                    : ($item['stat'] ?? ''),
            ];
        })->values()->all();

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'status'     => 'success',
                'aiSource'   => $aiSource,
                'aiInsights' => $aiInsights,
            ]);
        }

        $sourceLabel = $aiSource === 'gemini-ai' ? 'Gemini AI Live' : 'Smart Fallback';

        return back()->with('success', "AI Insights updated ({$sourceLabel}).");
    }

   // JANA DRAF EMEL PERINGATAN AUTOMATIK BERBANTUKAN AI
    public function generateAiReminderDraft(Request $request, AIAnalysisService $aiService)
    {
        $mode      = (string) $request->input('mode', 'default');
        $variation = (int) $request->input('variation', 1);

        if ($mode === 'department_overload') {
            $request->validate([
                'department' => 'required|string',
                'variation'  => 'nullable|integer|min:1|max:20',
            ]);

            $draft = $aiService->generateDepartmentOverloadDraft(
                $request->input('department'),
                $variation
            );

            return response()->json([
                'status' => 'success',
                'draft'  => $draft,
            ]);
        }

        $request->validate([
            'worker_id' => 'required|string',
            'variation' => 'nullable|integer|min:1|max:20',
        ]);

        $draft = $aiService->generateReminderDraft(
            $request->worker_id,
            $variation
        );

        return response()->json([
            'status' => 'success',
            'draft'  => $draft,
        ]);
    }

    public function kpiManagement(Request $request)
    {
        $selectedQuarter = $request->get('quarter', 'Q3');

        $masterDepartments = ['WebTech', 'R&D', 'Video Production', 'Graphic Design'];

        $existingRecords = Department::where('quarter', $selectedQuarter)
                            ->get()
                            ->keyBy('department_name');

        $departments = collect($masterDepartments)->map(function ($deptName) use ($existingRecords, $selectedQuarter) {
            if ($existingRecords->has($deptName)) {
                return $existingRecords->get($deptName);
            }

            return (object) [
                'department_id'   => null,
                'department_name' => $deptName,
                'target_score'    => 80,
                'assigned_by'     => 'HR Manager',
                'status'          => 'Pending',
                'quarter'         => $selectedQuarter
            ];
        });

        $setting = KpiSetting::first();

        return view('kpi-management', compact('departments', 'setting', 'selectedQuarter'));
    }

    public function updateFormula(Request $request)
    {
        KpiSetting::where('setting_id', 1)->update([
            'tasks_weightage'      => $request->input('tasks_weightage'),
            'attendance_weightage' => $request->input('attendance_weightage'),
            'leader_weightage'     => $request->input('leader_weightage'),
        ]);

        return back()->with('success', 'Weightage Formula updated successfully!');
    }

    public function updateThresholds(Request $request)
    {
        KpiSetting::where('setting_id', 1)->update([
            'gold_threshold'   => $request->input('gold_threshold'),
            'silver_threshold' => $request->input('silver_threshold'),
            'bronze_threshold' => $request->input('bronze_threshold'),
        ]);

        return back()->with('success', 'Badge thresholds updated successfully!');
    }

    public function updateTarget(Request $request)
    {
        $deptName        = $request->input('department_name');
        $selectedQuarter = $request->input('quarter', 'Q' . (int) ceil(date('n') / 3));
        $targets         = $request->input('targets');
        $newScore        = $targets[$deptName] ?? null;

        if ($deptName && $newScore !== null) {
            Department::updateOrCreate(
                [
                    'department_name' => $deptName,
                    'quarter'         => $selectedQuarter
                ],
                [
                    'target_score' => $newScore,
                    'assigned_by'  => 'HR Manager',
                    'status'       => 'Assigned'
                ]
            );
        }

        return back()->with('success', 'Target score updated successfully!');
    }

    // =========================================================================
    // PAPARAN HR - REPORT (SAMBUNGAN DATABASE MYSQL)
    // =========================================================================
    public function hrReport()
    {
        $rows = DB::table('reports_archive')
            ->orderByDesc('report_id')
            ->get();

        $typeReverseMap = [
            'DEPT_SUMMARY'      => ['type' => 'dept_summary',      'label' => 'Quarterly Department Performance Summary'],
            'EOM_WINNERS'       => ['type' => 'individual_audit',  'label' => 'Employee Individual KPI Evaluation Audit'],
            'LEAVE_AUDIT'       => ['type' => 'attendance_impact', 'label' => 'Emergency Leave & Attendance Impact'],
            'EXECUTIVE_SUMMARY' => ['type' => 'workload_health',   'label' => 'Overload & Workload Health Analysis'],
        ];

        $archives = $rows->map(function ($row) use ($typeReverseMap) {
            $metaPath = storage_path('app/reports/' . $row->report_title . '.json');
            $meta = file_exists($metaPath) ? json_decode(file_get_contents($metaPath), true) : [];
            $mapped = $typeReverseMap[$row->report_type] ?? ['type' => 'dept_summary', 'label' => $row->report_type];
            $extFormat = strtoupper(pathinfo($row->report_title, PATHINFO_EXTENSION) ?: $row->file_format);

            return [
                'id'        => (int) $row->report_id,
                'title'     => $row->report_title,
                'type'      => $meta['type'] ?? $mapped['type'],
                'typeName'  => $meta['typeName'] ?? $mapped['label'],
                'createdAt' => \Carbon\Carbon::parse($row->generated_at)->toIso8601String(),
                'format'    => $extFormat,
                'bytes'     => (int) ($meta['bytes'] ?? 1572864),
                'dept'      => $meta['dept'] ?? 'ALL',
                'deptLabel' => $meta['deptLabel'] ?? 'ALL DEPARTMENTS',
                'quarter'   => $meta['quarter'] ?? 'Q3 2026',
                'snapshot'  => $meta['snapshot'] ?? [
                    'headers' => ['Report Title', 'Type', 'Format', 'Generated By', 'Generated At'],
                    'rows'    => [[$row->report_title, $row->report_type, $row->file_format, $row->generated_by, $row->generated_at]],
                ],
            ];
        })->values()->all();

        $totalGenerated  = count($archives);
        $storageBytes    = collect($archives)->sum('bytes');
        $lastGeneratedAt = !empty($archives) ? $archives[0]['createdAt'] : null;

        return view('hrreport', compact(
            'archives',
            'totalGenerated',
            'storageBytes',
            'lastGeneratedAt'
        ));
    }

    // JANA LAPORAN DARI DATA MYSQL SEBENAR
    public function generateReport(Request $request)
    {
        $defaultQuarterKey = 'Q' . (int) ceil(date('n') / 3) . '-' . date('Y');
        $type       = $request->input('reportType', 'dept_summary');
        $quarterKey = $request->input('quarter', $defaultQuarterKey);
        $deptCode   = $request->input('dept', 'ALL');
        $format     = strtolower($request->input('format', 'pdf'));

        $parts       = explode('-', $quarterKey);
        $quarter     = $parts[0] ?? ('Q' . (int) ceil(date('n') / 3));
        $reportYear  = (int) ($parts[1] ?? date('Y'));
        $quarterMap  = [
            'Q1' => [1, 2, 3],
            'Q2' => [4, 5, 6],
            'Q3' => [7, 8, 9],
            'Q4' => [10, 11, 12],
        ];
        $qMonths     = $quarterMap[$quarter] ?? [10, 11, 12];
        $monthNames  = [1=>'Jan',2=>'Feb',3=>'Mar',4=>'Apr',5=>'May',6=>'Jun',7=>'Jul',8=>'Aug',9=>'Sep',10=>'Oct',11=>'Nov',12=>'Dec'];

        $deptMap = [
            'WEBTECH' => 'WebTech',
            'RND'     => 'R&D',
            'VIDEO'   => 'Video Production',
            'GRAPHIC' => 'Graphic Design',
        ];

        $typeLabels = [
            'dept_summary'      => 'Quarterly Department Performance Summary',
            'individual_audit'  => 'Employee Individual KPI Evaluation Audit',
            'attendance_impact' => 'Emergency Leave & Attendance Impact',
            'workload_health'   => 'Overload & Workload Health Analysis',
        ];

        $deptLabels = [
            'ALL'     => 'ALL DEPARTMENTS',
            'WEBTECH' => 'Webtech',
            'RND'     => 'R&D',
            'VIDEO'   => 'Video Production',
            'GRAPHIC' => 'Graphic Design',
        ];

        $deptQuery = Department::where('quarter', $quarter);
        if ($deptCode !== 'ALL' && isset($deptMap[$deptCode])) {
            $deptQuery->where('department_name', 'LIKE', '%' . $deptMap[$deptCode] . '%');
        }
        $departments = $deptQuery->get();

        if ($departments->isEmpty()) {
            $fallbackQuery = Department::query();
            if ($deptCode !== 'ALL' && isset($deptMap[$deptCode])) {
                $fallbackQuery->where('department_name', 'LIKE', '%' . $deptMap[$deptCode] . '%');
            }
            $departments = $fallbackQuery->get()->unique('department_name');
        }

        $headers = [];
        $rows = [];

        if ($type === 'individual_audit') {
            $headers = ['Employee', 'Department', 'Role', 'Quarter Avg KPI', 'Months Evaluated', 'Status'];

            $userQuery = User::where('role', '!=', 'HR');
            if ($deptCode !== 'ALL' && isset($deptMap[$deptCode])) {
                $matchingIds = Department::where('department_name', 'LIKE', '%' . $deptMap[$deptCode] . '%')
                    ->pluck('department_id');
                $userQuery->whereIn('department_id', $matchingIds);
            }

            $users = $userQuery->orderBy('full_name', 'asc')->get();
            foreach ($users as $u) {
                $deptObj = Department::where('department_id', $u->department_id)->first();
                $qEvals   = DB::table('monthly_evaluations')
                    ->where('worker_id', $u->worker_id)
                    ->where('evaluation_year', $reportYear)
                    ->whereIn('evaluation_month', $qMonths)
                    ->where('evaluation_status', '!=', 'PENDING')
                    ->get();

                $evalMonthsCount = $qEvals->pluck('evaluation_month')->unique()->count();
                $score  = $qEvals->isNotEmpty()
                    ? round((float) $qEvals->avg('total_score'), 1)
                    : (float) ($u->kpi_score ?? 0);
                $status = $score >= 85 ? 'PASSED' : ($score >= 70 ? 'GOOD' : 'WATCH');

                $rows[] = [
                    $u->full_name,
                    $deptObj->department_name ?? 'General',
                    $u->role,
                    number_format($score, 1) . '%',
                    $evalMonthsCount . '/3 Months',
                    $status
                ];
            }
        } elseif ($type === 'attendance_impact') {
            $headers = ['Employee', 'Department', 'Leave / Reason', 'KPI Impact', 'Status'];

            if (Schema::hasTable('emergency_leaves')) {
                $leaves = DB::table('emergency_leaves')
                    ->whereRaw('YEAR(COALESCE(leave_date, created_at)) = ?', [$reportYear])
                    ->whereRaw('MONTH(COALESCE(leave_date, created_at)) IN (' . implode(',', $qMonths) . ')')
                    ->get();
                foreach ($leaves as $lv) {
                    $u = User::where('worker_id', $lv->worker_id ?? '')->first();
                    $deptObj = $u ? Department::where('department_id', $u->department_id)->first() : null;
                    $deptName = $deptObj->department_name ?? 'General';

                    if ($deptCode !== 'ALL' && isset($deptMap[$deptCode]) && stripos($deptName, $deptMap[$deptCode]) === false) {
                        continue;
                    }

                    $st = strtoupper($lv->status ?? 'PENDING');
                    $rows[] = [
                        $u->full_name ?? ($lv->worker_id ?? 'Staff'),
                        $deptName,
                        $lv->reason ?? 'Emergency Leave',
                        $st === 'APPROVED' ? 'No deduction' : '-5%',
                        $st === 'APPROVED' ? 'APPROVED' : ($st === 'REJECTED' ? 'DEDUCTED' : 'WATCH')
                    ];
                }
            }

            if (empty($rows)) {
                $userQuery = User::where('role', '!=', 'HR');
                if ($deptCode !== 'ALL' && isset($deptMap[$deptCode])) {
                    $matchingIds = Department::where('department_name', 'LIKE', '%' . $deptMap[$deptCode] . '%')
                        ->pluck('department_id');
                    $userQuery->whereIn('department_id', $matchingIds);
                }

                foreach ($userQuery->get() as $u) {
                    $deptObj = Department::where('department_id', $u->department_id)->first();
                    $qAtt = DB::table('monthly_evaluations')
                        ->where('worker_id', $u->worker_id)
                        ->where('evaluation_year', $reportYear)
                        ->whereIn('evaluation_month', $qMonths)
                        ->avg('attendance_score');
                    $attScore = $qAtt !== null ? round((float) $qAtt, 1) : 20.0;
                    $rows[] = [
                        $u->full_name,
                        $deptObj->department_name ?? 'General',
                        'Quarter Attendance (' . $attScore . '/20)',
                        $attScore >= 18 ? 'No deduction' : '-5%',
                        $attScore >= 18 ? 'APPROVED' : 'WATCH'
                    ];
                }
            }
        } elseif ($type === 'workload_health') {
            $headers = ['Department', 'Total Staff', 'Quarter Tasks', 'Overloaded Staff', 'Status'];

            foreach ($departments->unique('department_name') as $d) {
                $allIds = Department::where('department_name', $d->department_name)->pluck('department_id');
                $staffQuery = User::whereIn('department_id', $allIds)->whereIn(DB::raw('UPPER(role)'), ['STAFF', 'WORKER']);
                $staffCount = (clone $staffQuery)->count();
                $overloaded = (clone $staffQuery)->where('kpi_score', '<', 75)->count();
                $workerIds  = (clone $staffQuery)->pluck('worker_id');
                $activeTasks = DB::table('tasks_etracker')
                    ->whereIn('worker_id', $workerIds)
                    ->whereRaw('YEAR(COALESCE(completion_date, synced_at)) = ?', [$reportYear])
                    ->whereRaw('MONTH(COALESCE(completion_date, synced_at)) IN (' . implode(',', $qMonths) . ')')
                    ->count();

                $status = $overloaded >= 2 ? 'RISK' : ($overloaded === 1 ? 'WATCH' : 'NORMAL');
                $rows[] = [
                    $d->department_name,
                    $staffCount . ' Staff',
                    $activeTasks . ' Tasks',
                    (string) $overloaded,
                    $status
                ];
            }
        } else {
            // DEFAULT: dept_summary (Gabungan Laporan Bulanan Leader -> Laporan Quarter HR)
            $headers = ['Department', 'Target', 'Quarter Avg', 'Monthly Reports (HR)', 'Status'];

            foreach ($departments->unique('department_name') as $d) {
                $allIds = Department::where('department_name', $d->department_name)->pluck('department_id');
                $staffQuery = User::whereIn('department_id', $allIds)->whereIn(DB::raw('UPPER(role)'), ['STAFF', 'WORKER']);
                $workerIds  = (clone $staffQuery)->pluck('worker_id');
                $target     = (float) ($d->target_score ?? 80);

                // Kumpul semua laporan bulanan yang telah dihantar oleh Leader ke HR dalam quarter ini
                $subs = DB::table('hr_submissions')
                    ->where(DB::raw('UPPER(department_name)'), strtoupper(trim($d->department_name)))
                    ->where('evaluation_year', $reportYear)
                    ->whereIn('evaluation_month', $qMonths)
                    ->orderBy('evaluation_month')
                    ->get();

                $qEvals = DB::table('monthly_evaluations')
                    ->whereIn('worker_id', $workerIds)
                    ->where('evaluation_year', $reportYear)
                    ->whereIn('evaluation_month', $qMonths)
                    ->where('evaluation_status', '!=', 'PENDING')
                    ->get();

                if ($subs->isNotEmpty()) {
                    $avgKpi = round((float) $subs->avg('dept_avg_score'), 1);
                } elseif ($qEvals->isNotEmpty()) {
                    $avgKpi = round((float) $qEvals->avg('total_score'), 1);
                } else {
                    $avgKpi = round((clone $staffQuery)->avg('kpi_score') ?? 0, 1);
                }

                $subMonthsLabels = $subs->pluck('evaluation_month')
                    ->unique()
                    ->map(fn($m) => $monthNames[(int) $m] ?? $m)
                    ->implode(', ');
                $subSummary = $subs->count() . '/3 Months' . ($subMonthsLabels ? " ({$subMonthsLabels})" : ' (Pending)');

                $status = $avgKpi >= $target ? 'PASSED' : ($avgKpi >= ($target - 5) ? 'GOOD' : 'WATCH');
                $rows[] = [
                    $d->department_name,
                    $target . '%',
                    number_format($avgKpi, 1) . '%',
                    $subSummary,
                    $status
                ];
            }
        }

        // Bina nama fail unik dengan versi (_v2, _v3) jika sudah wujud dalam MySQL
        $baseName = str_replace('-', '_', $quarterKey) . "_{$deptCode}_{$type}";
        $fileName = "{$baseName}.{$format}";
        $version  = 2;
        while (DB::table('reports_archive')->where('report_title', $fileName)->exists()) {
            $fileName = "{$baseName}_v{$version}.{$format}";
            $version++;
        }

        $MB        = 1048576;
        $baseBytes = $format === 'pdf' ? 1.2 * $MB : ($format === 'xlsx' ? 420 * 1024 : 120 * 1024);
        $bytes     = (int) round($baseBytes * ($deptCode === 'ALL' ? 1.25 : 0.8));

        // Padankan dengan nilai ENUM jadual reports_archive
        $dbReportTypeMap = [
            'dept_summary'      => 'DEPT_SUMMARY',
            'individual_audit'  => 'EOM_WINNERS',
            'attendance_impact' => 'LEAVE_AUDIT',
            'workload_health'   => 'EXECUTIVE_SUMMARY',
        ];
        $dbReportType = $dbReportTypeMap[$type] ?? 'DEPT_SUMMARY';
        $dbFileFormat = in_array(strtoupper($format), ['PDF', 'XLSX', 'ZIP']) ? strtoupper($format) : 'XLSX';
        $generatedBy  = Auth::user()->worker_id ?? DB::table('users')->value('worker_id') ?? 'ILM1001';
        $relativePath = 'storage/app/reports/' . $fileName;
        $now          = now();

        $reportId = DB::table('reports_archive')->insertGetId([
            'report_title' => $fileName,
            'report_type'  => $dbReportType,
            'file_format'  => $dbFileFormat,
            'file_path'    => $relativePath,
            'generated_by' => $generatedBy,
            'generated_at' => $now,
        ]);

        $record = [
            'id'        => (int) $reportId,
            'title'     => $fileName,
            'type'      => $type,
            'typeName'  => $typeLabels[$type] ?? 'Quarterly Department Performance Summary',
            'createdAt' => $now->toIso8601String(),
            'format'    => strtoupper($format),
            'bytes'     => $bytes,
            'dept'      => $deptCode,
            'deptLabel' => $deptLabels[$deptCode] ?? $deptCode,
            'quarter'   => str_replace('-', ' ', $quarterKey),
            'snapshot'  => [
                'headers' => $headers,
                'rows'    => $rows,
            ],
        ];

        // Simpan metadata & snapshot fail ke storan tempatan supaya Preview/Download kekal berfungsi selepas refresh
        $dir = storage_path('app/reports');
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        file_put_contents($dir . '/' . $fileName . '.json', json_encode($record));

        $totalGenerated = DB::table('reports_archive')->count();
        $totalBytes     = collect(glob($dir . '/*.json'))->sum(function ($f) {
            $d = json_decode(file_get_contents($f), true);
            return (int) ($d['bytes'] ?? 0);
        });

        return response()->json([
            'status'         => 'success',
            'record'         => $record,
            'totalGenerated' => $totalGenerated,
            'storageBytes'   => $totalBytes ?: $bytes,
        ]);
    }

    // PADAM REKOD ARCHIVE
    public function deleteReport($id)
    {
        $row = DB::table('reports_archive')->where('report_id', $id)->first();
        if ($row) {
            $metaPath = storage_path('app/reports/' . $row->report_title . '.json');
            if (file_exists($metaPath)) {
                @unlink($metaPath);
            }
            DB::table('reports_archive')->where('report_id', $id)->delete();
        }

        $dir = storage_path('app/reports');
        $totalBytes = is_dir($dir) ? collect(glob($dir . '/*.json'))->sum(function ($f) {
            $d = json_decode(file_get_contents($f), true);
            return (int) ($d['bytes'] ?? 0);
        }) : 0;

        return response()->json([
            'status'       => 'success',
            'storageBytes' => $totalBytes,
        ]);
    }

    // HELPER: KESAN NAMA LAJUR SEBENAR JADUAL emergency_leaves SECARA AUTOMATIK
    private function getEmergencyLeaveMeta(): array
    {
        if (!Schema::hasTable('emergency_leaves')) {
            Schema::create('emergency_leaves', function ($table) {
                $table->id();
                $table->string('worker_id');
                $table->date('leave_date');
                $table->string('reason');
                $table->text('notes')->nullable();
                $table->string('status', 30)->default('PENDING');
                $table->string('impact', 50)->nullable();
                $table->timestamps();
            });
        }

        $columns   = Schema::getColumnListing('emergency_leaves');
        $pk        = in_array('id', $columns) ? 'id' : (in_array('leave_id', $columns) ? 'leave_id' : $columns[0]);
        $dateCol   = in_array('leave_date', $columns) ? 'leave_date' : (in_array('date', $columns) ? 'date' : null);
        
        $notesCol = null;
        foreach (['notes', 'remarks', 'other_reason', 'description', 'details', 'explanation'] as $candidate) {
            if (in_array($candidate, $columns)) {
                $notesCol = $candidate;
                break;
            }
        }

        if (!$notesCol) {
            Schema::table('emergency_leaves', function ($table) {
                $table->text('notes')->nullable();
            });
            $notesCol  = 'notes';
            $columns[] = 'notes';
        }

        $impactCol = in_array('impact', $columns) ? 'impact' : (in_array('attendance_impact', $columns) ? 'attendance_impact' : null);

        $statusCol = null;
        foreach (['status', 'leave_status', 'approval_status', 'adjustment_status'] as $candidate) {
            if (in_array($candidate, $columns)) {
                $statusCol = $candidate;
                break;
            }
        }

        if (!$statusCol) {
            Schema::table('emergency_leaves', function ($table) {
                $table->string('status', 30)->default('PENDING');
            });
            $statusCol = 'status';
            $columns[] = 'status';

            if ($impactCol) {
                DB::table('emergency_leaves')
                    ->whereNotNull($impactCol)
                    ->where($impactCol, '!=', '')
                    ->where($impactCol, '!=', 'Pending Review')
                    ->update([
                        'status' => DB::raw("CASE WHEN {$impactCol} LIKE '%Deducted%' OR {$impactCol} LIKE '%-5%' THEN 'REJECTED' ELSE 'APPROVED' END")
                    ]);
            }
        }

        return compact('columns', 'pk', 'dateCol', 'statusCol', 'notesCol', 'impactCol');
    }

    // API NOTIFIKASI LOCENG HR (PERMOHONAN EMERGENCY LEAVE BERSTATUS PENDING)
    public function getHrNotifications()
    {
        $meta      = $this->getEmergencyLeaveMeta();
        $pk        = $meta['pk'];
        $dateCol   = $meta['dateCol'];
        $statusCol = $meta['statusCol'];
        $notesCol  = $meta['notesCol'];

        $items = [];
        $pendingRows = DB::table('emergency_leaves')
            ->whereRaw("UPPER(TRIM(COALESCE(`{$statusCol}`, 'PENDING'))) = ?", ['PENDING'])
            ->orderByDesc($pk)
            ->get();

        foreach ($pendingRows as $lv) {
            $user = User::where('worker_id', $lv->worker_id ?? '')->first();
            $dept = $user ? Department::where('department_id', $user->department_id)->first() : null;

            $rawDate   = ($dateCol && !empty($lv->$dateCol)) ? $lv->$dateCol : date('Y-m-d');
            $isoDate   = substr((string) $rawDate, 0, 10);
            $fmtDate   = \Carbon\Carbon::parse($isoDate)->format('d/m/Y');
            $createdAt = !empty($lv->created_at)
                ? \Carbon\Carbon::parse($lv->created_at)->diffForHumans()
                : 'Just now';

            $items[] = [
                'id'         => (int) ($lv->$pk ?? 0),
                'worker_id'  => $lv->worker_id ?? '',
                'staff'      => $user->full_name ?? ($lv->staff_name ?? $lv->worker_id ?? 'Staff'),
                'department' => $dept->department_name ?? ($lv->department ?? 'General'),
                'leaveDate'  => $isoDate,
                'leaveLabel' => $fmtDate,
                'reason'     => $lv->reason ?? 'Emergency Leave',
                'notes'      => $notesCol ? ($lv->$notesCol ?? '') : '',
                'timeAgo'    => $createdAt,
            ];
        }

        return response()->json([
            'status' => 'success',
            'count'  => count($items),
            'items'  => $items,
        ]);
    }

    // PAPARAN HR - EMERGENCY LEAVE (ASINGKAN PENDING & AUDIT LOG)
    public function emerLeave()
    {
        $meta      = $this->getEmergencyLeaveMeta();
        $pk        = $meta['pk'];
        $dateCol   = $meta['dateCol'];
        $statusCol = $meta['statusCol'];
        $notesCol  = $meta['notesCol'];

        $staffList = User::where('role', '!=', 'HR')
            ->orderBy('full_name', 'asc')
            ->get()
            ->map(function ($u) {
                $dept = Department::where('department_id', $u->department_id)->first();
                return (object) [
                    'worker_id'       => $u->worker_id,
                    'full_name'       => $u->full_name,
                    'department_name' => $dept->department_name ?? 'General',
                ];
            });

        $pendingRequests = [];
        $auditLogs       = [];

        $leaves = DB::table('emergency_leaves')->orderBy($pk, 'desc')->get();

        foreach ($leaves as $index => $lv) {
            $user = User::where('worker_id', $lv->worker_id ?? '')->first();
            $dept = $user ? Department::where('department_id', $user->department_id)->first() : null;

            $rawDate       = ($dateCol && !empty($lv->$dateCol)) ? $lv->$dateCol : date('Y-m-d');
            $formattedDate = substr((string) $rawDate, 0, 10);
            $rawStatus     = trim((string) ($lv->$statusCol ?? ''));
            $statusLower   = $rawStatus === '' ? 'pending' : strtolower($rawStatus);

            $entry = [
                'id'          => (int) ($lv->$pk ?? ($index + 1)),
                'worker_id'   => $lv->worker_id ?? '',
                'staff'       => $user->full_name ?? ($lv->staff_name ?? $lv->worker_id ?? 'Staff'),
                'department'  => $dept->department_name ?? ($lv->department ?? 'General'),
                'leaveDate'   => $formattedDate,
                'reason'      => $lv->reason ?? 'Emergency Leave',
                'notes'       => $notesCol ? ($lv->$notesCol ?? '') : '',
                'status'      => $statusLower,
                'submittedAt' => !empty($lv->created_at)
                    ? \Carbon\Carbon::parse($lv->created_at)->diffForHumans()
                    : 'Recently',
            ];

            if ($statusLower === 'pending') {
                $pendingRequests[] = $entry;
            } else {
                $auditLogs[] = $entry;
            }
        }

        return view('emerleave', compact('staffList', 'auditLogs', 'pendingRequests'));
    }

    // SIMPAN / KEMAS KINI KEPUTUSAN EMERGENCY LEAVE KE DATABASE MYSQL
    public function storeEmerLeave(Request $request)
    {
        $validated = $request->validate([
            'leave_id'  => 'nullable|integer',
            'worker_id' => 'required|string',
            'leaveDate' => 'required|date',
            'reason'    => 'required|string|max:100',
            'notes'     => 'nullable|string|max:200',
            'status'    => 'required|in:approved,rejected',
        ]);

        $meta      = $this->getEmergencyLeaveMeta();
        $columns   = $meta['columns'];
        $pk        = $meta['pk'];
        $dateCol   = $meta['dateCol'];
        $statusCol = $meta['statusCol'];
        $notesCol  = $meta['notesCol'];
        $impactCol = $meta['impactCol'];

        $user        = User::where('worker_id', $validated['worker_id'])->first();
        $dept        = $user ? DB::table('departments')->where('department_id', $user->department_id)->first() : null;
        $statusLower = strtolower($validated['status']);
        $impactText  = $statusLower === 'approved' ? 'No Deduct' : '-5% Deducted';

        $pendingRow = null;
        if (!empty($validated['leave_id'])) {
            $pendingRow = DB::table('emergency_leaves')
                ->where($pk, $validated['leave_id'])
                ->whereRaw("UPPER(TRIM(COALESCE(`{$statusCol}`, 'PENDING'))) = ?", ['PENDING'])
                ->first();
        }
        if (!$pendingRow && $dateCol) {
            $pendingRow = DB::table('emergency_leaves')
                ->where('worker_id', $validated['worker_id'])
                ->where($dateCol, $validated['leaveDate'])
                ->whereRaw("UPPER(TRIM(COALESCE(`{$statusCol}`, 'PENDING'))) = ?", ['PENDING'])
                ->first();
        }

        if (!$pendingRow && $dateCol) {
            $finalizedExists = DB::table('emergency_leaves')
                ->where('worker_id', $validated['worker_id'])
                ->where($dateCol, $validated['leaveDate'])
                ->whereRaw("UPPER(TRIM(COALESCE(`{$statusCol}`, ''))) != ?", ['PENDING'])
                ->exists();

            if ($finalizedExists) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'This staff member already has a finalized emergency leave record on the selected date.'
                ], 422);
            }
        }

        $payload = [];
        if (in_array('worker_id', $columns))  $payload['worker_id']  = $validated['worker_id'];
        if ($dateCol)                         $payload[$dateCol]     = $validated['leaveDate'];
        if (in_array('reason', $columns))     $payload['reason']     = $validated['reason'];
        if ($notesCol)                        $payload[$notesCol]    = $validated['notes'] ?? '';
        $payload[$statusCol] = strtoupper($statusLower);
        if ($impactCol)                       $payload[$impactCol]   = $impactText;
        if (in_array('updated_at', $columns)) $payload['updated_at'] = now();

        if ($pendingRow) {
            DB::table('emergency_leaves')
                ->where($pk, $pendingRow->$pk)
                ->update($payload);
            $recordId = (int) $pendingRow->$pk;
        } else {
            if (in_array('created_at', $columns)) $payload['created_at'] = now();
            $recordId = (int) DB::table('emergency_leaves')->insertGetId($payload);
        }

        if ($statusLower === 'rejected' && $user) {
            $setting   = KpiSetting::first();
            $attWeight = (float) ($setting->attendance_weightage ?? 20);
            $deduction = round(5 * ($attWeight / 100), 2);

            $evalYear  = (int) date('Y');
            $evalMonth = (int) date('n');

            $evalRow = DB::table('monthly_evaluations')
                ->where('worker_id', $user->worker_id)
                ->where('evaluation_month', $evalMonth)
                ->where('evaluation_year', $evalYear)
                ->first();

            if ($evalRow) {
                $newEvalAtt = max(0, round(((float) $evalRow->attendance_score) - $deduction, 2));
                $newTotal   = round(((float) $evalRow->tasks_score) + $newEvalAtt + ((float) $evalRow->leader_score), 2);

                DB::table('monthly_evaluations')
                    ->where('evaluation_id', $evalRow->evaluation_id)
                    ->update([
                        'attendance_score' => $newEvalAtt,
                        'total_score'      => $newTotal,
                    ]);
            } else {
                $tasksScore = $this->calculateMonthlyTaskScore($user->worker_id, $evalYear, $evalMonth);
                $newEvalAtt = max(0, round($attWeight - $deduction, 2));

                DB::table('monthly_evaluations')->insert([
                    'worker_id'         => $user->worker_id,
                    'evaluation_month'  => $evalMonth,
                    'evaluation_year'   => $evalYear,
                    'tasks_score'       => $tasksScore,
                    'attendance_score'  => $newEvalAtt,
                    'leader_score'      => 0.00,
                    'total_score'       => round($tasksScore + $newEvalAtt, 2),
                    'eom_badge'         => 'NONE',
                    'evaluation_status' => 'PENDING',
                ]);
            }

            $userCols = Schema::getColumnListing('users');
            if (in_array('attendance_score', $userCols)) {
                $newAtt = max(0, ((float) ($user->attendance_score ?? 100)) - 5);
                DB::table('users')->where('worker_id', $user->worker_id)->update(['attendance_score' => $newAtt]);
            } elseif (in_array('kpi_score', $userCols) && $user->kpi_score !== null) {
                $newKpi = max(0, round(((float) $user->kpi_score) - $deduction, 1));
                DB::table('users')->where('worker_id', $user->worker_id)->update(['kpi_score' => $newKpi]);
            }
        }

        $remainingPending = DB::table('emergency_leaves')
            ->whereRaw("UPPER(TRIM(COALESCE(`{$statusCol}`, 'PENDING'))) = ?", ['PENDING'])
            ->count();

        return response()->json([
            'status'       => 'success',
            'pendingCount' => $remainingPending,
            'record'       => [
                'id'         => $recordId,
                'worker_id'  => $validated['worker_id'],
                'staff'      => $user->full_name ?? $validated['worker_id'],
                'department' => $dept->department_name ?? 'General',
                'leaveDate'  => $validated['leaveDate'],
                'reason'     => $validated['reason'],
                'notes'      => $validated['notes'] ?? '',
                'status'     => $statusLower,
            ],
        ]);
    }

    // =========================================================================
    // PAPARAN & API DASHBOARD LEADER (SAMBUNGAN DATABASE MYSQL)
    // =========================================================================
    private function buildLeaderDashboardPayload()
    {
        $user = Auth::user();

        $matchingDeptIds = [];
        $deptName = 'WebTech';

        if (!empty($user->department_id)) {
            $matchingDeptIds[] = (int) $user->department_id;
            $dept = DB::table('departments')
                ->where('department_id', $user->department_id)
                ->first();

            if ($dept && !empty($dept->department_name)) {
                $deptName = $dept->department_name;
                $sameNameIds = DB::table('departments')
                    ->where('department_name', 'LIKE', '%' . trim($deptName) . '%')
                    ->pluck('department_id')
                    ->map(fn($id) => (int) $id)
                    ->toArray();
                $matchingDeptIds = array_values(array_unique(array_merge($matchingDeptIds, $sameNameIds)));
            }
        } else {
            $dept = DB::table('departments')->orderBy('department_id')->first();
            if ($dept) {
                $deptName = $dept->department_name;
                $matchingDeptIds = DB::table('departments')
                    ->where('department_name', 'LIKE', '%' . trim($deptName) . '%')
                    ->pluck('department_id')
                    ->map(fn($id) => (int) $id)
                    ->toArray();
            }
        }

        // Gunakan bulan & tahun kalendar semasa supaya reset setiap bulan
        $evalYear  = (int) date('Y');
        $evalMonth = (int) date('n');

        $settings        = DB::table('kpi_settings')->first();
        $cutoffDay       = (int) ($settings->cutoff_day ?? 28);
        $goldThreshold   = (float) ($settings->gold_threshold ?? 80.0);
        $silverThreshold = (float) ($settings->silver_threshold ?? 75.0);

        $staffRows = User::whereIn('department_id', $matchingDeptIds)
            ->whereIn(DB::raw('UPPER(role)'), ['STAFF', 'WORKER'])
            ->where('worker_id', '!=', $user->worker_id)
            ->orderBy('full_name')
            ->get();

        $staffList = $staffRows->map(function ($staff) use ($evalMonth, $evalYear) {
            $eval = DB::table('monthly_evaluations')
                ->where('worker_id', $staff->worker_id)
                ->where('evaluation_month', $evalMonth)
                ->where('evaluation_year', $evalYear)
                ->first();

            if ($eval) {
                $taskScore = (float) $eval->tasks_score;
                $attScore  = (float) $eval->attendance_score;
            } else {
                $taskScore = round($this->calculateMonthlyTaskScore($staff->worker_id, $evalYear, $evalMonth), 1);
                $attScore  = 20.0;
            }

            $isPending = !$eval || ($eval->evaluation_status === 'PENDING' && empty($eval->submitted_by_leader));

            return [
                'id'         => $staff->worker_id,
                'name'       => $staff->full_name,
                'kpi'        => $taskScore,
                'attendance' => $attScore,
                'rating'     => $isPending ? null : (float) $eval->leader_score,
            ];
        })->values()->all();

        // Kira purata bulan-bulan lepas (termasuk rekod sejarah terdahulu)
        $prevAvg = DB::table('monthly_evaluations as me')
            ->join('users as u', 'u.worker_id', '=', 'me.worker_id')
            ->whereIn('u.department_id', $matchingDeptIds)
            ->whereIn(DB::raw('UPPER(u.role)'), ['STAFF', 'WORKER'])
            ->where(function ($q) use ($evalYear, $evalMonth) {
                $q->where('me.evaluation_year', '<', $evalYear)
                  ->orWhere(function ($q2) use ($evalYear, $evalMonth) {
                      $q2->where('me.evaluation_year', $evalYear)
                         ->where('me.evaluation_month', '<', $evalMonth);
                  });
            })
            ->where('me.evaluation_status', '!=', 'PENDING')
            ->avg('me.total_score');

        $leaderPhoto = '';
        if (!empty($user->profile_photo)) {
            $clean = ltrim($user->profile_photo, '/');
            $leaderPhoto = (str_starts_with($user->profile_photo, 'http://') || str_starts_with($user->profile_photo, 'https://') || str_starts_with($user->profile_photo, '/'))
                ? $user->profile_photo
                : (str_starts_with($clean, 'uploads/') || str_starts_with($clean, 'storage/')
                    ? asset($clean)
                    : asset('storage/' . $clean));
        }

        return [
            'deptName'        => strtoupper($deptName),
            'deptLabel'       => $deptName,
            'evalMonth'       => $evalMonth,
            'evalYear'        => $evalYear,
            'cutoffDay'       => $cutoffDay,
            'goldThreshold'   => $goldThreshold,
            'silverThreshold' => $silverThreshold,
            'lastQuarterAvg'  => $prevAvg ? round((float) $prevAvg, 1) : 85.9,
            'staffList'       => $staffList,
            'leader'          => [
                'id'    => $user->worker_id,
                'name'  => $user->full_name,
                'title' => $user->designation ?: ($deptName . ' Team Lead'),
                'photo' => $leaderPhoto,
            ],
        ];
    }

    public function leaderDashboard()
    {
        $seedData = $this->buildLeaderDashboardPayload();
        return view('departmentdashboard', compact('seedData'));
    }

    public function leaderDashboardData()
    {
        return response()->json($this->buildLeaderDashboardPayload());
    }

    public function saveLeaderRating(Request $request)
    {
        $validated = $request->validate([
            'worker_id' => 'required|string|exists:users,worker_id',
            'eval'      => 'required|array',
            'eval.q1'   => 'required|numeric|min:0|max:10',
            'eval.q2'   => 'required|numeric|min:0|max:10',
            'eval.q3'   => 'required|numeric|min:0|max:10',
            'eval.q4'   => 'required|numeric|min:0|max:10',
        ]);

        $leader   = Auth::user();
        $workerId = $validated['worker_id'];

        $evalYear  = (int) date('Y');
        $evalMonth = (int) date('n');

        $existing = DB::table('monthly_evaluations')
            ->where('worker_id', $workerId)
            ->where('evaluation_month', $evalMonth)
            ->where('evaluation_year', $evalYear)
            ->first();

        // Kawalan UX: Jika sudah dinilai, pindaan mesti melalui Dedicated Page (/leader/input-rating)
        if ($existing && strtoupper((string) $existing->evaluation_status) !== 'PENDING' && (float) $existing->leader_score > 0) {
            return response()->json([
                'status'  => 'error',
                'message' => 'This staff evaluation is already submitted. Please use the Revise button to edit on the KPI Management page.',
            ], 422);
        }

        if ($existing) {
            $tasksScore = (float) $existing->tasks_score;
            $attScore   = (float) $existing->attendance_score;
        } else {
            $tasksScore = $this->calculateMonthlyTaskScore($workerId, $evalYear, $evalMonth);
            $attScore   = 20.00;
        }

        $criteria = [
            'q1' => round((float) $validated['eval']['q1'], 1),
            'q2' => round((float) $validated['eval']['q2'], 1),
            'q3' => round((float) $validated['eval']['q3'], 1),
            'q4' => round((float) $validated['eval']['q4'], 1),
        ];

        $rating     = round(array_sum($criteria), 1);
        $totalScore = round($tasksScore + $attScore + $rating, 2);
        $status     = $totalScore >= 90 ? 'EXCELLENCE' : ($totalScore >= 80 ? 'GOOD' : 'ATTENTION');

        $payload = [
            'leader_score'        => $rating,
            'total_score'         => $totalScore,
            'evaluation_status'   => $status,
            'submitted_by_leader' => $leader->worker_id,
            'submitted_at'        => now(),
        ];

        if (Schema::hasColumn('monthly_evaluations', 'criteria_scores')) {
            $payload['criteria_scores'] = json_encode($criteria);
        }

        if ($existing) {
            DB::table('monthly_evaluations')
                ->where('evaluation_id', $existing->evaluation_id)
                ->update($payload);
        } else {
            DB::table('monthly_evaluations')->insert(array_merge([
                'worker_id'        => $workerId,
                'evaluation_month' => $evalMonth,
                'evaluation_year'  => $evalYear,
                'tasks_score'      => $tasksScore,
                'attendance_score' => $attScore,
                'eom_badge'        => 'NONE',
            ], $payload));
        }

        User::where('worker_id', $workerId)->update(['kpi_score' => $totalScore]);

        $staffUser = User::where('worker_id', $workerId)->first();
        if ($staffUser && $staffUser->department_id) {
            $dept = DB::table('departments')
                ->where('department_id', $staffUser->department_id)
                ->first();
            $deptIds = $dept
                ? DB::table('departments')->where('department_name', $dept->department_name)->pluck('department_id')->toArray()
                : [$staffUser->department_id];

            $settings  = DB::table('kpi_settings')->first();
            $qualify   = (float) ($settings->silver_threshold ?? 75.0);
            $deptStaff = User::whereIn('department_id', $deptIds)->whereIn(DB::raw('UPPER(role)'), ['STAFF', 'WORKER'])->pluck('worker_id')->toArray();

            DB::table('monthly_evaluations')
                ->whereIn('worker_id', $deptStaff)
                ->where('evaluation_month', $evalMonth)
                ->where('evaluation_year', $evalYear)
                ->update(['eom_badge' => 'NONE']);

            $topRanked = DB::table('monthly_evaluations')
                ->whereIn('worker_id', $deptStaff)
                ->where('evaluation_month', $evalMonth)
                ->where('evaluation_year', $evalYear)
                ->where('evaluation_status', '!=', 'PENDING')
                ->where('total_score', '>=', $qualify)
                ->orderByDesc('total_score')
                ->orderByDesc('leader_score')
                ->limit(2)
                ->get();

            if (isset($topRanked[0])) {
                DB::table('monthly_evaluations')
                    ->where('evaluation_id', $topRanked[0]->evaluation_id)
                    ->update(['eom_badge' => 'GOLD']);
            }
            if (isset($topRanked[1])) {
                DB::table('monthly_evaluations')
                    ->where('evaluation_id', $topRanked[1]->evaluation_id)
                    ->update(['eom_badge' => 'SILVER']);
            }
        }

        return response()->json([
            'status'  => 'success',
            'payload' => $this->buildLeaderDashboardPayload(),
        ]);
    }

    public function sendLeaderReminder(Request $request)
    {
        $payload = $this->buildLeaderDashboardPayload();
        $pending = collect($payload['staffList'])->whereNull('rating')->pluck('name')->values()->all();

        return response()->json([
            'status'  => 'success',
            'pending' => $pending,
        ]);
    }

    // =========================================================================
    // PAPARAN & API LEADER - INPUT RATING (SAMBUNGAN DATABASE MYSQL)
    // =========================================================================
    private function buildLeaderInputRatingPayload()
    {
        $user = Auth::user();

        if (!Schema::hasColumn('monthly_evaluations', 'criteria_scores')) {
            Schema::table('monthly_evaluations', function ($table) {
                $table->text('criteria_scores')->nullable();
            });
        }

        $matchingDeptIds = [];
        $deptName = 'WebTech';

        if (!empty($user->department_id)) {
            $matchingDeptIds[] = (int) $user->department_id;
            $dept = DB::table('departments')
                ->where('department_id', $user->department_id)
                ->first();

            if ($dept && !empty($dept->department_name)) {
                $deptName = $dept->department_name;
                $sameNameIds = DB::table('departments')
                    ->where('department_name', 'LIKE', '%' . trim($deptName) . '%')
                    ->pluck('department_id')
                    ->map(fn($id) => (int) $id)
                    ->toArray();
                $matchingDeptIds = array_values(array_unique(array_merge($matchingDeptIds, $sameNameIds)));
            }
        } else {
            $dept = DB::table('departments')->orderBy('department_id')->first();
            if ($dept) {
                $deptName = $dept->department_name;
                $matchingDeptIds = DB::table('departments')
                    ->where('department_name', 'LIKE', '%' . trim($deptName) . '%')
                    ->pluck('department_id')
                    ->map(fn($id) => (int) $id)
                    ->toArray();
            }
        }

        // Gunakan bulan & tahun kalendar semasa supaya reset setiap bulan
        $evalYear  = (int) date('Y');
        $evalMonth = (int) date('n');

        $settings = DB::table('kpi_settings')->first();
        $thresholds = [
            'gold'   => (float) ($settings->gold_threshold ?? 85.0),
            'silver' => (float) ($settings->silver_threshold ?? 70.0),
            'bronze' => (float) ($settings->bronze_threshold ?? 50.0),
        ];

        $staffRows = User::whereIn('department_id', $matchingDeptIds)
            ->whereIn(DB::raw('UPPER(role)'), ['STAFF', 'WORKER'])
            ->where('worker_id', '!=', $user->worker_id)
            ->orderBy('full_name', 'asc')
            ->get();

        $members = $staffRows->map(function ($staff) use ($evalMonth, $evalYear) {
            $eval = DB::table('monthly_evaluations')
                ->where('worker_id', $staff->worker_id)
                ->where('evaluation_month', $evalMonth)
                ->where('evaluation_year', $evalYear)
                ->first();

            if ($eval) {
                $taskScore = round((float) $eval->tasks_score, 1);
                $attScore  = round((float) $eval->attendance_score, 1);
            } else {
                $taskScore = round($this->calculateMonthlyTaskScore($staff->worker_id, $evalYear, $evalMonth), 1);
                $attScore  = 20.0;
            }

            $uiStatus = 'PENDING';
            if ($eval) {
                $rawStatus = strtoupper($eval->evaluation_status ?? 'PENDING');
                if ($rawStatus === 'DRAFT') {
                    $uiStatus = 'DRAFT';
                } elseif ($rawStatus !== 'PENDING' && !empty($eval->submitted_by_leader)) {
                    $uiStatus = 'SUBMITTED';
                } elseif ($rawStatus === 'PENDING' && (!empty($eval->criteria_scores) || (float) $eval->leader_score > 0)) {
                    $uiStatus = 'DRAFT';
                }
            }

            $criteria = ['q1' => null, 'q2' => null, 'q3' => null, 'q4' => null];
            if ($eval && !empty($eval->criteria_scores)) {
                $decoded = json_decode($eval->criteria_scores, true);
                if (is_array($decoded)) {
                    foreach (['q1', 'q2', 'q3', 'q4'] as $k) {
                        $criteria[$k] = isset($decoded[$k]) && $decoded[$k] !== null ? (float) $decoded[$k] : null;
                    }
                }
            } elseif ($eval && (float) $eval->leader_score > 0 && $uiStatus !== 'PENDING') {
                $totalLeader = min(40.0, (float) $eval->leader_score);
                $base = min(10.0, floor(($totalLeader / 4) * 2) / 2);
                $remSteps = (int) round(($totalLeader - ($base * 4)) / 0.5);
                foreach (['q1', 'q2', 'q3', 'q4'] as $idx => $k) {
                    $criteria[$k] = min(10.0, $base + ($idx < $remSteps ? 0.5 : 0.0));
                }
            }

            return [
                'id'              => $staff->worker_id,
                'name'            => $staff->full_name,
                'taskScore'       => $taskScore,
                'attendanceScore' => $attScore,
                'eval'            => $criteria,
                'status'          => $uiStatus,
                'submittedAt'     => ($uiStatus === 'SUBMITTED' && !empty($eval->submitted_at))
                    ? \Carbon\Carbon::parse($eval->submitted_at)->toIso8601String()
                    : null,
            ];
        })->values()->all();

        $leaderPhoto = '';
        if (!empty($user->profile_photo)) {
            $clean = ltrim($user->profile_photo, '/');
            $leaderPhoto = (str_starts_with($user->profile_photo, 'http://') || str_starts_with($user->profile_photo, 'https://') || str_starts_with($user->profile_photo, '/'))
                ? $user->profile_photo
                : (str_starts_with($clean, 'uploads/') || str_starts_with($clean, 'storage/')
                    ? asset($clean)
                    : asset('storage/' . $clean));
        }

        return [
            'deptName'   => strtoupper($deptName),
            'evalMonth'  => $evalMonth,
            'evalYear'   => $evalYear,
            'thresholds' => $thresholds,
            'members'    => $members,
            'leader'     => [
                'id'    => $user->worker_id,
                'name'  => $user->full_name,
                'title' => $user->designation ?: ($deptName . ' Team Lead'),
                'photo' => $leaderPhoto,
            ],
        ];
    }

    public function leaderInputRating(Request $request)
    {
        $seedData = $this->buildLeaderInputRatingPayload();
        $seedData['initialMemberId'] = $request->query('worker_id', '');

        return view('departmentkpimng', compact('seedData'));
    }

    public function leaderInputRatingData()
    {
        return response()->json($this->buildLeaderInputRatingPayload());
    }

    public function saveLeaderInputRating(Request $request)
    {
        $validated = $request->validate([
            'worker_id' => 'required|string|exists:users,worker_id',
            'action'    => 'required|in:draft,submit,revise',
            'eval'      => 'required|array',
            'eval.q1'   => 'nullable|numeric|min:0|max:10',
            'eval.q2'   => 'nullable|numeric|min:0|max:10',
            'eval.q3'   => 'nullable|numeric|min:0|max:10',
            'eval.q4'   => 'nullable|numeric|min:0|max:10',
        ]);

        if (!Schema::hasColumn('monthly_evaluations', 'criteria_scores')) {
            Schema::table('monthly_evaluations', function ($table) {
                $table->text('criteria_scores')->nullable();
            });
        }

        $leader   = Auth::user();
        $workerId = $validated['worker_id'];
        $action   = $validated['action'];
        $evalVals = [
            'q1' => isset($validated['eval']['q1']) ? round((float) $validated['eval']['q1'], 1) : null,
            'q2' => isset($validated['eval']['q2']) ? round((float) $validated['eval']['q2'], 1) : null,
            'q3' => isset($validated['eval']['q3']) ? round((float) $validated['eval']['q3'], 1) : null,
            'q4' => isset($validated['eval']['q4']) ? round((float) $validated['eval']['q4'], 1) : null,
        ];

        if ($action === 'submit') {
            foreach (['q1', 'q2', 'q3', 'q4'] as $k) {
                if ($evalVals[$k] === null) {
                    return response()->json(['status' => 'error', 'message' => 'Please fill in all 4 criteria before submitting.'], 422);
                }
            }
        }

        $evalYear  = (int) date('Y');
        $evalMonth = (int) date('n');

        $existing = DB::table('monthly_evaluations')
            ->where('worker_id', $workerId)
            ->where('evaluation_month', $evalMonth)
            ->where('evaluation_year', $evalYear)
            ->first();

        if ($existing) {
            $tasksScore = (float) $existing->tasks_score;
            $attScore   = (float) $existing->attendance_score;
        } else {
            $tasksScore = $this->calculateMonthlyTaskScore($workerId, $evalYear, $evalMonth);
            $attScore   = 20.00;
        }

        $allNull     = collect($evalVals)->every(fn($v) => $v === null);
        $leaderScore = round(collect($evalVals)->filter(fn($v) => $v !== null)->sum(), 2);
        $totalScore  = round($tasksScore + $attScore + $leaderScore, 2);

        if ($action === 'submit') {
            $dbStatus = $totalScore >= 90 ? 'EXCELLENCE' : ($totalScore >= 80 ? 'GOOD' : 'ATTENTION');
            $payload = [
                'leader_score'        => $leaderScore,
                'total_score'         => $totalScore,
                'criteria_scores'     => json_encode($evalVals),
                'evaluation_status'   => $dbStatus,
                'submitted_by_leader' => $leader->worker_id,
                'submitted_at'        => now(),
            ];
        } else {
            $payload = [
                'leader_score'        => $leaderScore,
                'total_score'         => $totalScore,
                'criteria_scores'     => $allNull ? null : json_encode($evalVals),
                'eom_badge'           => 'NONE',
                'evaluation_status'   => 'PENDING',
                'submitted_by_leader' => null,
                'submitted_at'        => null,
            ];
        }

        if ($existing) {
            DB::table('monthly_evaluations')
                ->where('evaluation_id', $existing->evaluation_id)
                ->update($payload);
        } else {
            DB::table('monthly_evaluations')->insert(array_merge([
                'worker_id'        => $workerId,
                'evaluation_month' => $evalMonth,
                'evaluation_year'  => $evalYear,
                'tasks_score'      => $tasksScore,
                'attendance_score' => $attScore,
                'eom_badge'        => 'NONE',
            ], $payload));
        }

        if ($action === 'submit') {
            User::where('worker_id', $workerId)->update(['kpi_score' => $totalScore]);

            $staffUser = User::where('worker_id', $workerId)->first();
            if ($staffUser && $staffUser->department_id) {
                $dept = DB::table('departments')
                    ->where('department_id', $staffUser->department_id)
                    ->first();
                $deptIds = $dept
                    ? DB::table('departments')->where('department_name', $dept->department_name)->pluck('department_id')->toArray()
                    : [$staffUser->department_id];

                $settings  = DB::table('kpi_settings')->first();
                $qualify   = (float) ($settings->silver_threshold ?? 75.0);
                $deptStaff = User::whereIn('department_id', $deptIds)->whereIn(DB::raw('UPPER(role)'), ['STAFF', 'WORKER'])->pluck('worker_id')->toArray();

                DB::table('monthly_evaluations')
                    ->whereIn('worker_id', $deptStaff)
                    ->where('evaluation_month', $evalMonth)
                    ->where('evaluation_year', $evalYear)
                    ->update(['eom_badge' => 'NONE']);

                $topRanked = DB::table('monthly_evaluations')
                    ->whereIn('worker_id', $deptStaff)
                    ->where('evaluation_month', $evalMonth)
                    ->where('evaluation_year', $evalYear)
                    ->where('evaluation_status', '!=', 'PENDING')
                    ->where('total_score', '>=', $qualify)
                    ->orderByDesc('total_score')
                    ->orderByDesc('leader_score')
                    ->limit(2)
                    ->get();

                if (isset($topRanked[0])) {
                    DB::table('monthly_evaluations')
                        ->where('evaluation_id', $topRanked[0]->evaluation_id)
                        ->update(['eom_badge' => 'GOLD']);
                }
                if (isset($topRanked[1])) {
                    DB::table('monthly_evaluations')
                        ->where('evaluation_id', $topRanked[1]->evaluation_id)
                        ->update(['eom_badge' => 'SILVER']);
                }
            }
        }

        return response()->json([
            'status'  => 'success',
            'payload' => $this->buildLeaderInputRatingPayload(),
        ]);
    }

    // =========================================================================
    // PAPARAN & API LEADER - TEAM STATUS (SAMBUNGAN DATABASE MYSQL)
    // =========================================================================
    private function buildLeaderTeamStatusPayload()
    {
        $user = Auth::user();

        $matchingDeptIds = [];
        $deptName = 'WebTech';

        if (!empty($user->department_id)) {
            $matchingDeptIds[] = (int) $user->department_id;
            $dept = DB::table('departments')
                ->where('department_id', $user->department_id)
                ->first();

            if ($dept && !empty($dept->department_name)) {
                $deptName = $dept->department_name;
                $sameNameIds = DB::table('departments')
                    ->where('department_name', 'LIKE', '%' . trim($deptName) . '%')
                    ->pluck('department_id')
                    ->map(fn($id) => (int) $id)
                    ->toArray();
                $matchingDeptIds = array_values(array_unique(array_merge($matchingDeptIds, $sameNameIds)));
            }
        } else {
            $dept = DB::table('departments')->orderBy('department_id')->first();
            if ($dept) {
                $deptName = $dept->department_name;
                $matchingDeptIds = DB::table('departments')
                    ->where('department_name', 'LIKE', '%' . trim($deptName) . '%')
                    ->pluck('department_id')
                    ->map(fn($id) => (int) $id)
                    ->toArray();
            }
        }

        $evalYear  = (int) date('Y');
        $evalMonth = (int) date('n');

        $settings = DB::table('kpi_settings')->first();
        $thresholds = [
            'gold'   => (float) ($settings->gold_threshold ?? 85.0),
            'silver' => (float) ($settings->silver_threshold ?? 70.0),
            'bronze' => (float) ($settings->bronze_threshold ?? 50.0),
        ];

        $staffRows = User::whereIn('department_id', $matchingDeptIds)
            ->whereIn(DB::raw('UPPER(role)'), ['STAFF', 'WORKER'])
            ->where('worker_id', '!=', $user->worker_id)
            ->orderBy('full_name', 'asc')
            ->get();

        $members = $staffRows->map(function ($staff) use ($evalMonth, $evalYear) {
            $eval = DB::table('monthly_evaluations')
                ->where('worker_id', $staff->worker_id)
                ->where('evaluation_month', $evalMonth)
                ->where('evaluation_year', $evalYear)
                ->first();

            if ($eval) {
                $taskScore = round((float) $eval->tasks_score, 1);
                $attScore  = round((float) $eval->attendance_score, 1);
            } else {
                $taskScore = round($this->calculateMonthlyTaskScore($staff->worker_id, $evalYear, $evalMonth), 1);
                $attScore  = 20.0;
            }

            $isPending = !$eval || (strtoupper($eval->evaluation_status ?? 'PENDING') === 'PENDING' && empty($eval->submitted_by_leader));

            return [
                'id'     => $staff->worker_id,
                'name'   => strtoupper($staff->full_name),
                'task'   => $taskScore,
                'att'    => $attScore,
                'leader' => $isPending ? null : round((float) $eval->leader_score, 1),
            ];
        })->values()->all();

        return [
            'deptName'   => strtoupper($deptName),
            'deptLabel'  => $deptName,
            'evalMonth'  => $evalMonth,
            'evalYear'   => $evalYear,
            'thresholds' => $thresholds,
            'members'    => $members,
        ];
    }

    public function leaderTeamStatus()
    {
        $seedData = $this->buildLeaderTeamStatusPayload();
        return view('teamstatus', compact('seedData'));
    }

    public function leaderTeamStatusData()
    {
        return response()->json($this->buildLeaderTeamStatusPayload());
    }

    // =========================================================================
    // PAPARAN & API LEADER - REPORTS & PERFORMANCE SLIPS
    // =========================================================================
    private function buildLeaderReportsPayload()
    {
        $user = Auth::user();

        $matchingDeptIds = [];
        $deptName = 'WebTech';

        if (!empty($user->department_id)) {
            $matchingDeptIds[] = (int) $user->department_id;
            $dept = DB::table('departments')
                ->where('department_id', $user->department_id)
                ->first();

            if ($dept && !empty($dept->department_name)) {
                $deptName = $dept->department_name;
                $sameNameIds = DB::table('departments')
                    ->where('department_name', 'LIKE', '%' . trim($deptName) . '%')
                    ->pluck('department_id')
                    ->map(fn($id) => (int) $id)
                    ->toArray();
                $matchingDeptIds = array_values(array_unique(array_merge($matchingDeptIds, $sameNameIds)));
            }
        } else {
            $dept = DB::table('departments')->orderBy('department_id')->first();
            if ($dept) {
                $deptName = $dept->department_name;
                $matchingDeptIds = DB::table('departments')
                    ->where('department_name', 'LIKE', '%' . trim($deptName) . '%')
                    ->pluck('department_id')
                    ->map(fn($id) => (int) $id)
                    ->toArray();
            }
        }

        $settings = DB::table('kpi_settings')->first();
        $thresholds = [
            'gold'   => (float) ($settings->gold_threshold ?? 85.0),
            'silver' => (float) ($settings->silver_threshold ?? 70.0),
            'bronze' => (float) ($settings->bronze_threshold ?? 50.0),
        ];

        $staffRows = User::whereIn('department_id', $matchingDeptIds)
            ->whereIn(DB::raw('UPPER(role)'), ['STAFF', 'WORKER'])
            ->where('worker_id', '!=', $user->worker_id)
            ->orderBy('full_name', 'asc')
            ->get();

        $staffWorkerIds = $staffRows->pluck('worker_id')->toArray();

        // Kumpul 6 bulan terakhir + mana-mana bulan sejarah yang ada dalam monthly_evaluations
        $periods  = [];
        $cursor   = \Carbon\Carbon::now()->startOfMonth()->subMonths(5);
        $endMonth = \Carbon\Carbon::now()->startOfMonth();

        while ($cursor->lte($endMonth)) {
            $ymKey = sprintf('%04d-%02d', $cursor->year, $cursor->month);
            $periods[$ymKey] = ['year' => (int) $cursor->year, 'month' => (int) $cursor->month];
            $cursor->addMonth();
        }

        if (!empty($staffWorkerIds)) {
            $dbPeriods = DB::table('monthly_evaluations')
                ->whereIn('worker_id', $staffWorkerIds)
                ->select('evaluation_year', 'evaluation_month')
                ->distinct()
                ->get();

            foreach ($dbPeriods as $dp) {
                $y = (int) $dp->evaluation_year;
                $m = (int) $dp->evaluation_month;
                if ($y > 2000 && $m >= 1 && $m <= 12) {
                    $ymKey = sprintf('%04d-%02d', $y, $m);
                    $periods[$ymKey] = ['year' => $y, 'month' => $m];
                }
            }
        }

        ksort($periods);
        $periods = array_slice($periods, -6, 6, true);

        // Ambil semua rekod penghantaran HR bagi jabatan ini daripada hr_submissions
        $submissions = DB::table('hr_submissions')
            ->where(DB::raw('UPPER(department_name)'), strtoupper(trim($deptName)))
            ->get();

        $curYear   = (int) date('Y');
        $curMonth  = (int) date('n');
        $monthKeys = [];
        $months    = [];

        foreach ($periods as $p) {
            $y = $p['year'];
            $m = $p['month'];
            $dateObj     = \Carbon\Carbon::createFromDate($y, $m, 1);
            $monthKey    = strtoupper($dateObj->format('F_Y')); // cth: NOVEMBER_2026
            $monthTitle  = strtoupper($dateObj->format('F Y')); // cth: NOVEMBER 2026
            $monthKeys[] = $monthKey;

            $staffList   = [];
            $latestDraft = null;

            foreach ($staffRows as $staff) {
                $eval = DB::table('monthly_evaluations')
                    ->where('worker_id', $staff->worker_id)
                    ->where('evaluation_month', $m)
                    ->where('evaluation_year', $y)
                    ->first();

                if ($eval) {
                    $taskScore   = round((float) $eval->tasks_score, 1);
                    $attScore    = round((float) $eval->attendance_score, 1);
                    $isPending   = (strtoupper($eval->evaluation_status ?? 'PENDING') === 'PENDING' && empty($eval->submitted_by_leader));
                    $leaderScore = $isPending ? null : round((float) $eval->leader_score, 1);

                    if (!empty($eval->submitted_at) && (!$latestDraft || $eval->submitted_at > $latestDraft)) {
                        $latestDraft = $eval->submitted_at;
                    }
                } elseif ($y === $curYear && $m === $curMonth) {
                    $taskScore   = round($this->calculateMonthlyTaskScore($staff->worker_id, $y, $m), 1);
                    $attScore    = 20.0;
                    $leaderScore = null;
                } else {
                    $taskScore   = 0.0;
                    $attScore    = 20.0;
                    $leaderScore = null;
                }

                $staffList[] = [
                    'id'     => (string) $staff->worker_id,
                    'empId'  => (string) $staff->worker_id,
                    'name'   => strtoupper($staff->full_name),
                    'task'   => $taskScore,
                    'att'    => $attScore,
                    'leader' => $leaderScore,
                ];
            }

            // Semak status penghantaran dalam jadual hr_submissions
            $subRow = $submissions->first(function ($row) use ($y, $m) {
                return (int) $row->evaluation_year === $y && (int) $row->evaluation_month === $m;
            });

            $submittedAt = null;
            $submittedBy = $user->full_name;
            if ($subRow) {
                $submittedAt   = \Carbon\Carbon::parse($subRow->submitted_at)->toIso8601String();
                $submitterUser = User::where('worker_id', $subRow->submitted_by)->first();
                if ($submitterUser) {
                    $submittedBy = $submitterUser->full_name;
                }
            }

            $fallbackEnd = $dateObj->copy()->endOfMonth()->setTime(16, 0)->toIso8601String();

            $months[$monthKey] = [
                'title'       => $monthTitle,
                'submittedAt' => $submittedAt,
                'submittedBy' => $submittedBy,
                'draftAt'     => $latestDraft ? \Carbon\Carbon::parse($latestDraft)->toIso8601String() : $fallbackEnd,
                'syncAt'      => $fallbackEnd,
                'staff'       => $staffList,
            ];
        }

        return [
            'deptName'     => strtoupper($deptName),
            'deptLabel'    => $deptName,
            'leaderName'   => $user->full_name,
            'defaultMonth' => end($monthKeys) ?: '',
            'monthKeys'    => $monthKeys,
            'months'       => $months,
            'thresholds'   => $thresholds,
        ];
    }

    public function leaderReports()
    {
        $seedData = $this->buildLeaderReportsPayload();
        return view('deptreport', compact('seedData'));
    }

    public function leaderReportsData()
    {
        return response()->json($this->buildLeaderReportsPayload());
    }

    public function submitLeaderReportToHr(Request $request)
    {
        $request->validate([
            'month' => 'required|string|max:30',
        ]);

        $user      = Auth::user();
        $monthKey  = strtoupper(trim($request->input('month')));
        $payload   = $this->buildLeaderReportsPayload();
        $monthData = $payload['months'][$monthKey] ?? null;

        if (!$monthData) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid report month.'
            ], 422);
        }

        $staffList  = $monthData['staff'] ?? [];
        $totalStaff = count($staffList);
        $completed  = array_filter($staffList, fn($s) => $s['leader'] !== null);
        $doneCount  = count($completed);

        if ($totalStaff === 0 || $doneCount < $totalStaff) {
            return response()->json([
                'success' => false,
                'message' => 'All team member evaluations must be completed before being submitted to HR.'
            ], 422);
        }

        $sum = 0;
        foreach ($completed as $c) {
            $sum += ((float) $c['task'] + (float) $c['att'] + (float) $c['leader']);
        }
        $deptAvg  = round($sum / $doneCount, 1);
        $deptName = $payload['deptLabel'];

        $parsedDate = \Carbon\Carbon::createFromFormat('F_Y', ucfirst(strtolower($monthKey)))->startOfMonth();
        $evalYear   = (int) $parsedDate->year;
        $evalMonth  = (int) $parsedDate->month;
        $now        = now();

        $existing = DB::table('hr_submissions')
            ->where(DB::raw('UPPER(department_name)'), strtoupper(trim($deptName)))
            ->where('evaluation_year', $evalYear)
            ->where('evaluation_month', $evalMonth)
            ->first();

        if (!$existing) {
            DB::table('hr_submissions')->insert([
                'department_name'  => $deptName,
                'evaluation_month' => $evalMonth,
                'evaluation_year'  => $evalYear,
                'dept_avg_score'   => $deptAvg,
                'total_staff'      => $totalStaff,
                'submitted_by'     => $user->worker_id,
                'submitted_at'     => $now,
            ]);
        } else {
            $now = \Carbon\Carbon::parse($existing->submitted_at);
        }

        return response()->json([
            'success'     => true,
            'submittedAt' => $now->toIso8601String(),
            'submittedBy' => $user->full_name,
        ]);
    }

   // =========================================================================
    // FORGOT PASSWORD & RESET PASSWORD (EMAIL + 6-DIGIT OTP)
    // =========================================================================
    private function maskEmail(string $email): string
    {
        if (!str_contains($email, '@')) {
            return $email;
        }

        [$name, $domain] = explode('@', $email, 2);
        $visible = substr($name, 0, min(2, strlen($name)));

        return $visible . str_repeat('*', max(3, strlen($name) - 2)) . '@' . $domain;
    }

    private function sendOtpEmail(User $user, string $otp): void
    {
        $body = "Hello {$user->full_name},\n\n"
              . "Your One-Time Password (OTP) to reset your E-KPI System password is:\n\n"
              . "OTP CODE: {$otp}\n\n"
              . "This code is valid for 5 minutes. If you did not request a password reset, please ignore this email.\n\n"
              . "Regards,\n"
              . "Infinite Loop Media - E-KPI System";

        \Illuminate\Support\Facades\Mail::raw($body, function ($message) use ($user) {
            $message->to($user->email)
                    ->subject('E-KPI System - Password Reset OTP Code');
        });
    }

    public function showForgotPassword()
    {
        return view('forgotpw');
    }

    public function verifyForgotPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $email = strtolower(trim($request->input('email')));
        $user  = User::whereRaw('LOWER(TRIM(email)) = ?', [$email])->first();

        if (!$user) {
            return back()
                ->with('error', 'Email address not found in the system!')
                ->withInput();
        }

        $otp = (string) random_int(100000, 999999);

        try {
            $this->sendOtpEmail($user, $otp);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('OTP Mail Error: ' . $e->getMessage());

            return back()
                ->with('error', 'Unable to send OTP email right now. Please check mail settings or try again.')
                ->withInput();
        }

        $request->session()->put('password_reset_otp', [
            'worker_id'  => $user->worker_id,
            'email'      => $user->email,
            'otp_hash'   => Hash::make($otp),
            'expires_at' => now()->addMinutes(5)->timestamp,
            'attempts'   => 0,
        ]);

        return redirect()
            ->route('password.reset')
            ->with('success', 'A 6-digit OTP code has been sent to ' . $this->maskEmail($user->email) . '.');
    }

    public function resendForgotOtp(Request $request)
    {
        $otpData = $request->session()->get('password_reset_otp');

        if (!$otpData || empty($otpData['worker_id'])) {
            return redirect()
                ->route('password.request')
                ->with('error', 'Session expired. Please enter your email again.');
        }

        $user = User::where('worker_id', $otpData['worker_id'])->first();

        if (!$user) {
            $request->session()->forget('password_reset_otp');
            return redirect()
                ->route('password.request')
                ->with('error', 'Account not found.');
        }

        $otp = (string) random_int(100000, 999999);

        try {
            $this->sendOtpEmail($user, $otp);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('OTP Resend Error: ' . $e->getMessage());

            return back()->with('error', 'Failed to resend OTP email. Please try again.');
        }

        $request->session()->put('password_reset_otp', [
            'worker_id'  => $user->worker_id,
            'email'      => $user->email,
            'otp_hash'   => Hash::make($otp),
            'expires_at' => now()->addMinutes(5)->timestamp,
            'attempts'   => 0,
        ]);

        return back()->with('success', 'A new 6-digit OTP code has been sent to ' . $this->maskEmail($user->email) . '.');
    }

    public function showResetPassword(Request $request)
    {
        $otpData = $request->session()->get('password_reset_otp');

        if (!$otpData || empty($otpData['worker_id'])) {
            return redirect()
                ->route('password.request')
                ->with('error', 'Please enter your email to request an OTP first.');
        }

        $user = User::where('worker_id', $otpData['worker_id'])->first();

        if (!$user) {
            $request->session()->forget('password_reset_otp');
            return redirect()
                ->route('password.request')
                ->with('error', 'Account not found.');
        }

        $maskedEmail = $this->maskEmail($user->email);

        return view('resetpw', compact('user', 'maskedEmail'));
    }

    public function updateResetPassword(Request $request)
    {
        $otpData = $request->session()->get('password_reset_otp');

        if (!$otpData || empty($otpData['worker_id'])) {
            return redirect()
                ->route('password.request')
                ->with('error', 'Session expired. Please verify your email again.');
        }

        $request->validate([
            'otp'              => 'required|digits:6',
            'new_password'     => 'required|string|min:8',
            'confirm_password' => 'required|same:new_password',
        ], [
            'otp.required'          => 'Please enter the 6-digit OTP sent to your email.',
            'otp.digits'            => 'OTP code must be exactly 6 digits.',
            'new_password.min'      => 'Password must contain at least 8 characters.',
            'confirm_password.same' => 'Passwords do not match.',
        ]);

        if (now()->timestamp > ($otpData['expires_at'] ?? 0)) {
            return back()->withErrors(['otp' => 'Your OTP code has expired. Please click Resend OTP.']);
        }

        $attempts = (int) ($otpData['attempts'] ?? 0);
        if ($attempts >= 5) {
            $request->session()->forget('password_reset_otp');
            return redirect()
                ->route('password.request')
                ->with('error', 'Too many invalid OTP attempts. Please request a new OTP.');
        }

        if (!Hash::check(trim($request->input('otp')), $otpData['otp_hash'])) {
            $otpData['attempts'] = $attempts + 1;
            $request->session()->put('password_reset_otp', $otpData);

            return back()->withErrors(['otp' => 'Invalid OTP code. Please check your email and try again.']);
        }

        User::where('worker_id', $otpData['worker_id'])->update([
            'password_hash' => Hash::make($request->input('new_password')),
        ]);

        $request->session()->forget('password_reset_otp');

        return redirect()
            ->route('login')
            ->with('success', 'Password reset successful. You can now log in.');
    }
}