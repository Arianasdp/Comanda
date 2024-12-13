-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 13-12-2024 a las 03:47:36
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
-- Estructura de tabla para la tabla `pedidos`
--

CREATE TABLE `pedidos` (
  `id` int(11) NOT NULL,
  `id_mesa` int(11) NOT NULL,
  `estado` varchar(250) NOT NULL,
  `foto` varchar(250) DEFAULT '-',
  `fecha_inicio` datetime DEFAULT NULL,
  `tiempo_estimado` int(11) DEFAULT NULL,
  `fecha_entrega` datetime DEFAULT NULL,
  `precio` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Volcado de datos para la tabla `pedidos`
--

INSERT INTO `pedidos` (`id`, `id_mesa`, `estado`, `foto`, `fecha_inicio`, `tiempo_estimado`, `fecha_entrega`, `precio`) VALUES
(1, 1, 'servido', '-', '2024-12-10 15:59:37', 20, '2024-12-10 16:14:53', 120),
(4, 2, 'servido', '-', '2024-12-03 14:47:56', 60, '2024-12-10 20:45:53', 420),
(5, 3, 'servido', '-', '2024-12-03 21:44:05', 25, '2024-12-03 22:00:22', 220),
(6, 1, 'servido', '-', '2024-12-10 20:36:25', 60, '2024-12-10 20:46:53', 350),
(11, 1, 'servido', 'ImagenesDeMesas/1.2024-12-11_18-57-15.jpg', '2024-12-11 18:57:15', 25, '2024-12-11 19:44:00', 120);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `pedidos`
--
ALTER TABLE `pedidos`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `pedidos`
--
ALTER TABLE `pedidos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
