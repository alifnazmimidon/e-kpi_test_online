<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-KPI System - Worker</title>
    <link rel="stylesheet" href="{{ asset('style.css') }}">
</head>

<body>

    <!-- ================================
         HEADER
    ================================= -->
    <header class="header" style="color: white;">
        <div class="header-left">
            <span class="system-title">E-KPI SYSTEM</span>
            <div class="header-line"></div>

            <!-- LOGO PLACEHOLDER -->
            <div class="ilm-logo">
                <span>
                    <img src="{{ asset('assets/ILMgroups-logo-4.png') }}" alt="LOGO ILM">
                </span>
            </div>

            <span class="company-name">INFINITE LOOP MEDIA</span>
        </div>

        <div class="header-right">
            <div class="notification-wrapper">
                <!-- BELL BUTTON -->
                <button class="header-icon" id="notificationBtn" aria-label="Notifications">
                    <div class="bellIcon">
                        <img src="{{ asset('assets/bellwhite.png') }}" alt="Normal state" class="img-base">
                        <img src="{{ asset('assets/bellblack.png') }}" alt="Hover state" class="img-hover">
                    </div>
                    <span class="notification-badge" id="notifBadge" style="display: none;">0</span>
                </button>

                <!-- NOTIFICATION POPUP DROPDOWN -->
                <div class="notification-dropdown" id="notificationDropdown">
                    <div class="notification-header">
                        <h3>Notifications</h3>
                        <span class="mark-read" id="markAllRead">Mark all as read</span>
                    </div>

                    <div class="notification-list" id="notificationList">
                        <div class="no-notifications" id="emptyState">
                            <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
                                <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
                            </svg>
                            <p>No new notifications</p>
                        </div>
                    </div>
                </div>
            </div>

            <button class="header-icon">
                <center>
                    <div class="gear-icon">
                        <img src="{{ asset('assets/gearwhite.png') }}" class="img-base">
                        <img src="{{ asset('assets/gearblack.png') }}" class="img-hover">
                    </div>
                </center>
            </button>
        </div>
    </header>

    <!-- ================================
         SYNC STATUS
    ================================= -->
    <div class="sync-status">
        <span class="sync-dot"></span>
        <span>Synced just now</span>
    </div>

    <!-- ================================
         PAGE TITLE
    ================================= -->
    <h1 class="page-title">PROFILE</h1>

    <!-- ================================
         MAIN CONTENT
    ================================= -->
    <main class="content">

        <!-- =================================
             LEFT PROFILE CARD
        ================================== -->
        <section class="profile-card">

            <!-- PROFILE IMAGE PLACEHOLDER -->
            <div class="profile-image">
                @if(isset($user->profile_photo))
                    <img src="{{ asset('storage/' . $user->profile_photo) }}" alt="Profile Photo" style="width: 100%; height: 100%; border-radius: 50%; object-fit: cover;">
                @else
                    <span>PROFILE</span>
                    <small>PHOTO</small>
                @endif
            </div>

            <!-- WORKER NAME -->
            <h2 class="worker-name">
                {{ $user->name ?? 'Nama Pekerja' }}
            </h2>

            <!-- JOB POSITION -->
            <div class="worker-info">    
                <div class="worker-card">
                    <div class="worker-card-header">
                        <strong>Jawatan</strong>
                    </div>
                    <div class="worker-card-body">
                        <span>{{ $user->position ?? 'Programmer' }}</span>
                    </div>
                </div>
            </div>

            <!-- WORKER ID -->
            <div class="worker-info">
                <div class="worker-card">
                    <div class="worker-card-header">
                        <strong>Worker ID</strong>
                    </div>
                    <div class="worker-card-body">
                        <span>{{ $user->worker_id ?? 'ILM1234' }}</span>
                    </div>
                </div>
            </div>

            <!-- LOG OUT -->
            <form action="{{ route('logout') }}" method="POST" style="width: 100%;">
                @csrf
                <button type="submit" class="logout-button" style="width: 100%; border: none; cursor: pointer;">
                    Log Out
                </button>
            </form>

        </section>

        <!-- =================================
             RIGHT CONTENT
        ================================== -->
        <section class="right-content">

            <!-- =================================
                 KPI SCORES
            ================================== -->
            <div class="kpi-section">
                <div class="section-heading">KPI Scores</div>

                <div class="kpi-content">

                    <!-- CURRENT KPI -->
                    <div class="kpi-card">
                        <div class="kpi-title">Current KPI Score</div>
                        <div class="kpi-score">{{ $kpiScores->user_score ?? 0 }}%</div>
                        <div class="progress-track">
                            <div class="progress-bar" style="width: {{ $kpiScores->user_score ?? 0 }}%;"></div>
                        </div>
                    </div>

                    <!-- DEPARTMENT KPI -->
                    <div class="kpi-card">
                        <div class="kpi-title">KPI Department Score</div>
                        <div class="kpi-score">{{ $kpiScores->dept_score ?? 0 }}%</div>
                        <div class="progress-track">
                            <div class="progress-bar" style="width: {{ $kpiScores->dept_score ?? 0 }}%;"></div>
                        </div>
                    </div>

                    <!-- COMPANY KPI -->
                    <div class="kpi-card">
                        <div class="kpi-title">KPI Company Score</div>
                        <div class="kpi-score">{{ $kpiScores->company_score ?? 0 }}%</div>
                        <div class="progress-track">
                            <div class="progress-bar" style="width: {{ $kpiScores->company_score ?? 0 }}%;"></div>
                        </div>
                    </div>

                </div>
            </div>

            <!-- =================================
                 JOB REPORT
            ================================== -->
            <div class="job-report">
                <div class="section-heading">Job Report</div>

                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th class="bil-column">Bil.</th>
                                <th>Project</th>
                                <th>Completion Date</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($jobs as $index => $job)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $job->project_name ?? $job->name }}</td>
                                <td>{{ $job->completion_date }}</td>
                                <td>
                                    <span class="status-badge {{ strtolower(str_replace(' ', '-', $job->status)) }}">
                                        {{ $job->status }}
                                    </span>
                                </td>
                            </tr>
                            @empty
                            <!-- DEFAULT DUMMY APABILA DB KOSONG -->
                            <tr>
                                <td>1</td>
                                <td>Website Redesign</td>
                                <td>2026-11-15</td>
                                <td>In Progress</td>
                            </tr>
                            <tr>
                                <td>2</td>
                                <td>API Integration</td>
                                <td>2026-12-01</td>
                                <td>Pending</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </section>

    </main>

    <!-- JAVASCRIPT NOTIFIKASI -->
    <script>
        const notifBtn = document.getElementById('notificationBtn');
        const notifDropdown = document.getElementById('notificationDropdown');

        if (notifBtn && notifDropdown) {
            notifBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                notifDropdown.classList.toggle('active');
            });

            document.addEventListener('click', (e) => {
                if (!notifDropdown.contains(e.target) && !notifBtn.contains(e.target)) {
                    notifDropdown.classList.remove('active');
                }
            });
        }
    </script>

</body>
</html>