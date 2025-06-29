document.addEventListener('DOMContentLoaded', function () {
    const loginForm = document.getElementById('loginForm');
    
    loginForm.addEventListener('submit', function (e) {
        e.preventDefault();

        const submitBtn = this.querySelector('button[type="submit"]');
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Ingresando...';

        const formData = new FormData(this);

        if (!formData.get('usuario') || !formData.get('contrasena')) {
            showAlert('Por favor complete todos los campos.', 'warning');
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="fas fa-sign-in-alt me-2"></i> INGRESAR';
            return;
        }

        fetch(this.action, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showAlert(data.success, 'success');
                setTimeout(() => {
                    window.location.href = data.redirect;
                }, 1000); // esperar un segundo antes de redirigir
            } else if (data.error) {
                showAlert(data.error, 'danger');
            }
        })
        .catch(error => {
            showAlert('Error de conexión: ' + error.message, 'danger');
        })
        .finally(() => {
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="fas fa-sign-in-alt me-2"></i> INGRESAR';
        });
    });

    function showAlert(message, type) {
        const oldAlert = document.querySelector('.alert');
        if (oldAlert) oldAlert.remove();

        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${type} alert-dismissible fade show mt-3`;
        alertDiv.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        `;

        loginForm.parentNode.insertBefore(alertDiv, loginForm.nextSibling);

        setTimeout(() => {
            alertDiv.classList.remove('show');
            setTimeout(() => alertDiv.remove(), 300);
        }, 5000);
    }
});
