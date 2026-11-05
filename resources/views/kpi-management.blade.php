<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>KPI Management - E-KPI System</title>

    <link rel="stylesheet"
          href="{{ asset('stylekpm.css') }}">

</head>


<body>
    <!-- ================================
         HEADER
    ================================= -->

    <header class="header" style="color: white;">

        <div class="header-left">

            <span class="system-title">
                E-KPI SYSTEM
            </span>

            <div class="header-line"></div>


            <!-- LOGO PLACEHOLDER -->

            <div class="ilm-logo">

                <span>
                    <img src="assets/ILMgroups-logo-4.png" alt="LOGO ILM">
                </span>

            </div>


            <span class="company-name">
                INFINITE LOOP MEDIA
            </span>

        </div>


        <div class="header-right">

            <div class="notification-wrapper">
            <!-- BELL BUTTON -->
            <button class="header-icon" id="notificationBtn" aria-label="Notifications">
                <div class="bellIcon">
                <img src="assets/bellwhite.png" alt="Normal state" class="img-base">
                <img src="assets/bellblack.png" alt="Hover state" class="img-hover">
                </div>
                <!-- Badge can be hidden when count is 0 -->
                <span class="notification-badge" id="notifBadge" style="display: none;">0</span>
            </button>

            <!-- NOTIFICATION POPUP DROPDOWN -->
            <div class="notification-dropdown" id="notificationDropdown">
                <div class="notification-header">
                <h3>Notifications</h3>
                <span class="mark-read" id="markAllRead">Mark all as read</span>
                </div>

                <!-- SCROLLABLE LIST CONTAINER -->
                <div class="notification-list" id="notificationList">

                <!-- OPTION A: EMPTY STATE (Default when no data from DB) -->
                <div class="no-notifications" id="emptyState">
                    <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
                    <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
                    </svg>
                    <p>No new notifications</p>
                </div>

                <!-- OPTION B: DYNAMIC ITEMS (Populated via DB / JS) -->
                <!--
                <div class="notification-item unread">
                    <div class="notif-content">
                    <p class="notif-title">New KPI Score Updated</p>
                    <p class="notif-desc">Your KPI score for October has been approved.</p>
                    <span class="notif-time">5 mins ago</span>
                    </div>
                </div>
                -->

                </div>
            </div>
            </div>
            <button class="header-icon">
                <center>
                    <div class="gear-icon">
                        <!-- DEFAULT IMAGE -->
                        <img src="assets/gearwhite.png" class="img-base">
                        <!-- HOVER IMAGE -->
                        <img src="assets/gearblack.png" class="img-hover">
                    </div>
                </center>
            </button>

        </div>

    </header>


    <!-- =================================================
         NAVIGATION
    ================================================== -->

        <nav class="navigation">

        <a href="{{ route('hr.dashboard') }}" class="nav-link">
            Dashboard
        </a>

        <a href="{{ route('hr.kpi') }}" class="nav-link">
            KPI Management
        </a>

        <a href="hrreport.html" class="nav-link">
            Report
        </a>

        <a href="emerleave.html" class="nav-link">
            Emergency Leave
        </a>

        </nav>


    <!-- ==================================================
         MAIN CONTENT
    ================================================== -->

    <main class="kpi-management-page">


        <!-- ==================================================
             CONFIGURATION AREA
        ================================================== -->

        <section class="configuration-area">

            <!-- WEIGHTAGE FORMULA CONFIGURATION -->
                    <div class="configuration-card">
            <div class="configuration-header">
                <h2>Weightage Formula Configuration</h2>
            </div>

            <!-- Bungkus dengan FORM untuk hantar data ke Laravel -->
            <form action="{{ route('hr.updateFormula') }}" method="POST">
                @csrf

                <div class="configuration-body">
                    <!-- E-TRACKER -->
                    <div class="slider-row">
                        <div class="slider-label">
                            <label for="etrackerWeight">E-tracker tasks weightage</label>
                        </div>
                        <div class="slider-control">
                            <input type="range" 
                                id="etrackerWeight" 
                                name="tasks_weightage" 
                                min="0" max="100" step="1" 
                                value="{{ $setting->tasks_weightage ?? 40 }}" 
                                class="weight-slider">
                            <span id="etrackerValue" class="slider-value">{{ $setting->tasks_weightage ?? 40 }}%</span>
                        </div>
                    </div>

                    <!-- ATTENDANCE -->
                    <div class="slider-row">
                        <div class="slider-label">
                            <label for="attendanceWeight">Attendance System Weightage</label>
                        </div>
                        <div class="slider-control">
                            <input type="range" 
                                id="attendanceWeight" 
                                name="attendance_weightage" 
                                min="0" max="100" step="1" 
                                value="{{ $setting->attendance_weightage ?? 20 }}" 
                                class="weight-slider">
                            <span id="attendanceValue" class="slider-value">{{ $setting->attendance_weightage ?? 20 }}%</span>
                        </div>
                    </div>

                    <!-- LEADER RATING -->
                    <div class="slider-row">
                        <div class="slider-label">
                            <label for="leaderWeight">Leader rating Weightage</label>
                        </div>
                        <div class="slider-control">
                            <input type="range" 
                                id="leaderWeight" 
                                name="leader_weightage" 
                                min="0" max="100" step="1" 
                                value="{{ $setting->leader_weightage ?? 40 }}" 
                                class="weight-slider">
                            <span id="leaderValue" class="slider-value">{{ $setting->leader_weightage ?? 40 }}%</span>
                        </div>
                    </div>

                    <!-- TOTAL WEIGHTAGE INDICATOR -->
                    <div class="total-weight-box">
                        <span>Total Weightage:</span>
                        <strong id="totalWeightVal">100%</strong>
                    </div>

                    <!-- Tukar type="button" kepada type="submit" -->
                    <button type="submit" id="saveFormula" class="save-button">Save Formula Rules</button>
                    <p id="formulaSaved" class="saved-message"></p>
                </div>
            </form>
            </div>

            <!-- EOM BADGE THRESHOLD LOGIC -->
            <div class="configuration-card">
                <div class="configuration-header">
                    <h2>EOM Badge Threshold Logic</h2>
                </div>

                <!-- 1. Bungkus dengan FORM untuk hantar data ke Laravel -->
                <form action="{{ route('hr.updateThresholds') }}" method="POST">
                    @csrf

                    <div class="configuration-body">
                        <div>
                            <!-- GOLD -->
                            <div class="slider-row">
                                <div class="slider-label">
                                    <label for="goldThreshold">Gold badge target score</label>
                                </div>
                                <div class="slider-control">
                                    <input type="range" 
                                        id="goldThreshold" 
                                        name="gold_threshold" 
                                        min="0" max="100" step="1" 
                                        value="{{ $setting->gold_threshold ?? 90 }}" 
                                        class="badge-slider gold-slider">
                                    <span id="goldValue" class="slider-value gold-value">{{ (int)($setting->gold_threshold ?? 90) }}%</span>
                                </div>
                            </div>

                            <!-- SILVER -->
                            <div class="slider-row">
                                <div class="slider-label">
                                    <label for="silverThreshold">Silver badge target score</label>
                                </div>
                                <div class="slider-control">
                                    <input type="range" 
                                        id="silverThreshold" 
                                        name="silver_threshold" 
                                        min="0" max="100" step="1" 
                                        value="{{ $setting->silver_threshold ?? 85 }}" 
                                        class="badge-slider silver-slider">
                                    <span id="silverValue" class="slider-value silver-value">{{ (int)($setting->silver_threshold ?? 85) }}%</span>
                                </div>
                            </div>

                            <!-- BRONZE -->
                            <div class="slider-row">
                                <div class="slider-label">
                                    <label for="bronzeThreshold">Bronze badge target score</label>
                                </div>
                                <div class="slider-control">
                                    <input type="range" 
                                        id="bronzeThreshold" 
                                        name="bronze_threshold" 
                                        min="0" max="100" step="1" 
                                        value="{{ $setting->bronze_threshold ?? 80 }}" 
                                        class="badge-slider bronze-slider">
                                    <span id="bronzeValue" class="slider-value bronze-value">{{ (int)($setting->bronze_threshold ?? 80) }}%</span>
                                </div>
                            </div>
                        </div>

                        <!-- MATCHING HEIGHT SPACER BOX -->
                        <div class="threshold-info-box">
                            <span>Minimum Qualification:</span>
                            <strong style="color: #ea580c;">Bronze ({{ (int)($setting->bronze_threshold ?? 50) }}%)</strong>
                        </div>

                        <div>
                            <!-- 2. Tukar type="button" kepada type="submit" -->
                            <button type="submit" id="saveThreshold" class="save-button">Save Threshold</button>
                            <p id="thresholdSaved" class="saved-message"></p>
                        </div>
                    </div>
                </form>
            </div>
            </div>

        </section>



       <!-- ==================================================
            QUARTERLY DEPARTMENT TARGET ASSIGNMENT
        ================================================== -->
<section class="department-target-section">
    <div class="section-header">
        <div class="section-title">
            Quarterly Department Target Assignment
        </div>
        
        <!-- QUARTER SELECTOR FOR HISTORICAL & FUTURE TARGETS -->
        <select id="quarterSelect" 
                class="quarter-dropdown" 
                onchange="window.location.href='{{ route('hr.kpi') }}?quarter=' + this.value">
            <option value="Q3" {{ request('quarter', 'Q3') == 'Q3' ? 'selected' : '' }}>Quarter 3 (Current)</option>
            <option value="Q4" {{ request('quarter') == 'Q4' ? 'selected' : '' }}>Quarter 4</option>
            <option value="Q1" {{ request('quarter') == 'Q1' ? 'selected' : '' }}>Quarter 1</option>
            <option value="Q2" {{ request('quarter') == 'Q2' ? 'selected' : '' }}>Quarter 2</option>
        </select>
    </div>

    <div class="table-container">
        <form action="{{ route('hr.updateTarget') }}" method="POST">
            @csrf

            <!-- INPUT TERSEMBUNYI: Menghantar nilai quarter semasa ke Controller apabila butang Save ditekan -->
            <input type="hidden" name="quarter" value="{{ request('quarter', 'Q3') }}">

            <table class="department-table">
                <thead>
                    <tr>
                        <th style="width: 25%;">Department</th>
                        <th style="width: 25%;">Target Score (%)</th>
                        <th style="width: 20%;">Assigned By</th>
                        <th style="width: 15%;">Status</th>
                        <th style="width: 15%; text-align: center;">Action</th>
                    </tr>
                </thead>
                <tbody id="departmentTargetBody">
                        @foreach($departments as $dept)
                        <tr>
                        <td><strong>{{ $dept->department_name }}</strong></td>
                        <td>
                            <input type="number" 
                                name="targets[{{ $dept->department_name }}]" 
                                value="{{ $dept->target_score }}" 
                                style="width: 80px;" 
                                class="form-control"> %
                        </td>
                        <td>{{ $dept->assigned_by ?? 'HR Manager' }}</td>
                        <td>
                            <span class="status-badge {{ strtolower($dept->status ?? 'assigned') }}">
                                • {{ $dept->status ?? 'Assigned' }}
                            </span>
                        </td>
                        <td style="text-align: center;">
                            <!-- Hantar nama jabatan secara spesifik -->
                            <button type="submit" name="department_name" value="{{ $dept->department_name }}" class="btn btn-danger btn-sm">Save</button>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </form>
    </div>
</section>


    </main>


<!-- ==================================================
         JAVASCRIPT (CLEANED)
    ================================================== -->

    <script>
        // 1. NOTIFICATION DROPDOWN LOGIC (KEKAL)
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

        function renderNotifications(notifications) {
            const listContainer = document.getElementById('notificationList');
            const badge = document.getElementById('notifBadge');

            if (!listContainer || !badge) return;

            if (!notifications || notifications.length === 0) {
                listContainer.innerHTML = `
                    <div class="no-notifications">
                        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
                            <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
                        </svg>
                        <p>No new notifications</p>
                    </div>
                `;
                badge.style.display = 'none';
                return;
            }

            badge.innerText = notifications.length;
            badge.style.display = 'inline-block';

            listContainer.innerHTML = notifications.map(item => `
                <div class="notification-item ${item.is_read ? '' : 'unread'}">
                    <div class="notif-content">
                        <p class="notif-title">${item.title}</p>
                        <p class="notif-desc">${item.message}</p>
                        <span class="notif-time">${item.created_at}</span>
                    </div>
                </div>
            `).join('');
        }

        // 2. SLIDER & WEIGHTAGE FORMULA LOGIC (KEKAL)
        function updateSliderProgress(sliderInput, valueDisplay) {
            const min = parseFloat(sliderInput.min) || 0;
            const max = parseFloat(sliderInput.max) || 100;
            const val = parseFloat(sliderInput.value) || 0;
            
            const percentage = ((val - min) / (max - min)) * 100;
            sliderInput.style.setProperty('--progress', `${percentage}%`);
            
            if (valueDisplay) {
                valueDisplay.textContent = `${val}%`;
            }
        }

        function initSliders() {
            const sliders = [
                { input: 'etrackerWeight', display: 'etrackerValue' },
                { input: 'attendanceWeight', display: 'attendanceValue' },
                { input: 'leaderWeight', display: 'leaderValue' },
                { input: 'goldThreshold', display: 'goldValue' },
                { input: 'silverThreshold', display: 'silverValue' },
                { input: 'bronzeThreshold', display: 'bronzeValue' }
            ];

            sliders.forEach(item => {
                const inputElem = document.getElementById(item.input);
                const displayElem = document.getElementById(item.display);

                if (inputElem) {
                    updateSliderProgress(inputElem, displayElem);

                    inputElem.addEventListener('input', () => {
                        updateSliderProgress(inputElem, displayElem);
                        checkTotalWeightage();
                    });
                }
            });
        }

        function checkTotalWeightage() {
            const eElem = document.getElementById('etrackerWeight');
            const aElem = document.getElementById('attendanceWeight');
            const lElem = document.getElementById('leaderWeight');

            if (!eElem || !aElem || !lElem) return;

            const e = parseInt(eElem.value) || 0;
            const a = parseInt(aElem.value) || 0;
            const l = parseInt(lElem.value) || 0;

            const total = e + a + l;
            const totalElem = document.getElementById('totalWeightVal');
            const saveBtn = document.getElementById('saveFormula');

            if (totalElem) totalElem.textContent = `${total}%`;

            if (saveBtn) {
                if (total === 100) {
                    if (totalElem) totalElem.className = '';
                    saveBtn.disabled = false;
                } else {
                    if (totalElem) totalElem.className = 'invalid';
                    saveBtn.disabled = true;
                }
            }
        }

        // 3. SAVE FORMULA & THRESHOLD TO LOCALSTORAGE (KEKAL)
        const saveFormulaBtn = document.getElementById('saveFormula');
        if (saveFormulaBtn) {
            saveFormulaBtn.addEventListener('click', async () => {
                const payload = {
                    etracker: parseInt(document.getElementById('etrackerWeight').value),
                    attendance: parseInt(document.getElementById('attendanceWeight').value),
                    leader: parseInt(document.getElementById('leaderWeight').value)
                };
                const msg = document.getElementById('formulaSaved');
                localStorage.setItem('kpiFormula', JSON.stringify(payload));
                if (msg) {
                    msg.textContent = 'Formula rules saved successfully.';
                    msg.className = 'saved-message success';
                    setTimeout(() => { msg.className = 'saved-message'; }, 2500);
                }
            });
        }

        const saveThresholdBtn = document.getElementById('saveThreshold');
        if (saveThresholdBtn) {
            saveThresholdBtn.addEventListener('click', async () => {
                const payload = {
                    gold: parseInt(document.getElementById('goldThreshold').value),
                    silver: parseInt(document.getElementById('silverThreshold').value),
                    bronze: parseInt(document.getElementById('bronzeThreshold').value)
                };
                const msg = document.getElementById('thresholdSaved');
                localStorage.setItem('badgeThreshold', JSON.stringify(payload));
                if (msg) {
                    msg.textContent = 'Badge thresholds saved successfully.';
                    msg.className = 'saved-message success';
                    setTimeout(() => { msg.className = 'saved-message'; }, 2500);
                }
            });
        }

        // INITIALISE ON LOAD
        document.addEventListener('DOMContentLoaded', () => {
            const savedFormula = JSON.parse(localStorage.getItem('kpiFormula'));
            if (savedFormula) {
                if (document.getElementById('etrackerWeight')) document.getElementById('etrackerWeight').value = savedFormula.etracker;
                if (document.getElementById('attendanceWeight')) document.getElementById('attendanceWeight').value = savedFormula.attendance;
                if (document.getElementById('leaderWeight')) document.getElementById('leaderWeight').value = savedFormula.leader;
            }

            const savedThreshold = JSON.parse(localStorage.getItem('badgeThreshold'));
            if (savedThreshold) {
                if (document.getElementById('goldThreshold')) document.getElementById('goldThreshold').value = savedThreshold.gold;
                if (document.getElementById('silverThreshold')) document.getElementById('silverThreshold').value = savedThreshold.silver;
                if (document.getElementById('bronzeThreshold')) document.getElementById('bronzeThreshold').value = savedThreshold.bronze;
            }

            initSliders();
            checkTotalWeightage();
        });
    </script>

</body>

</html>