<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="icon" type="image/png" href="{{ asset('storage/image.png') }}">
    <title>@yield("title") &mdash; {{ config('app.name') }}</title>
    <link
    href="https://cdn.jsdelivr.net/npm/remixicon@4.7.0/fonts/remixicon.css"
    rel="stylesheet"
    />

    @vite('resources/css/app.css')
</head>
<body>
    <div id="website-container">
        @yield("content")
    </div>
    <script>
        const passwordToggles = document.querySelectorAll('.password-toggle');
        passwordToggles.forEach(toggle => {
            toggle.addEventListener('click', () => {
                const passwordInput = toggle.previousElementSibling;
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);
                toggle.classList.toggle('ri-eye-line');
                toggle.classList.toggle('ri-eye-close-line');
            });
        });

        const submitButton = document.querySelector('.submit-button-auth');
        const emailInput = document.querySelector('#email');
        const passwordInput = document.querySelector('#password');
        const passwordConfirmationInput = document.querySelector('#password_confirmation');
        const usernameInput = document.querySelector('#username');
        const roleInput = document.querySelector('#role');

        // Password requirements elements
        const reqLength = document.querySelector('.req-length');
        const reqUppercase = document.querySelector('.req-uppercase');
        const reqLowercase = document.querySelector('.req-lowercase');
        const reqNumber = document.querySelector('.req-number');
        const reqMatch = document.querySelector('.req-match');

        function updateRequirement(element, isValid) {
            const icon = element.querySelector('i');
            if (isValid) {
                element.classList.remove('text-gray-500');
                element.classList.add('text-green-600');
                icon.classList.remove('ri-subtract-line');
                icon.classList.add('ri-checkbox-circle-line');
            } else {
                element.classList.remove('text-green-600');
                element.classList.add('text-gray-500');
                icon.classList.remove('ri-checkbox-circle-line');
                icon.classList.add('ri-subtract-line');
            }
        }

        function validateEmail(email) {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return emailRegex.test(email);
        }

        function validatePassword() {
            if (!passwordInput) return false;
            
            const password = passwordInput.value;
            const passwordConfirmation = passwordConfirmationInput ? passwordConfirmationInput.value : '';

            // Validate each requirement
            const hasLength = password.length > 8;
            const hasUppercase = /[A-Z]/.test(password);
            const hasLowercase = /[a-z]/.test(password);
            const hasNumber = /[0-9]/.test(password);
            const passwordsMatch = password !== '' && password === passwordConfirmation;

            // Update UI for each requirement
            if (reqLength) updateRequirement(reqLength, hasLength);
            if (reqUppercase) updateRequirement(reqUppercase, hasUppercase);
            if (reqLowercase) updateRequirement(reqLowercase, hasLowercase);
            if (reqNumber) updateRequirement(reqNumber, hasNumber);
            if (reqMatch) updateRequirement(reqMatch, passwordsMatch);

            // Return whether all requirements are met
            return hasLength && hasUppercase && hasLowercase && hasNumber && passwordsMatch;
        }

        function toggleSubmitButton() {
            if (!submitButton) return; // safety guard
            let isValid = false;

            const emailValue = emailInput ? emailInput.value.trim() : '';
            const emailValid = emailInput ? validateEmail(emailValue) : true;

            // Register page (all registration fields present)
            if (usernameInput && passwordConfirmationInput && roleInput && passwordInput) {
                const allFieldsFilled = emailValue !== '' &&
                                       usernameInput.value.trim() !== '' &&
                                       passwordInput.value.trim() !== '' &&
                                       passwordConfirmationInput.value.trim() !== '';
                const roleValid = roleInput.value === 'student' || roleInput.value === 'investor';
                const passwordValid = validatePassword();
                isValid = allFieldsFilled && emailValid && roleValid && passwordValid;
            }
            // Recovery page (has password, password_confirmation, email but no username/role)
            else if (passwordInput && passwordConfirmationInput && !usernameInput && !roleInput && emailInput) {
                const allFieldsFilled = emailValue !== '' &&
                                       passwordInput.value.trim() !== '' &&
                                       passwordConfirmationInput.value.trim() !== '';
                const passwordValid = validatePassword();
                isValid = allFieldsFilled && emailValid && passwordValid;
            }
            // Login page (has password input but no confirmation or registration extras)
            else if (passwordInput && !passwordConfirmationInput) {
                isValid = emailValue !== '' && passwordInput.value.trim() !== '' && emailValid;
            }
            // Forgot password page (only email field present)
            else if (emailInput && !passwordInput) {
                isValid = emailValue !== '' && emailValid;
            }

            submitButton.disabled = !isValid;

            if (isValid) {
                submitButton.classList.remove('text-gray-800', 'bg-gray-300', 'cursor-not-allowed');
                submitButton.classList.add('bg-[#b01116]', 'text-white', 'cursor-pointer');
            } else {
                submitButton.classList.remove('bg-[#b01116]', 'text-white', 'cursor-pointer');
                submitButton.classList.add('text-gray-800', 'bg-gray-300', 'cursor-not-allowed');
            }
        }

        emailInput && emailInput.addEventListener('input', toggleSubmitButton);
        if (passwordInput) {
            passwordInput.addEventListener('input', toggleSubmitButton);
        }
        
        if (passwordConfirmationInput) {
            passwordConfirmationInput.addEventListener('input', toggleSubmitButton);
        }
        
        if (usernameInput) {
            usernameInput.addEventListener('input', toggleSubmitButton);
        }

        if (roleInput) {
            roleInput.addEventListener('change', toggleSubmitButton);
        }

        // Initialize button state on load
        toggleSubmitButton();
    </script>
</body>
</html>