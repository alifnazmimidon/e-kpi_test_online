<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Reports - E-KPI System</title>
    <link rel="stylesheet" href="{{ asset('stylereport.css') }}">
    
    {{-- DARK MODE --}}
    <link rel="stylesheet" href="{{ asset('ekpitheme.css') }}">
    <script src="{{ asset('ekpitheme.js') }}"></script>
</head>
<body>

    <a class="skip-link" href="#main">Skip to main content</a>

    <!-- SVG ICON SPRITE -->
    <svg width="0" height="0" style="position:absolute" aria-hidden="true" focusable="false">
        <symbol id="i-file" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6M16 13H8M16 17H8M10 9H8"/></symbol>
        <symbol id="i-download" viewBox="0 0 24 24"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><path d="m7 10 5 5 5-5M12 15V3"/></symbol>
        <symbol id="i-clock" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></symbol>
        <symbol id="i-zap" viewBox="0 0 24 24"><path d="M13 2 3 14h9l-1 8 10-12h-9z"/></symbol>
        <symbol id="i-loader" viewBox="0 0 24 24"><path d="M21 12a9 9 0 1 1-6.2-8.6"/></symbol>
        <symbol id="i-folder" viewBox="0 0 24 24"><path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"/></symbol>
        <symbol id="i-search" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></symbol>
        <symbol id="i-eye" viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></symbol>
        <symbol id="i-trash" viewBox="0 0 24 24"><path d="M3 6h18M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></symbol>
        <symbol id="i-x" viewBox="0 0 24 24"><path d="M18 6 6 18M6 6l12 12"/></symbol>
        <symbol id="i-stop" viewBox="0 0 24 24"><rect x="6" y="6" width="12" height="12" rx="2"/></symbol>
        <symbol id="i-reset" viewBox="0 0 24 24"><path d="M3 12a9 9 0 1 0 3-6.7L3 8"/><path d="M3 3v5h5"/></symbol>
        <symbol id="i-check" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="m9 12 2 2 4-4"/></symbol>
        <symbol id="i-alert-circle" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M12 8v4M12 16h.01"/></symbol>
        <symbol id="i-info" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M12 16v-4M12 8h.01"/></symbol>
        <symbol id="i-user" viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></symbol>
        <symbol id="i-settings" viewBox="0 0 24 24"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"/></symbol>
        <symbol id="i-logout" viewBox="0 0 24 24"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4M16 17l5-5-5-5M21 12H9"/></symbol>
        <symbol id="i-inbox" viewBox="0 0 24 24"><path d="M22 12h-6l-2 3h-4l-2-3H2"/><path d="M5.5 5.1 2 12v6a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-6l-3.5-6.9A2 2 0 0 0 16.7 4H7.3a2 2 0 0 0-1.8 1.1z"/></symbol>
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
        <a href="{{ route('hr.dashboard') }}" class="nav-link {{ request()->routeIs('hr.dashboard') ? 'active' : '' }}">Dashboard</a>
        <a href="{{ route('hr.kpi') }}" class="nav-link {{ request()->routeIs('hr.kpi') ? 'active' : '' }}">KPI Management</a>
        <a href="{{ route('hr.report') }}" class="nav-link {{ request()->routeIs('hr.report') ? 'active' : '' }}" aria-current="page">Report</a>
        <a href="{{ route('hr.emerleave') }}" class="nav-link {{ request()->routeIs('hr.emerleave') ? 'active' : '' }}">Emergency Leave</a>
    </nav>

    <!-- ============================== MAIN CONTAINER ============================== -->
    <main class="report-main-container" id="main">

        <!-- METRIC SUMMARY STRIP -->
        <section class="report-stats-grid" aria-label="Report statistics">
            <div class="report-stat-card reveal" style="--i:2">
                <div class="stat-icon-wrapper blue"><svg class="icon" aria-hidden="true"><use href="#i-file"/></svg></div>
                <div class="stat-info">
                    <span class="stat-label">Total Reports Generated</span>
                    <strong class="stat-number" id="totalReportCount">{{ $totalGenerated ?? 0 }}</strong>
                </div>
            </div>
            <div class="report-stat-card reveal" style="--i:3">
                <div class="stat-icon-wrapper green"><svg class="icon" aria-hidden="true"><use href="#i-download"/></svg></div>
                <div class="stat-info">
                    <span class="stat-label">Archive Storage Used</span>
                    <strong class="stat-number" id="storageUsed">0 KB</strong>
                </div>
            </div>
            <div class="report-stat-card reveal" style="--i:4">
                <div class="stat-icon-wrapper red"><svg class="icon" aria-hidden="true"><use href="#i-clock"/></svg></div>
                <div class="stat-info">
                    <span class="stat-label">Last Generated</span>
                    <strong class="stat-number" id="lastGeneratedTime">-</strong>
                </div>
            </div>
        </section>

        <!-- MAIN REPORT GENERATOR PANEL -->
        <div class="report-card reveal" style="--i:5">

            <div class="report-card-header">
                <div class="title-with-icon">
                    <div class="header-badge-icon"><svg class="icon" aria-hidden="true"><use href="#i-file"/></svg></div>
                    <h2>REPORTS GENERATION CENTER</h2>
                </div>
                <div class="format-group">
                    <label for="exportFormat">FORMAT :</label>
                    <select id="exportFormat" class="select-pill">
                        <option value="pdf">PDF (Printable Document)</option>
                        <option value="csv">CSV (Raw Data Sheet)</option>
                        <option value="xlsx">XLSX (Formatted Spreadsheet)</option>
                    </select>
                </div>
            </div>

            <div class="filter-bar">
                <span class="filter-label">FILTER BAR :</span>

                <div class="filter-item">
                    <label for="reportType">REPORT TYPE :</label>
                    <select id="reportType" class="select-pill">
                        <option value="dept_summary">Quarterly Department Performance Summary</option>
                        <option value="individual_audit">Employee Individual KPI Evaluation Audit</option>
                        <option value="attendance_impact">Emergency Leave & Attendance Impact</option>
                        <option value="workload_health">Overload & Workload Health Analysis</option>
                    </select>
                </div>

                <div class="filter-item">
                    <label for="quarterSelect">QUARTER :</label>
                    <select id="quarterSelect" class="select-pill">
                        @php
                            $currentYear = now()->year;
                            $years = [$currentYear, $currentYear - 1];
                        @endphp
                        @foreach($years as $yr)
                            @for($q = 4; $q >= 1; $q--)
                                <option value="Q{{ $q }}-{{ $yr }}" @selected($yr === $currentYear && $q === now()->quarter)>
                                    Q{{ $q }} {{ $yr }}
                                </option>
                            @endfor
                        @endforeach
                    </select>
                </div>

                <div class="filter-item">
                    <label for="departmentSelect">DEPARTMENT :</label>
                    <select id="departmentSelect" class="select-pill">
                        <option value="ALL">ALL DEPARTMENTS</option>
                        <option value="WEBTECH">Webtech</option>
                        <option value="RND">R&D</option>
                        <option value="VIDEO">Video Production</option>
                        <option value="GRAPHIC">Graphic Design</option>
                    </select>
                </div>

                <button type="button" class="reset-filters" id="resetFilters"><svg class="icon" aria-hidden="true"><use href="#i-reset"/></svg>Reset</button>
            </div>

            <div class="live-preview-chip" id="previewChip">
                <span class="chip-dot" aria-hidden="true"></span>
                <span class="chip-text" id="livePreviewSummary" aria-live="polite"></span>
            </div>

            <!-- GENERATE ACTION BOX -->
            <div class="generate-box-wrapper">
                <div class="generate-box" id="generateBox">
                    <div class="report-icon-bg" id="reportIconBg"><svg class="icon" aria-hidden="true"><use href="#i-file"/></svg></div>

                    <div class="generate-actions">
                        <button type="button" class="btn-generate" id="btnGenerate">
                            <svg class="icon btn-icon" aria-hidden="true"><use href="#i-zap"/></svg>
                            <svg class="icon btn-spinner" aria-hidden="true"><use href="#i-loader"/></svg>
                            <span id="btnGenerateLabel">GENERATE NEW REPORT</span>
                        </button>
                        <button type="button" class="btn-cancel" id="btnCancel" hidden>
                            <svg class="icon fill" aria-hidden="true"><use href="#i-stop"/></svg>Cancel
                        </button>
                    </div>

                    <div id="progressWrap" hidden style="width:100%;display:flex;flex-direction:column;align-items:center;gap:6px">
                        <div class="progress-bar-container" role="progressbar" aria-label="Report generation progress" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0" id="progressTrack">
                            <div class="progress-bar-fill" id="progressBarFill"></div>
                        </div>
                        <span class="progress-pct" id="progressPct">0%</span>
                    </div>

                    <p id="reportStatus" class="report-status-msg" role="status" aria-live="polite">Click above to fetch database records and build your file.</p>
                </div>
            </div>

            <!-- ARCHIVE -->
            <div class="archive-section">
                <div class="archive-header-row">
                    <div class="archive-title">
                        <svg class="icon" aria-hidden="true"><use href="#i-folder"/></svg>
                        <h3 style="font:inherit;letter-spacing:inherit">RECENTLY GENERATED REPORTS (SAVED ARCHIVE)</h3>
                    </div>

                    <div class="archive-tools">
                        <div class="archive-search-box">
                            <svg class="icon" aria-hidden="true"><use href="#i-search"/></svg>
                            <input type="search" id="archiveSearchInput" placeholder="Filter saved reports..." aria-label="Filter saved reports" autocomplete="off">
                            <button type="button" class="search-clear" id="searchClear" aria-label="Clear search" hidden><svg class="icon" aria-hidden="true"><use href="#i-x"/></svg></button>
                        </div>
                        <div class="filter-group" role="group" aria-label="Filter by file format">
                            <button type="button" class="filter-btn" data-format="all" aria-pressed="true">All <span id="cntAll">0</span></button>
                            <button type="button" class="filter-btn" data-format="PDF" aria-pressed="false">PDF <span id="cntPdf">0</span></button>
                            <button type="button" class="filter-btn" data-format="CSV" aria-pressed="false">CSV <span id="cntCsv">0</span></button>
                            <button type="button" class="filter-btn" data-format="XLSX" aria-pressed="false">XLSX <span id="cntXlsx">0</span></button>
                        </div>
                        <select id="archiveSort" class="select-pill" aria-label="Sort saved reports">
                            <option value="new">Newest first</option>
                            <option value="old">Oldest first</option>
                            <option value="name">Name (A-Z)</option>
                            <option value="size">Largest first</option>
                        </select>
                    </div>
                </div>

                <div class="archive-meta" id="archiveMeta" aria-live="polite"></div>
                <div class="archive-grid" id="archiveGrid"></div>
            </div>

        </div>
    </main>

    <!-- PREVIEW DIALOG (native <dialog>: focus trap + Esc + backdrop click) -->
    <dialog class="preview-dialog" id="previewDialog" aria-labelledby="modalReportTitle">
        <div class="modal-header">
            <div class="modal-title-group">
                <svg class="icon" aria-hidden="true"><use href="#i-eye"/></svg>
                <h3 id="modalReportTitle">Report File Preview</h3>
            </div>
            <button type="button" class="modal-close-btn" id="modalCloseX" aria-label="Close preview"><svg class="icon" aria-hidden="true"><use href="#i-x"/></svg></button>
        </div>
        <div class="modal-body" id="modalReportContent"></div>
        <div class="modal-footer">
            <button type="button" class="btn-modal-secondary" id="modalCloseBtn">Close Preview</button>
            <button type="button" class="btn-modal-primary" id="modalDownloadBtn"><svg class="icon" aria-hidden="true"><use href="#i-download"/></svg>Download File</button>
        </div>
    </dialog>

    <div class="toast-region" id="toastRegion" aria-live="polite"></div>

   <!-- LARAVEL SEED DATA (SAFE JSON TAG) -->
    <script type="application/json" id="laravelReportSeed">
        <?php echo json_encode([
            'archives'        => $archives ?? [],
            'totalGenerated'  => $totalGenerated ?? 0,
            'storageBytes'    => $storageBytes ?? 0,
            'lastGeneratedAt' => $lastGeneratedAt ?? null,
        ], JSON_HEX_TAG); ?>
    </script>

    <script>
        const REPORT_ROUTES = {
        generate: @json(route('hr.report.generate')),
        deleteTemplate: @json(route('hr.report.delete', ['id' => '__ID__'])),
    };
    /* =====================================================
       HELPERS
    ===================================================== */
    const $ = id => document.getElementById(id);
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
    const prefersReduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    const icon = (name, cls = '') => `<svg class="icon ${cls}" aria-hidden="true"><use href="#i-${name}"/></svg>`;
    const esc = s => {
        const d = document.createElement('div');
        d.textContent = String(s ?? '');
        return d.innerHTML;
    };
    const pad = n => String(n).padStart(2, '0');
    const fmtDate = d => `${pad(d.getDate())}/${pad(d.getMonth() + 1)}/${d.getFullYear()}`;
    const fmtBytes = b => b <= 0 ? '0 KB' : (b >= 1048576 ? (b / 1048576).toFixed(1) + ' MB' : Math.max(1, Math.round(b / 1024)) + ' KB');

    // Abortable sleep: lets the Cancel button stop a generation in progress
    function sleep(ms, signal) {
        return new Promise((resolve, reject) => {
            if (signal && signal.aborted) return reject(new DOMException('Aborted', 'AbortError'));
            const t = setTimeout(resolve, prefersReduced ? 0 : ms);
            if (signal) signal.addEventListener('abort', () => { clearTimeout(t); reject(new DOMException('Aborted', 'AbortError')); }, { once: true });
        });
    }

    function animateNumber(el, to, fmt) {
        if (!el) return;
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

    // Toast with optional action button (used for Undo / Preview)
    function showToast(message, type = 'info', { action = null, duration = 3500 } = {}) {
        const el = document.createElement('div');
        el.className = 'toast ' + type;
        const ic = type === 'success' ? 'check' : type === 'error' ? 'alert-circle' : 'info';
        el.innerHTML = `${icon(ic)}<span>${esc(message)}</span>
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

    /* =====================================================
       STATE (LOADED FROM LARAVEL BACKEND)
    ===================================================== */
    let seed = { archives: [], totalGenerated: 0, storageBytes: 0, lastGeneratedAt: null };
    try {
        const raw = $('laravelReportSeed')?.textContent?.trim();
        if (raw) seed = JSON.parse(raw);
    } catch (e) {
        console.warn('Seed parse warning:', e);
    }

    let archives = Array.isArray(seed.archives) ? seed.archives.map(a => ({
        ...a,
        createdAt: new Date(a.createdAt)
    })) : [];
    let totalGenerated = Number(seed.totalGenerated || 0);
    let storageBytes = Number(seed.storageBytes || 0);
    let lastGeneratedAt = seed.lastGeneratedAt ? new Date(seed.lastGeneratedAt) : null;

    let isGenerating = false, genController = null;
    let search = '', formatFilter = 'all', sortKey = 'new', newId = null, animateAll = true;
    const quarterEl = document.getElementById('quarterSelect');
    const currentQuarterFallback = `Q${Math.ceil((new Date().getMonth() + 1) / 3)}-${new Date().getFullYear()}`;

    const DEFAULTS = {
        exportFormat: 'pdf',
        reportType: 'dept_summary',
        quarterSelect: quarterEl?.options[0]?.value || currentQuarterFallback,
        departmentSelect: 'ALL'
    };
    const pendingDeletes = new Map();

    /* =====================================================
       STATS
    ===================================================== */
    function relTime(d) {
        if (!d || isNaN(d.getTime())) return '-';
        const s = Math.round((Date.now() - d.getTime()) / 1000);
        if (s < 45) return 'Just now';
        const m = Math.round(s / 60);
        if (m < 60) return `${m} min ago`;
        const now = new Date();
        const time = d.toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit' });
        if (d.toDateString() === now.toDateString()) return `Today, ${time}`;
        const y = new Date(now); y.setDate(now.getDate() - 1);
        return d.toDateString() === y.toDateString() ? `Yesterday, ${time}` : fmtDate(d);
    }
    function renderStats() {
        animateNumber($('totalReportCount'), totalGenerated, v => Math.round(v));
        animateNumber($('storageUsed'), storageBytes, v => fmtBytes(v));
        $('lastGeneratedTime').textContent = relTime(lastGeneratedAt);
    }

    /* =====================================================
       FILTERS + LIVE PREVIEW CHIP
    ===================================================== */
    const optText = id => { const s = $(id); return s.options[s.selectedIndex].text; };

    function updateLivePreview() {
        const chip = $('livePreviewSummary');
        chip.textContent = `Target Payload: ${optText('reportType')} | ${optText('quarterSelect')} | ${optText('departmentSelect')} | ${$('exportFormat').value.toUpperCase()}`;
        const box = $('previewChip');
        box.classList.remove('flash'); void box.offsetWidth; box.classList.add('flash');
    }
    function resetFilters() {
        Object.entries(DEFAULTS).forEach(([id, v]) => { $(id).value = v; });
        updateLivePreview();
    }

    /* =====================================================
       GENERATE REPORT  (abortable, step-based progress + MySQL API)
    ===================================================== */
    const STEPS = [
        { pct: 15, msg: 'Connecting to database & gathering metrics...' },
        { pct: 45, msg: 'Formatting document layout & calculating KPI weights...' },
        { pct: 75, msg: 'Finalizing file export stream...' },
        { pct: 100, msg: 'Report ready.' }
    ];

    function setProgress(pct) {
        $('progressBarFill').style.transform = `scaleX(${pct / 100})`;
        $('progressTrack').setAttribute('aria-valuenow', pct);
        $('progressPct').textContent = pct + '%';
    }
    function setStatus(html, cls) { const s = $('reportStatus'); s.innerHTML = html; s.className = 'report-status-msg ' + (cls || ''); }

    function setGenerating(on) {
        isGenerating = on;
        const btn = $('btnGenerate');
        btn.disabled = on; btn.classList.toggle('loading', on); btn.setAttribute('aria-busy', String(on));
        $('btnGenerateLabel').textContent = on ? 'GENERATING...' : 'GENERATE NEW REPORT';
        $('btnCancel').hidden = !on;
        $('progressWrap').hidden = !on;
        $('reportIconBg').classList.toggle('pulse-anim', on);
        $('generateBox').classList.toggle('busy', on);
        ['exportFormat', 'reportType', 'quarterSelect', 'departmentSelect', 'resetFilters'].forEach(id => { $(id).disabled = on; });
        if (on) setProgress(0);
    }

    async function generateReport() {
        if (isGenerating) return;

        const type = $('reportType').value, quarterKey = $('quarterSelect').value, dept = $('departmentSelect').value, fmt = $('exportFormat').value;
        const cfg = { reportType: type, quarter: quarterKey, dept, format: fmt };

        genController = new AbortController();
        setGenerating(true);

        try {
            // Start Laravel MySQL query concurrently while animating progress steps
            const fetchPromise = fetch(REPORT_ROUTES.generate, {
                method: 'POST',
                credentials: 'same-origin',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify(cfg),
                signal: genController.signal
            });

            for (const step of STEPS) {
                setStatus(icon('loader') + esc(step.msg), 'processing');
                setProgress(step.pct);
                await sleep(step.pct === 100 ? 250 : 380, genController.signal);
            }

            const response = await fetchPromise;
            if (!response.ok) {
                const errText = await response.text();
                throw new Error(`HTTP ${response.status}: ${errText.slice(0, 120)}`);
            }
            const result = await response.json();

            const record = {
                ...result.record,
                createdAt: new Date(result.record.createdAt)
            };

            archives.unshift(record);
            totalGenerated = result.totalGenerated;
            storageBytes = result.storageBytes;
            lastGeneratedAt = record.createdAt;
            search = ''; $('archiveSearchInput').value = ''; $('searchClear').hidden = true;
            formatFilter = 'all'; newId = record.id; animateAll = false;
            renderArchive(); renderStats();

            setStatus(icon('check') + `Success! Report generated and added to archive. (${esc(record.title)})`, 'success');
            showToast(`Report '${record.title}' created successfully.`, 'success', { action: { label: 'Preview', onClick: () => openPreviewModal(record.id) }, duration: 5000 });
        } catch (err) {
            if (err.name === 'AbortError') {
                setStatus(icon('info') + 'Generation cancelled. Nothing was added to the archive.', '');
                showToast('Report generation cancelled.', 'info');
            } else {
                console.error('Report generation error:', err);
                setStatus(icon('alert-circle') + 'Error generating report. Please check server connection.', 'error');
                showToast('Failed to generate report file.', 'error');
            }
        } finally {
            genController = null;
            setGenerating(false);
        }
    }

    /* =====================================================
       ARCHIVE: search + format filter + sort
    ===================================================== */
    function visibleArchives() {
        const q = search.trim().toLowerCase();
        let list = archives.filter(a =>
            (formatFilter === 'all' || a.format === formatFilter) &&
            (!q || [a.title, a.typeName, a.dept, a.deptLabel, a.quarter, a.format].some(v => String(v).toLowerCase().includes(q))));
        const by = {
            new: (a, b) => b.createdAt - a.createdAt, old: (a, b) => a.createdAt - b.createdAt,
            name: (a, b) => a.title.localeCompare(b.title), size: (a, b) => b.bytes - a.bytes
        }[sortKey];
        return list.sort(by);
    }

    function renderArchive() {
        const grid = $('archiveGrid'), list = visibleArchives();
        const count = f => archives.filter(a => a.format === f).length;
        $('cntAll').textContent = archives.length; $('cntPdf').textContent = count('PDF');
        $('cntCsv').textContent = count('CSV'); $('cntXlsx').textContent = count('XLSX');
        document.querySelectorAll('[data-format]').forEach(b => b.setAttribute('aria-pressed', String(b.dataset.format === formatFilter)));
        $('archiveMeta').textContent = archives.length ? `Showing ${list.length} of ${archives.length} saved ${archives.length === 1 ? 'report' : 'reports'}` : '';

        if (!list.length) {
            grid.innerHTML = `<div class="no-archive">${icon('inbox')}<p>${archives.length ? 'No matching report files found in archive.' : 'The archive is empty. Generate a report to see it here.'}</p></div>`;
            return;
        }

        grid.innerHTML = list.map((a, i) => {
            const cls = a.format.toLowerCase() + '-badge';
            return `
                <div class="archive-card ${a.id === newId ? 'new' : animateAll ? 'enter' : ''}" style="--i:${i}" data-id="${a.id}">
                    <div class="archive-card-top">
                        <span class="format-tag ${cls}">${icon('file')}${esc(a.format)}</span>
                        <span class="archive-date">${fmtDate(a.createdAt)}</span>
                    </div>
                    <div class="archive-card-details">
                        <strong title="${esc(a.title)}">${esc(a.title)}</strong>
                        <span>${esc(a.typeName)} (${fmtBytes(a.bytes)})</span>
                    </div>
                    <div class="archive-actions">
                        <button type="button" class="btn-archive-act view-btn" data-action="preview" aria-label="Preview ${esc(a.title)}">${icon('eye')}<span>Preview</span></button>
                        <button type="button" class="btn-archive-act dl-btn icon-only" data-action="download" aria-label="Download ${esc(a.title)}" title="Download file">${icon('download')}</button>
                        <button type="button" class="btn-archive-act delete-btn icon-only" data-action="delete" aria-label="Delete ${esc(a.title)}" title="Delete archive entry">${icon('trash')}</button>
                    </div>
                </div>`;
        }).join('');
        newId = null; animateAll = false;
    }

    // Delete with Undo + Backend Sync
    async function deleteArchiveItem(id) {
        const item = archives.find(a => a.id === id);
        if (!item) return;

        const deleteUrl = REPORT_ROUTES.deleteTemplate.replace('__ID__', encodeURIComponent(id));

        const timerId = setTimeout(() => {
            pendingDeletes.delete(id);

            fetch(deleteUrl, {
                method: 'DELETE',
                credentials: 'same-origin',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': csrfToken
                }
            }).catch(() => {});
        }, 6200);

        pendingDeletes.set(id, timerId);

        const card = document.querySelector(`.archive-card[data-id="${id}"]`);
        const done = () => {
            archives = archives.filter(a => a.id !== id);
            storageBytes = Math.max(0, storageBytes - item.bytes);
            renderArchive(); renderStats();
            showToast(`Removed '${item.title}'.`, 'info', {
                duration: 6000,
                action: {
            label: 'Undo',
            onClick: () => {
                clearTimeout(pendingDeletes.get(id));
                pendingDeletes.delete(id);
                archives.push(item);
                storageBytes += item.bytes;
                newId = item.id;
                renderArchive();
                renderStats();
            }
        }
            });
        };
        if (card && !prefersReduced) { card.classList.remove('enter', 'new'); card.classList.add('leaving'); setTimeout(done, 220); } else done();
    }

    /* =====================================================
       PREVIEW + DOWNLOAD (REAL PDF, CSV & XLSX GENERATORS)
    ===================================================== */
    const ST = { PASSED: ['ok', 'check'], NORMAL: ['ok', 'check'], APPROVED: ['ok', 'check'], GOOD: ['info', 'info'], WATCH: ['warn', 'alert-circle'], RISK: ['bad', 'alert-circle'], DEDUCTED: ['bad', 'alert-circle'] };

    function getSnapshot(item) {
        if (item.snapshot && Array.isArray(item.snapshot.headers) && Array.isArray(item.snapshot.rows)) {
            return item.snapshot;
        }
        return { headers: ['Info'], rows: [['No database records available.']] };
    }

    function openPreviewModal(id) {
        const item = archives.find(a => a.id === id);
        if (!item) return;
        const snap = getSnapshot(item);
        $('modalReportTitle').textContent = item.title;

        const cell = (v, h) => {
            if (h === 'Status' && ST[v]) return `<span class="st ${ST[v][0]}">${icon(ST[v][1])}${esc(v)}</span>`;
            return esc(v);
        };
        $('modalReportContent').innerHTML = `
            <div class="preview-summary-box">
                <div class="preview-row"><strong>Document Type:</strong> <span>${esc(item.typeName)}</span></div>
                <div class="preview-row"><strong>Target Quarter:</strong> <span>${esc(item.quarter)}</span></div>
                <div class="preview-row"><strong>Department Filter:</strong> <span>${esc(item.deptLabel || item.dept)}</span></div>
                <div class="preview-row"><strong>Export Format:</strong> <span>${esc(item.format)}</span></div>
                <div class="preview-row"><strong>File Size:</strong> <span>${fmtBytes(item.bytes)}</span></div>
                <div class="preview-row"><strong>Generated Date:</strong> <span>${fmtDate(item.createdAt)}</span></div>
            </div>
            <div class="preview-sample-table">
                <h4>Document Data Summary Snapshot</h4>
                <div class="table-scroll"><table>
                    <thead><tr>${snap.headers.map(h => `<th scope="col">${esc(h)}</th>`).join('')}</tr></thead>
                    <tbody>${snap.rows.length ? snap.rows.map((r, i) => `<tr style="--i:${i}">${r.map((v, c) => `<td>${cell(v, snap.headers[c])}</td>`).join('')}</tr>`).join('')
                        : `<tr><td colspan="${snap.headers.length}">No records for this selection.</td></tr>`}</tbody>
                </table></div>
            </div>`;

        $('modalDownloadBtn').onclick = () => { downloadArchive(item.id); $('previewDialog').close(); };
        $('previewDialog').showModal();
    }

    function toCSV(snap) {
        const q = v => `"${String(v).replace(/"/g, '""')}"`;
        return '\ufeff' + [snap.headers, ...snap.rows].map(r => r.map(q).join(',')).join('\r\n');
    }

    // Pure-JS valid binary PDF 1.4 generator (downloads genuine .pdf file directly)
    function buildPdfBlob(item, snap) {
        const clean = s => String(s ?? '').replace(/[^\x20-\x7E]/g, ' ').replace(/\\/g, '\\\\').replace(/\(/g, '\\(').replace(/\)/g, '\\)');
        const trunc = (s, max) => s.length > max ? s.slice(0, Math.max(1, max - 3)) + '...' : s;

        const pageW = 595, pageH = 842, margin = 40;
        const usableW = pageW - margin * 2;
        const cols = Math.max(1, snap.headers.length);
        const colW = usableW / cols;
        const maxChars = Math.max(8, Math.floor((colW - 10) / 5.2));
        const rowsPerPage = 24;

        const chunks = [];
        const totalRows = Math.max(1, snap.rows.length);
        for (let i = 0; i < totalRows; i += rowsPerPage) {
            chunks.push(snap.rows.slice(i, i + rowsPerPage));
        }

        const pageStreams = chunks.map((pageRows, pIdx) => {
            const ops = [];
            // Header banner
            ops.push('0.89 0.11 0.14 rg');
            ops.push(`${margin} ${pageH - 58} ${usableW} 4 re f`);
            ops.push('0.09 0.09 0.11 rg');
            ops.push(`BT /F2 15 Tf ${margin} ${pageH - 46} Td (E-KPI SYSTEM - ${clean(item.typeName.toUpperCase())}) Tj ET`);
            ops.push('0.35 0.35 0.40 rg');
            ops.push(`BT /F1 9 Tf ${margin} ${pageH - 74} Td (File: ${clean(item.title)}   |   Quarter: ${clean(item.quarter)}   |   Department: ${clean(item.deptLabel || item.dept)}   |   Date: ${clean(fmtDate(item.createdAt))}) Tj ET`);

            // Table header
            let y = pageH - 106;
            const rowH = 24;
            ops.push('0.09 0.09 0.11 rg');
            ops.push(`${margin} ${y} ${usableW} ${rowH} re f`);
            ops.push('1 1 1 rg');
            snap.headers.forEach((h, c) => {
                const x = margin + c * colW + 6;
                ops.push(`BT /F2 9 Tf ${x.toFixed(1)} ${y + 8} Td (${clean(trunc(String(h), maxChars))}) Tj ET`);
            });

            // Table body rows
            pageRows.forEach((r, rIdx) => {
                y -= rowH;
                if (rIdx % 2 === 1) {
                    ops.push('0.97 0.97 0.98 rg');
                    ops.push(`${margin} ${y} ${usableW} ${rowH} re f`);
                }
                ops.push('0.85 0.85 0.88 RG 0.5 w');
                ops.push(`${margin} ${y} m ${margin + usableW} ${y} l S`);
                ops.push('0.12 0.12 0.15 rg');
                r.forEach((val, c) => {
                    const x = margin + c * colW + 6;
                    ops.push(`BT /F1 9 Tf ${x.toFixed(1)} ${y + 8} Td (${clean(trunc(String(val), maxChars))}) Tj ET`);
                });
            });

            // Footer
            ops.push('0.45 0.45 0.50 rg');
            ops.push(`BT /F1 8 Tf ${margin} 28 Td (Generated by ILM Groups E-KPI System) Tj ET`);
            ops.push(`BT /F1 8 Tf ${pageW - margin - 55} 28 Td (Page ${pIdx + 1} of ${chunks.length}) Tj ET`);
            return ops.join('\n');
        });

        // Assemble PDF objects with exact byte offsets
        const objects = [];
        objects[1] = '<< /Type /Catalog /Pages 2 0 R >>';
        const pageIds = pageStreams.map((_, i) => 5 + i * 2);
        objects[2] = `<< /Type /Pages /Kids [${pageIds.map(id => `${id} 0 R`).join(' ')}] /Count ${pageIds.length} >>`;
        objects[3] = '<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica >>';
        objects[4] = '<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica-Bold >>';

        pageStreams.forEach((stream, i) => {
            const pId = 5 + i * 2;
            const cId = pId + 1;
            objects[pId] = `<< /Type /Page /Parent 2 0 R /MediaBox [0 0 ${pageW} ${pageH}] /Resources << /Font << /F1 3 0 R /F2 4 0 R >> >> /Contents ${cId} 0 R >>`;
            objects[cId] = `<< /Length ${stream.length} >>\nstream\n${stream}\nendstream`;
        });

        let pdf = '%PDF-1.4\n';
        const offsets = [0];
        for (let id = 1; id < objects.length; id++) {
            offsets[id] = pdf.length;
            pdf += `${id} 0 obj\n${objects[id]}\nendobj\n`;
        }
        const xrefPos = pdf.length;
        pdf += `xref\n0 ${objects.length}\n0000000000 65535 f \n`;
        for (let id = 1; id < objects.length; id++) {
            pdf += `${String(offsets[id]).padStart(10, '0')} 00000 n \n`;
        }
        pdf += `trailer\n<< /Size ${objects.length} /Root 1 0 R >>\nstartxref\n${xrefPos}\n%%EOF`;

        return new Blob([pdf], { type: 'application/pdf' });
    }

    // Pure-JS valid OpenXML .xlsx spreadsheet generator (opens cleanly in Excel without warnings)
    function buildXlsxBlob(item, snap) {
        const xmlEsc = s => String(s ?? '').replace(/[&<>"']/g, ch => ({
            '&': '\x26amp;',
            '<': '\x26lt;',
            '>': '\x26gt;',
            '"': '\x26quot;',
            "'": '\x26apos;'
        }[ch]));
            const colName = idx => {
            let s = '';
            for (let n = idx + 1; n > 0; n = Math.floor((n - 1) / 26)) {
                s = String.fromCharCode(65 + ((n - 1) % 26)) + s;
            }
            return s;
        };

        const allRows = [
            [`${item.typeName} (${item.quarter} - ${item.deptLabel || item.dept})`],
            [],
            snap.headers,
            ...snap.rows
        ];

        const sheetRowsXml = allRows.map((row, rIdx) => {
            const cells = row.map((val, cIdx) => {
                const ref = `${colName(cIdx)}${rIdx + 1}`;
                return `<c r="${ref}" t="inlineStr"><is><t>${xmlEsc(val)}</t></is></c>`;
            }).join('');
            return `<row r="${rIdx + 1}">${cells}</row>`;
        }).join('');

        const files = [
            {
                name: '[Content_Types].xml',
                data: `<?xml version="1.0" encoding="UTF-8" standalone="yes"?><Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types"><Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/><Default Extension="xml" ContentType="application/xml"/><Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/><Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/></Types>`
            },
            {
                name: '_rels/.rels',
                data: `<?xml version="1.0" encoding="UTF-8" standalone="yes"?><Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships"><Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/></Relationships>`
            },
            {
                name: 'xl/workbook.xml',
                data: `<?xml version="1.0" encoding="UTF-8" standalone="yes"?><workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships"><sheets><sheet name="KPI Report" sheetId="1" r:id="rId1"/></sheets></workbook>`
            },
            {
                name: 'xl/_rels/workbook.xml.rels',
                data: `<?xml version="1.0" encoding="UTF-8" standalone="yes"?><Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships"><Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/></Relationships>`
            },
            {
                name: 'xl/worksheets/sheet1.xml',
                data: `<?xml version="1.0" encoding="UTF-8" standalone="yes"?><worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main"><sheetData>${sheetRowsXml}</sheetData></worksheet>`
            }
        ];

        // CRC32 + Uncompressed ZIP builder
        const crcTable = new Uint32Array(256);
        for (let i = 0; i < 256; i++) {
            let c = i;
            for (let k = 0; k < 8; k++) c = (c & 1) ? (0xEDB88320 ^ (c >>> 1)) : (c >>> 1);
            crcTable[i] = c;
        }
        const crc32 = bytes => {
            let c = 0xFFFFFFFF;
            for (let i = 0; i < bytes.length; i++) c = crcTable[(c ^ bytes[i]) & 0xFF] ^ (c >>> 8);
            return (c ^ 0xFFFFFFFF) >>> 0;
        };

        const enc = new TextEncoder();
        const localParts = [], centralParts = [];
        let offset = 0;

        files.forEach(f => {
            const nameBytes = enc.encode(f.name);
            const dataBytes = enc.encode(f.data);
            const crc = crc32(dataBytes);

            const local = new Uint8Array(30 + nameBytes.length + dataBytes.length);
            const lv = new DataView(local.buffer);
            lv.setUint32(0, 0x04034b50, true);
            lv.setUint16(4, 20, true);
            lv.setUint32(14, crc, true);
            lv.setUint32(18, dataBytes.length, true);
            lv.setUint32(22, dataBytes.length, true);
            lv.setUint16(26, nameBytes.length, true);
            local.set(nameBytes, 30);
            local.set(dataBytes, 30 + nameBytes.length);
            localParts.push(local);

            const central = new Uint8Array(46 + nameBytes.length);
            const cv = new DataView(central.buffer);
            cv.setUint32(0, 0x02014b50, true);
            cv.setUint16(4, 20, true);
            cv.setUint16(6, 20, true);
            cv.setUint32(16, crc, true);
            cv.setUint32(20, dataBytes.length, true);
            cv.setUint32(24, dataBytes.length, true);
            cv.setUint16(28, nameBytes.length, true);
            cv.setUint32(42, offset, true);
            central.set(nameBytes, 46);
            centralParts.push(central);

            offset += local.length;
        });

        const centralSize = centralParts.reduce((s, p) => s + p.length, 0);
        const eocd = new Uint8Array(22);
        const ev = new DataView(eocd.buffer);
        ev.setUint32(0, 0x06054b50, true);
        ev.setUint16(8, files.length, true);
        ev.setUint16(10, files.length, true);
        ev.setUint32(12, centralSize, true);
        ev.setUint32(16, offset, true);

        return new Blob([...localParts, ...centralParts, eocd], {
            type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
        });
    }

    function triggerDownload(blob, filename) {
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = filename;
        document.body.appendChild(a);
        a.click();
        a.remove();
        setTimeout(() => URL.revokeObjectURL(url), 1500);
    }

    function downloadArchive(id) {
        const item = archives.find(a => a.id === id);
        if (!item) return;
        const snap = getSnapshot(item);

        if (item.format === 'CSV') {
            triggerDownload(new Blob([toCSV(snap)], { type: 'text/csv;charset=utf-8' }), item.title);
        } else if (item.format === 'XLSX') {
            triggerDownload(buildXlsxBlob(item, snap), item.title);
        } else {
            triggerDownload(buildPdfBlob(item, snap), item.title);
        }
        showToast(`Downloading ${item.title}`, 'success');
    }

    /* =====================================================
       ACCESSIBILITY: aria-expanded sync + Escape closes dropdowns
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

    /* =====================================================
       INIT
    ===================================================== */
    document.addEventListener('DOMContentLoaded', () => {
        ['exportFormat', 'reportType', 'quarterSelect', 'departmentSelect'].forEach(id => $(id).addEventListener('change', updateLivePreview));
        $('resetFilters').addEventListener('click', resetFilters);
        $('btnGenerate').addEventListener('click', generateReport);
        $('btnCancel').addEventListener('click', () => genController && genController.abort());

        const input = $('archiveSearchInput');
        input.addEventListener('input', () => { search = input.value; $('searchClear').hidden = !search; renderArchive(); });
        input.addEventListener('keydown', e => { if (e.key === 'Escape' && input.value) { input.value = ''; search = ''; $('searchClear').hidden = true; renderArchive(); } });
        $('searchClear').addEventListener('click', () => { input.value = ''; search = ''; $('searchClear').hidden = true; renderArchive(); input.focus(); });
        document.querySelectorAll('[data-format]').forEach(b => b.addEventListener('click', () => { formatFilter = b.dataset.format; animateAll = true; renderArchive(); }));
        $('archiveSort').addEventListener('change', e => { sortKey = e.target.value; animateAll = true; renderArchive(); });

        // one listener for every card button
        $('archiveGrid').addEventListener('click', e => {
            const btn = e.target.closest('[data-action]');
            if (!btn) return;
            const id = Number(btn.closest('.archive-card').dataset.id);
            ({ preview: openPreviewModal, download: downloadArchive, delete: deleteArchiveItem })[btn.dataset.action](id);
        });

        const dlg = $('previewDialog');
        $('modalCloseX').addEventListener('click', () => dlg.close());
        $('modalCloseBtn').addEventListener('click', () => dlg.close());
        dlg.addEventListener('click', e => { if (e.target === dlg) dlg.close(); });

        // block navigating away mid-generation by accident
        window.addEventListener('beforeunload', e => { if (isGenerating) { e.preventDefault(); e.returnValue = ''; } });
        setInterval(() => { $('lastGeneratedTime').textContent = relTime(lastGeneratedAt); }, 30000);

        updateLivePreview();
        renderArchive();
        renderStats();
        initDropdownA11y();
    });
    </script>
    <script src="{{ asset('session.js') }}?v=8"></script>

    
    {{-- DARK MODE --}}
    @include('partials.themeswitcher')
</body>
</html>