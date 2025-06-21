// Función principal que se ejecuta cuando el DOM está listo
document.addEventListener('DOMContentLoaded', function() {
    // Inicializar componentes
    initCarousel();
    
    // Cargar datos dinámicos
    loadUserData();
    loadAnnouncements();
    loadMembershipData();
    loadUpcomingClasses();
    loadMembershipPlans();
    // Configurar eventos
    setupEventHandlers();
});

// Inicializar el carrusel de bienvenida
function initCarousel() {
    const carousel = new bootstrap.Carousel('#header-carousel', {
        interval: 5000,
        pause: "hover"
    });
    
    // Animaciones para los elementos del carrusel
    document.getElementById('header-carousel').addEventListener('slide.bs.carousel', function(e) {
        const activeSlide = e.from;
        const nextSlide = e.to;
        
        // Quitar animaciones del slide activo
        document.querySelectorAll(`.carousel-item:nth-child(${activeSlide + 1}) .animate__animated`)
            .forEach(el => el.classList.remove('animate__fadeInDown', 'animate__fadeInUp', 'animate__zoomIn'));
        
        // Agregar animaciones al siguiente slide
        document.querySelectorAll(`.carousel-item:nth-child(${nextSlide + 1}) .animate__animated`)
            .forEach(el => {
                if(el.tagName === 'H1') {
                    el.classList.add('animate__fadeInDown');
                } else if(el.tagName === 'P') {
                    el.classList.add('animate__fadeInUp');
                } else if(el.tagName === 'A') {
                    el.classList.add('animate__zoomIn');
                }
            });
    });
}

// Cargar datos del usuario
async function loadUserData() {
    try {
        const response = await fetch('/GestiFit/src/usuarioPHP/usuario/obtenerInfo.php', {
            credentials: 'include' // Para enviar cookies de sesión
        });
        
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        const data = await response.json();
        
        if(data.success) {
            // Actualizar nombre de usuario en el navbar
            const nameElement = document.querySelector('.topbar .text-white strong');
            if (nameElement) {
                nameElement.textContent = data.data.name || 'Usuario';
            }
            
            console.log('Datos del usuario cargados:', data.data);
            
            // Actualizar avatar en el modal si existe
            const avatarElement = document.querySelector('#profileModal img');
            if (avatarElement && data.data.avatar) {
                avatarElement.src = data.data.avatar;
                avatarElement.alt = `Avatar de ${data.data.name}`;
            }
            
            // Actualizar datos en el modal de perfil
            updateProfileModal(data.data);
        } else {
            console.error('Error al cargar datos del usuario:', data.message);
            showErrorAlert('Error al cargar datos de usuario');
        }
    } catch (error) {
        console.error('Error en la solicitud:', error);
        showErrorAlert('Error de conexión al cargar datos');
    }
}

// Función para actualizar el modal de perfil con los datos del usuario
function updateProfileModal(userData) {
    const modal = document.getElementById('profileModal');
    if (!modal) return;
    
    // Actualizar campos del formulario
    modal.querySelector('input[type="text"]').value = userData.name || '';
    modal.querySelector('input[type="email"]').value = userData.email || '';
    modal.querySelector('input[type="tel"]').value = userData.phone || '';
    
    // Formatear fecha de nacimiento para el input date (YYYY-MM-DD)
    if (userData.birthdate) {
        const [day, month, year] = userData.birthdate.split('/');
        modal.querySelector('input[type="date"]').value = `${year}-${month.padStart(2, '0')}-${day.padStart(2, '0')}`;
    }
    
    // Actualizar otros campos según sea necesario
    // modal.querySelector('select').value = userData.gender || '';
    // modal.querySelector('textarea').value = userData.goals || '';
}

// Función para mostrar errores
function showErrorAlert(message) {
    const alertDiv = document.createElement('div');
    alertDiv.className = 'alert alert-danger position-fixed top-0 end-0 m-3';
    alertDiv.style.zIndex = '9999';
    alertDiv.textContent = message;
    
    document.body.appendChild(alertDiv);
    
    setTimeout(() => {
        alertDiv.remove();
    }, 5000);
}

// Función para cargar avisos en el carrusel
async function loadAnnouncements() {
    const carouselInner = document.querySelector('#header-carousel .carousel-inner');
    
    try {
        const response = await fetch('/GestiFit/src/usuarioPHP/obtenerAvisos.php');
        
        // Verificar si la respuesta es JSON
        const contentType = response.headers.get('content-type');
        if (!contentType || !contentType.includes('application/json')) {
            throw new Error('La respuesta no es JSON');
        }
        
        const data = await response.json();
        
        if (!data.success) {
            throw new Error(data.message || 'Error en los datos recibidos');
        }
        
        // Limpiar solo slides adicionales (mantener las 2 base)
        const existingSlides = Array.from(carouselInner.querySelectorAll('.carousel-item'));
        if (existingSlides.length > 2) {
            existingSlides.slice(2).forEach(slide => slide.remove());
        }
        
        console.log('Avisos cargados:', data.data);
        // Agregar avisos
        if (data.data && data.data.length > 0) {
            data.data.forEach((aviso, index) => {
                const slide = document.createElement('div');
                slide.className = `carousel-item ${index === 0 && existingSlides.length <= 2 ? 'active' : ''}`;
                
                // Asegurar que la imagen tenga una ruta válida
                const imagePath = aviso.image 
                    ? `/GestiFit/public/img/${aviso.image}`
                    : '/GestiFit/public/img/bannerNegro.jpg';
                
                slide.innerHTML = `
                    <img class="d-block w-100" src="${imagePath}" alt="${aviso.title || 'Aviso'}">
                    <div class="carousel-caption d-flex flex-column justify-content-center align-items-center h-100 p-4">
                    <h1 class="text-white mb-3 d-flex align-items-center">
                            <img src="/GestiFit/public/img/megafono.png" alt="Ícono de megáfono" width="32" height="32" class="me-2">
                            Avisos
                        </h1>
                        <div class="bg-dark bg-opacity-75 rounded p-4 text-center">
                            <h3 class="text-white animate__animated animate__fadeInDown">${aviso.title || 'Aviso importante'}</h3>
                            <p class="animate__animated animate__fadeInUp">${aviso.description || ''}</p>
                            ${aviso.button?.text ? `
                                <a href="${aviso.button?.link || '#'}" class="btn btn-primary animate__animated animate__zoomIn">
                                    ${aviso.button.text}
                                </a>
                            ` : ''}
                        </div>
                    </div>
                `;
                
                carouselInner.appendChild(slide);
            });
            
            // Actualizar indicadores
            updateCarouselIndicators();
            
            // Reiniciar carrusel si está inicializado
            const carousel = bootstrap.Carousel.getInstance('#header-carousel');
            if (carousel) {
                carousel.dispose();
            }
            new bootstrap.Carousel('#header-carousel');
        }
        
    } catch (error) {
        console.error('Error al cargar avisos:', error);
        showFallbackAnnouncement();
    }
}

function showFallbackAnnouncement() {
    const carouselInner = document.querySelector('#header-carousel .carousel-inner');
    const fallbackSlide = document.createElement('div');
    fallbackSlide.className = 'carousel-item active';
    fallbackSlide.innerHTML = `
        <img class="d-block w-100" src="/GestiFit/public/img/bannerNegro.jpg" alt="Aviso importante">
        <div class="carousel-caption d-flex flex-column justify-content-center align-items-center h-100 p-4">
            <div class="bg-dark bg-opacity-75 rounded p-4 text-center">
                <h3 class="animate__animated animate__fadeInDown">Información importante</h3>
                <p class="animate__animated animate__fadeInUp">Estamos teniendo problemas para cargar los avisos. Por favor intenta más tarde.</p>
            </div>
        </div>
    `;
    
    // Mantener solo las dos primeras slides base + este fallback
    const existingSlides = Array.from(carouselInner.querySelectorAll('.carousel-item'));
    if (existingSlides.length > 2) {
        existingSlides.slice(2).forEach(slide => slide.remove());
    }
    
    carouselInner.appendChild(fallbackSlide);
    updateCarouselIndicators();
}

function updateCarouselIndicators() {
    const indicatorsContainer = document.querySelector('#header-carousel .carousel-indicators');
    const slides = document.querySelectorAll('#header-carousel .carousel-item');
    
    // Limpiar indicadores existentes
    indicatorsContainer.innerHTML = '';
    
    // Crear nuevos indicadores
    slides.forEach((_, index) => {
        const button = document.createElement('button');
        button.type = 'button';
        button.dataset.bsTarget = '#header-carousel';
        button.dataset.bsSlideTo = index;
        if (index === 0) button.className = 'active';
        
        indicatorsContainer.appendChild(button);
    });
}


// Inicialización mejorada
document.addEventListener('DOMContentLoaded', () => {
    const carouselElement = document.querySelector('#header-carousel');
    if (carouselElement) {
        // Inicializar carrusel primero
        new bootstrap.Carousel(carouselElement, {
            interval: 5000,
            pause: 'hover'
        });
        
        // Luego cargar anuncios
        loadAnnouncements();
    }
});

// Llamar a la función cuando se cargue la página
document.addEventListener('DOMContentLoaded', function() {
    // ... otras inicializaciones ...
    loadAnnouncements();
});

// Cargar próximas clases (versión informativa)
async function loadUpcomingClasses() {
    const cardBody = document.querySelector('#proximas-clases-card .card-body');
    
    try {

                // 1. Primero haz la petición sin procesar
        const response = await fetch('/GestiFit/src/usuarioPHP/clases/indexVerClases.php');
        
        // 2. Obtén el texto crudo de la respuesta
        const rawResponse = await response.text();
        
        // 3. Muestra la respuesta cruda en la consola
        console.log('Respuesta cruda:', rawResponse);
        
        // 4. Ahora intenta parsear como JSON
        const data = JSON.parse(rawResponse);
        // Mostrar estado de carga
        cardBody.innerHTML = '<div class="text-center"><div class="spinner-border text-primary" role="status"></div></div>';

        //const response = await fetch('/GestiFit/src/usuarioPHP/clases/indexVerClases.php');
        
        // Verificar si la respuesta es JSON
        const contentType = response.headers.get('content-type');
        if (!contentType || !contentType.includes('application/json')) {
            throw new Error('La respuesta no es JSON');
        }

        //const data = await response.json();
        
        if (!response.ok || !data.success) {
            throw new Error(data.message || 'Error al cargar clases');
        }

        // Limpiar contenido existente
        cardBody.innerHTML = '';

        if (data.data && data.data.length > 0) {
            data.data.forEach((classItem, index) => {
                const isToday = classItem.is_today ? 'bg-primary' : 'bg-secondary';
                const badgeText = classItem.is_today ? 'Hoy' : classItem.day;
                
                const classHtml = document.createElement('div');
                classHtml.className = `mb-3 ${index > 0 ? 'mt-3' : ''}`;
                classHtml.innerHTML = `
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6 class="mb-0">${classItem.class_name}</h6>
                        <span class="badge ${isToday}">${badgeText}</span>
                    </div>
                    <p class="mb-1"><small><i class="far fa-clock me-2"></i>${classItem.start_time} - ${classItem.end_time}</small></p>
                    <p class="mb-2"><small><i class="fas fa-user-tie me-2"></i>Instr. ${classItem.instructor}</small></p>
                    ${index < data.data.length - 1 ? '<hr>' : ''}
                `;
                
                cardBody.appendChild(classHtml);
            });
        } else {
            cardBody.innerHTML = '<p class="text-center text-muted">No tienes clases programadas</p>';
        }

        // Agregar botón "Ver Todas" si existe contenido
        if (data.data && data.data.length > 0) {
            const verTodasBtn = document.createElement('a');
            verTodasBtn.href = 'clases.html';
            verTodasBtn.className = 'btn btn-primary w-100 mt-3';
            verTodasBtn.textContent = 'Ver Todas';
            cardBody.appendChild(verTodasBtn);
        }

    } catch (error) {
        console.error('Error al cargar próximas clases:', error);
        cardBody.innerHTML = `
            <p class="text-center text-danger">
                <i class="fas fa-exclamation-triangle me-2"></i>
                ${error.message || 'Error al cargar las clases'}
            </p>
            <button class="btn btn-sm btn-outline-primary w-100 mt-2" onclick="loadUpcomingClasses()">
                <i class="fas fa-sync-alt me-2"></i>Reintentar
            </button>
        `;
    }
}
// Cargar datos de membresía
async function loadMembershipData() {
    try {
        const response = await fetch('/GestiFit/src/usuarioPHP/membresia/indexVerMemb.php');
        const data = await response.json();
        
        if(data.success) {

            console.log('Datos de membresía cargados:', data.data);
            const membership = data.data;
            const card = document.querySelector('#mi-membresia-card');
            
            // Actualizar progreso
            const progressPercentage = Math.round((membership.days_used / membership.total_days) * 100);
            card.querySelector('.progress-bar').style.width = `${progressPercentage}%`;
            
            // Actualizar texto
            card.querySelector('h5').textContent = membership.plan_name;
            card.querySelector('small.text-muted').textContent = `Válida hasta: ${membership.end_date}`;
            card.querySelector('p.mb-3').innerHTML = `Días restantes: <strong>${membership.days_remaining}</strong>`;
            
            // Actualizar botón según estado
            const btn = card.querySelector('a.btn');
            if(membership.can_renew) {
                btn.classList.remove('btn-secondary');
                btn.classList.add('btn-outline-primary');
                btn.textContent = 'Renovar';
            } else {
                btn.classList.remove('btn-outline-primary');
                btn.classList.add('btn-secondary');
                btn.textContent = 'Ver detalles';
            }
        }
    } catch (error) {
        console.error('Error al cargar datos de membresía:', error);
    }
}

// Cargar planes de membresía disponibles (versión corregida)
async function loadMembershipPlans() {
    try {
        const response = await fetch('/GestiFit/src/usuarioPHP/membresia/verMemb.php');
        const data = await response.json();
        
        const plansContainer = document.getElementById('planes-container');
        const loadingElement = document.getElementById('planes-loading');
        
        if(data.success && data.data && data.data.length > 0) {
            // Ocultar elemento de carga
            loadingElement.style.display = 'none';
            
            // Generar tarjetas para cada plan
            data.data.forEach(plan => {
                // Asegurarnos que el precio sea un número
                const price = typeof plan.price === 'number' ? plan.price : 
                             parseFloat(plan.price) || 0;
                
                const planCol = document.createElement('div');
                planCol.className = 'col-md-4 mb-4';
                planCol.innerHTML = `
                    <div class="card membership-plan-card h-100 border-0 shadow-sm hover-top">
                        <div class="card-header ${plan.featured ? 'bg-primary text-white' : 'bg-success'} position-relative">
                            <h4 class="mb-0 text-center text-white">${plan.name || 'Plan'}</h4>
                            ${plan.featured ? '<span class="badge bg-warning text-dark position-absolute top-0 end-0 m-2">Popular</span>' : ''}
                        </div>
                        <div class="card-body">
                            <h2 class="text-center text-primary my-4">$${price.toFixed(2)}</h2>
                            <p class="text-center text-muted">${plan.duration || 'Duración no especificada'}</p>
                            ${plan.description ? `<p class="text-muted mb-4">${plan.description}</p>` : ''}
                            <hr>
                            <h6 class="text-primary">Beneficios:</h6>
                            <ul class="list-unstyled">
                                ${(plan.features || []).map(feature => `
                                    <li class="mb-2"><i class="fas fa-check text-primary me-2"></i>${feature}</li>
                                `).join('')}
                            </ul>
                        </div>
                        <div class="card-footer bg-transparent border-0 text-center">
                            ${plan.current ? 
                                `<button class="btn btn-outline-primary w-100" disabled>Tu plan actual</button>` :
                                `<a href='membresia.html'><button class="btn btn-${plan.upgrade ? 'warning' : 'primary'} w-100 select-plan" 
                                 data-plan-id="${plan.id}">
                                    ${plan.upgrade ? 'Mejorar Plan' : 'Seleccionar'}
                                </button></a>`
                            }
                        </div>
                    </div>
                `;
                plansContainer.appendChild(planCol);
            });
            
            // Configurar eventos para los botones de selección
            document.querySelectorAll('.select-plan').forEach(button => {
                button.addEventListener('click', function() {
                    selectMembershipPlan(this.dataset.planId);
                });
            });
        } else {
            loadingElement.innerHTML = '<p class="text-muted">No hay planes disponibles en este momento.</p>';
        }
    } catch (error) {
        console.error('Error al cargar planes de membresía:', error);
        document.getElementById('planes-loading').innerHTML = `
            <p class="text-danger">Error al cargar los planes. Por favor intenta más tarde.</p>
            <button class="btn btn-sm btn-outline-primary mt-2" onclick="loadMembershipPlans()">Reintentar</button>
        `;
    }
}

// Actualizar perfil de usuario
async function updateProfile() {
    const formData = {
        name: document.querySelector('#profileModal input[type="text"]').value,
        email: document.querySelector('#profileModal input[type="email"]').value,
        phone: document.querySelector('#profileModal input[type="tel"]').value,
        birthdate: document.querySelector('#profileModal input[type="date"]').value,
        gender: document.querySelector('#profileModal select').value,
        goals: document.querySelector('#profileModal textarea').value
    };
    
    try {
        const response = await fetch('/GestiFit/api/updateProfile.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(formData)
        });
        const data = await response.json();
        
        if(data.success) {
            showAlert('success', 'Perfil actualizado', 'Tus datos se han guardado correctamente.');
            loadUserData(); // Recargar datos del usuario
            
            // Cerrar modal
            const modal = bootstrap.Modal.getInstance('#profileModal');
            modal.hide();
        } else {
            showAlert('danger', 'Error', data.message || 'No se pudo actualizar el perfil.');
        }
    } catch (error) {
        showAlert('danger', 'Error', 'Ocurrió un error al intentar actualizar el perfil.');
        console.error('Error:', error);
    }
}

// Cambiar contraseña
async function changePassword() {
    const currentPassword = prompt('Ingresa tu contraseña actual:');
    if(!currentPassword) return;
    
    const newPassword = prompt('Ingresa tu nueva contraseña:');
    if(!newPassword) return;
    
    const confirmPassword = prompt('Confirma tu nueva contraseña:');
    if(newPassword !== confirmPassword) {
        return showAlert('danger', 'Error', 'Las contraseñas no coinciden.');
    }
    
    try {
        const response = await fetch('/GestiFit/api/changePassword.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                current_password: currentPassword,
                new_password: newPassword
            })
        });
        const data = await response.json();
        
        if(data.success) {
            showAlert('success', 'Contraseña cambiada', 'Tu contraseña se ha actualizado correctamente.');
        } else {
            showAlert('danger', 'Error', data.message || 'No se pudo cambiar la contraseña.');
        }
    } catch (error) {
        showAlert('danger', 'Error', 'Ocurrió un error al intentar cambiar la contraseña.');
        console.error('Error:', error);
    }
}

// Mostrar alerta al usuario
function showAlert(type, title, message) {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type} alert-dismissible fade show position-fixed top-0 end-0 m-3`;
    alertDiv.style.zIndex = '9999';
    alertDiv.role = 'alert';
    alertDiv.innerHTML = `
        <strong>${title}</strong> ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    `;
    
    document.body.appendChild(alertDiv);
    
    // Eliminar la alerta después de 5 segundos
    setTimeout(() => {
        const bsAlert = new bootstrap.Alert(alertDiv);
        bsAlert.close();
    }, 5000);
}

// Actualizar datos periódicamente
setInterval(() => {
    loadMembershipData();
    loadUpcomingClasses();
}, 300000); // Actualizar cada 5 minutos