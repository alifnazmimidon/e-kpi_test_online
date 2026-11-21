<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EKPI - Login</title>
    <link rel="stylesheet" href="{{ asset('style.css') }}?v=2">
</head>
<body>
    <section class="login-page" id="loginPage">

        <!-- LEFT -->
        <div class="login-left">
            <div class="login-box">
                <div class="login-logo">
                    <img
                        src="{{ asset('assets/ILMgroups-logo-4.png') }}"
                        alt="Infinite Loop Media Logo"
                    >
                </div>

                <h1 class="welcome-title">WELCOME BACK</h1>
                <p class="system-name">E-KPI SYSTEM</p>
               <!-- PAPARKAN MESEJ RALAT JIKA LOGIN GAGAL -->
                @if (session('error'))
                    <p style="color: #E31B23 !important; font-weight: 600; margin-bottom: 12px; text-align: center;">
                        {{ session('error') }}
                    </p>
                @endif

                @if ($errors->any())
                    <p style="color: #E31B23 !important; font-weight: 600; margin-bottom: 12px; text-align: center;">
                        {{ $errors->first() }}
                    </p>
                @endif

                <!-- PAPARKAN MESEJ BERJAYA SELEPAS RESET PASSWORD -->
                @if(session('success'))
                    <div style="color: #16a34a; margin-bottom: 15px; font-weight: bold; text-align: center;">
                        {{ session('success') }}
                    </div>
                @endif

                <!-- FORM LOGIN LARAVEL -->
                <form action="{{ route('login.post') }}" method="POST">
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
                                placeholder="••••••••"
                                required
                            >
                            <button
                                class="eye-button"
                                type="button"
                                onclick="togglePassword()"
                                id="eyeButton"
                                aria-label="Toggle password visibility"
                            >
                                <!-- SVG BULAT BERISI (DEFAULT) -->
                                <svg id="eyeIcon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#18181B" stroke-width="2">
                                    <circle cx="12" cy="12" r="9"/>
                                    <circle cx="12" cy="12" r="4" fill="#18181B"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- SUBMIT BUTTON -->
                    <button type="submit" class="login-button">
                        LOGIN
                    </button>
                </form>

                <p class="forgot-password">
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

    </section>

    <script>
        function togglePassword() {
            const password = document.getElementById("password");
            const eyeIcon = document.getElementById("eyeIcon");

            if (password.type === "password") {
                password.type = "text";
                eyeIcon.innerHTML = `<circle cx="12" cy="12" r="9"/>`;
            } else {
                password.type = "password";
                eyeIcon.innerHTML = `<circle cx="12" cy="12" r="9"/><circle cx="12" cy="12" r="4" fill="#18181B"/>`;
            }
        }
    </script>
</body>
</html>
