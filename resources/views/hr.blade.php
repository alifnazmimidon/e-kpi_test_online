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
                        <li><a href="{{ route('worker.dashboard') }}">View Profile</a></li>
                        <li><a href="{{ route('hr.dashboard') }}">Dashboard</a></li>
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
    <main class="dashboard" id="main">
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
                        <select id="departmentSelect" aria-label="Filter ranking by department" onchange="location.href='?dept_filter=' + encodeURIComponent(this.value);">
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
                        <span class="ai-status-badge">Live Insights</span>
                    </div>

                    <div class="ai-content-wrapper">
                        <div class="ai-content" id="aiCardContent"></div>
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
                            <th scope="col" style="width: 180px;">EOM Recipients</th>
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
                        </tr>
                        @empty
                        <tr class="empty-table-row">
                            <td colspan="6" style="text-align: center; color: #94a3b8; padding: 20px;">
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

        // 4. LOGIK BUTANG SYNC / REFRESH
        const syncBtn = $('syncBtn');
        const syncText = $('syncText');

        if (syncBtn) {
            syncBtn.addEventListener('click', function() {
                const iconSvg = syncBtn.querySelector('.icon');
                if (iconSvg) {
                    iconSvg.style.transition = 'transform 0.6s ease';
                    iconSvg.style.transform = 'rotate(360deg)';
                }
                if (syncText) syncText.textContent = 'Syncing...';

                setTimeout(() => {
                    window.location.reload();
                }, 400);
            });
        }
    });
</script>
<script src="{{ asset('session.js') }}?v=8"></script>
</body>
</html>