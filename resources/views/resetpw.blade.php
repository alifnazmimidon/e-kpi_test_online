<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - E-KPI System</title>
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
                RESET PASSWORD
            </h1>

            <p class="forgot-subtitle">
                SISTEM E - KPI PEKERJA
            </p>

            <p class="forgot-description">
                Create a new password for <strong>{{ $user->full_name }}</strong> ({{ $user->worker_id }}).
            </p>

            <!-- RESET FORM -->
            <form id="resetForm" method="POST" action="{{ route('password.update') }}">
                @csrf

                <!-- NEW PASSWORD -->
                <div class="forgot-form-group">
                    <label for="newPassword">
                        New Password
                    </label>

                    <input
                        type="password"
                        id="newPassword"
                        name="new_password"
                        placeholder="Enter your new password"
                        required
                    >
                </div>

                <!-- CONFIRM PASSWORD -->
                <div class="forgot-form-group">
                    <label for="confirmPassword">
                        Confirm Password
                    </label>

                    <input
                        type="password"
                        id="confirmPassword"
                        name="confirm_password"
                        placeholder="Confirm your new password"
                        required
                    >
                </div>

                <!-- PASSWORD REQUIREMENT -->
                <p class="password-hint">
                    Use at least 8 characters.
                </p>

                <!-- BUTTON -->
                <button type="submit" class="forgot-button">
                    RESET PASSWORD
                </button>

            </form>

            <!-- MESSAGE -->
            <p id="resetMessage" class="forgot-message" style="color: #E31B23;">
                @if ($errors->any())
                    {{ $errors->first() }}
                @endif
            </p>

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

    <script>
        const resetForm = document.getElementById("resetForm");
        const resetMessage = document.getElementById("resetMessage");

        resetForm.addEventListener("submit", function(event) {
            const newPassword = document.getElementById("newPassword").value;
            const confirmPassword = document.getElementById("confirmPassword").value;

            // Check password length
            if (newPassword.length < 8) {
                event.preventDefault();
                resetMessage.textContent = "Password must contain at least 8 characters.";
                resetMessage.style.color = "#E31B23";
                return;
            }

            // Check password confirmation
            if (newPassword !== confirmPassword) {
                event.preventDefault();
                resetMessage.textContent = "Passwords do not match.";
                resetMessage.style.color = "#E31B23";
                return;
            }
        });
    </script>

</body>
</html>