-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 13-12-2024 a las 03:47:07
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
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `usuario` varchar(250) NOT NULL,
  `clave` varchar(250) NOT NULL,
  `fechaBaja` date DEFAULT NULL,
  `rol` varchar(250) NOT NULL,
  `estado` varchar(250) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `usuario`, `clave`, `fechaBaja`, `rol`, `estado`) VALUES
(1, 'Franco', '$2y$10$8wEr7qGZxt8UAT2XUoSDUO/aIl9CbOgBxwrpaQ8Hre8NNorosbM6O', NULL, 'mozo', 'activo'),
(2, 'Pedro', '$2y$10$e2I3HQlE6zTx9XfByWuWdezqlOzdMjKnG/zgZqUU6FVbgGxhAtXEu', NULL, 'cocinero', 'activo'),
(3, 'Jorge', '$2y$10$A9Ngk7b10qj6hY5o641hF.bfZYOi7IOgM1e2uOEUh.t.ULTNUZnT6', NULL, 'bartender', 'inactivo'),
(4, 'Alicia', '$2y$10$nPeAxs2mxTpRIUZVildjXOWwXTqESzp42VpybFjmIsdHoJ0vxd8ki', NULL, 'socio', 'activo'),
(5, 'Marta', '$2y$10$rwJks4MsuIRZ4/UPwDXNm.NPrK621GX4iJCVHX0uQPuFRg/P.hhCi', NULL, 'cervecero', 'activo'),
(6, 'Carina', '$2y$10$81fZkXB7ryxL2pFRLfe5duUnzyGDARqQTeEBfcnwYmxry.GAaSTu2', NULL, 'cliente', 'activo'),
(7, 'Fernando', '$2y$10$bLq7cNMNIMEP54dZIbN3T.96I.ezdymny33n8hNQgPaz7X6fs01Ve', NULL, 'cocinero', 'inactivo'),
(8, 'Martina', '$2y$10$T0daqwOb.PaTB2JPWLxSSeV4kTrITv8I/0hi84/r5uBEashbHJ7hm', NULL, 'bartender', 'activo'),
(9, 'Marcos', '$2y$10$j9OCtuOiNHrw6lRw88tTiuC6oKOLwG.XgzkBuY4q6cvEfCa1iKC4u', NULL, 'bartender', 'inactivo');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `usuario` (`usuario`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
