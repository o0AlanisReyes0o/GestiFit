document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('registroForm');
    if (!form) return;

    const usernameInput = document.getElementById('username');
    const ageInput = document.getElementById('age');
    const passwordInput = document.getElementById('password');
    const confirmPasswordInput = document.getElementById('confirmPassword');
    const emailInput = document.getElementById('email');

    // CORREGIDO: se escribió mal el nombre de la variable
    const userTypeInput = document.createElement('input');
    userTypeInput.type = 'hidden';
    userTypeInput.name = 'tipo';
    userTypeInput.value = 'cliente';
    form.appendChild(userTypeInput);

    if (usernameInput) usernameInput.addEventListener('input', validateUsername);
    if (ageInput) ageInput.addEventListener('input', validateAge);
    if (passwordInput) passwordInput.addEventListener('input', validatePassword);
    if (confirmPasswordInput) confirmPasswordInput.addEventListener('input', validatePasswordConfirmation);
    if (emailInput) emailInput.addEventListener('input', validateEmail);

    form.addEventListener('submit', function (e) {
        e.preventDefault();
        const isValid = validateUsername() & validateAge() &
            validatePassword() & validatePasswordConfirmation() &
            validateEmail();

        if (isValid) {
            submitForm();
        }
    });

    function validateUsername() {
        if (!usernameInput) return true;
        const feedback = getFeedbackElement('usernameFeedback', usernameInput.parentNode);
        if (usernameInput.value.length < 4) {
            showError(feedback, 'El usuario debe tener al menos 4 caracteres');
            return false;
        }
        if (usernameInput.value.toLowerCase().startsWith('admin')) {
            showError(feedback, 'Los clientes no pueden usar "admin" en su usuario');
            return false;
        }
        showSuccess(feedback, 'Nombre de usuario válido');
        return true;
    }

    function validateAge() {
        if (!ageInput) return true;
        const feedback = getFeedbackElement('ageFeedback', ageInput.parentNode);
        const age = parseInt(ageInput.value);
        if (ageInput.value && isNaN(age)) {
            showError(feedback, 'La edad debe ser un número');
            return false;
        }
        if (age < 15 || age > 100) {
            showError(feedback, 'La edad debe estar entre 15 y 100 años');
            return false;
        }
        clearFeedback(feedback);
        return true;
    }

    function validatePassword() {
        if (!passwordInput) return true;
        const feedback = getFeedbackElement('passwordFeedback', passwordInput.parentNode);
        const pwd = passwordInput.value;
        if (pwd.length < 8) {
            showError(feedback, 'La contraseña debe tener al menos 8 caracteres');
            return false;
        }
        if (!/\d/.test(pwd) || !/[A-Z]/.test(pwd) || !/[a-z]/.test(pwd)) {
            showError(feedback, 'Debe contener mayúsculas, minúsculas y números');
            return false;
        }
        clearFeedback(feedback);
        return true;
    }

    function validatePasswordConfirmation() {
        if (!confirmPasswordInput || !passwordInput) return true;
        const feedback = getFeedbackElement('confirmPasswordFeedback', confirmPasswordInput.parentNode);
        if (confirmPasswordInput.value !== passwordInput.value) {
            showError(feedback, 'Las contraseñas no coinciden');
            return false;
        }
        clearFeedback(feedback);
        return true;
    }

    function validateEmail() {
        if (!emailInput) return true;
        const feedback = getFeedbackElement('emailFeedback', emailInput.parentNode);
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(emailInput.value)) {
            showError(feedback, 'Ingresa un email válido');
            return false;
        }
        clearFeedback(feedback);
        return true;
    }

    function getFeedbackElement(id, parent) {
        let feedback = parent.querySelector('#' + id);
        if (!feedback) {
            feedback = document.createElement('small');
            feedback.id = id;
            feedback.className = 'form-text d-block mt-1';
            parent.appendChild(feedback);
        }
        return feedback;
    }

    function showError(element, message) {
        element.textContent = message;
        element.className = 'form-text text-danger d-block mt-1';
    }

    function showSuccess(element, message) {
        element.textContent = message;
        element.className = 'form-text text-success d-block mt-1';
    }

    function clearFeedback(element) {
        element.textContent = '';
        element.className = 'form-text d-block mt-1';
    }

    function submitForm() {
        const formData = new FormData(form);
        const submitBtn = form.querySelector('button[type="submit"]');

        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Registrando...';

        fetch(form.action, {
            method: 'POST',
            body: formData
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(data.success);
                    form.reset();
                } else if (data.error) {
                    alert("Error: " + data.error);
                }
            })
            .catch(error => {
                alert('Error de conexión: ' + error.message);
            })
            .finally(() => {
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="fas fa-user-plus me-2"></i>Registrarse';
            });
    }
});
