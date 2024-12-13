-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 13-12-2024 a las 04:53:45
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
-- Base de datos: `lacomanda`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pedido_productos`
--

CREATE TABLE `pedido_productos` (
  `id` int(11) NOT NULL,
  `id_pedido` int(11) NOT NULL,
  `id_producto` int(11) NOT NULL,
  `id_empleado` int(11) DEFAULT NULL,
  `estado` varchar(250) NOT NULL,
  `fecha_inicio` datetime DEFAULT NULL,
  `fecha_entrega` datetime DEFAULT NULL,
  `tiempo_estimado` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Volcado de datos para la tabla `pedido_productos`
--

INSERT INTO `pedido_productos` (`id`, `id_pedido`, `id_producto`, `id_empleado`, `estado`, `fecha_inicio`, `fecha_entrega`, `tiempo_estimado`) VALUES
(1, 1, 5, 7, 'listo para servir', '2024-12-02 21:38:37', '2024-12-02 22:19:23', 20),
(5, 4, 7, 2, 'listo para servir', '2024-12-03 21:56:16', '2024-12-03 22:52:20', 60),
(6, 4, 8, 2, 'listo para servir', '2024-12-03 19:56:59', '2024-12-03 20:04:34', 30),
(7, 4, 4, 2, 'listo para servir', '2024-12-03 20:05:57', '2024-12-03 20:55:59', 15),
(8, 5, 5, 2, 'listo para servir', '2024-12-03 21:45:05', '2024-12-03 22:45:46', 25),
(9, 5, 4, 5, 'listo para servir', '2024-12-03 21:45:17', '2024-12-03 22:00:04', 15),
(10, 6, 6, 7, 'listo para servir', '2024-12-10 20:36:42', '2024-12-10 20:46:47', 60),
(11, 11, 5, 7, 'listo para servir', '2024-12-11 19:29:38', '2024-12-11 19:38:49', 25);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `pedido_productos`
--
ALTER TABLE `pedido_productos`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `pedido_productos`
--
ALTER TABLE `pedido_productos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
