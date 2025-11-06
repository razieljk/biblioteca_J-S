-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 01-11-2025 a las 03:19:27
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
-- Base de datos: `biblioteca_js`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `libro`
--

CREATE TABLE `libro` (
  `id` int(11) NOT NULL,
  `titulo` varchar(100) NOT NULL,
  `autor` varchar(100) NOT NULL,
  `isbm` varchar(100) NOT NULL,
  `categoria` varchar(100) NOT NULL,
  `disponibilidad` varchar(50) NOT NULL,
  `cantidad` int(11) NOT NULL,
  `descripcion` varchar(100) NOT NULL,
  `imagen` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `libro`
--

INSERT INTO `libro` (`id`, `titulo`, `autor`, `isbm`, `categoria`, `disponibilidad`, `cantidad`, `descripcion`, `imagen`) VALUES
(5, '100 años de soledad', 'gabriel garcia marquez', '193845729504', 'ciencia ficion', 'disponible', 1, 'libro de aventuras magicas', 'img-portadas/100 anios de soledad.png'),
(6, 'don quijote de la mancha', 'miguel de cervantes', '5638503942342', 'aventura', 'disponible', 2, 'libro de aventuras y diversion', 'img-portadas/quijote.png'),
(7, 'cuentos para monstruos', 'santiago pedraza', '123123123', 'Terror psicologico', 'disponible', 3, 'monsters', 'uploads/cuentos para monstruos.jpg');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `prestamo`
--

CREATE TABLE `prestamo` (
  `id` int(11) NOT NULL,
  `id_reserva` int(11) DEFAULT NULL,
  `fecha_prestamo` date NOT NULL,
  `fecha_devolucion` date NOT NULL,
  `estado` varchar(100) NOT NULL,
  `dias` int(11) DEFAULT NULL,
  `id_usuario` int(11) NOT NULL,
  `id_libro` int(11) NOT NULL,
  `fecha_solicitud` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `prestamo`
--

INSERT INTO `prestamo` (`id`, `id_reserva`, `fecha_prestamo`, `fecha_devolucion`, `estado`, `dias`, `id_usuario`, `id_libro`, `fecha_solicitud`) VALUES
(7, NULL, '2025-10-29', '2025-11-05', 'devuelto', 7, 18, 5, '2025-10-29 21:05:57'),
(9, NULL, '2025-10-29', '2025-11-05', 'devuelto', 7, 18, 6, '2025-10-29 21:17:31'),
(10, NULL, '0000-00-00', '0000-00-00', 'rechazado', 7, 18, 5, '2025-10-29 21:27:46'),
(11, NULL, '2025-10-29', '2025-11-05', 'devuelto', 20, 18, 7, '2025-10-29 21:36:45'),
(13, NULL, '2025-10-29', '2025-11-05', 'devuelto', 20, 18, 5, '2025-10-29 21:42:36'),
(14, NULL, '2025-10-29', '2025-11-05', 'devuelto', 20, 20, 5, '2025-10-29 21:57:46'),
(17, NULL, '2025-10-31', '2025-11-07', 'devuelto', 20, 18, 5, '2025-10-31 19:00:47');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `reserva`
--

CREATE TABLE `reserva` (
  `id` int(11) NOT NULL,
  `id_usuario` int(11) DEFAULT NULL,
  `id_libro` int(11) DEFAULT NULL,
  `dias` int(11) DEFAULT NULL,
  `fecha_reserva` date DEFAULT NULL,
  `estado` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `reserva`
--

INSERT INTO `reserva` (`id`, `id_usuario`, `id_libro`, `dias`, `fecha_reserva`, `estado`) VALUES
(32, 18, 5, 7, '2025-11-01', 'convertida'),
(38, 18, 5, 20, '2025-11-01', 'rechazada'),
(39, 18, 5, 30, '2025-11-01', 'convertida'),
(40, 18, 7, 30, '2025-11-01', 'pendiente');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `apellido` varchar(100) NOT NULL,
  `email` varchar(50) NOT NULL,
  `contrasena` varchar(255) NOT NULL,
  `tipo` enum('administrador','cliente') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `nombre`, `apellido`, `email`, `contrasena`, `tipo`) VALUES
(2, 'siri', 'betancurt', 'siri@gmail.com', '827ccb0eea8a706c4c34a16891f84e7b', 'administrador'),
(18, 'ana maria', 'epeza soto', 'ana@gmail.com', '$2y$10$fVcjovn1Ts0be59QPJ5FGO1fFWqz4c8TtdOEQviiilfY10VicSJG.', 'cliente'),
(19, 'Juan', 'trujillo', 'juan@gmail.com', '$2y$10$5eewMIMZmYileIn3PKTx3./PMHXdyaxi88R5mTbyqLreawqecZvVW', 'administrador'),
(20, 'carlitos', 'meza', 'carlitos@gmail.com', '$2y$10$XxJ9iGptPVfKGY5opsdD.uBHgMtsCRGIk5RsnUVhmDLEGwxe2BaZS', 'cliente'),
(21, 'sofia', 'perez', 'sofia@gmail.com', '$2y$10$Gfkbgy3YMywJavh3.6P4DuUbALs/nHa5hrkPbeFa/tOzgvNvXEsFG', 'cliente'),
(22, 'sarita', 'sar', 'sarita@gmail.com', '$2y$10$kkhSvQTC9NBDxgo.TrbSM.qxfFrdiLapJWlLOr0VKaQBpz.IY66sy', 'cliente');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `libro`
--
ALTER TABLE `libro`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `prestamo`
--
ALTER TABLE `prestamo`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id_reserva` (`id_reserva`);

--
-- Indices de la tabla `reserva`
--
ALTER TABLE `reserva`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `libro`
--
ALTER TABLE `libro`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de la tabla `prestamo`
--
ALTER TABLE `prestamo`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT de la tabla `reserva`
--
ALTER TABLE `reserva`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `prestamo`
--
ALTER TABLE `prestamo`
  ADD CONSTRAINT `prestamo_ibfk_1` FOREIGN KEY (`id_reserva`) REFERENCES `reserva` (`id`) ON DELETE SET NULL;

--
-- Filtros para la tabla `reserva`
--
ALTER TABLE `reserva`
  ADD CONSTRAINT `reserva_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `reserva_ibfk_2` FOREIGN KEY (`id_libro`) REFERENCES `libro` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
