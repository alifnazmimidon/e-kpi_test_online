<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>KPI Management - E-KPI System</title>
    <link rel="stylesheet" href="{{ asset('stylekpm.css') }}">
        <script src="{{ asset('session.js') }}?v=8"></script>
</head>

<body>

    <a class="skip-link" href="#main">Skip to main content</a>

    <!-- SVG ICON SPRITE -->
    <svg width="0" height="0" style="position:absolute" aria-hidden="true" focusable="false">
        <symbol id="i-sliders" viewBox="0 0 24 24"><path d="M4 21v-7M4 10V3M12 21v-9M12 8V3M20 21v-5M20 12V3M1 14h6M9 8h6M17 16h6"/></symbol>
        <symbol id="i-award" viewBox="0 0 24 24"><circle cx="12" cy="8" r="6"/><path d="M15.5 13.5 17 22l-5-3-5 3 1.5-8.5"/></symbol>
        <symbol id="i-target" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><circle cx="12" cy="12" r="6"/><circle cx="12" cy="12" r="2"/></symbol>
        <symbol id="i-save" viewBox="0 0 24 24"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><path d="M17 21v-8H7v8M7 3v5h8"/></symbol>
        <symbol id="i-reset" viewBox="0 0 24 24"><path d="M3 12a9 9 0 1 0 3-6.7L3 8"/><path d="M3 3v5h5"/></symbol>
        <symbol id="i-loader" viewBox="0 0 24 24"><path d="M21 12a9 9 0 1 1-6.2-8.6"/></symbol>
        <symbol id="i-check" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="m9 12 2 2 4-4"/></symbol>
        <symbol id="i-clock" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></symbol>
        <symbol id="i-minus" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M8 12h8"/></symbol>
        <symbol id="i-alert-circle" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M12 8v4M12 16h.01"/></symbol>
        <symbol id="i-x" viewBox="0 0 24 24"><path d="M18 6 6 18M6 6l12 12"/></symbol>
        <symbol id="i-building" viewBox="0 0 24 24"><rect x="4" y="2" width="16" height="20" rx="2"/><path d="M9 22v-4h6v4M8 6h.01M12 6h.01M16 6h.01M8 10h.01M12 10h.01M16 10h.01M8 14h.01M12 14h.01M16 14h.01"/></symbol>
        <symbol id="i-user" viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></symbol>
        <symbol id="i-dashboard" viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="9" rx="1"/><rect x="14" y="3" width="7" height="5" rx="1"/><rect x="14" y="12" width="7" height="9" rx="1"/><rect x="3" y="16" width="7" height="5" rx="1"/></symbol>        
        <symbol id="i-logout" viewBox="0 0 24 24"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4M16 17l5-5-5-5M21 12H9"/></symbol>
    </svg>

    <!-- ================================ HEADER ================================ -->
    <header class="header">
        <div class="header-left">
            <div class="ilm-logo"><img src="{{ asset('assets/ILMgroups-logo-4.png') }}" alt="ILM Groups logo"></div>
            <div class="header-line" aria-hidden="true"></div>
            <span class="system-title">E-KPI SYSTEM</span>
        </div>

        <div class="header-right">
            <!-- NOTIFIKASI -->
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

    <!-- ============================== NAVIGATION ============================== -->
    <nav class="navigation reveal" style="--i:1" aria-label="Main navigation">
        <a href="{{ route('hr.dashboard') }}" class="nav-link">Dashboard</a>
        <a href="{{ route('hr.kpi') }}" class="nav-link active" aria-current="page">KPI Management</a>
        <a href="{{ route('hr.report') }}" class="nav-link">Report</a>
        <a href="{{ route('hr.emerleave') }}" class="nav-link">Emergency Leave</a>
    </nav>

<!-- ============================== MAIN CONTENT ============================== -->
<main class="kpi-management-page" id="main">

    @if (session('success'))
        <div class="alert alert-success" role="alert">
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger" role="alert">
            <ul style="margin: 0; padding-left: 1.25rem;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <section class="configuration-area">
            <!-- ========== WEIGHTAGE FORMULA CONFIGURATION ========== -->
            <div class="configuration-card reveal" style="--i:2">
                <div class="configuration-header">
                    <svg class="icon" aria-hidden="true"><use href="#i-sliders"/></svg>
                    <h2>Weightage Formula Configuration</h2>
                    <span class="unsaved-pill" id="formulaUnsaved" hidden>Unsaved changes</span>
                </div>

                <form action="{{ route('hr.updateFormula') }}" method="POST">
                    @csrf
                    <div class="configuration-body">

                        <div class="switch-row">
                            <div class="switch-text">
                                <strong id="autoBalanceLabel">Auto-balance to 100%</strong>
                                <small>Moving one slider adjusts the other two proportionally.</small>
                            </div>
                            <label class="switch">
                                <input type="checkbox" id="autoBalance" role="switch" aria-labelledby="autoBalanceLabel" checked>
                                <span class="switch-track"></span>
                            </label>
                        </div>

                        <!-- E-TRACKER -->
                        <div class="slider-row">
                            <div class="slider-label">
                                <label for="etrackerWeight"><span class="swatch sw1"></span>E-tracker tasks weightage</label>
                            </div>
                            <div class="slider-control">
                                <input type="range" 
                                       id="etrackerWeight" 
                                       name="tasks_weightage" 
                                       min="0" max="100" step="1" 
                                       value="{{ $setting->tasks_weightage ?? 40 }}" 
                                       class="weight-slider" data-out="etrackerValue">
                                <output id="etrackerValue" for="etrackerWeight" class="slider-value">{{ $setting->tasks_weightage ?? 40 }}%</output>
                            </div>
                        </div>

                        <!-- ATTENDANCE -->
                        <div class="slider-row">
                            <div class="slider-label">
                                <label for="attendanceWeight"><span class="swatch sw2"></span>Attendance System Weightage</label>
                            </div>
                            <div class="slider-control">
                                <input type="range" 
                                       id="attendanceWeight" 
                                       name="attendance_weightage" 
                                       min="0" max="100" step="1" 
                                       value="{{ $setting->attendance_weightage ?? 20 }}" 
                                       class="weight-slider" data-out="attendanceValue">
                                <output id="attendanceValue" for="attendanceWeight" class="slider-value">{{ $setting->attendance_weightage ?? 20 }}%</output>
                            </div>
                        </div>

                        <!-- LEADER RATING -->
                        <div class="slider-row">
                            <div class="slider-label">
                                <label for="leaderWeight"><span class="swatch sw3"></span>Leader rating Weightage</label>
                            </div>
                            <div class="slider-control">
                                <input type="range" 
                                       id="leaderWeight" 
                                       name="leader_weightage" 
                                       min="0" max="100" step="1" 
                                       value="{{ $setting->leader_weightage ?? 40 }}" 
                                       class="weight-slider" data-out="leaderValue">
                                <output id="leaderValue" for="leaderWeight" class="slider-value">{{ $setting->leader_weightage ?? 40 }}%</output>
                            </div>
                        </div>

                        <!-- VISUAL DISTRIBUTION BAR -->
                        <div class="dist-bar" id="distBar" role="img" aria-label="Weight distribution">
                            <span class="seg1"></span><span class="seg2"></span><span class="seg3"></span>
                        </div>

                        <div class="total-weight-box" aria-live="polite">
                            <span>Total Weightage: <strong id="totalWeightVal">100%</strong></span>
                            <span class="total-hint" id="totalHint">Balanced</span>
                        </div>

                        <div class="spacer"></div>

                        <div class="button-row">
                            <button type="button" id="resetFormula" class="reset-button">
                                <svg class="icon" aria-hidden="true"><use href="#i-reset"/></svg>Reset
                            </button>
                            <button type="submit" id="saveFormula" class="save-button">
                                <svg class="icon btn-icon" aria-hidden="true"><use href="#i-save"/></svg>
                                <span>Save Formula Rules</span>
                            </button>
                        </div>
                        <p id="formulaSaved" class="saved-message" role="status"></p>
                    </div>
                </form>
            </div>

            <!-- ========== EOM BADGE THRESHOLD LOGIC ========== -->
            <div class="configuration-card reveal" style="--i:3">
                <div class="configuration-header">
                    <svg class="icon" aria-hidden="true"><use href="#i-award"/></svg>
                    <h2>EOM Badge Threshold Logic</h2>
                    <span class="unsaved-pill" id="thresholdUnsaved" hidden>Unsaved changes</span>
                </div>

                <form action="{{ route('hr.updateThresholds') }}" method="POST">
                    @csrf
                    <div class="configuration-body">

                        <div class="switch-row">
                            <div class="switch-text">
                                <strong>Auto-enforce tier order (Gold &gt; Silver &gt; Bronze)</strong>
                                <small>Moving one slider adjusts adjacent badge tiers automatically.</small>
                            </div>
                            <span class="total-hint" style="white-space: nowrap;">
                                <svg class="icon" aria-hidden="true"><use href="#i-check"/></svg>Active
                            </span>
                        </div>

                        <!-- GOLD -->
                        <div class="slider-row">
                            <div class="slider-label"><label for="goldThreshold">Gold badge target score</label></div>
                            <div class="slider-control">
                                <input type="range" 
                                       id="goldThreshold" 
                                       name="gold_threshold" 
                                       min="0" max="100" step="1" 
                                       value="{{ (int)($setting->gold_threshold ?? 90) }}" 
                                       class="badge-slider gold-slider" data-out="goldValue">
                                <output id="goldValue" for="goldThreshold" class="slider-value gold-value">{{ (int)($setting->gold_threshold ?? 90) }}%</output>
                            </div>
                        </div>

                        <!-- SILVER -->
                        <div class="slider-row">
                            <div class="slider-label"><label for="silverThreshold">Silver badge target score</label></div>
                            <div class="slider-control">
                                <input type="range" 
                                       id="silverThreshold" 
                                       name="silver_threshold" 
                                       min="0" max="100" step="1" 
                                       value="{{ (int)($setting->silver_threshold ?? 85) }}" 
                                       class="badge-slider silver-slider" data-out="silverValue">
                                <output id="silverValue" for="silverThreshold" class="slider-value silver-value">{{ (int)($setting->silver_threshold ?? 85) }}%</output>
                            </div>
                        </div>

                        <!-- BRONZE -->
                        <div class="slider-row">
                            <div class="slider-label"><label for="bronzeThreshold">Bronze badge target score</label></div>
                            <div class="slider-control">
                                <input type="range" 
                                       id="bronzeThreshold" 
                                       name="bronze_threshold" 
                                       min="0" max="100" step="1" 
                                       value="{{ (int)($setting->bronze_threshold ?? 80) }}" 
                                       class="badge-slider bronze-slider" data-out="bronzeValue">
                                <output id="bronzeValue" for="bronzeThreshold" class="slider-value bronze-value">{{ (int)($setting->bronze_threshold ?? 80) }}%</output>
                            </div>
                        </div>

                        <!-- VISUAL RANGE BAND -->
                        <div class="band" id="band" aria-hidden="true">
                            <i class="b-bronze" id="bandBronze"></i>
                            <i class="b-silver" id="bandSilver"></i>
                            <i class="b-gold" id="bandGold"></i>
                        </div>
                        <div class="band-legend" id="bandLegend" aria-live="polite"></div>

                        <div class="threshold-info-box" aria-live="polite">
                            <span>Minimum Qualification:</span>
                            <strong id="minQualification" style="color: #ea580c;">Bronze ({{ (int)($setting->bronze_threshold ?? 80) }}%)</strong>
                        </div>

                        <div class="spacer"></div>

                        <div class="button-row">
                            <button type="button" id="resetThreshold" class="reset-button">
                                <svg class="icon" aria-hidden="true"><use href="#i-reset"/></svg>Reset
                            </button>
                            <button type="submit" id="saveThreshold" class="save-button">
                                <svg class="icon btn-icon" aria-hidden="true"><use href="#i-save"/></svg>
                                <span>Save Threshold</span>
                            </button>
                        </div>
                        <p id="thresholdSaved" class="saved-message" role="status"></p>
                    </div>
                </form>
            </div>

        </section>

        <!-- ========== QUARTERLY DEPARTMENT TARGET ASSIGNMENT ========== -->
        <section class="department-target-section reveal" style="--i:4" aria-labelledby="targetTitle">
            <div class="section-header">
                <div class="title-wrap">
                    <svg class="icon" aria-hidden="true" style="font-size:20px;color:var(--ilm-red, #dc2626);"><use href="#i-target"/></svg>
                    <h2 class="section-title" id="targetTitle">Quarterly Department Target Assignment</h2>
                </div>
                
                @php
                    $curQ = 'Q' . (int) ceil(date('n') / 3);
                    $activeQ = request('quarter', $selectedQuarter ?? $curQ);
                @endphp
                <select id="quarterSelect" 
                        class="quarter-dropdown" 
                        aria-label="Select quarter" 
                        onchange="window.location.href='{{ route('hr.kpi') }}?quarter=' + this.value">
                    @foreach(['Q1' => 'Quarter 1', 'Q2' => 'Quarter 2', 'Q3' => 'Quarter 3', 'Q4' => 'Quarter 4'] as $qKey => $qLabel)
                        <option value="{{ $qKey }}" {{ $activeQ == $qKey ? 'selected' : '' }}>
                            {{ $qLabel }}{{ $qKey === $curQ ? ' (Current)' : '' }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="table-container" tabindex="0" role="region" aria-labelledby="targetTitle">
                <form action="{{ route('hr.updateTarget') }}" method="POST">
                    @csrf
                    <input type="hidden" name="quarter" value="{{ $activeQ }}">

                    <table class="department-table">
                        <thead>
                            <tr>
                                <th scope="col" style="width: 25%;">Department</th>
                                <th scope="col" style="width: 25%;">Target Score (%)</th>
                                <th scope="col" style="width: 20%;">Assigned By</th>
                                <th scope="col" style="width: 15%;">Status</th>
                                <th scope="col" style="width: 15%; text-align: center;">Action</th>
                            </tr>
                        </thead>
                        <tbody id="departmentTargetBody">
                            @foreach($departments as $index => $dept)
                                @php
                                    $statusKey = strtolower($dept->status ?? 'assigned');
                                    $iconName = match($statusKey) {
                                        'assigned' => 'check',
                                        'pending' => 'clock',
                                        default => 'minus'
                                    };
                                @endphp
                                <tr style="--i:{{ $index }}">
                                    <td><strong>{{ $dept->department_name }}</strong></td>
                                    <td>
                                        <div class="target-input-group">
                                            <input type="number" 
                                                   name="targets[{{ $dept->department_name }}]" 
                                                   value="{{ $dept->target_score }}" 
                                                   min="0" max="100" step="0.5" 
                                                   class="target-input form-control"
                                                   aria-label="Target score for {{ $dept->department_name }}">
                                            <span aria-hidden="true">%</span>
                                        </div>
                                    </td>
                                    <td>{{ $dept->assigned_by ?? 'HR Manager' }}</td>
                                    <td>
                                        <span class="status-badge {{ $statusKey }}">
                                            <svg class="icon" aria-hidden="true"><use href="#i-{{ $iconName }}"/></svg>
                                            {{ $dept->status ?? 'Assigned' }}
                                        </span>
                                    </td>
                                    <td style="text-align: center;">
                                        <button type="submit" 
                                                name="department_name" 
                                                value="{{ $dept->department_name }}" 
                                                class="save-button btn-update" style="padding: 6px 16px;">
                                            <svg class="icon btn-icon" aria-hidden="true"><use href="#i-save"/></svg>
                                            <span>Save</span>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </form>
            </div>
        </section>

    </main>

    <div class="toast-region" id="toastRegion" aria-live="polite"></div>

    <!-- =====================================================
         JAVASCRIPT INTEGRATED & ENHANCED
    ===================================================== -->
    <script>
    const $ = id => document.getElementById(id);
    const clamp = (v, lo, hi) => Math.min(Math.max(v, lo), hi);
    const icon = (name, cls = '') => `<svg class="icon ${cls}" aria-hidden="true"><use href="#i-${name}"/></svg>`;

   

    /* =====================================================
       2. WEIGHTAGE FORMULA & AUTO-BALANCE LOGIC
    ===================================================== */
    const wEls = ['etrackerWeight', 'attendanceWeight', 'leaderWeight'].map($);
    const tEls = ['goldThreshold', 'silverThreshold', 'bronzeThreshold'].map($);

    // Nilai awal dari Blade backend
    let savedW = wEls.map(el => parseInt(el.value, 10) || 0);
    let savedT = tEls.map(el => parseInt(el.value, 10) || 0);

    function paintSlider(el) {
        const p = (el.value - el.min) / (el.max - el.min) * 100;
        el.style.setProperty('--progress', p + '%');
        el.setAttribute('aria-valuetext', el.value + ' percent');
        const out = $(el.dataset.out);
        if (out) out.textContent = el.value + '%';
    }

    const getW = () => wEls.map(e => parseInt(e.value, 10) || 0);
    const getT = () => tEls.map(e => parseInt(e.value, 10) || 0);
    const setW = arr => wEls.forEach((e, i) => { e.value = arr[i]; paintSlider(e); });
    const setT = arr => tEls.forEach((e, i) => { e.value = arr[i]; paintSlider(e); });

    function onWeightInput(i) {
        if ($('autoBalance').checked) {
            const rest = 100 - (parseInt(wEls[i].value, 10) || 0);
            const [a, b] = [0, 1, 2].filter(j => j !== i);
            const va = parseInt(wEls[a].value, 10) || 0, vb = parseInt(wEls[b].value, 10) || 0;
            const s = va + vb;
            const na = s === 0 ? Math.ceil(rest / 2) : Math.round(va / s * rest);
            wEls[a].value = na; 
            wEls[b].value = rest - na;
        }
        wEls.forEach(paintSlider);
        renderWeights();
    }

    function renderWeights() {
        const w = getW(), total = w[0] + w[1] + w[2];
        const valid = total === 100, dirty = w.join() !== savedW.join();

        const tv = $('totalWeightVal');
        tv.textContent = total + '%';
        tv.className = valid ? '' : 'invalid';

        const hint = $('totalHint');
        hint.className = 'total-hint' + (valid ? '' : ' invalid');
        hint.innerHTML = valid ? icon('check') + 'Balanced'
            : icon('alert-circle') + (total > 100 ? `Over by ${total - 100}%` : `${100 - total}% left to assign`);

        const bar = $('distBar');
        if (bar) {
            bar.style.gridTemplateColumns = w.map(v => v + 'fr').join(' ');
            [...bar.children].forEach((s, i) => { s.textContent = w[i] >= 12 ? w[i] + '%' : ''; });
            bar.setAttribute('aria-label', `Weight distribution: E-tracker ${w[0]}%, Attendance ${w[1]}%, Leader rating ${w[2]}%`);
        }

        $('saveFormula').disabled = !valid;
        $('resetFormula').disabled = !dirty;
        if ($('formulaUnsaved')) $('formulaUnsaved').hidden = !dirty;
    }

    /* =====================================================
       3. EOM BADGE THRESHOLDS LOGIC
    ===================================================== */
    function onThresholdInput(i) {
        const before = getT();
        let [g, s, b] = before;
        if (i === 0) { g = clamp(g, 2, 100); s = Math.min(s, g - 1); b = Math.min(b, s - 1); }
        if (i === 1) { s = clamp(s, 1, 99);  g = Math.max(g, s + 1); b = Math.min(b, s - 1); }
        if (i === 2) { b = clamp(b, 0, 98);  s = Math.max(s, b + 1); g = Math.max(g, s + 1); }
        setT([g, s, b]);
        renderThresholds();
    }

    function renderThresholds() {
        const [g, s, b] = getT();
        const dirty = getT().join() !== savedT.join();

        const seg = (id, from, to) => {
            const el = $(id);
            if (el) {
                el.style.setProperty('--from', from / 100);
                el.style.setProperty('--span', Math.max(to - from, 0) / 100);
            }
        };
        seg('bandBronze', b, s); 
        seg('bandSilver', s, g); 
        seg('bandGold', g, 100);

        const chip = (c, t) => `<span style="--c:${c}">${t}</span>`;
        if ($('bandLegend')) {
            $('bandLegend').innerHTML =
                chip('#d97706', `Gold ${g}–100%`) +
                chip('#64748b', `Silver ${s}–${g - 1}%`) +
                chip('#ea580c', `Bronze ${b}–${s - 1}%`) +
                (b > 0 ? chip('#cbd5e1', `No badge 0–${b - 1}%`) : '');
        }

        if ($('minQualification')) $('minQualification').textContent = `Bronze (${b}%)`;
        $('resetThreshold').disabled = !dirty;
        if ($('thresholdUnsaved')) $('thresholdUnsaved').hidden = !dirty;
    }

    /* =====================================================
       4. INISIALISASI PADA INITIAL LOAD
    ===================================================== */
    document.addEventListener('DOMContentLoaded', () => {
        wEls.forEach((el, i) => {
            paintSlider(el);
            el.addEventListener('input', () => onWeightInput(i));
        });

        tEls.forEach((el, i) => {
            paintSlider(el);
            el.addEventListener('input', () => onThresholdInput(i));
        });

        $('autoBalance').addEventListener('change', () => {
            wEls.forEach(paintSlider);
            renderWeights();
        });

        $('resetFormula').addEventListener('click', () => { 
            setW(savedW); 
            renderWeights(); 
        });

        $('resetThreshold').addEventListener('click', () => { 
            setT(savedT); 
            renderThresholds(); 
        });

        renderWeights();
        renderThresholds();
    });
  let isSubmitting = false;

document.querySelectorAll('form').forEach(form => {
    form.addEventListener('submit', () => {
        isSubmitting = true;
    });
});

function isVisible(el) {
    return Boolean(
        el &&
        !el.hidden &&
        !el.classList.contains('hidden') &&
        el.style.display !== 'none'
    );
}

window.addEventListener('beforeunload', (e) => {
    if (isSubmitting) return;

    const formulaDirty = isVisible(document.getElementById('formulaUnsaved'));
    const thresholdDirty = isVisible(document.getElementById('thresholdUnsaved'));

    if (formulaDirty || thresholdDirty) {
        e.preventDefault();
        e.returnValue = '';
    }
});
    </script>
</body>
</html>