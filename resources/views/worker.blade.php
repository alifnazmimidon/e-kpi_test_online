<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>E-KPI System - Worker Profile</title>
    <link rel="stylesheet" href="{{ asset('styleworker.css') }}">

    <script>
        function showHeaderAvatarFallback() {
            const img = document.getElementById('headerUserProfilePic');
            const svg = document.getElementById('defaultAvatarSvg');
            if (img) img.style.display = 'none';
            if (svg) svg.style.display = '';
        }
        function showCardAvatarFallback() {
            const img = document.getElementById('cardUserProfilePic');
            const svg = document.getElementById('cardDefaultAvatarSvg');
            if (img) img.style.display = 'none';
            if (svg) svg.style.display = 'block';
        }
    </script>
</head>

<body>

    <a class="skip-link" href="#main">Skip to main content</a>

    <!-- SVG ICON SPRITE -->
    <svg width="0" height="0" style="position:absolute" aria-hidden="true" focusable="false">
        <symbol id="i-star" viewBox="0 0 24 24"><path d="m12 2 3.1 6.3 6.9 1-5 4.9 1.2 6.8L12 17.8 5.8 21l1.2-6.8-5-4.9 6.9-1z"/></symbol>
        <symbol id="i-history" viewBox="0 0 24 24"><path d="M3 12a9 9 0 1 0 3-6.7L3 8"/><path d="M3 3v5h5M12 7v5l4 2"/></symbol>
        <symbol id="i-copy" viewBox="0 0 24 24"><rect x="9" y="9" width="13" height="13" rx="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></symbol>
        <symbol id="i-check" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="m9 12 2 2 4-4"/></symbol>
        <symbol id="i-tick" viewBox="0 0 24 24"><path d="M20 6 9 17l-5-5"/></symbol>
        <symbol id="i-refresh" viewBox="0 0 24 24"><path d="M21 12a9 9 0 0 0-15-6.7L3 8"/><path d="M3 3v5h5M3 12a9 9 0 0 0 15 6.7L21 16"/><path d="M16 16h5v5"/></symbol>
        <symbol id="i-search" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></symbol>
        <symbol id="i-x" viewBox="0 0 24 24"><path d="M18 6 6 18M6 6l12 12"/></symbol>
        <symbol id="i-sort" viewBox="0 0 24 24"><path d="m7 15 5 5 5-5M7 9l5-5 5 5"/></symbol>
        <symbol id="i-arrow-up" viewBox="0 0 24 24"><path d="M12 19V5M5 12l7-7 7 7"/></symbol>
        <symbol id="i-arrow-down" viewBox="0 0 24 24"><path d="M12 5v14M19 12l-7 7-7-7"/></symbol>
        <symbol id="i-minus" viewBox="0 0 24 24"><path d="M5 12h14"/></symbol>
        <symbol id="i-clock" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></symbol>
        <symbol id="i-hourglass" viewBox="0 0 24 24"><path d="M5 22h14M5 2h14M17 22v-4.2a2 2 0 0 0-.6-1.4L12 12l-4.4 4.4a2 2 0 0 0-.6 1.4V22M7 2v4.2a2 2 0 0 0 .6 1.4L12 12l4.4-4.4a2 2 0 0 0 .6-1.4V2"/></symbol>
        <symbol id="i-medal" viewBox="0 0 24 24"><path d="M7.2 15 2.7 7.1a2 2 0 0 1 .1-2.2L4.4 2.8A2 2 0 0 1 6 2h12a2 2 0 0 1 1.6.8l1.6 2.1a2 2 0 0 1 .1 2.2L16.8 15"/><path d="M11 12 5.1 2.2M13 12l5.9-9.8M8 7h8"/><circle cx="12" cy="17" r="5"/></symbol>
        <symbol id="i-edit" viewBox="0 0 24 24"><path d="M12 20h9"/><path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4z"/></symbol>
        <symbol id="i-briefcase" viewBox="0 0 24 24"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></symbol>
        <symbol id="i-id" viewBox="0 0 24 24"><rect x="2" y="5" width="20" height="14" rx="2"/><circle cx="8" cy="11" r="2"/><path d="M14 10h4M14 14h4M5 16c.5-1.5 1.5-2 3-2s2.5.5 3 2"/></symbol>
        <symbol id="i-chart" viewBox="0 0 24 24"><path d="M3 3v18h18"/><path d="M8 17v-4M13 17V8M18 17v-7"/></symbol>
        <symbol id="i-list" viewBox="0 0 24 24"><rect x="8" y="2" width="8" height="4" rx="1"/><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2M9 12h6M9 16h4"/></symbol>
        <symbol id="i-inbox" viewBox="0 0 24 24"><path d="M22 12h-6l-2 3h-4l-2-3H2"/><path d="M5.5 5.1 2 12v6a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-6l-3.5-6.9A2 2 0 0 0 16.7 4H7.3a2 2 0 0 0-1.8 1.1z"/></symbol>
        <symbol id="i-alert-circle" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M12 8v4M12 16h.01"/></symbol>
    </svg>

    @php
        $currentUser = $user ?? Auth::user();
        $displayName = $currentUser->full_name ?? $currentUser->name ?? 'Nama Pekerja';
        $workerCode  = $currentUser->worker_id ?? 'ILM1234';
        $deptName    = $currentUser->department->department_name ?? ($currentUser->department_name ?? 'General');

        // Selesaikan senarai jawatan (sokong sama ada string tunggal atau array/JSON)
        $positionsList = [];
        if (!empty($currentUser->positions)) {
            if (is_string($currentUser->positions)) {
                $decoded = json_decode($currentUser->positions, true);
                $positionsList = is_array($decoded) ? $decoded : [$currentUser->positions];
            } elseif (is_array($currentUser->positions)) {
                $positionsList = $currentUser->positions;
            }
        }

        // Buang nilai kosong atau string '[]'
        $positionsList = array_values(array_filter($positionsList, fn($p) => is_string($p) && trim($p) !== '' && trim($p) !== '[]'));

        // Jika tiada sejarah dalam kolum positions, gunakan kolum asal designation / position
        if (empty($positionsList)) {
            $fallbackRole = $currentUser->designation ?? $currentUser->position ?? null;
            if (!empty($fallbackRole)) {
                $positionsList = [$fallbackRole];
            }
        }
        $latestPosition = count($positionsList) ? end($positionsList) : 'Programmer';

        // Selesaikan URL gambar profil (mendukung folder public/uploads/ maupun storage/avatars/)
        $rawPhoto = $currentUser->profile_photo ?? $currentUser->profile_pic_url ?? '';
        $photoUrl = '';
        if (!empty($rawPhoto)) {
            $cleanPath = ltrim($rawPhoto, '/');
            if (str_starts_with($rawPhoto, 'http://') || str_starts_with($rawPhoto, 'https://') || str_starts_with($rawPhoto, '/')) {
                $photoUrl = $rawPhoto;
            } elseif (str_starts_with($cleanPath, 'uploads/') || str_starts_with($cleanPath, 'assets/') || str_starts_with($cleanPath, 'storage/')) {
                $photoUrl = asset($cleanPath);
            } else {
                $photoUrl = asset('storage/' . $cleanPath);
            }
        }

        // Selesaikan pautan Edit Profile & Dashboard mengikut peranan (HR, LEADER, atau STAFF)
        $editProfileUrl = Route::has('profile.edit')
            ? route('profile.edit')
            : url('/profile');

        $roleUpper = strtoupper($currentUser->role ?? '');
        $dashboardUrl = match ($roleUpper) {
            'HR'     => Route::has('hr.dashboard') ? route('hr.dashboard') : url('/hr/dashboard'),
            'LEADER' => Route::has('leader.dashboard') ? route('leader.dashboard') : url('/leader/dashboard'),
            default  => Route::has('worker.dashboard') ? route('worker.dashboard') : url('/worker/profile'),
        };
    @endphp

    <!-- ================================ HEADER ================================ -->
    <header class="header">
        <div class="header-left">
            <div class="ilm-logo"><img src="{{ asset('assets/ILMgroups-logo-4.png') }}" alt="ILM Groups logo"></div>
            <div class="header-line" aria-hidden="true"></div>
            <span class="system-title">E-KPI SYSTEM</span>
        </div>

        <div class="header-right">

            <div class="notification-wrapper">
                <button type="button" class="header-icon" id="notificationBtn" aria-label="Notifications"
                        aria-haspopup="true" aria-expanded="false" aria-controls="notificationDropdown">
                    <div class="bellIcon">
                        <img src="{{ asset('assets/bellwhite.png') }}" alt="" class="img-base">
                        <img src="{{ asset('assets/bellblack.png') }}" alt="" class="img-hover">
                    </div>
                    <span class="notification-badge" id="notifBadge" style="display: none;">0</span>
                </button>

                <div class="notification-dropdown" id="notificationDropdown">
                    <div class="notification-header">
                        <h3>Notifications</h3>
                        <button type="button" class="mark-read" id="markAllRead">Mark all as read</button>
                    </div>
                    <div class="notification-list" id="notificationList" aria-live="polite">
                        <div class="no-notifications" id="emptyState">
                            <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
                                <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
                            </svg>
                            <p>No new notifications</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="profile-wrapper">
                <button type="button" class="header-icon profile-btn" id="profileBtn" aria-label="User profile menu"
                        aria-haspopup="true" aria-expanded="false" aria-controls="profileDropdown">
                    <div class="profile-avatar-wrapper">
                        <svg id="defaultAvatarSvg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#ffffff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" style="{{ $photoUrl ? 'display: none;' : '' }}">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                            <circle cx="12" cy="7" r="4"></circle>
                        </svg>
                        <img @if($photoUrl) src="{{ $photoUrl }}" @endif id="headerUserProfilePic" alt="" class="profile-avatar-img" style="{{ $photoUrl ? 'display: block;' : 'display: none;' }}" onerror="showHeaderAvatarFallback()">
                    </div>
                </button>

                <div class="profile-dropdown" id="profileDropdown">
                    <div class="profile-dropdown-header">
                        <span class="user-name" id="dropdownUserName">{{ $navUser->full_name ?? $navUser->name ?? 'User' }}</span>
                        <span class="user-role" id="dropdownUserRole">{{ $latestPosition ?? ($navUser->role ?? 'Worker') }}</span>
                    </div>

                    @php
                        $userRole = strtoupper(trim(auth()->user()->role ?? ''));
                        $isHr = $userRole === 'HR';
                        $isLeader = in_array($userRole, ['TEAM LEADER', 'TEAM_LEADER', 'LEADER'], true);
                    @endphp

                    <ul class="profile-dropdown-menu">
                        <li><a href="{{ route('profile.edit') }}">Edit Profile</a></li>

                        {{-- Menu Dashboard hanya dipaparkan untuk HR dan Department Leader sahaja --}}
                        @if($isHr || $isLeader)
                            <li>
                                <a href="{{ $isHr ? (Route::has('hr.dashboard') ? route('hr.dashboard') : url('/hr')) : (Route::has('leader.dashboard') ? route('leader.dashboard') : url('/leader')) }}">
                                    Dashboard
                                </a>
                            </li>
                        @endif

                        <li class="divider"></li>
                        <li>
                            <a href="{{ route('logout') }}" class="logout-link"
                            onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Log Out</a>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                @csrf
                            </form>
                        </li>
                    </ul>
                </div>
            </div>

        </div>
    </header>

    <!-- ============================== SYNC STATUS ============================== -->
    <div class="sync-status reveal" style="--i:1" id="syncStatus">
        <span class="sync-dot" aria-hidden="true"></span>
        <span id="syncText" role="status" aria-live="polite">Synced just now</span>
        <button type="button" class="sync-btn" id="syncBtn" aria-label="Refresh data" title="Refresh data">
            <svg class="icon" aria-hidden="true"><use href="#i-refresh"/></svg>
        </button>
    </div>

    <h1 class="page-title reveal" style="--i:2">PROFILE</h1>

    <!-- ============================== MAIN CONTENT ============================== -->
    <main class="content" id="main">

        <!-- ========== LEFT PROFILE CARD ========== -->
        <section class="profile-card reveal" style="--i:3" aria-label="Worker profile">

            <div class="profile-image" id="profileImageWrapper" style="cursor: pointer; position: relative;" title="Klik untuk tukar gambar profil" onclick="document.getElementById('workerPhotoInput').click()">
                <svg id="cardDefaultAvatarSvg" width="55" height="55" viewBox="0 0 24 24" fill="none" stroke="#64748b" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" style="{{ $photoUrl ? 'display: none;' : '' }}">
                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                    <circle cx="12" cy="7" r="4"></circle>
                </svg>
                <img @if($photoUrl) src="{{ $photoUrl }}" @endif id="cardUserProfilePic" alt="Worker profile picture" class="card-avatar-img" style="{{ $photoUrl ? 'display: block;' : 'display: none;' }}" onerror="showCardAvatarFallback()">
                <input type="file" id="workerPhotoInput" accept="image/png,image/jpeg,image/webp" style="display: none;">
            </div>

            <h2 class="worker-name" id="workerName">{{ $displayName }}</h2>

            <div class="worker-info">
                <div class="worker-card">
                    <div class="worker-card-header">
                        <svg class="icon" aria-hidden="true"><use href="#i-briefcase"/></svg>
                        <strong>Jawatan / Positions</strong>
                    </div>
                    <div class="worker-card-body" id="positionsContainer"></div>
                </div>
            </div>

            <div class="worker-info">
                <div class="worker-card">
                    <div class="worker-card-header">
                        <svg class="icon" aria-hidden="true"><use href="#i-id"/></svg>
                        <strong>Worker ID</strong>
                    </div>
                    <div class="worker-card-body">
                        <div class="id-row">
                            <span id="workerId">{{ $workerCode }}</span>
                            <button type="button" class="copy-btn" id="copyIdBtn" aria-label="Copy worker ID" title="Copy worker ID">
                                <svg class="icon" aria-hidden="true"><use href="#i-copy"/></svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <a href="{{ $editProfileUrl }}" class="edit-profile-button">
                <svg class="icon" aria-hidden="true"><use href="#i-edit"/></svg>Edit Profile
            </a>
        </section>

        <!-- ========== RIGHT CONTENT ========== -->
        <section class="right-content">

            <!-- KPI SCORES -->
            <div class="kpi-section reveal" style="--i:4">
                <h2 class="section-heading"><svg class="icon" aria-hidden="true"><use href="#i-chart"/></svg>KPI Scores</h2>
                <div class="kpi-content">

                    <div class="kpi-card">
                        <div class="kpi-title">Current KPI Score</div>
                        <div class="kpi-score" id="kpiCurrentVal">{{ round($kpiScores->user_score ?? 0) }}%</div>
                        <div class="progress-track" role="progressbar" aria-label="Current KPI score" aria-valuemin="0" aria-valuemax="100" aria-valuenow="{{ round($kpiScores->user_score ?? 0) }}" id="kpiCurrentTrack">
                            <div class="progress-bar" id="kpiCurrentBar"></div>
                        </div>
                        <span class="kpi-chip" id="kpiCurrentChip" hidden></span>
                    </div>

                    <div class="kpi-card">
                        <div class="kpi-title">KPI Department Score</div>
                        <div class="kpi-score" id="kpiDeptVal">{{ round($kpiScores->dept_score ?? 0) }}%</div>
                        <div class="progress-track" role="progressbar" aria-label="Department KPI score" aria-valuemin="0" aria-valuemax="100" aria-valuenow="{{ round($kpiScores->dept_score ?? 0) }}" id="kpiDeptTrack">
                            <div class="progress-bar" id="kpiDeptBar"></div>
                        </div>
                        <span class="kpi-chip" id="kpiDeptChip" hidden></span>
                    </div>

                    <div class="kpi-card">
                        <div class="kpi-title">KPI Company Score</div>
                        <div class="kpi-score" id="kpiCompanyVal">{{ round($kpiScores->company_score ?? 0) }}%</div>
                        <div class="progress-track" role="progressbar" aria-label="Company KPI score" aria-valuemin="0" aria-valuemax="100" aria-valuenow="{{ round($kpiScores->company_score ?? 0) }}" id="kpiCompanyTrack">
                            <div class="progress-bar" id="kpiCompanyBar"></div>
                        </div>
                        <span class="kpi-chip" id="kpiCompanyChip" hidden></span>
                    </div>

                </div>
            </div>

            <!-- JOB REPORT -->
            <div class="job-report reveal" style="--i:5">
                <h2 class="section-heading" id="jobTitle"><svg class="icon" aria-hidden="true"><use href="#i-list"/></svg>Job Report</h2>

                <div class="job-toolbar">
                    <div class="search-box">
                        <svg class="icon" aria-hidden="true"><use href="#i-search"/></svg>
                        <input type="search" id="jobSearch" placeholder="Search projects..." aria-label="Search projects" autocomplete="off">
                        <button type="button" class="search-clear" id="searchClear" aria-label="Clear search" hidden>
                            <svg class="icon" aria-hidden="true"><use href="#i-x"/></svg>
                        </button>
                    </div>
                    <div class="filter-group" role="group" aria-label="Filter by status">
                        <button type="button" class="filter-btn" data-filter="all" aria-pressed="true">All <span id="cntAll">0</span></button>
                        <button type="button" class="filter-btn" data-filter="in-progress" aria-pressed="false">In Progress <span id="cntIn">0</span></button>
                        <button type="button" class="filter-btn" data-filter="pending" aria-pressed="false">Pending <span id="cntPending">0</span></button>
                        <button type="button" class="filter-btn" data-filter="completed" aria-pressed="false">Completed <span id="cntDone">0</span></button>
                    </div>
                    <span class="result-count" id="resultCount" aria-live="polite"></span>
                </div>

                <div class="table-container" tabindex="0" role="region" aria-labelledby="jobTitle">
                    <table>
                        <thead>
                            <tr>
                                <th scope="col" class="bil-column">Bil.</th>
                                <th scope="col" aria-sort="none"><button type="button" class="sort-btn" data-sort="project">Project<svg class="icon" aria-hidden="true"><use href="#i-sort"/></svg></button></th>
                                <th scope="col" aria-sort="none"><button type="button" class="sort-btn" data-sort="assign_date">Assign Date<svg class="icon" aria-hidden="true"><use href="#i-sort"/></svg></button></th>
                                <th scope="col" aria-sort="none"><button type="button" class="sort-btn" data-sort="completion_date">Completion Date<svg class="icon" aria-hidden="true"><use href="#i-sort"/></svg></button></th>
                                <th scope="col" aria-sort="none"><button type="button" class="sort-btn" data-sort="status">Status<svg class="icon" aria-hidden="true"><use href="#i-sort"/></svg></button></th>
                            </tr>
                        </thead>
                        <tbody id="jobReportTableBody"></tbody>
                    </table>
                </div>
            </div>

        </section>
    </main>

    <div class="toast-region" id="toastRegion" aria-live="polite"></div>

    <!-- LARAVEL SEED DATA (LIVE MYSQL PAYLOAD) -->
    <?php
        $mappedJobs = collect($jobs ?? [])->map(function ($job) {
            $assignRaw = $job->assign_date ?? $job->assigned_date ?? $job->created_at ?? null;
            $compRaw   = $job->completion_date ?? $job->due_date ?? null;

            return [
                'project'         => $job->project_name ?? $job->name ?? 'Untitled Project',
                'assign_date'     => $assignRaw ? \Carbon\Carbon::parse($assignRaw)->format('Y-m-d') : '',
                'completion_date' => $compRaw ? \Carbon\Carbon::parse($compRaw)->format('Y-m-d') : '',
                'status'          => $job->status ?? 'Pending',
            ];
        })->values()->all();

           $workerSeedPayload = [
            'name'            => $displayName,
            'workerId'        => $workerCode,
            'department'      => $deptName,
            'profile_pic_url' => $photoUrl,
            'positions'       => array_values($positionsList),
            'kpi'             => [
                'current'    => (float) ($kpiScores->user_score ?? 0),
                'department' => (float) ($kpiScores->dept_score ?? 0),
                'company'    => (float) ($kpiScores->company_score ?? 0),
            ],
            'thresholds'      => isset($setting) ? [
                'gold'   => (int) ($setting->gold_threshold ?? 90),
                'silver' => (int) ($setting->silver_threshold ?? 85),
                'bronze' => (int) ($setting->bronze_threshold ?? 80),
            ] : null,
            'job_reports'     => $mappedJobs,
        ];
    ?>
    <script type="application/json" id="laravelWorkerSeed">
        <?php echo json_encode($workerSeedPayload, JSON_HEX_TAG); ?>
    </script>

 

    <script>
        (() => {
    /* =====================================================
       HELPERS
    ===================================================== */
    const $ = id => document.getElementById(id);
    const prefersReduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    const icon = (name, cls = '') => `<svg class="icon ${cls}" aria-hidden="true"><use href="#i-${name}"/></svg>`;
    const esc = s => String(s ?? '').replace(/[&<>"']/g, c => ({
    '&': '\x26amp;',
    '<': '\x26lt;',
    '>': '\x26gt;',
    '"': '\x26quot;',
    "'": '\x26#39;'
    }[c]));
    function todayISO() {
        const d = new Date(), p = n => String(n).padStart(2, '0');
        return `${d.getFullYear()}-${p(d.getMonth() + 1)}-${p(d.getDate())}`;
    }
    const fmtDate = iso => {
        if (!iso || !String(iso).includes('-')) return '-';
        const [y, m, d] = String(iso).split('-');
        return d && m && y ? `${d}/${m}/${y}` : iso;
    };
    const dayNumber = iso => {
        const [y, m, d] = String(iso).split('-').map(Number);
        return Date.UTC(y, m - 1, d) / 86400000;
    };

    function animateCount(el, to, { suffix = '%', duration = 1000 } = {}) {
        const from = parseFloat(el.dataset.val) || 0;
        el.dataset.val = to;
        if (prefersReduced || from === to) { el.textContent = Math.round(to) + suffix; return; }
        const start = performance.now();
        const step = now => {
            const p = Math.min((now - start) / duration, 1);
            el.textContent = Math.round(from + (to - from) * (1 - Math.pow(1 - p, 3))) + suffix;
            if (p < 1) requestAnimationFrame(step);
        };
        requestAnimationFrame(step);
    }

    function toast(text, type = 'success') {
        const el = document.createElement('div');
        el.className = 'toast ' + type;
        el.innerHTML = `${icon(type === 'success' ? 'check' : 'alert-circle')}<span>${esc(text)}</span>
                        <button type="button" aria-label="Dismiss notification">${icon('x')}</button>`;
        const close = () => { el.classList.add('out'); setTimeout(() => el.remove(), prefersReduced ? 0 : 220); };
        el.querySelector('button').addEventListener('click', close);
        $('toastRegion').appendChild(el);
        setTimeout(close, 3500);
    }

    function showCardAvatarFallback() {
        const img = $('cardUserProfilePic'), svg = $('cardDefaultAvatarSvg');
        if (img) img.style.display = 'none';
        if (svg) svg.style.display = 'block';
    }
    if (typeof window.showHeaderAvatarFallback !== 'function') {
        window.showHeaderAvatarFallback = function () {
            const img = $('headerUserProfilePic'), svg = $('defaultAvatarSvg');
            if (img) img.style.display = 'none';
            if (svg) svg.style.display = '';
        };
    }

    /* =====================================================
       READ LARAVEL SEED PAYLOAD
    ===================================================== */
    function readSeedData(doc = document) {
        const raw = doc.getElementById('laravelWorkerSeed')?.textContent?.trim();
        if (!raw) throw new Error('Seed payload not found');
        return JSON.parse(raw);
    }

    let initialSeed = null;
    try {
        initialSeed = readSeedData(document);
    } catch (e) {
        console.warn('Worker seed parse warning:', e);
    }

    /* =====================================================
       SYNC STATUS (live relative time + manual refresh)
    ===================================================== */
    let lastSync = null, syncState = 'idle';

    function relTime(ts) {
        const s = Math.round((Date.now() - ts) / 1000);
        if (s < 45) return 'just now';
        const m = Math.round(s / 60);
        if (m < 60) return `${m} min ago`;
        return `${Math.round(m / 60)} h ago`;
    }
    function renderSync() {
        const box = $('syncStatus'), text = $('syncText');
        box.classList.toggle('loading', syncState === 'loading');
        box.classList.toggle('error', syncState === 'error');
        text.textContent = syncState === 'loading' ? 'Syncing...'
            : syncState === 'error' ? (lastSync ? `Sync failed (last synced ${relTime(lastSync)})` : 'Sync failed')
            : `Synced ${lastSync ? relTime(lastSync) : 'just now'}`;
        $('syncBtn').setAttribute('aria-label', syncState === 'error' ? 'Retry sync' : 'Refresh data');
    }

    /* =====================================================
       KPI: badge tier (reads DB thresholds or localStorage)
    ===================================================== */
    let dbThresholds = initialSeed?.thresholds || null;

    function getThresholds() {
        if (dbThresholds && dbThresholds.gold > dbThresholds.silver && dbThresholds.silver > dbThresholds.bronze) {
            return dbThresholds;
        }
        try {
            const t = JSON.parse(localStorage.getItem('badgeThreshold'));
            if (t && t.gold > t.silver && t.silver > t.bronze && t.bronze >= 0 && t.gold <= 100) return t;
        } catch { /* ignore */ }
        return { gold: 90, silver: 85, bronze: 80 };
    }
    function tierFor(score) {
        const t = getThresholds();
        if (score >= t.gold) return { cls: 'gold', label: 'Gold tier', icon: 'medal' };
        if (score >= t.silver) return { cls: 'silver', label: 'Silver tier', icon: 'medal' };
        if (score >= t.bronze) return { cls: 'bronze', label: 'Bronze tier', icon: 'medal' };
        return { cls: '', label: 'Below badge range', icon: 'minus' };
    }
    function setChip(id, cls, iconName, text) {
        const el = $(id);
        el.hidden = false;
        el.className = 'kpi-chip ' + cls;
        el.innerHTML = icon(iconName) + esc(text);
    }
    function setKpi(prefix, value) {
        const v = Math.min(Math.max(Number(value) || 0, 0), 100);
        animateCount($(`${prefix}Val`), v);
        $(`${prefix}Track`).setAttribute('aria-valuenow', v);
        const bar = $(`${prefix}Bar`);
        bar.style.setProperty('--w', v + '%');
        bar.style.animation = 'none'; void bar.offsetWidth; bar.style.animation = '';
        return v;
    }
    function compareChip(id, mine, other, label) {
        const d = Math.round(mine - other);
        if (d === 0) setChip(id, '', 'minus', `On par with ${label}`);
        else setChip(id, d > 0 ? 'up' : 'down', d > 0 ? 'arrow-up' : 'arrow-down', `You: ${Math.abs(d)} pts ${d > 0 ? 'above' : 'below'}`);
    }

    /* =====================================================
       JOB REPORT: filter / search / sort
    ===================================================== */
    const STATUS = {
        'in-progress': { label: 'In Progress', icon: 'clock', order: 0 },
        'pending':     { label: 'Pending',     icon: 'hourglass', order: 1 },
        'completed':   { label: 'Completed',   icon: 'check', order: 2 }
    };
    const slug = s => String(s || '').toLowerCase().trim().replace(/\s+/g, '-');

    let jobs = [], filterKey = 'all', query = '', sort = { key: null, dir: 'asc' };

    function dueHint(job) {
        if (slug(job.status) === 'completed' || !job.completion_date || !String(job.completion_date).includes('-')) return '';
        const diff = dayNumber(job.completion_date) - dayNumber(todayISO());
        if (Number.isNaN(diff)) return '';
        if (diff < 0) return `<span class="due overdue">Overdue by ${-diff} ${-diff === 1 ? 'day' : 'days'}</span>`;
        if (diff <= 3) return `<span class="due soon">${diff === 0 ? 'Due today' : `Due in ${diff} ${diff === 1 ? 'day' : 'days'}`}</span>`;
        return '';
    }

    function renderJobs({ animate = true } = {}) {
        const tbody = $('jobReportTableBody');
        const count = k => jobs.filter(j => slug(j.status) === k).length;
        $('cntAll').textContent = jobs.length;
        $('cntIn').textContent = count('in-progress');
        $('cntPending').textContent = count('pending');
        $('cntDone').textContent = count('completed');
        document.querySelectorAll('.filter-btn').forEach(b => b.setAttribute('aria-pressed', String(b.dataset.filter === filterKey)));

        const q = query.trim().toLowerCase();
        let rows = jobs.filter(j => (filterKey === 'all' || slug(j.status) === filterKey) && (!q || String(j.project).toLowerCase().includes(q)));

        if (sort.key) {
            const val = j => sort.key === 'status' ? (STATUS[slug(j.status)]?.order ?? 9) : String(j[sort.key] ?? '').toLowerCase();
            rows = [...rows].sort((a, b) => (val(a) > val(b) ? 1 : val(a) < val(b) ? -1 : 0) * (sort.dir === 'asc' ? 1 : -1));
        }
        document.querySelectorAll('th[aria-sort]').forEach(th => {
            const btn = th.querySelector('.sort-btn'), active = btn.dataset.sort === sort.key;
            th.setAttribute('aria-sort', active ? (sort.dir === 'asc' ? 'ascending' : 'descending') : 'none');
            btn.querySelector('use').setAttribute('href', active ? (sort.dir === 'asc' ? '#i-arrow-up' : '#i-arrow-down') : '#i-sort');
        });

        $('resultCount').textContent = jobs.length ? `Showing ${rows.length} of ${jobs.length}` : '';

        if (!rows.length) {
            tbody.innerHTML = `<tr><td colspan="5" class="empty-cell">${icon('inbox')}${jobs.length ? 'No projects match your filters.' : 'No job reports available.'}</td></tr>`;
            return;
        }
        tbody.innerHTML = rows.map((job, i) => {
            const key = slug(job.status), st = STATUS[key] || { label: job.status, icon: 'minus' };
            return `
                <tr class="${animate ? 'enter' : ''}" style="--i:${i}">
                    <td>${i + 1}</td>
                    <td class="project-cell"><strong>${esc(job.project)}</strong></td>
                    <td>${fmtDate(job.assign_date)}</td>
                    <td>${fmtDate(job.completion_date)}${dueHint(job)}</td>
                    <td><span class="status-badge ${esc(key)}">${icon(st.icon)}${esc(st.label)}</span></td>
                </tr>`;
        }).join('');
    }

    function renderSkeleton() {
        $('jobReportTableBody').innerHTML = Array.from({ length: 4 }, () =>
            `<tr>${'<td><span class="skel"></span></td>'.repeat(5)}</tr>`).join('');
        $('positionsContainer').innerHTML = '<span class="skel block"></span><span class="skel block"></span>';
    }

    /* =====================================================
       RENDER WORKER PAGE WITH MYSQL DATA
    ===================================================== */
    function renderWorkerPageUI(data) {
        if (data.thresholds) dbThresholds = data.thresholds;

        // 1. Text
        $('workerName').textContent = data.name || 'Nama Pekerja';
        $('workerId').textContent = data.workerId || 'ILM0000';
        $('dropdownUserName').textContent = data.name || 'User';

        const positions = Array.isArray(data.positions) ? data.positions : [];
        const latestRole = positions.length ? positions[positions.length - 1] : 'Staff';
        $('dropdownUserRole').textContent = latestRole;

        // 2. Profile picture
        const url = (data.profile_pic_url || '').trim();
        if (url) {
            [['headerUserProfilePic', 'defaultAvatarSvg'], ['cardUserProfilePic', 'cardDefaultAvatarSvg']].forEach(([i, s]) => {
                $(i).src = url; $(i).style.display = 'block';
                if ($(s)) $(s).style.display = 'none';
            });
        } else {
            window.showHeaderAvatarFallback(); showCardAvatarFallback();
        }

        // Segerakkan dengan EKPI_Session (session.js) supaya tidak ditimpa data default
        if (typeof EKPI_Session !== 'undefined' && typeof EKPI_Session.updateUserSession === 'function') {
            EKPI_Session.updateUserSession({
                id: data.workerId,
                name: data.name,
                title: latestRole,
                department: data.department || '',
                profile_pic_url: url,
                positions: positions
            });
        }

        // 3. Positions: newest first; the last item of the array is the current role
        const pos = $('positionsContainer');
        if (positions.length) {
            const lastIdx = positions.length - 1;
            pos.innerHTML = positions.map((p, idx) => ({ p, idx })).reverse().map(({ p, idx }, i) => {
                const latest = idx === lastIdx;
                return `<div class="position-badge-item ${latest ? 'latest' : ''}" style="--i:${i}" title="${latest ? 'Current position' : 'Previous position'}">
                            <span class="pos-icon">${icon(latest ? 'star' : 'history')}</span>
                            <span class="pos-name">${esc(p)}</span>
                            ${latest ? '<span class="pos-tag">Current</span>' : ''}
                        </div>`;
            }).join('');
        } else {
            pos.innerHTML = '<span class="no-pos-text">Tiada Jawatan</span>';
        }

        // 4. KPI
        const kpi = data.kpi || { current: 0, department: 0, company: 0 };
        const cur = setKpi('kpiCurrent', kpi.current);
        const dep = setKpi('kpiDept', kpi.department);
        const com = setKpi('kpiCompany', kpi.company);
        const tier = tierFor(cur);
        setChip('kpiCurrentChip', tier.cls, tier.icon, tier.label);
        compareChip('kpiDeptChip', cur, dep, 'dept');
        compareChip('kpiCompanyChip', cur, com, 'company');

        // 5. Job report
        jobs = Array.isArray(data.job_reports) ? data.job_reports : [];
        renderJobs();
    }

    /* =====================================================
       LIVE LARAVEL MYSQL FETCH & REFRESH HOOK
    ===================================================== */
    let firstLoad = true;
    async function fetchWorkerProfileFromBackend() {
        if (syncState === 'loading') return;
        syncState = 'loading'; renderSync();

        try {
            let data;
            if (firstLoad && initialSeed) {
                // Muatan pertama terus guna data Laravel MySQL dari Blade
                data = initialSeed;
            } else {
                renderSkeleton();
                const response = await fetch(window.location.href, {
                    credentials: 'same-origin',
                    headers: {
                        'Accept': 'application/json, text/html',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                if (!response.ok) throw new Error('Failed to fetch worker profile');

                const contentType = response.headers.get('content-type') || '';
                if (contentType.includes('application/json')) {
                    data = await response.json();
                } else {
                    const html = await response.text();
                    const parsedDoc = new DOMParser().parseFromString(html, 'text/html');
                    data = readSeedData(parsedDoc);
                }
            }

            renderWorkerPageUI(data);
            lastSync = Date.now(); syncState = 'ok';
            if (!firstLoad) toast('Profile data refreshed.');
        } catch (err) {
            console.error('[Worker Page] Error fetching backend profile:', err);
            syncState = 'error';
            if (firstLoad) {
                jobs = [];
                renderJobs({ animate: false });
                $('positionsContainer').innerHTML = '<span class="no-pos-text">Unable to load</span>';
            }
            toast('Could not refresh data. Please try again.', 'error');
        } finally {
            firstLoad = false; renderSync();
        }
    }

    /* =====================================================
       INTERACTIONS
    ===================================================== */
    async function copyWorkerId() {
        const id = $('workerId').textContent.trim(), btn = $('copyIdBtn');
        try {
            if (navigator.clipboard && window.isSecureContext) await navigator.clipboard.writeText(id);
            else {
                const ta = document.createElement('textarea');
                ta.value = id; ta.style.position = 'fixed'; ta.style.opacity = '0';
                document.body.appendChild(ta); ta.select();
                if (!document.execCommand('copy')) throw new Error('copy failed');
                ta.remove();
            }
            btn.classList.add('copied');
            btn.innerHTML = icon('tick');
            btn.setAttribute('aria-label', 'Worker ID copied');
            toast(`Worker ID ${id} copied.`);
            setTimeout(() => { btn.classList.remove('copied'); btn.innerHTML = icon('copy'); btn.setAttribute('aria-label', 'Copy worker ID'); }, 1800);
        } catch {
            toast('Could not copy. Please copy it manually.', 'error');
        }
    }

    function initInteractions() {

        const photoInput = $('workerPhotoInput');
        if (photoInput) {
            photoInput.addEventListener('change', async (e) => {
                const file = e.target.files?.[0];
                if (!file) return;

                const formData = new FormData();
                formData.append('photo', file);

                const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
                try {
                    const res = await fetch('{{ route("worker.photo.update") }}', {
                        method: 'POST',
                        credentials: 'same-origin',
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': csrf
                        },
                        body: formData
                    });
                    const data = await res.json();
                    if (!res.ok || data.status !== 'success') {
                        throw new Error(data.message || 'Upload gagal');
                    }

                    // Kemas kini gambar di kad profil & header serta-merta
                    [['headerUserProfilePic', 'defaultAvatarSvg'], ['cardUserProfilePic', 'cardDefaultAvatarSvg']].forEach(([imgId, svgId]) => {
                        if ($(imgId)) { $(imgId).src = data.photo_url; $(imgId).style.display = 'block'; }
                        if ($(svgId)) $(svgId).style.display = 'none';
                    });

                    if (typeof EKPI_Session !== 'undefined' && typeof EKPI_Session.updateUserSession === 'function') {
                        EKPI_Session.updateUserSession({ profile_pic_url: data.photo_url });
                    }

                    toast('Gambar profil berjaya disimpan ke pangkalan data.');
                } catch (err) {
                    console.error(err);
                    toast('Gagal memuat naik gambar profil.', 'error');
                } finally {
                    photoInput.value = '';
                }
            });
        }

        $('syncBtn').addEventListener('click', fetchWorkerProfileFromBackend);
        $('copyIdBtn').addEventListener('click', copyWorkerId);

        const search = $('jobSearch');
        search.addEventListener('input', () => { query = search.value; $('searchClear').hidden = !query; renderJobs({ animate: false }); });
        search.addEventListener('keydown', e => { if (e.key === 'Escape' && search.value) { search.value = ''; query = ''; $('searchClear').hidden = true; renderJobs({ animate: false }); } });
        $('searchClear').addEventListener('click', () => { search.value = ''; query = ''; $('searchClear').hidden = true; renderJobs({ animate: false }); search.focus(); });

        document.querySelectorAll('.filter-btn').forEach(b => b.addEventListener('click', () => { filterKey = b.dataset.filter; renderJobs(); }));

        document.querySelectorAll('.sort-btn').forEach(b => b.addEventListener('click', () => {
            const k = b.dataset.sort;
            if (sort.key !== k) sort = { key: k, dir: 'asc' };
            else if (sort.dir === 'asc') sort.dir = 'desc';
            else sort = { key: null, dir: 'asc' };
            renderJobs();
        }));

        setInterval(renderSync, 30000);
        document.addEventListener('visibilitychange', () => {
            if (!document.hidden && lastSync && Date.now() - lastSync > 5 * 60 * 1000) fetchWorkerProfileFromBackend();
        });
    }

    function initDropdownA11y() {
        [['notificationBtn', 'notificationDropdown'], ['profileBtn', 'profileDropdown']].forEach(([b, d]) => {
            const btn = $(b), dd = $(d);
            if (!btn || !dd) return;
            new MutationObserver(() => btn.setAttribute('aria-expanded', dd.classList.contains('active')))
                .observe(dd, { attributes: true, attributeFilter: ['class'] });
        });
        document.addEventListener('keydown', e => {
            if (e.key === 'Escape')
                document.querySelectorAll('.notification-dropdown.active, .profile-dropdown.active').forEach(d => d.classList.remove('active'));
        });
    }

    document.addEventListener('DOMContentLoaded', () => {
        initInteractions();
        initDropdownA11y();
        fetchWorkerProfileFromBackend();
    });
    })();
    </script>
   <script src="{{ asset('session.js') }}?v=8"></script>
</body>

</html>