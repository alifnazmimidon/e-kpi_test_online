<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Team Status - E-KPI System</title>
    <link rel="stylesheet" href="{{ asset('styleev.css') }}">
</head>
<body>

    @php
        $members      = $seedData['members'] ?? [];
        $totalMembers = count($members);
        $completedArr = array_values(array_filter($members, fn($m) => isset($m['leader']) && $m['leader'] !== null));
        $doneCount    = count($completedArr);
        $pendingCount = $totalMembers - $doneCount;

        $teamAvg   = null;
        $leaderAvg = null;
        $leaderPct = 0;
        $topMember = null;

        if ($doneCount > 0) {
            $sumTotal  = 0;
            $sumLeader = 0;
            foreach ($completedArr as $cm) {
                $tScore = round(((float)$cm['task']) + ((float)$cm['att']) + ((float)$cm['leader']), 1);
                $sumTotal  += $tScore;
                $sumLeader += (float)$cm['leader'];
                if ($topMember === null || $tScore > $topMember['total']) {
                    $topMember = array_merge($cm, ['total' => $tScore]);
                }
            }
            $teamAvg   = round($sumTotal / $doneCount, 1);
            $leaderAvg = round($sumLeader / $doneCount, 1);
            $leaderPct = (int) round(($leaderAvg / 40) * 100);
        }
    @endphp

    <a class="skip-link" href="#main">Skip to main content</a>

    <!-- SVG ICON SPRITE -->
    <svg width="0" height="0" style="position:absolute" aria-hidden="true" focusable="false">
        <symbol id="i-refresh" viewBox="0 0 24 24"><path d="M21 12a9 9 0 0 0-15-6.7L3 8"/><path d="M3 3v5h5M3 12a9 9 0 0 0 15 6.7L21 16"/><path d="M16 16h5v5"/></symbol>
        <symbol id="i-search" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></symbol>
        <symbol id="i-users" viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/></symbol>
        <symbol id="i-user" viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></symbol>
        <symbol id="i-sort" viewBox="0 0 24 24"><path d="m7 15 5 5 5-5M7 9l5-5 5 5"/></symbol>
        <symbol id="i-arrow-up" viewBox="0 0 24 24"><path d="M12 19V5M5 12l7-7 7 7"/></symbol>
        <symbol id="i-arrow-down" viewBox="0 0 24 24"><path d="M12 5v14M19 12l-7 7-7-7"/></symbol>
        <symbol id="i-arrow-right" viewBox="0 0 24 24"><path d="M5 12h14M12 5l7 7-7 7"/></symbol>
        <symbol id="i-chev-l" viewBox="0 0 24 24"><path d="m15 18-6-6 6-6"/></symbol>
        <symbol id="i-chev-r" viewBox="0 0 24 24"><path d="m9 18 6-6-6-6"/></symbol>
        <symbol id="i-check" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="m9 12 2 2 4-4"/></symbol>
        <symbol id="i-clock" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></symbol>
        <symbol id="i-x" viewBox="0 0 24 24"><path d="M18 6 6 18M6 6l12 12"/></symbol>
        <symbol id="i-alert-circle" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M12 8v4M12 16h.01"/></symbol>
        <symbol id="i-info" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M12 16v-4M12 8h.01"/></symbol>
        <symbol id="i-inbox" viewBox="0 0 24 24"><path d="M22 12h-6l-2 3h-4l-2-3H2"/><path d="M5.5 5.1 2 12v6a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-6l-3.5-6.9A2 2 0 0 0 16.7 4H7.3a2 2 0 0 0-1.8 1.1z"/></symbol>
        <symbol id="i-download" viewBox="0 0 24 24"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><path d="m7 10 5 5 5-5M12 15V3"/></symbol>
        <symbol id="i-award" viewBox="0 0 24 24"><circle cx="12" cy="8" r="6"/><path d="M15.5 13.5 17 22l-5-3-5 3 1.5-8.5"/></symbol>
        <symbol id="i-medal" viewBox="0 0 24 24"><path d="M7.2 15 2.7 7.1a2 2 0 0 1 .1-2.2L4.4 2.8A2 2 0 0 1 6 2h12a2 2 0 0 1 1.6.8l1.6 2.1a2 2 0 0 1 .1 2.2L16.8 15"/><path d="M11 12 5.1 2.2M13 12l5.9-9.8M8 7h8"/><circle cx="12" cy="17" r="5"/></symbol>
        <symbol id="i-crown" viewBox="0 0 24 24"><path d="M11.6 3.3a.5.5 0 0 1 .8 0l3 5.6a1 1 0 0 0 1.5.3l4.3-3.7a.5.5 0 0 1 .8.5l-2.8 10.2a1 1 0 0 1-1 .7H5.8a1 1 0 0 1-1-.7L2 6a.5.5 0 0 1 .8-.5l4.3 3.7a1 1 0 0 0 1.5-.3z"/><path d="M5 21h14"/></symbol>
        <symbol id="i-activity" viewBox="0 0 24 24"><path d="M22 12h-4l-3 9L9 3l-3 9H2"/></symbol>
        <symbol id="i-dashboard" viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="9" rx="1"/><rect x="14" y="3" width="7" height="5" rx="1"/><rect x="14" y="12" width="7" height="9" rx="1"/><rect x="3" y="16" width="7" height="5" rx="1"/></symbol>        
        <symbol id="i-logout" viewBox="0 0 24 24"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4M16 17l5-5-5-5M21 12H9"/></symbol>        
        <symbol id="i-eye" viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></symbol>
    </svg>

    <!-- ================================ HEADER ================================ -->
    <header class="header">
        <div class="header-left">
            <span class="system-title">E-KPI SYSTEM</span>
            <div class="header-line" aria-hidden="true"></div>
            <div class="ilm-logo"><img src="{{ asset('assets/ILMgroups-logo-4.png') }}" alt="ILM Groups logo" onerror="this.style.display='none'"></div>
            <span class="company-name">INFINITE LOOP MEDIA</span>
        </div>

        <div class="header-right">
            <!-- NOTIFICATIONS -->
            <div class="notification-wrapper">
                <button type="button" class="header-icon" id="notificationBtn" aria-label="Notifications"
                        aria-haspopup="true" aria-expanded="false" aria-controls="notificationDropdown">
                    <div class="bellIcon">
                        <img src="{{ asset('assets/bellwhite.png') }}" alt="" class="img-base" onerror="this.style.display='none'">
                        <img src="{{ asset('assets/bellblack.png') }}" alt="" class="img-hover" onerror="this.style.display='none'">
                    </div>
                    <span class="notification-badge" id="notifBadge" style="{{ $pendingCount > 0 ? 'display: inline-block;' : 'display: none;' }}">{{ $pendingCount }}</span>
                </button>

                <div class="notification-dropdown" id="notificationDropdown">
                    <div class="notification-header">
                        <h3>Notifications</h3>
                        <button type="button" class="mark-read" id="markAllRead">Mark all as read</button>
                    </div>
                    <div class="notification-list" id="notificationList" aria-live="polite">
                        @if($pendingCount === 0)
                            <div class="no-notifications">
                                <svg class="icon" aria-hidden="true"><use href="#i-check"/></svg>
                                <p>No pending evaluations</p>
                            </div>
                        @else
                            @foreach($members as $m)
                                @if(!isset($m['leader']) || $m['leader'] === null)
                                    <div class="notification-item unread" data-member="{{ $m['id'] }}" role="button" tabindex="0">
                                        <h4 class="notif-title">Evaluation Required</h4>
                                        <p class="notif-desc">{{ $m['name'] }} requires team leader rating.</p>
                                        <span class="notif-time">Status: Pending</span>
                                    </div>
                                @endif
                            @endforeach
                        @endif
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
                <button type="button" class="header-icon profile-btn" id="profileBtn" aria-label="User profile menu"
                        aria-haspopup="true" aria-expanded="false" aria-controls="profileDropdown">
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
                        <span class="user-name" id="dropdownUserName">{{ $navUser->full_name ?? 'Leader' }}</span>
                        <span class="user-role" id="dropdownUserRole">{{ $navUser->designation ?? (($seedData['deptLabel'] ?? 'Department') . ' Team Lead') }}</span>
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
        <a href="{{ route('leader.dashboard') }}" class="nav-link {{ request()->routeIs('leader.dashboard') ? 'active' : '' }}">Dashboard</a>
        <a href="{{ route('leader.kpi') }}" class="nav-link {{ request()->routeIs('leader.kpi') ? 'active' : '' }}">Input Rating</a>
        <a href="{{ route('leader.team.status') }}" class="nav-link {{ request()->routeIs('leader.team.status') ? 'active' : '' }}" aria-current="page">Team Status</a>
        <a href="{{ route('leader.reports') }}" class="nav-link {{ request()->routeIs('leader.reports') ? 'active' : '' }}">Reports</a>

        <div class="sync-status reveal" style="--i:1" id="syncStatus">
            <span class="sync-dot" aria-hidden="true"></span>
            <span id="syncText" role="status" aria-live="polite">Synced just now</span>
            <button type="button" class="sync-btn" id="syncBtn" aria-label="Refresh data" title="Refresh data">
                <svg class="icon" id="syncIcon" aria-hidden="true"><use href="#i-refresh"/></svg>
            </button>
        </div>
    </nav>

    <!-- ============================== MAIN ============================== -->
    <main class="breakdown-page" id="main">

        <!-- SUMMARY CARDS -->
        <section class="summary-cards-grid reveal" style="--i:2" aria-label="Team summary">
            <div class="summary-card">
                <div class="card-icon-wrap red"><svg class="icon" aria-hidden="true"><use href="#i-activity"/></svg></div>
                <div class="card-info">
                    <span class="card-label">Team Average Score</span>
                    <strong class="card-value" id="statTeamAvg" data-val="{{ $teamAvg ?? 0 }}">{{ $teamAvg !== null ? number_format($teamAvg, 1) . '%' : '--' }}</strong>
                    <span class="card-sub" id="statTeamAvgSub">{{ $totalMembers > 0 ? "Based on {$doneCount} of {$totalMembers} completed" : '' }}</span>
                </div>
            </div>
            <button type="button" class="summary-card" id="cardPending" title="Show pending members" {{ $pendingCount === 0 ? 'disabled' : '' }}>
                <div class="card-icon-wrap dark"><svg class="icon" aria-hidden="true"><use href="#i-alert-circle"/></svg></div>
                <div class="card-info">
                    <span class="card-label">Pending Evaluations</span>
                    <strong class="card-value" id="statPendingCount" data-val="{{ $pendingCount }}">{{ $pendingCount }} {{ $pendingCount === 1 ? 'Member' : 'Members' }}</strong>
                    <span class="card-sub">Click to filter</span>
                </div>
            </button>
            <div class="summary-card">
                <div class="card-icon-wrap green"><svg class="icon" aria-hidden="true"><use href="#i-user"/></svg></div>
                <div class="card-info">
                    <span class="card-label">Leader Rating Avg</span>
                    <strong class="card-value" id="statLeaderAvg" data-val="{{ $leaderAvg ?? 0 }}">{{ $leaderAvg !== null ? number_format($leaderAvg, 1) . ' / 40' : '--' }}</strong>
                    <span class="card-sub" id="statLeaderAvgSub">{{ $doneCount > 0 ? "{$leaderPct}% of maximum" : '' }}</span>
                </div>
            </div>
            <button type="button" class="summary-card" id="cardTop" title="Inspect top performer" {{ $topMember === null ? 'disabled' : '' }}>
                <div class="card-icon-wrap gold"><svg class="icon" aria-hidden="true"><use href="#i-award"/></svg></div>
                <div class="card-info">
                    <span class="card-label">Top Performer</span>
                    <strong class="card-value" id="statTopStaff">{{ $topMember ? $topMember['name'] . ' (' . number_format($topMember['total'], 1) . '%)' : 'None yet' }}</strong>
                    <span class="card-sub">Click to inspect</span>
                </div>
            </button>
        </section>

        <!-- MAIN TABLE CARD -->
        <section class="breakdown-card reveal" style="--i:3" aria-labelledby="pageTitle">

            <div class="breakdown-title-bar">
                <div class="title-meta">
                    <h1 class="page-title" id="pageTitle">{{ strtoupper($seedData['deptName'] ?? 'WEBTECH') }} TEAM EVALUATION & STATUS</h1>
                    <span class="subtitle">Real-time status overview of individual team member task, attendance, and leader evaluation ratings.</span>
                </div>
                <button type="button" class="btn-export" id="btnExportCSV" title="Export the rows currently shown as CSV" {{ $totalMembers === 0 ? 'disabled' : '' }}>
                    <svg class="icon" aria-hidden="true"><use href="#i-download"/></svg>
                    <span>Export CSV</span>
                </button>
            </div>

            <div class="table-controls-row">
                <div class="search-input-wrap">
                    <svg class="icon search-icon" aria-hidden="true"><use href="#i-search"/></svg>
                    <input type="search" id="searchInput" placeholder="Search staff name..." aria-label="Search staff members" autocomplete="off">
                    <button type="button" class="search-clear" id="searchClear" aria-label="Clear search" hidden><svg class="icon" aria-hidden="true"><use href="#i-x"/></svg></button>
                    <span class="kbd" aria-hidden="true">/</span>
                </div>

                <div class="filter-pills" role="group" aria-label="Filter by status">
                    <button type="button" class="pill-btn" data-filter="all" aria-pressed="true">All <span id="cntAll">{{ $totalMembers }}</span></button>
                    <button type="button" class="pill-btn" data-filter="completed" aria-pressed="false">Completed <span id="cntCompleted">{{ $doneCount }}</span></button>
                    <button type="button" class="pill-btn" data-filter="pending" aria-pressed="false">Pending <span id="cntPending">{{ $pendingCount }}</span></button>
                </div>
            </div>
            <p class="result-meta" id="resultMeta" aria-live="polite">{{ $totalMembers > 0 ? "Showing {$totalMembers} of {$totalMembers} team members" : '' }}</p>

            <div class="table-responsive-wrapper" tabindex="0" role="region" aria-labelledby="pageTitle">
                <table class="breakdown-table" id="breakdownTable">
                    <thead>
                        <tr>
                            <th scope="col" style="width: 60px;">NO</th>
                            <th scope="col" style="width: 190px;" aria-sort="none"><button type="button" class="sort-btn" data-sort="name">STAFF NAME<svg class="icon sort-icon" aria-hidden="true"><use href="#i-sort"/></svg></button></th>
                            <th scope="col" style="width: 130px;" aria-sort="none"><button type="button" class="sort-btn" data-sort="tasks">TASKS (/40)<svg class="icon sort-icon" aria-hidden="true"><use href="#i-sort"/></svg></button></th>
                            <th scope="col" style="width: 150px;" aria-sort="none"><button type="button" class="sort-btn" data-sort="attendance">ATTENDANCE (/20)<svg class="icon sort-icon" aria-hidden="true"><use href="#i-sort"/></svg></button></th>
                            <th scope="col" style="width: 160px;" aria-sort="none"><button type="button" class="sort-btn" data-sort="rating">LEADER (/40)<svg class="icon sort-icon" aria-hidden="true"><use href="#i-sort"/></svg></button></th>
                            <th scope="col" style="width: 140px;" aria-sort="none"><button type="button" class="sort-btn" data-sort="total">TOTAL SCORE<svg class="icon sort-icon" aria-hidden="true"><use href="#i-sort"/></svg></button></th>
                            <th scope="col" style="width: 130px;">STATUS</th>
                            <th scope="col" style="width: 120px;">ACTION</th>
                        </tr>
                    </thead>
                    <tbody id="breakdownTbody" aria-live="polite">
                        @forelse($members as $idx => $m)
                            @php
                                $isPending = !isset($m['leader']) || $m['leader'] === null;
                                $tVal  = (float) ($m['task'] ?? 0);
                                $aVal  = (float) ($m['att'] ?? 0);
                                $lVal  = $isPending ? null : (float) $m['leader'];
                                $tot   = $isPending ? null : round($tVal + $aVal + $lVal, 1);
                                $isTop = !$isPending && $topMember && (string)$topMember['id'] === (string)$m['id'];
                            @endphp
                            <tr class="table-row-item {{ $isPending ? 'pending' : '' }}" style="--i:{{ $idx }}" data-id="{{ $m['id'] }}">
                                <td class="col-center"><strong>{{ $idx + 1 }}</strong></td>
                                <td>
                                    <strong class="staff-name">
                                        {{ $m['name'] }}
                                        @if($isTop)
                                            <span class="crown" title="Top performer"><svg class="icon" aria-hidden="true"><use href="#i-crown"/></svg></span>
                                        @endif
                                    </strong>
                                </td>
                                <td class="col-center">
                                    <div class="score-cell">
                                        <span>{{ number_format($tVal, 1) }} <small>/ 40</small></span>
                                        <div class="meter"><i style="--p:{{ min($tVal / 40, 1) }};--i:{{ $idx }}"></i></div>
                                    </div>
                                </td>
                                <td class="col-center">
                                    <div class="score-cell">
                                        <span>{{ number_format($aVal, 1) }} <small>/ 20</small></span>
                                        <div class="meter"><i style="--p:{{ min($aVal / 20, 1) }};--i:{{ $idx }}"></i></div>
                                    </div>
                                </td>
                                <td class="col-center">
                                    @if($isPending)
                                        <span class="pending-tag"><svg class="icon" aria-hidden="true"><use href="#i-clock"/></svg>PENDING</span>
                                    @else
                                        <div class="score-cell">
                                            <span>{{ number_format($lVal, 1) }} <small>/ 40</small></span>
                                            <div class="meter"><i style="--p:{{ min($lVal / 40, 1) }};--i:{{ $idx }}"></i></div>
                                        </div>
                                    @endif
                                </td>
                                <td class="col-center">
                                    @if($isPending)
                                        <span class="dash">--</span>
                                    @else
                                        <div class="score-cell">
                                            <strong>{{ number_format($tot, 1) }}%</strong>
                                            <div class="stack-mini" title="Task {{ $tVal }} · Attendance {{ $aVal }} · Leader {{ $lVal }}">
                                                <i class="s-task" style="width:{{ $tVal }}%"></i><i class="s-att" style="width:{{ $aVal }}%"></i><i class="s-leader" style="width:{{ $lVal }}%"></i>
                                            </div>
                                        </div>
                                    @endif
                                </td>
                                <td class="col-center">
                                    <span class="status-pill {{ $isPending ? 'pending' : 'complete' }}">
                                        <svg class="icon" aria-hidden="true"><use href="{{ $isPending ? '#i-clock' : '#i-check' }}"/></svg>
                                        {{ $isPending ? 'PENDING' : 'COMPLETE' }}
                                    </span>
                                </td>
                                <td class="col-center">
                                    <button type="button" class="btn-inspect" data-inspect="{{ $m['id'] }}" aria-label="Inspect {{ $m['name'] }}">
                                        <svg class="icon" aria-hidden="true"><use href="#i-eye"/></svg>Inspect
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="empty-msg">
                                    <svg class="icon" aria-hidden="true"><use href="#i-inbox"/></svg>No team members found in this department.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </section>
    </main>

    <!-- STAFF INSPECTION DIALOG -->
    <dialog class="detail-dialog" id="detailDialog" aria-labelledby="dlgTitle">
        <div class="dialog-header">
            <h3 id="dlgTitle"><svg class="icon" aria-hidden="true"><use href="#i-eye"/></svg>Staff Team Status Details</h3>
            <button type="button" class="dialog-close" id="dialogCloseX" aria-label="Close dialog"><svg class="icon" aria-hidden="true"><use href="#i-x"/></svg></button>
        </div>

        <div class="dialog-body">
            <div class="dialog-content" id="dlgContent">
                <div class="dialog-user-badge">
                    <div>
                        <strong id="dlgStaffName">STAFF NAME</strong>
                        <span class="dept" id="dlgStaffDept">{{ $seedData['deptLabel'] ?? 'WebTech' }} Department</span>
                    </div>
                    <div class="dialog-tags" id="dlgTags"></div>
                </div>

                <div class="dialog-metrics-grid">
                    <div class="metric-box">
                        <span class="m-label">Tasks (40%)</span>
                        <span class="m-val" id="dlgTaskVal">-</span>
                        <div class="meter"><i id="dlgTaskBar"></i></div>
                    </div>
                    <div class="metric-box">
                        <span class="m-label">Attendance (20%)</span>
                        <span class="m-val" id="dlgAttVal">-</span>
                        <div class="meter"><i id="dlgAttBar"></i></div>
                    </div>
                    <div class="metric-box">
                        <span class="m-label">Leader Rating (40%)</span>
                        <span class="m-val" id="dlgLeaderVal">-</span>
                        <div class="meter"><i id="dlgLeaderBar"></i></div>
                    </div>
                    <div class="metric-box">
                        <span class="m-label">Position in team</span>
                        <span class="m-val" id="dlgRankVal">-</span>
                        <div class="meter"><i id="dlgRankBar"></i></div>
                    </div>
                    <div class="metric-box highlight">
                        <span class="m-label">Overall projected</span>
                        <span class="m-val" id="dlgTotalVal">-</span>
                        <div class="stack-mini" id="dlgStack" aria-hidden="true"></div>
                    </div>
                </div>

                <div class="insight-row" id="dlgInsights"></div>
            </div>
        </div>

        <div class="dialog-footer">
            <div class="dialog-nav">
                <button type="button" id="dlgPrev" aria-label="Previous team member"><svg class="icon" aria-hidden="true"><use href="#i-chev-l"/></svg></button>
                <span class="pos" id="dlgPos"></span>
                <button type="button" id="dlgNext" aria-label="Next team member"><svg class="icon" aria-hidden="true"><use href="#i-chev-r"/></svg></button>
            </div>
            <button type="button" class="btn-modal-secondary" id="dialogCloseBtn">Close</button>
            <a href="{{ route('leader.kpi') }}" class="btn-modal-primary" id="dialogGoEval">
                <span id="dialogGoLabel">Evaluate Member</span>
                <svg class="icon" aria-hidden="true"><use href="#i-arrow-right"/></svg>
            </a>
        </div>
    </dialog>

    <div class="toast-region" id="toastRegion" aria-live="polite"></div>

    <script>
    (function () {
        const SEED_PAYLOAD  = @json($seedData ?? ['members' => []]);
        const DATA_ENDPOINT = "{{ route('leader.team.status.data') }}";
        const INPUT_KPI_URL = "{{ route('leader.kpi') }}";

        const byId = id => document.getElementById(id);
        const prefersReduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
        const icon = (name, cls = '') => `<svg class="icon ${cls}" aria-hidden="true"><use href="#i-${name}"/></svg>`;
        const round1 = n => Math.round(n * 10) / 10;

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
            const from = parseFloat(el.dataset.val) || 0;
            el.dataset.val = to;
            if (prefersReduced || from === to) { el.textContent = fmt(to); return; }
            const start = performance.now(), dur = 700;
            const step = now => {
                const p = Math.min((now - start) / dur, 1);
                el.textContent = fmt(from + (to - from) * (1 - Math.pow(1 - p, 3)));
                if (p < 1) requestAnimationFrame(step);
            };
            requestAnimationFrame(step);
        }

        function toast(text, type = 'info', duration = 3200) {
            const region = byId('toastRegion');
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

        window.showAvatarFallback = function () {
            const img = byId('headerUserProfilePic'), svg = byId('defaultAvatarSvg');
            if (img) img.style.display = 'none';
            if (svg) svg.style.display = '';
        };

        const MAX = { task: 40, att: 20, leader: 40 };
        let breakdownData = Array.isArray(SEED_PAYLOAD.members) ? SEED_PAYLOAD.members : [];
        const DEPT = SEED_PAYLOAD.deptLabel || 'WebTech';
        let dbThresholds = SEED_PAYLOAD.thresholds || { gold: 85, silver: 70, bronze: 50 };

        let currentFilter = 'all', currentSearch = '', sort = { key: null, asc: true };
        let animateRows = false, flashId = null, dialogOrder = [], dialogId = null;

        const SORT_FIELD = { name: 'name', tasks: 'task', attendance: 'att', rating: 'leader', total: 'total' };

        function derived() {
            const rows = breakdownData.map(d => {
                const pending = d.leader === null || d.leader === undefined;
                return {
                    ...d,
                    task: Number(d.task || 0),
                    att: Number(d.att || 0),
                    leader: pending ? null : Number(d.leader),
                    pending,
                    status: pending ? 'PENDING' : 'COMPLETE',
                    total: pending ? null : round1(Number(d.task || 0) + Number(d.att || 0) + Number(d.leader))
                };
            });
            const done = rows.filter(r => !r.pending).sort((a, b) => b.total - a.total);
            done.forEach((r, i) => { r.rank = i + 1; });
            const avg = done.length ? round1(done.reduce((a, r) => a + r.total, 0) / done.length) : null;
            return { rows, done, avg, top: done[0] || null };
        }

        function tierFor(total) {
            const t = dbThresholds;
            return total >= t.gold ? ['gold', 'Gold tier']
                 : total >= t.silver ? ['silver', 'Silver tier']
                 : total >= t.bronze ? ['bronze', 'Bronze tier']
                 : ['none', 'Below badge range'];
        }

        function renderSummaryStats() {
            const { rows, done, avg, top } = derived();
            const pending = rows.length - done.length;

            if (avg !== null) animateNumber(byId('statTeamAvg'), avg, v => v.toFixed(1) + '%');
            else byId('statTeamAvg').textContent = '--';
            byId('statTeamAvgSub').textContent = rows.length ? `Based on ${done.length} of ${rows.length} completed` : '';

            animateNumber(byId('statPendingCount'), pending, v => { const n = Math.round(v); return `${n} Member${n === 1 ? '' : 's'}`; });
            byId('cardPending').disabled = pending === 0;

            if (done.length) {
                const l = done.reduce((a, r) => a + r.leader, 0) / done.length;
                animateNumber(byId('statLeaderAvg'), round1(l), v => `${v.toFixed(1)} / 40`);
                byId('statLeaderAvgSub').textContent = `${Math.round(l / MAX.leader * 100)}% of maximum`;
            } else {
                byId('statLeaderAvg').textContent = '--';
                byId('statLeaderAvgSub').textContent = '';
            }

            byId('statTopStaff').textContent = top ? `${top.name} (${top.total.toFixed(1)}%)` : 'None yet';
            byId('cardTop').disabled = !top;
        }

        function renderNotifications() {
            const pending = derived().rows.filter(r => r.pending), list = byId('notificationList'), badge = byId('notifBadge');
            if (!list || !badge) return;
            if (!pending.length) {
                list.innerHTML = `<div class="no-notifications">${icon('check')}<p>No pending evaluations</p></div>`;
                badge.style.display = 'none';
                return;
            }
            list.innerHTML = pending.map(m => `
                <div class="notification-item unread" data-member="${esc(m.id)}" role="button" tabindex="0">
                    <h4 class="notif-title">Evaluation Required</h4>
                    <p class="notif-desc">${esc(m.name)} requires team leader rating.</p>
                    <span class="notif-time">Status: Pending</span>
                </div>`).join('');
            badge.textContent = pending.length;
            badge.style.display = 'inline-block';
        }

        function visibleRows() {
            const { rows } = derived(), q = currentSearch.toLowerCase();
            let list = rows.filter(r => (currentFilter === 'all' || (currentFilter === 'completed' ? !r.pending : r.pending)) && (!q || r.name.toLowerCase().includes(q)));
            if (sort.key) {
                const f = SORT_FIELD[sort.key], dir = sort.asc ? 1 : -1;
                list = [...list].sort((a, b) => {
                    const x = a[f], y = b[f];
                    if (x === null && y === null) return 0;
                    if (x === null) return 1;
                    if (y === null) return -1;
                    return (typeof x === 'string' ? x.localeCompare(y) : x - y) * dir;
                });
            }
            return list;
        }

        function highlight(name, q) {
            const i = q ? name.toLowerCase().indexOf(q.toLowerCase()) : -1;
            return i < 0 ? esc(name) : `${esc(name.slice(0, i))}<mark>${esc(name.slice(i, i + q.length))}</mark>${esc(name.slice(i + q.length))}`;
        }

        const scoreCell = (v, max, i) => `<div class="score-cell"><span>${v.toFixed(1)} <small>/ ${max}</small></span><div class="meter"><i style="--p:${Math.min(v / max, 1)};--i:${i}"></i></div></div>`;

        function renderTable() {
            const { rows, top } = derived(), tbody = byId('breakdownTbody'), list = visibleRows();
            if (!tbody) return;

            byId('cntAll').textContent = rows.length;
            byId('cntCompleted').textContent = rows.filter(r => !r.pending).length;
            byId('cntPending').textContent = rows.filter(r => r.pending).length;
            document.querySelectorAll('.pill-btn').forEach(b => b.setAttribute('aria-pressed', String(b.dataset.filter === currentFilter)));
            document.querySelectorAll('th[aria-sort]').forEach(th => {
                const b = th.querySelector('.sort-btn'), on = b.dataset.sort === sort.key;
                th.setAttribute('aria-sort', on ? (sort.asc ? 'ascending' : 'descending') : 'none');
                b.querySelector('use').setAttribute('href', on ? (sort.asc ? '#i-arrow-up' : '#i-arrow-down') : '#i-sort');
            });
            byId('resultMeta').textContent = rows.length ? `Showing ${list.length} of ${rows.length} team members` : '';
            byId('btnExportCSV').disabled = !list.length;

            if (!list.length) {
                tbody.innerHTML = `<tr><td colspan="8" class="empty-msg">${icon('inbox')}No team members match your criteria.</td></tr>`;
                return;
            }

            tbody.innerHTML = list.map((r, i) => {
                const stack = r.pending ? '' : `<div class="stack-mini" title="Task ${r.task} · Attendance ${r.att} · Leader ${r.leader}">
                    <i class="s-task" style="width:${r.task}%"></i><i class="s-att" style="width:${r.att}%"></i><i class="s-leader" style="width:${r.leader}%"></i></div>`;
                return `
                    <tr class="table-row-item ${animateRows ? 'enter' : ''} ${r.pending ? 'pending' : ''} ${String(r.id) === String(flashId) ? 'flash' : ''}" style="--i:${i}" data-id="${esc(r.id)}">
                        <td class="col-center"><strong>${i + 1}</strong></td>
                        <td><strong class="staff-name">${highlight(r.name, currentSearch)}${top && String(top.id) === String(r.id) ? `<span class="crown" title="Top performer">${icon('crown')}</span>` : ''}</strong></td>
                        <td class="col-center">${scoreCell(r.task, MAX.task, i)}</td>
                        <td class="col-center">${scoreCell(r.att, MAX.att, i)}</td>
                        <td class="col-center">${r.pending ? `<span class="pending-tag">${icon('clock')}PENDING</span>` : scoreCell(r.leader, MAX.leader, i)}</td>
                        <td class="col-center">${r.pending ? '<span class="dash">--</span>' : `<div class="score-cell"><strong>${r.total.toFixed(1)}%</strong>${stack}</div>`}</td>
                        <td class="col-center"><span class="status-pill ${r.pending ? 'pending' : 'complete'}">${icon(r.pending ? 'clock' : 'check')}${r.pending ? 'PENDING' : 'COMPLETE'}</span></td>
                        <td class="col-center"><button type="button" class="btn-inspect" data-inspect="${esc(r.id)}" aria-label="Inspect ${esc(r.name)}">${icon('eye')}Inspect</button></td>
                    </tr>`;
            }).join('');
            animateRows = false;
            flashId = null;
        }

        function renderAll() {
            renderSummaryStats();
            renderNotifications();
            renderTable();
        }

        function openDetailModal(id, { keepOrder = false } = {}) {
            const { rows, done, avg } = derived();
            const r = rows.find(x => String(x.id) === String(id));
            if (!r) return;
            if (!keepOrder) {
                dialogOrder = visibleRows().map(x => String(x.id));
                if (!dialogOrder.includes(String(r.id))) dialogOrder = rows.map(x => String(x.id));
            }
            dialogId = String(r.id);

            byId('dlgStaffName').textContent = r.name;
            byId('dlgStaffDept').textContent = `${DEPT} Department`;
            const setMetric = (valId, barId, v, max) => {
                byId(valId).textContent = v === null ? '(PENDING)' : `${v.toFixed(1)} / ${max}`;
                byId(barId).style.setProperty('--p', v === null ? 0 : Math.min(v / max, 1));
            };
            setMetric('dlgTaskVal', 'dlgTaskBar', r.task, MAX.task);
            setMetric('dlgAttVal', 'dlgAttBar', r.att, MAX.att);
            setMetric('dlgLeaderVal', 'dlgLeaderBar', r.leader, MAX.leader);
            byId('dlgRankVal').textContent = r.pending ? '-' : `#${r.rank} of ${done.length}`;
            byId('dlgRankBar').style.setProperty('--p', r.pending || done.length < 2 ? (r.pending ? 0 : 1) : 1 - (r.rank - 1) / (done.length - 1));
            byId('dlgTotalVal').textContent = r.pending ? '--' : `${r.total.toFixed(1)}%`;
            byId('dlgStack').innerHTML = r.pending ? '' : `<i class="s-task" style="width:${r.task}%"></i><i class="s-att" style="width:${r.att}%"></i><i class="s-leader" style="width:${r.leader}%"></i>`;

            const [tc, tl] = r.pending ? ['none', 'Awaiting leader rating'] : tierFor(r.total);
            byId('dlgTags').innerHTML = `<span class="status-pill ${r.pending ? 'pending' : 'complete'}">${icon(r.pending ? 'clock' : 'check')}${r.status}</span>
                <span class="tier-tag ${tc}">${tc === 'none' ? '' : icon('medal')}${tl}</span>`;

            const ins = [];
            if (!r.pending && avg !== null) {
                const d = round1(r.total - avg);
                ins.push(d === 0 ? `<span>${icon('activity')}On par with the team average</span>`
                    : `<span class="${d > 0 ? 'up' : 'down'}">${icon(d > 0 ? 'arrow-up' : 'arrow-down')}${Math.abs(d).toFixed(1)} pts ${d > 0 ? 'above' : 'below'} team average (${avg.toFixed(1)}%)</span>`);
            } else if (r.pending) {
                ins.push(`<span class="down">${icon('alert-circle')}The leader rating (40%) is still missing</span>`);
            }
            byId('dlgInsights').innerHTML = ins.join('');

            byId('dialogGoEval').href = `${INPUT_KPI_URL}?worker_id=${encodeURIComponent(r.id)}`;
            byId('dialogGoLabel').textContent = r.pending ? 'Evaluate Member' : 'Revise Evaluation';

            const pos = dialogOrder.indexOf(String(r.id));
            byId('dlgPos').textContent = `${pos + 1} / ${dialogOrder.length}`;
            byId('dlgPrev').disabled = pos <= 0;
            byId('dlgNext').disabled = pos >= dialogOrder.length - 1;

            const c = byId('dlgContent'); c.classList.remove('swap'); void c.offsetWidth; if (keepOrder) c.classList.add('swap');
            const dlg = byId('detailDialog');
            if (!dlg.open) { if (typeof dlg.showModal === 'function') dlg.showModal(); else window.alert(`Staff overview: ${r.name}`); }
        }

        function stepDialog(delta) {
            const pos = dialogOrder.indexOf(String(dialogId)) + delta;
            if (pos >= 0 && pos < dialogOrder.length) openDetailModal(dialogOrder[pos], { keepOrder: true });
        }

        function exportToCSV() {
            const list = visibleRows();
            if (!list.length) return;
            const q = v => `"${String(v).split('"').join('""')}"`;
            const headers = ['NO', 'STAFF NAME', 'TASKS (/40)', 'ATTENDANCE (/20)', 'LEADER RATING (/40)', 'TOTAL SCORE (%)', 'STATUS'];
            const lines = list.map((r, i) => [i + 1, r.name, r.task.toFixed(1), r.att.toFixed(1), r.pending ? 'PENDING' : r.leader.toFixed(1), r.pending ? '' : r.total.toFixed(1), r.status].map(q).join(','));
            const blob = new Blob(['\ufeff' + [headers.map(q).join(','), ...lines].join('\r\n')], { type: 'text/csv;charset=utf-8' });
            const url = URL.createObjectURL(blob), a = document.createElement('a');
            a.href = url;
            a.download = `${DEPT}_Team_Status_${new Date().toISOString().slice(0, 10)}.csv`;
            document.body.appendChild(a); a.click(); a.remove();
            setTimeout(() => URL.revokeObjectURL(url), 1000);
            toast(`CSV downloaded (${list.length} ${list.length === 1 ? 'row' : 'rows'})`, 'success');
        }

        let lastSync = Date.now(), syncState = 'ok', spinDeg = 0;

        function relTime(ts) {
            const s = Math.round((Date.now() - ts) / 1000);
            if (s < 45) return 'just now';
            const m = Math.round(s / 60);
            return m < 60 ? `${m} min ago` : `${Math.round(m / 60)} h ago`;
        }

        function renderSync() {
            const box = byId('syncStatus'), text = byId('syncText'), btn = byId('syncBtn');
            if (!box || !text) return;
            box.classList.toggle('loading', syncState === 'loading');
            box.classList.toggle('error', syncState === 'error');
            text.textContent = syncState === 'loading' ? 'Syncing...'
                : syncState === 'error' ? (lastSync ? `Sync failed (last synced ${relTime(lastSync)})` : 'Sync failed')
                : `Synced ${lastSync ? relTime(lastSync) : 'just now'}`;
            if (btn) btn.setAttribute('aria-label', syncState === 'error' ? 'Retry sync' : 'Refresh data');
        }

        async function syncTeamStatus() {
            if (syncState === 'loading') return;
            syncState = 'loading';
            renderSync();

            const iconEl = byId('syncIcon');
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
                if (!response.ok) throw new Error('HTTP ' + response.status);
                const payload = await response.json();

                breakdownData = Array.isArray(payload.members) ? payload.members : [];
                if (payload.thresholds) dbThresholds = payload.thresholds;

                animateRows = true;
                renderAll();
                const dlg = byId('detailDialog');
                if (dlg && dlg.open && dialogId) openDetailModal(dialogId, { keepOrder: true });

                lastSync = Date.now();
                syncState = 'ok';
                toast('Team status synced with database.', 'success');
            } catch (err) {
                console.error('[TeamStatus] Sync error:', err);
                syncState = 'error';
                toast('Could not refresh team status. Please try again.', 'error');
            } finally {
                renderSync();
            }
        }

        function setSearch(v) {
            currentSearch = v;
            byId('searchInput').value = v;
            byId('searchClear').hidden = !v;
            renderTable();
        }

        function init() {
            const syncBtn = byId('syncBtn');
            if (syncBtn) syncBtn.addEventListener('click', syncTeamStatus);
            setInterval(renderSync, 30000);

            const search = byId('searchInput');
            if (search) {
                search.addEventListener('input', () => setSearch(search.value.trim()));
                search.addEventListener('keydown', e => { if (e.key === 'Escape' && search.value) { e.stopPropagation(); setSearch(''); } });
            }
            const searchClear = byId('searchClear');
            if (searchClear) searchClear.addEventListener('click', () => { setSearch(''); search.focus(); });

            document.addEventListener('keydown', e => {
                if (e.key === '/' && !/^(INPUT|TEXTAREA|SELECT)$/.test(document.activeElement.tagName) && !byId('detailDialog').open) {
                    e.preventDefault();
                    if (search) search.focus();
                }
            });

            document.querySelectorAll('.pill-btn').forEach(b => b.addEventListener('click', () => {
                currentFilter = b.dataset.filter;
                animateRows = true;
                renderTable();
            }));

            byId('cardPending').addEventListener('click', () => {
                currentFilter = 'pending';
                animateRows = true;
                renderTable();
                byId('breakdownTable').scrollIntoView({ behavior: prefersReduced ? 'auto' : 'smooth', block: 'center' });
            });

            byId('cardTop').addEventListener('click', () => {
                const t = derived().top;
                if (t) openDetailModal(t.id);
            });

            document.querySelectorAll('.sort-btn').forEach(b => b.addEventListener('click', () => {
                const k = b.dataset.sort;
                if (sort.key !== k) sort = { key: k, asc: true };
                else if (sort.asc) sort.asc = false;
                else sort = { key: null, asc: true };
                animateRows = true;
                renderTable();
            }));

            byId('breakdownTbody').addEventListener('click', e => {
                const row = e.target.closest('tr[data-id]');
                if (row) openDetailModal(row.dataset.id);
            });

            byId('notificationList').addEventListener('click', e => {
                const it = e.target.closest('[data-member]');
                if (it) {
                    byId('notificationDropdown').classList.remove('active');
                    openDetailModal(it.dataset.member);
                }
            });

            byId('markAllRead').addEventListener('click', () => {
                byId('notifBadge').style.display = 'none';
                toast('Notifications marked as read', 'info');
            });

            byId('btnExportCSV').addEventListener('click', exportToCSV);

            const dlg = byId('detailDialog');
            byId('dialogCloseX').addEventListener('click', () => dlg.close());
            byId('dialogCloseBtn').addEventListener('click', () => dlg.close());
            dlg.addEventListener('click', e => { if (e.target === dlg) dlg.close(); });
            byId('dlgPrev').addEventListener('click', () => stepDialog(-1));
            byId('dlgNext').addEventListener('click', () => stepDialog(1));
            dlg.addEventListener('keydown', e => { if (e.key === 'ArrowLeft') stepDialog(-1); if (e.key === 'ArrowRight') stepDialog(1); });

            renderAll();
            renderSync();
        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', init);
        } else {
            init();
        }
    })();
    </script>
    <script src="{{ asset('session.js') }}?v=8"></script>
</body>
</html>