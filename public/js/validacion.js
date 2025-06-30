document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('registroForm');
    if (!form) return;

    const usernameInput = document.getElementById('username');
    const ageInput = document.getElementById('age');
    const passwordInput = document.getElementById('password');
    const confirmPasswordInput = document.getElementById('confirmPassword');
    const emailInput = document.getElementById('email');
    const errorAlert = document.getElementById('errorAlert');
    const successAlert = document.getElementById('successAlert');

    // Agregar campo oculto para tipo de usuario
    const userTypeInput = document.createElement('input');
    userTypeInput.type = 'hidden';
    userTypeInput.name = 'tipo';
    userTypeInput.value = 'cliente';
    form.appendChild(userTypeInput);

    // Asignar eventos de validación
    if (usernameInput) usernameInput.addEventListener('input', validateUsername);
    if (ageInput) ageInput.addEventListener('input', validateAge);
    if (passwordInput) passwordInput.addEventListener('input', validatePassword);
    if (confirmPasswordInput) confirmPasswordInput.addEventListener('input', validatePasswordConfirmation);
    if (emailInput) emailInput.addEventListener('input', validateEmail);

    form.addEventListener('submit', function (e) {
        e.preventDefault();
        
        // Validar todos los campos
        const isUsernameValid = validateUsername();
        const isAgeValid = validateAge();
        const isPasswordValid = validatePassword();
        const isPasswordConfirmationValid = validatePasswordConfirmation();
        const isEmailValid = validateEmail();

        if (isUsernameValid && isAgeValid && isPasswordValid && 
            isPasswordConfirmationValid && isEmailValid) {
            submitForm();
        } else {
            showAlert(errorAlert, 'Por favor corrige los errores en el formulario', 'danger');
        }
    });

    function validateUsername() {
        if (!usernameInput) return true;
        const feedback = getFeedbackElement('usernameFeedback', usernameInput.parentNode.parentNode);
        
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
        const feedback = getFeedbackElement('ageFeedback', ageInput.parentNode.parentNode);
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
        const feedback = getFeedbackElement('passwordFeedback', passwordInput.parentNode.parentNode);
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
        const feedback = getFeedbackElement('confirmPasswordFeedback', confirmPasswordInput.parentNode.parentNode);
        
        if (confirmPasswordInput.value !== passwordInput.value) {
            showError(feedback, 'Las contraseñas no coinciden');
            return false;
        }
        clearFeedback(feedback);
        return true;
    }

    function validateEmail() {
        if (!emailInput) return true;
        const feedback = getFeedbackElement('emailFeedback', emailInput.parentNode.parentNode);
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        
        if (!emailRegex.test(emailInput.value)) {
            showError(feedback, 'Ingresa un email válido');
            return false;
        }
        clearFeedback(feedback);
        return true;
    }

    function getFeedbackElement(id, parent) {
        let feedback = document.getElementById(id);
        
        if (!feedback) {
            feedback = document.createElement('div');
            feedback.id = id;
            feedback.className = 'validation-feedback';
            
            // Insertar después del grupo de input
            const inputGroup = parent.querySelector('.input-group');
            if (inputGroup) {
                inputGroup.parentNode.insertBefore(feedback, inputGroup.nextSibling);
            } else {
                parent.appendChild(feedback);
            }
        }
        
        return feedback;
}

    function showError(element, message) {
        element.textContent = message;
        element.classList.add('text-danger');
        element.classList.remove('text-success');
    }

    function showSuccess(element, message) {
        element.textContent = message;
        element.classList.add('text-success');
        element.classList.remove('text-danger');
    }

    function clearFeedback(element) {
        element.textContent = '';
        element.classList.remove('text-danger', 'text-success');
    }

    function showAlert(alertElement, message, type) {
        alertElement.textContent = message;
        alertElement.classList.remove('d-none', 'alert-success', 'alert-danger');
        alertElement.classList.add(`alert-${type}`);
        
        setTimeout(() => {
            alertElement.classList.add('d-none');
        }, 5000);
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
                showAlert(successAlert, data.success, 'success');
                setTimeout(() => {
                    window.location.href = '/GestiFit/public/public/login.html';
                }, 2000);
            } else if (data.error) {
                showAlert(errorAlert, data.error, 'danger');
            }
        })
        .catch(error => {
            showAlert(errorAlert, 'Error de conexión: ' + error.message, 'danger');
        })
        .finally(() => {
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="fas fa-user-plus me-2"></i>Registrarse';
        });
    }
});