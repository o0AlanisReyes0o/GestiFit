-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 29-06-2025 a las 05:32:48
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `gestifitbd`
--

-- Crear base de datos
DROP DATABASE IF EXISTS gestifitbd;
CREATE DATABASE gestifitbd CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE gestifitbd;
-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `catalogo_metodos_pago`
--

CREATE TABLE `catalogo_metodos_pago` (
  `id_tipo` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `descripcion` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `catalogo_metodos_pago`
--

INSERT INTO `catalogo_metodos_pago` (`id_tipo`, `nombre`, `descripcion`) VALUES
(1, 'tarjeta', 'Tarjetas de débito o crédito'),
(2, 'efectivo', 'Pago directo en recepción'),
(3, 'transferencia', 'Transferencia bancaria'),
(4, 'wallet', 'Pagos digitales como PayPal o MercadoPago');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `clasedias`
--

CREATE TABLE `clasedias` (
  `id` int(11) NOT NULL,
  `idClase` int(11) NOT NULL,
  `dia` enum('Lunes','Martes','Miércoles','Jueves','Viernes','Sábado') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `clasedias`
--

INSERT INTO `clasedias` (`id`, `idClase`, `dia`) VALUES
(1, 1, 'Lunes'),
(2, 1, 'Miércoles'),
(3, 1, 'Viernes'),
(4, 2, 'Martes'),
(5, 2, 'Jueves'),
(6, 3, 'Lunes'),
(7, 3, 'Miércoles'),
(8, 3, 'Viernes'),
(9, 4, 'Martes'),
(10, 4, 'Jueves'),
(11, 5, 'Sábado');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `clases_grupales`
--

CREATE TABLE `clases_grupales` (
  `id_clase` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `id_instructor` int(11) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `hora_inicio` time NOT NULL,
  `hora_fin` time NOT NULL,
  `cupo_maximo` int(11) NOT NULL,
  `lugar` varchar(100) NOT NULL,
  `estado` enum('disponible','llena','vencida') NOT NULL DEFAULT 'disponible',
  `dificultad` enum('principiante','intermedio','avanzado') DEFAULT NULL,
  `requisitos` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `clases_grupales`
--

INSERT INTO `clases_grupales` (`id_clase`, `nombre`, `id_instructor`, `descripcion`, `hora_inicio`, `hora_fin`, `cupo_maximo`, `lugar`, `estado`, `dificultad`, `requisitos`) VALUES
(1, 'Yoga', 2, 'Clase de Yoga relajante', '08:00:00', '09:00:00', 15, 'Sala 1', 'disponible', 'principiante', 'Llevar tapete de yoga'),
(2, 'Box', 2, 'Clase de boxeo', '10:00:00', '11:00:00', 20, 'Sala 2', 'disponible', 'avanzado', 'Guantes, toalla'),
(3, 'Spinning', 2, 'Clase de spinning en bicicleta', '17:00:00', '18:00:00', 25, 'Sala 3', 'disponible', 'intermedio', 'Ropa cómoda'),
(4, 'Crossfit', 2, 'Clase intensa de Crossfit', '19:00:00', '20:00:00', 10, 'Sala 4', 'disponible', 'avanzado', 'Experiencia previa'),
(5, 'Pilates', 2, 'Pilates para todos los niveles', '06:00:00', '07:00:00', 12, 'Sala 5', 'disponible', 'principiante', 'Ropa cómoda');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `membresia`
--

CREATE TABLE `membresia` (
  `idMembresia` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `costo` decimal(8,2) NOT NULL,
  `duracionMeses` int(11) DEFAULT NULL,
  `descripcion` TEXT, 
  `beneficios` TEXT -- Colocar separados por comas
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


--
-- Volcado de datos para la tabla `membresia`
--

INSERT INTO `membresia` (`idMembresia`, `nombre`, `costo`, `duracionMeses`, `descripcion`, `beneficios`) VALUES
(1, 'Básica', 299.00, 1, 'Para empezar en tu incursion al gimnasio', 'Clases grupales, Acceso ilimitado'),
(2, 'Premium', 499.00, 3, 'Para los pros de pros', 'Clases grupales, Acceso ilimitado, Spa proximamente');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `metodos_pago`
--

CREATE TABLE `metodos_pago` (
  `id_metodo` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `id_tipo` int(11) NOT NULL,
  `alias` varchar(50) DEFAULT NULL,
  `ultimos_digitos` varchar(4) DEFAULT NULL,
  `fecha_creacion` datetime NOT NULL DEFAULT current_timestamp(),
  `activo` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `metodos_pago`
--

INSERT INTO `metodos_pago` (`id_metodo`, `id_usuario`, `id_tipo`, `alias`, `ultimos_digitos`, `fecha_creacion`, `activo`) VALUES
(1, 3, 1, 'Visa terminada en 1234', '1234', '2025-06-28 19:09:48', 1),
(2, 3, 2, 'Pago en caja', NULL, '2025-06-28 19:09:48', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pagos`
--

CREATE TABLE `pagos` (
  `id_pago` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `id_metodo_pago` int(11) DEFAULT NULL,
  `id_membresia` int(11) DEFAULT NULL,
  `fecha_pago` datetime NOT NULL DEFAULT current_timestamp(),
  `monto` decimal(10,2) NOT NULL,
  `estado_pago` enum('pendiente','completado','fallido','reembolsado') NOT NULL DEFAULT 'pendiente',
  `referencia_pago` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `pagos`
--

INSERT INTO `pagos` (`id_pago`, `id_usuario`, `id_metodo_pago`, `id_membresia`, `fecha_pago`, `monto`, `estado_pago`, `referencia_pago`) VALUES
(1, 3, 1, 1, '2025-06-28 19:09:48', 299.00, 'completado', NULL),
(2, 3, 2, 2, '2025-06-28 19:09:48', 499.00, 'pendiente', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `reservas_clases`
--

CREATE TABLE `reservas_clases` (
  `id_reserva` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `id_clase` int(11) NOT NULL,
  `dia` varchar(20) DEFAULT NULL,
  `fecha_reserva` datetime NOT NULL DEFAULT current_timestamp(),
  `asistio` tinyint(1) DEFAULT 0,
  `calificacion` int(11) DEFAULT NULL CHECK (`calificacion` between 1 and 5),
  `comentarios` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


--
-- Estructura de tabla para la tabla `usuario`
--

CREATE TABLE `usuario` (
  `idUsuario` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `apellidoPaterno` varchar(50) NOT NULL,
  `apellidoMaterno` varchar(50) DEFAULT NULL,
  `edad` int(11) DEFAULT NULL,
  `tipo` enum('administrador','cliente','instructor') NOT NULL,
  `turno` enum('Matutino','Vespertino','Nocturno') DEFAULT NULL,
  `usuario` varchar(50) NOT NULL,
  `contrasena` varchar(255) DEFAULT NULL,
  `direccion` text DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `fechaRegistro` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `usuario`
--

INSERT INTO `usuario` (`idUsuario`, `nombre`, `apellidoPaterno`, `apellidoMaterno`, `edad`, `tipo`, `turno`, `usuario`, `contrasena`, `direccion`, `email`, `telefono`, `fechaRegistro`) VALUES
(1, 'Carlos', 'López', 'Martínez', 35, 'administrador', 'Matutino', 'admin1', '$2b$12$CWF7Z/VN4wK9zH1WwGwhzeQEKyWgIXLrKFT7P5HubxLLuTQ2fkHuu', 'Calle Admin #1', 'admin1@correo.com', '555000001', '2025-06-28 19:09:48'),
(2, 'María', 'Gómez', NULL, 38, 'instructor', NULL, 'instructora1', '$2b$12$CWF7Z/VN4wK9zH1WwGwhzeQEKyWgIXLrKFT7P5HubxLLuTQ2fkHuu', 'Av. Real 456', 'maria@example.com', '5557654321', '2025-06-28 19:09:48'),
(3, 'Juan', 'Pérez', NULL, 32, 'cliente', NULL, 'cliente1', '$2b$12$CWF7Z/VN4wK9zH1WwGwhzeQEKyWgIXLrKFT7P5HubxLLuTQ2fkHuu', 'Calle Falsa 123', 'juan@example.com', '5551234567', '2025-06-28 19:09:48'),
(4, 'Diego', 'Martinez', 'Ruiz', 21, 'cliente', NULL, 'Diego', '$2y$10$z.Zcl2Gc1syQcVG5nHMNyOggsQ86sA6r7yCqLGhwhOetKXhhbJcFO', 'es una prueba #6000', 'ayfjkky575009@gmail.com', '+525537966490', '2025-06-28 19:34:52'),
(6, 'Francisco', 'Martinez', 'Olarte', 21, 'cliente', NULL, 'Francisco', '$2y$10$2uu3UbZNxqk1I8EGP.yzIufpV61fm8QQ20fr.PeXmSco8gqISXJk6', 'es una prueba #6000', 'franolarte575009@gmail.com', '+525537966490', '2025-06-28 19:37:12'),
(9, 'Diego', 'Martinez', 'Martinez', 21, 'administrador', NULL, '01Diego', '$2y$10$JjZWnjco3Dnq1Z65CWD92.aQqUIDbMTfD4QlCqWRMWfca7u1QvMz.', 'es una prueba #6', 'ayfy575009@gmail.com', '+525537966490', '2025-06-28 19:47:13'),
(10, 'Viviana', 'Carrera', 'Lopez', 23, 'administrador', NULL, '03Vivia', '$2y$10$5wzG6kPiAkr9eKPIfnAkyOUGJ7H.PXwPMmL3qnUtEP6Htt6QlsBa6', 'es una prueba #675', 'vivilopez575009@gmail.com', '+525537966476', '2025-06-28 19:51:58');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuariomembresia`
--

CREATE TABLE `usuariomembresia` (
  `idUsuario` int(11) NOT NULL,
  `idMembresia` int(11) NOT NULL,
  `fechaInicio` date DEFAULT current_timestamp(),
  `fechaFin` date DEFAULT NULL,
  `estado` enum('activa', 'inactiva','cancelada')
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `usuariomembresia`
--

INSERT INTO `usuariomembresia` (`idUsuario`, `idMembresia`, `fechaInicio`, `fechaFin`, `estado`) VALUES
(3, 1, '2025-06-28', '2025-07-28', 'activa');
CREATE TABLE rutinas (
    id_rutina INT AUTO_INCREMENT PRIMARY KEY,
    nombre_rutina VARCHAR(100) NOT NULL,
    nivel_rutina ENUM('Principiante', 'Intermedio', 'Avanzado') NOT NULL,
    descripcion TEXT NOT NULL,
    duracion_semanas INT NOT NULL,
    dias_por_semana INT NOT NULL,
    objetivo ENUM('Perdida de peso', 'Ganancia muscular', 'Fuerza', 'Resistencia', 'Tonificación') NOT NULL,
    equipamiento_necesario TEXT, -- Separar por comas
    instrucciones TEXT NOT NULL,
    video_url VARCHAR(255),
    imagen_url VARCHAR(255),
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    activa BOOLEAN DEFAULT TRUE
);

INSERT INTO rutinas (
    nombre_rutina,
    nivel_rutina,
    descripcion,
    duracion_semanas,
    dias_por_semana,
    objetivo,
    equipamiento_necesario,
    instrucciones,
    video_url,
    imagen_url
) VALUES 
(
    'Rutina Full Body para Principiantes',
    'Principiante',
    'Entrenamiento general para todo el cuerpo. Ideal para quienes inician en el gimnasio.',
    4,
    3,
    'Tonificación',
    'Mancuernas ligeras, colchoneta',
    'Calentar 5 minutos. Realizar 3 circuitos de: 10 sentadillas, 10 flexiones de brazo apoyando rodillas, 15 abdominales, 10 remo con mancuernas. Descansar 60 segundos entre circuitos.',
    'https://www.youtube.com/watch?v=6Rw4kV_BFrY',
    'https://www.labolsadelcorredor.com/wp-content/uploads/2018/02/Anllela-Sagra-fitness.jpg'
),
(
    'Hipertrofia Torso-Pierna Intermedia',
    'Intermedio',
    'Programa de ganancia muscular divido en torso y pierna, ideal para usuarios intermedios.',
    8,
    5,
    'Ganancia muscular',
    'Barras, mancuernas, polea alta, banco inclinado',
    'Día 1: Torso - press banca, dominadas, remo con barra. Día 2: Pierna - sentadilla, peso muerto rumano, prensa. Alternar días. 3-4 series por ejercicio, 8-12 repeticiones.',
    'https://www.youtube.com/watch?v=LBnHgognZfk',
    'https://mundoentrenamiento.com/wp-content/uploads/2024/01/Rutina-torso-pierna-1-768x553.jpg'
),
(
    'Fuerza 5x5 para Avanzados',
    'Avanzado',
    'Enfocado en incremento de fuerza en ejercicios compuestos usando el método 5x5.',
    12,
    4,
    'Fuerza',
    'Rack de sentadillas, barra olímpica, discos, banco',
    'Lunes: sentadilla 5x5, press banca 5x5, remo 5x5. Miércoles: sentadilla ligera 5x5, press militar 5x5, peso muerto 1x5. Viernes: repetir lunes. Progresar peso semanalmente.',
    'https://www.youtube.com/watch?v=OMUVKii-gNI',
    'https://hips.hearstapps.com/hmg-prod/images/5x5-workout-1-6501c9ff943e8.jpg?resize=980:*'
),
(
    'Cardio y Quema de Grasa en Casa',
    'Principiante',
    'Entrenamiento enfocado en la pérdida de peso sin necesidad de gimnasio.',
    6,
    4,
    'Perdida de peso',
    'Ninguno',
    'Circuito HIIT: 30 segundos de jumping jacks, 30 segundos de mountain climbers, 30 segundos de burpees. Repetir el circuito 4 veces con 1 minuto de descanso entre rondas.',
    'https://www.youtube.com/watch?v=dJIu5LgJ2ow',
    'https://laopinion.com/wp-content/uploads/sites/3/2019/09/gesina-kunkel-zqchszxx9g4-unsplash.jpg?resize=480,270&quality=80'
),
(
    'Resistencia Muscular Avanzada',
    'Avanzado',
    'Programa para mejorar la capacidad de mantener esfuerzo muscular prolongado.',
    10,
    5,
    'Resistencia',
    'Bandas elásticas, mancuernas, bicicleta estática',
    'Ejercicios con tiempos prolongados: sentadilla isométrica 1 min, push-ups al fallo, plancha 1 min. Alternar grupos musculares por día. Enfocado en resistencia más que carga.',
    'https://www.youtube.com/watch?v=0TWJSkEuc2Y',
    'https://marathonhandbook.com/wp-content/uploads/2022/09/Muscular-Endurance-10.jpg'
);
CREATE TABLE configuraciones (
    id_config INT AUTO_INCREMENT PRIMARY KEY,
    clave VARCHAR(100) NOT NULL UNIQUE,
    valor TEXT NOT NULL,
    descripcion TEXT,
    editable BOOLEAN NOT NULL DEFAULT TRUE
);



INSERT INTO configuraciones (clave, valor, descripcion) VALUES
('aviso_mantenimiento', 'El gimnasio permanecerá cerrado por mantenimiento el próximo domingo de 8:00 a.m. a 2:00 p.m.', 'Mensaje informativo visible en el dashboard para todos los usuarios'),
('aviso_evento_especial', 'Este viernes tendremos una clase especial de yoga con música en vivo a las 6:00 p.m. ¡Cupo limitado!', 'Anuncio de eventos o clases especiales para incentivar la participación'),
('aviso_nuevas_clases', 'Nuevas clases de spinning y box ya están disponibles en el horario matutino. Reserva tu lugar.', 'Anuncio para destacar nuevas actividades disponibles'),
('aviso_vencimiento_membresia', 'Tu membresía está próxima a vencer. Renueva antes del día límite para evitar interrupciones.', 'Mensaje dinámico que puede mostrarse según la situación del usuario (requiere lógica extra)'),
('aviso_promocion', '¡Promoción especial! Renueva tu membresía anual con 15% de descuento solo durante esta semana.', 'Promociones activas visibles en la sección de pagos o membresías'),
('aviso_cierre_feriado', 'El gimnasio estará cerrado el 16 de septiembre por motivo de día festivo nacional.', 'Notificación de cierre por días feriados');


--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `catalogo_metodos_pago`
--
ALTER TABLE `catalogo_metodos_pago`
  ADD PRIMARY KEY (`id_tipo`),
  ADD UNIQUE KEY `nombre` (`nombre`);

--
-- Indices de la tabla `clasedias`
--
ALTER TABLE `clasedias`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idClase` (`idClase`);

--
-- Indices de la tabla `clases_grupales`
--
ALTER TABLE `clases_grupales`
  ADD PRIMARY KEY (`id_clase`),
  ADD KEY `id_instructor` (`id_instructor`);

--
-- Indices de la tabla `membresia`
--
ALTER TABLE `membresia`
  ADD PRIMARY KEY (`idMembresia`);

--
-- Indices de la tabla `metodos_pago`
--
ALTER TABLE `metodos_pago`
  ADD PRIMARY KEY (`id_metodo`),
  ADD KEY `id_usuario` (`id_usuario`),
  ADD KEY `id_tipo` (`id_tipo`);

--
-- Indices de la tabla `pagos`
--
ALTER TABLE `pagos`
  ADD PRIMARY KEY (`id_pago`),
  ADD KEY `id_usuario` (`id_usuario`),
  ADD KEY `id_metodo_pago` (`id_metodo_pago`),
  ADD KEY `id_membresia` (`id_membresia`);

--
-- Indices de la tabla `reservas_clases`
--
ALTER TABLE `reservas_clases`
  ADD PRIMARY KEY (`id_reserva`),
  ADD KEY `id_usuario` (`id_usuario`),
  ADD KEY `id_clase` (`id_clase`);

--
-- Indices de la tabla `usuario`
--
ALTER TABLE `usuario`
  ADD PRIMARY KEY (`idUsuario`),
  ADD UNIQUE KEY `usuario` (`usuario`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indices de la tabla `usuariomembresia`
--
ALTER TABLE `usuariomembresia`
  ADD PRIMARY KEY (`idUsuario`,`idMembresia`),
  ADD KEY `idMembresia` (`idMembresia`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `catalogo_metodos_pago`
--
ALTER TABLE `catalogo_metodos_pago`
  MODIFY `id_tipo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `clasedias`
--
ALTER TABLE `clasedias`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT de la tabla `clases_grupales`
--
ALTER TABLE `clases_grupales`
  MODIFY `id_clase` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `membresia`
--
ALTER TABLE `membresia`
  MODIFY `idMembresia` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `metodos_pago`
--
ALTER TABLE `metodos_pago`
  MODIFY `id_metodo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `pagos`
--
ALTER TABLE `pagos`
  MODIFY `id_pago` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `reservas_clases`
--
ALTER TABLE `reservas_clases`
  MODIFY `id_reserva` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `usuario`
--
ALTER TABLE `usuario`
  MODIFY `idUsuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `clasedias`
--
ALTER TABLE `clasedias`
  ADD CONSTRAINT `clasedias_ibfk_1` FOREIGN KEY (`idClase`) REFERENCES `clases_grupales` (`id_clase`) ON DELETE CASCADE;

--
-- Filtros para la tabla `clases_grupales`
--
ALTER TABLE `clases_grupales`
  ADD CONSTRAINT `clases_grupales_ibfk_1` FOREIGN KEY (`id_instructor`) REFERENCES `usuario` (`idUsuario`) ON DELETE CASCADE;

--
-- Filtros para la tabla `metodos_pago`
--
ALTER TABLE `metodos_pago`
  ADD CONSTRAINT `metodos_pago_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`idUsuario`) ON DELETE CASCADE,
  ADD CONSTRAINT `metodos_pago_ibfk_2` FOREIGN KEY (`id_tipo`) REFERENCES `catalogo_metodos_pago` (`id_tipo`) ON DELETE CASCADE;

--
-- Filtros para la tabla `pagos`
--
ALTER TABLE `pagos`
  ADD CONSTRAINT `pagos_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`idUsuario`) ON DELETE CASCADE,
  ADD CONSTRAINT `pagos_ibfk_2` FOREIGN KEY (`id_metodo_pago`) REFERENCES `metodos_pago` (`id_metodo`) ON DELETE CASCADE,
  ADD CONSTRAINT `pagos_ibfk_3` FOREIGN KEY (`id_membresia`) REFERENCES `membresia` (`idMembresia`) ON DELETE CASCADE;

--
-- Filtros para la tabla `reservas_clases`
--
ALTER TABLE `reservas_clases`
  ADD CONSTRAINT `reservas_clases_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`idUsuario`),
  ADD CONSTRAINT `reservas_clases_ibfk_2` FOREIGN KEY (`id_clase`) REFERENCES `clases_grupales` (`id_clase`) ON DELETE CASCADE;

--
-- Filtros para la tabla `usuariomembresia`
--
ALTER TABLE `usuariomembresia`
  ADD CONSTRAINT `usuariomembresia_ibfk_1` FOREIGN KEY (`idUsuario`) REFERENCES `usuario` (`idUsuario`) ON DELETE CASCADE,
  ADD CONSTRAINT `usuariomembresia_ibfk_2` FOREIGN KEY (`idMembresia`) REFERENCES `membresia` (`idMembresia`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
