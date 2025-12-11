-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 11-12-2025 a las 14:48:11
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
-- Base de datos: `caracterizacion`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `caracteristicas`
--

CREATE TABLE `caracteristicas` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `caracteristicas`
--

INSERT INTO `caracteristicas` (`id`, `nombre`, `descripcion`, `created_at`) VALUES
(1, 'Critico', 'Proyecto crítico para el negocio', '2025-11-28 15:06:49'),
(2, 'Innovador', 'Proyecto con alto componente de innovación', '2025-11-28 15:06:49'),
(3, 'Global', 'Proyecto con alcance global', '2025-11-28 15:06:49');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `caracterizaciones`
--

CREATE TABLE `caracterizaciones` (
  `id` int(11) NOT NULL,
  `proyecto_id` int(11) NOT NULL,
  `restricciones_json` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`restricciones_json`)),
  `tipo_restriccion` int(11) DEFAULT NULL,
  `complejidad_json` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`complejidad_json`)),
  `equipo_json` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`equipo_json`)),
  `dominio_cynefin` varchar(50) DEFAULT NULL,
  `estrategias_json` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`estrategias_json`)),
  `estado` enum('pendiente','completada') DEFAULT 'pendiente',
  `usuario_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `complejidades_adicionales`
--

CREATE TABLE `complejidades_adicionales` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `complejidades_adicionales`
--

INSERT INTO `complejidades_adicionales` (`id`, `nombre`, `descripcion`, `created_at`) VALUES
(1, 'Equipo de desarrollo', 'Exigencias especiales requeridas para el equipo de desarrollo y nivel de trabajo en equipo', '2025-11-25 21:49:12'),
(2, 'Restricción de tiempo', 'Además de ser fijo, el tiempo está muy ajustado', '2025-11-25 21:49:12'),
(3, 'Tamaño', 'Muchas personas en el proyecto o gran cantidad de requisitos', '2025-11-25 21:49:12'),
(4, 'Desarrollo global', 'Existen distancias física, temporal o cultural entre los miembros del equipo', '2025-11-25 21:49:12'),
(5, 'Criticidad del problema', 'El dominio del problema es crítico: impacto en la vida, la seguridad, grandes pérdidas de dinero, etc', '2025-11-25 21:49:12'),
(6, 'Poca experiencia', 'El equipo posee poca experiencia en el dominio del problema, en las tecnologías a emplear o en el proceso y gestión del proyecto', '2025-11-25 21:49:12'),
(7, 'Requisitos variables', 'El cliente cambia los requisitos con alta frecuencia', '2025-11-25 21:49:12'),
(8, 'Otras restricciones', 'Restricciones fuertes del negocio, legales, etc. u otros factores de complejidad importantes', '2025-11-25 21:49:12'),
(9, 'Tecnologías emergentes', 'Uso de tecnologías nuevas o poco maduras en el proyecto', '2025-11-25 21:49:12'),
(10, 'Integración compleja', 'Necesidad de integrar múltiples sistemas legacy o heterogéneos', '2025-11-25 21:49:12');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cynefin`
--

CREATE TABLE `cynefin` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `triple_restriccion_id` int(11) DEFAULT NULL,
  `complejidad_adicional_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `dominios`
--

CREATE TABLE `dominios` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `dominios`
--

INSERT INTO `dominios` (`id`, `nombre`, `created_at`) VALUES
(1, 'Tecnología', '2025-11-28 15:06:49'),
(2, 'Salud', '2025-11-28 15:06:49'),
(3, 'Educación', '2025-11-28 15:06:49'),
(4, 'Finanzas', '2025-11-28 15:06:49'),
(5, 'Comercio', '2025-11-28 15:06:49'),
(6, 'Logística', '2025-11-28 15:06:49');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `estrategias`
--

CREATE TABLE `estrategias` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `es_estandar` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `estrategias`
--

INSERT INTO `estrategias` (`id`, `nombre`, `descripcion`, `es_estandar`, `created_at`) VALUES
(1, 'Ágil', 'Metodología ágil', 1, '2025-11-28 15:06:49'),
(2, 'Cascada', 'Metodología en cascada', 1, '2025-11-28 15:06:49'),
(3, 'Híbrida', 'Enfoque híbrido', 1, '2025-11-28 15:06:49');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `organizaciones`
--

CREATE TABLE `organizaciones` (
  `id` int(11) NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `telefono` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `direccion` text DEFAULT NULL,
  `usuario_admin_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `organizaciones`
--

INSERT INTO `organizaciones` (`id`, `nombre`, `descripcion`, `created_at`, `telefono`, `email`, `direccion`, `usuario_admin_id`) VALUES
(3, 'Empresa Test', 'Descripción de prueba', '2025-12-09 05:00:40', '555-4321', 'empresa@test.com', 'Calle Test 123', 2),
(5, 'ALACALDIA DE PIENDMAO', 'ENTADIAD PUBLICA', '2025-12-10 02:28:16', '3218666530', 'sistemas@piendamo-cauca.gov.co', 'Piendamo', 5),
(6, 'nueva', 'neuva', '2025-12-10 04:02:50', '3218666530', 'jcvm2001valencia@gmail.com', 'Vereda Puente Real', 6),
(7, 'nueva 2', 'nueva 2', '2025-12-11 00:06:51', '3218666530', 'jcvm2001valencia@gmail.com', 'Vereda Puente Real', 7);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `paises`
--

CREATE TABLE `paises` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `paises`
--

INSERT INTO `paises` (`id`, `nombre`, `created_at`) VALUES
(1, 'Argentina', '2025-11-28 15:06:49'),
(2, 'Brasil', '2025-11-28 15:06:49'),
(3, 'Chile', '2025-11-28 15:06:49'),
(4, 'Colombia', '2025-11-28 15:06:49'),
(5, 'México', '2025-11-28 15:06:49'),
(6, 'España', '2025-11-28 15:06:49'),
(7, 'argenetina ', '2025-11-29 04:08:21');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `perfiles`
--

CREATE TABLE `perfiles` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `perfiles`
--

INSERT INTO `perfiles` (`id`, `nombre`, `descripcion`, `created_at`) VALUES
(1, 'Desarrollador', 'Desarrollador de software', '2025-11-28 15:06:49'),
(2, 'Analista', 'Analista funcional', '2025-11-28 15:06:49'),
(3, 'Líder Técnico', 'Líder técnico de proyecto', '2025-11-28 15:06:49'),
(4, 'Arquitecto', 'Arquitecto de software', '2025-11-28 15:06:49'),
(5, 'QA', 'Control de calidad', '2025-11-28 15:06:49');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `proyectos`
--

CREATE TABLE `proyectos` (
  `id` int(11) NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `horas` int(11) DEFAULT 0,
  `pais_id` int(11) DEFAULT NULL,
  `dominio_id` int(11) DEFAULT NULL,
  `usuario_id` int(11) DEFAULT NULL,
  `organizacion_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `estado` varchar(20) DEFAULT 'pendiente',
  `fecha_inicio` date DEFAULT NULL,
  `fecha_fin` date DEFAULT NULL,
  `lider_proyecto_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `proyectos`
--

INSERT INTO `proyectos` (`id`, `nombre`, `descripcion`, `horas`, `pais_id`, `dominio_id`, `usuario_id`, `organizacion_id`, `created_at`, `estado`, `fecha_inicio`, `fecha_fin`, `lider_proyecto_id`) VALUES
(3, 'prueba ', 'prueba ', 0, 4, 5, 5, 3, '2025-12-11 03:27:34', 'pendiente', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `roles`
--

CREATE TABLE `roles` (
  `id` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `roles`
--

INSERT INTO `roles` (`id`, `nombre`, `descripcion`, `created_at`) VALUES
(1, 'AdminOrg', 'Administrador de Organización - Gestiona toda la organización y proyectos', '2025-11-28 15:07:44'),
(2, 'LiderProyecto', 'Líder de Proyecto - Realiza caracterización de proyectos', '2025-11-28 15:07:44'),
(3, 'AdminSistema', 'Administrador del Sistema - Super usuario, gestiona estrategias', '2025-11-28 15:07:44');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `triple_restricciones`
--

CREATE TABLE `triple_restricciones` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `triple_restricciones`
--

INSERT INTO `triple_restricciones` (`id`, `nombre`, `descripcion`, `created_at`) VALUES
(1, 'Tiempo', 'Restricciones de tiempo del proyecto', '2025-11-28 15:06:49'),
(2, 'Costo', 'Restricciones de presupuesto', '2025-11-28 15:06:49'),
(3, 'Alcance', 'Restricciones de alcance funcional', '2025-11-28 15:06:49');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) DEFAULT NULL,
  `apellido` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `usuario` varchar(50) DEFAULT NULL,
  `contrasena` varchar(255) DEFAULT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `rol_id` int(11) NOT NULL,
  `esta_borrado` tinyint(1) DEFAULT 0,
  `creado_en` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `nombre`, `apellido`, `email`, `usuario`, `contrasena`, `telefono`, `rol_id`, `esta_borrado`, `creado_en`) VALUES
(1, 'Juan Camilo', 'valencia Mosquera', 'jcvm2001valencia@gmail.com', 'camilotest', '$2y$10$KSK/9lkBVLdZA41OdzgmAeWFD3KHNhsvLYQS7UxHbUCxCTPGrQ4rW', '3218666530', 1, 0, '2025-12-09 04:42:24'),
(2, 'Juan', 'Perez', 'juan@test.com', 'juanperez', '$2y$10$TuHashAqui', '555-1234', 1, 0, '2025-12-09 05:00:40'),
(5, 'John Freiman', 'Urbano Urrutia', 'sistemas@piendamo-cauca.gov.co', 'ALPIENDAMO', '$2y$10$f9c3bpYlGPQcULlcbyQZhu6DNYtQv.5ZHv8guoJtEg7V6QNhvpJqG', '3218666530', 1, 0, '2025-12-10 02:27:47'),
(6, 'John Freiman', 'Urbano Urrutia', 'juancamilovalencia@unicomfacauca.edu.co', 'JuanCamilo', '$2y$10$vosbofwsVapVxgMCyF/90ubg.8HC0BAI3OR52S0oi6afgLNYZQEsi', '3218666530', 2, 0, '2025-12-10 04:02:40'),
(7, 'pamela', 'nueva', 'pame123kate@gmail.com', 'usuario3', '$2y$10$Rx3w4QsTtmg0SPn4897e1us2OAA573elwQEIwL3BskzvCAWkoIog6', '3218666530', 1, 0, '2025-12-11 00:06:42');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario_codigos_2fa`
--

CREATE TABLE `usuario_codigos_2fa` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) DEFAULT NULL,
  `codigo` varchar(6) NOT NULL,
  `fecha_expiracion` datetime NOT NULL,
  `utilizado` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuario_codigos_2fa`
--

INSERT INTO `usuario_codigos_2fa` (`id`, `usuario_id`, `codigo`, `fecha_expiracion`, `utilizado`, `created_at`) VALUES
(39, NULL, '282365', '2025-12-10 05:23:09', 0, '2025-12-10 04:08:09'),
(40, NULL, '508977', '2025-12-10 05:28:40', 0, '2025-12-10 04:13:40');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `caracteristicas`
--
ALTER TABLE `caracteristicas`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nombre` (`nombre`);

--
-- Indices de la tabla `caracterizaciones`
--
ALTER TABLE `caracterizaciones`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_proyecto` (`proyecto_id`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- Indices de la tabla `complejidades_adicionales`
--
ALTER TABLE `complejidades_adicionales`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nombre` (`nombre`);

--
-- Indices de la tabla `cynefin`
--
ALTER TABLE `cynefin`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nombre` (`nombre`),
  ADD KEY `triple_restriccion_id` (`triple_restriccion_id`),
  ADD KEY `complejidad_adicional_id` (`complejidad_adicional_id`);

--
-- Indices de la tabla `dominios`
--
ALTER TABLE `dominios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nombre` (`nombre`);

--
-- Indices de la tabla `estrategias`
--
ALTER TABLE `estrategias`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nombre` (`nombre`);

--
-- Indices de la tabla `organizaciones`
--
ALTER TABLE `organizaciones`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_organizaciones_admin` (`usuario_admin_id`);

--
-- Indices de la tabla `paises`
--
ALTER TABLE `paises`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nombre` (`nombre`);

--
-- Indices de la tabla `perfiles`
--
ALTER TABLE `perfiles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nombre` (`nombre`);

--
-- Indices de la tabla `proyectos`
--
ALTER TABLE `proyectos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pais_id` (`pais_id`),
  ADD KEY `dominio_id` (`dominio_id`),
  ADD KEY `organizacion_id` (`organizacion_id`),
  ADD KEY `idx_proyectos_usuario` (`usuario_id`),
  ADD KEY `lider_proyecto_id` (`lider_proyecto_id`);

--
-- Indices de la tabla `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nombre` (`nombre`);

--
-- Indices de la tabla `triple_restricciones`
--
ALTER TABLE `triple_restricciones`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nombre` (`nombre`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `usuario` (`usuario`),
  ADD KEY `idx_usuarios_rol` (`rol_id`);

--
-- Indices de la tabla `usuario_codigos_2fa`
--
ALTER TABLE `usuario_codigos_2fa`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_usuario_codigos_2fa_usuario` (`usuario_id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `caracteristicas`
--
ALTER TABLE `caracteristicas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `caracterizaciones`
--
ALTER TABLE `caracterizaciones`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `complejidades_adicionales`
--
ALTER TABLE `complejidades_adicionales`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `cynefin`
--
ALTER TABLE `cynefin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `dominios`
--
ALTER TABLE `dominios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `estrategias`
--
ALTER TABLE `estrategias`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `organizaciones`
--
ALTER TABLE `organizaciones`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `paises`
--
ALTER TABLE `paises`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `perfiles`
--
ALTER TABLE `perfiles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `proyectos`
--
ALTER TABLE `proyectos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `triple_restricciones`
--
ALTER TABLE `triple_restricciones`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `usuario_codigos_2fa`
--
ALTER TABLE `usuario_codigos_2fa`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=52;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `caracterizaciones`
--
ALTER TABLE `caracterizaciones`
  ADD CONSTRAINT `caracterizaciones_ibfk_1` FOREIGN KEY (`proyecto_id`) REFERENCES `proyectos` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `caracterizaciones_ibfk_2` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL;

--
-- Filtros para la tabla `cynefin`
--
ALTER TABLE `cynefin`
  ADD CONSTRAINT `cynefin_ibfk_1` FOREIGN KEY (`triple_restriccion_id`) REFERENCES `triple_restricciones` (`id`),
  ADD CONSTRAINT `cynefin_ibfk_2` FOREIGN KEY (`complejidad_adicional_id`) REFERENCES `complejidades_adicionales` (`id`);

--
-- Filtros para la tabla `organizaciones`
--
ALTER TABLE `organizaciones`
  ADD CONSTRAINT `fk_organizaciones_usuario_admin` FOREIGN KEY (`usuario_admin_id`) REFERENCES `usuarios` (`id`),
  ADD CONSTRAINT `organizaciones_ibfk_1` FOREIGN KEY (`usuario_admin_id`) REFERENCES `usuarios` (`id`);

--
-- Filtros para la tabla `proyectos`
--
ALTER TABLE `proyectos`
  ADD CONSTRAINT `proyectos_ibfk_1` FOREIGN KEY (`pais_id`) REFERENCES `paises` (`id`),
  ADD CONSTRAINT `proyectos_ibfk_2` FOREIGN KEY (`dominio_id`) REFERENCES `dominios` (`id`),
  ADD CONSTRAINT `proyectos_ibfk_4` FOREIGN KEY (`organizacion_id`) REFERENCES `organizaciones` (`id`),
  ADD CONSTRAINT `proyectos_ibfk_5` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `proyectos_ibfk_6` FOREIGN KEY (`lider_proyecto_id`) REFERENCES `usuarios` (`id`);

--
-- Filtros para la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD CONSTRAINT `fk_usuarios_rol` FOREIGN KEY (`rol_id`) REFERENCES `roles` (`id`);

--
-- Filtros para la tabla `usuario_codigos_2fa`
--
ALTER TABLE `usuario_codigos_2fa`
  ADD CONSTRAINT `fk_usuario_codigos_2fa_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`),
  ADD CONSTRAINT `usuario_codigos_2fa_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `organizaciones` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
