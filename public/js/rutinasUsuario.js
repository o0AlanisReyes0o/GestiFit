// Configuración global
const API_URL = '/GestiFit/src/usuarioPHP/rutinas/obtenerRutinas.php';
const ROUTINE_CONTAINER = document.getElementById('routines-container');

// Estados de la UI
const UI_STATES = {
    loading: `
        <div class="col-12 text-center py-5">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Cargando rutinas...</span>
            </div>
            <p class="mt-3 text-muted">Cargando tus rutinas de entrenamiento...</p>
        </div>
    `,
    empty: `
        <div class="col-12 text-center py-5">
            <div class="alert alert-info">
                <i class="fas fa-info-circle me-2"></i>
                No hay rutinas disponibles en este momento.
            </div>
        </div>
    `,
    error: (message = 'Error al cargar las rutinas') => `
        <div class="col-12 text-center py-5">
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-triangle me-2"></i>
                ${message}
            </div>
            <button class="btn btn-primary mt-3" onclick="loadRoutines()">
                <i class="fas fa-sync-alt me-2"></i> Intentar nuevamente
            </button>
        </div>
    `
};

// Cargar rutinas al iniciar
document.addEventListener('DOMContentLoaded', () => {
    loadRoutines();
    setupFilterButtons();
});

// Configurar botones de filtro
function setupFilterButtons() {
    document.querySelectorAll('.filter-buttons button').forEach(button => {
        button.addEventListener('click', function() {
            // Actualizar estado visual de los botones
            document.querySelectorAll('.filter-buttons button').forEach(btn => {
                btn.classList.remove('active', 'btn-primary');
                btn.classList.add('btn-outline-primary');
            });
            
            this.classList.add('active', 'btn-primary');
            this.classList.remove('btn-outline-primary');
            
            // Aplicar filtro
            const filter = this.dataset.filter;
            filterRoutines(filter);
        });
    });
}

// Cargar rutinas desde la API
async function loadRoutines() {
    try {
        ROUTINE_CONTAINER.innerHTML = UI_STATES.loading;
        
        const response = await fetch(API_URL);
        
        if (!response.ok) {
            throw new Error(`Error HTTP: ${response.status}`);
        }
        
        const data = await response.json();
        
        if (!data.success || !data.rutinas || data.rutinas.length === 0) {
            ROUTINE_CONTAINER.innerHTML = UI_STATES.empty;
            return;
        }
        
        displayRoutines(data.rutinas);
    } catch (error) {
        console.error('Error:', error);
        ROUTINE_CONTAINER.innerHTML = UI_STATES.error(error.message);
    }
}

// Mostrar rutinas en el DOM
function displayRoutines(routines) {
    console.log('Datos de rutinas recibidos:', routines);
    ROUTINE_CONTAINER.innerHTML = routines.map(routine => `
        <div class="col-lg-6 mb-4" 
             data-level="${routine.nivel}" 
             data-goal="${routine.objetivo_key}">
            <div class="card routine-card ${routine.nivel}">
                <div class="card-header ${getHeaderClass(routine.nivel)}">
                    <h4><i class="fas ${getIconByLevel(routine.nivel)} me-2"></i> ${routine.nombre}</h4>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <span class="badge ${getBadgeClass(routine.nivel)} mb-2">
                            ${routine.nivel_display}
                        </span>
                        <small class="text-muted">${routine.duracion_semanas} semanas</small>
                    </div>
                    
                    <p class="card-text mt-2">${routine.descripcion}</p>
                    
                    <div class="routine-meta mb-3">
                        <span class="badge bg-light text-dark">
                            <i class="fas fa-bullseye me-1"></i> ${routine.objetivo}
                        </span>
                        <span class="badge bg-light text-dark">
                            <i class="fas fa-calendar-day me-1"></i> ${routine.dias_por_semana} días/sem
                        </span>
                    </div>
                    
                    <div class="mb-3">
                        <h5 class="h6"><i class="fas fa-tools me-2"></i>Equipamiento:</h5>
                        <ul class="equipment-list">
                            ${routine.equipamiento.map(item => `<li><i class="fas fa-check text-success me-2"></i>${item}</li>`).join('')}
                        </ul>
                    </div>
                    
                    <div class="mb-3">
                        <h5 class="h6"><i class="fas fa-list-ol me-2"></i>Instrucciones:</h5>
                        <ol class="ps-3">
                            ${routine.instrucciones.map(inst => `<li class="mb-1">${inst}</li>`).join('')}
                        </ol>
                    </div>
                    
                    ${routine.video_url ? `
                    <div class="mt-4">
                        <div class="ratio ratio-16x9">
                            <iframe src="${routine.video_url}" 
                                    title="Video de ${routine.nombre}"
                                    allowfullscreen
                                    loading="lazy">
                            </iframe>
                        </div>
                    </div>
                    ` : ''}
                </div>
            </div>
        </div>
    `).join('');
}

// Filtrar rutinas
function filterRoutines(filter) {
    console.log('Filtrando por:', filter);
    const routines = document.querySelectorAll('#routines-container > [data-level]');
    let hasVisibleItems = false;
    
    routines.forEach(routine => {
        const shouldShow = filter === 'all' || 
                         routine.dataset.level === filter || 
                         routine.dataset.goal === filter;
        
        routine.style.display = shouldShow ? '' : 'none';
        
        if (shouldShow) hasVisibleItems = true;
    });
    
    // Mostrar mensaje si no hay resultados
    const noResultsMessage = document.getElementById('no-results-message');
    if (noResultsMessage) {
        noResultsMessage.style.display = hasVisibleItems ? 'none' : 'block';
    }
}

// Helper: Obtener clase de badge según nivel
function getBadgeClass(level) {
    const classes = {
        'principiante': 'bg-success text-white',
        'intermedio': 'bg-warning text-dark',
        'avanzado': 'bg-danger text-white'
    };
    return classes[level] || 'bg-secondary text-white';
}

// Helper: Obtener clase para el encabezado según nivel
function getHeaderClass(level) {
    const classes = {
        'principiante': 'bg-success',
        'intermedio': 'bg-warning',
        'avanzado': 'bg-danger'
    };
    return classes[level] || 'bg-primary';
}

// Helper: Obtener icono según nivel
function getIconByLevel(level) {
    const icons = {
        'principiante': 'fa-seedling',
        'intermedio': 'fa-fire',
        'avanzado': 'fa-bolt'
    };
    return icons[level] || 'fa-dumbbell';
}