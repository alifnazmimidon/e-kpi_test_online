<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - E-KPI System</title>
    <link rel="stylesheet" href="{{ asset('stylereset.css') }}">
</head>
<body>

    <div class="forgot-page">

        <!-- LEFT SIDE -->
        <div class="forgot-left">

            <!-- LOGO -->
            <div class="forgot-logo">
                <img src="{{ asset('assets/ILMgroups-logo-4.png') }}" alt="Infinite Loop Media Logo">
            </div>

            <!-- TITLE -->
            <h1 class="forgot-title">
                FORGOT PASSWORD
            </h1>

            <p class="forgot-subtitle">
                SISTEM E - KPI PEKERJA
            </p>

            <p class="forgot-description">
                Enter your Worker ID to verify your account and reset your password.
            </p>

            <!-- VERIFY WORKER ID FORM -->
            <form method="POST" action="{{ route('password.verify') }}">
                @csrf

                <!-- WORKER ID -->
                <div class="forgot-form-group">
                    <label for="workerId">
                        Worker ID
                    </label>

                    <input
                        type="text"
                        id="workerId"
                        name="worker_id"
                        value="{{ old('worker_id') }}"
                        placeholder="e.g. ILM1001"
                        required
                        autofocus
                    >
                </div>

                <!-- BUTTON -->
                <button type="submit" class="forgot-button">
                    VERIFY ACCOUNT
                </button>
            </form>

            <!-- ERROR MESSAGE -->
            @if (session('error'))
                <p class="forgot-message" style="color: #E31B23;">
                    {{ session('error') }}
                </p>
            @endif

            @if ($errors->any())
                <p class="forgot-message" style="color: #E31B23;">
                    {{ $errors->first() }}
                </p>
            @endif

            <!-- BACK TO LOGIN -->
            <a href="{{ route('login') }}" class="back-login">
                ← Back to Login
            </a>

        </div>

        <!-- RIGHT SIDE -->
        <div class="forgot-right">
            <img
                src="{{ asset('assets/81d6c4a1c69d5fe0e4ff15b812788947.jpg') }}"
                alt="Infinite Loop Media"
            >
        </div>

    </div>

</body>
</html>