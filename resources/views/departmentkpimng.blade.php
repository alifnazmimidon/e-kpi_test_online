<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Department KPI Management - E-KPI System</title>
    <link rel="stylesheet" href="{{ asset('styledeptkpm.css') }}">
    <script src="{{ asset('session.js') }}?v=8"></script>
</head>
<body>

    <a class="skip-link" href="#main">Skip to main content</a>

    <!-- SVG ICON SPRITE -->
    <svg width="0" height="0" style="position:absolute" aria-hidden="true" focusable="false">
        <symbol id="i-refresh" viewBox="0 0 24 24"><path d="M21 12a9 9 0 0 0-15-6.7L3 8"/><path d="M3 3v5h5M3 12a9 9 0 0 0 15 6.7L21 16"/><path d="M16 16h5v5"/></symbol>
        <symbol id="i-sliders" viewBox="0 0 24 24"><path d="M4 21v-7M4 10V3M12 21v-9M12 8V3M20 21v-5M20 12V3M1 14h6M9 8h6M17 16h6"/></symbol>
        <symbol id="i-user" viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></symbol>
        <symbol id="i-users" viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/></symbol>
        <symbol id="i-save" viewBox="0 0 24 24"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><path d="M17 21v-8H7v8M7 3v5h8"/></symbol>
        <symbol id="i-send" viewBox="0 0 24 24"><path d="m22 2-7 20-4-9-9-4Z"/><path d="M22 2 11 13"/></symbol>
        <symbol id="i-check" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="m9 12 2 2 4-4"/></symbol>
        <symbol id="i-clock" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></symbol>
        <symbol id="i-alert-circle" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M12 8v4M12 16h.01"/></symbol>
        <symbol id="i-alert" viewBox="0 0 24 24"><path d="M10.3 3.9 1.8 18a2 2 0 0 0 1.7 3h17a2 2 0 0 0 1.7-3L13.7 3.9a2 2 0 0 0-3.4 0z"/><path d="M12 9v4M12 17h.01"/></symbol>
        <symbol id="i-info" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M12 16v-4M12 8h.01"/></symbol>
        <symbol id="i-x" viewBox="0 0 24 24"><path d="M18 6 6 18M6 6l12 12"/></symbol>
        <symbol id="i-loader" viewBox="0 0 24 24"><path d="M21 12a9 9 0 1 1-6.2-8.6"/></symbol>
        <symbol id="i-cpu" viewBox="0 0 24 24"><rect width="16" height="16" x="4" y="4" rx="2"/><rect width="6" height="6" x="9" y="9" rx="1"/><path d="M15 2v2M15 20v2M2 15h2M2 9h2M20 15h2M20 9h2M9 2v2M9 20v2"/></symbol>
        <symbol id="i-medal" viewBox="0 0 24 24"><path d="M7.2 15 2.7 7.1a2 2 0 0 1 .1-2.2L4.4 2.8A2 2 0 0 1 6 2h12a2 2 0 0 1 1.6.8l1.6 2.1a2 2 0 0 1 .1 2.2L16.8 15"/><path d="M11 12 5.1 2.2M13 12l5.9-9.8M8 7h8"/><circle cx="12" cy="17" r="5"/></symbol>
        <symbol id="i-lock" viewBox="0 0 24 24"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></symbol>
        <symbol id="i-edit" viewBox="0 0 24 24"><path d="M12 20h9"/><path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4z"/></symbol>
        <symbol id="i-chev-l" viewBox="0 0 24 24"><path d="m15 18-6-6 6-6"/></symbol>
        <symbol id="i-chev-r" viewBox="0 0 24 24"><path d="m9 18 6-6-6-6"/></symbol>
        <symbol id="i-arrow-right" viewBox="0 0 24 24"><path d="M5 12h14M12 5l7 7-7 7"/></symbol>
        <symbol id="i-dashboard" viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="9" rx="1"/><rect x="14" y="3" width="7" height="5" rx="1"/><rect x="14" y="12" width="7" height="9" rx="1"/><rect x="3" y="16" width="7" height="5" rx="1"/></symbol>        
        <symbol id="i-logout" viewBox="0 0 24 24"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4M16 17l5-5-5-5M21 12H9"/></symbol>
    </svg>

    @php
        $leaderInfo  = $seedData['leader'] ?? [];
        $leaderName  = $leaderInfo['name'] ?? (Auth::user()->full_name ?? 'Leader');
        $leaderTitle = $leaderInfo['title'] ?? (Auth::user()->designation ?? 'Team Lead');
        $leaderPhoto = $leaderInfo['photo'] ?? '';
    @endphp

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
                    <div class="notification-list" id="notificationList" aria-live="polite"></div>
                </div>
            </div>

            <div class="profile-wrapper">
                <button type="button" class="header-icon profile-btn" id="profileBtn" aria-label="User profile menu"
                        aria-haspopup="true" aria-expanded="false" aria-controls="profileDropdown">
                    <div class="profile-avatar-wrapper" id="headerAvatarContainer">
                        <svg id="defaultAvatarSvg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#ffffff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" style="{{ $leaderPhoto ? 'display: none;' : '' }}">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                            <circle cx="12" cy="7" r="4"></circle>
                        </svg>
                        <img @if($leaderPhoto) src="{{ $leaderPhoto }}" @endif id="headerUserProfilePic" alt="" class="profile-avatar-img" style="{{ $leaderPhoto ? 'display: block;' : 'display: none;' }}" onerror="showAvatarFallback()">
                    </div>
                </button>

                <div class="profile-dropdown" id="profileDropdown">
                    <div class="profile-dropdown-header">
                        <span class="user-name" id="dropdownUserName">{{ $leaderName }}</span>
                        <span class="user-role" id="dropdownUserRole">{{ $leaderTitle }}</span>
                    </div>
                    <ul class="profile-dropdown-menu">
                        <li><a href="{{ route('worker.dashboard') }}"><svg class="icon" aria-hidden="true" style="font-size:15px"><use href="#i-user"/></svg>View Profile</a></li>
                        <li><a href="{{ route('leader.dashboard') }}"><svg class="icon" aria-hidden="true" style="font-size:15px"><use href="#i-dashboard"/></svg>Dashboard</a></li>
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
        <a href="{{ route('leader.dashboard') }}" class="nav-link">Dashboard</a>
        <a href="{{ route('leader.kpi') }}" class="nav-link active" aria-current="page">Input Rating</a>
        <a href="{{ Route::has('leader.team.status') ? route('leader.team.status') : url('/leader/team-status') }}" class="nav-link">Team Status</a>
        <a href="{{ route('leader.reports') }}" class="nav-link {{ request()->routeIs('leader.reports') ? 'active' : '' }}">Reports</a>

        <div class="sync-status reveal" style="--i:1" id="syncStatus">
            <span class="sync-dot" aria-hidden="true"></span>
            <span id="syncText" role="status" aria-live="polite">Synced just now</span>
            <button type="button" class="sync-btn" id="syncBtn" aria-label="Refresh data" title="Refresh data">
                <svg class="icon" aria-hidden="true"><use href="#i-refresh"/></svg>
            </button>
        </div>
    </nav>

    <!-- ============================== MAIN CONTENT ============================== -->
    <main class="dept-kpi-page" id="main">

        <!-- TEAM PROGRESS -->
        <section class="team-progress reveal" style="--i:2" aria-label="Team evaluation progress">
            <div class="tp-top">
                <span class="tp-title"><svg class="icon" aria-hidden="true" style="vertical-align:-2px;margin-right:6px"><use href="#i-users"/></svg>TEAM EVALUATION PROGRESS</span>
                <span class="tp-count" id="tpCount">0 of 0 submitted</span>
                <div class="tp-chips">
                    <span class="tp-chip complete"><svg class="icon" aria-hidden="true"><use href="#i-check"/></svg><span id="tpSubmitted">0</span> Submitted</span>
                    <span class="tp-chip draft"><svg class="icon" aria-hidden="true"><use href="#i-edit"/></svg><span id="tpDraft">0</span> Draft</span>
                    <span class="tp-chip pending"><svg class="icon" aria-hidden="true"><use href="#i-clock"/></svg><span id="tpPending">0</span> Pending</span>
                </div>
            </div>
            <div class="tp-track" role="progressbar" aria-label="Evaluations submitted" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0" id="tpTrack"><div class="tp-bar" id="tpBar"></div></div>
            <button type="button" class="btn-next" id="btnNext">
                <span id="btnNextLabel">NEXT TO EVALUATE</span><svg class="icon" aria-hidden="true"><use href="#i-arrow-right"/></svg>
            </button>
        </section>

        <section class="kpi-config-container reveal" style="--i:3">

            <!-- CARD HEADER -->
            <div class="card-top-header">
                <div class="card-title-group">
                    <svg class="icon" aria-hidden="true"><use href="#i-sliders"/></svg>
                    <h2>WEIGHTAGE FORMULA CONFIGURATION</h2>
                </div>
                <div class="member-selector-wrap">
                    <label for="memberSelect">SELECT TEAM MEMBER :</label>
                    <button type="button" class="member-nav" id="memberPrev" aria-label="Previous team member"><svg class="icon" aria-hidden="true"><use href="#i-chev-l"/></svg></button>
                    <select id="memberSelect" class="member-dropdown">
                        @foreach($seedData['members'] ?? [] as $m)
                            <option value="{{ $m['id'] }}">{{ strtoupper($m['name']) }}{{ ($m['status'] ?? '') === 'SUBMITTED' ? ' (submitted)' : '' }}</option>
                        @endforeach
                    </select>
                    <button type="button" class="member-nav" id="memberNext" aria-label="Next team member"><svg class="icon" aria-hidden="true"><use href="#i-chev-r"/></svg></button>
                </div>
            </div>

            <!-- SECTION 1: AUTOMATED SYSTEM INPUT -->
            <div class="config-section">
                <div class="section-sub-header">
                    <svg class="icon" aria-hidden="true"><use href="#i-cpu"/></svg>
                    <h3>AUTOMATED SYSTEM INPUT (READ-ONLY) :</h3>
                </div>

                <div class="system-scores-grid">
                    <div class="score-input-item">
                        <div class="score-row">
                            <span class="score-label">TASK SCORE :</span>
                            <div class="readonly-badge"><span id="taskScoreVal">0.0</span> / 40%</div>
                        </div>
                        <div class="mini-meter" aria-hidden="true"><i id="taskMeter"></i></div>
                    </div>
                    <div class="score-input-item">
                        <div class="score-row">
                            <span class="score-label">ATTENDANCE SYSTEM SCORE :</span>
                            <div class="readonly-badge"><span id="attendanceScoreVal">0.0</span> / 20%</div>
                        </div>
                        <div class="mini-meter" aria-hidden="true"><i id="attMeter"></i></div>
                    </div>
                </div>
            </div>

            <!-- SECTION 2: LEADER EVALUATION INPUT -->
            <div class="config-section border-top">
                <div class="section-sub-header">
                    <svg class="icon" aria-hidden="true"><use href="#i-user"/></svg>
                    <h3>LEADER EVALUATION INPUT (40% MAXIMUM)</h3>
                    <span class="status-pill-sub" id="evaluationStatus"></span>
                </div>

                <div class="leader-eval-form" id="evalForm">

                    <div class="lock-note" id="lockNote" hidden>
                        <svg class="icon" aria-hidden="true"><use href="#i-lock"/></svg>
                        <span id="lockNoteText"></span>
                    </div>

                    <div class="eval-row" data-row="1">
                        <label for="eval1">1. WORK QUALITY & OUTPUT ACCURACY :</label>
                        <input type="range" class="eval-slider" id="slide1" min="0" max="10" step="0.5" value="0" aria-label="Work quality score slider" tabindex="-1">
                        <div class="input-score-box"><input type="number" id="eval1" min="0" max="10" step="0.5" class="score-field" placeholder="-" inputmode="decimal" aria-describedby="err1"><span class="max-denom">/ 10</span></div>
                        <span class="row-error" id="err1" role="alert" hidden></span>
                    </div>

                    <div class="eval-row" data-row="2">
                        <label for="eval2">2. PROBLEM SOLVING & TECHNICAL SKILL :</label>
                        <input type="range" class="eval-slider" id="slide2" min="0" max="10" step="0.5" value="0" aria-label="Problem solving score slider" tabindex="-1">
                        <div class="input-score-box"><input type="number" id="eval2" min="0" max="10" step="0.5" class="score-field" placeholder="-" inputmode="decimal" aria-describedby="err2"><span class="max-denom">/ 10</span></div>
                        <span class="row-error" id="err2" role="alert" hidden></span>
                    </div>

                    <div class="eval-row" data-row="3">
                        <label for="eval3">3. TEAMWORK & COMMUNICATION :</label>
                        <input type="range" class="eval-slider" id="slide3" min="0" max="10" step="0.5" value="0" aria-label="Teamwork score slider" tabindex="-1">
                        <div class="input-score-box"><input type="number" id="eval3" min="0" max="10" step="0.5" class="score-field" placeholder="-" inputmode="decimal" aria-describedby="err3"><span class="max-denom">/ 10</span></div>
                        <span class="row-error" id="err3" role="alert" hidden></span>
                    </div>

                    <div class="eval-row" data-row="4">
                        <label for="eval4">4. INITIATIVE & DISCIPLINE :</label>
                        <input type="range" class="eval-slider" id="slide4" min="0" max="10" step="0.5" value="0" aria-label="Initiative score slider" tabindex="-1">
                        <div class="input-score-box"><input type="number" id="eval4" min="0" max="10" step="0.5" class="score-field" placeholder="-" inputmode="decimal" aria-describedby="err4"><span class="max-denom">/ 10</span></div>
                        <span class="row-error" id="err4" role="alert" hidden></span>
                    </div>

                    <div class="eval-summary-row margin-top">
                        <span class="summary-label">TOTAL LEADER RATING SCORE :</span>
                        <div class="summary-badge"><strong id="totalLeaderScore">0.0</strong> / 40</div>
                    </div>

                    <div class="eval-summary-row highlight">
                        <span class="summary-label">OVERALL PROJECTED SCORE :</span>
                        <div class="overall-badge" id="overallBadgeBox">
                            <strong id="overallScoreVal">0.0%</strong>
                            <span class="badge-icon-tag" id="badgeIconTag"></span>
                        </div>
                    </div>

                    <div>
                        <div class="stack-bar" id="stackBar" role="img" aria-label="Score composition"><span class="seg1"></span><span class="seg2"></span><span class="seg3"></span><span class="seg4"></span></div>
                        <div class="stack-legend" id="stackLegend"></div>
                    </div>

                </div>
            </div>

            <!-- ACTION BUTTONS -->
            <div class="action-footer-row">
                <span class="submit-hint" id="submitHint" aria-live="polite"></span>
                <div class="footer-buttons">
                    <button type="button" class="btn-action btn-secondary" id="btnSaveDraft">
                        <svg class="icon btn-icon" aria-hidden="true"><use href="#i-save"/></svg>
                        <svg class="icon btn-spinner" aria-hidden="true"><use href="#i-loader"/></svg>
                        <span>SAVE DRAFT</span>
                    </button>
                    <button type="button" class="btn-action btn-primary" id="btnSubmitFinal">
                        <svg class="icon btn-icon" aria-hidden="true"><use id="submitIcon" href="#i-send"/></svg>
                        <svg class="icon btn-spinner" aria-hidden="true"><use href="#i-loader"/></svg>
                        <span id="submitLabel">SUBMIT FINAL</span>
                    </button>
                </div>
            </div>

        </section>

    </main>

    <!-- CONFIRM DIALOG -->
    <dialog class="dept-dialog" id="confirmDialog" aria-labelledby="confirmTitle" aria-describedby="confirmDesc">
        <form method="dialog">
            <div class="confirm-body">
                <div class="confirm-icon" id="confirmIconBox"><svg class="icon" aria-hidden="true"><use id="confirmIconUse" href="#i-send"/></svg></div>
                <div><h3 id="confirmTitle"></h3><p id="confirmDesc"></p></div>
            </div>
            <div class="modal-footer">
                <button type="submit" value="cancel" class="btn-modal-secondary" id="confirmCancel">Cancel</button>
                <button type="submit" value="confirm" class="btn-modal-primary" id="confirmOk">Confirm</button>
            </div>
        </form>
    </dialog>

    <div class="toast-region" id="toastRegion" aria-live="polite"></div>

    <!-- LARAVEL MYSQL SEED DATA -->
    <script type="application/json" id="laravelInputRatingSeed">
        @json($seedData)
    </script>

    <script>
    /* =====================================================
       HELPERS
    ===================================================== */
    const $ = id => document.getElementById(id);
    const prefersReduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    const icon = (name, cls = '') => `<svg class="icon ${cls}" aria-hidden="true"><use href="#i-${name}"/></svg>`;
    const esc = s => String(s ?? '').replace(/[&<>"']/g, c => ({
        '&': '\u0026amp;',
        '<': '\u0026lt;',
        '>': '\u0026gt;',
        '"': '\u0026quot;',
        "'": '\u0026#39;'
    }[c]));
    const round1 = n => Math.round(n * 10) / 10;
    const pad = n => String(n).padStart(2, '0');
    const fmtDateTime = d => {
        const dt = d instanceof Date ? d : new Date(d);
        if (Number.isNaN(dt.getTime())) return '-';
        return `${pad(dt.getDate())}/${pad(dt.getMonth() + 1)}/${dt.getFullYear()}, ${pad(dt.getHours())}:${pad(dt.getMinutes())}`;
    };

    const ROUTES = {
        data: @json(route('leader.kpi.data')),
        save: @json(route('leader.kpi.save'))
    };

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

    function confirmDialog({ title, desc, ok = 'Confirm', cancel = 'Cancel', iconName = 'send', warn = false }) {
        const dlg = $('confirmDialog');
        if (typeof dlg.showModal !== 'function') return Promise.resolve(window.confirm(`${title}\n\n${desc}`));
        $('confirmTitle').textContent = title; $('confirmDesc').textContent = desc;
        $('confirmOk').textContent = ok; $('confirmCancel').textContent = cancel;
        $('confirmIconUse').setAttribute('href', '#i-' + iconName);
        $('confirmIconBox').classList.toggle('warn', warn);
        return new Promise(resolve => {
            dlg.returnValue = '';
            dlg.addEventListener('close', () => resolve(dlg.returnValue === 'confirm'), { once: true });
            dlg.showModal();
        });
    }

    if (typeof window.showAvatarFallback !== 'function') {
        window.showAvatarFallback = function () {
            const img = $('headerUserProfilePic'), svg = $('defaultAvatarSvg');
            if (img) img.style.display = 'none';
            if (svg) svg.style.display = '';
        };
    }

    /* =====================================================
       LIVE MYSQL PAYLOAD INITIALIZATION
    ===================================================== */
    let seedPayload = {};
    try {
        seedPayload = JSON.parse($('laravelInputRatingSeed')?.textContent || '{}');
    } catch (e) {
        console.error('Seed parse error:', e);
    }

    const KEYS = ['q1', 'q2', 'q3', 'q4'];
    const MAX_PER_CRITERION = 10;

    function normalizeMembers(rawList) {
        return (Array.isArray(rawList) ? rawList : []).map(m => ({
            id: String(m.id),
            name: m.name || 'Staff',
            taskScore: Number(m.taskScore) || 0,
            attendanceScore: Number(m.attendanceScore) || 0,
            eval: {
                q1: m.eval?.q1 !== null && m.eval?.q1 !== undefined ? Number(m.eval.q1) : null,
                q2: m.eval?.q2 !== null && m.eval?.q2 !== undefined ? Number(m.eval.q2) : null,
                q3: m.eval?.q3 !== null && m.eval?.q3 !== undefined ? Number(m.eval.q3) : null,
                q4: m.eval?.q4 !== null && m.eval?.q4 !== undefined ? Number(m.eval.q4) : null,
            },
            status: m.status || 'PENDING',
            submittedAt: m.submittedAt ? new Date(m.submittedAt) : null
        }));
    }

    let teamMembers = normalizeMembers(seedPayload.members);
    let dbThresholds = seedPayload.thresholds || { gold: 90, silver: 85, bronze: 80 };

    // Pilih pekerja daripada parameter URL ?worker_id=... atau pekerja pertama
    const requestedId = String(seedPayload.initialMemberId || '');
    let selectedMemberId = teamMembers.some(m => m.id === requestedId)
        ? requestedId
        : (teamMembers[0]?.id || '');

    const member = () => teamMembers.find(m => m.id === selectedMemberId) || {
        id: '', name: 'No Staff', taskScore: 0, attendanceScore: 0,
        eval: { q1: null, q2: null, q3: null, q4: null }, status: 'PENDING', submittedAt: null
    };

    function syncLeaderHeader() {
        const ldr = seedPayload.leader || {};
        if (ldr.name) $('dropdownUserName').textContent = ldr.name;
        if (ldr.title) $('dropdownUserRole').textContent = ldr.title;
        if (ldr.photo) {
            $('headerUserProfilePic').src = ldr.photo;
            $('headerUserProfilePic').style.display = 'block';
            $('defaultAvatarSvg').style.display = 'none';
        }
        if (typeof EKPI_Session !== 'undefined' && typeof EKPI_Session.updateUserSession === 'function') {
            EKPI_Session.updateUserSession({
                id: ldr.id,
                name: ldr.name,
                title: ldr.title,
                profile_pic_url: ldr.photo || ''
            });
        }
    }

    
    function getThresholds() {
        if (dbThresholds && dbThresholds.gold > dbThresholds.silver && dbThresholds.silver > dbThresholds.bronze) {
            return dbThresholds;
        }
        return { gold: 90, silver: 85, bronze: 80 };
    }

    /* =====================================================
       FORM STATE: read, validate, dirty
    ===================================================== */
    const inputOf = i => $('eval' + i), slideOf = i => $('slide' + i);

    function fieldError(v) {
        if (v === undefined) return 'Enter a number between 0 and 10.';
        if (v === null) return '';
        if (!Number.isFinite(v) || v < 0 || v > MAX_PER_CRITERION) return `Score must be between 0 and ${MAX_PER_CRITERION}.`;
        return '';
    }
    function readForm() {
        const values = {}, errors = {};
        KEYS.forEach((k, idx) => {
            const raw = inputOf(idx + 1).value.trim();
            const v = raw === '' ? null : (Number.isFinite(parseFloat(raw)) ? parseFloat(raw) : undefined);
            values[k] = v === undefined ? NaN : v;
            errors[k] = raw === '' ? '' : fieldError(v === undefined ? undefined : v);
        });
        return { values, errors };
    }
    const hasErrors = errors => KEYS.some(k => errors[k]);
    const isComplete = v => KEYS.every(k => v[k] !== null && Number.isFinite(v[k]));
    function isDirty() {
        if (!teamMembers.length) return false;
        const { values } = readForm(), saved = member().eval;
        return KEYS.some(k => (Number.isNaN(values[k]) ? 'x' : values[k]) !== saved[k]);
    }

    /* =====================================================
       RENDER
    ===================================================== */
    function paintSlider(i, v) {
        const s = slideOf(i);
        const valid = v !== null && Number.isFinite(v) && v >= 0 && v <= MAX_PER_CRITERION;
        s.classList.toggle('unset', !valid);
        s.value = valid ? v : 0;
        s.style.setProperty('--progress', (valid ? v / MAX_PER_CRITERION * 100 : 0) + '%');
    }

    function renderScoreBreakdown(values) {
        const m = member();
        const leader = round1(KEYS.reduce((a, k) => a + (Number.isFinite(values[k]) && values[k] >= 0 && values[k] <= MAX_PER_CRITERION ? values[k] : 0), 0));
        const overall = round1(m.taskScore + m.attendanceScore + leader);

        $('totalLeaderScore').textContent = leader.toFixed(1);
        const ov = $('overallScoreVal'), before = ov.textContent;
        ov.textContent = overall.toFixed(1) + '%';
        if (before !== ov.textContent) { ov.classList.remove('bump'); void ov.offsetWidth; ov.classList.add('bump'); }

        const t = getThresholds(), tag = $('badgeIconTag');
        const tier = overall >= t.gold ? ['gold', 'Gold Qualifier'] : overall >= t.silver ? ['silver', 'Silver Qualifier'] : overall >= t.bronze ? ['bronze', 'Bronze Qualifier'] : ['none', 'Below Target'];
        const label = `${tier[1]}${isComplete(values) ? '' : ' (projected)'}`;
        if (tag.dataset.label !== label) {
            tag.dataset.label = label; tag.className = 'badge-icon-tag ' + tier[0];
            tag.innerHTML = (tier[0] === 'none' ? '' : icon('medal')) + `<span>${label}</span>`;
        }

        const rest = Math.max(0, round1(100 - overall));
        $('stackBar').style.gridTemplateColumns = [m.taskScore, m.attendanceScore, leader, rest].map(v => v + 'fr').join(' ');
        $('stackBar').setAttribute('aria-label', `Score composition: task ${m.taskScore}, attendance ${m.attendanceScore}, leader rating ${leader}, remaining ${rest} of 100`);
        $('stackLegend').innerHTML = [['#e11d48', 'Task', m.taskScore], ['#0284c7', 'Attendance', m.attendanceScore], ['#3B3B3B', 'Leader', leader]]
            .map(([c, n, v]) => `<span style="--c:${c}">${n} ${v.toFixed(1)}</span>`).join('');
    }

    function renderStatusAndButtons() {
        const m = member(), noStaff = !teamMembers.length, locked = noStaff || m.status === 'SUBMITTED';
        const pill = $('evaluationStatus');
        const cfg = noStaff ? ['pending', 'NO STAFF', 'clock']
            : m.status === 'SUBMITTED' ? ['complete', 'FINAL SUBMITTED', 'lock']
            : m.status === 'DRAFT' ? ['draft', 'DRAFT SAVED', 'edit']
            : ['pending', 'PENDING EVALUATION', 'clock'];
        if (pill.dataset.s !== cfg[1]) { pill.dataset.s = cfg[1]; pill.className = 'status-pill-sub ' + cfg[0]; pill.innerHTML = icon(cfg[2]) + cfg[1]; }

        $('lockNote').hidden = m.status !== 'SUBMITTED';
        if (m.status === 'SUBMITTED') $('lockNoteText').textContent = `Final evaluation submitted on ${m.submittedAt ? fmtDateTime(m.submittedAt) : '-'}. Fields are locked - use "Revise evaluation" to change it.`;
        KEYS.forEach((k, idx) => {
            inputOf(idx + 1).disabled = locked; slideOf(idx + 1).disabled = locked; slideOf(idx + 1).tabIndex = locked ? -1 : 0;
            inputOf(idx + 1).closest('.input-score-box').classList.toggle('disabled', locked);
        });

        const { values, errors } = readForm();
        const dirty = isDirty(), invalid = hasErrors(errors), complete = isComplete(values);
        const save = $('btnSaveDraft'), submit = $('btnSubmitFinal'), hint = $('submitHint');

        save.hidden = locked;
        save.disabled = !dirty || invalid;

        submit.classList.toggle('revise', m.status === 'SUBMITTED');
        $('submitLabel').textContent = m.status === 'SUBMITTED' ? 'REVISE EVALUATION' : 'SUBMIT FINAL';
        $('submitIcon').setAttribute('href', m.status === 'SUBMITTED' ? '#i-edit' : '#i-send');
        submit.disabled = noStaff ? true : (m.status === 'SUBMITTED' ? false : (!complete || invalid));

        hint.classList.toggle('ready', !locked && complete && !invalid);
        const scored = KEYS.filter(k => Number.isFinite(values[k])).length;
        hint.innerHTML = noStaff ? icon('info') + 'No staff found in this department.'
            : m.status === 'SUBMITTED' ? icon('lock') + 'Locked after final submission'
            : invalid ? icon('alert-circle') + 'Fix the highlighted scores to continue'
            : complete ? icon('check') + 'All 4 criteria scored - ready to submit'
            : icon('info') + `${scored} of 4 criteria scored - score all 4 to submit`;
    }

    function renderRowErrors(errors) {
        KEYS.forEach((k, idx) => {
            const i = idx + 1, e = $('err' + i), box = inputOf(i).closest('.input-score-box');
            e.hidden = !errors[k]; e.innerHTML = errors[k] ? icon('alert-circle') + esc(errors[k]) : '';
            box.classList.toggle('invalid', !!errors[k]);
            inputOf(i).setAttribute('aria-invalid', String(!!errors[k]));
        });
    }

    function updateCalculations() {
        const { values, errors } = readForm();
        KEYS.forEach((k, idx) => paintSlider(idx + 1, errors[k] ? null : values[k]));
        renderRowErrors(errors);
        renderScoreBreakdown(values);
        renderStatusAndButtons();
    }

    function loadMemberData(id) {
        if (!teamMembers.length) {
            updateCalculations();
            return;
        }
        selectedMemberId = String(id);
        const m = member();
        selectedMemberId = m.id;
        $('memberSelect').value = m.id;
        $('taskScoreVal').textContent = m.taskScore.toFixed(1);
        $('attendanceScoreVal').textContent = m.attendanceScore.toFixed(1);
        $('taskMeter').style.setProperty('--p', m.taskScore / 40);
        $('attMeter').style.setProperty('--p', m.attendanceScore / 20);
        KEYS.forEach((k, idx) => { inputOf(idx + 1).value = m.eval[k] === null ? '' : m.eval[k]; });
        const i = teamMembers.findIndex(x => x.id === m.id);
        $('memberPrev').disabled = i <= 0; $('memberNext').disabled = i >= teamMembers.length - 1;
        updateCalculations();
        updateNextButton();
    }

    function populateMemberDropdown() {
        if (!teamMembers.length) {
            $('memberSelect').innerHTML = '<option value="">NO STAFF</option>';
            return;
        }
        $('memberSelect').innerHTML = teamMembers.map(m => `<option value="${esc(m.id)}">${esc(m.name.toUpperCase())}${m.status === 'SUBMITTED' ? ' (submitted)' : ''}</option>`).join('');
        $('memberSelect').value = selectedMemberId;
    }

    /* =====================================================
       TEAM PROGRESS + NOTIFICATIONS
    ===================================================== */
    function updateNextButton() {
        const n = teamMembers.length;
        const next = nextToEvaluate();
        $('btnNext').disabled = !next;
        $('btnNextLabel').textContent = next ? `NEXT: ${next.name.toUpperCase()}` : (n ? 'ALL SUBMITTED' : 'NO STAFF');
    }

    function renderTeamProgress() {
        const n = teamMembers.length, c = s => teamMembers.filter(m => m.status === s).length;
        const sub = c('SUBMITTED'), pct = n ? sub / n : 0;
        $('tpCount').textContent = `${sub} of ${n} submitted`;
        $('tpSubmitted').textContent = sub; $('tpDraft').textContent = c('DRAFT'); $('tpPending').textContent = c('PENDING');
        $('tpBar').style.setProperty('--p', pct);
        $('tpTrack').setAttribute('aria-valuenow', Math.round(pct * 100));
        const next = nextToEvaluate();
        $('btnNext').disabled = !next;
        $('btnNextLabel').textContent = next ? `NEXT: ${next.name.toUpperCase()}` : (n ? 'ALL SUBMITTED' : 'NO STAFF');
        populateMemberDropdown();
        updateNextButton();
    }

    function nextToEvaluate() {
        const i = teamMembers.findIndex(m => m.id === selectedMemberId), n = teamMembers.length;
        for (let step = 1; step <= n; step++) {
            const m = teamMembers[(i + step) % n];
            if (m && m.status !== 'SUBMITTED' && m.id !== selectedMemberId) return m;
        }
        return null;
    }

    function renderNotifications() {
        const todo = teamMembers.filter(m => m.status !== 'SUBMITTED'), list = $('notificationList'), badge = $('notifBadge');
        if (!todo.length) { list.innerHTML = `<div class="no-notifications">${icon('check')}<p>No pending evaluations</p></div>`; badge.style.display = 'none'; return; }
        list.innerHTML = todo.map(m => `
            <div class="notification-item unread" data-member="${esc(m.id)}" role="button" tabindex="0">
                <h4 class="notif-title">Evaluation ${m.status === 'DRAFT' ? 'Draft' : 'Pending'}</h4>
                <p class="notif-desc">${esc(m.name.toUpperCase())} needs a final team leader evaluation.</p>
                <span class="notif-time">Status: ${m.status}</span>
            </div>`).join('');
        badge.textContent = todo.length; badge.style.display = 'inline-block';
    }

    function refreshAll() { renderTeamProgress(); renderNotifications(); }

    /* =====================================================
       MEMBER SWITCHING (guards unsaved edits)
    ===================================================== */
    async function switchMember(id) {
        id = String(id);
        if (id === selectedMemberId) return true;
        if (isDirty()) {
            const ok = await confirmDialog({
                title: 'Discard unsaved changes?',
                desc: `You have unsaved scores for ${member().name.toUpperCase()}. Switching now will discard them.`,
                ok: 'Discard & switch', cancel: 'Keep editing', iconName: 'alert', warn: true
            });
            if (!ok) { $('memberSelect').value = selectedMemberId; return false; }
        }
        loadMemberData(id);
        return true;
    }

    /* =====================================================
       SAVE DRAFT / SUBMIT FINAL / REVISE (LIVE MYSQL POST)
    ===================================================== */
    function setBusy(btn, on) { btn.classList.toggle('loading', on); btn.disabled = on || btn.disabled; btn.setAttribute('aria-busy', String(on)); }

    async function postEvaluationToMySQL(workerId, evalValues, action) {
        const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
        const res = await fetch(ROUTES.save, {
            method: 'POST',
            credentials: 'same-origin',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': csrf
            },
            body: JSON.stringify({
                worker_id: workerId,
                eval: evalValues,
                action: action
            })
        });
        const data = await res.json();
        if (!res.ok || data.status !== 'success') {
            throw new Error(data.message || 'Failed to save evaluation to database.');
        }
        if (data.payload) {
            seedPayload = data.payload;
            if (data.payload.thresholds) dbThresholds = data.payload.thresholds;
            teamMembers = normalizeMembers(data.payload.members);
        }
    }

    async function saveEvaluation(isFinal) {
        const m = member();
        if (!m.id) return;
        if (m.status === 'SUBMITTED') return reviseEvaluation();

        const { values, errors } = readForm();
        if (hasErrors(errors)) { renderRowErrors(errors); return; }
        if (isFinal && !isComplete(values)) { toast('Please score all 4 criteria before submitting.', 'error'); return; }

        if (isFinal) {
            const total = round1(m.taskScore + m.attendanceScore + KEYS.reduce((a, k) => a + values[k], 0));
            const ok = await confirmDialog({
                title: 'Submit final evaluation?',
                desc: `${m.name.toUpperCase()} will be recorded with an overall score of ${total.toFixed(1)}%. You can still revise it afterwards, but it will return to draft until you submit again.`,
                ok: 'Submit final'
            });
            if (!ok) return;
        }

        const btn = isFinal ? $('btnSubmitFinal') : $('btnSaveDraft');
        setBusy(btn, true);
        try {
            await postEvaluationToMySQL(m.id, values, isFinal ? 'submit' : 'draft');

            const next = nextToEvaluate();
            toast(
                isFinal ? `Final evaluation for ${m.name.toUpperCase()} submitted to MySQL.` : `Draft for ${m.name.toUpperCase()} saved to MySQL.`,
                isFinal ? 'success' : 'info',
                { duration: 5000, action: isFinal && next ? { label: `Next: ${next.name}`, onClick: () => switchMember(next.id) } : null }
            );
        } catch (err) {
            console.error(err);
            toast(err.message || 'Could not save the evaluation. Please try again.', 'error');
        } finally {
            btn.classList.remove('loading'); btn.removeAttribute('aria-busy');
            refreshAll(); loadMemberData(m.id);
        }
    }

    async function reviseEvaluation() {
        const m = member();
        const ok = await confirmDialog({
            title: 'Revise this evaluation?',
            desc: `${m.name.toUpperCase()}'s final evaluation will be unlocked and marked as draft until you submit it again.`,
            ok: 'Unlock & revise', iconName: 'edit', warn: true
        });
        if (!ok) return;

        const btn = $('btnSubmitFinal');
        setBusy(btn, true);
        try {
            await postEvaluationToMySQL(m.id, m.eval, 'revise');
            toast('Evaluation unlocked for editing.', 'info');
        } catch (err) {
            console.error(err);
            toast(err.message || 'Could not unlock evaluation. Please try again.', 'error');
        } finally {
            btn.classList.remove('loading'); btn.removeAttribute('aria-busy');
            refreshAll(); loadMemberData(m.id);
            inputOf(1).focus();
        }
    }

    /* =====================================================
       SYNC STATUS (LIVE LARAVEL MYSQL FETCH)
    ===================================================== */
    let lastSync = null, syncState = 'idle', firstLoad = true;

    function relTime(ts) {
        const s = Math.round((Date.now() - ts) / 1000);
        if (s < 45) return 'just now';
        const mn = Math.round(s / 60);
        return mn < 60 ? `${mn} min ago` : `${Math.round(mn / 60)} h ago`;
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

    async function fetchTeamDataFromBackend() {
        if (syncState === 'loading') return;
        syncState = 'loading'; renderSync();
        try {
            if (!firstLoad) {
                const response = await fetch(ROUTES.data, {
                    credentials: 'same-origin',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                if (!response.ok) throw new Error('Failed to fetch team data');
                const fresh = await response.json();
                seedPayload = fresh;
                if (fresh.thresholds) dbThresholds = fresh.thresholds;
                teamMembers = normalizeMembers(fresh.members);
            }
            syncLeaderHeader();
            refreshAll();
            if (!isDirty()) loadMemberData(selectedMemberId);
            lastSync = Date.now(); syncState = 'ok';
            if (!firstLoad) toast('Department data synchronized.', 'success');
        } catch (err) {
            console.error('[Dept KPI] Sync error:', err);
            syncState = 'error';
            toast('Could not refresh department data. Please try again.', 'error');
        } finally {
            firstLoad = false; renderSync();
        }
    }

    /* =====================================================
       INIT
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
        syncLeaderHeader();
        populateMemberDropdown();
        refreshAll();
        loadMemberData(selectedMemberId);

        $('memberSelect').addEventListener('change', e => switchMember(e.target.value));
        $('memberPrev').addEventListener('click', () => { const i = teamMembers.findIndex(m => m.id === selectedMemberId); if (i > 0) switchMember(teamMembers[i - 1].id); });
        $('memberNext').addEventListener('click', () => { const i = teamMembers.findIndex(m => m.id === selectedMemberId); if (i < teamMembers.length - 1) switchMember(teamMembers[i + 1].id); });
        $('btnNext').addEventListener('click', () => { const n = nextToEvaluate(); if (n) switchMember(n.id); });
        $('notificationList').addEventListener('click', e => { const it = e.target.closest('[data-member]'); if (it) { $('notificationDropdown').classList.remove('active'); switchMember(it.dataset.member); } });
        $('notificationList').addEventListener('keydown', e => { if (e.key === 'Enter') { const it = e.target.closest('[data-member]'); if (it) it.click(); } });

        KEYS.forEach((k, idx) => {
            const i = idx + 1;
            inputOf(i).addEventListener('input', updateCalculations);
            inputOf(i).addEventListener('blur', () => {
                const v = parseFloat(inputOf(i).value);
                if (Number.isFinite(v) && v >= 0 && v <= MAX_PER_CRITERION) { inputOf(i).value = Math.round(v * 2) / 2; updateCalculations(); }
            });
            slideOf(i).addEventListener('input', () => { inputOf(i).value = slideOf(i).value; updateCalculations(); });
        });

        $('btnSaveDraft').addEventListener('click', () => saveEvaluation(false));
        $('btnSubmitFinal').addEventListener('click', () => saveEvaluation(true));

        $('syncBtn').addEventListener('click', fetchTeamDataFromBackend);
        setInterval(renderSync, 30000);
        document.addEventListener('visibilitychange', () => {
            if (!document.hidden && lastSync && Date.now() - lastSync > 5 * 60 * 1000) fetchTeamDataFromBackend();
        });

        document.addEventListener('keydown', e => {
            if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 's') { e.preventDefault(); if (!$('btnSaveDraft').hidden && !$('btnSaveDraft').disabled) saveEvaluation(false); }
        });
        window.addEventListener('beforeunload', e => { if (isDirty()) { e.preventDefault(); e.returnValue = ''; } });

        initDropdownA11y();
        fetchTeamDataFromBackend();
    });
    
    </script>
</body>
</html>