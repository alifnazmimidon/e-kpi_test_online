<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Profile Settings - E-KPI System</title>
    <link rel="stylesheet" href="{{ asset('styleprofile.css') }}">

    <script>
        function showHeaderAvatarFallback() {
            const img = document.getElementById('headerUserProfilePic');
            const svg = document.getElementById('defaultAvatarSvg');
            if (img) img.style.display = 'none';
            if (svg) svg.style.display = '';
        }
        function showCardAvatarFallback() {
            const img = document.getElementById('cardUserProfilePic');
            const svg = document.getElementById('cardDefaultAvatarSvg');
            if (img) img.style.display = 'none';
            if (svg) svg.style.display = 'block';
        }
    </script>
    <script src="{{ asset('session.js') }}?v=8"></script>
</head>
<body>

    <a class="skip-link" href="#main">Skip to main content</a>

    <!-- SVG ICON SPRITE -->
    <svg width="0" height="0" style="position:absolute" aria-hidden="true" focusable="false">
        <symbol id="i-camera" viewBox="0 0 24 24"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"/><circle cx="12" cy="13" r="4"/></symbol>
        <symbol id="i-lock" viewBox="0 0 24 24"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></symbol>
        <symbol id="i-star" viewBox="0 0 24 24"><path d="m12 2 3.1 6.3 6.9 1-5 4.9 1.2 6.8L12 17.8 5.8 21l1.2-6.8-5-4.9 6.9-1z"/></symbol>
        <symbol id="i-history" viewBox="0 0 24 24"><path d="M3 12a9 9 0 1 0 3-6.7L3 8"/><path d="M3 3v5h5M12 7v5l4 2"/></symbol>
        <symbol id="i-plus" viewBox="0 0 24 24"><path d="M12 5v14M5 12h14"/></symbol>
        <symbol id="i-x" viewBox="0 0 24 24"><path d="M18 6 6 18M6 6l12 12"/></symbol>
        <symbol id="i-check" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="m9 12 2 2 4-4"/></symbol>
        <symbol id="i-alert-circle" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M12 8v4M12 16h.01"/></symbol>
        <symbol id="i-alert" viewBox="0 0 24 24"><path d="M10.3 3.9 1.8 18a2 2 0 0 0 1.7 3h17a2 2 0 0 0 1.7-3L13.7 3.9a2 2 0 0 0-3.4 0z"/><path d="M12 9v4M12 17h.01"/></symbol>
        <symbol id="i-info" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M12 16v-4M12 8h.01"/></symbol>
        <symbol id="i-save" viewBox="0 0 24 24"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><path d="M17 21v-8H7v8M7 3v5h8"/></symbol>
        <symbol id="i-loader" viewBox="0 0 24 24"><path d="M21 12a9 9 0 1 1-6.2-8.6"/></symbol>
        <symbol id="i-arrow-left" viewBox="0 0 24 24"><path d="M19 12H5M12 19l-7-7 7-7"/></symbol>
        <symbol id="i-briefcase" viewBox="0 0 24 24"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></symbol>
        <symbol id="i-building" viewBox="0 0 24 24"><rect x="4" y="2" width="16" height="20" rx="2"/><path d="M9 22v-4h6v4M8 6h.01M12 6h.01M16 6h.01M8 10h.01M12 10h.01M16 10h.01M8 14h.01M12 14h.01M16 14h.01"/></symbol>
    </svg>

    @php
        $currentUser = $user ?? Auth::user();
        $displayName = $currentUser->full_name ?? $currentUser->name ?? 'User';
        $workerCode  = $currentUser->worker_id ?? ($currentUser->id ?? 'ILM0001');
        $emailVal    = $currentUser->email ?? '';
        $phoneVal    = $currentUser->phone ?? $currentUser->phone_number ?? '';
        $deptVal = $currentUser->department->department_name ?? ($currentUser->department_name ?? 'No Department');

        $positionsList = [];
        if (!empty($currentUser->positions)) {
            if (is_string($currentUser->positions)) {
                $decoded = json_decode($currentUser->positions, true);
                $positionsList = is_array($decoded) ? $decoded : [$currentUser->positions];
            } elseif (is_array($currentUser->positions)) {
                $positionsList = $currentUser->positions;
            }
        }

        $positionsList = array_values(array_filter($positionsList, fn($p) => is_string($p) && trim($p) !== '' && trim($p) !== '[]'));

        if (empty($positionsList)) {
            $fallbackRole = $currentUser->designation ?? $currentUser->position ?? null;
            if (!empty($fallbackRole)) {
                $positionsList = [$fallbackRole];
            }
        }
        $latestPosition = count($positionsList) ? end($positionsList) : '-';

        $rawPhoto = $currentUser->profile_photo ?? $currentUser->profile_pic_url ?? '';
        $photoUrl = '';
        if (!empty($rawPhoto)) {
            $photoUrl = (str_starts_with($rawPhoto, 'http://') || str_starts_with($rawPhoto, 'https://') || str_starts_with($rawPhoto, '/'))
                ? $rawPhoto
                : asset('storage/' . ltrim($rawPhoto, '/'));
        }

        $role = strtoupper(trim($currentUser->role ?? ''));
        $isHr = $role === 'HR';
        $isLeader = in_array($role, ['TEAM LEADER', 'TEAM_LEADER', 'LEADER'], true);

        if ($isHr) {
            $dashboardUrl = Route::has('hr.dashboard') ? route('hr.dashboard') : url('/hr');
        } elseif ($isLeader) {
            $dashboardUrl = Route::has('leader.dashboard') ? route('leader.dashboard') : url('/leader');
        } else {
            $dashboardUrl = Route::has('worker.dashboard') ? route('worker.dashboard') : url('/worker');
        }
    @endphp
    <!-- ================================ HEADER ================================ -->
    <header class="header">
        <div class="header-left">
            <div class="ilm-logo"><img src="{{ asset('assets/ILMgroups-logo-4.png') }}" alt="ILM Groups logo"></div>
            <div class="header-line" aria-hidden="true"></div>
            <span class="system-title">E-KPI SYSTEM</span>
        </div>

        <div class="header-right">
            <div class="profile-wrapper">
                <button class="header-icon profile-btn" id="profileBtn" type="button" aria-label="User profile">
                    <div class="profile-avatar-wrapper">
                        <svg id="defaultAvatarSvg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#ffffff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" style="{{ $photoUrl ? 'display: none;' : '' }}">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                            <circle cx="12" cy="7" r="4"></circle>
                        </svg>
                        <img @if($photoUrl) src="{{ $photoUrl }}" @endif id="headerUserProfilePic" alt="" class="profile-avatar-img" style="{{ $photoUrl ? 'display: block;' : 'display: none;' }}" onerror="showHeaderAvatarFallback()">
                    </div>
                </button>
            </div>
        </div>
    </header>

    <!-- ============================== MAIN CONTAINER ============================== -->
    <main class="profile-container" id="main">
        <div class="profile-card reveal" style="--i:1">

            <!-- BANNER -->
            <div class="profile-banner">
                <div class="avatar-upload-container" id="avatarDrop">
                    <div class="avatar-preview">
                        <svg id="bannerDefaultAvatarSvg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#cbd5e1" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" style="{{ $photoUrl ? 'display: none;' : '' }}">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                            <circle cx="12" cy="7" r="4"></circle>
                        </svg>
                        <img @if($photoUrl) src="{{ $photoUrl }}" @endif id="avatarPreview" alt="Profile picture" style="{{ $photoUrl ? 'display: block;' : 'display: none;' }}">
                        <span class="avatar-drop-hint" aria-hidden="true">Drop image</span>
                        <span class="avatar-busy" id="avatarBusy" hidden aria-hidden="true"><svg class="icon"><use href="#i-loader"/></svg></span>
                    </div>
                    <input type="file" id="imageUpload" accept="image/png, image/jpeg, image/webp">
                    <label for="imageUpload" class="upload-badge-btn" title="Upload new picture">
                        <svg class="icon" aria-hidden="true"><use href="#i-camera"/></svg>
                        <span class="sr-only">Upload new profile picture</span>
                    </label>
                    <button type="button" class="remove-photo-btn" id="removePhotoBtn" aria-label="Remove profile picture" title="Remove picture" {{ $photoUrl ? '' : 'hidden' }}>
                        <svg class="icon" aria-hidden="true"><use href="#i-x"/></svg>
                    </button>
                </div>

                <div class="user-header-info">
                    <h2 id="displayUserName">{{ $displayName }}</h2>
                    <p><svg class="icon" aria-hidden="true"><use href="#i-briefcase"/></svg><span id="displayPrimaryRole">{{ $latestPosition }}</span></p>
                    <span class="dept-tag"><svg class="icon" aria-hidden="true"><use href="#i-building"/></svg><span id="displayDepartment">{{ $deptVal }}</span></span>
                </div>

                <div class="completeness" aria-live="polite">
                    <div class="completeness-top"><span>Profile completeness</span><strong id="completeVal">0%</strong></div>
                    <div class="completeness-track" role="progressbar" aria-label="Profile completeness" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0" id="completeTrack">
                        <div class="completeness-bar" id="completeBar"></div>
                    </div>
                    <div class="completeness-hint" id="completeHint"></div>
                </div>
            </div>

            <p class="field-error" id="avatarError" role="alert" hidden style="margin: 12px 32px 0"></p>

            <!-- FORM -->
            <form id="profileForm" class="profile-form" novalidate>
                <div class="form-grid">

                    <div class="form-group reveal" style="--i:2">
                        <label for="full_name">Full Name<span class="req" aria-hidden="true">*</span></label>
                        <input type="text" id="full_name" name="full_name" value="{{ $displayName }}" required autocomplete="name" aria-describedby="nameError">
                        <span class="field-error" id="nameError" role="alert" hidden></span>
                    </div>

                    <div class="form-group reveal" style="--i:3">
                        <label for="worker_id">Worker ID</label>
                        <div class="input-wrap">
                            <input type="text" id="worker_id" value="{{ $workerCode }}" readonly class="readonly-input" title="Managed by HR system" aria-readonly="true">
                            <svg class="icon lock" aria-hidden="true"><use href="#i-lock"/></svg>
                        </div>
                    </div>

                    <div class="form-group reveal" style="--i:4">
                        <label for="email">Email Address<span class="req" aria-hidden="true">*</span></label>
                        <input type="email" id="email" name="email" value="{{ $emailVal }}" required autocomplete="email" aria-describedby="emailError">
                        <span class="field-error" id="emailError" role="alert" hidden></span>
                    </div>

                    <div class="form-group reveal" style="--i:5">
                        <label for="phone">Phone Number</label>
                        <input type="tel" id="phone" name="phone" value="{{ $phoneVal }}" autocomplete="tel" aria-describedby="phoneError">
                        <span class="field-error" id="phoneError" role="alert" hidden></span>
                    </div>

                    <div class="form-group reveal" style="--i:6">
                        <label for="department">Department</label>
                        <div class="input-wrap">
                            <input type="text" id="department" value="{{ $deptVal }}" readonly class="readonly-input" title="Managed by HR system" aria-readonly="true">
                            <svg class="icon lock" aria-hidden="true"><use href="#i-lock"/></svg>
                        </div>
                    </div>

                    <!-- POSITIONS -->
                    <div class="form-group full-width reveal" style="--i:7">
                        <label for="newPositionInput">Job Positions (Historical & Current)</label>
                        <div class="position-input-group">
                            <div class="input-wrap">
                                <input type="text" id="newPositionInput" maxlength="60" placeholder="Enter new position title (e.g. Talent Acquisition Specialist)..." aria-describedby="positionError positionHint" autocomplete="off">
                            </div>
                            <button type="button" class="btn-add-position" id="addPositionBtn">
                                <svg class="icon" aria-hidden="true"><use href="#i-plus"/></svg>Add Position
                            </button>
                        </div>
                        <div style="display:flex;align-items:center;gap:8px">
                            <span class="field-error" id="positionError" role="alert" hidden></span>
                            <span class="char-count" id="positionCount" aria-hidden="true">0 / 60</span>
                        </div>
                        <p class="field-hint" id="positionHint">
                            <svg class="icon" aria-hidden="true"><use href="#i-info"/></svg>
                            Saved positions are kept permanently in your service record and cannot be removed. Positions you add now can still be removed until you save.
                        </p>
                        <div class="positions-badges-container" id="positionsContainer" aria-live="polite"></div>
                    </div>

                </div>

                <div class="form-actions">
                    <button type="submit" class="save-profile-btn" id="saveProfileBtn" disabled>
                        <svg class="icon btn-icon" aria-hidden="true"><use href="#i-save"/></svg>
                        <svg class="icon btn-spinner" aria-hidden="true"><use href="#i-loader"/></svg>
                        <span>Save Changes</span>
                    </button>
                    <button type="button" class="cancel-btn" id="backBtn">
                        <svg class="icon" aria-hidden="true"><use href="#i-arrow-left"/></svg>Back to Dashboard
                    </button>
                    <span class="unsaved-pill" id="unsavedPill" hidden>Unsaved changes</span>
                    <button type="button" class="link-btn" id="resetBtn" hidden>Discard changes</button>
                    <p id="profileMessage" class="form-message" role="status"></p>
                </div>
            </form>

        </div>
    </main>

    <!-- CONFIRM DIALOG -->
    <dialog class="confirm-dialog" id="confirmDialog" aria-labelledby="confirmTitle" aria-describedby="confirmDesc">
        <form method="dialog">
            <div class="confirm-body">
                <div class="confirm-icon"><svg class="icon" aria-hidden="true"><use href="#i-alert"/></svg></div>
                <div>
                    <h3 id="confirmTitle"></h3>
                    <p id="confirmDesc"></p>
                </div>
            </div>
            <div class="confirm-actions">
                <button type="submit" value="cancel" class="btn-secondary" id="confirmCancel">Cancel</button>
                <button type="submit" value="confirm" class="btn-primary" id="confirmOk">Confirm</button>
            </div>
        </form>
    </dialog>

    <div class="toast-region" id="toastRegion" aria-live="polite"></div>

    <!-- LARAVEL SEED DATA -->
    <?php
        $profileSeed = [
            'workerId'        => $workerCode,
            'fullName'        => $displayName,
            'email'           => $emailVal,
            'phone'           => $phoneVal,
            'department'      => $deptVal,
            'positions'       => array_values($positionsList),
            'profile_pic_url' => $photoUrl,
            'updateUrl'       => route('profile.update'),
            'dashboardUrl'    => $dashboardUrl,
        ];
    ?>
    <script type="application/json" id="laravelProfileSeed">
        <?php echo json_encode($profileSeed, JSON_HEX_TAG); ?>
    </script>

    <script>
    const $ = id => document.getElementById(id);
    const prefersReduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    const icon = (name, cls = '') => `<svg class="icon ${cls}" aria-hidden="true"><use href="#i-${name}"/></svg>`;
    const esc = s => String(s ?? '').replace(/[&<>"']/g, c => ({
    '&': '\x26amp;',
    '<': '\x26lt;',
    '>': '\x26gt;',
    '"': '\x26quot;',
    "'": '\x26#39;'
    }[c]));
    const csrfToken = () => document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

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

    function setError(fieldId, errId, message) {
        const f = $(fieldId), e = $(errId);
        if (f) f.setAttribute('aria-invalid', String(!!message));
        e.hidden = !message;
        e.innerHTML = message ? icon('alert-circle') + esc(message) : '';
    }

    function confirmDialog({ title, desc, ok = 'Confirm', cancel = 'Cancel' }) {
        const dlg = $('confirmDialog');
        if (typeof dlg.showModal !== 'function') return Promise.resolve(window.confirm(`${title}\n\n${desc}`));
        $('confirmTitle').textContent = title; $('confirmDesc').textContent = desc;
        $('confirmOk').textContent = ok; $('confirmCancel').textContent = cancel;
        return new Promise(resolve => {
            dlg.returnValue = '';
            dlg.addEventListener('close', () => resolve(dlg.returnValue === 'confirm'), { once: true });
            dlg.showModal();
        });
    }

    const SEED = JSON.parse($('laravelProfileSeed').textContent.trim());
    const EMAIL_RE = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    const PHONE_CHARS = /^\+?[0-9\s\-()]+$/;

    let snapshot = { name: SEED.fullName, email: SEED.email, phone: SEED.phone };
    let savedPositions = Array.isArray(SEED.positions) ? [...SEED.positions] : [];
    let pendingPositions = [];
    let savedPic = SEED.profile_pic_url || '';
    let pendingPic;
    let allowLeave = false;

    const allPositions = () => [...savedPositions, ...pendingPositions];
    const currentPic = () => pendingPic !== undefined ? pendingPic : savedPic;

    function formValues() {
        return { name: $('full_name').value.trim(), email: $('email').value.trim(), phone: $('phone').value.trim() };
    }
    function isDirty() {
        const v = formValues();
        return v.name !== snapshot.name || v.email !== snapshot.email || v.phone !== snapshot.phone
            || pendingPositions.length > 0 || pendingPic !== undefined;
    }

    const nameError  = v => v.length < 2 ? 'Please enter your full name (at least 2 characters).' : '';
    const emailError = v => !v ? 'Email address is required.' : !EMAIL_RE.test(v) ? 'Please enter a valid email address.' : '';
    function phoneError(v) {
        if (!v) return '';
        const digits = v.replace(/\D/g, '').length;
        return (!PHONE_CHARS.test(v) || digits < 8 || digits > 15) ? 'Enter a valid phone number (8–15 digits, e.g. +60 12-345 6789).' : '';
    }

    function validateForm() {
        const v = formValues();
        const checks = [
            ['full_name', 'nameError', nameError(v.name)],
            ['email', 'emailError', emailError(v.email)],
            ['phone', 'phoneError', phoneError(v.phone)]
        ];
        checks.forEach(([f, e, msg]) => setError(f, e, msg));
        const bad = checks.find(c => c[2]);
        if (bad) $(bad[0]).focus();
        return !bad;
    }

    function updateCompleteness() {
        const v = formValues();
        const items = [
            ['name', !nameError(v.name)],
            ['email', !emailError(v.email)],
            ['phone', !!v.phone && !phoneError(v.phone)],
            ['photo', !!currentPic()],
            ['position', allPositions().length > 0]
        ];
        const pct = Math.round(items.filter(i => i[1]).length / items.length * 100);
        const missing = items.filter(i => !i[1]).map(i => i[0]);
        $('completeVal').textContent = pct + '%';
        $('completeTrack').setAttribute('aria-valuenow', pct);
        $('completeBar').style.setProperty('--p', pct / 100);
        $('completeBar').classList.toggle('done', pct === 100);
        $('completeHint').textContent = missing.length ? `Add: ${missing.join(', ')}` : 'Your profile is complete.';
    }

    function syncUI() {
        const dirty = isDirty();
        $('saveProfileBtn').disabled = !dirty;
        $('unsavedPill').hidden = !dirty;
        $('resetBtn').hidden = !dirty;
        const name = $('full_name').value.trim();
        $('displayUserName').textContent = name || snapshot.name;
        $('displayDepartment').textContent = $('department').value;
        updateCompleteness();
    }

    const ALLOWED = ['image/png', 'image/jpeg', 'image/webp'];

    function applyAvatar(src) {
        const has = !!src;
        [['avatarPreview', 'bannerDefaultAvatarSvg'], ['headerUserProfilePic', 'defaultAvatarSvg']].forEach(([img, svg]) => {
            const i = $(img), s = $(svg);
            if (has) { i.src = src; i.style.display = 'block'; if (s) s.style.display = 'none'; }
            else { i.removeAttribute('src'); i.style.display = 'none'; if (s) s.style.display = 'block'; }
        });
        $('removePhotoBtn').hidden = !has;
    }

    function processImage(file) {
        return new Promise((resolve, reject) => {
            const url = URL.createObjectURL(file), img = new Image();
            img.onload = () => {
                const s = Math.min(img.width, img.height), c = document.createElement('canvas');
                c.width = c.height = 256;
                c.getContext('2d').drawImage(img, (img.width - s) / 2, (img.height - s) / 2, s, s, 0, 0, 256, 256);
                URL.revokeObjectURL(url);
                resolve(c.toDataURL('image/jpeg', 0.85));
            };
            img.onerror = () => { URL.revokeObjectURL(url); reject(new Error('decode')); };
            img.src = url;
        });
    }

    async function handleImageFile(file) {
        const err = $('avatarError');
        err.hidden = true;
        if (!file) return;
        if (!ALLOWED.includes(file.type)) { err.hidden = false; err.innerHTML = icon('alert-circle') + 'Please choose a PNG, JPG or WEBP image.'; return; }
        if (file.size > 5 * 1024 * 1024) { err.hidden = false; err.innerHTML = icon('alert-circle') + 'Image is larger than 5 MB. Please choose a smaller file.'; return; }
        $('avatarBusy').hidden = false;
        try {
            pendingPic = await processImage(file);
            applyAvatar(pendingPic);
            syncUI();
        } catch {
            err.hidden = false; err.innerHTML = icon('alert-circle') + 'That image could not be read. Please try another file.';
        } finally {
            $('avatarBusy').hidden = true;
            $('imageUpload').value = '';
        }
    }
    function previewProfileImage(event) { handleImageFile(event.target.files[0]); }

    function initAvatar() {
        $('imageUpload').addEventListener('change', previewProfileImage);
        $('removePhotoBtn').addEventListener('click', () => { pendingPic = ''; applyAvatar(''); syncUI(); });

        const drop = $('avatarDrop');
        ['dragenter', 'dragover'].forEach(t => drop.addEventListener(t, e => { e.preventDefault(); drop.classList.add('dragover'); }));
        ['dragleave', 'drop'].forEach(t => drop.addEventListener(t, e => { e.preventDefault(); drop.classList.remove('dragover'); }));
        drop.addEventListener('drop', e => handleImageFile(e.dataTransfer.files[0]));
        ['dragover', 'drop'].forEach(t => window.addEventListener(t, e => e.preventDefault()));
    }

    function renderPositions() {
        const box = $('positionsContainer'), list = allPositions();
        if (!list.length) {
            box.innerHTML = '<span class="no-positions">No positions recorded.</span>';
            $('displayPrimaryRole').textContent = '-';
            return;
        }

        $('displayPrimaryRole').textContent = list[list.length - 1];
        box.innerHTML = list.map((t, i) => ({ t, i })).reverse().map(({ t, i }, n) => {
            const current = i === list.length - 1, pending = i >= savedPositions.length;
            return `
                <div class="position-badge ${current ? 'current' : ''} ${pending ? 'pending' : ''}" style="--i:${n}" data-title="${esc(t)}">
                    <span class="pos-icon">${icon(current ? 'star' : 'history')}</span>
                    <span class="pos-title">${esc(t)}</span>
                    ${current ? '<span class="current-tag">Current</span>' : ''}
                    ${pending
                        ? `<span class="new-tag">Unsaved</span><button type="button" class="pos-remove" data-remove="${esc(t)}" aria-label="Remove ${esc(t)}" title="Remove (not saved yet)">${icon('x')}</button>`
                        : `<span class="lock-icon" title="Permanent record (cannot be removed)">${icon('lock')}</span>`}
                </div>`;
        }).join('');
    }

    function addNewPosition() {
        const input = $('newPositionInput'), title = input.value.trim().replace(/\s+/g, ' ');
        if (!title) return setError('newPositionInput', 'positionError', 'Please type a position title before adding.'), input.focus();
        if (title.length < 2) return setError('newPositionInput', 'positionError', 'Position title is too short.'), input.focus();
        if (allPositions().some(p => p.toLowerCase() === title.toLowerCase()))
            return setError('newPositionInput', 'positionError', 'This position is already in your profile record.'), input.focus();

        setError('newPositionInput', 'positionError', '');
        pendingPositions.push(title);
        input.value = ''; $('positionCount').textContent = '0 / 60';
        renderPositions(); syncUI();
        input.focus();
    }

    function removePending(title) {
        const badge = [...document.querySelectorAll('.position-badge')].find(b => b.dataset.title === title);
        const done = () => { pendingPositions = pendingPositions.filter(p => p !== title); renderPositions(); syncUI(); };
        if (badge && !prefersReduced) { badge.classList.add('leaving'); setTimeout(done, 200); } else done();
    }

    async function updateProfileDetails() {
        if (!validateForm()) return;

        if (pendingPositions.length) {
            const ok = await confirmDialog({
                title: 'Save permanent positions?',
                desc: `${pendingPositions.length === 1 ? 'This position' : 'These positions'} will be added to your service record and cannot be removed afterwards: ${pendingPositions.join(', ')}.`,
                ok: 'Save permanently'
            });
            if (!ok) return;
        }

        const btn = $('saveProfileBtn'), msg = $('profileMessage'), v = formValues();
        const payload = {
            name: v.name,
            email: v.email,
            phone: v.phone,
            positions: allPositions()
        };
        if (pendingPic !== undefined) {
            payload.profile_pic_url = pendingPic;
        }

        btn.classList.add('loading'); btn.disabled = true; btn.setAttribute('aria-busy', 'true');
        try {
            const response = await fetch(SEED.updateUrl, {
                method: 'PUT',
                credentials: 'same-origin',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken(),
                    'X-Requested-With': 'XMLHttpRequest'
                },
               body: JSON.stringify({
                ...payload,
                name: payload.name || payload.full_name,
                full_name: payload.name || payload.full_name
                })
            });

            const resData = await response.json().catch(() => ({}));

            if (!response.ok) {
                if (response.status === 422 && resData.errors) {
                    if (resData.errors.name) setError('full_name', 'nameError', resData.errors.name[0]);
                    if (resData.errors.email) setError('email', 'emailError', resData.errors.email[0]);
                    if (resData.errors.phone) setError('phone', 'phoneError', resData.errors.phone[0]);
                    if (resData.errors.positions) setError('newPositionInput', 'positionError', resData.errors.positions[0]);
                    if (resData.errors.profile_pic_url) {
                        const err = $('avatarError');
                        err.hidden = false;
                        err.innerHTML = icon('alert-circle') + esc(resData.errors.profile_pic_url[0]);
                    }
                    throw new Error('Please check the highlighted fields.');
                }
                throw new Error(resData.message || 'Failed to save profile');
            }

            const finalPositions = Array.isArray(resData.positions) ? resData.positions : allPositions();
            const finalPhoto = resData.profile_pic_url !== undefined ? resData.profile_pic_url : currentPic();

            snapshot = { name: v.name, email: v.email, phone: v.phone };
            savedPositions = finalPositions;
            pendingPositions = [];
            if (pendingPic !== undefined) {
                savedPic = finalPhoto;
                pendingPic = undefined;
                applyAvatar(savedPic);
            }
            renderPositions();

            if (typeof EKPI_Session !== 'undefined' && typeof EKPI_Session.updateUserSession === 'function') {
                EKPI_Session.updateUserSession({
                    id: SEED.workerId,
                    name: v.name,
                    title: finalPositions.length ? finalPositions[finalPositions.length - 1] : '',
                    department: SEED.department,
                    profile_pic_url: savedPic,
                    positions: finalPositions
                });
            }

            msg.innerHTML = icon('check') + (resData.message || 'Profile details updated and saved!');
            msg.className = 'form-message success';
            toast('Profile saved to database.');
        } catch (err) {
            console.error(err);
            msg.innerHTML = icon('alert-circle') + esc(err.message || 'Could not save your profile. Please try again.');
            msg.className = 'form-message error';
            toast(err.message || 'Could not save your profile.', 'error');
        } finally {
            btn.classList.remove('loading'); btn.removeAttribute('aria-busy');
            syncUI();
        }
        clearTimeout(msg._t);
        msg._t = setTimeout(() => { msg.className = 'form-message'; }, 3500);
    }

    function resetChanges() {
        $('full_name').value = snapshot.name; $('email').value = snapshot.email; $('phone').value = snapshot.phone;
        pendingPositions = []; pendingPic = undefined;
        applyAvatar(savedPic);
        ['full_name:nameError', 'email:emailError', 'phone:phoneError', 'newPositionInput:positionError'].forEach(p => { const [f, e] = p.split(':'); setError(f, e, ''); });
        $('newPositionInput').value = ''; $('positionCount').textContent = '0 / 60';
        renderPositions(); syncUI();
    }

    async function goBack() {
        if (isDirty() && !(await confirmDialog({
            title: 'Discard unsaved changes?',
            desc: 'You have changes that have not been saved. If you leave now they will be lost.',
            ok: 'Discard & leave',
            cancel: 'Keep editing'
        }))) return;
        allowLeave = true;
        window.location.href = SEED.dashboardUrl;
    }

    document.addEventListener('DOMContentLoaded', () => {
        $('profileForm').addEventListener('submit', e => { e.preventDefault(); updateProfileDetails(); });
        $('addPositionBtn').addEventListener('click', addNewPosition);
        $('resetBtn').addEventListener('click', resetChanges);
        $('backBtn').addEventListener('click', goBack);

        $('newPositionInput').addEventListener('keydown', e => { if (e.key === 'Enter') { e.preventDefault(); addNewPosition(); } });
        $('newPositionInput').addEventListener('input', e => {
            $('positionCount').textContent = `${e.target.value.length} / 60`;
            if (e.target.value.trim()) setError('newPositionInput', 'positionError', '');
        });
        $('positionsContainer').addEventListener('click', e => {
            const b = e.target.closest('[data-remove]'); if (b) removePending(b.dataset.remove);
        });

        [['full_name', 'nameError', nameError], ['email', 'emailError', emailError], ['phone', 'phoneError', phoneError]].forEach(([f, e, fn]) => {
            $(f).addEventListener('input', () => { if ($(f).getAttribute('aria-invalid') === 'true' && !fn($(f).value.trim())) setError(f, e, ''); syncUI(); });
            $(f).addEventListener('blur', () => setError(f, e, fn($(f).value.trim())));
        });

        window.addEventListener('beforeunload', e => { if (!allowLeave && isDirty()) { e.preventDefault(); e.returnValue = ''; } });
        document.addEventListener('keydown', e => {
            if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 's') { e.preventDefault(); if (!$('saveProfileBtn').disabled) $('profileForm').requestSubmit(); }
        });

        if (typeof EKPI_Session !== 'undefined' && typeof EKPI_Session.updateUserSession === 'function') {
            EKPI_Session.updateUserSession({
                id: SEED.workerId,
                name: snapshot.name,
                title: savedPositions.length ? savedPositions[savedPositions.length - 1] : '',
                department: SEED.department,
                profile_pic_url: savedPic,
                positions: savedPositions
            });
        }

        initAvatar();
        applyAvatar(savedPic);
        renderPositions();
        syncUI();
    });
    </script>
</body>
</html>