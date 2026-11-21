/* ====================================================================
   SHARED USER SESSION & PROFILE SYNC MODULE (E-KPI SYSTEM)
==================================================================== */

const EKPI_Session = {
    // 1. DEFAULT USER STATE
    currentUser: {
        id: "HR001",
        name: "Ahmad HR",
        title: "HR Administrator",
        department: "Human Resources",
        profile_pic_url: "",
        positions: [
            "Talent Acquisition Executive",
            "HR Administrator"
        ]
    },

    // 2. INITIALIZE SESSION & DROPDOWNS ON PAGE LOAD
    init() {
        const cachedUser = localStorage.getItem('ekpi_user_session');
        if (cachedUser) {
            try {
                this.currentUser = JSON.parse(cachedUser);
            } catch (e) {
                console.warn("Failed to parse cached user session:", e);
            }
        }

        this.syncHeaderUI();
        this.setupDropdownListeners();
        this.setupCrossTabSync();
        this.fetchLiveSession();
    },

    // 3. SYNCHRONIZE HEADER UI (Name, Role, Avatar)
    syncHeaderUI() {
        const nameLabel = document.getElementById('dropdownUserName');
        const roleLabel = document.getElementById('dropdownUserRole');
        const headerImg = document.getElementById('headerUserProfilePic');
        const defaultSvg = document.getElementById('defaultAvatarSvg');

        if (nameLabel) {
            nameLabel.textContent = this.currentUser.name || 'User';
        }

        const primaryTitle = (this.currentUser.positions && this.currentUser.positions.length > 0)
            ? this.currentUser.positions[this.currentUser.positions.length - 1]
            : (this.currentUser.title || 'Staff');

        if (roleLabel) {
            roleLabel.textContent = primaryTitle;
        }

        if (headerImg) {
            if (this.currentUser.profile_pic_url && this.currentUser.profile_pic_url.trim() !== '') {
                headerImg.src = this.currentUser.profile_pic_url;
                headerImg.style.display = 'block';
                if (defaultSvg) defaultSvg.style.display = 'none';
            } else {
                headerImg.style.display = 'none';
                if (defaultSvg) defaultSvg.style.display = 'block';
            }
        }
    },

    // 4. UPDATE SESSION (Triggered on profile updates)
    updateUserSession(updatedData) {
        this.currentUser = { ...this.currentUser, ...updatedData };
        localStorage.setItem('ekpi_user_session', JSON.stringify(this.currentUser));
        this.syncHeaderUI();
    },

    // 5. CROSS-TAB LISTENERS
    setupCrossTabSync() {
        window.addEventListener('storage', (e) => {
            if (e.key === 'ekpi_user_session' && e.newValue) {
                try {
                    this.currentUser = JSON.parse(e.newValue);
                    this.syncHeaderUI();
                } catch (err) {
                    console.error("Error parsing cross-tab session update:", err);
                }
            }
        });
    },

    // 6. UNIFIED DROPDOWN CONTROLLER (Profile & Notification)
    setupDropdownListeners() {
        const profileBtn = document.getElementById('profileBtn');
        const profileDropdown = document.getElementById('profileDropdown');
        const notifBtn = document.getElementById('notificationBtn');
        const notifDropdown = document.getElementById('notificationDropdown');

        // Toggle Profile Dropdown
        if (profileBtn && profileDropdown) {
            profileBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                if (notifDropdown) notifDropdown.classList.remove('active');
                profileDropdown.classList.toggle('active');
            });
        }

        // Toggle Notification Dropdown
        if (notifBtn && notifDropdown) {
            notifBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                if (profileDropdown) profileDropdown.classList.remove('active');
                notifDropdown.classList.toggle('active');
            });
        }

        // Close all dropdowns when clicking outside
        document.addEventListener('click', (e) => {
            if (profileDropdown && !profileDropdown.contains(e.target) && (!profileBtn || !profileBtn.contains(e.target))) {
                profileDropdown.classList.remove('active');
            }
            if (notifDropdown && !notifDropdown.contains(e.target) && (!notifBtn || !notifBtn.contains(e.target))) {
                notifDropdown.classList.remove('active');
            }
        });
    },

    // 7. BACKEND API PLACEHOLDER
    async fetchLiveSession() {
        try {
            /* 
            const response = await fetch('/api/user/session');
            if (!response.ok) throw new Error('Session fetch failed');
            const data = await response.json();
            this.updateUserSession(data);
            */
        } catch (err) {
            console.warn("[Session Sync] Live fetch warning:", err);
        }
    }
};

// AUTO-INITIALIZE ON DOM LOAD
document.addEventListener('DOMContentLoaded', () => {
    EKPI_Session.init();
});