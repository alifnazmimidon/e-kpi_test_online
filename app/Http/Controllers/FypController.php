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

class FypController extends Controller
{
    // LOGIN
    public function login(Request $request)
    {
        $request->validate([
            'worker_id' => 'required',
            'password'  => 'required',
        ]);

        // Cari user berdasarkan worker_id
        $user = User::where('worker_id', $request->worker_id)->first();

        // Semak password
        if ($user && Hash::check($request->password, $user->password_hash)) {
            Auth::login($user);

            // Redirect mengikut role user
            if ($user->role === 'HR') {
                return redirect()->route('hr.dashboard');
            } elseif ($user->role === 'LEADER') {
                return redirect()->route('leader.dashboard');
            } else {
                return redirect()->route('worker.dashboard');
            }
        }

            return back()->with('error', 'Worker ID atau Password salah!')->withInput();
        }

        //  LOGOUT
        public function logout()
        {
            Auth::logout();
            return redirect()->route('login');
        }

        // PAPARAN DASHBOARD STAFF / WORKER
    public function workerProfile()
        {
        $user = Auth::user() ?? User::first(); 

        $kpiScores = (object) [
            'user_score'    => $user->kpi_score ?? 75,
            'dept_score'    => $user->department->target_score ?? 80,
            'company_score' => 85,
        ];

        // Ambil tugasan daripada jadual tasks_etracker mengikut worker_id
        $workerId = $user->worker_id ?? 'ILM3003';
        $jobs = Task::where('worker_id', $workerId)->get();

        return view('worker', compact('user', 'kpiScores', 'jobs'));
        }

        // PAPARAN DASHBOARD HR
        public function hrDashboard(Request $request)
        {
        $avgKpiScore = round(User::avg('kpi_score') ?? 0, 1);
        $unhealthyStaffCount = User::where('role', 'STAFF')
                                ->where('kpi_score', '<', 75)
                                ->count();

        // Purata sasaran jabatan Quarter 3
        $targetAchievement = round(Department::where('quarter', 'Q3')->avg('target_score') ?? 0, 1);
        
        // Trend berbanding Quarter 2
        $q2Avg = Department::where('quarter', 'Q2')->avg('target_score') ?? 0;
        $kpiTrendDiff = round($targetAchievement - $q2Avg, 1);

        // JADUAL
        $departmentsPerformance = Department::where('quarter', 'Q3')->get()->map(function($dept) {
        $allDeptIdsWithSameName = Department::where('department_name', $dept->department_name)
                                            ->pluck('department_id');

        // Tapis keluar peranan HR daripada pengiraan staf & KPI jabatan
        $staffQuery = User::whereIn('department_id', $allDeptIdsWithSameName)
                        ->where('role', '!=', 'HR');

        $staffCount = (clone $staffQuery)->count();
        $avgKpi     = (clone $staffQuery)->avg('kpi_score') ?? $dept->target_score;

        // Agregat tugasan daripada tasks_etracker (Staf & Leader sahaja)
        $workerIds      = (clone $staffQuery)->pluck('worker_id');
        $totalTasks     = DB::table('tasks_etracker')->whereIn('worker_id', $workerIds)->count();
        $completedTasks = DB::table('tasks_etracker')->whereIn('worker_id', $workerIds)->where('status', 'Completed')->count();

        return (object) [
            'department_name'   => $dept->department_name,
            'avg_kpi'           => round($avgKpi, 1),
            'total_staff'       => $staffCount,
            'submitted'         => $completedTasks,
            'total_submissions' => $totalTasks > 0 ? $totalTasks : $staffCount,
            'status'            => ($completedTasks >= $totalTasks && $totalTasks > 0) ? 'COMPLETE' : 'PENDING',
            'gold_badges'       => (clone $staffQuery)->where('kpi_score', '>=', 90)->count(),
            'silver_badges'     => (clone $staffQuery)->where('kpi_score', '>=', 80)->where('kpi_score', '<', 90)->count(),
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
                            ->map(function($user) {
                                return (object) [
                                    'name'      => $user->full_name,
                                    'kpi_score' => $user->kpi_score ?? 0,
                                ];
                            });

        // GRAF TREND KPI Q1 - Q4 (100% DARI JADUAL DEPARTMENTS)
        $quarters = ['Q1', 'Q2', 'Q3', 'Q4'];
        $kpiTrend = [];
        foreach ($quarters as $q) {
            $kpiTrend[] = round(Department::where('quarter', $q)->avg('target_score') ?? 0, 1);
        }

        // AI ANALYTICS SLIDER (DINAMIK DARI DATA MYSQL SEBENAR)
        $topWorker = User::orderBy('kpi_score', 'desc')->first();
        $topDept   = Department::where('quarter', 'Q3')->orderBy('target_score', 'desc')->first();

        $aiInsights = [
            [
                'tag'   => 'AI Recommendation',
                'title' => ($topWorker->full_name ?? 'Staf') . ' (' . ($selectedDeptName) . ')',
                'desc'  => 'Disyorkan untuk Anugerah Kecemerlangan berdasarkan markah KPI tertinggi dalam database.',
                'stat'  => ($topWorker->kpi_score ?? 0) . '% Penilaian Kecekapan'
            ],
            [
                'tag'   => 'Predictive Alert',
                'title' => ($topDept->department_name ?? 'R&D') . ' Department Trend',
                'desc'  => 'Pencapaian sasaran jabatan kini mendahului pada ' . ($topDept->target_score ?? 0) . '% untuk Quarter 3.',
                'stat'  => '+' . ($topDept->target_score ?? 0) . '% Purata Target'
            ],
            [
                'tag'   => 'Workload Optimisation',
                'title' => 'Amaran Beban Kerja Staf',
                'desc'  => $unhealthyStaffCount . ' orang staf dikesan memerlukan perhatian dan agihan semula tugasan.',
                'stat'  => $unhealthyStaffCount . ' Staf Berisiko'
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

        return back()->with('success', 'Weightage Formula berjaya dikemaskini!');
    }
    public function updateThresholds(Request $request)
    {
    KpiSetting::where('setting_id', 1)->update([
        'gold_threshold'   => $request->input('gold_threshold'),
        'silver_threshold' => $request->input('silver_threshold'),
        'bronze_threshold' => $request->input('bronze_threshold'),
    ]);

    return back()->with('success', 'Badge thresholds berjaya dikemaskini!');
    }

    //FUNGSI UNTUK PROSES SIMPAN / KEMASKINI TARGET SCORE
   public function updateTarget(Request $request)
    {
    $deptName        = $request->input('department_name');
    $selectedQuarter = $request->input('quarter', 'Q3');
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

    return back()->with('success', 'Target score berjaya dikemaskini!');
    }

    // PAPARAN HR - REPORT
    public function hrReport()
    {
        return view('hrreport');
    }

    //PAPARAN HR - EMERGENCY LEAVE
    public function emerLeave()
    {
        return view('emerleave');
    }

    //PAPARAN DASHBOARD LEADER
    public function leaderDashboard()
    {
        return view('leader');
    }

}