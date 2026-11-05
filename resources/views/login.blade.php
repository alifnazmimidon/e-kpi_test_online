<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EKPI - Login</title>
    <link rel="stylesheet" href="{{ asset('style.css') }}">
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
                @if(session('error'))
                    <div style="color: red; margin-bottom: 15px; font-weight: bold; text-align: center;">
                        {{ session('error') }}
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
                                placeholder="Contoh: ILM3003"
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
                            >
                                ◉
                            </button>
                        </div>
                    </div>

                    <!-- SUBMIT BUTTON -->
                    <button type="submit" class="login-button">
                        LOGIN
                    </button>
                </form>

                <p class="forgot-password">FORGOT PASSWORD ?</p>
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
        // FUNGSI TOGGLE PASSWORD VISIBILITY
        function togglePassword() {
            const password = document.getElementById("password");
            const eyeButton = document.getElementById("eyeButton");

            if (password.type === "password") {
                password.type = "text";
            } else {
                password.type = "password";
            }
        }
    </script>
</body>
</html>