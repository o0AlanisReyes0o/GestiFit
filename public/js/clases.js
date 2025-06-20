document.addEventListener('DOMContentLoaded', function() {
    // Elementos del DOM
    const clasesContainer = document.getElementById('clases-container');
    const modalsContainer = document.getElementById('modals-container');
    const scheduleBody = document.getElementById('schedule-body');
    const filterDay = document.getElementById('filter-day');
    const filterTime = document.getElementById('filter-time');
    const filterLevel = document.getElementById('filter-level');

    // Datos de las clases
    let clasesData = [];
    let scheduleData = {};

    async function loadClases() {
        try {
            const response = await fetch('/GestiFit/src/usuarioPHP/clases/obtenerClases.php');
            const data = await response.json();
            
            if (!data.success) {
                throw new Error(data.error || 'Error al cargar clases');
            }
            
            // Verificar estructura de datos
            if (!Array.isArray(data.clases) || typeof data.horario !== 'object') {
                console.error("Estructura de datos inválida:", data);
                throw new Error('Formato de datos incorrecto del servidor');
            }
            
            clasesData = data.clases;
            scheduleData = data.horario;
            renderClases();
            renderSchedule();
            
        } catch (error) {
            console.error('Error cargando clases:', error);
            mostrarError(error);
        }
    }

    function mostrarError(error) {
        clasesContainer.innerHTML = `
            <div class="alert alert-danger">
                <h4>Error al cargar clases</h4>
                <p>${error.message}</p>
                <button class="btn btn-primary" onclick="loadClases()">Reintentar</button>
            </div>
        `;
    }

    // Función para renderizar las clases
    function renderClases() {
        clasesContainer.innerHTML = '';
        modalsContainer.innerHTML = '';
        console.log(clasesData);
        clasesData.forEach(clase => {
            // Crear tarjeta de clase
            const classCard = document.createElement('div');
            classCard.className = 'col-lg-4 col-md-6 wow fadeInUp';
            classCard.setAttribute('data-wow-delay', '0.2s');
            classCard.innerHTML = `
                <div class="class-card h-100" data-id="${clase.id_clase}" data-dias="${clase.dias.join(',')}" 
                     data-hora="${clase.hora_inicio.split(':')[0]}" data-nivel="${clase.dificultad}">
                    <div class="class-img position-relative overflow-hidden">
                        <img src="/GestiFit/public/img/work-6.jpg" 
                             class="img-fluid w-100" alt="${clase.nombre}">
                        <div class="class-overlay d-flex align-items-center justify-content-center">
                            <div class="text-center p-4">
                                <h4 class="text-white mb-3">${clase.nombre}</h4>
                                <a href="#modal-clase-${clase.id_clase}" class="btn btn-primary" data-bs-toggle="modal">Más información</a>
                            </div>
                        </div>
                        <div class="class-status ${clase.cupos_disponibles > 0 ? 'status-available' : 'status-full'}">
                            ${clase.cupos_disponibles > 0 ? 'Disponible' : 'Lleno'}
                        </div>
                    </div>
                    <div class="class-content p-4">
                        <div class="d-flex justify-content-between mb-3">
                            <span class="badge bg-${getBadgeColor(clase.dificultad)}">${capitalizeFirstLetter(clase.dificultad)}</span>
                            <span class="text-muted"><i class="far fa-clock me-2"></i> ${clase.hora_inicio} - ${clase.hora_fin}</span>
                        </div>
                        <h4 class="mb-3">${clase.nombre}</h4>
                        <p class="mb-4">${clase.descripcion}</p>
                        <div class="d-flex justify-content-between border-top pt-3">
                            <div>
                                <i class="fas fa-user-tie text-primary me-2"></i>
                                <span>${clase.instructor_nombre} ${clase.instructor_apellido}</span>
                            </div>
                            <div>
                                <i class="fas fa-calendar-alt text-primary me-2"></i>
                                <span>${clase.dias.map(d => capitalizeFirstLetter(d)).join(', ')}</span>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            clasesContainer.appendChild(classCard);

            // Crear modal para la clase
            const modal = document.createElement('div');
            modal.className = 'modal fade';
            modal.id = `modal-clase-${clase.id_clase}`;
            modal.setAttribute('tabindex', '-1');
            modal.setAttribute('aria-labelledby', `modalLabel-${clase.id_clase}`);
            modal.setAttribute('aria-hidden', 'true');
            modal.innerHTML = `
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="modalLabel-${clase.id_clase}">${clase.nombre}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <img src="/GestiFit/public/img/icon-3.png" 
                                         class="img-fluid rounded mb-3" alt="${clase.nombre}">
                                    <div class="mb-4">
                                        <h5 class="text-primary">Descripción</h5>
                                        <p>${clase.descripcion}</p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="card border-0 shadow-sm h-100">
                                        <div class="card-body p-4">
                                            <h5 class="card-title text-primary mb-4">Horarios Disponibles</h5>
                                            
                                            ${clase.dias.map(dia => `
                                                <div class="schedule-item mb-3 p-3 bg-light rounded">
                                                    <div class="d-flex justify-content-between mb-2">
                                                        <strong>${capitalizeFirstLetter(dia)}</strong>
                                                        <span class="badge bg-${clase.cupos_disponibles > 0 ? 'primary' : 'danger'}">
                                                            ${clase.cupos_disponibles > 0 ? 'Disponible' : 'Lleno'}
                                                        </span>
                                                    </div>
                                                    <p class="mb-1"><i class="far fa-clock me-2"></i> ${clase.hora_inicio} - ${clase.hora_fin}</p>
                                                    <p class="mb-1"><i class="fas fa-user-tie me-2"></i> ${clase.instructor_nombre} ${clase.instructor_apellido}</p>
                                                    <p class="mb-0"><i class="fas fa-users me-2"></i> 
                                                        <span class="seats-${getSeatsClass(clase.cupos_disponibles)}">
                                                            ${clase.cupos_disponibles}/${clase.cupo_maximo} cupos
                                                        </span>
                                                    </p>
                                                </div>
                                            `).join('')}
                                            
                                            <div class="mt-4">
                                                <h5 class="text-primary mb-3">Requisitos</h5>
                                                <ul class="requirements-list">
                                                    ${clase.requisitos.map(r => `<li>${r}</li>`).join('')}
                                                </ul>
                                            </div>
                                            
                                            <div class="mt-4">
                                                <button class="btn btn-primary w-100 btn-register" 
                                                        data-clase-id="${clase.id_clase}"
                                                        ${clase.cupos_disponibles <= 0 ? 'disabled' : ''}>
                                                    <i class="fas fa-calendar-plus me-2"></i> 
                                                    ${clase.cupos_disponibles > 0 ? 'Inscribirse a esta clase' : 'Clase llena'}
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            modalsContainer.appendChild(modal);
        });

        // Inicializar eventos de los botones de registro
        initRegisterButtons();
    }

    // Función para renderizar el horario
    function renderSchedule() {
        scheduleBody.innerHTML = '';

        // Ordenar las horas
        const horas = Object.keys(scheduleData).sort((a, b) => a - b);

        horas.forEach(hora => {
            const row = document.createElement('tr');
            const horaData = scheduleData[hora];
            
            row.innerHTML = `
                <td>${horaData.hora}</td>
                ${['lunes', 'martes', 'miércoles', 'jueves', 'viernes', 'sábado'].map(dia => `
                    <td>
                        ${horaData[dia] ? `
                            <span class="d-block">${horaData[dia].nombre}</span>
                            <small class="text-muted">(${horaData[dia].instructor})</small>
                        ` : ''}
                    </td>
                `).join('')}
            `;
            
            scheduleBody.appendChild(row);
        });
    }

    // Función para inicializar los botones de registro
    function initRegisterButtons() {
        document.querySelectorAll('.btn-register').forEach(button => {
            button.addEventListener('click', function() {
                const claseId = this.dataset.claseId;
                registerToClass(claseId);
            });
        });
    }

    // Función para registrar a una clase
    async function registerToClass(claseId) {
        try {
            // Verificar si el usuario está logueado (deberías implementar esto)
            const isLoggedIn = checkIfUserIsLoggedIn();
            
            if (!isLoggedIn) {
                window.location.href = `login.php?redirect=${encodeURIComponent(window.location.pathname)}&action=register&clase=${claseId}`;
                return;
            }

            const response = await fetch('/GestiFit/src/usuarioPHP/clases/inscribirClase.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    id_clase: claseId,
                    id_usuario: getUserId() // Deberías implementar esta función
                })
            });

            const data = await response.json();

            if (data.success) {
                alert('¡Inscripción exitosa!');
                // Recargar las clases para actualizar la disponibilidad
                loadClases();
            } else {
                alert('Error: ' + (data.message || 'No se pudo completar la inscripción'));
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Error al conectar con el servidor');
        }
    }

    // Funciones auxiliares
    function getBadgeColor(dificultad) {
        switch(dificultad) {
            case 'principiante': return 'success';
            case 'intermedio': return 'primary';
            case 'avanzado': return 'warning';
            default: return 'secondary';
        }
    }

    function getSeatsClass(cupos) {
        if (cupos > 5) return 'available';
        if (cupos > 0) return 'low';
        return 'none';
    }

    function capitalizeFirstLetter(string) {
        return string.charAt(0).toUpperCase() + string.slice(1);
    }

    // Funciones que deberías implementar según tu sistema
    function checkIfUserIsLoggedIn() {
        // Implementar lógica para verificar si el usuario está logueado
        return false;
    }

    function getUserId() {
        // Implementar lógica para obtener el ID del usuario logueado
        return 0;
    }

    // Filtrado de clases
    function applyFilters() {
        const dayValue = filterDay.value;
        const timeValue = filterTime.value;
        const levelValue = filterLevel.value;
        
        document.querySelectorAll('.class-card').forEach(card => {
            const cardDays = card.dataset.dias.split(',');
            const cardHour = parseInt(card.dataset.hora);
            const cardLevel = card.dataset.nivel;
            
            // Verificar filtros
            const dayMatch = !dayValue || cardDays.includes(dayValue);
            const levelMatch = !levelValue || cardLevel === levelValue;
            
            let timeMatch = true;
            if (timeValue === 'morning') {
                timeMatch = cardHour >= 6 && cardHour < 12;
            } else if (timeValue === 'afternoon') {
                timeMatch = cardHour >= 12 && cardHour < 18;
            } else if (timeValue === 'evening') {
                timeMatch = cardHour >= 18 && cardHour < 21;
            }
            
            // Mostrar/ocultar según coincidencia
            card.closest('.col-lg-4').style.display = (dayMatch && timeMatch && levelMatch) ? 'block' : 'none';
        });
    }

    // Event listeners para filtros
    filterDay.addEventListener('change', applyFilters);
    filterTime.addEventListener('change', applyFilters);
    filterLevel.addEventListener('change', applyFilters);

    // Cargar las clases al iniciar
    loadClases();
});