<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AIAnalysisService
{
    protected string $apiKey;
    protected string $model;

    public function __construct()
    {
        $this->apiKey = (string) config('services.gemini.key');
        $this->model = (string) config('services.gemini.model', 'gemini-3.8-flash');
    }

    /**
     * 1. Analisis Prestasi Silang Jadual (5 Kategori + Huraian Modal) untuk HR Dashboard
     */
    public function analyzeHrOverview(int $variation = 1): array
    {
        $settings  = DB::table('kpi_settings')->first();
        $evalYear  = (int) date('Y');
        $evalMonth = (int) date('n');
        $prevDate  = \Carbon\Carbon::createFromDate($evalYear, $evalMonth, 1)->subMonth();
        $prevYear  = (int) $prevDate->year;
        $prevMonth = (int) $prevDate->month;
        $quarter   = 'Q' . (int) ceil($evalMonth / 3);

        // 1. Rekod penilaian bulan semasa & perbandingan dengan bulan lepas
        $currentEvals = DB::table('monthly_evaluations as me')
            ->join('users as u', 'me.worker_id', '=', 'u.worker_id')
            ->leftJoin('departments as d', 'u.department_id', '=', 'd.department_id')
            ->where('me.evaluation_year', $evalYear)
            ->where('me.evaluation_month', $evalMonth)
            ->select(
                'u.worker_id as staff_id',
                'u.full_name as staff_name',
                'd.department_name as department',
                'me.tasks_score as task_completion',
                'me.attendance_score as attendance_rating',
                'me.leader_score as leader_rating',
                'me.total_score as current_kpi_score',
                'me.eom_badge as award_badge',
                'me.evaluation_status as submission_status'
            )
            ->get();

        if ($currentEvals->isEmpty()) {
            $currentEvals = DB::table('monthly_evaluations as me')
                ->join('users as u', 'me.worker_id', '=', 'u.worker_id')
                ->leftJoin('departments as d', 'u.department_id', '=', 'd.department_id')
                ->select(
                    'u.worker_id as staff_id',
                    'u.full_name as staff_name',
                    'd.department_name as department',
                    'me.tasks_score as task_completion',
                    'me.attendance_score as attendance_rating',
                    'me.leader_score as leader_rating',
                    'me.total_score as current_kpi_score',
                    'me.eom_badge as award_badge',
                    'me.evaluation_status as submission_status'
                )
                ->orderByDesc('me.evaluation_year')
                ->orderByDesc('me.evaluation_month')
                ->limit(20)
                ->get();
        }

        $prevScores = DB::table('monthly_evaluations')
            ->where('evaluation_year', $prevYear)
            ->where('evaluation_month', $prevMonth)
            ->pluck('total_score', 'worker_id');

        $evaluationsWithDelta = $currentEvals->map(function ($row) use ($prevScores) {
            $prev = isset($prevScores[$row->staff_id]) ? (float) $prevScores[$row->staff_id] : null;
            $row->previous_month_score = $prev;
            $row->score_change = $prev !== null ? round((float) $row->current_kpi_score - $prev, 1) : null;
            return $row;
        });

        // 2. Beban tugasan tertunggak (PENDING / IN_PROGRESS) dari tasks_etracker
        $backlogByStaff = \Illuminate\Support\Facades\Schema::hasTable('tasks_etracker')
            ? DB::table('tasks_etracker as t')
                ->join('users as u', 't.worker_id', '=', 'u.worker_id')
                ->leftJoin('departments as d', 'u.department_id', '=', 'd.department_id')
                ->whereRaw('UPPER(TRIM(t.status)) != ?', ['COMPLETED'])
                ->select(
                    'u.full_name as staff_name',
                    'd.department_name as department',
                    DB::raw('COUNT(*) as unfinished_tasks')
                )
                ->groupBy('u.worker_id', 'u.full_name', 'd.department_name')
                ->orderByDesc('unfinished_tasks')
                ->limit(5)
                ->get()
            : collect();

        // 3. Permohonan Emergency Leave yang masih menunggu kelulusan HR
        $pendingLeavesCount = 0;
        if (\Illuminate\Support\Facades\Schema::hasTable('emergency_leaves')) {
            $statusCol = \Illuminate\Support\Facades\Schema::hasColumn('emergency_leaves', 'status') ? 'status' : null;
            $pendingLeavesCount = $statusCol
                ? DB::table('emergency_leaves')->whereRaw("UPPER(TRIM(COALESCE(`{$statusCol}`, 'PENDING'))) = ?", ['PENDING'])->count()
                : DB::table('emergency_leaves')->count();
        }

        // 4. Sasaran jabatan suku tahunan vs laporan yang sudah dihantar ke HR
        $deptTargets = DB::table('departments')
            ->where('quarter', $quarter)
            ->select('department_name', 'target_score', 'status')
            ->get();

        $submittedDepts = \Illuminate\Support\Facades\Schema::hasTable('hr_submissions')
            ? DB::table('hr_submissions')
                ->where('evaluation_year', $evalYear)
                ->where('evaluation_month', $evalMonth)
                ->pluck('department_name')
                ->map(fn($n) => strtoupper(trim($n)))
                ->all()
            : [];

        // Tapis hanya staf paling relevan (3 terendah, 3 tertinggi, dan semua PENDING) supaya prompt ringan & pantas
        $relevantEvals = $evaluationsWithDelta->sortBy('current_kpi_score')->take(4)
            ->merge($evaluationsWithDelta->sortByDesc('current_kpi_score')->take(3))
            ->merge($evaluationsWithDelta->where('submission_status', 'PENDING'))
            ->unique('staff_id')
            ->values();

        $payloadData = [
            'thresholds'               => $settings,
            'department_targets'       => $deptTargets,
            'departments_submitted_hr' => $submittedDepts,
            'pending_emergency_leaves' => $pendingLeavesCount,
            'unfinished_tasks_backlog' => $backlogByStaff,
            'staff_evaluations'        => $relevantEvals,
        ];

        $focusAngles = [
            1 => 'Highlight the sharpest score drops with root causes, heaviest task backlogs, pending approvals, department target gaps, and top EOM contenders.',
            2 => 'Focus on preventative coaching for borderline staff, task completion bottlenecks, HR review queues, under-target teams, and month-over-month improvers.',
            3 => 'Provide an executive HR briefing covering root-cause risk, workload distribution, submission compliance, target variance, and award-ready talent.',
        ];
        $selectedFocus = $focusAngles[(($variation - 1) % 3) + 1];

        $prompt = <<<PROMPT
        You are an HR performance analyst for the E-KPI System (Infinite Loop Media Sdn Bhd).
        Analyze the cross-module HR dataset below.
        Analysis Perspective (Variation #{$variation}): {$selectedFocus}

        System Data:
        {$this->toJson($payloadData)}

        STRICT LANGUAGE & CATEGORY RULES:
        1. Write ALL output strictly in clear, professional English. Do NOT use Malay.
        2. NEVER use database column names or underscores like "leader_score", "tasks_score", "attendance_score", "total_score", or "evaluation_status". Use natural HR terms like "leader rating", "task completion", "attendance", and "overall KPI score".
        3. Generate 5 distinct cards covering these exact 5 categories (one card per category):
           - "ROOT-CAUSE ALERT": Identify a staff member with low/dropping score and state the specific weak component (task completion, attendance, or leader rating) plus month-over-month change if available.
           - "WORKLOAD & OVERLOAD": Highlight the staff/department carrying the highest unfinished task backlog.
           - "COMPLIANCE & QUEUE": Highlight pending staff evaluations, unsubmitted department reports, or pending emergency leave reviews ({$pendingLeavesCount} pending).
           - "DEPT BENCHMARK": Compare a lagging department's performance against its quarterly target score.
           - "EOM & TOP MOMENTUM": Highlight the highest-scoring staff or strongest month-over-month improver eligible for Employee of the Month.
        4. Keep text concise for the mini card ("summary"), and provide a richer 2-sentence explanation for the detailed modal ("details"):
           - "title": Maximum 5 words.
           - "summary": 1 short sentence (maximum 18 words) with real names and numbers.
           - "details": 2 clear sentences explaining the root cause, metric comparison, and operational impact.
           - "recommendation": 1 short actionable sentence for HR (maximum 14 words).

        Return ONLY valid JSON matching this exact structure:
        {
          "insights": [
            {
              "tag": "ROOT-CAUSE ALERT | WORKLOAD & OVERLOAD | COMPLIANCE & QUEUE | DEPT BENCHMARK | EOM & TOP MOMENTUM",
              "title": "Short title maximum 5 words",
              "summary": "One concise sentence maximum 18 words.",
              "details": "Two detailed sentences with exact metrics and context for the HR modal.",
              "recommendation": "One concise action sentence maximum 14 words."
            }
          ]
        }
        PROMPT;

        $temperature = $variation > 1 ? 0.65 : 0.35;
        $result = $this->callGemini($prompt, $temperature);

        if (empty($result['insights'])) {
            $sortedLow     = $evaluationsWithDelta->sortBy('current_kpi_score')->values();
            $sortedHigh    = $evaluationsWithDelta->sortByDesc('current_kpi_score')->values();
            $pendingEvals  = $evaluationsWithDelta->where('submission_status', 'PENDING')->values();
            $sortedBacklog = $backlogByStaff->values();
            $sortedTargets = $deptTargets->sortBy('target_score')->values();

            $idx = max(0, $variation - 1);
            $fallbackInsights = [];

            // 1. ROOT-CAUSE ALERT (Pusing ikut staf skor terendah ke-1, ke-2, ke-3...)
            if ($sortedLow->isNotEmpty()) {
                $targetEval = $sortedLow->get($idx % $sortedLow->count());
                $weakArea = ((float) $targetEval->task_completion < 25)
                    ? 'low task completion'
                    : (((float) $targetEval->attendance_rating < 18) ? 'attendance deductions' : 'low leader rating');
                $deltaText = $targetEval->score_change !== null
                    ? ' (' . ($targetEval->score_change >= 0 ? '+' : '') . $targetEval->score_change . '% vs last month)'
                    : '';

                $fallbackInsights[] = [
                    'tag'            => 'ROOT-CAUSE ALERT',
                    'title'          => "{$targetEval->staff_name} Score Alert",
                    'summary'        => "{$targetEval->staff_name} ({$targetEval->department}) recorded {$targetEval->current_kpi_score}%{$deltaText}, driven by {$weakArea}.",
                    'details'        => "{$targetEval->staff_name} from {$targetEval->department} recorded an overall KPI score of {$targetEval->current_kpi_score}%{$deltaText}, pulled down mainly by {$weakArea}. Early intervention with the department leader is advised before the end-of-month cutoff.",
                    'recommendation' => "Arrange a targeted coaching review with the {$targetEval->department} leader.",
                ];
            }

            // 2. WORKLOAD & OVERLOAD (Pusing ikut senarai tugasan tertunggak)
            if ($sortedBacklog->isNotEmpty()) {
                $topBacklog = $sortedBacklog->get($idx % $sortedBacklog->count());
                $fallbackInsights[] = [
                    'tag'            => 'WORKLOAD & OVERLOAD',
                    'title'          => "{$topBacklog->staff_name} Task Backlog",
                    'summary'        => "{$topBacklog->staff_name} ({$topBacklog->department}) currently carries {$topBacklog->unfinished_tasks} unfinished tasks in the tracker.",
                    'details'        => "{$topBacklog->staff_name} in {$topBacklog->department} has {$topBacklog->unfinished_tasks} active or pending tasks carried over in the task tracker. Unresolved carry-over tasks directly reduce monthly task completion weightage.",
                    'recommendation' => "Rebalance active deliverables with {$topBacklog->department} leadership before cutoff.",
                ];
            }

            // 3. COMPLIANCE & QUEUE
            $pendingEval = $pendingEvals->isNotEmpty() ? $pendingEvals->get($idx % $pendingEvals->count()) : null;
            $fallbackInsights[] = [
                'tag'            => 'COMPLIANCE & QUEUE',
                'title'          => $pendingEval ? "{$pendingEval->staff_name} Review Pending" : 'HR Approval Queue Status',
                'summary'        => $pendingEval
                    ? "{$pendingEval->staff_name} ({$pendingEval->department}) has an incomplete evaluation alongside {$pendingLeavesCount} pending emergency leave requests."
                    : "There are currently {$pendingLeavesCount} emergency leave requests awaiting HR verification across departments.",
                'details'        => "Operational tracking shows {$pendingLeavesCount} emergency leave submissions awaiting HR verification alongside " . $pendingEvals->count() . " pending monthly evaluation records. Clearing these items ensures accurate monthly attendance and final KPI calculations.",
                'recommendation' => 'Clear pending reviews and send reminders to incomplete departments.',
            ];

            // 4. DEPT BENCHMARK (Pusing ikut jabatan dalam quarter semasa)
            if ($sortedTargets->isNotEmpty()) {
                $targetDept = $sortedTargets->get($idx % $sortedTargets->count());
                $fallbackInsights[] = [
                    'tag'            => 'DEPT BENCHMARK',
                    'title'          => "{$targetDept->department_name} Target Benchmark",
                    'summary'        => "{$targetDept->department_name} is benchmarked against a {$targetDept->target_score}% quarterly target for {$quarter}.",
                    'details'        => "{$targetDept->department_name} is currently measured against its {$quarter} target score of {$targetDept->target_score}%. Proactive follow-up is recommended to close any remaining performance variance.",
                    'recommendation' => "Trigger an AI Follow-Up to align {$targetDept->department_name} milestones.",
                ];
            }

            // 5. EOM & TOP MOMENTUM (Pusing ikut senarai top performer)
            if ($sortedHigh->isNotEmpty()) {
                $topEval = $sortedHigh->get($idx % $sortedHigh->count());
                $fallbackInsights[] = [
                    'tag'            => 'EOM & TOP MOMENTUM',
                    'title'          => "{$topEval->staff_name} Top Momentum",
                    'summary'        => "{$topEval->staff_name} ({$topEval->department}) ranks among top performers with {$topEval->current_kpi_score}% overall KPI.",
                    'details'        => "{$topEval->staff_name} from {$topEval->department} achieved a strong overall KPI score of {$topEval->current_kpi_score}%, exceeding department expectations. This places them as a strong contender for Employee of the Month recognition.",
                    'recommendation' => "Shortlist {$topEval->staff_name} for the Employee of the Month award.",
                ];
            }

            return [
                'insights' => $fallbackInsights,
                'source'   => 'fallback',
            ];
        }

        $replacements = [
            'leader_score'         => 'leader rating',
            'leader_rating'        => 'leader rating',
            'tasks_score'          => 'task completion score',
            'task_performance'     => 'task completion score',
            'attendance_score'     => 'attendance score',
            'attendance_rating'    => 'attendance score',
            'total_score'          => 'overall KPI score',
            'current_kpi_score'    => 'overall KPI score',
            'previous_month_score' => 'last month score',
            'evaluation_status'    => 'submission status',
        ];

        $result['insights'] = array_map(function ($item) use ($replacements) {
            foreach (['title', 'summary', 'details', 'recommendation'] as $field) {
                if (!empty($item[$field])) {
                    $item[$field] = str_ireplace(
                        array_keys($replacements),
                        array_values($replacements),
                        $item[$field]
                    );
                }
            }
            return $item;
        }, array_slice($result['insights'], 0, 5));

        $result['source'] = 'gemini-ai';
        return $result;
    }

    /**
     * 2. Jana Draf E-mel Peringatan Automatik untuk Ketua Jabatan / Staf
     */
    public function generateReminderDraft(string $workerId, int $variation = 1): array
    {
        $user = DB::table('users as u')
            ->leftJoin('departments as d', 'u.department_id', '=', 'd.department_id')
            ->where('u.worker_id', $workerId)
            ->select('u.worker_id', 'u.full_name', 'u.email', 'u.role', 'u.kpi_score', 'd.department_name')
            ->first();

        $latestEval = DB::table('monthly_evaluations')
            ->where('worker_id', $workerId)
            ->orderByDesc('evaluation_year')
            ->orderByDesc('evaluation_month')
            ->first();

        $settings  = DB::table('kpi_settings')->first();
        $cutoffDay = $settings->cutoff_day ?? 28;
        $isLeader  = strtoupper($user->role ?? '') === 'LEADER';
        $name      = $user->full_name ?? $workerId;
        $dept      = strtoupper($user->department_name ?? 'DEPARTMENT');

        $tones = [
            1 => 'polite and supportive',
            2 => 'concise and action-oriented',
            3 => 'formal executive reminder',
        ];
        $selectedTone = $tones[(($variation - 1) % 3) + 1];

        $contextNote = $isLeader
            ? "Penerima ialah Ketua Jabatan (Department Leader) bernama {$name}. Ingatkan beliau untuk melengkapkan dan menghantar penilaian KPI bulanan semua staf di bawah jabatan {$dept} sebelum hari ke-{$cutoffDay}."
            : "Penerima ialah Staf bernama {$name} dengan skor KPI terkini {$user->kpi_score}%. Ingatkan beliau untuk menyemak prestasi KPI dan berbincang dengan Ketua Jabatan {$dept}.";

        $prompt = <<<PROMPT
Tulis satu draf e-mel peringatan dalam nada {$selectedTone} daripada pihak HR menggunakan sistem E-KPI (Infinite Loop Media Sdn Bhd).
Maklumat Penerima: {$this->toJson($user)}
Rekod Penilaian Terkini: {$this->toJson($latestEval)}
Konteks Tugasan: {$contextNote}
Variasi Ke: {$variation} (gunakan ayat yang segar dan berbeza daripada draf biasa).

Pulangkan HANYA dalam format JSON sah ini (dalam Bahasa Inggeris profesional):
{
  "subject": "Subjek e-mel yang jelas dan profesional",
  "body": "Isi kandungan e-mel lengkap bermula dengan 'Dear {$name},' dan penutup rasmi daripada HR Department, Infinite Loop Media Sdn Bhd"
}
PROMPT;

        $result = $this->callGemini($prompt);

        // 3 variasi templat peribadi jika API Gemini sesak (503) semasa klik Regenerate
        $idx = ($variation - 1) % 3;

        if ($isLeader) {
            $fallbackTemplates = [
                [
                    'subject' => "Action Required: Pending Monthly KPI Submissions ({$dept})",
                    'body'    => "Dear {$name},\n\nThis is a friendly reminder from HR to complete and submit the pending monthly KPI evaluations for your team in {$dept} before the {$cutoffDay}th of this month.\n\nYour prompt action helps us finalize company-wide performance analytics on schedule.\n\nBest regards,\nHR Department\nInfinite Loop Media Sdn Bhd",
                ],
                [
                    'subject' => "Follow-Up: {$dept} Monthly KPI Evaluation Deadline",
                    'body'    => "Dear {$name},\n\nOur E-KPI system indicates that monthly staff evaluations for {$dept} are still pending submission. Kindly review and finalize all pending team assessments before the {$cutoffDay}th cutoff date.\n\nPlease reach out to HR if you require any assistance.\n\nWarm regards,\nHR Department\nInfinite Loop Media Sdn Bhd",
                ],
                [
                    'subject' => "Reminder: Finalize {$dept} Team KPI Assessments",
                    'body'    => "Dear {$name},\n\nWe would like to remind you to submit the remaining monthly KPI ratings for {$dept} prior to the {$cutoffDay}th deadline so that monthly Employee of the Month (EOM) calculations can proceed smoothly.\n\nThank you for your leadership and cooperation.\n\nSincerely,\nHR Department\nInfinite Loop Media Sdn Bhd",
                ],
            ];
        } else {
            $fallbackTemplates = [
                [
                    'subject' => "Performance Review Notice: Monthly KPI Follow-Up ({$dept})",
                    'body'    => "Dear {$name},\n\nWe noticed your recent KPI performance requires attention. Please coordinate with your {$dept} department leader to review your targets and action plan for this quarter.\n\nBest regards,\nHR Department\nInfinite Loop Media Sdn Bhd",
                ],
                [
                    'subject' => "KPI Check-In & Coaching Session ({$dept})",
                    'body'    => "Dear {$name},\n\nAs part of our continuous performance support in {$dept}, HR encourages you to schedule a brief 1-on-1 check-in with your team leader to discuss task milestones and improvement goals.\n\nWarm regards,\nHR Department\nInfinite Loop Media Sdn Bhd",
                ],
                [
                    'subject' => "Action Plan: Upcoming KPI Target Alignment ({$dept})",
                    'body'    => "Dear {$name},\n\nKindly review your latest monthly evaluation metrics on the E-KPI portal and align with your {$dept} leader on key deliverables ahead of the {$cutoffDay}th review cycle.\n\nSincerely,\nHR Department\nInfinite Loop Media Sdn Bhd",
                ],
            ];
        }

        return [
            'recipient_email' => $user->email ?? '',
            'recipient_name'  => $name,
            'recipient_role'  => strtoupper($user->role ?? 'STAFF'),
            'subject'         => $result['subject'] ?? $fallbackTemplates[$idx]['subject'],
            'body'            => $result['body'] ?? $fallbackTemplates[$idx]['body'],
        ];
    }

    /**
     * 3. Jana Draf E-mel Gabungan kepada Leader Jabatan untuk Senarai Staf Overload
     */
    public function generateDepartmentOverloadDraft(string $departmentName, int $variation = 1): array
    {
        $deptUpper = strtoupper(trim($departmentName));

        $deptIds = DB::table('departments')
            ->whereRaw('UPPER(TRIM(department_name)) = ?', [$deptUpper])
            ->pluck('department_id')
            ->all();

        if (empty($deptIds)) {
            $deptIds = DB::table('departments')
                ->where('department_name', 'LIKE', '%' . trim($departmentName) . '%')
                ->pluck('department_id')
                ->all();
        }

        // Cari Leader jabatan tersebut
        $leader = DB::table('users')
            ->whereIn('department_id', $deptIds)
            ->whereRaw('UPPER(TRIM(role)) = ?', ['LEADER'])
            ->select('worker_id', 'full_name', 'email', 'role')
            ->first();

        $leaderName  = $leader->full_name ?? ($deptUpper . ' Department Leader');
        $leaderEmail = $leader->email ?? '';

        // Ambil semua staf overload (< 75%) dalam jabatan tersebut
        $overloadedStaff = DB::table('users')
            ->whereIn('department_id', $deptIds)
            ->whereIn(DB::raw('UPPER(TRIM(role))'), ['STAFF', 'WORKER'])
            ->where('kpi_score', '<', 75)
            ->orderBy('kpi_score', 'asc')
            ->select('worker_id', 'full_name', 'email', 'kpi_score')
            ->get();

        $settings     = DB::table('kpi_settings')->first();
        $cutoffDay    = (int) ($settings->cutoff_day ?? 28);
        $tasksWeight  = (float) ($settings->tasks_weightage ?? 40);
        $attWeight    = (float) ($settings->attendance_weightage ?? 20);
        $leaderWeight = (float) ($settings->leader_weightage ?? 40);

        $hasTasksTable = \Illuminate\Support\Facades\Schema::hasTable('tasks_etracker');
        $taskCols      = $hasTasksTable ? \Illuminate\Support\Facades\Schema::getColumnListing('tasks_etracker') : [];
        $taskNameCol   = in_array('project_name', $taskCols) ? 'project_name' : (in_array('task_name', $taskCols) ? 'task_name' : null);

        $staffDetails = $overloadedStaff->map(function ($s) use ($tasksWeight, $attWeight, $leaderWeight, $hasTasksTable, $taskNameCol) {
            $latestEval = DB::table('monthly_evaluations')
                ->where('worker_id', $s->worker_id)
                ->orderByDesc('evaluation_year')
                ->orderByDesc('evaluation_month')
                ->first();

            $unfinishedQuery = $hasTasksTable
                ? DB::table('tasks_etracker')
                    ->where('worker_id', $s->worker_id)
                    ->whereRaw('UPPER(TRIM(status)) != ?', ['COMPLETED'])
                : null;

            $unfinishedCount = $unfinishedQuery ? (clone $unfinishedQuery)->count() : 0;
            $sampleTasks = ($unfinishedQuery && $taskNameCol)
                ? (clone $unfinishedQuery)->limit(3)->pluck($taskNameCol)->filter()->values()->all()
                : [];

            $tScore = $latestEval ? (float) $latestEval->tasks_score : null;
            $aScore = $latestEval ? (float) $latestEval->attendance_score : null;
            $lScore = $latestEval ? (float) $latestEval->leader_score : null;

            $issues  = [];
            $actions = [];

            if ($unfinishedCount >= 3 || ($tScore !== null && ($tScore / max(1, $tasksWeight)) < 0.70)) {
                $scoreInfo = $tScore !== null ? "task completion at {$tScore}/{$tasksWeight}" : 'low task completion rate';
                $issues[]  = "{$unfinishedCount} unfinished tasks in tracker ({$scoreInfo})";
                $actions[] = "Reprioritize active deliverables and redistribute heavy workload to clear the {$unfinishedCount}-task backlog";
            }
            if ($aScore !== null && ($aScore / max(1, $attWeight)) < 0.85) {
                $issues[]  = "attendance/leave deductions ({$aScore}/{$attWeight})";
                $actions[] = "Review attendance records and reinforce daily check-in consistency";
            }
            if ($lScore !== null && $lScore > 0 && ($lScore / max(1, $leaderWeight)) < 0.70) {
                $issues[]  = "below-target leader evaluation ({$lScore}/{$leaderWeight})";
                $actions[] = "Schedule a 1-on-1 coaching session to address deliverable quality and execution";
            }
            if (empty($issues)) {
                $issues[]  = "overall KPI score ({$s->kpi_score}%) is below the 75% healthy benchmark";
                $actions[] = "Conduct weekly progress check-ins until KPI recovers above 75%";
            }

            return [
                'worker_id'        => $s->worker_id,
                'name'             => $s->full_name,
                'kpi_score'        => round((float) $s->kpi_score, 1),
                'task_completion'  => $tScore !== null ? "{$tScore}/{$tasksWeight}" : 'N/A',
                'attendance'       => $aScore !== null ? "{$aScore}/{$attWeight}" : 'N/A',
                'leader_rating'    => $lScore !== null ? "{$lScore}/{$leaderWeight}" : 'N/A',
                'unfinished_tasks' => $unfinishedCount,
                'pending_titles'   => $sampleTasks,
                'identified_issue' => implode('; ', $issues),
                'suggested_action' => implode('; ', $actions),
            ];
        })->values()->all();

        $tones = [
            1 => 'supportive, structured, and collaborative',
            2 => 'concise, clear, and action-oriented',
            3 => 'formal executive HR review',
        ];
        $selectedTone = $tones[(($variation - 1) % 3) + 1];

        $prompt = <<<PROMPT
You are an HR Business Partner for the E-KPI System (Infinite Loop Media Sdn Bhd).
Write ONE consolidated email in a {$selectedTone} tone from HR to the Department Leader of {$deptUpper}, {$leaderName}.

Purpose:
Instead of HR messaging each staff member individually, HR is providing {$leaderName} with a complete list of overloaded / underperforming staff (< 75% KPI threshold) in {$deptUpper}, explaining what is problematic for each worker and recommending specific improvement actions for the leader to execute before the {$cutoffDay}th cutoff.

Overloaded Staff Data in {$deptUpper}:
{$this->toJson($staffDetails)}

STRICT RULES:
1. Write strictly in clear, professional English.
2. NEVER use database column names with underscores (such as "tasks_score", "leader_score", "attendance_score", or "kpi_score"). Use natural HR terms.
3. Structure the email body cleanly with a numbered list covering EVERY staff member in the dataset. For each person, clearly state:
   - Full Name & Current KPI (%)
   - Key Issue / Bottleneck (mentioning their specific weak metric or pending tasks)
   - Recommended Action for Leader
4. Start with "Dear {$leaderName}," and end with a professional sign-off from "HR Department, Infinite Loop Media Sdn Bhd".

Return ONLY valid JSON:
{
  "subject": "Clear professional email subject",
  "body": "Complete formatted email body"
}
PROMPT;

        $result = $this->callGemini($prompt, $variation > 1 ? 0.55 : 0.3);

        // Fallback terstruktur sekiranya Gemini API sesak (429/503)
        $staffLines = [];
        foreach ($staffDetails as $idx => $st) {
            $num = $idx + 1;
            $staffLines[] = "{$num}. {$st['name']} (Current KPI: {$st['kpi_score']}%)\n"
                . "   - Issue: {$st['identified_issue']}.\n"
                . "   - Recommended Action: {$st['suggested_action']}.";
        }
        $compiledList = !empty($staffLines)
            ? implode("\n\n", $staffLines)
            : "No staff members currently fall below the 75% KPI threshold.";

        $fallbackSubject = match (($variation - 1) % 3) {
            0 => "HR Action Plan: {$deptUpper} Staff Workload & KPI Review (" . count($staffDetails) . " Staff)",
            1 => "Follow-Up Required: {$deptUpper} Underperforming & Overloaded Staff Summary",
            default => "Department KPI Coaching & Workload Rebalancing — {$deptUpper}",
        };

        $fallbackBody = "Dear {$leaderName},\n\n"
            . "Our E-KPI monitoring indicates that " . count($staffDetails) . " team member(s) in {$deptUpper} currently record an overall KPI below the 75% healthy benchmark. Below is the summary of each affected staff member, their key performance bottleneck, and HR's recommended follow-up actions:\n\n"
            . "{$compiledList}\n\n"
            . "Kindly review these items with the respective team members and rebalance active tasks where necessary ahead of the {$cutoffDay}th evaluation cutoff. Please let HR know if additional support is required.\n\n"
            . "Best regards,\nHR Department\nInfinite Loop Media Sdn Bhd";

        return [
            'recipient_email' => $leaderEmail,
            'recipient_name'  => $leaderName,
            'recipient_role'  => 'LEADER (' . $deptUpper . ')',
            'subject'         => $result['subject'] ?? $fallbackSubject,
            'body'            => $result['body'] ?? $fallbackBody,
        ];
    }


    /**
     * Fungsi teras menghantar permintaan HTTP ke Google Gemini API
     * dengan tukar ganti model automatik jika pelayan utama sesak (503/429) atau bermasalah
     */
    protected function callGemini(string $prompt, float $temperature = 0.3): array
    {
        @set_time_limit(120);

        $cleanKey = trim($this->apiKey);
        if (empty($cleanKey)) {
            Log::warning('GEMINI_API_KEY belum ditetapkan dalam .env');
            return [];
        }

        $models = [
            'gemini-3.8-flash',
            'gemini-3.5-flash-lite',
            'gemini-3.7-flash',
            'gemini-3.6-flash',
            'gemini-3.8-pro',
        ];

        foreach ($models as $index => $modelName) {
            if ($index > 0) {
                usleep(400000);
            }

            $url = 'https://generativelanguage.googleapis.com/v1beta/models/' . $modelName . ':generateContent';

            try {
                $http = Http::connectTimeout(6)
                    ->timeout(18)
                    ->withHeaders([
                        'Expect'         => '',
                        'x-goog-api-key' => $cleanKey,
                    ])
                    ->withOptions([
                        'force_ip_resolve' => 'v4',
                        'version'          => 1.1,
                    ])
                    ->acceptJson();

                if (app()->environment('local')) {
                    $http = $http->withoutVerifying();
                }

                $response = $http->post($url, [
                    'contents' => [
                        [
                            'parts' => [
                                ['text' => $prompt],
                            ],
                        ],
                    ],
                    'generationConfig' => [
                        'temperature'      => $temperature,
                        'responseMimeType' => 'application/json',
                    ],
                ]);

                if (in_array($response->status(), [429, 503])) {
                    Log::warning("Model {$modelName} sesak (HTTP {$response->status()}), mencuba model sandaran...");
                    continue;
                }

                if (!$response->successful()) {
                    Log::error('Gemini API Error', [
                        'model'  => $modelName,
                        'status' => $response->status(),
                        'body'   => $response->body(),
                    ]);
                    continue;
                }

                $rawText = (string) data_get($response->json(), 'candidates.0.content.parts.0.text', '{}');
                $cleanText = preg_replace('/^```(?:json)?\s*|\s*```$/m', '', trim($rawText));
                $decoded = json_decode($cleanText, true);

                if (is_array($decoded) && !empty($decoded)) {
                    return $decoded;
                }

                Log::warning("Gemini JSON tidak sah ({$modelName}): " . substr($rawText, 0, 200));
            } catch (\Throwable $e) {
                Log::warning("Gemini Timeout/Exception ({$modelName}): " . $e->getMessage());
            }
        }

        return [];
    }

    protected function toJson(mixed $data): string
    {
        return json_encode($data, JSON_UNESCAPED_UNICODE);
    }
}