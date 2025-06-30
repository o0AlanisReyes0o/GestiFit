
-- Crear base de datos
DROP DATABASE IF EXISTS gestifitbd;
CREATE DATABASE gestifitbd CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE gestifitbd;

-- Tabla Usuario
CREATE TABLE Usuario (
    idUsuario INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL,
    apellidoPaterno VARCHAR(50) NOT NULL,
    apellidoMaterno VARCHAR(50),
    edad INT,
    tipo ENUM('administrador', 'cliente', 'instructor') NOT NULL,
    turno ENUM('Matutino', 'Vespertino', 'Nocturno') NULL,
    usuario VARCHAR(50) UNIQUE NOT NULL,
    contrasena VARCHAR(255) NULL,
    direccion TEXT,
    email VARCHAR(100) UNIQUE,
    telefono VARCHAR(20),
    fechaRegistro DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Tabla Membresia
CREATE TABLE Membresia (
    idMembresia INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL,
    costo DECIMAL(8,2) NOT NULL,
    duracionMeses INT
) ENGINE=InnoDB;

-- Tabla UsuarioMembresia
CREATE TABLE UsuarioMembresia (
    idUsuario INT,
    idMembresia INT,
    fechaInicio DATE DEFAULT CURRENT_TIMESTAMP,
    fechaFin DATE,
    PRIMARY KEY (idUsuario, idMembresia),
    FOREIGN KEY (idUsuario) REFERENCES Usuario(idUsuario) ON DELETE CASCADE,
    FOREIGN KEY (idMembresia) REFERENCES Membresia(idMembresia) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Tabla catalogo_metodos_pago
CREATE TABLE catalogo_metodos_pago (
    id_tipo INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL UNIQUE,
    descripcion TEXT
) ENGINE=InnoDB;

-- Tabla metodos_pago
CREATE TABLE metodos_pago (
    id_metodo INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT NOT NULL,
    id_tipo INT NOT NULL,
    alias VARCHAR(50),
    ultimos_digitos VARCHAR(4),
    fecha_creacion DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    activo BOOLEAN NOT NULL DEFAULT TRUE,
    FOREIGN KEY (id_usuario) REFERENCES Usuario(idUsuario) ON DELETE CASCADE,
    FOREIGN KEY (id_tipo) REFERENCES catalogo_metodos_pago(id_tipo) ON DELETE RESTRICT
) ENGINE=InnoDB;

-- Tabla pagos
CREATE TABLE pagos (
    id_pago INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT NOT NULL,
    id_metodo_pago INT,
    id_membresia INT,
    fecha_pago DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    monto DECIMAL(10,2) NOT NULL,
    estado_pago ENUM('pendiente', 'completado', 'fallido', 'reembolsado') NOT NULL DEFAULT 'pendiente',
    referencia_pago VARCHAR(100),
    FOREIGN KEY (id_usuario) REFERENCES Usuario(idUsuario),
    FOREIGN KEY (id_metodo_pago) REFERENCES metodos_pago(id_metodo),
    FOREIGN KEY (id_membresia) REFERENCES Membresia(idMembresia)
) ENGINE=InnoDB;

-- Tabla clases_grupales
CREATE TABLE clases_grupales (
    id_clase INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    id_instructor INT NOT NULL,
    descripcion TEXT,
    hora_inicio TIME NOT NULL,
    hora_fin TIME NOT NULL,
    cupo_maximo INT NOT NULL,
    lugar VARCHAR(100) NOT NULL,
    estado ENUM('disponible', 'llena', 'vencida') NOT NULL DEFAULT 'disponible',
    dificultad ENUM('principiante', 'intermedio', 'avanzado'),
    requisitos TEXT,
    FOREIGN KEY (id_instructor) REFERENCES Usuario(idUsuario)
) ENGINE=InnoDB;

-- Tabla ClaseDias
CREATE TABLE ClaseDias (
    id INT AUTO_INCREMENT PRIMARY KEY,
    idClase INT NOT NULL,
    dia ENUM('Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado') NOT NULL,
    FOREIGN KEY (idClase) REFERENCES clases_grupales(id_clase) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Tabla reservas_clases
CREATE TABLE reservas_clases (
    id_reserva INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT NOT NULL,
    id_clase INT NOT NULL,
    dia VARCHAR(20),
    fecha_reserva DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    asistio BOOLEAN DEFAULT FALSE,
    calificacion INT CHECK (calificacion BETWEEN 1 AND 5),
    comentarios TEXT,
    FOREIGN KEY (id_usuario) REFERENCES Usuario(idUsuario),
    FOREIGN KEY (id_clase) REFERENCES clases_grupales(id_clase)
) ENGINE=InnoDB;



-- Adaptación del script de inserts original a la base real 'gestifitbd'

-- Métodos de pago
INSERT INTO catalogo_metodos_pago (id_tipo, nombre, descripcion) VALUES
(1, 'tarjeta', 'Tarjetas de débito o crédito'),
(2, 'efectivo', 'Pago directo en recepción'),
(3, 'transferencia', 'Transferencia bancaria'),
(4, 'wallet', 'Pagos digitales como PayPal o MercadoPago');

-- Usuarios con contraseñas bcrypt (clave: 12345678)
INSERT INTO Usuario (nombre, apellidoPaterno, apellidoMaterno, edad, tipo, turno, usuario, contrasena, direccion, email, telefono) VALUES
('Carlos', 'López', 'Martínez', 35, 'administrador', 'Matutino', 'admin1', '$2b$12$CWF7Z/VN4wK9zH1WwGwhzeQEKyWgIXLrKFT7P5HubxLLuTQ2fkHuu', 'Calle Admin #1', 'admin1@correo.com', '555000001'),
('María', 'Gómez', NULL, 38, 'instructor', NULL, 'instructora1', '$2b$12$CWF7Z/VN4wK9zH1WwGwhzeQEKyWgIXLrKFT7P5HubxLLuTQ2fkHuu', 'Av. Real 456', 'maria@example.com', '5557654321'),
('Juan', 'Pérez', NULL, 32, 'cliente', NULL, 'cliente1', '$2b$12$CWF7Z/VN4wK9zH1WwGwhzeQEKyWgIXLrKFT7P5HubxLLuTQ2fkHuu', 'Calle Falsa 123', 'juan@example.com', '5551234567');

-- Membresías
INSERT INTO Membresia (idMembresia, nombre, costo, duracionMeses) VALUES
(1, 'Básica', 299.00, 1),
(2, 'Premium', 499.00, 3);

-- Métodos de pago del usuario
INSERT INTO metodos_pago (id_metodo, id_usuario, id_tipo, alias, ultimos_digitos) VALUES
(1, 3, 1, 'Visa terminada en 1234', '1234'),
(2, 3, 2, 'Pago en caja', NULL);

-- Pagos
INSERT INTO pagos (id_pago, id_usuario, id_metodo_pago, id_membresia, monto, estado_pago) VALUES
(1, 3, 1, 1, 299.00, 'completado'),
(2, 3, 2, 2, 499.00, 'pendiente');

-- Membresía activa
INSERT INTO UsuarioMembresia (idUsuario, idMembresia, fechaInicio, fechaFin) VALUES
(3, 1, CURDATE(), DATE_ADD(CURDATE(), INTERVAL 30 DAY));

-- Clases
INSERT INTO clases_grupales (id_clase, nombre, id_instructor, descripcion, hora_inicio, hora_fin, cupo_maximo, lugar, estado, dificultad, requisitos) VALUES
(1, 'Yoga', 2, 'Clase de Yoga relajante', '08:00:00', '09:00:00', 15, 'Sala 1', 'disponible', 'principiante', 'Llevar tapete de yoga'),
(2, 'Box', 2, 'Clase de boxeo', '10:00:00', '11:00:00', 20, 'Sala 2', 'disponible', 'avanzado', 'Guantes, toalla'),
(3, 'Spinning', 2, 'Clase de spinning en bicicleta', '17:00:00', '18:00:00', 25, 'Sala 3', 'disponible', 'intermedio', 'Ropa cómoda'),
(4, 'Crossfit', 2, 'Clase intensa de Crossfit', '19:00:00', '20:00:00', 10, 'Sala 4', 'disponible', 'avanzado', 'Experiencia previa'),
(5, 'Pilates', 2, 'Pilates para todos los niveles', '06:00:00', '07:00:00', 12, 'Sala 5', 'disponible', 'principiante', 'Ropa cómoda');

-- Días por clase
INSERT INTO ClaseDias (idClase, dia) VALUES
(1, 'Lunes'), (1, 'Miércoles'), (1, 'Viernes'),
(2, 'Martes'), (2, 'Jueves'),
(3, 'Lunes'), (3, 'Miércoles'), (3, 'Viernes'),
(4, 'Martes'), (4, 'Jueves'),
(5, 'Sábado');
