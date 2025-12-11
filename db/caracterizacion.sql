-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 10-12-2025 a las 02:31:19
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
-- Estructura de tabla para la tabla `estrategias_en_proyectos`
--

CREATE TABLE `estrategias_en_proyectos` (
  `id` int(11) NOT NULL,
  `proyecto_id` int(11) NOT NULL,
  `estrategia_id` int(11) NOT NULL,
  `comentarios` text DEFAULT NULL,
  `usuario_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
(3, 'Empresa Test', 'Descripción de prueba', '2025-12-09 05:00:40', '555-4321', 'empresa@test.com', 'Calle Test 123', 2);

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
  `equipo_json` text DEFAULT NULL,
  `pais` varchar(100) DEFAULT NULL,
  `complejidad_total` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `proyectos_caracteristicas`
--

CREATE TABLE `proyectos_caracteristicas` (
  `id` int(11) NOT NULL,
  `proyecto_id` int(11) NOT NULL,
  `caracteristica_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `proyectos_perfiles`
--

CREATE TABLE `proyectos_perfiles` (
  `id` int(11) NOT NULL,
  `proyecto_id` int(11) NOT NULL,
  `perfil_id` int(11) NOT NULL,
  `cantidad` int(11) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
(3, 'John Freiman', 'Urbano Urrutia', 'sistemas@piendamo-cauca.gov.co', 'jcvm2001va', '$2y$10$2QoMn.F7NjybcryfgCMPDeTbfJBnPFO0nIvxudgO1JDUSKPu.vPzm', '3218666530', 1, 0, '2025-12-09 05:18:31');

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

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario_tokens_recuperacion`
--

CREATE TABLE `usuario_tokens_recuperacion` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) DEFAULT NULL,
  `token` varchar(64) NOT NULL,
  `fecha_expiracion` datetime NOT NULL,
  `utilizado` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario_tokens_verificacion`
--

CREATE TABLE `usuario_tokens_verificacion` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) DEFAULT NULL,
  `token` varchar(64) NOT NULL,
  `fecha_expiracion` datetime NOT NULL,
  `utilizado` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
-- Indices de la tabla `estrategias_en_proyectos`
--
ALTER TABLE `estrategias_en_proyectos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_proyecto_estrategia` (`proyecto_id`,`estrategia_id`),
  ADD KEY `estrategia_id` (`estrategia_id`);

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
  ADD KEY `idx_proyectos_usuario` (`usuario_id`);

--
-- Indices de la tabla `proyectos_caracteristicas`
--
ALTER TABLE `proyectos_caracteristicas`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_proyecto_caracteristica` (`proyecto_id`,`caracteristica_id`),
  ADD KEY `caracteristica_id` (`caracteristica_id`);

--
-- Indices de la tabla `proyectos_perfiles`
--
ALTER TABLE `proyectos_perfiles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_proyecto_perfil` (`proyecto_id`,`perfil_id`),
  ADD KEY `perfil_id` (`perfil_id`);

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
-- Indices de la tabla `usuario_tokens_recuperacion`
--
ALTER TABLE `usuario_tokens_recuperacion`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `token` (`token`),
  ADD KEY `fk_usuario_tokens_recuperacion_usuario` (`usuario_id`);

--
-- Indices de la tabla `usuario_tokens_verificacion`
--
ALTER TABLE `usuario_tokens_verificacion`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `token` (`token`),
  ADD KEY `fk_usuario_tokens_verificacion_usuario` (`usuario_id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `caracteristicas`
--
ALTER TABLE `caracteristicas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

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
-- AUTO_INCREMENT de la tabla `estrategias_en_proyectos`
--
ALTER TABLE `estrategias_en_proyectos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `organizaciones`
--
ALTER TABLE `organizaciones`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `proyectos_caracteristicas`
--
ALTER TABLE `proyectos_caracteristicas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `proyectos_perfiles`
--
ALTER TABLE `proyectos_perfiles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `usuario_codigos_2fa`
--
ALTER TABLE `usuario_codigos_2fa`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT de la tabla `usuario_tokens_recuperacion`
--
ALTER TABLE `usuario_tokens_recuperacion`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `usuario_tokens_verificacion`
--
ALTER TABLE `usuario_tokens_verificacion`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `cynefin`
--
ALTER TABLE `cynefin`
  ADD CONSTRAINT `cynefin_ibfk_1` FOREIGN KEY (`triple_restriccion_id`) REFERENCES `triple_restricciones` (`id`),
  ADD CONSTRAINT `cynefin_ibfk_2` FOREIGN KEY (`complejidad_adicional_id`) REFERENCES `complejidades_adicionales` (`id`);

--
-- Filtros para la tabla `estrategias_en_proyectos`
--
ALTER TABLE `estrategias_en_proyectos`
  ADD CONSTRAINT `estrategias_en_proyectos_ibfk_1` FOREIGN KEY (`proyecto_id`) REFERENCES `proyectos` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `estrategias_en_proyectos_ibfk_2` FOREIGN KEY (`estrategia_id`) REFERENCES `estrategias` (`id`) ON DELETE CASCADE;

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
  ADD CONSTRAINT `proyectos_ibfk_5` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `proyectos_caracteristicas`
--
ALTER TABLE `proyectos_caracteristicas`
  ADD CONSTRAINT `proyectos_caracteristicas_ibfk_1` FOREIGN KEY (`proyecto_id`) REFERENCES `proyectos` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `proyectos_caracteristicas_ibfk_2` FOREIGN KEY (`caracteristica_id`) REFERENCES `caracteristicas` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `proyectos_perfiles`
--
ALTER TABLE `proyectos_perfiles`
  ADD CONSTRAINT `proyectos_perfiles_ibfk_1` FOREIGN KEY (`proyecto_id`) REFERENCES `proyectos` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `proyectos_perfiles_ibfk_2` FOREIGN KEY (`perfil_id`) REFERENCES `perfiles` (`id`) ON DELETE CASCADE;

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

--
-- Filtros para la tabla `usuario_tokens_recuperacion`
--
ALTER TABLE `usuario_tokens_recuperacion`
  ADD CONSTRAINT `fk_usuario_tokens_recuperacion_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`),
  ADD CONSTRAINT `usuario_tokens_recuperacion_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `organizaciones` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `usuario_tokens_verificacion`
--
ALTER TABLE `usuario_tokens_verificacion`
  ADD CONSTRAINT `fk_usuario_tokens_verificacion_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`),
  ADD CONSTRAINT `usuario_tokens_verificacion_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `organizaciones` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
