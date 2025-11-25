-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 25-11-2025 a las 20:45:22
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
-- Base de datos: `proyecto_caracterizacion`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `calificaciones_estrategias`
--

CREATE TABLE `calificaciones_estrategias` (
  `id` int(11) NOT NULL,
  `estrategia_id` int(11) DEFAULT NULL,
  `proyecto_id` int(11) DEFAULT NULL,
  `calificacion` int(11) DEFAULT NULL CHECK (`calificacion` between 1 and 5),
  `comentario` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `dominios_problema`
--

CREATE TABLE `dominios_problema` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `dominios_problema`
--

INSERT INTO `dominios_problema` (`id`, `nombre`, `descripcion`, `created_at`) VALUES
(1, 'Industria Farmacéutica', 'Proyectos relacionados con medicamentos, investigación médica y desarrollo farmacéutico', '2025-11-25 16:49:12'),
(2, 'Banca', 'Sistemas bancarios, financieros y de gestión de inversiones', '2025-11-25 16:49:12'),
(3, 'Salud', 'Sistemas de salud, hospitalarios y médicos', '2025-11-25 16:49:12'),
(4, 'Metalurgia', 'Industria metalúrgica y procesos de fabricación', '2025-11-25 16:49:12'),
(5, 'Aeronaval', 'Sector aeronáutico y naval', '2025-11-25 16:49:12'),
(6, 'Educación', 'Sistemas educativos y plataformas de aprendizaje', '2025-11-25 16:49:12'),
(7, 'Retail', 'Comercio minorista y puntos de venta', '2025-11-25 16:49:12'),
(8, 'Telecomunicaciones', 'Servicios de telecomunicaciones y redes', '2025-11-25 16:49:12'),
(9, 'Energía', 'Sector energético y utilities', '2025-11-25 16:49:12'),
(10, 'Gobierno', 'Sistemas gubernamentales y de administración pública', '2025-11-25 16:49:12');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `estrategias_personalizadas`
--

CREATE TABLE `estrategias_personalizadas` (
  `id` int(11) NOT NULL,
  `proyecto_id` int(11) DEFAULT NULL,
  `categoria` varchar(100) DEFAULT NULL,
  `descripcion` text DEFAULT NULL,
  `es_estandar` tinyint(1) DEFAULT 0,
  `calificacion` int(11) DEFAULT NULL,
  `comentario` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `factores_complejidad`
--

CREATE TABLE `factores_complejidad` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `factores_complejidad`
--

INSERT INTO `factores_complejidad` (`id`, `nombre`, `descripcion`, `created_at`) VALUES
(1, 'Equipo de desarrollo', 'Exigencias especiales requeridas para el equipo de desarrollo y nivel de trabajo en equipo', '2025-11-25 16:49:12'),
(2, 'Restricción de tiempo', 'Además de ser fijo, el tiempo está muy ajustado', '2025-11-25 16:49:12'),
(3, 'Tamaño', 'Muchas personas en el proyecto o gran cantidad de requisitos', '2025-11-25 16:49:12'),
(4, 'Desarrollo global', 'Existen distancias física, temporal o cultural entre los miembros del equipo', '2025-11-25 16:49:12'),
(5, 'Criticidad del problema', 'El dominio del problema es crítico: impacto en la vida, la seguridad, grandes pérdidas de dinero, etc', '2025-11-25 16:49:12'),
(6, 'Poca experiencia', 'El equipo posee poca experiencia en el dominio del problema, en las tecnologías a emplear o en el proceso y gestión del proyecto', '2025-11-25 16:49:12'),
(7, 'Requisitos variables', 'El cliente cambia los requisitos con alta frecuencia', '2025-11-25 16:49:12'),
(8, 'Otras restricciones', 'Restricciones fuertes del negocio, legales, etc. u otros factores de complejidad importantes', '2025-11-25 16:49:12'),
(9, 'Tecnologías emergentes', 'Uso de tecnologías nuevas o poco maduras en el proyecto', '2025-11-25 16:49:12'),
(10, 'Integración compleja', 'Necesidad de integrar múltiples sistemas legacy o heterogéneos', '2025-11-25 16:49:12');

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
(1, 'Desarrollador', 'Desarrollador de software', '2025-11-25 16:49:12'),
(2, 'Tester', 'Especialista en pruebas de calidad', '2025-11-25 16:49:12'),
(3, 'Arquitecto', 'Arquitecto de software', '2025-11-25 16:49:12'),
(4, 'Project Manager', 'Gestor de proyectos', '2025-11-25 16:49:12'),
(5, 'Product Owner', 'Responsable de producto', '2025-11-25 16:49:12'),
(6, 'Scrum Master', 'Facilitador de metodologías ágiles', '2025-11-25 16:49:12'),
(7, 'Diseñador UX/UI', 'Diseñador de experiencia de usuario', '2025-11-25 16:49:12'),
(8, 'DevOps', 'Especialista en operaciones de desarrollo', '2025-11-25 16:49:12'),
(9, 'Analista de Negocio', 'Analista de requisitos de negocio', '2025-11-25 16:49:12'),
(10, 'Líder Técnico', 'Líder técnico del equipo', '2025-11-25 16:49:12'),
(11, 'Especialista QA', 'Especialista en calidad de software', '2025-11-25 16:49:12'),
(12, 'Data Scientist', 'Científico de datos', '2025-11-25 16:49:12'),
(13, 'Administrador de BD', 'Administrador de bases de datos', '2025-11-25 16:49:12');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `proyectos`
--

CREATE TABLE `proyectos` (
  `id` int(11) NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `pais_cliente` varchar(100) DEFAULT NULL,
  `tamano_estimado` varchar(100) DEFAULT NULL,
  `dominio_problema` text DEFAULT NULL,
  `descripcion` text DEFAULT NULL,
  `equipo` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`equipo`)),
  `factores` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`factores`)),
  `complejidad_total` int(11) DEFAULT NULL,
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `proyectos`
--

INSERT INTO `proyectos` (`id`, `nombre`, `pais_cliente`, `tamano_estimado`, `dominio_problema`, `descripcion`, `equipo`, `factores`, `complejidad_total`, `fecha_creacion`) VALUES
(1, 'Proyecto de Transformación Digital Educativa', 'Colombia', 'Mediano', 'Tecnología Educativa', 'Implementación de un sistema de caracterización y análisis de proyectos TIC para instituciones educativas.', '[{\"nombre\": \"Juan Pérez\", \"rol\": \"Líder de proyecto\", \"responsabilidad\": \"Coordinación general\"}, {\"nombre\": \"Ana Gómez\", \"rol\": \"Analista\", \"responsabilidad\": \"Evaluación de complejidad\"}]', '[\"Tiempo\", \"Alcance\", \"Costo\"]', 3, '2025-10-19 20:02:20'),
(3, 'caracterización ', 'Colombia', 'No definido', 'tecnología', 'caracterización de proyectos de software', '[{\"cantidad\":\"1\",\"perfil\":\"desarrollador\"},{\"cantidad\":\"2\",\"perfil\":\"testers\"},{\"cantidad\":\"1\",\"perfil\":\"QA\"}]', '{\"restricciones\":[\"Tiempo\",\"Alcance\"],\"complejidad\":[\"Tama\\u00f1o\"],\"dominio_cynefin\":\"Complejo\",\"estrategias\":{\"Tipo de acci\\u00f3n\":\"Experimentar para descubrir pr\\u00e1cticas \\u00fatiles\",\"Pr\\u00e1cticas\":\"Emergen seg\\u00fan resultados\",\"Enfoque de gesti\\u00f3n de proyecto\":\"Emp\\u00edrico\",\"Modelo de ciclo de vida\":\"Iterativo e incremental\",\"Acuerdos de trabajo\":\"Se revisan en retrospectivas\",\"Planificaci\\u00f3n\":\"A corto plazo, revisada con frecuencia\",\"Din\\u00e1micas a explotar\":\"Detectar patrones\",\"Din\\u00e1micas a prevenir\":\"Evitar aumento innecesario de complejidad\",\"Enfoque \\u00e1gil\":\"Scrum o Kanban\"}}', 1, '2025-10-20 22:12:26'),
(4, 'casa', 'argenetina ', 'No definido', 'AWDSADF', 'ADSFADSF', '[{\"cantidad\":\"1\",\"perfil\":\"DCDCXCX\"}]', '{\"restricciones\":[\"Tiempo\"],\"complejidad\":[\"Desarrollo global\"],\"dominio_cynefin\":\"Claro\",\"estrategias\":{\"Tipo de acci\\u00f3n\":\"Evidente\",\"Pr\\u00e1cticas\":\"Emplear la mejor pr\\u00e1ctica\",\"Enfoque de gesti\\u00f3n de proyecto\":\"Secuencial o por flujo tenso\",\"Modelo de ciclo de vida\":\"Secuencial\",\"Acuerdos de trabajo\":\"B\\u00e1sicos, con fundamentos al inicio\",\"Planificaci\\u00f3n\":\"Planificaci\\u00f3n inicial\",\"Din\\u00e1micas a explotar\":\"Controlar que el contexto no cambie\",\"Din\\u00e1micas a prevenir\":\"No detectar cambios del contexto por comodidad\",\"Enfoque \\u00e1gil\":\"Cascada o Kanban\"}}', 1, '2025-10-22 20:53:15'),
(5, 'prueba 3', 'Colombia', 'No definido', 'prueba 4', 'prueba 4', '[{\"cantidad\":\"1\",\"perfil\":\"desarrollador\"}]', '{\"restricciones\":[\"Costo\"],\"complejidad\":[\"Equipo de desarrollo\",\"Restricci\\u00f3n de tiempo\",\"Tama\\u00f1o\"],\"dominio_cynefin\":\"Ca\\u00f3tico\",\"estrategias\":{\"Tipo de acci\\u00f3n\":\"Actuar de inmediato\",\"Pr\\u00e1cticas\":\"Usar las que garanticen estabilidad\",\"Enfoque de gesti\\u00f3n de proyecto\":\"Emp\\u00edrico\",\"Modelo de ciclo de vida\":\"Iterativo e incremental\",\"Acuerdos de trabajo\":\"Establecidos por el l\\u00edder o coach \\u00e1gil\",\"Planificaci\\u00f3n\":\"Basada en la experiencia del equipo\",\"Din\\u00e1micas a explotar\":\"Restablecer orden\",\"Din\\u00e1micas a prevenir\":\"Permanecer demasiado tiempo en caos\",\"Enfoque \\u00e1gil\":\"Scrum con pr\\u00e1cticas adaptativas\"}}', 3, '2025-11-25 16:08:58');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `usuario` varchar(50) NOT NULL,
  `contrasena` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `nombre`, `email`, `usuario`, `contrasena`, `created_at`) VALUES
(1, 'Admin', 'admin@proyecto.com', 'admin', '$2y$10$EjemploHashContrasena1234567890abcdef', '2025-10-07 00:01:56'),
(2, 'camilo', 'jcvm2001valencia@gmail.com', 'camilotest', '$2y$10$s4GJcKFFvemy1Z16IpjsHutjxG/c6R35upsGcO6o8Fju99zbZlkpK', '2025-10-07 00:15:06'),
(4, 'accesorios ', 'prueba123@gmail.com', 'accesorios ', '$2y$10$tItaGR4zxp84z9LqbuZrBeIK9uqhYKdVEpGzacHBdIQsC2wASlJWW', '2025-10-19 16:00:20'),
(5, 'Juan Camilo Valencia Mosquera', 'sistemas@piendamo-cauca.gov.co', 'camilo', '$2y$10$rbCAphUFYf0E.pQkch0uU.LY/9nMXA4ReCKIEl5pPGDUIgULSLLX2', '2025-10-19 16:11:52'),
(7, 'Juan Camilo Valencia Mosquera', 'juan.valencia@unicomfacauca.edu.co', 'camilotest1', '$2y$10$QaDhJ5RelwE/qukoaXfrYula94QJMlz4wuQEE7UVbji4tHI2J4fvG', '2025-11-25 16:07:05');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `calificaciones_estrategias`
--
ALTER TABLE `calificaciones_estrategias`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `dominios_problema`
--
ALTER TABLE `dominios_problema`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nombre` (`nombre`);

--
-- Indices de la tabla `estrategias_personalizadas`
--
ALTER TABLE `estrategias_personalizadas`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `factores_complejidad`
--
ALTER TABLE `factores_complejidad`
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
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `usuario` (`usuario`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `calificaciones_estrategias`
--
ALTER TABLE `calificaciones_estrategias`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `dominios_problema`
--
ALTER TABLE `dominios_problema`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `estrategias_personalizadas`
--
ALTER TABLE `estrategias_personalizadas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `factores_complejidad`
--
ALTER TABLE `factores_complejidad`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `perfiles`
--
ALTER TABLE `perfiles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT de la tabla `proyectos`
--
ALTER TABLE `proyectos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
