function validarLogin(e) {
    e.preventDefault();
    const user = document.getElementById('adminUser').value.trim();
    const pass = document.getElementById('adminPass').value.trim();
    const msg = document.getElementById('loginMsg');
    const spinner = document.getElementById('spinner');

    if (user.toLowerCase() === "admin" && pass === "admin123") {
        msg.style.display = "none";
        if (spinner) spinner.classList.remove('show');
        window.location.href = "admin.html";
    } else {
        msg.style.display = "block";
        if (spinner) spinner.classList.remove('show');
    }
}

document.getElementById('admin-name').innerText = localStorage.getItem('admin') || 'Admin';

        new Chart(document.getElementById('chart-membresia-usada'), {
            type: 'pie',
            data: { labels: ['Premium', 'Básica', 'Estudiantil'], datasets: [{ data: [45, 30, 25], backgroundColor: ['#007bff', '#28a745', '#ffc107'] }] }
        });
        new Chart(document.getElementById('chart-membresias-actuales'), {
            type: 'bar',
            data: { labels: ['Enero', 'Febrero', 'Marzo', 'Abril'], datasets: [{ label: 'Membresías activas', data: [120, 150, 130, 170], backgroundColor: '#17a2b8' }] }
        });
        new Chart(document.getElementById('chart-clientes'), {
            type: 'line',
            data: { labels: ['Abr', 'May'], datasets: [{ label: 'Clientes Nuevos', data: [80, 95], borderColor: '#6f42c1', tension: 0.4 }] }
        });
        new Chart(document.getElementById('chart-entrenadores'), {
            type: 'radar',
            data: { labels: ['Puntualidad', 'Trato', 'Rutinas', 'Motivación'], datasets: [{ label: 'Evaluación promedio', data: [4, 5, 4.5, 4.7], backgroundColor: 'rgba(255,99,132,0.2)', borderColor: 'rgb(255,99,132)' }] }
        });

  document.querySelectorAll('nav .nav-link').forEach(link => {
    if (link.href === window.location.href) {
      link.classList.add('active');
    } else {
      link.classList.remove('active');
    }
  });

// admin.js

const charts = {
    clientes: new Chart(document.getElementById('clientesChart'), {
        type: 'bar',
        data: {
            labels: ['Ene', 'Feb', 'Mar', 'Abr', 'May'],
            datasets: [{
                label: 'Clientes nuevos',
                data: [12, 19, 3, 5, 7],
                backgroundColor: 'rgba(54, 162, 235, 0.7)'
            }]
        }
    }),
    instructores: new Chart(document.getElementById('instructoresChart'), {
        type: 'line',
        data: {
            labels: ['A', 'B', 'C', 'D'],
            datasets: [{
                label: 'Evaluación mensual',
                data: [4, 3, 5, 4],
                backgroundColor: 'rgba(255, 99, 132, 0.4)',
                borderColor: 'rgba(255, 99, 132, 1)',
                fill: true
            }]
        }
    }),
    clases: new Chart(document.getElementById('clasesChart'), {
        type: 'doughnut',
        data: {
            labels: ['Yoga', 'HIIT', 'Zumba'],
            datasets: [{
                label: 'Preferencia',
                data: [10, 20, 30],
                backgroundColor: ['#007bff', '#dc3545', '#ffc107']
            }]
        }
    }),
    membresias: new Chart(document.getElementById('membresiasChart'), {
        type: 'pie',
        data: {
            labels: ['Estándar', 'Premium', 'VIP'],
            datasets: [{
                label: 'Distribución',
                data: [50, 30, 20],
                backgroundColor: ['#007bff', '#dc3545', '#ffc107']
            }]
        }
    })
};

function showChart(id) {
    document.querySelectorAll('.chart-container').forEach(c =>
        c.classList.remove('active')
    );
    document.getElementById(id).classList.add('active');

    document.querySelectorAll('.card-option').forEach(c =>
        c.classList.remove('active')
    );
    const card = document.getElementById('card-' + id);
    if (card) card.classList.add('active');
}
