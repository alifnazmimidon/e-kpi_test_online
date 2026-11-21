window.EKPI_Session = {
    currentUser: {
        id: "",
        name: "",
        title: "",
        department: "",
        profile_pic_url: "",
        positions: []
    },

    pendingLogoutForm: null,
    pendingLogoutHref: null,
    isConfirmedLogout: false,
    nativeFormSubmit: HTMLFormElement.prototype.submit,

    init() {
        this.injectLogoutModal();

        const cachedUser = localStorage.getItem('ekpi_user_session');
        if (cachedUser) {
            try {
                this.currentUser = JSON.parse(cachedUser);
                this.syncHeaderUI();
            } catch (e) {
                localStorage.removeItem('ekpi_user_session');
            }
        }

        this.setupDropdownListeners();
        this.setupCrossTabSync();
        this.setupLogoutModal();
    },

    isSafeImageUrl(url) {
        const clean = String(url || '').trim();
        return clean.startsWith('http://')
            || clean.startsWith('https://')
            || clean.startsWith('/')
            || /^data:image\/(png|jpeg|jpg|webp);base64,/i.test(clean);
    },

    syncHeaderUI() {
        const nameLabel = document.getElementById('dropdownUserName');
        const roleLabel = document.getElementById('dropdownUserRole');
        const headerImg = document.getElementById('headerUserProfilePic');
        const defaultSvg = document.getElementById('defaultAvatarSvg');

        if (nameLabel && this.currentUser.name) {
            nameLabel.textContent = this.currentUser.name;
        }

        const primaryTitle = (this.currentUser.positions && this.currentUser.positions.length > 0)
            ? this.currentUser.positions[this.currentUser.positions.length - 1]
            : this.currentUser.title;

        if (roleLabel && primaryTitle) {
            roleLabel.textContent = primaryTitle;
        }

        if (headerImg) {
            if (this.isSafeImageUrl(this.currentUser.profile_pic_url)) {
                headerImg.src = this.currentUser.profile_pic_url.trim();
                headerImg.style.display = 'block';
                if (defaultSvg) defaultSvg.style.display = 'none';
            } else {
                headerImg.removeAttribute('src');
                headerImg.style.display = 'none';
                if (defaultSvg) defaultSvg.style.display = 'block';
            }
        }
    },

    showAvatarFallback() {
        const headerImg = document.getElementById('headerUserProfilePic')
            || document.getElementById('headerAvatarImg')
            || document.querySelector('.user-avatar img');
        const defaultSvg = document.getElementById('defaultAvatarSvg')
            || document.getElementById('headerAvatarFallback')
            || document.querySelector('.user-avatar-fallback');

        if (headerImg) {
            headerImg.removeAttribute('src');
            headerImg.style.display = 'none';
        }
        if (defaultSvg) {
            defaultSvg.style.display = 'block';
        }
    },

    updateUserSession(updatedData) {
        this.currentUser = { ...this.currentUser, ...updatedData };
        localStorage.setItem('ekpi_user_session', JSON.stringify(this.currentUser));
        this.syncHeaderUI();
    },

    clearSession() {
        localStorage.removeItem('ekpi_user_session');
    },

    setupCrossTabSync() {
        if (window.__ekpiStorageBound) return;
        window.__ekpiStorageBound = true;

        window.addEventListener('storage', (e) => {
            if (e.key === 'ekpi_user_session') {
                if (!e.newValue) return;
                try {
                    this.currentUser = JSON.parse(e.newValue);
                    this.syncHeaderUI();
                } catch (err) {
                    console.error("Error parsing cross-tab session update:", err);
                }
            }
        });
    },

    setupDropdownListeners() {
        const profileBtn = document.getElementById('profileBtn');
        const profileDropdown = document.getElementById('profileDropdown');
        const notifBtn = document.getElementById('notificationBtn');
        const notifDropdown = document.getElementById('notificationDropdown');

        if (profileBtn && profileDropdown && !profileBtn.dataset.bound) {
            profileBtn.dataset.bound = '1';
            profileBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                if (notifDropdown) notifDropdown.classList.remove('active');
                profileDropdown.classList.toggle('active');
            });
        }

        if (notifBtn && notifDropdown && !notifBtn.dataset.bound) {
            notifBtn.dataset.bound = '1';
            notifBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                if (profileDropdown) profileDropdown.classList.remove('active');
                notifDropdown.classList.toggle('active');
            });
        }

        if (!document.body.dataset.dropdownOutsideBound) {
            document.body.dataset.dropdownOutsideBound = '1';
            document.addEventListener('click', (e) => {
                if (profileDropdown && !profileDropdown.contains(e.target) && (!profileBtn || !profileBtn.contains(e.target))) {
                    profileDropdown.classList.remove('active');
                }
                if (notifDropdown && !notifDropdown.contains(e.target) && (!notifBtn || !notifBtn.contains(e.target))) {
                    notifDropdown.classList.remove('active');
                }
            });
        }
    },

    injectLogoutModal() {
        if (!document.body || document.getElementById('ekpiLogoutOverlay')) return;

        const style = document.createElement('style');
        style.textContent = `
            #ekpiLogoutOverlay {
                position: fixed;
                inset: 0;
                background: rgba(15, 23, 42, 0.45);
                backdrop-filter: blur(4px);
                -webkit-backdrop-filter: blur(4px);
                display: none;
                align-items: center;
                justify-content: center;
                padding: 16px;
                z-index: 999999;
                font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            }
            #ekpiLogoutOverlay.open {
                display: flex;
                animation: ekpiFadeIn 0.18s ease-out;
            }
            .ekpi-logout-card {
                background: #ffffff;
                border-radius: 16px;
                width: 100%;
                max-width: 440px;
                padding: 24px;
                box-shadow: 0 20px 25px -5px rgba(15, 23, 42, 0.16), 0 8px 10px -6px rgba(15, 23, 42, 0.1);
                animation: ekpiScaleIn 0.18s ease-out;
            }
            .ekpi-logout-header {
                display: flex;
                align-items: center;
                justify-content: space-between;
                margin-bottom: 18px;
            }
            .ekpi-logout-title-wrap {
                display: flex;
                align-items: center;
                gap: 10px;
            }
            .ekpi-logout-title-wrap svg {
                width: 20px;
                height: 20px;
                color: #e11d48;
                flex-shrink: 0;
            }
            .ekpi-logout-title {
                margin: 0;
                font-size: 16px;
                font-weight: 700;
                color: #0f172a;
                letter-spacing: -0.01em;
            }
            .ekpi-logout-close {
                width: 34px;
                height: 34px;
                border-radius: 8px;
                border: 1.5px solid #fda4af;
                background: #ffffff;
                color: #e11d48;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                cursor: pointer;
                transition: all 0.15s ease;
            }
            .ekpi-logout-close:hover {
                background: #fff1f2;
                border-color: #e11d48;
            }
            .ekpi-logout-body {
                background: #f8fafc;
                border: 1px solid #e2e8f0;
                border-radius: 12px;
                padding: 18px 20px;
                margin-bottom: 22px;
            }
            .ekpi-logout-question {
                margin: 0 0 6px 0;
                font-size: 15px;
                font-weight: 700;
                color: #0f172a;
            }
            .ekpi-logout-subtext {
                margin: 0;
                font-size: 13px;
                line-height: 1.5;
                color: #64748b;
            }
            .ekpi-logout-footer {
                display: flex;
                align-items: center;
                justify-content: flex-end;
                gap: 10px;
            }
            .ekpi-btn-cancel {
                background: #ffffff;
                border: 1px solid #cbd5e1;
                color: #334155;
                font-size: 13.5px;
                font-weight: 600;
                padding: 10px 18px;
                border-radius: 8px;
                cursor: pointer;
                transition: all 0.15s ease;
            }
            .ekpi-btn-cancel:hover {
                background: #f1f5f9;
                border-color: #94a3b8;
            }
            .ekpi-btn-confirm {
                background: #e11d48;
                border: 1px solid #e11d48;
                color: #ffffff;
                font-size: 13.5px;
                font-weight: 600;
                padding: 10px 20px;
                border-radius: 8px;
                cursor: pointer;
                display: inline-flex;
                align-items: center;
                gap: 8px;
                transition: all 0.15s ease;
            }
            .ekpi-btn-confirm:hover {
                background: #be123c;
                border-color: #be123c;
            }
            @keyframes ekpiFadeIn {
                from { opacity: 0; }
                to { opacity: 1; }
            }
            @keyframes ekpiScaleIn {
                from { opacity: 0; transform: scale(0.96); }
                to { opacity: 1; transform: scale(1); }
            }
        `;
        document.head.appendChild(style);

        const overlay = document.createElement('div');
        overlay.id = 'ekpiLogoutOverlay';
        overlay.setAttribute('role', 'dialog');
        overlay.setAttribute('aria-modal', 'true');
        overlay.setAttribute('aria-labelledby', 'ekpiLogoutHeading');
        overlay.innerHTML = `
            <div class="ekpi-logout-card">
                <div class="ekpi-logout-header">
                    <div class="ekpi-logout-title-wrap">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
                            <polyline points="16 17 21 12 16 7"/>
                            <line x1="21" y1="12" x2="9" y2="12"/>
                        </svg>
                        <h3 class="ekpi-logout-title" id="ekpiLogoutHeading">Confirm Log Out</h3>
                    </div>
                    <button type="button" class="ekpi-logout-close" id="ekpiLogoutCloseBtn" aria-label="Close">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round">
                            <line x1="18" y1="6" x2="6" y2="18"/>
                            <line x1="6" y1="6" x2="18" y2="18"/>
                        </svg>
                    </button>
                </div>

                <div class="ekpi-logout-body">
                    <p class="ekpi-logout-question">Are you sure you want to log out?</p>
                    <p class="ekpi-logout-subtext">Your current session will end and you will need to log in again to access the system.</p>
                </div>

                <div class="ekpi-logout-footer">
                    <button type="button" class="ekpi-btn-cancel" id="ekpiLogoutCancelBtn">Cancel</button>
                    <button type="button" class="ekpi-btn-confirm" id="ekpiLogoutConfirmBtn">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
                            <polyline points="16 17 21 12 16 7"/>
                            <line x1="21" y1="12" x2="9" y2="12"/>
                        </svg>
                        <span>Yes, Log Out</span>
                    </button>
                </div>
            </div>
        `;
        document.body.appendChild(overlay);

        const closeModal = () => this.closeLogoutModal();

        document.getElementById('ekpiLogoutCloseBtn').addEventListener('click', closeModal);
        document.getElementById('ekpiLogoutCancelBtn').addEventListener('click', closeModal);

        overlay.addEventListener('click', (e) => {
            if (e.target === overlay) closeModal();
        });

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && overlay.classList.contains('open')) {
                closeModal();
            }
        });

        document.getElementById('ekpiLogoutConfirmBtn').addEventListener('click', () => {
            this.isConfirmedLogout = true;
            this.clearSession();
            if (this.pendingLogoutForm) {
                this.nativeFormSubmit.call(this.pendingLogoutForm);
            } else if (this.pendingLogoutHref && this.pendingLogoutHref !== '#') {
                window.location.href = this.pendingLogoutHref;
            }
        });
    },

    openLogoutModal(form, href) {
        this.injectLogoutModal();
        this.pendingLogoutForm = form || null;
        this.pendingLogoutHref = href || null;

        const profileDropdown = document.getElementById('profileDropdown');
        if (profileDropdown) profileDropdown.classList.remove('active');

        const overlay = document.getElementById('ekpiLogoutOverlay');
        if (overlay) {
            overlay.classList.add('open');
            const cancelBtn = document.getElementById('ekpiLogoutCancelBtn');
            if (cancelBtn) cancelBtn.focus();
        }
    },

    closeLogoutModal() {
        const overlay = document.getElementById('ekpiLogoutOverlay');
        if (overlay) overlay.classList.remove('open');
        this.pendingLogoutForm = null;
        this.pendingLogoutHref = null;
        this.isConfirmedLogout = false;
    },

    isLogoutForm(form) {
        if (!form || form.tagName !== 'FORM') return false;
        const action = (form.getAttribute('action') || '').toLowerCase();
        const id = (form.id || '').toLowerCase();
        return action.includes('logout') || id.includes('logout');
    },

    setupLogoutModal() {
        if (window.__ekpiLogoutBound) return;
        window.__ekpiLogoutBound = true;

        const self = this;
        this.injectLogoutModal();

        window.addEventListener('click', (e) => {
            if (self.isConfirmedLogout) return;
            if (e.target && e.target.closest && e.target.closest('#ekpiLogoutOverlay')) return;

            const trigger = e.target && e.target.closest
                ? e.target.closest('.logout-link, a[href*="logout" i], button[data-logout], [onclick*="logout" i], form[action*="logout" i] button, #logout-form button')
                : null;
            if (!trigger) return;

            e.preventDefault();
            e.stopImmediatePropagation();

            const form = trigger.closest('form')
                || document.getElementById('logout-form')
                || document.querySelector('form[action*="logout"]');

            const href = trigger.tagName === 'A' ? trigger.getAttribute('href') : null;
            self.openLogoutModal(form, href);
        }, true);

        window.addEventListener('submit', (e) => {
            if (self.isConfirmedLogout) return;
            if (self.isLogoutForm(e.target)) {
                e.preventDefault();
                e.stopImmediatePropagation();
                self.openLogoutModal(e.target, null);
            }
        }, true);

        HTMLFormElement.prototype.submit = function () {
            if (!self.isConfirmedLogout && self.isLogoutForm(this)) {
                self.openLogoutModal(this, null);
                return;
            }
            return self.nativeFormSubmit.apply(this, arguments);
        };
    }
};

// Alias global untuk atribut onerror pada <img> header
window.showAvatarFallback = () => window.EKPI_Session.showAvatarFallback();
window.showHeaderAvatarFallback = window.showAvatarFallback;

// Aktifkan modal serta-merta & jalankan init apabila DOM sedia
window.EKPI_Session.setupLogoutModal();
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => window.EKPI_Session.init());
} else {
    window.EKPI_Session.init();
}