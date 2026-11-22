<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EKPI - Login</title>
    <link rel="stylesheet" href="{{ asset('style.css') }}?v=3">
</head>
<body>
    <main class="login-page" id="loginPage">

        <!-- LEFT: LOGIN FORM -->
        <div class="login-left">
            <div class="login-box">
                <div class="login-logo reveal" style="--i: 0">
                    <img
                        src="{{ asset('assets/ILMgroups-logo-4.png') }}"
                        alt="Infinite Loop Media Logo"
                    >
                </div>

                <h1 class="welcome-title reveal" style="--i: 1">WELCOME BACK</h1>
                <p class="system-name reveal" style="--i: 2">E-KPI SYSTEM</p>

                <!-- PAPARKAN MESEJ RALAT JIKA LOGIN GAGAL -->
                @if (session('error'))
                    <div class="login-alert error reveal" style="--i: 2" role="alert">
                        {{ session('error') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="login-alert error reveal" style="--i: 2" role="alert">
                        {{ $errors->first() }}
                    </div>
                @endif

                <!-- PAPARKAN MESEJ BERJAYA SELEPAS RESET PASSWORD -->
                @if (session('success'))
                    <div class="login-alert success reveal" style="--i: 2" role="status">
                        {{ session('success') }}
                    </div>
                @endif

                <!-- FORM LOGIN LARAVEL -->
                <form action="{{ route('login.post') }}" method="POST" id="loginForm" class="reveal" style="--i: 3">
                    @csrf

                    <!-- WORKER ID -->
                    <div class="input-group">
                        <label for="workerID">Worker ID :</label>
                        <div class="input-wrapper">
                            <input
                                type="text"
                                id="workerID"
                                name="worker_id"
                                value="{{ old('worker_id') }}"
                                placeholder="e.g., ILM3003"
                                autocomplete="username"
                                required
                            >
                        </div>
                    </div>

                    <!-- PASSWORD -->
                    <div class="input-group">
                        <label for="password">Password :</label>
                        <div class="input-wrapper">
                            <input
                                type="password"
                                id="password"
                                name="password"
                                placeholder="Password"
                                autocomplete="current-password"
                                required
                            >
                            <button
                                class="eye-button"
                                type="button"
                                onclick="togglePassword()"
                                id="eyeButton"
                                aria-label="Show password"
                                aria-pressed="false"
                                title="Show password"
                            >
                                <!-- ICON: SHOW PASSWORD (MATA TERBUKA) -->
                                <svg class="eye-svg eye-open" width="20" height="20" viewBox="0 0 24 24" fill="none"
                                     stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                    <path d="M2.062 12.348a1 1 0 0 1 0-.696 10.75 10.75 0 0 1 19.876 0 1 1 0 0 1 0 .696 10.75 10.75 0 0 1-19.876 0"/>
                                    <circle cx="12" cy="12" r="3"/>
                                </svg>

                                <!-- ICON: HIDE PASSWORD (MATA BERPALANG) -->
                                <svg class="eye-svg eye-closed" width="20" height="20" viewBox="0 0 24 24" fill="none"
                                     stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                    <path d="M10.733 5.076a10.744 10.744 0 0 1 11.205 6.575 1 1 0 0 1 0 .696 10.747 10.747 0 0 1-1.444 2.49"/>
                                    <path d="M14.084 14.158a3 3 0 0 1-4.242-4.242"/>
                                    <path d="M17.479 17.499a10.75 10.75 0 0 1-15.417-5.151 1 1 0 0 1 0-.696 10.75 10.75 0 0 1 4.446-5.143"/>
                                    <path class="eye-slash" d="m2 2 20 20"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- SUBMIT BUTTON -->
                    <button type="submit" class="login-button" id="loginSubmitBtn">
                        <span class="btn-text">LOGIN</span>
                        <span class="btn-loader" aria-hidden="true"></span>
                    </button>
                </form>

                <p class="forgot-password reveal" style="--i: 4">
                    <a href="{{ route('password.request') }}" class="forgot-link">FORGOT PASSWORD ?</a>
                </p>
            </div>
        </div>

        <!-- RIGHT IMAGE -->
        <div class="login-right">
            <img
                src="{{ asset('assets/81d6c4a1c69d5fe0e4ff15b812788947.jpg') }}"
                alt="Infinite Loop Media"
                onerror="this.style.display='none';"
            >
            <div class="image-overlay"></div>
        </div>

    </main>

    <script>
        function togglePassword() {
            const passwordInput = document.getElementById("password");
            const eyeButton = document.getElementById("eyeButton");
            const isHidden = passwordInput.type === "password";

            passwordInput.type = isHidden ? "text" : "password";
            eyeButton.classList.toggle("is-visible", isHidden);
            eyeButton.setAttribute("aria-pressed", String(isHidden));
            eyeButton.setAttribute("aria-label", isHidden ? "Hide password" : "Show password");
            eyeButton.setAttribute("title", isHidden ? "Hide password" : "Show password");
        }

        // Kesan loading pada butang semasa borang dihantar
        document.getElementById("loginForm").addEventListener("submit", function () {
            const btn = document.getElementById("loginSubmitBtn");
            btn.classList.add("is-loading");
            btn.disabled = true;
        });
    </script>
</body>
</html>