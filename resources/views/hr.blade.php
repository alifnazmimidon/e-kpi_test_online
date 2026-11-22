<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-KPI System - HR Dashboard</title>
    <!-- Aset CSS dari folder public/ -->
    <link rel="stylesheet" href="{{ asset('stylehr.css') }}">
    <link rel="stylesheet" href="{{ asset('stylereport.css') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

<body>

    <a class="skip-link" href="#main">Skip to main content</a>

    <!-- SVG ICON SPRITE (UI Baharu) -->
    <svg width="0" height="0" style="position:absolute" aria-hidden="true" focusable="false">
        <symbol id="i-refresh" viewBox="0 0 24 24"><path d="M21 12a9 9 0 0 0-15-6.7L3 8"/><path d="M3 3v5h5M3 12a9 9 0 0 0 15 6.7L21 16"/><path d="M16 16h5v5"/></symbol>
        <symbol id="i-chart" viewBox="0 0 24 24"><path d="M3 3v18h18"/><path d="M8 17v-4M13 17V8M18 17v-7"/></symbol>
        <symbol id="i-alert" viewBox="0 0 24 24"><path d="M10.3 3.9 1.8 18a2 2 0 0 0 1.7 3h17a2 2 0 0 0 1.7-3L13.7 3.9a2 2 0 0 0-3.4 0z"/><path d="M12 9v4M12 17h.01"/></symbol>
        <symbol id="i-target" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><circle cx="12" cy="12" r="6"/><circle cx="12" cy="12" r="2"/></symbol>
        <symbol id="i-trophy" viewBox="0 0 24 24"><path d="M6 9H4.5a2.5 2.5 0 0 1 0-5H6M18 9h1.5a2.5 2.5 0 0 0 0-5H18M4 22h16M10 14.7V17c0 .6-.5 1-1 1.2-1.2.6-2 2-2 3.8M14 14.7V17c0 .6.5 1 1 1.2 1.2.6 2 2 2 3.8"/><path d="M18 2H6v7a6 6 0 0 0 12 0V2z"/></symbol>
        <symbol id="i-sparkles" viewBox="0 0 24 24"><path d="m12 3-1.9 5.8a2 2 0 0 1-1.3 1.3L3 12l5.8 1.9a2 2 0 0 1 1.3 1.3L12 21l1.9-5.8a2 2 0 0 1 1.3-1.3L21 12l-5.8-1.9a2 2 0 0 1-1.3-1.3z"/></symbol>
        <symbol id="i-user" viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></symbol>
        <symbol id="i-arrow-up" viewBox="0 0 24 24"><path d="M12 19V5M5 12l7-7 7 7"/></symbol>
        <symbol id="i-arrow-down" viewBox="0 0 24 24"><path d="M12 5v14M19 12l-7 7-7-7"/></symbol>
        <symbol id="i-chev-l" viewBox="0 0 24 24"><path d="m15 18-6-6 6-6"/></symbol>
        <symbol id="i-chev-r" viewBox="0 0 24 24"><path d="m9 18 6-6-6-6"/></symbol>
        <symbol id="i-crown" viewBox="0 0 24 24"><path d="M11.6 3.3a.5.5 0 0 1 .8 0l3 5.6a1 1 0 0 0 1.5.3l4.3-3.7a.5.5 0 0 1 .8.5l-2.8 10.2a1 1 0 0 1-1 .7H5.8a1 1 0 0 1-1-.7L2 6a.5.5 0 0 1 .8-.5l4.3 3.7a1 1 0 0 0 1.5-.3z"/><path d="M5 21h14"/></symbol>
        <symbol id="i-medal" viewBox="0 0 24 24"><path d="M7.2 15 2.7 7.1a2 2 0 0 1 .1-2.2L4.4 2.8A2 2 0 0 1 6 2h12a2 2 0 0 1 1.6.8l1.6 2.1a2 2 0 0 1 .1 2.2L16.8 15"/><path d="M11 12 5.1 2.2M13 12l5.9-9.8M8 7h8"/><circle cx="12" cy="17" r="5"/></symbol>
        <symbol id="i-check" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="m9 12 2 2 4-4"/></symbol>
        <symbol id="i-clock" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></symbol>
        <symbol id="i-pause" viewBox="0 0 24 24"><rect x="6" y="4" width="4" height="16" rx="1"/><rect x="14" y="4" width="4" height="16" rx="1"/></symbol>
        <symbol id="i-play" viewBox="0 0 24 24"><path d="m6 3 14 9-14 9z"/></symbol>
        <symbol id="i-building" viewBox="0 0 24 24"><rect x="4" y="2" width="16" height="20" rx="2"/><path d="M9 22v-4h6v4M8 6h.01M12 6h.01M16 6h.01M8 10h.01M12 10h.01M16 10h.01M8 14h.01M12 14h.01M16 14h.01"/></symbol>
        <symbol id="i-user" viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></symbol>
        <symbol id="i-dashboard" viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="9" rx="1"/><rect x="14" y="3" width="7" height="5" rx="1"/><rect x="14" y="12" width="7" height="9" rx="1"/><rect x="3" y="16" width="7" height="5" rx="1"/></symbol>        
        <symbol id="i-logout" viewBox="0 0 24 24"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4M16 17l5-5-5-5M21 12H9"/></symbol>
    </svg>

    <!-- HEADER -->
    <header class="header">
        <div class="header-left">
            <div class="ilm-logo"><img src="{{ asset('assets/ILMgroups-logo-4.png') }}" alt="ILM Groups logo"></div>
            <div class="header-line" aria-hidden="true"></div>
            <span class="system-title">E-KPI SYSTEM</span>
        </div>

        <div class="header-right">
            <!-- NOTIFICATIONS -->
            <div class="notification-wrapper">
                <button class="header-icon" id="notificationBtn" aria-label="Notifications" aria-haspopup="true" aria-expanded="false" aria-controls="notificationDropdown">
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

           <!-- PROFILE -->
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
                        <span class="user-name" id="dropdownUserName">{{ $navUser->full_name ?? $navUser->name ?? 'Ahmad HR' }}</span>
                        <span class="user-role" id="dropdownUserRole">{{ ($navUser->role ?? 'HR') === 'HR' ? 'HR Administrator' : $navUser->role }}</span>
                    </div>
                    <ul class="profile-dropdown-menu">
                        <li><a href="{{ route('worker.dashboard') }}"><svg class="icon" aria-hidden="true" style="font-size:15px"><use href="#i-user"/></svg>View Profile</a></li>
                        <li><a href="{{ route('hr.dashboard') }}"><svg class="icon" aria-hidden="true" style="font-size:15px"><use href="#i-dashboard"/></svg>Dashboard</a></li>
                        <li class="divider"></li>
                        <li>
                            <a href="{{ route('logout') }}" class="logout-link"
                               onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                               <svg class="icon" aria-hidden="true" style="font-size:15px"><use href="#i-logout"/></svg>Log Out</a>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                @csrf
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </header>

    <!-- NAVIGATION -->

    <nav class="navigation reveal" style="--i:1" aria-label="Main navigation">
    <a href="{{ route('hr.dashboard') }}" class="nav-link {{ request()->routeIs('hr.dashboard') ? 'active' : '' }}">Dashboard</a>
    <a href="{{ route('hr.kpi') }}" class="nav-link {{ request()->routeIs('hr.kpi') ? 'active' : '' }}">KPI Management</a>
    <a href="{{ route('hr.report') }}" class="nav-link {{ request()->routeIs('hr.report') ? 'active' : '' }}">Report</a>
    <a href="{{ route('hr.emerleave') }}" class="nav-link {{ request()->routeIs('hr.emerleave') ? 'active' : '' }}">Emergency Leave</a>

    <!-- SYNC STATUS -->
    <div class="sync-status reveal" style="--i:1" id="syncStatus">
        <span class="sync-dot" aria-hidden="true"></span>
        <span id="syncText" role="status" aria-live="polite">Synced just now</span>
        <button type="button" class="sync-btn" id="syncBtn" aria-label="Refresh data" title="Refresh data">
            <svg class="icon" aria-hidden="true"><use href="#i-refresh"/></svg>
        </button>
    </div>
    </nav>

<!-- MAIN DASHBOARD -->
    @php
        $syncPayloadData = [
            'kpiTrend' => $kpiTrend ?? [20, 45, 60, 75],
            'departmentScores' => collect($departmentsPerformance ?? [])->map(function ($dept) {
                return [
                    'name'  => strtoupper($dept->department_name),
                    'score' => (float) $dept->avg_kpi,
                ];
            })->values(),
            'aiInsights' => $aiInsights ?? [],
        ];
    @endphp
    <main class="dashboard" id="main" data-sync-payload='@json($syncPayloadData, JSON_HEX_APOS)'>
        <section class="top-section">

            <!-- LEFT SIDE -->
            <div class="top-left">
                <!-- KPI SUMMARY CARDS -->
                <div class="summary-cards">

                    <!-- 1. AVERAGE KPI SCORE -->
                    <div class="summary-card reveal" style="--i:2" id="avgKpiCard">
                        <div class="card-header-row">
                            <h3>Average KPI Score (%)</h3>
                            <div class="card-icon blue"><svg class="icon" aria-hidden="true"><use href="#i-chart"/></svg></div>
                        </div>
                        <div class="summary-value" id="avgKpiVal">
                            {{ number_format($avgKpiScore ?? 0, 1) }}%
                        </div>
                        <div class="card-footer">
                            <span class="trend-badge {{ ($kpiTrendDiff ?? 0) >= 0 ? 'positive' : 'negative' }}" id="avgKpiTrend">
                                <span class="trend-icon"><svg class="icon" aria-hidden="true"><use href="{{ ($kpiTrendDiff ?? 0) >= 0 ? '#i-arrow-up' : '#i-arrow-down' }}"/></svg></span>
                                <span id="avgKpiTrendText">{{ ($kpiTrendDiff ?? 0) >= 0 ? '+' : '−' }}{{ number_format(abs($kpiTrendDiff ?? 0), 1) }}% vs last quarter</span>
                            </span>
                        </div>
                    </div>

                    <!-- 2. UNHEALTHY / OVERLOAD -->
                    <div class="summary-card {{ ($unhealthyStaffCount ?? 0) > 0 ? 'overload' : '' }} reveal" style="--i:3" id="overloadCard">
                        <div class="card-header-row">
                            <h3>Unhealthy / Overload</h3>
                            <div class="card-icon red"><svg class="icon" aria-hidden="true"><use href="#i-alert"/></svg></div>
                        </div>
                        <div class="summary-value danger-value" id="overloadVal">
                            {{ $unhealthyStaffCount ?? 0 }} Staff
                        </div>
                        <div class="card-footer">
                            <span class="sub-text {{ ($unhealthyStaffCount ?? 0) > 0 ? 'danger-text' : '' }}" id="overloadSubText">
                                {{ ($unhealthyStaffCount ?? 0) > 0 ? 'Need HR Attention!' : 'Normal Workload' }}
                            </span>
                        </div>
                    </div>

                    <!-- 3. TARGET ACHIEVEMENT -->
                    <div class="summary-card reveal" style="--i:4" id="targetCard">
                        <div class="card-header-row">
                            <h3>Target Achievement</h3>
                            <div class="card-icon green"><svg class="icon" aria-hidden="true"><use href="#i-target"/></svg></div>
                        </div>
                        <div class="summary-value" id="targetVal">
                            {{ number_format($targetAchievement ?? 78.0, 1) }}%
                        </div>
                        <div class="card-footer">
                            <span class="sub-text" id="targetProgressText">
                                Quarter {{ str_replace('Q', '', $selectedQuarter ?? '3') }} Progress
                            </span>
                        </div>
                    </div>

                </div>

                <!-- CHARTS -->
                <section class="charts-section reveal" style="--i:5" aria-labelledby="chartsTitle">
                    <h2 class="section-title-chart" id="chartsTitle">
                        <svg class="icon" aria-hidden="true"><use href="#i-chart"/></svg> Charts
                    </h2>

                    <div class="charts-container">
                        <!-- CHART 1: KPI TREND -->
                        <div class="chart-card">
                            <h3>Chart 1: KPI Trend (Q1-Q4)</h3>

                            <div class="line-chart">
                                <div class="y-axis" aria-hidden="true">
                                    <span style="--p:0%">100</span>
                                    <span style="--p:20%">80</span>
                                    <span style="--p:40%">60</span>
                                    <span style="--p:60%">40</span>
                                    <span style="--p:80%">20</span>
                                    <span style="--p:100%">0</span>
                                </div>

                                <div class="chart-area" id="trendChart" role="group" aria-label="KPI trend by quarter">
                                    <div class="grid-line line-100"></div>
                                    <div class="grid-line line-80"></div>
                                    <div class="grid-line line-60"></div>
                                    <div class="grid-line line-40"></div>
                                    <div class="grid-line line-20"></div>
                                    <div class="grid-line line-0"></div>

                                    <svg class="trend-svg" viewBox="0 0 400 160" preserveAspectRatio="none" aria-hidden="true">
                                        <defs>
                                            <linearGradient id="trendFill" x1="0" y1="0" x2="0" y2="1">
                                                <stop offset="0" stop-color="#e11d48" stop-opacity="0.18"/>
                                                <stop offset="1" stop-color="#e11d48" stop-opacity="0"/>
                                            </linearGradient>
                                        </defs>
                                        <path id="trendArea" d="" fill="url(#trendFill)"/>
                                        <polyline id="trendPolyline" points="" fill="none" stroke="#e11d48" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" vector-effect="non-scaling-stroke"/>
                                    </svg>

                                    <div id="trendDots"></div>

                                    <div class="x-axis" aria-hidden="true">
                                        <span style="left:5%">Q1</span>
                                        <span style="left:35%">Q2</span>
                                        <span style="left:65%">Q3</span>
                                        <span style="left:95%">Q4</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- CHART 2: DEPARTMENT BARS -->
                        <div class="chart-card">
                            <h3>Chart 2: KPI Overall By Department</h3>
                            <div class="department-bars" id="departmentBarsContainer"></div>
                        </div>
                    </div>
                </section>
            </div>

            <!-- RIGHT SIDE -->
            <div class="top-right">

                <!-- BADGE RANKING -->
                <section class="badge-section reveal" style="--i:3" aria-labelledby="badgeTitle">
                    <div class="badge-header">
                        <h3 id="badgeTitle"><svg class="icon" aria-hidden="true"><use href="#i-trophy"/></svg> Badge Ranking</h3>
                        <select id="departmentSelect" aria-label="Filter ranking by department" onchange="filterBadgeRanking(this.value)">
                            <option value="WEBTECH" {{ ($selectedDeptName ?? '') == 'WEBTECH' ? 'selected' : '' }}>WEBTECH</option>
                            <option value="R&D" {{ ($selectedDeptName ?? '') == 'R&D' ? 'selected' : '' }}>R&D</option>
                            <option value="VIDEO" {{ ($selectedDeptName ?? '') == 'VIDEO' ? 'selected' : '' }}>VIDEO PRODUCTION</option>
                            <option value="GRAPHIC" {{ ($selectedDeptName ?? '') == 'GRAPHIC' ? 'selected' : '' }}>GRAPHIC DESIGN</option>
                        </select>
                    </div>

                    <div class="badge-list" id="badgeList">
                        @forelse($badgeRankings as $index => $worker)
                        <div class="badge-user rank-{{ $index + 1 }}" style="--i:{{ $index }}">
                            <div class="user-info">
                                <div class="user-avatar">
                                    @if(!empty($worker->profile_photo))
                                        <img src="{{ $worker->profile_photo }}"
                                             alt="{{ $worker->name }}"
                                             style="width:100%; height:100%; object-fit:cover; border-radius:50%; display:block;"
                                             onerror="this.style.display='none'; this.nextElementSibling.style.display='';">
                                        <svg class="icon" aria-hidden="true" style="display:none;"><use href="#i-user"/></svg>
                                    @else
                                        <svg class="icon" aria-hidden="true"><use href="#i-user"/></svg>
                                    @endif

                                    @if($index === 0)
                                        <span class="rank-icon"><svg class="icon" aria-hidden="true"><use href="#i-crown"/></svg></span>
                                    @elseif($index < 3)
                                        <span class="rank-icon"><svg class="icon" aria-hidden="true"><use href="#i-medal"/></svg></span>
                                    @endif
                                </div>
                                <span class="name">{{ $worker->name }}</span>
                            </div>
                            <span class="rank-badge">#{{ $index + 1 }} ({{ number_format($worker->kpi_score ?? 0, 1) }}%)</span>
                        </div>
                        @empty
                        <p style="text-align: center; color: #94a3b8; padding: 15px;">No worker records found.</p>
                        @endforelse
                    </div>
                </section>

                <!-- AI ANALYTICS -->
                <section class="ai-section reveal" style="--i:4" aria-label="AI Analytics" role="group" aria-roledescription="carousel">
                    <div class="ai-header">
                        <div class="section-title">
                            <svg class="icon ai-sparkle" aria-hidden="true"><use href="#i-sparkles"/></svg> AI Analytics
                        </div>
                        <div style="display:flex; align-items:center; gap:8px;">
                            <span class="ai-status-badge" id="aiStatusBadge">
                                {{ ($aiSource ?? '') === 'gemini-ai' ? 'Gemini AI Live' : 'Smart Insights' }}
                            </span>
                            <form action="{{ route('hr.ai.refresh') }}" method="POST" id="aiRefreshForm" style="margin:0;">
                                @csrf
                                <button type="button" class="nav-btn" id="aiRefreshBtn"
                                        onclick="refreshAiAnalytics()"
                                        title="Regenerate AI Analysis" aria-label="Regenerate AI Analysis">
                                    <svg class="icon" id="aiRefreshIcon" aria-hidden="true"><use href="#i-refresh"/></svg>
                                </button>
                            </form>
                        </div>
                    </div>

                    <style>
                        #aiCardContent {
                            height: 148px !important;
                            min-height: 148px !important;
                            max-height: 148px !important;
                            overflow-y: auto !important;
                            display: flex;
                            flex-direction: column;
                            justify-content: flex-start;
                            padding: 14px 12px 14px 16px !important;
                            scrollbar-width: thin;
                            scrollbar-color: #cbd5e1 transparent;
                        }
                        #aiCardContent::-webkit-scrollbar {
                            width: 5px;
                        }
                        #aiCardContent::-webkit-scrollbar-thumb {
                            background-color: #cbd5e1;
                            border-radius: 999px;
                        }
                        #aiCardContent * {
                            white-space: normal !important;
                            text-overflow: unset !important;
                        }
                    </style>

                    <div class="ai-content-wrapper">
                        <div class="ai-content" id="aiCardContent">
                            <div class="ai-content-wrapper">
                                <div class="ai-content" id="aiCardContent" data-ai-insights='@json($aiInsights ?? [])'></div>
                            </div>
                        </div>
                    </div>

                    <div class="ai-navigation">
                        <button type="button" class="nav-btn" onclick="prevAISlide()" aria-label="Previous insight">
                            <svg class="icon" aria-hidden="true"><use href="#i-chev-l"/></svg>
                        </button>
                        <div class="dot-indicators" id="aiDots"></div>
                        <button type="button" class="nav-btn" onclick="nextAISlide()" aria-label="Next insight">
                            <svg class="icon" aria-hidden="true"><use href="#i-chev-r"/></svg>
                        </button>
                        <button type="button" class="nav-btn ghost" id="aiToggle" aria-label="Pause auto-rotation">
                            <svg class="icon fill" aria-hidden="true"><use href="#i-pause"/></svg>
                        </button>
                    </div>
                </section>

            </div>
        </section>

        <!-- DEPARTMENTS PERFORMANCE TABLE -->
        <section class="performance-section reveal" style="--i:6" aria-labelledby="perfTitle">
            <h2 class="performance-title" id="perfTitle">
                <span><svg class="icon" aria-hidden="true"><use href="#i-building"/></svg> Departments’ Performance</span>
                <span class="dept-count-badge" id="deptCount">
                    {{ count($departmentsPerformance ?? []) }} Departments
                </span>
            </h2>

            <div class="table-wrapper" tabindex="0" role="region" aria-labelledby="perfTitle">
                <table class="performance-table">
                    <thead>
                     <tr>
                         <th scope="col" style="width: 60px;">No.</th>
                         <th scope="col" style="width: 180px;">Department</th>
                         <th scope="col" style="width: 130px;">Avg. KPI (%)</th>
                         <th scope="col" style="width: 130px;">Total Staff</th>
                         <th scope="col" style="width: 220px;">Submission Status</th>
                         <th scope="col" style="width: 160px;">EOM Recipients</th>
                         <th scope="col" style="width: 150px;">AI Action</th>
                     </tr>
                 </thead>
                    <tbody id="performanceTableBody">
                        @forelse($departmentsPerformance as $index => $dept)
                        <tr style="--i:{{ $index }}">
                            <td>{{ $index + 1 }}</td>
                            <td class="dept-name"><strong>{{ strtoupper($dept->department_name) }}</strong></td>
                            <td class="kpi-score">{{ number_format($dept->avg_kpi, 1) }}%</td>
                            <td>{{ $dept->total_staff }} STAFF</td>
                            <td>
                                @php $isComplete = strtolower($dept->status) === 'complete' || $dept->submitted == $dept->total_staff; @endphp
                                <span class="status-pill {{ $isComplete ? 'complete' : 'pending' }}">
                                    <svg class="icon" aria-hidden="true"><use href="{{ $isComplete ? '#i-check' : '#i-clock' }}"/></svg>
                                    {{ strtoupper($dept->status) }} ({{ $dept->submitted }}/{{ $dept->total_staff }})
                                </span>
                            </td>
                            <td>
                                @if(($dept->gold_badges ?? 0) > 0 || ($dept->silver_badges ?? 0) > 0)
                                    <div class="eom-container">
                                        @if(($dept->gold_badges ?? 0) > 0)
                                            <span class="eom-badge gold" title="1st place"><svg class="icon" aria-hidden="true"><use href="#i-crown"/></svg> {{ $dept->gold_badges }}</span>
                                        @endif
                                        @if(($dept->gold_badges ?? 0) > 0 && ($dept->silver_badges ?? 0) > 0)
                                            <span class="eom-sep" aria-hidden="true"></span>
                                        @endif
                                        @if(($dept->silver_badges ?? 0) > 0)
                                            <span class="eom-badge silver" title="2nd place"><svg class="icon" aria-hidden="true"><use href="#i-medal"/></svg> {{ $dept->silver_badges }}</span>
                                        @endif
                                    </div>
                                @else
                                    <span class="eom-empty">-</span>
                                @endif
                            </td>
                            <td>
                             @if(!empty($dept->target_worker_id) && (!$isComplete || ($dept->avg_kpi ?? 0) < 80))
                                 <button type="button"
                                         class="ai-remind-btn"
                                         onclick="openAiReminderModal('{{ $dept->target_worker_id }}', '{{ strtoupper($dept->department_name) }}')">
                                     <svg class="icon" style="width:14px;height:14px;" aria-hidden="true"><use href="#i-sparkles"/></svg>
                                     {{ !$isComplete ? 'AI Remind' : 'AI Follow-Up' }}
                                 </button>
                             @else
                                 <span style="color:#16a34a; font-size:0.82rem; font-weight:600;">✓ On Track</span>
                             @endif
                         </td>
                        </tr>
                        @empty
                        <tr class="empty-table-row">
                            <td colspan="7" style="text-align: center; color: #94a3b8; padding: 20px;">
                                No department performance records found.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </main>

    <!-- TOAST REGION -->
    <div class="toast-region" id="toastRegion" aria-live="polite"></div>

    <!-- JAVASCRIPT LOGIC (DYNAMIC MYSQL INTEGRATION) -->
<script>
    const $ = id => document.getElementById(id);
    const prefersReduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    const icon = (name, cls = '') => `<svg class="icon ${cls}" aria-hidden="true"><use href="#i-${name}"/></svg>`;
    const esc = s => String(s).replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));

    // 1. RENDER CHARTS
    function renderCharts(data) {
        if (data.kpiTrend && data.kpiTrend.length === 4) {
            const xs = [20, 140, 260, 380];
            const pts = data.kpiTrend.map((v, i) => ({ 
                i, 
                v, 
                x: xs[i], 
                y: 160 - (Math.min(Math.max(v, 0), 100) / 100 * 160) 
            }));
            const line = pts.map(p => `${p.x},${p.y}`).join(' ');

            if ($('trendPolyline')) $('trendPolyline').setAttribute('points', line);
            if ($('trendArea')) $('trendArea').setAttribute('d', `M${pts[0].x},160 L${line.replace(/ /g, ' L')} L${pts[3].x},160 Z`);
            if ($('trendDots')) {
                $('trendDots').innerHTML = pts.map(p => `
                    <span class="plot-dot" tabindex="0" style="left:${p.x / 4}%;top:${(p.y / 160) * 100}%;--i:${p.i}"
                          data-tip="Q${p.i + 1}: ${p.v}%" aria-label="Q${p.i + 1}: ${p.v} percent"></span>`).join('');
            }
        }

        const box = $('departmentBarsContainer');
        if (box && data.departmentScores && data.departmentScores.length) {
            box.innerHTML = data.departmentScores.map((d, i) => `
                <div class="bar-row">
                    <strong>${esc(d.name)}</strong>
                    <div class="bar" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="${d.score}">
                        <div class="bar-fill" style="--w:${d.score}%;--d:${300 + i * 90}ms"></div>
                    </div>
                    <b>${d.score}%</b>
                </div>`).join('');
        }
    }

    // 2. AI CAROUSEL
    let aiInsights = [];
    let currentSlide = 0, aiTimer = null, aiPaused = prefersReduced, aiHover = false;

    function renderAISlide(index) {
        const card = $('aiCardContent');
        if (!card || !aiInsights || aiInsights.length === 0) return;
        const item = aiInsights[index];
        card.style.opacity = '0';
        card.style.transform = 'translateY(6px)';
        setTimeout(() => {
            card.innerHTML = `
                <span class="ai-tag">${esc(item.tag ?? 'AI Insight')}</span>
                <div class="ai-title">${esc(item.title ?? '')}</div>
                <div class="ai-desc">${esc(item.desc ?? '')}</div>
                <div class="ai-stat">${esc(item.stat ?? '')}</div>`;
            if ($('aiDots')) {
                $('aiDots').innerHTML = aiInsights.map((_, i) =>
                    `<button type="button" class="${i === index ? 'active' : ''}" onclick="goToAISlide(${i})"></button>`).join('');
            }
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, prefersReduced ? 0 : 150);
    }

    function nextAISlide() { if (aiInsights.length) { currentSlide = (currentSlide + 1) % aiInsights.length; renderAISlide(currentSlide); } }
    function prevAISlide() { if (aiInsights.length) { currentSlide = (currentSlide - 1 + aiInsights.length) % aiInsights.length; renderAISlide(currentSlide); } }
    function goToAISlide(i) { currentSlide = i; renderAISlide(i); }

    function syncAIAutoplay() {
        clearInterval(aiTimer);
        if (!aiPaused && !aiHover && aiInsights.length > 0) aiTimer = setInterval(nextAISlide, 6000);
    }

    //AI REFRESH
    async function refreshAiAnalytics() {
        const form = $('aiRefreshForm');
        const btn = $('aiRefreshBtn');
        const iconEl = $('aiRefreshIcon');
        const card = $('aiCardContent');
        const badge = $('aiStatusBadge');
        const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

        if (!form || !btn || btn.disabled) return;

        // Aktifkan animasi pusingan ikon & paparan loading dalam kad AI sahaja
        btn.disabled = true;
        btn.style.opacity = '0.65';
        if (iconEl) iconEl.style.animation = 'spin 0.85s linear infinite';
        clearInterval(aiTimer);

        if (card) {
            card.innerHTML = `
                <span class="ai-tag">ANALYZING DATA</span>
                <div class="ai-title">Regenerating AI Insights...</div>
                <div class="ai-desc">Analyzing latest department KPI & submission records.</div>`;
        }

        try {
            const res = await fetch(form.action, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': token,
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json, text/html'
                }
            });

            if (!res.ok) throw new Error('Failed to refresh AI insights');

            const contentType = res.headers.get('content-type') || '';

            if (contentType.includes('application/json')) {
                // Jika Controller memulangkan respons JSON
                const data = await res.json();
                if (Array.isArray(data.aiInsights) && data.aiInsights.length > 0) {
                    aiInsights = data.aiInsights;
                }
                if (badge && data.aiSource) {
                    badge.textContent = data.aiSource === 'gemini-ai' ? 'Gemini AI Live' : 'Smart Insights';
                }
            } else {
                // Jika Controller masih menggunakan redirect()->back(), ekstrak data AI sahaja daripada HTML
                const html = await res.text();
                const doc = new DOMParser().parseFromString(html, 'text/html');
                const newCard = doc.getElementById('aiCardContent');
                const newBadge = doc.getElementById('aiStatusBadge');

                if (newCard && newCard.dataset.aiInsights) {
                    const parsed = JSON.parse(newCard.dataset.aiInsights);
                    if (Array.isArray(parsed) && parsed.length > 0) {
                        aiInsights = parsed;
                    }
                }
                if (newBadge && badge) {
                    badge.textContent = newBadge.textContent.trim();
                }
            }

            currentSlide = 0;
            renderAISlide(0);
            showAiToast('AI Analytics updated.');
        } catch (err) {
            console.error('AI Refresh Error:', err);
            renderAISlide(currentSlide);
            showAiToast('Failed to refresh AI Analytics.', true);
        } finally {
            btn.disabled = false;
            btn.style.opacity = '1';
            if (iconEl) iconEl.style.animation = '';
            syncAIAutoplay();
        }
    }

    // FILTER BADGE RANKING TANPA REFRESH PAGE
    async function filterBadgeRanking(dept) {
        const selectEl = $('departmentSelect');
        const listEl = $('badgeList');
        if (!selectEl || !listEl) return;

        const url = new URL(window.location.href);
        url.searchParams.set('dept_filter', dept);

        selectEl.disabled = true;
        listEl.style.opacity = '0.45';
        listEl.style.transition = 'opacity 180ms ease';

        try {
            const res = await fetch(url.toString(), {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json, text/html'
                }
            });

            if (!res.ok) throw new Error('Failed to load badge rankings');

            const contentType = res.headers.get('content-type') || '';

            if (contentType.includes('application/json')) {
                const data = await res.json();
                renderBadgeList(data.badgeRankings || []);
            } else {
                // Ekstrak bahagian #badgeList sahaja daripada respons HTML sedia ada
                const html = await res.text();
                const doc = new DOMParser().parseFromString(html, 'text/html');
                const newList = doc.getElementById('badgeList');
                if (newList) {
                    listEl.innerHTML = newList.innerHTML;
                }
            }

            // Kemas kini parameter URL secara senyap tanpa reload halaman
            window.history.replaceState({}, '', url.toString());
        } catch (err) {
            console.error('Badge Filter Error:', err);
        } finally {
            selectEl.disabled = false;
            listEl.style.opacity = '1';
        }
    }

    function renderBadgeList(rankings) {
        const listEl = $('badgeList');
        if (!listEl) return;

        if (!rankings || rankings.length === 0) {
            listEl.innerHTML = `<p style="text-align: center; color: #94a3b8; padding: 15px;">No worker records found.</p>`;
            return;
        }

        listEl.innerHTML = rankings.map((worker, index) => {
            const rank = index + 1;
            const score = Number(worker.kpi_score ?? 0).toFixed(1);
            const avatarHtml = worker.profile_photo
                ? `<img src="${esc(worker.profile_photo)}" alt="${esc(worker.name)}"
                        style="width:100%; height:100%; object-fit:cover; border-radius:50%; display:block;"
                        onerror="this.style.display='none'; this.nextElementSibling.style.display='';">
                   <svg class="icon" aria-hidden="true" style="display:none;"><use href="#i-user"/></svg>`
                : `<svg class="icon" aria-hidden="true"><use href="#i-user"/></svg>`;

            let rankIconHtml = '';
            if (index === 0) {
                rankIconHtml = `<span class="rank-icon"><svg class="icon" aria-hidden="true"><use href="#i-crown"/></svg></span>`;
            } else if (index < 3) {
                rankIconHtml = `<span class="rank-icon"><svg class="icon" aria-hidden="true"><use href="#i-medal"/></svg></span>`;
            }

            return `
                <div class="badge-user rank-${rank}" style="--i:${index}">
                    <div class="user-info">
                        <div class="user-avatar">
                            ${avatarHtml}
                            ${rankIconHtml}
                        </div>
                        <span class="name">${esc(worker.name)}</span>
                    </div>
                    <span class="rank-badge">#${rank} (${score}%)</span>
                </div>`;
        }).join('');
    }

    function showAiToast(message, isError = false) {
        const region = $('toastRegion');
        if (!region) return;
        const toast = document.createElement('div');
        toast.className = `toast ${isError ? 'error' : ''}`;
        toast.innerHTML = `${icon(isError ? 'alert' : 'check')}<span>${esc(message)}</span>`;
        region.appendChild(toast);
        setTimeout(() => {
            toast.classList.add('out');
            setTimeout(() => toast.remove(), 220);
        }, 2600);
    }

  @php
        $chartTrendData = $kpiTrend ?? [20, 45, 60, 75];
        $chartDeptScores = collect($departmentsPerformance ?? [])->map(fn($dept) => [
            'name'  => strtoupper($dept->department_name),
            'score' => (float) $dept->avg_kpi,
        ])->values();
        $chartAiInsights = $aiInsights ?? [
            ['tag' => 'AI Recommendation', 'title' => 'Top Performance', 'desc' => 'Department performance metrics updated.', 'stat' => 'Optimal']
        ];
    @endphp

    // 3. INITIALIZATION WITH LIVE MYSQL DATA
    document.addEventListener('DOMContentLoaded', () => {
        const liveDatabaseChartData = {
            kpiTrend: @json($chartTrendData),
            departmentScores: @json($chartDeptScores)
        };

        aiInsights = @json($chartAiInsights);
        renderCharts(liveDatabaseChartData);
        if (aiInsights.length > 0) renderAISlide(0);

        const aiSection = document.querySelector('.ai-section');
        if (aiSection) {
            aiSection.addEventListener('mouseenter', () => { aiHover = true; syncAIAutoplay(); });
            aiSection.addEventListener('mouseleave', () => { aiHover = false; syncAIAutoplay(); });
        }
        const aiToggleBtn = $('aiToggle');
            if (aiToggleBtn) {
                aiToggleBtn.addEventListener('click', () => {
                    aiPaused = !aiPaused;
                    syncAIAutoplay();
                    const iconUse = aiToggleBtn.querySelector('use');
                    if (iconUse) {
                        iconUse.setAttribute('href', aiPaused ? '#i-play' : '#i-pause');
                    }
                    aiToggleBtn.setAttribute('aria-label', aiPaused ? 'Play AI insights' : 'Pause AI insights');
                });
            }
        syncAIAutoplay();


        // 4. LOGIK BUTANG SYNC / REFRESH (TANPA RELOAD KESELURUHAN PAGE)
        const syncStatusEl = $('syncStatus');
        const syncBtn = $('syncBtn');
        const syncText = $('syncText');
        let lastSyncedAt = Date.now();
        let isSyncing = false;

        function formatSyncAge(ts) {
            const diffSec = Math.floor((Date.now() - ts) / 1000);
            if (diffSec < 10) return 'Synced just now';
            if (diffSec < 60) return `Synced ${diffSec}s ago`;
            const diffMin = Math.floor(diffSec / 60);
            if (diffMin < 60) return `Synced ${diffMin}m ago`;
            return `Synced ${Math.floor(diffMin / 60)}h ago`;
        }

        // Kemas kini label masa penyegerakan setiap 30 saat
        setInterval(() => {
            if (!isSyncing && syncText && !syncStatusEl?.classList.contains('error')) {
                syncText.textContent = formatSyncAge(lastSyncedAt);
            }
        }, 30000);

        function showSyncToast(message, isError = false) {
            const region = $('toastRegion');
            if (!region) return;
            const toast = document.createElement('div');
            toast.className = `toast ${isError ? 'error' : ''}`;
            toast.innerHTML = `${icon(isError ? 'alert' : 'check')}<span>${esc(message)}</span>`;
            region.appendChild(toast);
            setTimeout(() => {
                toast.classList.add('out');
                setTimeout(() => toast.remove(), 220);
            }, 2600);
        }

        async function syncDashboardData() {
            if (isSyncing) return;
            isSyncing = true;

            if (syncStatusEl) {
                syncStatusEl.classList.remove('error');
                syncStatusEl.classList.add('loading');
            }
            if (syncBtn) syncBtn.disabled = true;
            if (syncText) syncText.textContent = 'Syncing...';

            try {
                // Kekalkan pilihan jabatan semasa pada Badge Ranking
                const url = new URL(window.location.href);
                const deptSelect = $('departmentSelect');
                if (deptSelect && deptSelect.value) {
                    url.searchParams.set('dept_filter', deptSelect.value);
                }

                const res = await fetch(url.toString(), {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'text/html'
                    }
                });

                if (!res.ok) throw new Error('Sync request failed');

                const html = await res.text();
                const doc = new DOMParser().parseFromString(html, 'text/html');

                // A. Kemas kini 3 Summary Cards
                ['avgKpiVal', 'avgKpiTrendText', 'overloadVal', 'overloadSubText', 'targetVal', 'targetProgressText'].forEach(id => {
                    const currentEl = $(id);
                    const freshEl = doc.getElementById(id);
                    if (currentEl && freshEl) currentEl.textContent = freshEl.textContent.trim();
                });

                ['avgKpiTrend', 'overloadCard', 'overloadSubText'].forEach(id => {
                    const currentEl = $(id);
                    const freshEl = doc.getElementById(id);
                    if (currentEl && freshEl) {
                        currentEl.className = freshEl.className;
                        if (id === 'avgKpiTrend') currentEl.innerHTML = freshEl.innerHTML;
                    }
                });

                // B. Kemas kini Charts & AI Analytics
                const freshMain = doc.getElementById('main');
                if (freshMain && freshMain.dataset.syncPayload) {
                    const payload = JSON.parse(freshMain.dataset.syncPayload);
                    renderCharts({
                        kpiTrend: payload.kpiTrend,
                        departmentScores: payload.departmentScores
                    });
                    if (Array.isArray(payload.aiInsights) && payload.aiInsights.length > 0) {
                        aiInsights = payload.aiInsights;
                        currentSlide = 0;
                        renderAISlide(0);
                    }
                }

                const freshAiBadge = doc.getElementById('aiStatusBadge');
                if ($('aiStatusBadge') && freshAiBadge) {
                    $('aiStatusBadge').textContent = freshAiBadge.textContent.trim();
                }

                // C. Kemas kini Badge Ranking
                const freshBadgeList = doc.getElementById('badgeList');
                if ($('badgeList') && freshBadgeList) {
                    $('badgeList').innerHTML = freshBadgeList.innerHTML;
                }

                // D. Kemas kini Jadual Departments' Performance
                const freshDeptCount = doc.getElementById('deptCount');
                if ($('deptCount') && freshDeptCount) {
                    $('deptCount').textContent = freshDeptCount.textContent.trim();
                }
                const freshPerfBody = doc.getElementById('performanceTableBody');
                if ($('performanceTableBody') && freshPerfBody) {
                    $('performanceTableBody').innerHTML = freshPerfBody.innerHTML;
                }

                lastSyncedAt = Date.now();
                if (syncStatusEl) syncStatusEl.classList.remove('loading', 'error');
                if (syncText) syncText.textContent = 'Synced just now';
                showSyncToast('Dashboard data synced.');
            } catch (err) {
                console.error('Dashboard Sync Error:', err);
                if (syncStatusEl) {
                    syncStatusEl.classList.remove('loading');
                    syncStatusEl.classList.add('error');
                }
                if (syncText) syncText.textContent = 'Sync failed';
                showSyncToast('Failed to sync dashboard data.', true);
            } finally {
                isSyncing = false;
                if (syncBtn) syncBtn.disabled = false;
            }
        }

        if (syncBtn) {
            syncBtn.addEventListener('click', syncDashboardData);
        }
    });

    async function refreshAiAnalytics() {
        const form = $('aiRefreshForm');
        const btn = $('aiRefreshBtn');
        const iconEl = $('aiRefreshIcon');
        const card = $('aiCardContent');
        const badge = $('aiStatusBadge');
        const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

        if (!form || !btn || btn.disabled) return;

        // Aktifkan animasi pusingan ikon & paparan loading dalam kad AI sahaja
        btn.disabled = true;
        btn.style.opacity = '0.65';
        if (iconEl) iconEl.style.animation = 'spin 0.85s linear infinite';
        clearInterval(aiTimer);

        if (card) {
            card.innerHTML = `
                <span class="ai-tag">ANALYZING DATA</span>
                <div class="ai-title">Regenerating AI Insights...</div>
                <div class="ai-desc">Analyzing latest department KPI & submission records.</div>`;
        }

        try {
            const res = await fetch(form.action, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': token,
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json, text/html'
                }
            });

            if (!res.ok) throw new Error('Failed to refresh AI insights');

            const contentType = res.headers.get('content-type') || '';

            if (contentType.includes('application/json')) {
                // Jika Controller memulangkan respons JSON
                const data = await res.json();
                if (Array.isArray(data.aiInsights) && data.aiInsights.length > 0) {
                    aiInsights = data.aiInsights;
                }
                if (badge && data.aiSource) {
                    badge.textContent = data.aiSource === 'gemini-ai' ? 'Gemini AI Live' : 'Smart Insights';
                }
            } else {
                // Jika Controller masih menggunakan redirect()->back(), ekstrak data AI sahaja daripada HTML
                const html = await res.text();
                const doc = new DOMParser().parseFromString(html, 'text/html');
                const newCard = doc.getElementById('aiCardContent');
                const newBadge = doc.getElementById('aiStatusBadge');

                if (newCard && newCard.dataset.aiInsights) {
                    const parsed = JSON.parse(newCard.dataset.aiInsights);
                    if (Array.isArray(parsed) && parsed.length > 0) {
                        aiInsights = parsed;
                    }
                }
                if (newBadge && badge) {
                    badge.textContent = newBadge.textContent.trim();
                }
            }

            currentSlide = 0;
            renderAISlide(0);
            showAiToast('AI Analytics updated.');
        } catch (err) {
            console.error('AI Refresh Error:', err);
            renderAISlide(currentSlide);
            showAiToast('Failed to refresh AI Analytics.', true);
        } finally {
            btn.disabled = false;
            btn.style.opacity = '1';
            if (iconEl) iconEl.style.animation = '';
            syncAIAutoplay();
        }
    }
</script>
<script src="{{ asset('session.js') }}?v=8"></script>
</body>

<!-- AI EMAIL REMINDER MODAL -->
 <style>
     .ai-remind-btn {
         display: inline-flex; align-items: center; gap: 6px;
         background: linear-gradient(135deg, #be123c, #e11d48);
         color: #fff; border: none; border-radius: 8px;
         padding: 6px 12px; font-size: 0.8rem; font-weight: 600;
         cursor: pointer; transition: transform 0.15s ease, box-shadow 0.15s ease;
     }
     .ai-remind-btn:hover { transform: translateY(-1px); box-shadow: 0 4px 12px rgba(225, 29, 72, 0.28); }
     .ai-modal-backdrop {
         position: fixed; inset: 0; background: rgba(15, 23, 42, 0.55);
         backdrop-filter: blur(3px); display: none; align-items: center;
         justify-content: center; z-index: 9999; padding: 16px;
     }
     .ai-modal-backdrop.open { display: flex; }
     .ai-modal-card {
         background: #fff; width: 100%; max-width: 560px; border-radius: 16px;
         box-shadow: 0 20px 40px rgba(15, 23, 42, 0.22); overflow: hidden;
         animation: modalPop 0.2s ease-out;
     }
     @keyframes modalPop { from { opacity: 0; transform: scale(0.96); } to { opacity: 1; transform: scale(1); } }
     .ai-modal-header {
         background: #111827; color: #fff; padding: 16px 20px;
         display: flex; align-items: center; justify-content: space-between;
     }
     .ai-modal-header h3 { margin: 0; font-size: 1rem; display: flex; align-items: center; gap: 8px; }
     .ai-modal-close { background: none; border: none; color: #9ca3af; font-size: 1.4rem; cursor: pointer; line-height: 1; }
     .ai-modal-body { padding: 20px; display: flex; flex-direction: column; gap: 12px; }
     .ai-field label { display: block; font-size: 0.78rem; font-weight: 700; color: #374151; margin-bottom: 4px; text-transform: uppercase; }
     .ai-field input, .ai-field textarea {
         width: 100%; border: 1px solid #d1d5db; border-radius: 8px;
         padding: 9px 12px; font-size: 0.9rem; font-family: inherit; color: #111827;
     }
     .ai-field textarea { min-height: 165px; resize: vertical; line-height: 1.5; }
     .ai-modal-footer {
         padding: 14px 20px; background: #f9fafb; border-top: 1px solid #e5e7eb;
         display: flex; justify-content: space-between; align-items: center; gap: 10px; flex-wrap: wrap;
     }
     .ai-btn-secondary {
         background: #fff; border: 1px solid #d1d5db; color: #374151;
         padding: 8px 14px; border-radius: 8px; font-size: 0.85rem; font-weight: 600; cursor: pointer;
     }
     .ai-btn-primary {
         background: #be123c; border: none; color: #fff;
         padding: 8px 16px; border-radius: 8px; font-size: 0.85rem; font-weight: 600; cursor: pointer;
     }
 </style>

 <div class="ai-modal-backdrop" id="aiReminderModal" role="dialog" aria-modal="true" aria-labelledby="aiModalTitle">
     <div class="ai-modal-card">
         <div class="ai-modal-header">
             <h3 id="aiModalTitle">
                 <svg class="icon" style="width:18px;height:18px;color:#fb7185;" aria-hidden="true"><use href="#i-sparkles"/></svg>
                 AI Smart Email Reminder (<span id="aiModalDept">DEPT</span>)
             </h3>
             <button type="button" class="ai-modal-close" onclick="closeAiReminderModal()" aria-label="Close">&times;</button>
         </div>

         <div class="ai-modal-body">
             <div id="aiModalLoading" style="text-align:center; padding:32px 12px; color:#4b5563;">
                 <p style="font-weight:600; margin-bottom:6px;">Generating personalized email draft with Gemini AI...</p>
                 <small style="color:#6b7280;">Analyzing evaluation records & department deadline</small>
             </div>

             <div id="aiModalForm" style="display:none; flex-direction:column; gap:12px;">
                 <div class="ai-field">
                     <label for="aiDraftTo">Recipient (<span id="aiDraftRecipientName">-</span>)</label>
                     <input type="email" id="aiDraftTo" placeholder="recipient@infiniteloop.com.my">
                 </div>
                 <div class="ai-field">
                     <label for="aiDraftSubject">Email Subject</label>
                     <input type="text" id="aiDraftSubject">
                 </div>
                 <div class="ai-field">
                     <label for="aiDraftBody">AI-Generated Message</label>
                     <textarea id="aiDraftBody"></textarea>
                 </div>
             </div>
         </div>

         <div class="ai-modal-footer">
             <button type="button" class="ai-btn-secondary" id="aiRegenerateBtn" onclick="regenerateCurrentDraft()">Regenerate</button>
             <div style="display:flex; gap:8px;">
                 <button type="button" class="ai-btn-secondary" id="aiCopyBtn" onclick="copyAiDraft()">Copy Draft</button>
                 <button type="button" class="ai-btn-primary" onclick="openMailClient()">Open in Email App</button>
             </div>
         </div>
     </div>
 </div>

 <script>
        let activeReminderWorkerId = null;
        let activeReminderDept = '';
        let reminderVariation = 1;

        function openAiReminderModal(workerId, deptName) {
            activeReminderWorkerId = workerId;
            activeReminderDept = deptName;
            reminderVariation = 1;
            document.getElementById('aiModalDept').textContent = deptName;
            document.getElementById('aiReminderModal').classList.add('open');
            fetchAiDraft(workerId, reminderVariation);
        }

        function closeAiReminderModal() {
            document.getElementById('aiReminderModal').classList.remove('open');
        }

        function regenerateCurrentDraft() {
            if (!activeReminderWorkerId) return;
            reminderVariation++;
            fetchAiDraft(activeReminderWorkerId, reminderVariation);
        }

        async function fetchAiDraft(workerId, variation = 1) {
            const loading = document.getElementById('aiModalLoading');
            const form = document.getElementById('aiModalForm');
            const regenBtn = document.getElementById('aiRegenerateBtn');

            loading.style.display = 'block';
            form.style.display = 'none';
            regenBtn.disabled = true;
            regenBtn.textContent = 'Generating...';

            try {
                const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                const res = await fetch("{{ route('hr.ai.reminder') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': token
                    },
                    body: JSON.stringify({
                        worker_id: workerId,
                        variation: variation
                    })
                });

                const data = await res.json();
                if (data.status === 'success' && data.draft) {
                    document.getElementById('aiDraftRecipientName').textContent = `${data.draft.recipient_name} (${data.draft.recipient_role})`;
                    document.getElementById('aiDraftTo').value = data.draft.recipient_email || '';
                    document.getElementById('aiDraftSubject').value = data.draft.subject || '';
                    document.getElementById('aiDraftBody').value = data.draft.body || '';
                }
            } catch (err) {
                console.error('AI Draft Error:', err);
            } finally {
                loading.style.display = 'none';
                form.style.display = 'flex';
                regenBtn.disabled = false;
                regenBtn.textContent = 'Regenerate';
            }
        }

        function copyAiDraft() {
            const subject = document.getElementById('aiDraftSubject').value;
            const body = document.getElementById('aiDraftBody').value;
            navigator.clipboard.writeText(`Subject: ${subject}\n\n${body}`);
            const btn = document.getElementById('aiCopyBtn');
            btn.textContent = 'Copied!';
            setTimeout(() => { btn.textContent = 'Copy Draft'; }, 2000);
        }

        function openMailClient() {
            const to = encodeURIComponent(document.getElementById('aiDraftTo').value);
            const subject = encodeURIComponent(document.getElementById('aiDraftSubject').value);
            const body = encodeURIComponent(document.getElementById('aiDraftBody').value);
            window.location.href = `mailto:${to}?subject=${subject}&body=${body}`;
        }

        // Papar teks penuh apabila tetikus hover pada kandungan kad AI
        const aiCardEl = document.getElementById('aiCardContent');
        if (aiCardEl) {
            const applyAiTooltips = () => {
                aiCardEl.querySelectorAll('h3, h4, p, div, span').forEach(el => {
                    if (el.children.length === 0 && el.textContent.trim().length > 0) {
                        el.title = el.textContent.trim();
                    }
                });
            };
            applyAiTooltips();
            new MutationObserver(applyAiTooltips).observe(aiCardEl, { childList: true, subtree: true });
        }
    </script>
</html>