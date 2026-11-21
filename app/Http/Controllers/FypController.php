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

        return view('worker', compact('user', 'kpiScores', 'jobs', 'setting'));
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
        $unhealthyStaffCount = User::where('role', 'STAFF')
                                ->where('kpi_score', '<', 75)
                                ->count();

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

            return (object) [
                'department_name'   => $dept->department_name,
                'avg_kpi'           => round($avgKpi, 1),
                'total_staff'       => $staffCount,
                'submitted'         => $submittedCount,
                'total_submissions' => $staffCount,
                'status'            => $isComplete ? 'COMPLETE' : 'PENDING',
                'gold_badges'       => $goldCount,
                'silver_badges'     => $silverCount,
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

        $aiInsights = [
            [
                'tag'   => 'AI Recommendation',
                'title' => ($topWorker->full_name ?? 'Staff') . ' (' . ($selectedDeptName) . ')',
                'desc'  => 'Recommended for the Excellence Award based on the highest KPI score in the database.',
                'stat'  => ($topWorker->kpi_score ?? 0) . '% Efficiency Rating'
            ],
            [
                'tag'   => 'Predictive Alert',
                'title' => ($topDept->department_name ?? 'R&D') . ' Department Trend',
                'desc'  => 'Department target achievement is currently leading at ' . ($topDept->target_score ?? 0) . '% for Quarter ' . $qNum . ' (' . $currentQuarter . ').',
                'stat'  => '+' . ($topDept->target_score ?? 0) . '% Average Target'
            ],
            [
                'tag'   => 'Workload Optimisation',
                'title' => 'Staff Workload Alert',
                'desc'  => $unhealthyStaffCount . ' staff member(s) detected requiring attention and task redistribution.',
                'stat'  => $unhealthyStaffCount . ' At-Risk Staff'
            ]
        ];

        return view('hr', compact(
            'avgKpiScore',
            'unhealthyStaffCount',
            'targetAchievement',
            'kpiTrendDiff',
            'departmentsPerformance',
            'badgeRankings',
            'selectedDeptName',
            'kpiTrend',
            'aiInsights'
        ));
    }

    public function kpiManagement(Request $request)
    {
        $selectedQuarter = $request->get('quarter', 'Q' . (int) ceil(date('n') / 3));
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

    // PAPARAN HR - EMERGENCY LEAVE (SAMBUNGAN DATABASE MYSQL)
    public function emerLeave()
    {
        // 1. Dapatkan senarai staf & jabatan sebenar dari jadual users & departments
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

        // 2. Dapatkan rekod audit log dari jadual emergency_leaves
        $auditLogs = [];
        if (Schema::hasTable('emergency_leaves')) {
            $columns = Schema::getColumnListing('emergency_leaves');
            $pk = in_array('id', $columns) ? 'id' : (in_array('leave_id', $columns) ? 'leave_id' : $columns[0]);

            $leaves = DB::table('emergency_leaves')->orderBy($pk, 'desc')->get();

            foreach ($leaves as $index => $lv) {
                $user = User::where('worker_id', $lv->worker_id ?? '')->first();
                $dept = $user ? Department::where('department_id', $user->department_id)->first() : null;

                $rawDate = $lv->leave_date ?? $lv->date ?? date('Y-m-d');
                $formattedDate = substr((string) $rawDate, 0, 10);

                $auditLogs[] = [
                    'id'         => $lv->$pk ?? ($index + 1),
                    'worker_id'  => $lv->worker_id ?? '',
                    'staff'      => $user->full_name ?? ($lv->staff_name ?? $lv->worker_id ?? 'Staff'),
                    'department' => $dept->department_name ?? ($lv->department ?? 'General'),
                    'leaveDate'  => $formattedDate,
                    'reason'     => $lv->reason ?? 'Emergency Leave',
                    'notes'      => $lv->notes ?? ($lv->remarks ?? ''),
                    'status'     => strtolower($lv->status ?? 'approved'),
                ];
            }
        }

        return view('emerleave', compact('staffList', 'auditLogs'));
    }

    // SIMPAN MANUAL ATTENDANCE OVERRIDE KE DATABASE MYSQL
    public function storeEmerLeave(Request $request)
    {
        $validated = $request->validate([
            'worker_id' => 'required|string',
            'leaveDate' => 'required|date',
            'reason'    => 'required|string|max:100',
            'notes'     => 'nullable|string|max:200',
            'status'    => 'required|in:approved,rejected',
        ]);

        // Bina jadual emergency_leaves secara automatik jika belum wujud dalam MySQL
        if (!Schema::hasTable('emergency_leaves')) {
            Schema::create('emergency_leaves', function ($table) {
                $table->id();
                $table->string('worker_id');
                $table->date('leave_date');
                $table->string('reason');
                $table->text('notes')->nullable();
                $table->string('status', 20)->default('approved');
                $table->string('impact', 50)->nullable();
                $table->timestamps();
            });
        }

        $columns = Schema::getColumnListing('emergency_leaves');
        $dateCol = in_array('leave_date', $columns) ? 'leave_date' : (in_array('date', $columns) ? 'date' : null);

        // Semak rekod bertindih untuk staf & tarikh yang sama
        if ($dateCol) {
            $exists = DB::table('emergency_leaves')
                ->where('worker_id', $validated['worker_id'])
                ->where($dateCol, $validated['leaveDate'])
                ->exists();

            if ($exists) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'This staff member already has an emergency leave record on the selected date.'
                ], 422);
            }
        }

        $user        = User::where('worker_id', $validated['worker_id'])->first();
        $dept        = $user ? DB::table('departments')->where('department_id', $user->department_id)->first() : null;
        $statusLower = strtolower($validated['status']);
        $impactText  = $statusLower === 'approved' ? 'No Deduct' : '-5% Deducted';

        // Susun data mengikut kolum sebenar yang wujud dalam jadual emergency_leaves
        $insertData = [];
        if (in_array('worker_id', $columns))  $insertData['worker_id']  = $validated['worker_id'];
        if ($dateCol)                         $insertData[$dateCol]     = $validated['leaveDate'];
        if (in_array('reason', $columns))     $insertData['reason']     = $validated['reason'];
        if (in_array('notes', $columns))      $insertData['notes']      = $validated['notes'] ?? '';
        if (in_array('remarks', $columns))    $insertData['remarks']    = $validated['notes'] ?? '';
        if (in_array('status', $columns))     $insertData['status']     = strtoupper($statusLower);
        if (in_array('impact', $columns))     $insertData['impact']     = $impactText;
        if (in_array('created_at', $columns)) $insertData['created_at'] = now();
        if (in_array('updated_at', $columns)) $insertData['updated_at'] = now();

        $insertedId = DB::table('emergency_leaves')->insertGetId($insertData);

        // Tolak markah kehadiran dalam jadual monthly_evaluations & users jika status = rejected (-5%)
        if ($statusLower === 'rejected' && $user) {
            $setting   = KpiSetting::first();
            $attWeight = (float) ($setting->attendance_weightage ?? 20);
            $deduction = round(5 * ($attWeight / 100), 2); // 5% daripada 20 = 1.00

            // Gunakan bulan & tahun kalendar semasa supaya reset setiap bulan
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

            // Segerakkan jadual users
            $userCols = Schema::getColumnListing('users');
            if (in_array('attendance_score', $userCols)) {
                $newAtt = max(0, ((float) ($user->attendance_score ?? 100)) - 5);
                DB::table('users')->where('worker_id', $user->worker_id)->update(['attendance_score' => $newAtt]);
            } elseif (in_array('kpi_score', $userCols) && $user->kpi_score !== null) {
                $newKpi = max(0, round(((float) $user->kpi_score) - $deduction, 1));
                DB::table('users')->where('worker_id', $user->worker_id)->update(['kpi_score' => $newKpi]);
            }
        }

        return response()->json([
            'status' => 'success',
            'record' => [
                'id'         => $insertedId,
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
            'rating'    => 'nullable|numeric|min:0|max:40',
        ]);

        $leader    = Auth::user();
        $workerId  = $validated['worker_id'];
        $ratingVal = $validated['rating'];

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

        if ($ratingVal === null) {
            $totalScore = round($tasksScore + $attScore, 2);
            $payload = [
                'leader_score'        => 0.00,
                'total_score'         => $totalScore,
                'eom_badge'           => 'NONE',
                'evaluation_status'   => 'PENDING',
                'submitted_by_leader' => null,
                'submitted_at'        => null,
            ];
        } else {
            $rating     = round((float) $ratingVal, 1);
            $totalScore = round($tasksScore + $attScore + $rating, 2);
            $status     = $totalScore >= 90 ? 'EXCELLENCE' : ($totalScore >= 80 ? 'GOOD' : 'ATTENTION');

            $payload = [
                'leader_score'        => $rating,
                'total_score'         => $totalScore,
                'evaluation_status'   => $status,
                'submitted_by_leader' => $leader->worker_id,
                'submitted_at'        => now(),
            ];
        }

        if (Schema::hasColumn('monthly_evaluations', 'criteria_scores')) {
            $payload['criteria_scores'] = null;
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
    // FORGOT PASSWORD & RESET PASSWORD
    // =========================================================================
    public function showForgotPassword()
    {
        return view('forgotpw');
    }

    public function verifyForgotPassword(Request $request)
    {
        $request->validate([
            'worker_id' => 'required|string',
        ]);

        $workerId = trim($request->input('worker_id'));
        $user = User::where('worker_id', $workerId)->first();

        if (!$user) {
            return back()
                ->with('error', 'Worker ID not found in the system!')
                ->withInput();
        }

        $request->session()->put('reset_worker_id', $user->worker_id);

        return redirect()->route('password.reset');
    }

    public function showResetPassword(Request $request)
    {
        $workerId = $request->session()->get('reset_worker_id');

        if (!$workerId) {
            return redirect()
                ->route('password.request')
                ->with('error', 'Please verify your Worker ID first.');
        }

        $user = User::where('worker_id', $workerId)->first();

        if (!$user) {
            $request->session()->forget('reset_worker_id');
            return redirect()
                ->route('password.request')
                ->with('error', 'Account not found.');
        }

        return view('resetpw', compact('user'));
    }

    public function updateResetPassword(Request $request)
    {
        $workerId = $request->session()->get('reset_worker_id');

        if (!$workerId) {
            return redirect()
                ->route('password.request')
                ->with('error', 'Session expired. Please verify your Worker ID again.');
        }

        $request->validate([
            'new_password'     => 'required|string|min:8',
            'confirm_password' => 'required|same:new_password',
        ], [
            'new_password.min'      => 'Password must contain at least 8 characters.',
            'confirm_password.same' => 'Passwords do not match.',
        ]);

        User::where('worker_id', $workerId)->update([
            'password_hash' => Hash::make($request->input('new_password')),
        ]);

        $request->session()->forget('reset_worker_id');

        return redirect()
            ->route('login')
            ->with('success', 'Password reset successful. You can now log in.');
    }
}