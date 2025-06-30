document.addEventListener('DOMContentLoaded', () => {
    // Cargar todos los datos al iniciar
    cargarDatosMembresia();
    cargarHistorialPagos();
    cargarMembresiasDisponibles();
    
    // Configurar eventos
    //document.getElementById('renew-btn')?.addEventListener('click', renovarMembresia);
    document.getElementById('upgrade-btn')?.addEventListener('click', mostrarModalMembresias);
    document.getElementById('make-payment-btn')?.addEventListener('click', mostrarModalPago);
    document.getElementById('renew-upgrade-btn')?.addEventListener('click', mostrarModalPago);
    
    // Evento para cuando se abre el modal de pago
    $('#paymentModal').on('show.bs.modal', cargarFormularioPago);

});

// ================ FUNCIONES PRINCIPALES ================

async function cargarDatosMembresia() {
    try {
        const response = await fetch('/GestiFit/src/usuarioPHP/membresia/obtenerMemb.php');
        
        if (!response.ok) {
            throw new Error('Error en la respuesta del servidor');
        }
        
        const data = await response.json();
        
        if (!data.exito) {
            // Mostrar mensaje amigable cuando no hay membresía
            mostrarMensajeSinMembresia();
            return;
        }
        
        actualizarUIMembresia(data.membresia);
    } catch (error) {
        console.error("Error al cargar membresía:", error);
        mostrarMensajeSinMembresia();
    }
}

function mostrarMensajeSinMembresia() {
    const container = document.getElementById('membership-info');
    if (!container) return;
    
    container.innerHTML = `
        <div class="alert alert-info">
            <h4>No tienes una membresía activa</h4>
            <p>Actualmente no estás disfrutando de los beneficios de una membresía.</p>
        </div>
    `;
}

async function cargarHistorialPagos() {
    const tbody = document.getElementById('payments-body');
    
    try {
        // Mostrar estado de carga
        tbody.innerHTML = `
            <tr>
                <td colspan="6" class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Cargando historial...</span>
                    </div>
                </td>
            </tr>
        `;

        const response = await fetch('/GestiFit/src/usuarioPHP/pagos/obtenerPagos.php');
        console.log(response)
        // Verificar si la respuesta es exitosa
        if (!response.ok) {
            throw new Error(`Error HTTP: ${response.status}`);
        }

        // Verificar el tipo de contenido
        const contentType = response.headers.get('content-type');
        if (!contentType || !contentType.includes('application/json')) {
            const textResponse = await response.text();
            console.error('Respuesta no JSON:', textResponse);
            throw new Error('El servidor no devolvió JSON');
        }

        const data = await response.json();
        console.log('Datos recibidos:', data); // Para depuración

        // Validar estructura de los datos
        if (!data || !data.pagos || !Array.isArray(data.pagos)) {
            throw new Error('Estructura de datos incorrecta');
        }

        // Procesar y mostrar los pagos
        actualizarUIHistorialPagos(data.pagos);
        
    } catch (error) {
        console.error('Error al cargar historial:', error);
        tbody.innerHTML = `
            <tr>
                <td colspan="6" class="text-center py-4 text-danger">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    ${error.message || 'Error al cargar el historial'}
                </td>
            </tr>
        `;
    }
}

async function cargarMembresiasDisponibles() {
    try {
        const response = await fetch('/GestiFit/src/usuarioPHP/membresia/obtenerMembDisp.php');
        
        if (!response.ok) {
            throw new Error('Error en la conexión');
        }
        
        const data = await response.json();
        
        if (!data.exito) {
            throw new Error(data.mensaje || 'Error al cargar membresías');
        }
        
        sessionStorage.setItem('membresiasDisponibles', JSON.stringify(data.membresias));
        return data.membresias; // Para usar con await
    } catch (error) {
        console.error("Error:", error);
        mostrarErrorAlUsuario("No se pudieron cargar las membresías. Intenta recargar la página.");
        return [];
    }
}


// ================ ACTUALIZACIÓN DE UI ================

function actualizarUIMembresia(membresia) {
    // Información básica
    document.getElementById('membership-name').textContent = membresia.nombre;
    document.getElementById('membership-start').textContent = membresia.fecha_inicio;
    document.getElementById('membership-end').textContent = membresia.fecha_fin;
    document.getElementById('next-payment').textContent = membresia.fecha_fin;
    document.getElementById('membership-days-left').textContent = `${membresia.dias_restantes} días restantes`;
    
    // Barra de progreso
    const porcentaje = 100 - (membresia.dias_restantes / membresia.duracion * 100);
    document.getElementById('membership-progress').style.width = `${porcentaje}%`;
    
    // Estado
    const estadoElement = document.getElementById('membership-status');
    estadoElement.textContent = membresia.estado;
    estadoElement.className = `status-badge text-white ${membresia.estado === 'activa' ? 'bg-success' : 'bg-warning'}`;
    
    // Beneficios
    const benefitsContainer = document.getElementById('benefits-container');
    benefitsContainer.innerHTML = '';
    
    if (membresia.beneficios && membresia.beneficios.length > 0) {
        membresia.beneficios.forEach(beneficio => {
            benefitsContainer.innerHTML += `
                <div class="d-flex align-items-start mb-3">
                    <div class="feature-icon bg-primary text-white">
                        <i class="fas fa-check"></i>
                    </div>
                    <div>
                        <h3 class="mb-0 text-xl">${beneficio}</h3>
                    </div>
                </div>
            `;
        });
    }
}

function actualizarUIHistorialPagos(pagos) {
    console.log('pagos', pagos);
    const tbody = document.getElementById('payments-body');
    tbody.innerHTML = '';

    if (!pagos || pagos.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="6" class="text-center py-4 text-muted">
                    <i class="fas fa-info-circle me-2"></i>
                    No se encontraron pagos registrados
                </td>
            </tr>
        `;
        return;
    }

    // Calcular total del mes actual
    let totalMes = 0;
    const mesActual = new Date().getMonth() + 1; // Los meses son 0-indexados, sumamos 1
    const añoActual = new Date().getFullYear();

    pagos.forEach(pago => {
        try {
            // Validación y conversión segura del monto
            const monto = typeof pago.monto === 'number' ? pago.monto : parseFloat(pago.monto) || 0;
            const montoFormateado = monto.toFixed(2);
            
            // Parsear fecha correctamente (formato "dd/mm/yyyy HH:mm")
            const [datePart, timePart] = pago.fecha_pago.split(' ');
            const [day, month, year] = datePart.split('/').map(Number);
            const [hours, minutes] = timePart.split(':').map(Number);
            
            // Crear objeto Date (los meses son 0-indexados en JavaScript)
            const fechaPago = new Date(year, month - 1, day, hours, minutes);
            
            // Formatear fecha para mostrar
            const fechaFormateada = fechaPago.toLocaleDateString('es-ES', {
                day: '2-digit',
                month: '2-digit',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });

            // Sumar al total si es del mes actual
            if (fechaPago.getMonth() + 1 === mesActual && fechaPago.getFullYear() === añoActual) {
                totalMes += monto;
            }

            // Crear fila de la tabla
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td>${fechaFormateada}</td>
                <td>${pago.concepto || 'Pago de membresía'}</td>
                <td><span class="badge bg-light text-dark">${pago.metodo_pago || 'N/A'}</span></td>
                <td>$${montoFormateado}</td>
                <td>
                    <span class="badge ${pago.estado_pago === 'completado' ? 'bg-success' : 'bg-warning'}">
                        ${pago.estado_pago || 'pendiente'}
                    </span>
                </td>
                <td>
                    <button class="btn btn-sm btn-outline-primary view-receipt" data-id="${pago.id_pago || ''}">
                        <i class="fas fa-receipt"></i>
                    </button>
                </td>
            `;
            tbody.appendChild(tr);

        } catch (error) {
            console.error('Error procesando pago:', pago, error);
        }
    });

    // Actualizar total del mes
    document.getElementById('current-month-payment').textContent = `$${totalMes.toFixed(2)}`;

    // Actualizar información del último pago (si existe)
    if (pagos.length > 0) {
        const ultimoPago = pagos[pagos.length - 1];
        const proximo_pago = parseFloat(ultimoPago?.monto || 0);
        const metodoUltimoPago = ultimoPago?.metodo_pago || 'N/A';
        document.getElementById('payment-method2').textContent = metodoUltimoPago;
        document.getElementById('next-payment-amount2').textContent = `$${proximo_pago.toFixed(2)}`;
    }
}
// ================ FUNCIONALIDADES DE PAGO ================

async function cargarFormularioPago() {
    const modalContent = document.getElementById('payment-modal-content');
    modalContent.innerHTML = `
        <div class="text-center py-3">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Cargando formulario...</span>
            </div>
        </div>
    `;

    try {
        const [metodosResponse, membresiasResponse] = await Promise.all([
            fetch('/GestiFit/src/usuarioPHP/pagos/obtenerMetodosPago.php'),
            fetch('/GestiFit/src/usuarioPHP/membresia/obtenerMembDisp.php')
        ]);

        if (!metodosResponse.ok || !membresiasResponse.ok) {
            throw new Error(`Error en la solicitud: ${metodosResponse.status} ${membresiasResponse.status}`);
        }

        const [metodosData, membresiasData] = await Promise.all([
            metodosResponse.json(),
            membresiasResponse.json()
        ]);

        console.log("Datos de métodos:", metodosData); // Para depuración

        // Validación adaptada a la nueva estructura
        if (!metodosData?.exito || !Array.isArray(metodosData.metodos_usuario)) {
            throw new Error('Estructura de métodos de pago inválida');
        }

        if (!membresiasData?.exito || !Array.isArray(membresiasData.membresias)) {
            throw new Error('Estructura de membresías inválida');
        }

        // Construcción del formulario con la estructura correcta
        modalContent.innerHTML = `
            <form id="payment-form">
                <div class="mb-3">
                    <label class="form-label">Membresía</label>
                    <select class="form-select" id="membresia-pago" required>
                        ${membresiasData.membresias.map(m => `
                            <option value="${m.id_membresia}" data-precio="${Number(m.precio) || 0}">
                                ${m.nombre} - $${(Number(m.precio) || 0).toFixed(2)} (${m.duracion_dias} días)
                            </option>
                        `).join('')}
                    </select>
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Método de Pago</label>
                    <select class="form-select" id="metodo-pago" required>
                        <option value="">Seleccione un método</option>
                        ${metodosData.metodos_usuario.map(m => `
                            <option value="${m.id_metodo}">
                                ${m.tipo} ${m.alias ? `(${m.alias})` : ''}
                            </option>
                        `).join('')}
                        <option value="nuevo">+ Agregar nuevo método</option>
                    </select>
                </div>
                
                <div id="nuevo-metodo-container" class="d-none">
                    <div class="mb-3">
                        <label class="form-label">Tipo</label>
                        <select class="form-select" id="nuevo-metodo-tipo">
                            ${metodosData.catalogo_tipos?.map(t => `
                                <option value="${t.id_tipo}">${t.descripcion}</option>
                            `).join('') || ''}
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Alias (opcional)</label>
                        <input type="text" class="form-control" id="nuevo-metodo-alias">
                    </div>
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Monto a Pagar (MXN)</label>
                    <input type="number" class="form-control" id="monto-pago" 
                           value="${membresiasData.membresias[0]?.precio || 0}" readonly>
                </div>
                
                <div class="d-grid gap-2 mt-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-credit-card me-2"></i> Realizar Pago
                    </button>
                </div>
            </form>
        `;

        // Configura eventos
        document.getElementById('metodo-pago').addEventListener('change', function() {
            document.getElementById('nuevo-metodo-container').classList.toggle('d-none', this.value !== 'nuevo');
        });
        
        document.getElementById('membresia-pago').addEventListener('change', function() {
            const precio = this.options[this.selectedIndex].dataset.precio;
            document.getElementById('monto-pago').value = precio;
        });
        
        document.getElementById('payment-form').addEventListener('submit', procesarPago);
        
    } catch (error) {
        console.error("Error:", error);
        modalContent.innerHTML = `
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-triangle me-2"></i>
                ${error.message || 'Error al cargar el formulario'}
                <button class="btn btn-sm btn-secondary mt-2" onclick="cargarFormularioPago()">
                    Reintentar
                </button>
            </div>
        `;
    }
}

async function procesarPago(event) {
    event.preventDefault();
    
    const form = event.target;
    const submitBtn = form.querySelector('button[type="submit"]');
    const originalBtnText = submitBtn.innerHTML;
    
    try {
        // Show loading state
        submitBtn.innerHTML = `
            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
            Procesando...
        `;
        submitBtn.disabled = true;
        
        // Validate form
        const idMembresia = document.getElementById('membresia-pago').value;
        const monto = document.getElementById('monto-pago').value;
        const metodoPago = document.getElementById('metodo-pago').value;
        
        if (!idMembresia || !monto || !metodoPago) {
            throw new Error('Complete todos los campos requeridos');
        }
        
        // Prepare form data
        const formData = new FormData();
        formData.append('id_membresia', idMembresia);
        formData.append('monto', monto);
        
        if (metodoPago === 'nuevo') {
            const tipo = document.getElementById('nuevo-metodo-tipo').value;
            if (!tipo) throw new Error('Seleccione un tipo de método de pago');
            
            formData.append('nuevo_metodo', '1');
            formData.append('tipo_metodo', tipo);
            formData.append('alias_metodo', document.getElementById('nuevo-metodo-alias').value || 'Nuevo método');
        } else {
            formData.append('id_metodo_pago', metodoPago);
        }
        // MOSTRAR TODO LO QUE SE VA A ENVIAR
for (const [key, value] of formData.entries()) {
    console.log(`${key}:`, value);
}

        // Send request
        const response = await fetch('/GestiFit/src/usuarioPHP/pagos/procesarPago.php', {
            method: 'POST',
            body: formData
        });
        
        // Handle response
        if (!response.ok) {
            const errorText = await response.text();
            throw new Error(`Error del servidor (${response.status}): ${errorText.substring(0, 100)}`);
        }

        const data = await response.json();
        
        if (!data.exito) {
            throw new Error(data.mensaje || 'Error al procesar el pago');
        }
        
        // Success
        $('#paymentModal').modal('hide');
        mostrarAlerta('success', 'Pago completado. Referencia: ' + data.referencia);
        
        // Refresh data
        await cargarDatosMembresia();
        await cargarHistorialPagos();
        
    } catch (error) {
        console.error("Error en procesarPago:", error);
        mostrarAlerta('danger', error.message.includes('<!DOCTYPE') ? 
            'Error interno del servidor' : error.message);
    } finally {
        submitBtn.innerHTML = originalBtnText;
        submitBtn.disabled = false;
    }
}
// ================ FUNCIONALIDADES DE MEMBRESÍAS ================

async function mostrarModalMembresias() {
    const modalContent = document.getElementById('upgrade-modal-content');
    modalContent.innerHTML = `
        <div class="text-center py-3">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Cargando opciones...</span>
            </div>
        </div>
    `;
    
    $('#upgradeModal').modal('show');
    
    try {
        const response = await fetch('/GestiFit/backend/membresias/get_disponibles.php');
        const data = await response.json();
        
        if (!data.exito) {
            throw new Error(data.mensaje || 'Error al cargar membresías');
        }
        
        modalContent.innerHTML = `
            <h4 class="mb-4">Selecciona una membresía</h4>
            <div class="row">
                ${data.membresias.map(membresia => `
                    <div class="col-md-6 mb-4">
                        <div class="card membership-option ${membresia.tipo === 'premium' ? 'border-primary border-2' : ''}">
                            <div class="card-header bg-light">
                                <h5 class="mb-0">${membresia.nombre}</h5>
                                <span class="badge bg-${membresia.tipo === 'premium' ? 'primary' : 'secondary'}">
                                    ${membresia.tipo}
                                </span>
                            </div>
                            <div class="card-body">
                                <h3 class="card-title">$${membresia.precio.toFixed(2)}</h3>
                                <small class="text-muted">${membresia.duracion_dias} días de acceso</small>
                                
                                <ul class="list-unstyled mt-3">
                                    ${membresia.beneficios.map(b => `<li><i class="fas fa-check text-success me-2"></i>${b}</li>`).join('')}
                                </ul>
                                
                                <button type="button" class="btn btn-primary w-100 upgrade-btn" 
                                        data-id="${membresia.id_membresia}">
                                    Seleccionar
                                </button>
                            </div>
                        </div>
                    </div>
                `).join('')}
            </div>
        `;
        
        // Eventos para los botones de selección
        document.querySelectorAll('.upgrade-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                const membresiaId = btn.dataset.id;
                const membresia = data.membresias.find(m => m.id_membresia == membresiaId);
                
                if (confirm(`¿Deseas actualizar a la membresía ${membresia.nombre} por $${membresia.precio.toFixed(2)}?`)) {
                    actualizarMembresia(membresiaId);
                }
            });
        });
        
    } catch (error) {
        modalContent.innerHTML = `
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-triangle me-2"></i>
                ${error.message || 'Error al cargar opciones'}
            </div>
        `;
    }
}

async function actualizarMembresia(membresiaId) {
    try {
        const response = await fetch('/GestiFit/src/usuarioPHP/actualizar_membresia.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `id_membresia=${membresiaId}&id_usuario=1` // ID usuario debería venir de sesión
        });
        
        const data = await response.json();
        
        if (!data.exito) {
            throw new Error(data.mensaje || 'Error al actualizar');
        }
        
        $('#upgradeModal').modal('hide');
        mostrarAlerta('success', 'Membresía actualizada con éxito');
        
        // Recargar datos
        await Promise.all([
            cargarDatosMembresia(),
            cargarHistorialPagos()
        ]);
        
    } catch (error) {
        console.error("Error:", error);
        mostrarAlerta('danger', error.message || 'Error al actualizar membresía');
    }
}

// ================ FUNCIONES AUXILIARES ================

function mostrarError(elementId, mensaje) {
    const element = document.getElementById(elementId);
    if (element) {
        element.innerHTML = `
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-triangle me-2"></i>
                ${mensaje}
            </div>
        `;
    }
}

function mostrarAlerta(tipo, mensaje) {
    const alerta = document.createElement('div');
    alerta.className = `alert alert-${tipo} alert-dismissible fade show fixed-top mx-auto mt-3`;
    alerta.style.maxWidth = '500px';
    alerta.style.zIndex = '1060';
    alerta.role = 'alert';
    alerta.innerHTML = `
        ${mensaje}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    `;
    
    document.body.appendChild(alerta);
    
    // Auto-ocultar después de 5 segundos
    setTimeout(() => {
        const bsAlert = new bootstrap.Alert(alerta);
        bsAlert.close();
    }, 5000);
}

// Función para mostrar el modal de pago
function mostrarModalPago() {
    // Mostrar el modal usando Bootstrap
    const paymentModal = new bootstrap.Modal(document.getElementById('paymentModal'));
    paymentModal.show();
    
    // Cargar el formulario de pago (ya lo tenemos implementado)
    cargarFormularioPago();
}

function formatFecha(fechaString) {
    const opciones = { year: 'numeric', month: 'short', day: 'numeric' };
    return new Date(fechaString).toLocaleDateString('es-ES', opciones);
}