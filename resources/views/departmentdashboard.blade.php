<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Department Dashboard - E-KPI System</title>
    <link rel="stylesheet" href="{{ asset('styledepartment.css') }}">
      <script src="{{ asset('session.js') }}?v=8"></script>
</head>
<body>

    <a class="skip-link" href="#main">Skip to main content</a>

    <!-- SVG ICON SPRITE -->
    <svg width="0" height="0" style="position:absolute" aria-hidden="true" focusable="false">
        <symbol id="i-refresh" viewBox="0 0 24 24"><path d="M21 12a9 9 0 0 0-15-6.7L3 8"/><path d="M3 3v5h5M3 12a9 9 0 0 0 15 6.7L21 16"/><path d="M16 16h5v5"/></symbol>
        <symbol id="i-building" viewBox="0 0 24 24"><rect x="4" y="2" width="16" height="20" rx="2"/><path d="M9 22v-4h6v4M8 6h.01M12 6h.01M16 6h.01M8 10h.01M12 10h.01M16 10h.01M8 14h.01M12 14h.01M16 14h.01"/></symbol>
        <symbol id="i-clock" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></symbol>
        <symbol id="i-arrow-up" viewBox="0 0 24 24"><path d="M12 19V5M5 12l7-7 7 7"/></symbol>
        <symbol id="i-arrow-down" viewBox="0 0 24 24"><path d="M12 5v14M19 12l-7 7-7-7"/></symbol>
        <symbol id="i-sort" viewBox="0 0 24 24"><path d="m7 15 5 5 5-5M7 9l5-5 5 5"/></symbol>
        <symbol id="i-medal" viewBox="0 0 24 24"><path d="M7.2 15 2.7 7.1a2 2 0 0 1 .1-2.2L4.4 2.8A2 2 0 0 1 6 2h12a2 2 0 0 1 1.6.8l1.6 2.1a2 2 0 0 1 .1 2.2L16.8 15"/><path d="M11 12 5.1 2.2M13 12l5.9-9.8M8 7h8"/><circle cx="12" cy="17" r="5"/></symbol>
        <symbol id="i-alert" viewBox="0 0 24 24"><path d="M10.3 3.9 1.8 18a2 2 0 0 0 1.7 3h17a2 2 0 0 0 1.7-3L13.7 3.9a2 2 0 0 0-3.4 0z"/><path d="M12 9v4M12 17h.01"/></symbol>
        <symbol id="i-alert-circle" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M12 8v4M12 16h.01"/></symbol>
        <symbol id="i-check" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="m9 12 2 2 4-4"/></symbol>
        <symbol id="i-mail" viewBox="0 0 24 24"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="m22 7-10 6L2 7"/></symbol>
        <symbol id="i-users" viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/></symbol>
        <symbol id="i-edit" viewBox="0 0 24 24"><path d="M12 20h9"/><path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4z"/></symbol>
        <symbol id="i-plus" viewBox="0 0 24 24"><path d="M12 5v14M5 12h14"/></symbol>
        <symbol id="i-minus" viewBox="0 0 24 24"><path d="M5 12h14"/></symbol>
        <symbol id="i-x" viewBox="0 0 24 24"><path d="M18 6 6 18M6 6l12 12"/></symbol>
        <symbol id="i-info" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M12 16v-4M12 8h.01"/></symbol>
        <symbol id="i-loader" viewBox="0 0 24 24"><path d="M21 12a9 9 0 1 1-6.2-8.6"/></symbol>
        <symbol id="i-inbox" viewBox="0 0 24 24"><path d="M22 12h-6l-2 3h-4l-2-3H2"/><path d="M5.5 5.1 2 12v6a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-6l-3.5-6.9A2 2 0 0 0 16.7 4H7.3a2 2 0 0 0-1.8 1.1z"/></symbol>
        <symbol id="i-user" viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></symbol>
        <symbol id="i-settings" viewBox="0 0 24 24"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"/></symbol>
        <symbol id="i-logout" viewBox="0 0 24 24"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4M16 17l5-5-5-5M21 12H9"/></symbol>
    </svg>

    <!-- ================================ HEADER ================================ -->
    <header class="header">
        <div class="header-left">
            <span class="system-title">E-KPI SYSTEM</span>
            <div class="header-line" aria-hidden="true"></div>
            <div class="ilm-logo"><img src="{{ asset('assets/ILMgroups-logo-4.png') }}" alt="ILM Groups logo"></div>
            <span class="company-name">INFINITE LOOP MEDIA</span>
        </div>

        <div class="header-right">
            <div class="notification-wrapper">
                <button class="header-icon" id="notificationBtn" aria-label="Notifications"
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
                    <div class="notification-list" id="notificationList" aria-live="polite"></div>
                </div>
            </div>

            <div class="profile-wrapper">
                <button class="header-icon profile-btn" id="profileBtn" aria-label="User profile menu"
                        aria-haspopup="true" aria-expanded="false" aria-controls="profileDropdown">
                    <div class="profile-avatar-wrapper" id="headerAvatarContainer">
                        <svg id="defaultAvatarSvg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#ffffff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                            <circle cx="12" cy="7" r="4"></circle>
                        </svg>
                        <img id="headerUserProfilePic" src="" alt="" class="profile-avatar-img" style="display: none;" onerror="showAvatarFallback()">
                    </div>
                </button>

                <div class="profile-dropdown" id="profileDropdown">
                    <div class="profile-dropdown-header">
                        <span class="user-name" id="dropdownUserName">{{ Auth::user()->full_name ?? 'Kamal Hassan' }}</span>
                        <span class="user-role" id="dropdownUserRole">{{ Auth::user()->designation ?? 'Team Lead' }}</span>
                    </div>
                    <ul class="profile-dropdown-menu">
                        <li><a href="{{ route('worker.dashboard') }}"><svg class="icon" aria-hidden="true" style="font-size:15px"><use href="#i-user"/></svg>View Profile</a></li>
                        <li><a href="{{ route('profile.edit') }}"><svg class="icon" aria-hidden="true" style="font-size:15px"><use href="#i-settings"/></svg>Account Settings</a></li>
                        <li class="divider"></li>
                        <li>
                            <a href="{{ route('logout') }}" class="logout-link"
                               onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                <svg class="icon" aria-hidden="true" style="font-size:15px"><use href="#i-logout"/></svg>Log Out
                            </a>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                @csrf
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </header>

    <!-- ============================== NAVIGATION ============================== -->
    <nav class="navigation reveal" style="--i:1" aria-label="Main navigation">
        <a href="{{ route('leader.dashboard') }}" class="nav-link active" aria-current="page">Dashboard</a>
        <a href="{{ route('leader.kpi') }}" class="nav-link">Input Rating</a>
        <a href="{{ route('leader.team.status') }}" class="nav-link">Team Status</a>
        <a href="{{ route('leader.reports') }}" class="nav-link">Reports</a>

        <div class="sync-status reveal" style="--i:1" id="syncStatus">
            <span class="sync-dot" aria-hidden="true"></span>
            <span id="syncText" role="status" aria-live="polite">Synced just now</span>
            <button type="button" class="sync-btn" id="syncBtn" aria-label="Refresh data" title="Refresh data">
                <svg class="icon" aria-hidden="true"><use href="#i-refresh"/></svg>
            </button>
        </div>
    </nav>

    <!-- ============================== MAIN CONTAINER ============================== -->
    <main class="dept-main-container" id="main">

        <!-- HEADER STRIP -->
        <div class="dept-header-strip reveal" style="--i:2">
            <div class="dept-title-group">
                <svg class="icon" aria-hidden="true"><use href="#i-building"/></svg>
                <span class="dept-name" id="deptName">DEPARTMENT : {{ $seedData['deptName'] ?? 'WEBTECH' }}</span>
            </div>
            <div class="cutoff-pill" id="cutoffPill">
                <svg class="icon" aria-hidden="true"><use href="#i-clock"/></svg>
                <span id="cutoffText"></span>
            </div>
        </div>

        <!-- METRIC CARDS -->
        <section class="dept-summary-cards" aria-label="Department summary">

            <div class="dept-card reveal" style="--i:3">
                <div class="dept-card-header"><h3>DEPT AVERAGE KPI SCORE (%)</h3></div>
                <div class="dept-card-value" id="avgKpiVal">0.0%</div>
                <div class="dept-card-footer">
                    <span class="trend-badge positive" id="avgKpiTrend">
                        <svg class="icon" aria-hidden="true"><use href="#i-arrow-up"/></svg>
                        <span id="avgKpiTrendText">0.0% vs last quarter</span>
                    </span>
                </div>
            </div>

            <div class="dept-card reveal" style="--i:4" id="ratingCard">
                <div class="dept-card-header"><h3>TEAM RATING STATUS</h3></div>
                <div class="dept-card-value danger-text" id="ratingStatusVal">0/0 STAFF</div>
                <div class="dept-card-footer">
                    <span class="sub-status-text danger-text" id="pendingRatingSubtext"></span>
                </div>
            </div>

            <div class="dept-card reveal" style="--i:5">
                <div class="dept-card-header"><h3>DEPT EOM ELIGIBILITY</h3></div>
                <div class="eom-eligibility-info">
                    <span class="qualify-badge" id="qualifyBadge">0 QUALIFIED</span>
                    <div class="eom-badges-row">
                        <div class="eom-badge-pill gold" id="goldPill">
                            <svg class="icon" aria-hidden="true"><use href="#i-medal"/></svg><span id="goldCount">0 GOLD</span>
                        </div>
                        <div class="eom-badges-row-divider" aria-hidden="true"></div>
                        <div class="eom-badge-pill silver" id="silverPill">
                            <svg class="icon" aria-hidden="true"><use href="#i-medal"/></svg><span id="silverCount">0 SILVER</span>
                        </div>
                    </div>
                </div>
            </div>

        </section>

        <!-- SYSTEM WARNING BANNER -->
        <div class="system-warning-box reveal" style="--i:6" id="systemWarningBox" role="status" aria-live="polite">
            <div class="warning-content">
                <div class="warning-icon-wrapper"><svg class="icon" id="warningIcon" aria-hidden="true"><use href="#i-alert"/></svg></div>
                <div class="warning-text-group">
                    <strong class="warning-title" id="warningTitle">SYSTEM WARNING :</strong>
                    <span id="warningMsgText"></span>
                </div>
            </div>
            <button type="button" class="btn-email-reminder" id="btnReminder">
                <svg class="icon btn-icon" aria-hidden="true"><use href="#i-mail"/></svg>
                <svg class="icon btn-spinner" aria-hidden="true"><use href="#i-loader"/></svg>
                <span id="btnReminderLabel">SEND EMAIL REMINDER</span>
            </button>
        </div>

        <!-- STAFF TABLE -->
        <section class="performance-section reveal" style="--i:7" aria-labelledby="tableTitle">
            <div class="performance-title">
                <h2 class="table-title-group" id="tableTitle" style="font-weight:500">
                    <svg class="icon" aria-hidden="true"><use href="#i-users"/></svg>TEAM KPI EVALUATION TABLE
                </h2>
                <div class="title-tools">
                    <div class="filter-group" role="group" aria-label="Filter staff by rating status">
                        <button type="button" class="filter-btn" data-filter="all" aria-pressed="true">All <span id="cntAll">0</span></button>
                        <button type="button" class="filter-btn" data-filter="pending" aria-pressed="false">Pending <span id="cntPending">0</span></button>
                        <button type="button" class="filter-btn" data-filter="rated" aria-pressed="false">Rated <span id="cntRated">0</span></button>
                    </div>
                    <span class="dept-count-badge" id="staffCountBadge">0 Staff Members</span>
                </div>
            </div>

            <div class="table-wrapper" tabindex="0" role="region" aria-labelledby="tableTitle">
                <table class="performance-table">
                    <thead>
                        <tr>
                            <th scope="col" style="width: 50px;">NO</th>
                            <th scope="col" style="width: 180px;" aria-sort="none"><button type="button" class="sort-btn left" data-sort="name">STAFF NAME<svg class="icon" aria-hidden="true"><use href="#i-sort"/></svg></button></th>
                            <th scope="col" style="width: 110px;" aria-sort="none"><button type="button" class="sort-btn" data-sort="kpi">KPI (/40)<svg class="icon" aria-hidden="true"><use href="#i-sort"/></svg></button></th>
                            <th scope="col" style="width: 140px;" aria-sort="none"><button type="button" class="sort-btn" data-sort="attendance">ATTENDANCE (/20)<svg class="icon" aria-hidden="true"><use href="#i-sort"/></svg></button></th>
                            <th scope="col" style="width: 130px;" aria-sort="none"><button type="button" class="sort-btn" data-sort="rating">RATING (/40)<svg class="icon" aria-hidden="true"><use href="#i-sort"/></svg></button></th>
                            <th scope="col" style="width: 120px;" aria-sort="none"><button type="button" class="sort-btn" data-sort="totalScore">TOTAL SCORE<svg class="icon" aria-hidden="true"><use href="#i-sort"/></svg></button></th>
                            <th scope="col" style="width: 140px;">STATUS</th>
                            <th scope="col" style="width: 130px;">EOM BADGE</th>
                            <th scope="col" style="width: 120px;">ACTION</th>
                        </tr>
                    </thead>
                    <tbody id="staffTableBody" aria-live="polite"></tbody>
                </table>
            </div>
        </section>

    </main>

    <!-- RATING DIALOG -->
    <dialog class="dept-dialog" id="ratingDialog" aria-labelledby="ratingTitle">
        <div class="modal-header">
            <div class="modal-title-group">
                <svg class="icon" aria-hidden="true"><use href="#i-edit"/></svg>
                <h3 id="ratingTitle">Input Leader Rating Score</h3>
            </div>
            <button type="button" class="modal-close-btn" id="ratingCloseX" aria-label="Close"><svg class="icon" aria-hidden="true"><use href="#i-x"/></svg></button>
        </div>

        <div class="modal-body">
            <div class="modal-staff-info">
                <strong id="modalStaffName">Staff Name</strong>
                <span id="modalStaffSub">{{ $seedData['deptLabel'] ?? 'WebTech' }} Department</span>
            </div>

            <form id="ratingForm" novalidate>
                <div class="criteria-grid">
                    <div class="criteria-item">
                        <div class="criteria-head">
                            <label for="critQ1">1. Work Quality &amp; Output Accuracy</label>
                        </div>
                        <div class="criteria-controls">
                            <input type="range" id="sliderQ1" min="0" max="10" step="0.5" value="0" aria-label="Work Quality slider">
                            <div class="criteria-num-wrap">
                                <input type="number" id="critQ1" data-slider="sliderQ1" min="0" max="10" step="0.5" placeholder="0.0" required>
                                <span>/10</span>
                            </div>
                        </div>
                    </div>

                    <div class="criteria-item">
                        <div class="criteria-head">
                            <label for="critQ2">2. Problem Solving &amp; Technical Skill</label>
                        </div>
                        <div class="criteria-controls">
                            <input type="range" id="sliderQ2" min="0" max="10" step="0.5" value="0" aria-label="Problem Solving slider">
                            <div class="criteria-num-wrap">
                                <input type="number" id="critQ2" data-slider="sliderQ2" min="0" max="10" step="0.5" placeholder="0.0" required>
                                <span>/10</span>
                            </div>
                        </div>
                    </div>

                    <div class="criteria-item">
                        <div class="criteria-head">
                            <label for="critQ3">3. Teamwork &amp; Communication</label>
                        </div>
                        <div class="criteria-controls">
                            <input type="range" id="sliderQ3" min="0" max="10" step="0.5" value="0" aria-label="Teamwork slider">
                            <div class="criteria-num-wrap">
                                <input type="number" id="critQ3" data-slider="sliderQ3" min="0" max="10" step="0.5" placeholder="0.0" required>
                                <span>/10</span>
                            </div>
                        </div>
                    </div>

                    <div class="criteria-item">
                        <div class="criteria-head">
                            <label for="critQ4">4. Initiative &amp; Discipline</label>
                        </div>
                        <div class="criteria-controls">
                            <input type="range" id="sliderQ4" min="0" max="10" step="0.5" value="0" aria-label="Initiative slider">
                            <div class="criteria-num-wrap">
                                <input type="number" id="critQ4" data-slider="sliderQ4" min="0" max="10" step="0.5" placeholder="0.0" required>
                                <span>/10</span>
                            </div>
                        </div>
                    </div>
                </div>

                <span class="field-error" id="ratingError" role="alert" hidden></span>

                <div class="score-breakdown-box" aria-live="polite">
                    <div class="breakdown-row"><span>Leader Rating (/40):</span> <strong id="modalLeaderTotalVal">0.0 / 40</strong></div>
                    <div class="breakdown-row"><span>KPI Score (/40):</span> <strong id="modalKpiVal">0.0</strong></div>
                    <div class="breakdown-row"><span>Attendance (/20):</span> <strong id="modalAttVal">0.0</strong></div>
                    <div class="breakdown-row"><span>Projected Status:</span> <strong id="modalStatus">-</strong></div>
                    <div class="breakdown-row highlight"><span>Projected Total:</span> <strong id="modalProjectedTotal">0.0%</strong></div>
                </div>
            </form>
        </div>

        <div class="modal-footer">
            <button type="button" class="btn-modal-secondary" id="ratingCancel">Cancel</button>
            <button type="submit" form="ratingForm" class="btn-modal-primary" id="ratingSave">Save Rating</button>
        </div>
    </dialog>

    <!-- CONFIRM DIALOG (email reminder) -->
    <dialog class="dept-dialog" id="confirmDialog" aria-labelledby="confirmTitle" aria-describedby="confirmDesc">
        <form method="dialog">
            <div class="confirm-body">
                <div class="confirm-icon"><svg class="icon" aria-hidden="true"><use href="#i-mail"/></svg></div>
                <div><h3 id="confirmTitle"></h3><p id="confirmDesc"></p></div>
            </div>
            <div class="modal-footer">
                <button type="submit" value="cancel" class="btn-modal-secondary">Cancel</button>
                <button type="submit" value="confirm" class="btn-modal-primary" id="confirmOk">Send</button>
            </div>
        </form>
    </dialog>

    <div class="toast-region" id="toastRegion" aria-live="polite"></div>

    <!-- LARAVEL MYSQL SEED DATA -->
    <script type="application/json" id="laravelDeptSeed">
        @json($seedData)
    </script>

    <script>
    /* =====================================================
       LOAD SEED DATA FROM LARAVEL MYSQL
    ===================================================== */
    let seed = {
        deptName: 'WEBTECH',
        deptLabel: 'WebTech',
        cutoffDay: 28,
        goldThreshold: 90,
        silverThreshold: 85,
        bronzeThreshold: 80,
        lastQuarterAvg: 85.9,
        staffList: [],
        leader: { id: '', name: 'Team Lead', title: 'Department Leader', photo: '' }
    };
    try {
        const raw = document.getElementById('laravelDeptSeed')?.textContent?.trim();
        if (raw) seed = Object.assign(seed, JSON.parse(raw));
    } catch (e) {
        console.warn('Dept seed parse warning:', e);
    }

    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

    @php
        $leaderRoutes = [
            'rating' => \Illuminate\Support\Facades\Route::has('leader.rating.save')
                ? route('leader.rating.save')
                : url('/leader/rating'),
            'reminder' => \Illuminate\Support\Facades\Route::has('leader.reminder')
                ? route('leader.reminder')
                : url('/leader/reminder'),
            'dashboardData' => \Illuminate\Support\Facades\Route::has('leader.dashboard.data')
                ? route('leader.dashboard.data')
                : url('/leader/dashboard/data'),
        ];
    @endphp
    const ROUTES = @json($leaderRoutes);

    if (window.EKPI_Session && seed?.leader) {
        window.EKPI_Session.updateUserSession({
            name: seed.leader.name || '',
            title: seed.leader.title || '',
            profile_pic_url: seed.leader.photo || ''
        });
    }

    const markAllRead = document.getElementById('markAllRead');
    if (markAllRead) {
        markAllRead.addEventListener('click', () => {
            const badge = document.getElementById('notifBadge');
            if (badge) badge.style.display = 'none';
            toast('Notifications marked as read', 'info');
        });
    }
    /* =====================================================
       HELPERS
    ===================================================== */
    const $ = id => document.getElementById(id);
    const prefersReduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    const icon = (name, cls = '') => `<svg class="icon ${cls}" aria-hidden="true"><use href="#i-${name}"/></svg>`;
    const esc = s => String(s ?? '').replace(/[&<>"']/g, c => ({
    '\u0026': '\u0026amp;',
    '\u003C': '\u0026lt;',
    '\u003E': '\u0026gt;',
    '\u0022': '\u0026quot;',
    '\u0027': '\u0026#39;'
}[c]));
    const round1 = n => Math.round(n * 10) / 10;

    function animateNumber(el, to, fmt) {
        const from = parseFloat(el.dataset.val) || 0;
        el.dataset.val = to;
        if (prefersReduced || from === to) { el.textContent = fmt(to); return; }
        const start = performance.now(), dur = 900;
        const step = now => {
            const p = Math.min((now - start) / dur, 1);
            el.textContent = fmt(from + (to - from) * (1 - Math.pow(1 - p, 3)));
            if (p < 1) requestAnimationFrame(step);
        };
        requestAnimationFrame(step);
    }

    function toast(text, type = 'success', { action = null, duration = 3500 } = {}) {
        const el = document.createElement('div');
        el.className = 'toast ' + type;
        el.innerHTML = `${icon(type === 'success' ? 'check' : type === 'error' ? 'alert-circle' : 'info')}<span>${esc(text)}</span>
            ${action ? `<button type="button" class="toast-action">${esc(action.label)}</button>` : ''}
            <button type="button" class="toast-close" aria-label="Dismiss notification">${icon('x')}</button>`;
        const close = () => { el.classList.add('out'); setTimeout(() => el.remove(), prefersReduced ? 0 : 220); };
        el.querySelector('.toast-close').addEventListener('click', close);
        if (action) el.querySelector('.toast-action').addEventListener('click', () => { action.onClick(); close(); });
        $('toastRegion').appendChild(el);
        setTimeout(close, duration);
    }

    if (typeof window.showAvatarFallback !== 'function') {
        window.showAvatarFallback = function () {
            const img = $('headerUserProfilePic'), svg = $('defaultAvatarSvg');
            if (img) img.style.display = 'none';
            if (svg) svg.style.display = '';
        };
    }

    function confirmDialog({ title, desc, ok = 'Confirm' }) {
        const dlg = $('confirmDialog');
        if (typeof dlg.showModal !== 'function') return Promise.resolve(window.confirm(`${title}\n\n${desc}`));
        $('confirmTitle').textContent = title; $('confirmDesc').textContent = desc; $('confirmOk').textContent = ok;
        return new Promise(resolve => {
            dlg.returnValue = '';
            dlg.addEventListener('close', () => resolve(dlg.returnValue === 'confirm'), { once: true });
            dlg.showModal();
        });
    }

    /* =====================================================
       STATE
    ===================================================== */
    let DEPT_NAME = seed.deptName || 'WEBTECH';
    const MAX = { kpi: 40, attendance: 20, rating: 40 };
    let staffList = Array.isArray(seed.staffList) ? seed.staffList : [];
    let LAST_QUARTER_AVG = Number(seed.lastQuarterAvg || 85.9);

    const todayStart = () => { const d = new Date(); d.setHours(0, 0, 0, 0); return d; };
    function getCutoffDate() {
        const now = todayStart();
        const day = Number(seed.cutoffDay || 28);
        return new Date(now.getFullYear(), now.getMonth(), day);
    }

    function qualifyScore() {
        return Number(seed.silverThreshold || 75);
    }

    let filterKey = 'all', sort = { key: null, dir: 'asc' }, activeEditId = null, flashId = null, animateRows = true;

    /* =====================================================
       SCORING
    ===================================================== */
    function statusFor(total) {
        if (total === null) return { text: 'INCOMPLETE', cls: 'incomplete', icon: 'clock' };
        if (total >= 90) return { text: 'EXCELLENCE', cls: 'complete', icon: 'check' };
        if (total >= 80) return { text: 'GOOD', cls: 'complete', icon: 'check' };
        return { text: 'ATTENTION', cls: 'attention', icon: 'alert-circle' };
    }

    function processStaffData() {
        const q = qualifyScore();
        const processed = staffList.map(s => {
            const totalScore = s.rating === null ? null : round1(Number(s.kpi) + Number(s.attendance) + Number(s.rating));
            return { ...s, kpi: Number(s.kpi), attendance: Number(s.attendance), rating: s.rating === null ? null : Number(s.rating), totalScore, status: statusFor(totalScore), eomBadge: null };
        });
        const ranked = processed.filter(s => s.totalScore !== null && s.totalScore >= q)
            .sort((a, b) => b.totalScore - a.totalScore || b.rating - a.rating || a.name.localeCompare(b.name));
        if (ranked[0]) ranked[0].eomBadge = { label: 'EOM 1', type: 'gold' };
        if (ranked[1]) ranked[1].eomBadge = { label: 'EOM 2', type: 'silver' };
        return { processed, qualified: ranked.length, q };
    }

    /* =====================================================
       RENDER
    ===================================================== */
    function renderCutoff() {
        const cutoff = getCutoffDate();
        const days = Math.round((cutoff - todayStart()) / 86400000);
        const date = cutoff.toLocaleDateString('en-GB', { day: 'numeric', month: 'long', year: 'numeric' }).toUpperCase();
        const pill = $('cutoffPill');
        let tail, cls;
        if (days < 0) { tail = `<strong>CUT-OFF PASSED ${-days} ${-days === 1 ? 'DAY' : 'DAYS'} AGO</strong>`; cls = 'urgent'; }
        else if (days === 0) { tail = '<strong>LAST DAY</strong>'; cls = 'urgent'; }
        else { tail = `<strong>${days} ${days === 1 ? 'DAY' : 'DAYS'} LEFT</strong>`; cls = days <= 3 ? 'urgent' : days <= 7 ? 'soon' : 'calm'; }
        pill.className = 'cutoff-pill ' + cls;
        $('cutoffText').innerHTML = `CUT-OFF : ${date} ( ${tail} )`;
    }

    function renderSummary({ processed, qualified, q }) {
        const rated = processed.filter(s => s.totalScore !== null), pending = processed.filter(s => s.totalScore === null);
        const avg = rated.length ? rated.reduce((a, s) => a + s.totalScore, 0) / rated.length : 0;
        const trend = rated.length ? avg - LAST_QUARTER_AVG : 0;

        animateNumber($('avgKpiVal'), avg, v => v.toFixed(1) + '%');
        const up = trend >= 0;
        $('avgKpiTrend').className = 'trend-badge ' + (up ? 'positive' : 'negative');
        $('avgKpiTrend').querySelector('use').setAttribute('href', up ? '#i-arrow-up' : '#i-arrow-down');
        animateNumber($('avgKpiTrendText'), Math.abs(trend), v => `${up ? '+' : '−'}${v.toFixed(1)}% vs last quarter`);

        const complete = pending.length === 0 && processed.length > 0;
        $('ratingStatusVal').textContent = `${rated.length}/${processed.length} STAFF`;
        $('ratingStatusVal').className = 'dept-card-value ' + (complete ? 'success-text' : 'danger-text');
        $('ratingCard').classList.toggle('complete', complete);
        const sub = $('pendingRatingSubtext');
        sub.className = 'sub-status-text ' + (complete ? 'success-text' : 'danger-text');
        sub.innerHTML = complete ? icon('check') + 'ALL RATINGS COMPLETED' : icon('alert-circle') + `${pending.length} STAFF RATING PENDING`;

        const gold = processed.filter(s => s.eomBadge && s.eomBadge.type === 'gold').length;
        const silver = processed.filter(s => s.eomBadge && s.eomBadge.type === 'silver').length;
        $('qualifyBadge').textContent = `${qualified} QUALIFIED (SCORE >= ${q}%)`;
        $('qualifyBadge').classList.toggle('none', qualified === 0);
        [['goldCount', 'goldPill', `${gold} GOLD`], ['silverCount', 'silverPill', `${silver} SILVER`]].forEach(([t, p, txt]) => {
            if ($(t).textContent !== txt) { $(t).textContent = txt; $(p).classList.remove('pop'); void $(p).offsetWidth; $(p).classList.add('pop'); }
        });

        const box = $('systemWarningBox'), btn = $('btnReminder');
        box.classList.toggle('ok', complete);
        $('warningIcon').querySelector('use').setAttribute('href', complete ? '#i-check' : '#i-alert');
        $('warningTitle').textContent = complete ? 'ALL CLEAR :' : 'SYSTEM WARNING :';
        $('warningMsgText').textContent = complete
            ? 'ALL LEADER RATINGS HAVE BEEN SUBMITTED FOR THIS QUARTER.'
            : `${pending.length} STAFF MEMBER${pending.length > 1 ? 'S' : ''} (${pending.map(s => s.name.toUpperCase()).join(', ')}) ${pending.length > 1 ? 'ARE' : 'IS'} MISSING LEADER RATING INPUTS BEFORE CUT OFF DATE.`;
        btn.hidden = complete || pending.length === 0;

        renderNotifications(pending);
    }

    function renderNotifications(pending) {
        const list = $('notificationList'), badge = $('notifBadge');
        if (!pending.length) {
            list.innerHTML = `<div class="no-notifications">${icon('check')}<p>No new notifications</p></div>`;
            badge.style.display = 'none';
            return;
        }
        list.innerHTML = `<div class="notification-item unread">
                <h4 class="notif-title">Rating Input Pending</h4>
                <p class="notif-desc">${pending.length} staff member${pending.length > 1 ? 's' : ''} (${esc(pending.map(s => s.name).join(', '))}) ${pending.length > 1 ? 'are' : 'is'} missing leader rating before cut-off date.</p>
                <span class="notif-time">Action required</span>
            </div>`;
        badge.textContent = pending.length; badge.style.display = 'inline-block';
    }

    function renderStaffTable() {
        const data = processStaffData();
        renderSummary(data);

        const all = data.processed;
        const nPending = all.filter(s => s.totalScore === null).length;
        $('cntAll').textContent = all.length; $('cntPending').textContent = nPending; $('cntRated').textContent = all.length - nPending;
        document.querySelectorAll('.filter-btn').forEach(b => b.setAttribute('aria-pressed', String(b.dataset.filter === filterKey)));

        let rows = all.filter(s => filterKey === 'all' || (filterKey === 'pending' ? s.totalScore === null : s.totalScore !== null));
        if (sort.key) {
            const v = s => s[sort.key] === null ? -1 : s[sort.key];
            rows = [...rows].sort((a, b) => {
                const x = v(a), y = v(b);
                return (typeof x === 'string' ? x.localeCompare(y) : x - y) * (sort.dir === 'asc' ? 1 : -1);
            });
        }
        document.querySelectorAll('th[aria-sort]').forEach(th => {
            const btn = th.querySelector('.sort-btn'), on = btn.dataset.sort === sort.key;
            th.setAttribute('aria-sort', on ? (sort.dir === 'asc' ? 'ascending' : 'descending') : 'none');
            btn.querySelector('use').setAttribute('href', on ? (sort.dir === 'asc' ? '#i-arrow-up' : '#i-arrow-down') : '#i-sort');
        });
        $('staffCountBadge').textContent = `${rows.length} Staff ${rows.length === 1 ? 'Member' : 'Members'}`;

        const tbody = $('staffTableBody');
        if (!rows.length) { tbody.innerHTML = `<tr><td colspan="9" class="empty-cell">${icon('inbox')} No staff match this filter.</td></tr>`; return; }

        tbody.innerHTML = rows.map((s, i) => {
            const pending = s.totalScore === null;
            const eom = s.eomBadge ? `<div class="eom-badge-cell ${s.eomBadge.type}">${icon('medal')}<span>${s.eomBadge.label}</span></div>` : '<span class="eom-none">-</span>';
            return `
                <tr class="${animateRows ? 'enter' : ''} ${pending ? 'pending' : ''} ${String(s.id) === String(flashId) ? 'flash' : ''}" style="--i:${i}">
                    <td><strong>${i + 1}</strong></td>
                    <td style="text-align:left;padding-left:16px"><span class="staff-name-text">${esc(s.name)}</span></td>
                    <td>${s.kpi.toFixed(1)}</td>
                    <td>${s.attendance.toFixed(1)}</td>
                    <td><span class="score-highlight ${pending ? 'empty' : ''}">${pending ? '-' : s.rating.toFixed(1)}</span></td>
                    <td>
                        <strong class="total-score-text">${pending ? 'PENDING' : s.totalScore.toFixed(1) + '%'}</strong>
                        ${pending ? '' : `<div class="mini-bar ${s.totalScore < 80 ? 'low' : ''}"><i style="--p:${Math.min(s.totalScore, 100) / 100}"></i></div>`}
                    </td>
                    <td><span class="status-pill ${s.status.cls}">${icon(s.status.icon)}${s.status.text}</span></td>
                    <td>${eom}</td>
                    <td>
                        ${pending
                            ? `<button type="button" class="btn-table-action primary" data-edit="${esc(s.id)}" aria-label="Input rating for ${esc(s.name)}">${icon('plus')}RATE</button>`
                            : `<a href="/leader/input-rating?worker_id=${encodeURIComponent(s.id)}" class="btn-table-action" style="text-decoration:none;display:inline-flex;align-items:center;justify-content:center;gap:4px;" aria-label="Revise rating for ${esc(s.name)}">${icon('edit')}REVISE</a>`
                        }
                    </td>
                </tr>`;
        }).join('');
        flashId = null; animateRows = false;
    }

    function renderSkeleton() {
        $('staffTableBody').innerHTML = Array.from({ length: 4 }, () => `<tr>${'<td><span class="skel"></span></td>'.repeat(9)}</tr>`).join('');
    }

    /* =====================================================
       RATING DIALOG (4 CRITERIA -> SAVES DIRECTLY TO MYSQL)
    ===================================================== */
    function getCriteriaValues() {
        const ids = ['critQ1', 'critQ2', 'critQ3', 'critQ4'];
        const vals = {};
        let sum = 0;
        let allValid = true;

        ids.forEach((id, idx) => {
            const raw = $(id).value.trim();
            const num = parseFloat(raw);
            if (raw === '' || !Number.isFinite(num) || num < 0 || num > 10) {
                allValid = false;
            } else {
                vals[`q${idx + 1}`] = round1(num);
                sum += round1(num);
            }
        });

        return { valid: allValid, eval: vals, totalLeader: round1(sum) };
    }

    function previewTotals() {
        const s = staffList.find(x => String(x.id) === String(activeEditId));
        if (!s) return { valid: false };
        const { valid, eval: evalObj, totalLeader } = getCriteriaValues();

        $('modalLeaderTotalVal').textContent = valid ? `${totalLeader.toFixed(1)} / 40` : '0.0 / 40';
        const total = valid ? round1(Number(s.kpi) + Number(s.attendance) + totalLeader) : null;
        $('modalProjectedTotal').textContent = total === null ? '-' : total.toFixed(1) + '%';
        const st = statusFor(total);
        $('modalStatus').textContent = valid ? st.text : '-';
        $('modalStatus').style.color = !valid ? '' : st.cls === 'attention' ? '#e11d48' : '#16a34a';
        $('ratingSave').disabled = !valid;
        return { valid, eval: evalObj, totalLeader };
    }

    function setRatingError(msg) {
        const e = $('ratingError');
        e.hidden = !msg;
        e.innerHTML = msg ? icon('alert-circle') + esc(msg) : '';
    }

    function openRatingModal(id) {
        const s = staffList.find(x => String(x.id) === String(id));
        if (!s) return;

        // Jika staf sudah dinilai, paksa buka Dedicated Page untuk proses Revise
        if (s.rating !== null) {
            window.location.href = `/leader/input-rating?worker_id=${encodeURIComponent(s.id)}`;
            return;
        }

        activeEditId = s.id;
        $('modalStaffName').textContent = s.name.toUpperCase();
        $('modalStaffSub').textContent = `${seed.deptLabel || 'WebTech'} Department`;
        $('modalKpiVal').textContent = Number(s.kpi).toFixed(1);
        $('modalAttVal').textContent = Number(s.attendance).toFixed(1);

        ['critQ1', 'critQ2', 'critQ3', 'critQ4'].forEach((id, idx) => {
            $(id).value = '';
            $(`sliderQ${idx + 1}`).value = 0;
        });

        setRatingError('');
        previewTotals();
        $('ratingDialog').showModal();
        $('critQ1').focus();
    }

    async function applyRating(id, evalPayload) {
        const s = staffList.find(x => String(x.id) === String(id));
        if (!s) return;

        try {
            const res = await fetch(ROUTES.rating, {
                method: 'POST',
                credentials: 'same-origin',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({ worker_id: id, eval: evalPayload })
            });

            const data = await res.json();
            if (!res.ok) {
                throw new Error(data.message || ('HTTP ' + res.status));
            }

            if (data.payload && Array.isArray(data.payload.staffList)) {
                staffList = data.payload.staffList;
            }

            flashId = id;
            renderStaffTable();
            toast(`Final rating for ${s.name} saved. Use Revise on KPI Management page for any future changes.`, 'success', { duration: 5000 });
        } catch (err) {
            console.error('[Save Rating Error]:', err);
            toast(err.message || 'Failed to save rating to database.', 'error');
        }
    }

    function saveRating(e) {
        e.preventDefault();
        const { valid, eval: evalObj } = previewTotals();
        if (!valid) {
            setRatingError('Please enter a valid score between 0 and 10 for all 4 criteria.');
            return;
        }
        const id = activeEditId;
        $('ratingDialog').close();
        applyRating(id, evalObj);
    }

    /* =====================================================
       EMAIL REMINDER
    ===================================================== */
    let cooldownTimer = null;

    async function sendEmailReminder() {
        const pending = staffList.filter(s => s.rating === null);
        if (!pending.length) return;
        const ok = await confirmDialog({
            title: 'Send email reminder?',
            desc: `A reminder will be sent regarding ${pending.map(s => s.name).join(', ')} for missing leader rating before the cut-off date.`,
            ok: 'Send reminder'
        });
        if (!ok) return;

        const btn = $('btnReminder'), label = $('btnReminderLabel');
        btn.disabled = true; btn.classList.add('loading'); label.textContent = 'SENDING...';
        try {
            const res = await fetch(ROUTES.reminder, {
                method: 'POST',
                credentials: 'same-origin',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({})
            });
            if (!res.ok) throw new Error('HTTP ' + res.status);
            toast(`Reminder sent for ${pending.map(s => s.name).join(', ')}.`, 'success');
        } catch (err) {
            console.error(err);
            toast('Could not send the reminder. Please try again.', 'error');
            btn.disabled = false; label.textContent = 'SEND EMAIL REMINDER';
            return;
        } finally {
            btn.classList.remove('loading');
        }
        let left = 60;
        clearInterval(cooldownTimer);
        const tick = () => {
            if (left <= 0) { clearInterval(cooldownTimer); btn.disabled = false; label.textContent = 'SEND EMAIL REMINDER'; return; }
            label.textContent = `REMINDER SENT (RESEND IN ${left}s)`; left--;
        };
        tick(); cooldownTimer = setInterval(tick, 1000);
    }

    /* =====================================================
       SYNC STATUS (FETCH LIVE MYSQL DATA)
    ===================================================== */
    let lastSync = null, syncState = 'idle', firstLoad = true;

    function relTime(ts) {
        const s = Math.round((Date.now() - ts) / 1000);
        if (s < 45) return 'just now';
        const m = Math.round(s / 60);
        return m < 60 ? `${m} min ago` : `${Math.round(m / 60)} h ago`;
    }

    function renderSync() {
        const box = $('syncStatus'), text = $('syncText');
        if (!box || !text) return;
        box.classList.toggle('loading', syncState === 'loading');
        box.classList.toggle('error', syncState === 'error');
        text.textContent = syncState === 'loading' ? 'Syncing...'
            : syncState === 'error' ? (lastSync ? `Sync failed (last synced ${relTime(lastSync)})` : 'Sync failed')
            : `Synced ${lastSync ? relTime(lastSync) : 'just now'}`;
        $('syncBtn').setAttribute('aria-label', syncState === 'error' ? 'Retry sync' : 'Refresh data');
    }

    async function fetchDeptDataFromBackend() {
        if (syncState === 'loading') return;
        syncState = 'loading'; renderSync();
        if (firstLoad) renderSkeleton();
        try {
            if (!firstLoad) {
                const res = await fetch(ROUTES.dashboardData, {
                    credentials: 'same-origin',
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                });
                if (!res.ok) throw new Error('HTTP ' + res.status);
                const fresh = await res.json();
                seed = Object.assign(seed, fresh);
                DEPT_NAME = seed.deptName || DEPT_NAME;
                staffList = Array.isArray(seed.staffList) ? seed.staffList : [];
                LAST_QUARTER_AVG = Number(seed.lastQuarterAvg || LAST_QUARTER_AVG);
                $('deptName').textContent = `DEPARTMENT : ${DEPT_NAME}`;
            }

            animateRows = true;
            renderCutoff();
            renderStaffTable();

            lastSync = Date.now(); syncState = 'ok';
            if (!firstLoad) toast('Department data refreshed from database.', 'success');
        } catch (err) {
            console.error('[Dept Dashboard] Sync Error:', err);
            syncState = 'error';
            toast('Could not refresh department data. Please try again.', 'error');
        } finally {
            firstLoad = false; renderSync();
        }
    }

    /* =====================================================
       ACCESSIBILITY & INIT
    ===================================================== */
    function initDropdownA11y() {
        [['notificationBtn', 'notificationDropdown'], ['profileBtn', 'profileDropdown']].forEach(([b, d]) => {
            const btn = $(b), dd = $(d);
            if (!btn || !dd) return;
            new MutationObserver(() => btn.setAttribute('aria-expanded', dd.classList.contains('active')))
                .observe(dd, { attributes: true, attributeFilter: ['class'] });
        });
        document.addEventListener('keydown', e => {
            if (e.key === 'Escape') document.querySelectorAll('.notification-dropdown.active, .profile-dropdown.active').forEach(d => d.classList.remove('active'));
        });
    }

    document.addEventListener('DOMContentLoaded', () => {
        $('deptName').textContent = `DEPARTMENT : ${DEPT_NAME}`;

        $('syncBtn').addEventListener('click', fetchDeptDataFromBackend);
        setInterval(renderSync, 30000);
        setInterval(renderCutoff, 60000);
        document.addEventListener('visibilitychange', () => {
            if (!document.hidden && lastSync && Date.now() - lastSync > 5 * 60 * 1000) fetchDeptDataFromBackend();
        });

        document.querySelectorAll('.filter-btn').forEach(b => b.addEventListener('click', () => { filterKey = b.dataset.filter; animateRows = true; renderStaffTable(); }));
        document.querySelectorAll('.sort-btn').forEach(b => b.addEventListener('click', () => {
            const k = b.dataset.sort;
            if (sort.key !== k) sort = { key: k, dir: 'asc' }; else if (sort.dir === 'asc') sort.dir = 'desc'; else sort = { key: null, dir: 'asc' };
            animateRows = true; renderStaffTable();
        }));
        $('staffTableBody').addEventListener('click', e => {
            const b = e.target.closest('[data-edit]');
            if (b) openRatingModal(b.dataset.edit);
        });

        const dlg = $('ratingDialog');
        $('ratingForm').addEventListener('submit', saveRating);

        ['Q1', 'Q2', 'Q3', 'Q4'].forEach(code => {
            const numEl = $(`crit${code}`);
            const sldEl = $(`slider${code}`);

            sldEl.addEventListener('input', () => {
                numEl.value = sldEl.value;
                setRatingError('');
                previewTotals();
            });

            numEl.addEventListener('input', () => {
                const v = parseFloat(numEl.value);
                if (Number.isFinite(v)) {
                    sldEl.value = Math.min(10, Math.max(0, v));
                }
                setRatingError('');
                previewTotals();
            });
        });

        $('ratingCancel').addEventListener('click', () => dlg.close());
        $('ratingCloseX').addEventListener('click', () => dlg.close());
        dlg.addEventListener('click', e => { if (e.target === dlg) dlg.close(); });
        dlg.addEventListener('close', () => { activeEditId = null; });

        $('btnReminder').addEventListener('click', sendEmailReminder);

        initDropdownA11y();
        fetchDeptDataFromBackend();
    });
    </script>
 
</body>
</html>