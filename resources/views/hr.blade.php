<!DOCTYPE html>
<html lang="en">

<<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-KPI System - HR Dashboard</title>

    <link rel="stylesheet" href="{{ asset('stylehr.css') }}">
</head>

<body>

    <!-- ================================
         HEADER
    ================================= -->
    <header class="header" style="color: white;">

        <div class="header-left">
            <span class="system-title">E-KPI SYSTEM</span>
            <div class="header-line"></div>

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

            <!-- SETTINGS ICON -->
            <button class="header-icon">
                <center>
                    <div class="gear-icon">
                        <img src="{{ asset('assets/gearwhite.png') }}" class="img-base">
                        <img src="{{ asset('assets/gearblack.png') }}" class="img-hover">
                    </div>
                </center>
            </button>

            <!-- FORM LOGOUT -->
            <form action="{{ route('logout') }}" method="POST" style="display: inline; margin-left: 10px;">
                @csrf
                <button type="submit" class="nav-link" style="background: #e11d48; color: white; border: none; padding: 6px 12px; border-radius: 4px; cursor: pointer;">
                    Logout
                </button>
            </form>

        </div>

    </header>

    <!-- ================================
         NAVIGATION
    ================================= -->
    <nav class="navigation">
        <a href="{{ route('hr.dashboard') }}" class="nav-link active">Dashboard</a>
       <a href="{{ route('hr.kpi') }}" class="nav-link">KPI Management</a>
        <a href="#" class="nav-link">Report</a>
        <a href="#" class="nav-link">Emergency Leave</a>

        <div class="sync-status">
            <span class="sync-dot"></span>
            <span>Synced just now</span>
        </div>
    </nav>

            <!-- ================================
            MAIN DASHBOARD
            ================================= -->
        <main class="dashboard">

            <section class="top-section">

                <!-- LEFT SIDE -->
                <div class="top-left">

                    <!-- KPI SUMMARY CARDS -->
                    <div class="summary-cards">

                        <!-- 1. AVERAGE KPI SCORE -->
                        <div class="summary-card" id="avgKpiCard">
                            <div class="card-header-row">
                                <h3>Average KPI Score (%)</h3>
                                <div class="card-icon blue">📊</div>
                            </div>
                            <div class="summary-value" id="avgKpiVal">
                                {{ number_format($avgKpiScore ?? 84.2, 1) }}%
                            </div>
                            <div class="card-footer">
                                <span class="trend-badge positive" id="avgKpiTrend">
                                    <span class="trend-icon">↑</span> 
                                    <span id="avgKpiTrendText">+2.4% vs last quarter</span>
                                </span>
                            </div>
                        </div>

                        <!-- 2. UNHEALTHY / OVERLOAD -->
                        <div class="summary-card {{ ($unhealthyStaffCount ?? 0) > 0 ? 'overload' : '' }}" id="overloadCard">
                            <div class="card-header-row">
                                <h3>Unhealthy / Overload</h3>
                                <div class="card-icon red">⚠️</div>
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
                        <div class="summary-card" id="targetCard">
                            <div class="card-header-row">
                                <h3>Target Achievement</h3>
                                <div class="card-icon green">🎯</div>
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
                <section class="charts-section">
                    <div class="section-title-chart">Charts</div>

                    <div class="charts-container">
                        <!-- CHART 1: KPI TREND -->
                        <div class="chart-card">
                            <h3>Chart 1: KPI Trend (Q1-Q4)</h3>

                            <div class="line-chart">
                                <div class="y-axis">
                                    <span>80</span>
                                    <span>60</span>
                                    <span>40</span>
                                    <span>20</span>
                                    <span>0</span>
                                </div>

                                <div class="chart-area">
                                    <div class="grid-line line-80"></div>
                                    <div class="grid-line line-60"></div>
                                    <div class="grid-line line-40"></div>
                                    <div class="grid-line line-20"></div>
                                    <div class="grid-line line-0"></div>

                                    <svg class="trend-svg" id="trendSvg" viewBox="0 0 400 200" preserveAspectRatio="none">
                                        <polyline id="trendPolyline" points="" fill="none" stroke="var(--ilm-red, #e11d48)" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" />
                                        <g id="trendDots"></g>
                                    </svg>

                                    <div class="x-axis">
                                        <span>Q1</span>
                                        <span>Q2</span>
                                        <span>Q3</span>
                                        <span>Q4</span>
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
                <section class="badge-section">
                    <div class="badge-header">
                        <h3>🏆 Badge Ranking</h3>
                        <select class="form-select" onchange="location.href='?dept_filter=' + encodeURIComponent(this.value);">
                            <option value="WEBTECH" {{ ($selectedDeptName ?? '') == 'WEBTECH' ? 'selected' : '' }}>WEBTECH</option>
                            <option value="R&D" {{ ($selectedDeptName ?? '') == 'R&D' ? 'selected' : '' }}>R&D</option>
                            <option value="VIDEO" {{ ($selectedDeptName ?? '') == 'VIDEO' ? 'selected' : '' }}>VIDEO PRODUCTION</option>
                        </select>
                    </div>

                    <div class="badge-list" id="badgeList">
                        @forelse($badgeRankings as $index => $worker)
                        <div class="badge-user rank-{{ $index + 1 }}">
                            <div class="user-info">
                                <div class="user-avatar">👤</div>
                                <span class="name">{{ $worker->name }}</span>
                            </div>
                            <span class="rank-badge">#{{ $index + 1 }} ({{ number_format($worker->kpi_score ?? 0, 1) }}%)</span>
                        </div>
                        @empty
                        <p style="text-align: center; color: #94a3b8; padding: 15px;">Tiada data pekerja bagi jabatan ini.</p>
                        @endforelse
                    </div>
                </section>

                <!-- AI ANALYTICS -->
                <section class="ai-section">
                    <div class="ai-header">
                        <div class="section-title">
                            <span class="ai-sparkle">✦</span> AI Analytics
                        </div>
                        <span class="ai-status-badge">Live Insights</span>
                    </div>

                    <div class="ai-content-wrapper">
                        <div class="ai-content" id="aiCardContent"></div>
                    </div>

                    <div class="ai-navigation">
                        <button class="nav-btn" onclick="prevAISlide()">‹</button>
                        <div class="dot-indicators" id="aiDots"></div>
                        <button class="nav-btn" onclick="nextAISlide()">›</button>
                    </div>
                </section>

            </div>

        </section>

                <!-- DEPARTMENTS PERFORMANCE TABLE -->
            <section class="performance-section">
                <div class="performance-title">
                    <span>Departments’ Performance</span>
                    <!-- Paparkan jumlah jabatan secara dinamik -->
                    <span class="dept-count-badge" id="deptCount">
                        {{ count($departmentsPerformance ?? []) }} Departments
                    </span>
                </div>

                <div class="table-wrapper">
                    <table class="performance-table">
                        <thead>
                            <tr>
                                <th style="width: 60px;">No.</th>
                                <th style="width: 180px;">Department</th>
                                <th style="width: 130px;">Avg. KPI (%)</th>
                                <th style="width: 130px;">Total Staff</th>
                                <th style="width: 220px;">Submission Status</th>
                                <th style="width: 180px;">EOM Recipients</th>
                            </tr>
                        </thead>
                        <tbody id="performanceTableBody">
                            @forelse($departmentsPerformance as $index => $dept)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td><strong>{{ strtoupper($dept->department_name) }}</strong></td>
                                <td>{{ number_format($dept->avg_kpi, 1) }}%</td>
                                <td>{{ $dept->total_staff }} STAFF</td>
                                <td>
                                    <!-- Status penyerahan: COMPLETE (Hijau) atau PENDING (Merah/Kuning) -->
                                    <span class="status-badge {{ strtolower($dept->status) }}">
                                        • {{ $dept->status }} ({{ $dept->submitted }}/{{ $dept->total_staff }})
                                    </span>
                                </td>
                                <td>
                                    <!-- Paparan Lencana Employee of the Month (Gold & Silver) -->
                                    @if(($dept->gold_badges ?? 0) > 0)
                                        🥇 {{ $dept->gold_badges }}
                                    @endif
                                    @if(($dept->silver_badges ?? 0) > 0)
                                        | 🥈 {{ $dept->silver_badges }}
                                    @endif
                                    @if(($dept->gold_badges ?? 0) == 0 && ($dept->silver_badges ?? 0) == 0)
                                        <span style="color: #94a3b8;">-</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" style="text-align: center; color: #94a3b8; padding: 20px;">
                                    Tiada rekod prestasi jabatan ditemui.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>

    </main>

            <<!-- ================================
            JAVASCRIPT LOGIC (100% MYSQL DYNAMIC DATA)
            ================================= -->
        <script>
            // 1. NOTIFICATION DROPDOWN TOGGLE
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

            // 2. RENDER CHARTS (DYNAMIC MYSQL DATA)
            function renderCharts(data) {
                // Chart 1: KPI Trend Polyline & Dots (Q1 - Q4)
                if (data.kpiTrend && data.kpiTrend.length === 4) {
                    const xCoords = [20, 140, 260, 380];
                    
                    const points = data.kpiTrend.map((val, index) => {
                        const clampedVal = Math.min(Math.max(val, 0), 100);
                        const yCoord = 160 - ((clampedVal / 100) * 130);
                        return { x: xCoords[index], y: yCoord, val: val };
                    });

                    const pointsString = points.map(p => `${p.x},${p.y}`).join(' ');
                    const polyline = document.getElementById('trendPolyline');
                    if (polyline) polyline.setAttribute('points', pointsString);

                    const dotsGroup = document.getElementById('trendDots');
                    if (dotsGroup) {
                        dotsGroup.innerHTML = points.map(p => `
                            <circle cx="${p.x}" cy="${p.y}" r="5">
                                <title>Score: ${p.val}%</title>
                            </circle>
                        `).join('');
                    }
                }

                // Chart 2: Department Horizontal Progress Bars
                const barsContainer = document.getElementById('departmentBarsContainer');
                if (barsContainer && data.departmentScores && data.departmentScores.length > 0) {
                    barsContainer.innerHTML = data.departmentScores.map(dept => `
                        <div class="bar-row">
                            <strong>${dept.name}:</strong>
                            <div class="bar">
                                <div class="bar-fill" style="width: ${dept.score}%;"></div>
                            </div>
                            <b>${dept.score}%</b>
                        </div>
                    `).join('');
                }
            }

            // 3. AI ANALYTICS CAROUSEL SLIDER (DYNAMIC MYSQL DATA)
            let aiInsights = [];
            let currentSlide = 0;

            function renderAISlide(index) {
                const card = document.getElementById('aiCardContent');
                const dotsContainer = document.getElementById('aiDots');
                if (!card || !dotsContainer || !aiInsights || aiInsights.length === 0) return;

                const item = aiInsights[index];

                card.style.opacity = '0';
                card.style.transform = 'translateY(4px)';

                setTimeout(() => {
                    card.innerHTML = `
                        <span class="ai-tag">${item.tag}</span>
                        <div class="ai-title">${item.title}</div>
                        <div class="ai-desc">${item.desc}</div>
                        <div class="ai-stat">${item.stat}</div>
                    `;
                    
                    dotsContainer.innerHTML = aiInsights.map((_, i) => `
                        <span class="${i === index ? 'active' : ''}" onclick="goToAISlide(${i})"></span>
                    `).join('');

                    card.style.opacity = '1';
                    card.style.transform = 'translateY(0)';
                }, 150);
            }

            function nextAISlide() {
                if (!aiInsights || aiInsights.length === 0) return;
                currentSlide = (currentSlide + 1) % aiInsights.length;
                renderAISlide(currentSlide);
            }

            function prevAISlide() {
                if (!aiInsights || aiInsights.length === 0) return;
                currentSlide = (currentSlide - 1 + aiInsights.length) % aiInsights.length;
                renderAISlide(currentSlide);
            }

            function goToAISlide(index) {
                currentSlide = index;
                renderAISlide(currentSlide);
            }

            // 4. INITIALIZATION ON DOM CONTENT LOADED
            document.addEventListener('DOMContentLoaded', () => {
                // Tarik data graf terus dari Controller Laravel (MySQL)
                const liveDatabaseChartData = {
                    kpiTrend: {!! json_encode($kpiTrend ?? [0, 0, 0, 0]) !!},
                    departmentScores: {!! json_encode(collect($departmentsPerformance ?? [])->map(function($dept) {
                        return [
                            'name' => $dept->department_name,
                            'score' => $dept->avg_kpi
                        ];
                    })) !!}
                };

                // Tarik data AI Analytics terus dari Controller Laravel (MySQL)
                aiInsights = {!! json_encode($aiInsights ?? []) !!};

                // Render Graf & Slider AI jika data wujud
                renderCharts(liveDatabaseChartData);
                if (aiInsights.length > 0) {
                    renderAISlide(0);
                }
            });
        </script>

</body>

</html>