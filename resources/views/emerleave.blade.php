<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Emergency Leave - E-KPI System</title>
    <link rel="stylesheet" href="{{ asset('styleel.css') }}">

    <style>
        .pending-requests-card {
            background: #ffffff;
            border: 1.5px solid #fecaca;
            border-radius: 16px;
            overflow: hidden;
            margin-bottom: 24px;
            box-shadow: 0 8px 24px rgba(127, 0, 0, 0.06);
        }
        .pending-requests-header {
            background: linear-gradient(135deg, #7f0000, #991b1b);
            color: #ffffff;
            padding: 14px 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
        }
        .pending-requests-header h2 {
            margin: 0;
            font-size: 1.02rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .pending-badge-count {
            background: #fef3c7;
            color: #92400e;
            font-size: 0.78rem;
            font-weight: 700;
            padding: 3px 10px;
            border-radius: 999px;
            white-space: nowrap;
        }
        .pending-list-body {
            padding: 16px 20px;
            display: flex;
            flex-direction: column;
            gap: 12px;
        }
        .pending-req-item {
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 14px 16px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            flex-wrap: wrap;
            background: #fffafa;
            transition: all 0.25s ease;
        }
        .pending-req-item.highlighted {
            border-color: #dc2626;
            background: #fef2f2;
            box-shadow: 0 0 0 3px rgba(220, 38, 38, 0.15);
        }
        .pri-info { flex: 1; min-width: 240px; }
        .pri-top {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
            margin-bottom: 4px;
        }
        .pri-name { font-size: 0.96rem; font-weight: 700; color: #0f172a; }
        .pri-dept {
            background: #fee2e2;
            color: #991b1b;
            font-size: 0.73rem;
            font-weight: 700;
            padding: 2px 8px;
            border-radius: 6px;
        }
        .pri-time { font-size: 0.75rem; color: #64748b; }
        .pri-details { font-size: 0.85rem; color: #334155; }
        .pri-notes {
            margin-top: 6px;
            font-size: 0.8rem;
            color: #475569;
            background: #ffffff;
            border-left: 3px solid #991b1b;
            padding: 5px 10px;
            border-radius: 0 6px 6px 0;
        }
        .pri-actions { display: flex; align-items: center; gap: 8px; flex-wrap: wrap; }
        .pri-btn-approve {
            display: inline-flex; align-items: center; gap: 6px;
            padding: 8px 14px; border-radius: 8px; border: none;
            background: #16a34a; color: #ffffff;
            font-size: 0.83rem; font-weight: 600; cursor: pointer;
        }
        .pri-btn-approve:hover { background: #15803d; }
        .pri-btn-reject {
            display: inline-flex; align-items: center; gap: 6px;
            padding: 8px 14px; border-radius: 8px; border: 1px solid #fecaca;
            background: #fef2f2; color: #b91c1c;
            font-size: 0.83rem; font-weight: 600; cursor: pointer;
        }
        .pri-btn-reject:hover { background: #fee2e2; }
        .pri-btn-approve:disabled, .pri-btn-reject:disabled { opacity: 0.6; cursor: not-allowed; }

        /* NOTIFIKASI LOCENG HR */
        .hr-notif-item {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            padding: 12px 14px;
            border-bottom: 1px solid #f1f5f9;
            text-decoration: none;
            color: #0f172a;
            transition: background 0.15s ease;
            cursor: pointer;
        }
        .hr-notif-item:hover { background: #fff5f5; }
        .hr-notif-item.unread { background: #fef2f2; }
        .hr-notif-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #dc2626;
            margin-top: 6px;
            flex-shrink: 0;
        }
        .hr-notif-item:not(.unread) .hr-notif-dot { background: #cbd5e1; }
        .hr-notif-body { flex: 1; min-width: 0; }
        .hr-notif-title {
            font-size: 0.84rem;
            font-weight: 700;
            color: #111827;
            margin-bottom: 2px;
        }
        .hr-notif-meta {
            font-size: 0.78rem;
            color: #475569;
            line-height: 1.35;
        }
        .hr-notif-time {
            font-size: 0.72rem;
            color: #94a3b8;
            margin-top: 4px;
            display: block;
        }
    </style>
</head>

<body>

    <a class="skip-link" href="#main">Skip to main content</a>

    <!-- SVG ICON SPRITE -->
    <svg width="0" height="0" style="position:absolute" aria-hidden="true" focusable="false">
        <symbol id="i-edit" viewBox="0 0 24 24"><path d="M12 20h9"/><path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4z"/></symbol>
        <symbol id="i-history" viewBox="0 0 24 24"><path d="M3 12a9 9 0 1 0 3-6.7L3 8"/><path d="M3 3v5h5M12 7v5l4 2"/></symbol>
        <symbol id="i-check" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="m9 12 2 2 4-4"/></symbol>
        <symbol id="i-x-circle" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="m15 9-6 6M9 9l6 6"/></symbol>
        <symbol id="i-shield" viewBox="0 0 24 24"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><path d="m9 12 2 2 4-4"/></symbol>
        <symbol id="i-trend-down" viewBox="0 0 24 24"><path d="m22 17-8.5-8.5-5 5L2 7"/><path d="M16 17h6v-6"/></symbol>
        <symbol id="i-building" viewBox="0 0 24 24"><rect x="4" y="2" width="16" height="20" rx="2"/><path d="M9 22v-4h6v4M8 6h.01M12 6h.01M16 6h.01M8 10h.01M12 10h.01M16 10h.01M8 14h.01M12 14h.01M16 14h.01"/></symbol>
        <symbol id="i-save" viewBox="0 0 24 24"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><path d="M17 21v-8H7v8M7 3v5h8"/></symbol>
        <symbol id="i-loader" viewBox="0 0 24 24"><path d="M21 12a9 9 0 1 1-6.2-8.6"/></symbol>
        <symbol id="i-alert-circle" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M12 8v4M12 16h.01"/></symbol>
        <symbol id="i-alert" viewBox="0 0 24 24"><path d="M10.3 3.9 1.8 18a2 2 0 0 0 1.7 3h17a2 2 0 0 0 1.7-3L13.7 3.9a2 2 0 0 0-3.4 0z"/><path d="M12 9v4M12 17h.01"/></symbol>
        <symbol id="i-x" viewBox="0 0 24 24"><path d="M18 6 6 18M6 6l12 12"/></symbol>
        <symbol id="i-inbox" viewBox="0 0 24 24"><path d="M22 12h-6l-2 3h-4l-2-3H2"/><path d="M5.5 5.1 2 12v6a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-6l-3.5-6.9A2 2 0 0 0 16.7 4H7.3a2 2 0 0 0-1.8 1.1z"/></symbol>
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

    <!-- ============================== NAVIGATION ============================== -->
    <nav class="navigation reveal" style="--i:1" aria-label="Main navigation">
        <a href="{{ route('hr.dashboard') }}" class="nav-link {{ request()->routeIs('hr.dashboard') ? 'active' : '' }}">Dashboard</a>
        <a href="{{ route('hr.kpi') }}" class="nav-link {{ request()->routeIs('hr.kpi') ? 'active' : '' }}">KPI Management</a>
        <a href="{{ route('hr.report') }}" class="nav-link {{ request()->routeIs('hr.report') ? 'active' : '' }}">Report</a>
        <a href="{{ route('hr.emerleave') }}" class="nav-link {{ request()->routeIs('hr.emerleave') ? 'active' : '' }}" aria-current="page">Emergency Leave</a>
    </nav>

    <!-- ============================== MAIN CONTENT ============================== -->
    <main class="emergency-leave-page" id="main">

        <h1 class="emergency-page-title reveal" style="--i:2">Emergency Leave Adjustment & Attendance Override</h1>

        <!-- ========== PENDING WORKER REQUESTS (DARI PROFIL PEKERJA) ========== -->
        <section class="pending-requests-card reveal" style="--i:2" aria-labelledby="pendingReqTitle">
            <div class="pending-requests-header">
                <h2 id="pendingReqTitle">
                    <svg class="icon" aria-hidden="true"><use href="#i-alert-circle"/></svg>
                    Pending Worker Emergency Leave Requests
                </h2>
                <span class="pending-badge-count" id="pendingReqBadge">0 Pending</span>
            </div>
            <div class="pending-list-body" id="pendingRequestsBody">
                <p style="margin:0; color:#64748b; font-size:0.88rem;">Loading pending requests...</p>
            </div>
        </section>

        <!-- ========== MANUAL ATTENDANCE OVERRIDE FORM ========== -->
        <section class="override-card reveal" style="--i:3" aria-labelledby="overrideTitle">
            <div class="override-header">
                <svg class="icon" aria-hidden="true"><use href="#i-edit"/></svg>
                <h2 id="overrideTitle">Manual Attendance Override Form</h2>
            </div>

            <form id="overrideForm" class="override-body" novalidate>

                <div class="form-group">
                    <label for="staff">Select staff:</label>
                    <select id="staff" required aria-describedby="staffHint staffError">
                        <option value="" disabled selected>Choose a staff member...</option>
                        @foreach($staffList ?? [] as $st)
                            <option value="{{ $st->full_name }}" data-dept="{{ $st->department_name ?? 'General' }}" data-worker-id="{{ $st->worker_id }}">
                                {{ $st->full_name }} ({{ $st->department_name ?? 'General' }})
                            </option>
                        @endforeach
                    </select>
                    <span class="field-hint" id="staffHint" hidden></span>
                    <span class="field-error" id="staffError" role="alert" hidden></span>
                </div>

                <div class="form-group">
                    <label for="leaveDate">Leave date:</label>
                    <input type="date" id="leaveDate" required aria-describedby="dateError">
                    <span class="field-error" id="dateError" role="alert" hidden></span>
                </div>

                <div class="form-group">
                    <label for="reason">Reason:</label>
                    <select id="reason" required>
                        <option value="Medical / Hospitalization">Medical / Hospitalization</option>
                        <option value="Medical Emergency">Medical Emergency</option>
                        <option value="Family Emergency">Family Emergency</option>
                        <option value="Personal Emergency">Personal Emergency</option>
                        <option value="Accident / Vehicle Breakdown">Accident / Vehicle Breakdown</option>
                        <option value="Natural Disaster">Natural Disaster</option>
                        <option value="Other">Other</option>
                    </select>
                </div>

                <!-- shown only when Reason = Other -->
                <div class="collapse" id="notesCollapse">
                    <div inert>
                        <div class="form-group">
                            <label for="notes">Please specify the reason:</label>
                            <textarea id="notes" maxlength="200" aria-describedby="notesError" placeholder="Briefly describe the emergency..."></textarea>
                            <span class="char-count" id="notesCount" aria-hidden="true">0 / 200</span>
                            <span class="field-error" id="notesError" role="alert" hidden></span>
                        </div>
                    </div>
                </div>

                <fieldset class="adjustment-group">
                    <legend class="adjustment-label">Adjustment Action:</legend>
                    <div class="adjustment-options">

                        <label class="radio-option approve">
                            <input type="radio" name="adjustment" value="approved" checked>
                            <span class="ro-frame"></span>
                            <span class="ro-icon"><svg class="icon" aria-hidden="true"><use href="#i-shield"/></svg></span>
                            <span class="ro-text"><strong>Approved Override</strong><small>Attendance score stays unchanged</small></span>
                            <svg class="icon ro-check" aria-hidden="true"><use href="#i-check"/></svg>
                        </label>

                        <label class="radio-option reject">
                            <input type="radio" name="adjustment" value="rejected">
                            <span class="ro-frame"></span>
                            <span class="ro-icon"><svg class="icon" aria-hidden="true"><use href="#i-trend-down"/></svg></span>
                            <span class="ro-text"><strong>Reject & Deduct (-<span class="deduct-pct">5</span>%)</strong><small>Deducted from attendance score</small></span>
                            <svg class="icon ro-check" aria-hidden="true"><use href="#i-check"/></svg>
                        </label>

                    </div>
                </fieldset>

                <div class="form-actions">
                    <button type="submit" class="save-adjustment" id="saveBtn">
                        <svg class="icon btn-icon" aria-hidden="true"><use href="#i-save"/></svg>
                        <svg class="icon btn-spinner" aria-hidden="true"><use href="#i-loader"/></svg>
                        <span>Save Adjustments</span>
                    </button>
                    <p id="saveMessage" class="override-message" role="status"></p>
                </div>
            </form>
        </section>

        <!-- ========== AUDIT LOG TRAIL ========== -->
        <section class="audit-card reveal" style="--i:4" aria-labelledby="auditTitle">
            <div class="audit-header">
                <svg class="icon" aria-hidden="true"><use href="#i-history"/></svg>
                <h2 id="auditTitle">Emergency Leave Audit Log Trail</h2>
                <div class="filter-group" role="group" aria-label="Filter audit log by status" style="margin-left:auto">
                    <button type="button" class="filter-btn" data-filter="all" aria-pressed="true">All <span id="cntAll">0</span></button>
                    <button type="button" class="filter-btn" data-filter="approved" aria-pressed="false">Approved <span id="cntApproved">0</span></button>
                    <button type="button" class="filter-btn" data-filter="rejected" aria-pressed="false">Rejected <span id="cntRejected">0</span></button>
                </div>
                <span class="log-count-badge" id="auditLogCount" style="margin-left:0">0 Entries</span>
            </div>

            <div class="audit-table-wrapper" tabindex="0" role="region" aria-labelledby="auditTitle">
                <table class="audit-table">
                    <thead>
                        <tr>
                            <th scope="col" style="width: 60px;">No.</th>
                            <th scope="col" style="width: 180px;">Staff Name</th>
                            <th scope="col" style="width: 160px;">Department</th>
                            <th scope="col" style="width: 140px;">Leave Date</th>
                            <th scope="col" style="width: 200px;">Reason</th>
                            <th scope="col" style="width: 140px;">Status</th>
                            <th scope="col" style="width: 160px;">Attendance Impact</th>
                        </tr>
                    </thead>
                    <tbody id="auditTableBody" aria-live="polite"></tbody>
                </table>
            </div>
        </section>

    </main>

    <!-- CONFIRMATION DIALOG -->
    <dialog class="confirm-dialog" id="confirmDialog" aria-labelledby="confirmTitle" aria-describedby="confirmDesc">
        <form method="dialog">
            <div class="confirm-body">
                <div class="confirm-icon"><svg class="icon" aria-hidden="true"><use href="#i-alert"/></svg></div>
                <div>
                    <h3 id="confirmTitle">Confirm attendance deduction</h3>
                    <p id="confirmDesc"></p>
                </div>
            </div>
            <div class="confirm-actions">
                <button type="submit" value="cancel" class="btn-secondary">Cancel</button>
                <button type="submit" value="confirm" class="btn-danger">Confirm Deduction</button>
            </div>
        </form>
    </dialog>

    <div class="toast-region" id="toastRegion" aria-live="polite"></div>

    <!-- LARAVEL SEED DATA -->
    <script type="application/json" id="laravelEmerLeaveSeed">
        <?php echo json_encode([
            'pendingRequests' => $pendingRequests ?? [],
            'auditLogs'       => $auditLogs ?? [],
        ], JSON_HEX_TAG); ?>
    </script>

    <script>
    const OVERRIDE_URL = @json(route('hr.emerleave.store'));
    const NOTIF_API    = @json(\Illuminate\Support\Facades\Route::has('hr.notifications') ? route('hr.notifications') : url('/hr/notifications'));
    const EMER_URL     = @json(route('hr.emerleave'));

    const DEDUCTION_PERCENT = 5;
    const $ = id => document.getElementById(id);
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
    const prefersReduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    const icon = (name, cls = '') => `<svg class="icon ${cls}" aria-hidden="true"><use href="#i-${name}"/></svg>`;
    const esc = s => {
        const d = document.createElement('div');
        d.textContent = String(s ?? '');
        return d.innerHTML;
    };

    function todayISO() {
        const d = new Date(), p = n => String(n).padStart(2, '0');
        return `${d.getFullYear()}-${p(d.getMonth() + 1)}-${p(d.getDate())}`;
    }
    const fmtDate = iso => {
        const [y, m, d] = String(iso || '').split('-');
        return d && m && y ? `${d}/${m}/${y}` : (iso || '-');
    };

    function fallbackAvatar() {
        const img = $('headerUserProfilePic'), svg = $('defaultAvatarSvg');
        if (img) img.style.display = 'none';
        if (svg) svg.style.display = '';
    }
    if (typeof window.showHeaderAvatarFallback !== 'function') window.showHeaderAvatarFallback = fallbackAvatar;
    window.showAvatarFallback = fallbackAvatar;

    function toast(text, type = 'success') {
        const el = document.createElement('div');
        el.className = 'toast ' + type;
        el.innerHTML = `${icon(type === 'success' ? 'check' : 'alert-circle')}<span>${esc(text)}</span>
                        <button type="button" aria-label="Dismiss notification">${icon('x')}</button>`;
        const close = () => { el.classList.add('out'); setTimeout(() => el.remove(), prefersReduced ? 0 : 220); };
        el.querySelector('button').addEventListener('click', close);
        $('toastRegion').appendChild(el);
        setTimeout(close, 3800);
    }

    /* =====================================================
       STATE & RENDERING (MYSQL SEED)
    ===================================================== */
    let seed = { pendingRequests: [], auditLogs: [] };
    try {
        const raw = $('laravelEmerLeaveSeed')?.textContent?.trim();
        if (raw) seed = JSON.parse(raw);
    } catch (e) {
        console.warn('Seed parse warning:', e);
    }

    let pendingRequests = Array.isArray(seed.pendingRequests) ? seed.pendingRequests : [];
    let auditLogs       = Array.isArray(seed.auditLogs) ? seed.auditLogs : [];
    let currentFilter   = 'all';

    const impactLabel = status => status === 'approved' ? 'No Deduct' : `-${DEDUCTION_PERCENT}% Deducted`;

    function renderPendingRequests(highlightId = null) {
        const body  = $('pendingRequestsBody');
        const badge = $('pendingReqBadge');
        if (badge) badge.textContent = `${pendingRequests.length} Pending`;
        if (!body) return;

        if (!pendingRequests.length) {
            body.innerHTML = `<p style="margin:0; color:#64748b; font-size:0.88rem;">All caught up — no pending emergency leave requests from staff.</p>`;
            return;
        }

        body.innerHTML = pendingRequests.map(req => {
            const isHighlighted = highlightId && Number(req.id) === Number(highlightId);
            const noteHtml = req.notes
                ? `<div class="pri-notes" style="margin-top:6px;padding:6px 10px;background:#fffbeb;border-left:3px solid #f59e0b;border-radius:6px;color:#78350f;"><strong>Details / Explanation:</strong> ${esc(req.notes)}</div>`
                : '';
            return `
                <div class="pending-req-item ${isHighlighted ? 'highlighted' : ''}" id="pending-req-${req.id}">
                    <div class="pri-info">
                        <div class="pri-top">
                            <span class="pri-name">${esc(req.staff)} (${esc(req.worker_id)})</span>
                            <span class="pri-dept">${esc(req.department)}</span>
                            <span class="pri-time">&bull; Submitted ${esc(req.submittedAt || 'recently')}</span>
                        </div>
                        <div class="pri-details">
                            Leave Date: <strong>${fmtDate(req.leaveDate)}</strong> &nbsp;|&nbsp; Reason: <strong>${esc(req.reason)}</strong>
                        </div>
                        ${noteHtml}
                    </div>
                    <div class="pri-actions">
                        <button type="button" class="pri-btn-approve" onclick="processPendingLeave(${req.id}, 'approved', this)">
                            ${icon('check')} Approve (No Deduct)
                        </button>
                        <button type="button" class="pri-btn-reject" onclick="processPendingLeave(${req.id}, 'rejected', this)">
                            ${icon('x-circle')} Reject (-${DEDUCTION_PERCENT}%)
                        </button>
                    </div>
                </div>`;
        }).join('');

        if (highlightId) {
            document.getElementById(`pending-req-${highlightId}`)?.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    }

    function renderAuditLogs({ newId = null, animateAll = false } = {}) {
        const tbody = $('auditTableBody');
        const count = s => auditLogs.filter(l => l.status === s).length;

        $('cntAll').textContent = auditLogs.length;
        $('cntApproved').textContent = count('approved');
        $('cntRejected').textContent = count('rejected');
        document.querySelectorAll('.filter-btn').forEach(b => b.setAttribute('aria-pressed', String(b.dataset.filter === currentFilter)));

        const rows = currentFilter === 'all' ? auditLogs : auditLogs.filter(l => l.status === currentFilter);
        $('auditLogCount').textContent = currentFilter === 'all'
            ? `${auditLogs.length} ${auditLogs.length === 1 ? 'Entry' : 'Entries'}`
            : `${rows.length} of ${auditLogs.length} Entries`;

        if (!rows.length) {
            tbody.innerHTML = `<tr><td colspan="7" class="empty-cell">${icon('inbox')}No emergency leave audit records found.</td></tr>`;
            return;
        }

        tbody.innerHTML = rows.map((log, i) => {
            const ok = log.status === 'approved';
            const cls = [animateAll ? 'enter' : '', log.id === newId ? 'new' : ''].join(' ');
            return `
                <tr class="${cls}" style="--i:${i}">
                    <td>${i + 1}</td>
                    <td><strong>${esc(log.staff)}</strong></td>
                    <td>${esc(log.department)}</td>
                    <td>${fmtDate(log.leaveDate)}</td>
                    <td>${esc(log.reason)}${log.notes ? `<span class="reason-note" title="${esc(log.notes)}">${esc(log.notes)}</span>` : ''}</td>
                    <td><span class="status-pill ${ok ? 'approved' : 'rejected'}">${icon(ok ? 'check' : 'x-circle')}${ok ? 'Approved' : 'Rejected'}</span></td>
                    <td><span class="impact-text ${ok ? 'no-deduct' : 'deducted'}">${icon(ok ? 'shield' : 'trend-down')}${impactLabel(log.status)}</span></td>
                </tr>`;
        }).join('');
    }

    /* =====================================================
       FORM & CONFIRMATION
    ===================================================== */
    function setError(fieldId, errId, message) {
        const field = $(fieldId), err = $(errId);
        field.setAttribute('aria-invalid', String(!!message));
        err.hidden = !message;
        err.innerHTML = message ? icon('alert-circle') + esc(message) : '';
    }
    function clearErrors() { setError('staff', 'staffError', ''); setError('leaveDate', 'dateError', ''); setError('notes', 'notesError', ''); }

    function toggleNotes() {
        const open = $('reason').value === 'Other';
        $('notesCollapse').classList.toggle('open', open);
        $('notesCollapse').firstElementChild.toggleAttribute('inert', !open);
        if (!open) setError('notes', 'notesError', '');
    }

    function showDepartment() {
        const opt = $('staff').selectedOptions[0], hint = $('staffHint');
        const dept = opt && opt.dataset.dept;
        hint.hidden = !dept;
        hint.innerHTML = dept ? icon('building') + 'Department: ' + esc(dept) : '';
        if (dept) setError('staff', 'staffError', '');
    }

    function resetForm() {
        $('overrideForm').reset();
        $('leaveDate').value = todayISO();
        $('notesCount').textContent = '0 / 200';
        $('staffHint').hidden = true;
        clearErrors();
        toggleNotes();
    }

    function confirmDeduction(staff, iso) {
        const dlg = $('confirmDialog');
        const text = `${DEDUCTION_PERCENT}% will be deducted from ${staff}'s attendance score for ${fmtDate(iso)}. This action is recorded in the audit log.`;
        if (typeof dlg.showModal !== 'function') return Promise.resolve(window.confirm(text));
        $('confirmDesc').textContent = text;
        return new Promise(resolve => {
            dlg.returnValue = '';
            dlg.addEventListener('close', () => resolve(dlg.returnValue === 'confirm'), { once: true });
            dlg.showModal();
        });
    }

    window.processPendingLeave = async function (leaveId, decision, btnEl) {
        const req = pendingRequests.find(r => Number(r.id) === Number(leaveId));
        if (!req) return;

        if (decision === 'rejected' && !(await confirmDeduction(req.staff, req.leaveDate))) {
            return;
        }

        const card = document.getElementById(`pending-req-${leaveId}`);
        card?.querySelectorAll('button').forEach(b => b.disabled = true);

        try {
            const response = await fetch(OVERRIDE_URL, {
                method: 'POST',
                credentials: 'same-origin',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({
                    leave_id:   req.id,
                    worker_id:  req.worker_id,
                    staff:      req.staff,
                    department: req.department,
                    leaveDate:  req.leaveDate,
                    reason:     req.reason,
                    notes:      req.notes || '',
                    status:     decision
                })
            });

            const result = await response.json();
            if (!response.ok || result.status !== 'success') {
                throw new Error(result.message || 'Failed to update request.');
            }

            pendingRequests = pendingRequests.filter(r => Number(r.id) !== Number(leaveId));
            auditLogs.unshift(result.record);
            currentFilter = 'all';

            renderPendingRequests();
            renderAuditLogs({ newId: result.record.id });

            if (typeof window.refreshHrBellNotifications === 'function') {
                window.refreshHrBellNotifications();
            }

            toast(`${req.staff}: ${decision === 'approved' ? 'Emergency leave approved (No deduction)' : `Rejected (-${DEDUCTION_PERCENT}% attendance deducted)`}.`);
        } catch (err) {
            console.error(err);
            card?.querySelectorAll('button').forEach(b => b.disabled = false);
            toast(err.message || 'Could not process request.', 'error');
        }
    };

    function validateForm() {
        clearErrors();
        const staff = $('staff'), date = $('leaveDate'), notes = $('notes');
        let first = null;
        const fail = (el, errId, msg) => { setError(el.id, errId, msg); first = first || el; };

        if (!staff.value) fail(staff, 'staffError', 'Please select a staff member.');
        if (!date.value) fail(date, 'dateError', 'Please select a leave date.');
        else if (date.value > todayISO()) fail(date, 'dateError', 'Leave date cannot be in the future.');
        else if (staff.value) {
            const selectedOption = staff.options ? staff.options[staff.selectedIndex] : null;
            const selectedWorkerId = selectedOption?.dataset?.workerId || '';

            const isDuplicate = auditLogs.some(l => {
                const logWorkerId = l.worker_id || l.workerId;
                const sameStaff = (logWorkerId && selectedWorkerId)
                    ? String(logWorkerId) === String(selectedWorkerId)
                    : (l.staff === staff.value);
                return sameStaff && l.leaveDate === date.value;
            });

            if (isDuplicate) {
                fail(date, 'dateError', `${staff.value} already has a finalized record for ${fmtDate(date.value)}.`);
            }
        }
        if ($('reason').value === 'Other' && !notes.value.trim()) fail(notes, 'notesError', 'Please describe the reason.');

        if (first) first.focus();
        return !first;
    }

    async function saveAdjustment() {
        if (!validateForm()) return;

        const staffSelect = $('staff'), msg = $('saveMessage'), btn = $('saveBtn');
        const selectedOpt = staffSelect.selectedOptions[0];
        const status = document.querySelector('input[name="adjustment"]:checked').value;
        const payload = {
            worker_id: selectedOpt?.dataset.workerId || '',
            staff: staffSelect.value,
            department: selectedOpt?.dataset.dept || 'General',
            leaveDate: $('leaveDate').value,
            reason: $('reason').value,
            notes: $('reason').value === 'Other' ? $('notes').value.trim() : '',
            status,
            impact: impactLabel(status)
        };

        if (status === 'rejected' && !(await confirmDeduction(payload.staff, payload.leaveDate))) return;

        btn.classList.add('loading'); btn.disabled = true; btn.setAttribute('aria-busy', 'true');
        try {
            const response = await fetch(OVERRIDE_URL, {
                method: 'POST',
                credentials: 'same-origin',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify(payload)
            });

            if (!response.ok) {
                const errText = await response.text();
                throw new Error(`HTTP ${response.status}: ${errText.slice(0, 120)}`);
            }

            const result = await response.json();
            const savedRecord = result.record;

            pendingRequests = pendingRequests.filter(r => !(String(r.worker_id) === String(savedRecord.worker_id) && r.leaveDate === savedRecord.leaveDate));
            auditLogs.unshift(savedRecord);
            currentFilter = 'all';

            renderPendingRequests();
            renderAuditLogs({ newId: savedRecord.id });

            if (typeof window.refreshHrBellNotifications === 'function') {
                window.refreshHrBellNotifications();
            }

            msg.innerHTML = icon('check') + 'Attendance adjustment saved successfully.';
            msg.className = 'override-message success';
            toast(`${savedRecord.staff}: ${savedRecord.status === 'approved' ? 'override approved' : `-${DEDUCTION_PERCENT}% deducted`}.`);
            resetForm();
        } catch (err) {
            console.error(err);
            msg.innerHTML = icon('alert-circle') + 'Error saving attendance adjustment.';
            msg.className = 'override-message error';
            toast('Could not save the adjustment. Please try again.', 'error');
        } finally {
            btn.classList.remove('loading'); btn.disabled = false; btn.removeAttribute('aria-busy');
        }

        clearTimeout(msg._t);
        msg._t = setTimeout(() => { msg.className = 'override-message'; }, 3500);
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
        document.querySelectorAll('.deduct-pct').forEach(el => { el.textContent = DEDUCTION_PERCENT; });

        $('leaveDate').value = todayISO();
        $('leaveDate').max = todayISO();

        $('overrideForm').addEventListener('submit', e => { e.preventDefault(); saveAdjustment(); });
        $('staff').addEventListener('change', showDepartment);
        $('leaveDate').addEventListener('change', () => setError('leaveDate', 'dateError', ''));
        $('reason').addEventListener('change', toggleNotes);
        $('notes').addEventListener('input', e => {
            $('notesCount').textContent = `${e.target.value.length} / 200`;
            if (e.target.value.trim()) setError('notes', 'notesError', '');
        });

        document.querySelectorAll('.filter-btn').forEach(b => b.addEventListener('click', () => {
            currentFilter = b.dataset.filter;
            renderAuditLogs({ animateAll: true });
        }));
        $('confirmDialog').addEventListener('click', e => { if (e.target === e.currentTarget) e.currentTarget.close(); });

        const reviewId = new URLSearchParams(window.location.search).get('review_id');
        toggleNotes();
        renderPendingRequests(reviewId);
        renderAuditLogs({ animateAll: true });
        initDropdownA11y();
    });
    </script>

    <script src="{{ asset('session.js') }}?v=8"></script>

    <!-- LIVE HR NOTIFICATION BELL SYNC -->
    <script>
    (() => {
        function syncBellBadge(count) {
            const badge = document.getElementById('notifBadge');
            if (!badge) return;
            if (count > 0) {
                badge.textContent = count > 9 ? '9+' : String(count);
                badge.style.setProperty('display', 'flex', 'important');
            } else {
                badge.style.setProperty('display', 'none', 'important');
            }
        }

        function renderHrNotifications(items) {
            const list = document.getElementById('notificationList');
            const rows = Array.isArray(items) ? items : [];
            syncBellBadge(rows.length);

            if (!list) return;
            if (!rows.length) {
                list.innerHTML = `
                    <div class="no-notifications" id="emptyState">
                        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
                            <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
                        </svg>
                        <p>No pending emergency leave requests</p>
                    </div>`;
                return;
            }

            list.innerHTML = rows.map(it => `
                <a href="${EMER_URL}?review_id=${encodeURIComponent(it.id)}"
                   class="hr-notif-item unread"
                   data-el-id="${it.id}">
                    <span class="hr-notif-dot" aria-hidden="true"></span>
                    <div class="hr-notif-body">
                        <div class="hr-notif-title">Emergency Leave: ${esc(it.staff)}</div>
                        <div class="hr-notif-meta">
                            <strong>${esc(it.department)}</strong> &bull; ${esc(it.leaveLabel || fmtDate(it.leaveDate))}<br>
                            Reason: ${esc(it.reason)}
                        </div>
                        <span class="hr-notif-time">${esc(it.timeAgo || it.submittedAt || 'Pending review')} &bull; Click to review</span>
                    </div>
                </a>`).join('');
        }

        async function fetchHrNotifications() {
            // Papar terus dari data halaman semasa supaya tidak kosong sementara fetch
            if (typeof pendingRequests !== 'undefined' && Array.isArray(pendingRequests)) {
                renderHrNotifications(pendingRequests);
            }

            try {
                const res = await fetch(NOTIF_API, {
                    credentials: 'same-origin',
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                });
                if (!res.ok) return;
                const data = await res.json();
                if (data.status === 'success') {
                    renderHrNotifications(data.items || []);
                }
            } catch (err) {
                console.warn('HR Notification fetch warning:', err);
            }
        }

        window.refreshHrBellNotifications = fetchHrNotifications;

        document.addEventListener('DOMContentLoaded', () => {
            fetchHrNotifications();
            // Jalankan semula selepas session.js selesai pada window.load & klik loceng
            window.addEventListener('load', () => setTimeout(fetchHrNotifications, 150));
            document.getElementById('notificationBtn')?.addEventListener('click', () => {
                setTimeout(fetchHrNotifications, 50);
            });
            setInterval(fetchHrNotifications, 30000);
        });
    })();
    </script>

</body>
</html>