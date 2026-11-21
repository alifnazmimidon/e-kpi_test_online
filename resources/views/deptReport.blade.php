<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Department Reports & Performance Slips - E-KPI System</title>
    <link rel="stylesheet" href="{{ asset('styledeptreport.css') }}">
</head>
<body>

    <a class="skip-link" href="#main">Skip to main content</a>

    <!-- SVG ICON SPRITE -->
    <svg width="0" height="0" style="position:absolute" aria-hidden="true" focusable="false">
        <symbol id="i-file-text" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6M16 13H8M16 17H8M10 9H8"/></symbol>
        <symbol id="i-user-check" viewBox="0 0 24 24"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="8.5" cy="7" r="4"/><path d="m17 11 2 2 4-4"/></symbol>
        <symbol id="i-trending-up" viewBox="0 0 24 24"><path d="m23 6-9.5 9.5-5-5L1 18"/><path d="M17 6h6v6"/></symbol>
        <symbol id="i-download" viewBox="0 0 24 24"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><path d="m7 10 5 5 5-5M12 15V3"/></symbol>
        <symbol id="i-eye" viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></symbol>
        <symbol id="i-printer" viewBox="0 0 24 24"><path d="M6 9V2h12v7"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></symbol>
        <symbol id="i-calendar" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></symbol>
        <symbol id="i-check" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="m9 12 2 2 4-4"/></symbol>
        <symbol id="i-clock" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></symbol>
        <symbol id="i-medal" viewBox="0 0 24 24"><path d="M7.2 15 2.7 7.1a2 2 0 0 1 .1-2.2L4.4 2.8A2 2 0 0 1 6 2h12a2 2 0 0 1 1.6.8l1.6 2.1a2 2 0 0 1 .1 2.2L16.8 15"/><path d="M11 12 5.1 2.2M13 12l5.9-9.8M8 7h8"/><circle cx="12" cy="17" r="5"/></symbol>
        <symbol id="i-user" viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></symbol>
        <symbol id="i-settings" viewBox="0 0 24 24"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"/></symbol>
        <symbol id="i-logout" viewBox="0 0 24 24"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4M16 17l5-5-5-5M21 12H9"/></symbol>
        <symbol id="i-x" viewBox="0 0 24 24"><path d="M18 6 6 18M6 6l12 12"/></symbol>
        <symbol id="i-spreadsheet" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6M8 13h8M8 17h8M12 9v10"/></symbol>
        <symbol id="i-bar-chart" viewBox="0 0 24 24"><path d="M3 3v18h18"/><path d="M8 17v-4M13 17V8M18 17v-7"/></symbol>
        <symbol id="i-inbox" viewBox="0 0 24 24"><path d="M22 12h-6l-2 3h-4l-2-3H2"/><path d="M5.5 5.1 2 12v6a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-6l-3.5-6.9A2 2 0 0 0 16.7 4H7.3a2 2 0 0 0-1.8 1.1z"/></symbol>
        <symbol id="i-refresh" viewBox="0 0 24 24"><path d="M21 12a9 9 0 0 0-15-6.7L3 8"/><path d="M3 3v5h5M3 12a9 9 0 0 0 15 6.7L21 16"/><path d="M16 16h5v5"/></symbol>
        <symbol id="i-chev-l" viewBox="0 0 24 24"><path d="m15 18-6-6 6-6"/></symbol>
        <symbol id="i-chev-r" viewBox="0 0 24 24"><path d="m9 18 6-6-6-6"/></symbol>
        <symbol id="i-alert-circle" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M12 8v4M12 16h.01"/></symbol>
        <symbol id="i-info" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M12 16v-4M12 8h.01"/></symbol>
        <symbol id="i-loader" viewBox="0 0 24 24"><path d="M21 12a9 9 0 1 1-6.2-8.6"/></symbol>
        <symbol id="i-send" viewBox="0 0 24 24"><path d="m22 2-7 20-4-9-9-4Z"/><path d="M22 2 11 13"/></symbol>
        <symbol id="i-activity" viewBox="0 0 24 24"><path d="M22 12h-4l-3 9L9 3l-3 9H2"/></symbol>
    </svg>

    <!-- HEADER -->
    <header class="header">
        <div class="header-left">
            <span class="system-title">E-KPI SYSTEM</span>
            <div class="header-line" aria-hidden="true"></div>
            <div class="ilm-logo"><img src="{{ asset('assets/ILMgroups-logo-4.png') }}" alt="ILM Groups logo" onerror="this.style.display='none'"></div>
            <span class="company-name">INFINITE LOOP MEDIA</span>
        </div>

        <div class="header-right">
            <div class="notification-wrapper">
                <button type="button" class="header-icon" id="notificationBtn" aria-label="Notifications" aria-haspopup="true" aria-expanded="false" aria-controls="notificationDropdown">
                    <div class="bellIcon">
                        <img src="{{ asset('assets/bellwhite.png') }}" alt="" class="img-base" onerror="this.style.display='none'">
                        <img src="{{ asset('assets/bellblack.png') }}" alt="" class="img-hover" onerror="this.style.display='none'">
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

            @php
                $navUser     = Auth::user();
                $navRawPhoto = $navUser->profile_photo ?? '';
                $navPhotoUrl = '';
                if (!empty($navRawPhoto)) {
                    $cleanNav = ltrim($navRawPhoto, '/');
                    $navPhotoUrl = (str_starts_with($navRawPhoto, 'http://') || str_starts_with($navRawPhoto, 'https://') || str_starts_with($navRawPhoto, '/'))
                        ? $navRawPhoto
                        : (str_starts_with($cleanNav, 'uploads/') || str_starts_with($cleanNav, 'storage/')
                            ? asset($cleanNav)
                            : asset('storage/' . $cleanNav));
                }
            @endphp

            <div class="profile-wrapper">
                <button type="button" class="header-icon profile-btn" id="profileBtn" aria-label="User profile menu" aria-haspopup="true" aria-expanded="false" aria-controls="profileDropdown">
                    <div class="profile-avatar-wrapper" id="headerAvatarContainer">
                        <svg id="defaultAvatarSvg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#ffffff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" style="{{ $navPhotoUrl ? 'display: none;' : '' }}">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                            <circle cx="12" cy="7" r="4"></circle>
                        </svg>
                        <img @if($navPhotoUrl) src="{{ $navPhotoUrl }}" @endif id="headerUserProfilePic" alt="" class="profile-avatar-img" style="{{ $navPhotoUrl ? 'display: block;' : 'display: none;' }}" onerror="showAvatarFallback()">
                    </div>
                </button>

                <div class="profile-dropdown" id="profileDropdown">
                    <div class="profile-dropdown-header">
                        <span class="user-name" id="dropdownUserName">{{ $navUser->full_name ?? 'Team Leader' }}</span>
                        <span class="user-role" id="dropdownUserRole">{{ $navUser->designation ?? (($seedData['deptLabel'] ?? 'Department') . ' Team Lead') }}</span>
                    </div>
                    <ul class="profile-dropdown-menu">
                        <li><a href="{{ route('worker.dashboard') }}"><svg class="icon" aria-hidden="true" style="font-size:15px"><use href="#i-user"/></svg>View Profile</a></li>
                        <li><a href="{{ route('leader.dashboard') }}"><svg class="icon" aria-hidden="true" style="font-size:15px"><use href="#i-settings"/></svg>Dashboard</a></li>
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

    <!-- NAVIGATION BAR -->
    <nav class="navigation reveal" style="--i:1" aria-label="Main navigation">
        <a href="{{ route('leader.dashboard') }}" class="nav-link {{ request()->routeIs('leader.dashboard') ? 'active' : '' }}">Dashboard</a>
        <a href="{{ route('leader.kpi') }}" class="nav-link {{ request()->routeIs('leader.kpi') ? 'active' : '' }}">Input Rating</a>
        <a href="{{ route('leader.team.status') }}" class="nav-link {{ request()->routeIs('leader.team.status') ? 'active' : '' }}">Team Status</a>
        <a href="{{ route('leader.reports') }}" class="nav-link active" aria-current="page">Reports</a>

        <div class="sync-status reveal" style="--i:1" id="syncStatus">
            <span class="sync-dot" aria-hidden="true"></span>
            <span id="syncText" role="status" aria-live="polite">Synced just now</span>
            <button type="button" class="sync-btn" id="syncBtn" aria-label="Refresh data" title="Refresh data">
                <svg class="icon" id="syncIcon" aria-hidden="true"><use href="#i-refresh"/></svg>
            </button>
        </div>
    </nav>

    <!-- MAIN REPORT PAGE CONTAINER -->
    <main class="reports-container" id="main">

        <!-- TITLE BAR & MONTH FILTER -->
        <section class="page-title-card reveal" style="--i:2">
            <div class="title-meta-group">
                <h1 class="main-page-title">DEPARTMENT REPORTS & PERFORMANCE SLIPS</h1>
                <p class="title-subtext">{{ $seedData['deptLabel'] ?? 'Webtech' }} Department - Monthly Performance Slips, Aggregate Reports & HR Submissions</p>
            </div>

            <div class="month-filter-wrap">
                <label for="monthSelect"><svg class="icon" aria-hidden="true"><use href="#i-calendar"/></svg>FILTER MONTH :</label>
                <div class="month-switch">
                    <button type="button" class="month-nav" id="monthPrev" aria-label="Previous month"><svg class="icon" aria-hidden="true"><use href="#i-chev-l"/></svg></button>
                    <select id="monthSelect" class="month-dropdown"></select>
                    <button type="button" class="month-nav" id="monthNext" aria-label="Next month"><svg class="icon" aria-hidden="true"><use href="#i-chev-r"/></svg></button>
                </div>
            </div>
        </section>

        <!-- 3 CORE REPORT CARDS -->
        <section class="cards-grid reveal" style="--i:3">

            <!-- CARD 1 -->
            <article class="report-card primary-accent">
                <div>
                    <div class="card-badge-row">
                        <span class="card-chip chip-red"><svg class="icon" aria-hidden="true"><use href="#i-file-text"/></svg>Executive Report</span>
                        <span class="status-pill green" id="kpiReportStatus"></span>
                    </div>
                    <div class="card-header">
                        <h2>DEPARTMENT KPI PERFORMANCE REPORT</h2>
                        <p class="card-desc">Comprehensive monthly evaluation summary covering overall department average score, top performer, and evaluation completion rate.</p>
                    </div>
                    <div class="metrics-stack" id="card1Metrics">
                        <div class="metric-item"><span class="metric-label">Dept Average Score</span><strong class="metric-val text-red" id="valDeptAvg">--</strong></div>
                        <div class="metric-item"><span class="metric-label">Evaluation Rate</span><strong class="metric-val" id="valEvalRate">--</strong></div>
                        <div class="rate-bar" id="evalRateBar" aria-hidden="true"><i></i></div>
                        <div class="metric-item"><span class="metric-label">Top Performer</span><strong class="metric-val" id="valTopPerformer">--</strong></div>
                    </div>
                </div>
                <div class="card-actions">
                    <button type="button" class="btn-primary" id="btnDownloadDeptPdf">
                        <svg class="icon" aria-hidden="true"><use href="#i-printer"/></svg><span>Print / Save PDF Report</span>
                    </button>
                    <button type="button" class="btn-secondary" id="btnPreviewDeptAnalytics">
                        <svg class="icon" aria-hidden="true"><use href="#i-bar-chart"/></svg><span>Preview Analytics</span>
                    </button>
                </div>
            </article>

            <!-- CARD 2 -->
            <article class="report-card dark-accent">
                <div>
                    <div class="card-badge-row">
                        <span class="card-chip chip-dark"><svg class="icon" aria-hidden="true"><use href="#i-user-check"/></svg>Confidential Slips</span>
                        <span class="status-pill green" id="slipsReadyStatus"></span>
                    </div>
                    <div class="card-header">
                        <h2>INDIVIDUAL PERFORMANCE SLIPS (STAFF SLIPS)</h2>
                        <p class="card-desc">Generate, inspect, and print official performance slips for team members containing full score breakdown and final tier.</p>
                    </div>
                    <div class="staff-slip-selector-box">
                        <label for="staffSlipSelect">SELECT TEAM MEMBER :</label>
                        <select id="staffSlipSelect" class="staff-dropdown"></select>
                    </div>
                </div>
                <div class="card-actions duo-actions">
                    <button type="button" class="btn-primary" id="btnInspectSlip">
                        <svg class="icon" aria-hidden="true"><use href="#i-eye"/></svg><span>Inspect & Print Slip</span>
                    </button>
                    <button type="button" class="btn-secondary" id="btnDownloadAllSlips">
                        <svg class="icon" aria-hidden="true"><use href="#i-printer"/></svg><span>Print / Save All Slips (PDF)</span>
                    </button>
                </div>
            </article>

            <!-- CARD 3 -->
            <article class="report-card success-accent">
                <div>
                    <div class="card-badge-row">
                        <span class="card-chip chip-green"><svg class="icon" aria-hidden="true"><use href="#i-trending-up"/></svg>Audit & Data</span>
                        <span class="status-pill dark" id="hrSubmittedPill"></span>
                    </div>
                    <div class="card-header">
                        <h2>HISTORICAL TREND & HR AUDIT LOG</h2>
                        <p class="card-desc">Month-on-month comparative analytics, historical performance tracking, and the official HR compliance audit trail.</p>
                    </div>
                    <div class="metrics-stack" id="card3Metrics">
                        <div class="metric-item"><span class="metric-label">MoM Growth Rate</span><strong class="metric-val" id="valMomGrowth">--</strong></div>
                        <div class="metric-item"><span class="metric-label">HR Compliance Status</span><strong class="metric-val" id="valCompliance">--</strong></div>
                        <div class="metric-item"><span class="metric-label">Submission Timestamp</span><strong class="metric-val" id="valSubmissionTime">--</strong></div>
                    </div>
                </div>
                <div class="card-actions">
                    <button type="button" class="btn-primary" id="btnSubmitHR" hidden>
                        <svg class="icon btn-icon" aria-hidden="true"><use href="#i-send"/></svg>
                        <svg class="icon btn-spinner" aria-hidden="true"><use href="#i-loader"/></svg>
                        <span>Submit to HR</span>
                    </button>
                    <button type="button" class="btn-primary" id="btnExportExcel">
                        <svg class="icon" aria-hidden="true"><use href="#i-spreadsheet"/></svg><span>Export for Excel (.CSV)</span>
                    </button>
                    <button type="button" class="btn-secondary" id="btnViewAuditLog">
                        <svg class="icon" aria-hidden="true"><use href="#i-clock"/></svg><span>View HR Audit Log</span>
                    </button>
                </div>
            </article>

        </section>
    </main>

    <!-- PERFORMANCE SLIP MODAL -->
    <dialog class="slip-dialog" id="slipModal" aria-labelledby="slipTitle">
        <div class="slip-modal-header">
            <div class="modal-title-wrap"><svg class="icon" aria-hidden="true"><use href="#i-file-text"/></svg><h3 id="slipTitle">OFFICIAL PERFORMANCE SLIP</h3></div>
            <button type="button" class="dialog-close" id="closeSlipModal" aria-label="Close modal"><svg class="icon" aria-hidden="true"><use href="#i-x"/></svg></button>
        </div>
        <div class="slip-printable-area" id="printableSlipArea"></div>
        <div class="slip-modal-footer">
            <div class="slip-nav">
                <button type="button" id="slipPrev" aria-label="Previous team member"><svg class="icon" aria-hidden="true"><use href="#i-chev-l"/></svg></button>
                <span class="pos" id="slipPos"></span>
                <button type="button" id="slipNext" aria-label="Next team member"><svg class="icon" aria-hidden="true"><use href="#i-chev-r"/></svg></button>
            </div>
            <button type="button" class="btn-secondary" id="btnPrintSlipModal"><svg class="icon" aria-hidden="true"><use href="#i-printer"/></svg><span>Print Slip</span></button>
            <button type="button" class="btn-primary" id="btnDownloadSinglePdf"><svg class="icon" aria-hidden="true"><use href="#i-download"/></svg><span>Save as PDF</span></button>
        </div>
    </dialog>

    <!-- HR AUDIT TRAIL MODAL -->
    <dialog class="audit-dialog" id="auditModal" aria-labelledby="auditTitle">
        <div class="slip-modal-header">
            <div class="modal-title-wrap"><svg class="icon" aria-hidden="true"><use href="#i-clock"/></svg><h3 id="auditTitle">HR SUBMISSION AUDIT TRAIL - <span id="auditMonth"></span></h3></div>
            <button type="button" class="dialog-close" id="closeAuditModal" aria-label="Close modal"><svg class="icon" aria-hidden="true"><use href="#i-x"/></svg></button>
        </div>
        <div class="audit-body"><ul class="audit-timeline" id="auditTimeline"></ul></div>
        <div class="slip-modal-footer">
            <button type="button" class="btn-secondary" id="btnExportAudit"><svg class="icon" aria-hidden="true"><use href="#i-download"/></svg><span>Export log (.CSV)</span></button>
        </div>
    </dialog>

    <!-- ANALYTICS MODAL -->
    <dialog class="analytics-dialog" id="analyticsModal" aria-labelledby="analyticsTitle">
        <div class="slip-modal-header">
            <div class="modal-title-wrap"><svg class="icon" aria-hidden="true"><use href="#i-bar-chart"/></svg><h3 id="analyticsTitle">DEPARTMENT ANALYTICS - <span id="anMonthLabel"></span></h3></div>
            <button type="button" class="dialog-close" id="closeAnalytics" aria-label="Close modal"><svg class="icon" aria-hidden="true"><use href="#i-x"/></svg></button>
        </div>
        <div class="analytics-body">
            <div class="chart-card">
                <h4><svg class="icon" aria-hidden="true"><use href="#i-trending-up"/></svg>Department average by month</h4>
                <div id="trendHost"></div>
            </div>
            <div class="chart-card">
                <h4><svg class="icon" aria-hidden="true"><use href="#i-user-check"/></svg>Score composition per staff</h4>
                <div class="stack-rows" id="stackRows"></div>
                <div class="legend" id="stackLegend"></div>
            </div>
        </div>
    </dialog>

    <!-- CONFIRM DIALOG -->
    <dialog class="confirm-dialog" id="confirmDialog" aria-labelledby="confirmTitle" aria-describedby="confirmDesc">
        <form method="dialog">
            <div class="confirm-body">
                <div class="confirm-icon"><svg class="icon" aria-hidden="true"><use href="#i-send"/></svg></div>
                <div><h3 id="confirmTitle"></h3><p id="confirmDesc"></p></div>
            </div>
            <div class="slip-modal-footer">
                <button type="submit" value="cancel" class="btn-modal-secondary">Cancel</button>
                <button type="submit" value="confirm" class="btn-modal-primary" id="confirmOk">Confirm</button>
            </div>
        </form>
    </dialog>

    <!-- printed content is injected here (slip / all slips / department report) -->
    <div id="printSheet" aria-hidden="true"></div>

    <div class="toast-region" id="toastRegion" aria-live="polite"></div>

    <script>
    (function () {
        const SEED_PAYLOAD       = @json($seedData);
        const DATA_ENDPOINT      = "{{ route('leader.reports.data') }}";
        const SUBMIT_HR_ENDPOINT = "{{ route('leader.reports.submit') }}";
        const CSRF_TOKEN         = "{{ csrf_token() }}";

        const $ = id => document.getElementById(id);
        const prefersReduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
        const icon = (name, cls = '') => `<svg class="icon ${cls}" aria-hidden="true"><use href="#i-${name}"/></svg>`;
        const round1 = n => Math.round(n * 10) / 10;
        const pad = n => String(n).padStart(2, '0');
        const fmtDate = d => `${pad(d.getDate())}/${pad(d.getMonth() + 1)}/${d.getFullYear()}`;
        const fmtTime = d => { const h = d.getHours(); return `${pad(h % 12 || 12)}:${pad(d.getMinutes())} ${h < 12 ? 'AM' : 'PM'}`; };
        const fmtStamp = d => `${fmtDate(d)} - ${fmtTime(d)}`;
        const swap = el => { if (!el) return; el.classList.remove('swap'); void el.offsetWidth; el.classList.add('swap'); };

       function esc(s) {
            if (s === null || s === undefined) return '';
            const d = document.createElement('div');
            d.textContent = String(s);
            return d.innerHTML
                .replace(/"/g, '\u0026quot;')
                .replace(/'/g, '\u0026#039;');
        }
        

        function animateNumber(el, to, fmt) {
            if (!el) return;
            const from = parseFloat(el.dataset.val);
            el.dataset.val = to;
            if (prefersReduced || !Number.isFinite(from) || from === to) { el.textContent = fmt(to); return; }
            const start = performance.now(), dur = 800;
            const step = now => {
                const p = Math.min((now - start) / dur, 1);
                el.textContent = fmt(from + (to - from) * (1 - Math.pow(1 - p, 3)));
                if (p < 1) requestAnimationFrame(step);
            };
            requestAnimationFrame(step);
        }

        function toast(text, type = 'info', duration = 3500) {
            const region = $('toastRegion');
            if (!region) return;
            const el = document.createElement('div');
            el.className = 'toast ' + type;
            el.innerHTML = `${icon(type === 'success' ? 'check' : type === 'error' ? 'alert-circle' : 'info')}<span>${esc(text)}</span>
                <button type="button" class="toast-close" aria-label="Dismiss notification">${icon('x')}</button>`;
            const close = () => { el.classList.add('out'); setTimeout(() => el.remove(), prefersReduced ? 0 : 220); };
            el.querySelector('.toast-close').addEventListener('click', close);
            region.appendChild(el);
            setTimeout(close, duration);
        }

        function confirmDialog({ title, desc, ok = 'Confirm' }) {
            const dlg = $('confirmDialog');
            if (typeof dlg.showModal !== 'function') return Promise.resolve(window.confirm(`${title}\n\n${desc}`));
            $('confirmTitle').textContent = title;
            $('confirmDesc').textContent = desc;
            $('confirmOk').textContent = ok;
            return new Promise(resolve => {
                dlg.returnValue = '';
                dlg.addEventListener('close', () => resolve(dlg.returnValue === 'confirm'), { once: true });
                dlg.showModal();
            });
        }

        function downloadFile(filename, text, type = 'text/csv;charset=utf-8') {
            const url = URL.createObjectURL(new Blob(['\ufeff' + text], { type }));
            const a = document.createElement('a');
            a.href = url; a.download = filename; document.body.appendChild(a); a.click(); a.remove();
            setTimeout(() => URL.revokeObjectURL(url), 1000);
        }
        const csvLine = arr => arr.map(v => `"${String(v ?? '').split('"').join('""')}"`).join(',');

        window.showAvatarFallback = function () {
            const img = $('headerUserProfilePic'), svg = $('defaultAvatarSvg');
            if (img) img.style.display = 'none';
            if (svg) svg.style.display = '';
        };

        /* =====================================================
           DATA STATE FROM BACKEND
        ===================================================== */
        let MONTH_KEYS    = Array.isArray(SEED_PAYLOAD.monthKeys) ? SEED_PAYLOAD.monthKeys : [];
        let MONTHS        = SEED_PAYLOAD.months || {};
        const DEPT        = SEED_PAYLOAD.deptName || 'WEBTECH';
        const LEADER_NAME = SEED_PAYLOAD.leaderName || 'Team Leader';
        let DB_THRESHOLDS = SEED_PAYLOAD.thresholds || { gold: 90, silver: 85, bronze: 80 };

        let monthKey = SEED_PAYLOAD.defaultMonth || MONTH_KEYS[MONTH_KEYS.length - 1] || '';
        let slipStaffId = '', slipOrder = [];

        function getThresholds() {
        const t = DB_THRESHOLDS;
        if (t && t.gold > t.silver && t.silver > t.bronze && t.bronze >= 0 && t.gold <= 100) return t;
        return { gold: 90, silver: 85, bronze: 80 };
    }

        function tierOf(total) {
            if (total === null) return { cls: 'pending', label: 'PENDING', short: 'Pending' };
            const t = getThresholds();
            return total >= t.gold ? { cls: 'gold', label: 'GOLD QUALIFIER', short: 'Gold' }
                 : total >= t.silver ? { cls: 'silver', label: 'SILVER QUALIFIER', short: 'Silver' }
                 : total >= t.bronze ? { cls: 'bronze', label: 'BRONZE QUALIFIER', short: 'Bronze' }
                 : { cls: 'none', label: 'BELOW TARGET', short: 'Below target' };
        }

        function stats(key) {
            const m = MONTHS[key] || { title: key, staff: [], submittedAt: null };
            const rows = (m.staff || []).map(s => {
                const pending = s.leader === null || s.leader === undefined;
                const task = Number(s.task || 0);
                const att = Number(s.att || 0);
                const leader = pending ? null : Number(s.leader);
                const total = pending ? null : round1(task + att + leader);
                return { ...s, id: String(s.id), task, att, leader, pending, total, tier: tierOf(total) };
            });
            const done = rows.filter(r => !r.pending);
            const avg = done.length ? round1(done.reduce((a, r) => a + r.total, 0) / done.length) : null;
            const complete = rows.length > 0 && done.length === rows.length;
            const top = [...done].sort((a, b) => b.total - a.total)[0] || null;
            const idx = MONTH_KEYS.indexOf(key), prevKey = idx > 0 ? MONTH_KEYS[idx - 1] : null;
            const prevAvg = prevKey ? stats0(prevKey) : null;
            const mom = complete && avg !== null && prevAvg ? round1((avg - prevAvg) / prevAvg * 100) : null;
            return { m, rows, done, avg, complete, top, mom, submitted: m.submittedAt ? new Date(m.submittedAt) : null };
        }

        function stats0(key) {
            const m = MONTHS[key];
            if (!m || !Array.isArray(m.staff)) return null;
            const done = m.staff.filter(s => s.leader !== null && s.leader !== undefined);
            return done.length ? done.reduce((a, s) => a + Number(s.task || 0) + Number(s.att || 0) + Number(s.leader), 0) / done.length : null;
        }

        /* =====================================================
           RENDER: month, cards, selectors
        ===================================================== */
        function setPill(el, cls, iconName, text) {
            if (!el) return;
            el.className = 'status-pill ' + cls;
            el.innerHTML = icon(iconName) + esc(text);
        }

        function populateMonthSelect() {
            const sel = $('monthSelect');
            if (!sel) return;
            sel.innerHTML = MONTH_KEYS.map(k => `<option value="${esc(k)}">${esc(MONTHS[k]?.title || k)}</option>`).join('');
            if (monthKey && MONTHS[monthKey]) sel.value = monthKey;
        }

        function renderMonth() {
            if (!monthKey || !MONTHS[monthKey]) return;
            const S = stats(monthKey), n = S.rows.length, idx = MONTH_KEYS.indexOf(monthKey);
            $('monthSelect').value = monthKey;
            $('monthPrev').disabled = idx <= 0;
            $('monthNext').disabled = idx >= MONTH_KEYS.length - 1;

            // Card 1
            animateNumber($('valDeptAvg'), S.avg ?? 0, v => S.avg === null ? '--' : `${v.toFixed(1)}%${S.complete ? '' : ' (partial)'}`);
            $('valEvalRate').textContent = n > 0 ? `${Math.round(S.done.length / n * 100)}% (${S.done.length}/${n})` : '0% (0/0)';
            $('evalRateBar').classList.toggle('partial', !S.complete);
            $('evalRateBar').firstElementChild.style.setProperty('--p', n > 0 ? S.done.length / n : 0);
            $('valTopPerformer').textContent = S.top ? `${S.top.name} (${S.top.total.toFixed(1)}%)` : '--';
            S.complete ? setPill($('kpiReportStatus'), 'green', 'check', 'READY') : setPill($('kpiReportStatus'), 'amber', 'clock', 'IN PROGRESS');

            // Card 2
            S.complete ? setPill($('slipsReadyStatus'), 'green', 'check', `${S.done.length} SLIPS READY`) : setPill($('slipsReadyStatus'), 'amber', 'clock', `${S.done.length} OF ${n} READY`);
            const sel = $('staffSlipSelect'), keep = String(sel.value || '');
            sel.innerHTML = S.rows.length
                ? S.rows.map(r => `<option value="${esc(r.id)}">${esc(r.name)} (${r.pending ? 'rating pending' : `${r.total.toFixed(1)}% - ${r.tier.short}`})</option>`).join('')
                : '<option value="">No team members found</option>';
            if (keep && S.rows.some(r => String(r.id) === keep)) sel.value = keep;
            $('btnInspectSlip').disabled = S.rows.length === 0;

            // Card 3
            const mom = $('valMomGrowth');
            mom.textContent = S.mom === null ? (idx === 0 ? 'N/A' : 'Pending') : `${S.mom > 0 ? '+' : S.mom < 0 ? '−' : ''}${Math.abs(S.mom).toFixed(1)}%`;
            mom.className = 'metric-val ' + (S.mom === null ? '' : S.mom >= 0 ? 'text-green' : 'text-red');
            const comp = $('valCompliance');
            comp.textContent = S.submitted ? 'Compliant' : S.complete ? 'Ready to submit' : 'In Review';
            comp.className = 'metric-val ' + (S.submitted ? 'text-green' : 'text-amber');
            $('valSubmissionTime').textContent = S.submitted ? fmtStamp(S.submitted) : 'Pending Final Submission';
            S.submitted ? setPill($('hrSubmittedPill'), 'dark', 'check', 'SUBMITTED TO HR') : setPill($('hrSubmittedPill'), 'amber', 'clock', 'NOT SUBMITTED');
            $('btnSubmitHR').hidden = !(S.complete && !S.submitted);

            $('btnDownloadAllSlips').disabled = S.done.length === 0;
            ['card1Metrics', 'card3Metrics'].forEach(id => swap($(id)));
        }

        /* =====================================================
           SLIP (modal + print share one template)
        ===================================================== */
        function slipHTML(S, r) {
            const ref = `SLP-${S.m.title.slice(0, 3)}${S.m.title.slice(-2)}-${String(r.empId).slice(-4)}`;
            const row = (label, w, v, max) => `<tr><td>${label}${v === null ? '' : `<span class="slip-bar"><i style="--p:${Math.min(v / max, 1)}"></i></span>`}</td><td class="text-center">${w}</td><td class="text-right">${v === null ? 'PENDING' : `${v.toFixed(1)} / ${max.toFixed(1)}`}</td></tr>`;
            return `<div class="slip">
                <div class="slip-company-header">
                    <div class="slip-brand"><h2>INFINITE LOOP MEDIA</h2><span>E-KPI PERFORMANCE SLIP - ${esc(S.m.title)}</span></div>
                    <div class="slip-badge-tag ${r.tier.cls}">${r.tier.cls === 'pending' ? icon('clock') : r.tier.cls === 'none' ? '' : icon('medal')}${r.tier.label}</div>
                </div>
                ${r.pending ? `<div class="slip-provisional">${icon('alert-circle')}Provisional slip - the leader rating (40%) has not been submitted yet.</div>` : ''}
                <div class="slip-staff-meta">
                    <div class="meta-col"><span>Staff Name: <strong>${esc(r.name)}</strong></span><span>Staff ID: <strong>${esc(r.empId)}</strong></span></div>
                    <div class="meta-col text-right"><span>Department: <strong>${esc(DEPT)}</strong></span><span>Issued Date: <strong>${S.submitted ? fmtDate(S.submitted) : 'Not issued'}</strong></span></div>
                </div>
                <table class="slip-breakdown-table">
                    <thead><tr><th>EVALUATION COMPONENT</th><th class="text-center">WEIGHTAGE</th><th class="text-right">SCORE ACHIEVED</th></tr></thead>
                    <tbody>
                        ${row('Automated Task Output Score', '40.0%', r.task, 40)}
                        ${row('Attendance & Punctuality Score', '20.0%', r.att, 20)}
                        ${row('Leader Quality & Initiative Rating', '40.0%', r.leader, 40)}
                        <tr class="total-row"><td>OVERALL TOTAL SCORE</td><td class="text-center">100.0%</td><td class="text-right">${r.pending ? '--' : r.total.toFixed(1) + '%'}</td></tr>
                    </tbody>
                </table>
                <div class="slip-footer-notes"><p>Ref: ${esc(ref)}. This slip is computer generated and officially recognized by Infinite Loop Media HR Department.</p></div>
            </div>`;
        }

        function renderSlip(animate = false) {
            const S = stats(monthKey), r = S.rows.find(x => String(x.id) === String(slipStaffId));
            if (!r) return;
            $('printableSlipArea').innerHTML = slipHTML(S, r);
            if (animate) swap($('printableSlipArea'));
            const pos = slipOrder.indexOf(String(r.id));
            $('slipPos').textContent = `${pos + 1} / ${slipOrder.length}`;
            $('slipPrev').disabled = pos <= 0;
            $('slipNext').disabled = pos >= slipOrder.length - 1;
            ['btnPrintSlipModal', 'btnDownloadSinglePdf'].forEach(id => {
                $(id).disabled = r.pending;
                $(id).title = r.pending ? 'Available once the leader rating is submitted' : '';
            });
        }

        function openSlipModal() {
            const S = stats(monthKey);
            slipStaffId = String($('staffSlipSelect').value || '');
            slipOrder = S.rows.map(r => String(r.id));
            if (!S.rows.some(r => String(r.id) === slipStaffId)) return;
            renderSlip();
            const m = $('slipModal');
            if (typeof m.showModal === 'function') m.showModal();
        }

        function stepSlip(d) {
            const p = slipOrder.indexOf(String(slipStaffId)) + d;
            if (p >= 0 && p < slipOrder.length) {
                slipStaffId = slipOrder[p];
                $('staffSlipSelect').value = slipStaffId;
                renderSlip(true);
            }
        }

        /* =====================================================
           PRINT (browser "Save as PDF")
        ===================================================== */
        function printDocument(html, fileTitle) {
            const sheet = $('printSheet'), prev = document.title;
            sheet.innerHTML = html;
            document.title = fileTitle;
            const done = () => { document.title = prev; sheet.innerHTML = ''; window.removeEventListener('afterprint', done); };
            window.addEventListener('afterprint', done);
            setTimeout(() => window.print(), 60);
        }
        const fileSafe = s => String(s).replace(/\s+/g, '_');

        function printSlip() {
            const S = stats(monthKey), r = S.rows.find(x => String(x.id) === String(slipStaffId));
            if (!r || r.pending) return;
            printDocument(`<div class="print-page">${slipHTML(S, r)}</div>`, `Slip_${fileSafe(S.m.title)}_${fileSafe(r.name)}`);
        }

        function printAllSlips() {
            const S = stats(monthKey);
            if (!S.done.length) { toast('No completed evaluations to print for this month.', 'error'); return; }
            const skipped = S.rows.length - S.done.length;
            printDocument(S.done.map(r => `<div class="print-page">${slipHTML(S, r)}</div>`).join(''), `All_Slips_${fileSafe(S.m.title)}`);
            if (skipped) toast(`${skipped} provisional ${skipped === 1 ? 'slip was' : 'slips were'} skipped (rating pending).`, 'info', 5000);
        }

        function printDeptReport() {
            const S = stats(monthKey), n = S.rows.length;
            const rows = S.rows.map((r, i) => `<tr><td>${i + 1}</td><td>${esc(r.name)}</td><td>${r.task.toFixed(1)} / 40</td><td>${r.att.toFixed(1)} / 20</td><td>${r.pending ? 'PENDING' : r.leader.toFixed(1) + ' / 40'}</td><td>${r.pending ? '--' : r.total.toFixed(1) + '%'}</td><td>${r.tier.label}</td></tr>`).join('');
            printDocument(`<div class="print-page print-report">
                <h1>Department KPI Performance Report - ${esc(DEPT)}</h1>
                <div class="sub">${esc(S.m.title)} ${S.complete ? '' : '(PARTIAL - evaluations still in progress)'} · Generated ${fmtStamp(new Date())}</div>
                <div class="print-kpis">
                    <div><span>Dept Average Score</span><strong>${S.avg === null ? '--' : S.avg.toFixed(1) + '%'}</strong></div>
                    <div><span>Evaluation Rate</span><strong>${n > 0 ? Math.round(S.done.length / n * 100) : 0}% (${S.done.length}/${n})</strong></div>
                    <div><span>Top Performer</span><strong>${S.top ? esc(S.top.name) + ' (' + S.top.total.toFixed(1) + '%)' : '--'}</strong></div>
                </div>
                <table><thead><tr><th>No</th><th>Staff</th><th>Tasks</th><th>Attendance</th><th>Leader</th><th>Total</th><th>Tier</th></tr></thead><tbody>${rows}</tbody></table>
                <div class="sub">Computer generated report - Infinite Loop Media HR Department.</div></div>`, `Dept_KPI_Report_${fileSafe(S.m.title)}`);
        }

        /* =====================================================
           EXPORT, AUDIT LOG, SUBMIT TO HR (LIVE DB)
        ===================================================== */
        function exportMonthCSV() {
            const S = stats(monthKey);
            const lines = [csvLine(['Staff ID', 'Name', 'Task (/40)', 'Attendance (/20)', 'Leader Rating (/40)', 'Total (%)', 'Tier', 'Status'])]
                .concat(S.rows.map(r => csvLine([r.empId, r.name, r.task.toFixed(1), r.att.toFixed(1), r.pending ? '' : r.leader.toFixed(1), r.pending ? '' : r.total.toFixed(1), r.tier.label, r.pending ? 'PENDING' : 'COMPLETE'])));
            downloadFile(`${DEPT}_KPI_${fileSafe(S.m.title)}.csv`, lines.join('\r\n'));
            toast(`${S.m.title} dataset downloaded (opens in Excel).`, 'success');
        }

        function auditEntries(key) {
            const S = stats(key), m = S.m, pend = S.rows.filter(r => r.pending);
            const submitter = m.submittedBy || LEADER_NAME;
            const out = [];
            if (S.submitted) out.push({ tone: 'green', ic: 'check', title: 'Submitted to HR Portal', text: `All ${S.rows.length} team evaluations validated and finalized by ${submitter}.`, at: S.submitted });
            else if (S.complete) out.push({ tone: 'amber', ic: 'clock', title: 'Ready for HR submission', text: 'All evaluations are complete. Waiting for the leader to submit to HR.', at: null });
            else out.push({ tone: 'amber', ic: 'clock', title: 'Awaiting final submission', text: `${pend.length} evaluation${pend.length > 1 ? 's' : ''} still pending: ${pend.map(r => r.name).join(', ')}.`, at: null });
            out.push({ tone: 'green', ic: 'check', title: S.complete ? 'Draft Completed' : 'Draft Saved', text: `Leader rating scores saved for ${S.done.map(r => r.name).join(', ') || 'no one yet'}.`, at: m.draftAt ? new Date(m.draftAt) : null });
            out.push({ tone: 'dark', ic: 'activity', title: 'Automated System Score Sync', text: 'Attendance system & task metrics synchronized from server.', at: m.syncAt ? new Date(m.syncAt) : null });
            return out;
        }

        function openAudit() {
            if (!monthKey || !MONTHS[monthKey]) return;
            $('auditMonth').textContent = MONTHS[monthKey].title;
            $('auditTimeline').innerHTML = auditEntries(monthKey).map((e, i) => `
                <li style="--i:${i}"><span class="timeline-dot ${e.tone}">${icon(e.ic)}</span>
                    <div class="timeline-content"><strong>${esc(e.title)}</strong><p>${esc(e.text)}</p><span class="time-stamp">${e.at ? fmtStamp(e.at) : 'Not yet completed'}</span></div></li>`).join('');
            $('auditModal').showModal();
        }

        function exportAuditCSV() {
            const t = MONTHS[monthKey].title;
            downloadFile(`${DEPT}_Audit_${fileSafe(t)}.csv`, [csvLine(['Event', 'Details', 'Timestamp'])].concat(auditEntries(monthKey).map(e => csvLine([e.title, e.text, e.at ? fmtStamp(e.at) : '']))).join('\r\n'));
            toast('Audit log downloaded.', 'success');
        }

        async function submitToHR() {
            const S = stats(monthKey);
            if (!S.complete || S.submitted) return;
            const ok = await confirmDialog({
                title: `Submit ${S.m.title} to HR?`,
                desc: `All ${S.rows.length} evaluations will be finalized and sent to the HR portal. This creates an entry in the audit log.`,
                ok: 'Submit to HR'
            });
            if (!ok) return;

            const btn = $('btnSubmitHR');
            btn.classList.add('loading');
            btn.disabled = true;

            try {
                const res = await fetch(SUBMIT_HR_ENDPOINT, {
                    method: 'POST',
                    credentials: 'same-origin',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': CSRF_TOKEN,
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({ month: monthKey })
                });
                const data = await res.json();
                if (!res.ok || !data.success) {
                    throw new Error(data.message || 'Could not submit to HR.');
                }

                MONTHS[monthKey].submittedAt = data.submittedAt;
                MONTHS[monthKey].submittedBy = data.submittedBy || LEADER_NAME;
                buildNotifications();
                renderNotifications();
                toast(`${S.m.title} submitted to HR.`, 'success');
            } catch (err) {
                console.error(err);
                toast(err.message || 'Could not submit to HR. Please try again.', 'error');
            } finally {
                btn.classList.remove('loading');
                btn.disabled = false;
                renderMonth();
            }
        }

        /* =====================================================
           ANALYTICS (inline SVG trend + stacked composition bars)
        ===================================================== */
        function openAnalytics() {
            if (!monthKey || !MONTHS[monthKey] || !MONTH_KEYS.length) return;

            const S = stats(monthKey), n = MONTH_KEYS.length;
            $('anMonthLabel').textContent = S.m.title;

            const pts = MONTH_KEYS.map((k, i) => {
                const st = stats(k);
                return { k, i, title: MONTHS[k].title, avg: st.avg, partial: !st.complete, x: (i + 0.5) / n * 100 };
            });
            const line = pts.filter(p => p.avg !== null && !p.partial).map(p => `${p.x},${100 - p.avg}`).join(' ');
            $('trendHost').innerHTML = `<div class="trend-wrap" role="group" aria-label="Department average by month">
                ${[0, 25, 50, 75, 100].map(v => `<div class="trend-grid" style="top:${100 - v}%"><span>${v}</span></div>`).join('')}
                <svg class="trend-svg" viewBox="0 0 100 100" preserveAspectRatio="none" aria-hidden="true"><polyline points="${line}" fill="none" stroke="#e11d48" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" vector-effect="non-scaling-stroke"/></svg>
                ${pts.filter(p => p.avg !== null).map(p => `<span class="trend-dot ${p.partial ? 'partial' : ''} ${p.k === monthKey ? 'current' : ''}" tabindex="0" style="left:${p.x}%;top:${100 - p.avg}%;--i:${p.i}" data-tip="${esc(p.title)}: ${p.avg.toFixed(1)}%${p.partial ? ' (partial)' : ''}" aria-label="${esc(p.title)}: ${p.avg.toFixed(1)} percent${p.partial ? ', partial' : ''}"></span>`).join('')}
                <div class="trend-x">${pts.map(p => `<span class="${p.k === monthKey ? 'on' : ''}" style="left:${p.x}%">${esc(p.title.split(' ')[0].slice(0, 3))}</span>`).join('')}</div>
            </div>`;

            $('stackRows').innerHTML = S.rows.map((r, i) => `<div class="stack-row" style="--i:${i}">
                <span class="nm" title="${esc(r.name)}">${esc(r.name)}</span>
                <div class="stack-track" role="img" aria-label="${esc(r.name)}: task ${r.task}, attendance ${r.att}, leader ${r.pending ? 'pending' : r.leader}">
                    <i class="s-task" style="width:${r.task}%"></i><i class="s-att" style="width:${r.att}%"></i>${r.pending ? '' : `<i class="s-leader" style="width:${r.leader}%"></i>`}</div>
                <span class="tt">${r.pending ? '--' : r.total.toFixed(1)}</span></div>`).join('');
            $('stackLegend').innerHTML = [['#e11d48', 'Task (40)'], ['#0284c7', 'Attendance (20)'], ['#3B3B3B', 'Leader rating (40)']].map(([c, t]) => `<span style="--c:${c}">${t}</span>`).join('');
            $('analyticsModal').showModal();
        }

        /* =====================================================
           NOTIFICATIONS (derived from live DB state)
        ===================================================== */
        let notifications = [];

        function buildNotifications() {
            const items = [];
            let nid = 1;
            const latestKey = MONTH_KEYS[MONTH_KEYS.length - 1];
            if (latestKey && MONTHS[latestKey]) {
                const st = stats(latestKey);
                const pendCount = st.rows.length - st.done.length;
                if (pendCount > 0) {
                    items.push({
                        id: nid++,
                        title: `${st.m.title} Evaluations Pending`,
                        desc: `${pendCount} team member${pendCount > 1 ? 's' : ''} still awaiting leader rating.`,
                        time: 'Current Month',
                        unread: true
                    });
                } else if (!st.submitted && st.rows.length > 0) {
                    items.push({
                        id: nid++,
                        title: `${st.m.title} Slips Ready`,
                        desc: `All ${st.done.length} team slips are ready to print & submit to HR.`,
                        time: 'Ready',
                        unread: true
                    });
                } else if (st.submitted) {
                    items.push({
                        id: nid++,
                        title: `${st.m.title} Submitted to HR`,
                        desc: `Finalized on ${fmtStamp(st.submitted)}.`,
                        time: 'Completed',
                        unread: false
                    });
                }
            }
            notifications = items;
        }

        function renderNotifications() {
            const list = $('notificationList'), badge = $('notifBadge');
            if (!list || !badge) return;
            const unread = notifications.filter(n => n.unread).length;
            badge.textContent = unread;
            badge.style.display = unread ? 'inline-block' : 'none';
            list.innerHTML = notifications.length ? notifications.map(n => `
                <div class="notification-item ${n.unread ? 'unread' : ''}" data-id="${n.id}" role="button" tabindex="0">
                    <h4 class="notif-title">${esc(n.title)}</h4><p class="notif-desc">${esc(n.desc)}</p><span class="notif-time">${esc(n.time)}</span></div>`).join('')
                : `<div class="no-notifications">${icon('inbox')}<p>No notifications</p></div>`;
        }

        /* =====================================================
           SYNC STATUS (LIVE BACKEND REFRESH)
        ===================================================== */
        let lastSync = Date.now(), syncState = 'ok', spinDeg = 0;

        function relTime(ts) {
            const s = Math.round((Date.now() - ts) / 1000);
            if (s < 45) return 'just now';
            const m = Math.round(s / 60);
            return m < 60 ? `${m} min ago` : `${Math.round(m / 60)} h ago`;
        }

        function renderSync() {
            const box = $('syncStatus'), text = $('syncText'), btn = $('syncBtn');
            if (!box || !text) return;
            box.classList.toggle('loading', syncState === 'loading');
            box.classList.toggle('error', syncState === 'error');
            text.textContent = syncState === 'loading' ? 'Syncing...'
                : syncState === 'error' ? (lastSync ? `Sync failed (last synced ${relTime(lastSync)})` : 'Sync failed')
                : `Synced ${lastSync ? relTime(lastSync) : 'just now'}`;
            if (btn) btn.setAttribute('aria-label', syncState === 'error' ? 'Retry sync' : 'Refresh data');
        }

        async function fetchReportsFromBackend() {
            if (syncState === 'loading') return;
            syncState = 'loading';
            renderSync();

            const iconEl = $('syncIcon');
            if (iconEl) {
                spinDeg += 360;
                iconEl.style.transition = 'transform 0.6s ease';
                iconEl.style.transform = `rotate(${spinDeg}deg)`;
            }

            try {
                const response = await fetch(DATA_ENDPOINT, {
                    method: 'GET',
                    credentials: 'same-origin',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                if (!response.ok) throw new Error('Failed to fetch reports');
                const payload = await response.json();

                if (Array.isArray(payload.monthKeys) && payload.monthKeys.length) {
                    MONTH_KEYS = payload.monthKeys;
                }
                if (payload.months) {
                    MONTHS = payload.months;
                }
                if (payload.thresholds) {
                    DB_THRESHOLDS = payload.thresholds;
                }
                if (!MONTHS[monthKey] && MONTH_KEYS.length) {
                    monthKey = payload.defaultMonth || MONTH_KEYS[MONTH_KEYS.length - 1];
                }

                populateMonthSelect();
                renderMonth();
                buildNotifications();
                renderNotifications();
                lastSync = Date.now();
                syncState = 'ok';
                toast('Report data refreshed from database.', 'success');
            } catch (err) {
                console.error('[Reports] Sync error:', err);
                syncState = 'error';
                toast('Could not refresh the reports. Please try again.', 'error');
            } finally {
                renderSync();
            }
        }

        /* =====================================================
           INIT
        ===================================================== */
        function setMonth(key) {
            if (MONTHS[key] && key !== monthKey) {
                monthKey = key;
                renderMonth();
                toast(`Report data updated for ${MONTHS[key].title}`, 'info', 2200);
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            populateMonthSelect();
            $('monthSelect').addEventListener('change', e => setMonth(e.target.value));
            $('monthPrev').addEventListener('click', () => setMonth(MONTH_KEYS[MONTH_KEYS.indexOf(monthKey) - 1]));
            $('monthNext').addEventListener('click', () => setMonth(MONTH_KEYS[MONTH_KEYS.indexOf(monthKey) + 1]));

            $('btnDownloadDeptPdf').addEventListener('click', printDeptReport);
            $('btnPreviewDeptAnalytics').addEventListener('click', openAnalytics);
            $('btnInspectSlip').addEventListener('click', openSlipModal);
            $('btnDownloadAllSlips').addEventListener('click', printAllSlips);
            $('btnExportExcel').addEventListener('click', exportMonthCSV);
            $('btnViewAuditLog').addEventListener('click', openAudit);
            $('btnExportAudit').addEventListener('click', exportAuditCSV);
            $('btnSubmitHR').addEventListener('click', submitToHR);

            $('btnPrintSlipModal').addEventListener('click', printSlip);
            $('btnDownloadSinglePdf').addEventListener('click', printSlip);
            $('slipPrev').addEventListener('click', () => stepSlip(-1));
            $('slipNext').addEventListener('click', () => stepSlip(1));
            $('slipModal').addEventListener('keydown', e => {
                if (e.key === 'ArrowLeft') stepSlip(-1);
                if (e.key === 'ArrowRight') stepSlip(1);
            });

            [['slipModal', 'closeSlipModal'], ['auditModal', 'closeAuditModal'], ['analyticsModal', 'closeAnalytics']].forEach(([d, c]) => {
                $(c).addEventListener('click', () => $(d).close());
                $(d).addEventListener('click', e => { if (e.target === $(d)) $(d).close(); });
            });

            $('notificationList').addEventListener('click', e => {
                const it = e.target.closest('.notification-item');
                if (it) {
                    const n = notifications.find(x => x.id === Number(it.dataset.id));
                    if (n && n.unread) { n.unread = false; renderNotifications(); }
                }
            });
            $('notificationList').addEventListener('keydown', e => {
                if (e.key === 'Enter') {
                    const it = e.target.closest('.notification-item');
                    if (it) it.click();
                }
            });
            $('markAllRead').addEventListener('click', () => {
                notifications.forEach(n => { n.unread = false; });
                renderNotifications();
                toast('Notifications marked as read', 'info');
            });

            $('syncBtn').addEventListener('click', fetchReportsFromBackend);
            setInterval(renderSync, 30000);

            buildNotifications();
            renderNotifications();
            renderMonth();
            renderSync();
        });
    })();
    </script>
    <script src="{{ asset('session.js') }}?v=8"></script>
</body>
</html>