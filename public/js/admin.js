document.addEventListener('DOMContentLoaded', () => {
  const chartIds = {
    clientes: 'clientesChart',
    instructores: 'instructoresChart',
    clases: 'clasesChart',
    membresias: 'membresiasChart'
  };

  fetch('/GestiFit/public/php/datosGraficas.php')
    .then(response => response.json())
    .then(data => {
      Object.entries(chartIds).forEach(([key, id]) => {
        const canvas = document.getElementById(id);
        if (!canvas) return;

        const ctx = canvas.getContext('2d');

        // 🔥 Destruye el gráfico anterior si existe
        const existingChart = Chart.getChart(ctx);
        if (existingChart) {
          existingChart.destroy();
        }

        let config;

        switch (key) {
          case 'clientes':
            config = {
              type: 'bar',
              data: {
                labels: data[key].labels,
                datasets: [{
                  label: 'Clientes nuevos',
                  data: data[key].data,
                  backgroundColor: '#0C1844',
                  borderColor: 'rgba(54, 162, 235, 1)',
                  fill: true
                }]
              }
            };
            break;

          case 'instructores':
            config = {
              type: 'line',
              data: {
                labels: data[key].labels,
                datasets: [{
                  label: 'Evaluación mensual',
                  data: data[key].data,
                  backgroundColor: 'rgba(255, 99, 132, 0.4)',
                  borderColor: 'rgba(255, 99, 132, 1)',
                  fill: true
                }]
              }
            };
            break;

          case 'clases':
            config = {
              type: 'doughnut',
              data: {
                labels: data[key].labels,
                datasets: [{
                  label: 'Preferencia',
                  data: data[key].data,
                  backgroundColor: ['#0C1844', '#6f42c1', '#20c997', '#e4809b' , '#0dcaf0']
                }]
              }
            };
            break;

          case 'membresias':
            config = {
              type: 'pie',
              data: {
                labels: data[key].labels,
                datasets: [{
                  label: 'Distribución',
                  data: data[key].data,
                  backgroundColor: ['#007bff', '#C80036', '#0C1844', '#d63384']
                }]
              }
            };
            break;
        }

        new Chart(ctx, config);
      });
    })
    .catch(err => {
      console.error("Error al cargar datos de gráficas:", err);
    });

  // --- BOTONES "VER MÁS" ---
  document.querySelectorAll('.ver-mas-btn').forEach(btn => {
    btn.addEventListener('click', (e) => {
      e.stopPropagation();
      const wrapper = btn.closest('.chart-wrapper');
      if (wrapper) {
        const link = wrapper.getAttribute('data-link');
        if (link) window.location.href = link;
      }
    });
  });

  // --- FUNCIONALIDAD PÁGINA ACTIVA EN MENÚ ---
  const currentPath = window.location.pathname.toLowerCase();

  document.querySelectorAll('nav a.nav-link').forEach(link => {
    const href = link.getAttribute('href').toLowerCase();

    // Marca como activo si la ruta coincide o si es la página actual
    if (href === currentPath || (href !== "#" && currentPath.includes(href))) {
      link.classList.add('active');
    } else {
      link.classList.remove('active');
    }
  });
});