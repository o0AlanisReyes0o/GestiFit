const charts = {
    clientes: new Chart(document.getElementById('clientesChart'), {
        type: 'bar',
        data: {
            labels: ['Ene', 'Feb', 'Mar', 'Abr', 'May'],
            datasets: [{
                label: 'Clientes nuevos',
                data: [12, 19, 3, 5, 7],
                backgroundColor: 'rgba(255, 246, 122, 0.7)'
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
    document.querySelectorAll('.chart-container').forEach(c => c.classList.remove('active'));
    document.getElementById(id).classList.add('active');

    document.querySelectorAll('.sidebar a').forEach(link => link.classList.remove('active'));
    const activeLink = Array.from(document.querySelectorAll('.sidebar a')).find(a => a.textContent.trim().toLowerCase().includes(id));
    if (activeLink) activeLink.classList.add('active');

    document.querySelectorAll('.card-option').forEach(c => c.classList.remove('active'));
    const card = document.getElementById('card-' + id);
    if (card) card.classList.add('active');
}

const sidebar = document.getElementById('sidebar');
const toggleBtn = document.getElementById('sidebar-toggle');

function setSidebarState(collapsed) {
    if (collapsed) {
        sidebar.classList.add('collapsed');
        document.body.classList.add('sidebar-collapsed');
        localStorage.setItem('sidebarCollapsed', 'true');
    } else {
        sidebar.classList.remove('collapsed');
        document.body.classList.remove('sidebar-collapsed');
        localStorage.setItem('sidebarCollapsed', 'false');
    }
}

toggleBtn.addEventListener('click', () => {
    const isCollapsed = sidebar.classList.contains('collapsed');
    setSidebarState(!isCollapsed);
});

document.addEventListener('DOMContentLoaded', () => {
    const storedState = localStorage.getItem('sidebarCollapsed') === 'true';
    setSidebarState(storedState);
});