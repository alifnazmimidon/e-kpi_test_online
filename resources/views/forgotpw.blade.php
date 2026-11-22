<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - E-KPI System</title>
    <link rel="stylesheet" href="{{ asset('stylereset.css') }}?v=2">
</head>
<body>

    <main class="forgot-page">

        <!-- LEFT SIDE -->
        <div class="forgot-left">
            <div class="forgot-box">

                <!-- LOGO -->
                <div class="forgot-logo reveal" style="--i: 0">
                    <img src="{{ asset('assets/ILMgroups-logo-4.png') }}" alt="Infinite Loop Media Logo">
                </div>

                <!-- TITLE -->
                <h1 class="forgot-title reveal" style="--i: 1">
                    FORGOT PASSWORD
                </h1>

                <p class="forgot-subtitle reveal" style="--i: 2">
                    SISTEM E - KPI PEKERJA
                </p>

                <p class="forgot-description reveal" style="--i: 2">
                    Enter your Worker ID to verify your account and reset your password.
                </p>

                <!-- ERROR MESSAGE -->
                @if (session('error'))
                    <div class="forgot-alert error reveal" style="--i: 2" role="alert">
                        {{ session('error') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="forgot-alert error reveal" style="--i: 2" role="alert">
                        {{ $errors->first() }}
                    </div>
                @endif

                <!-- VERIFY WORKER ID FORM -->
                <form method="POST" action="{{ route('password.verify') }}" id="forgotForm" class="reveal" style="--i: 3">
                    @csrf

                    <!-- WORKER ID -->
                    <div class="forgot-form-group">
                        <label for="workerId">
                            Worker ID :
                        </label>

                        <div class="input-wrapper">
                            <input
                                type="text"
                                id="workerId"
                                name="worker_id"
                                value="{{ old('worker_id') }}"
                                placeholder="e.g. ILM1001"
                                autocomplete="username"
                                required
                                autofocus
                            >
                        </div>
                    </div>

                    <!-- BUTTON -->
                    <button type="submit" class="forgot-button" id="forgotSubmitBtn">
                        <span class="btn-text">VERIFY ACCOUNT</span>
                        <span class="btn-loader" aria-hidden="true"></span>
                    </button>
                </form>

                <!-- BACK TO LOGIN -->
                <div class="back-login-wrap reveal" style="--i: 4">
                    <a href="{{ route('login') }}" class="back-login">
                        <span class="back-arrow" aria-hidden="true">←</span> Back to Login
                    </a>
                </div>

            </div>
        </div>

        <!-- RIGHT SIDE -->
        <div class="forgot-right">
            <img
                src="{{ asset('assets/81d6c4a1c69d5fe0e4ff15b812788947.jpg') }}"
                alt="Infinite Loop Media"
                onerror="this.style.display='none';"
            >
            <div class="image-overlay"></div>
        </div>

    </main>

    <script>
        document.getElementById("forgotForm").addEventListener("submit", function () {
            const btn = document.getElementById("forgotSubmitBtn");
            btn.classList.add("is-loading");
            btn.disabled = true;
        });
    </script>

</body>
</html>