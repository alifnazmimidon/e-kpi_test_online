<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - E-KPI System</title>
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
                    RESET PASSWORD
                </h1>

                <p class="forgot-subtitle reveal" style="--i: 2">
                    SISTEM E - KPI PEKERJA
                </p>

                <p class="forgot-description reveal" style="--i: 2">
                    Create a new password for <strong>{{ $user->full_name }}</strong> ({{ $user->worker_id }}).
                </p>

                <!-- SERVER ERROR MESSAGE -->
                @if ($errors->any())
                    <div class="forgot-alert error reveal" style="--i: 2" role="alert">
                        {{ $errors->first() }}
                    </div>
                @endif

                <!-- CLIENT VALIDATION MESSAGE -->
                <div id="resetMessage" class="forgot-alert error" role="alert" hidden></div>

                <!-- RESET FORM -->
                <form id="resetForm" method="POST" action="{{ route('password.update') }}" class="reveal" style="--i: 3">
                    @csrf

                    <!-- NEW PASSWORD -->
                    <div class="forgot-form-group">
                        <label for="newPassword">
                            New Password :
                        </label>

                        <div class="input-wrapper">
                            <input
                                type="password"
                                id="newPassword"
                                name="new_password"
                                placeholder="Enter your new password"
                                autocomplete="new-password"
                                required
                            >
                            <button
                                class="eye-button"
                                type="button"
                                onclick="togglePasswordField('newPassword', this)"
                                aria-label="Show new password"
                                aria-pressed="false"
                                title="Show password"
                            >
                                <!-- ICON: SHOW PASSWORD -->
                                <svg class="eye-svg eye-open" width="20" height="20" viewBox="0 0 24 24" fill="none"
                                     stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                    <path d="M2.062 12.348a1 1 0 0 1 0-.696 10.75 10.75 0 0 1 19.876 0 1 1 0 0 1 0 .696 10.75 10.75 0 0 1-19.876 0"/>
                                    <circle cx="12" cy="12" r="3"/>
                                </svg>
                                <!-- ICON: HIDE PASSWORD -->
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

                    <!-- CONFIRM PASSWORD -->
                    <div class="forgot-form-group">
                        <label for="confirmPassword">
                            Confirm Password :
                        </label>

                        <div class="input-wrapper">
                            <input
                                type="password"
                                id="confirmPassword"
                                name="confirm_password"
                                placeholder="Confirm your new password"
                                autocomplete="new-password"
                                required
                            >
                            <button
                                class="eye-button"
                                type="button"
                                onclick="togglePasswordField('confirmPassword', this)"
                                aria-label="Show confirm password"
                                aria-pressed="false"
                                title="Show password"
                            >
                                <!-- ICON: SHOW PASSWORD -->
                                <svg class="eye-svg eye-open" width="20" height="20" viewBox="0 0 24 24" fill="none"
                                     stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                    <path d="M2.062 12.348a1 1 0 0 1 0-.696 10.75 10.75 0 0 1 19.876 0 1 1 0 0 1 0 .696 10.75 10.75 0 0 1-19.876 0"/>
                                    <circle cx="12" cy="12" r="3"/>
                                </svg>
                                <!-- ICON: HIDE PASSWORD -->
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

                    <!-- PASSWORD REQUIREMENT -->
                    <p class="password-hint" id="passwordHint">
                        Use at least 8 characters.
                    </p>

                    <!-- BUTTON -->
                    <button type="submit" class="forgot-button" id="resetSubmitBtn">
                        <span class="btn-text">RESET PASSWORD</span>
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
        function togglePasswordField(inputId, btn) {
            const input = document.getElementById(inputId);
            const isHidden = input.type === "password";

            input.type = isHidden ? "text" : "password";
            btn.classList.toggle("is-visible", isHidden);
            btn.setAttribute("aria-pressed", String(isHidden));
            btn.setAttribute("aria-label", isHidden ? "Hide password" : "Show password");
            btn.setAttribute("title", isHidden ? "Hide password" : "Show password");
        }

        const resetForm = document.getElementById("resetForm");
        const newPasswordInput = document.getElementById("newPassword");
        const passwordHint = document.getElementById("passwordHint");
        const resetMessage = document.getElementById("resetMessage");
        const resetSubmitBtn = document.getElementById("resetSubmitBtn");

        // Penunjuk visual apabila panjang kata laluan >= 8 aksara
        newPasswordInput.addEventListener("input", function () {
            passwordHint.classList.toggle("is-valid", this.value.length >= 8);
        });

        function showValidationError(msg) {
            resetMessage.textContent = msg;
            resetMessage.hidden = false;
            // Ulang animasi shake jika ralat berlaku lagi
            resetMessage.style.animation = "none";
            void resetMessage.offsetWidth;
            resetMessage.style.animation = "";
        }

        resetForm.addEventListener("submit", function (event) {
            const newPassword = newPasswordInput.value;
            const confirmPassword = document.getElementById("confirmPassword").value;

            // Check password length
            if (newPassword.length < 8) {
                event.preventDefault();
                showValidationError("Password must contain at least 8 characters.");
                newPasswordInput.focus();
                return;
            }

            // Check password confirmation
            if (newPassword !== confirmPassword) {
                event.preventDefault();
                showValidationError("Passwords do not match.");
                document.getElementById("confirmPassword").focus();
                return;
            }

            resetMessage.hidden = true;
            resetSubmitBtn.classList.add("is-loading");
            resetSubmitBtn.disabled = true;
        });
    </script>

</body>
</html>