document.addEventListener('DOMContentLoaded', function() {
    const loginForm = document.getElementById('loginForm');
    const emailInput = document.getElementById('email');
    const passwordInput = document.getElementById('password');
    const emailError = document.getElementById('emailError');
    const passwordError = document.getElementById('passwordError');
    const togglePassword = document.getElementById('togglePassword');

    if (togglePassword) {
        togglePassword.addEventListener('click', function() {
            const icon = this.querySelector('i');
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                icon.classList.replace('fa-eye', 'fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                icon.classList.replace('fa-eye-slash', 'fa-eye');
            }
        });
    }

    if (loginForm) {
        loginForm.addEventListener('submit', function(e) {
            let isValid = true;

            // Simple validación de email
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(emailInput.value)) {
                emailError.classList.remove('hidden');
                emailInput.classList.add('border-red-500');
                isValid = false;
            } else {
                emailError.classList.add('hidden');
                emailInput.classList.remove('border-red-500');
            }

            // Simple validación de contraseña
            if (passwordInput.value.length < 6) {
                passwordError.classList.remove('hidden');
                passwordInput.classList.add('border-red-500');
                isValid = false;
            } else {
                passwordError.classList.add('hidden');
                passwordInput.classList.remove('border-red-500');
            }

            if (!isValid) {
                e.preventDefault(); // Detener envío del formulario
                loginForm.classList.add('shake');
                setTimeout(() => {
                    loginForm.classList.remove('shake');
                }, 500);
            } else {
                // Si es válido, el formulario se enviará normalmente al backend
                const submitBtn = document.getElementById('submitBtn');
                const btnText = document.getElementById('btnText');
                const btnSpinner = document.getElementById('btnSpinner');

                if (submitBtn) {
                    btnText.textContent = 'Ingresando...';
                    btnSpinner.classList.remove('hidden');
                    submitBtn.disabled = true;
                }
            }
        });
    }

    // Ocultar alertas después de unos segundos
    setTimeout(() => {
        const errorAlert = document.getElementById('error-alert');
        const successAlert = document.getElementById('success-alert');
        if (errorAlert) {
            errorAlert.style.display = 'none';
        }
        if (successAlert) {
            successAlert.style.display = 'none';
        }
    }, 5000); // 5 segundos
}); 