-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 14-06-2025 a las 07:53:49
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `hospital_dashboard_db`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `computer_inventory`
--

CREATE TABLE `computer_inventory` (
  `id` int(11) NOT NULL,
  `employee_name` varchar(150) NOT NULL,
  `employee_role` varchar(100) DEFAULT NULL,
  `employee_phone` varchar(20) DEFAULT NULL,
  `employee_email` varchar(100) NOT NULL,
  `equipment_type` enum('PC','Portátil') NOT NULL,
  `brand` varchar(100) DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `mac_address` varchar(17) DEFAULT NULL,
  `has_antivirus_license` tinyint(1) NOT NULL DEFAULT 1,
  `has_office_license` tinyint(1) NOT NULL DEFAULT 1,
  `has_windows_license` tinyint(1) NOT NULL DEFAULT 1,
  `registration_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `compliance_due_date` date DEFAULT NULL COMMENT 'Fecha límite para regularizar licencias pendientes'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `personnel_entries`
--

CREATE TABLE `personnel_entries` (
  `id` int(11) NOT NULL,
  `report_id` int(11) NOT NULL,
  `personnel_type` enum('Estudiante','Residente','Externo','Auditor Externo') NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `university` varchar(255) DEFAULT NULL,
  `residency_duration_months` int(11) DEFAULT NULL,
  `hospital_area` varchar(150) DEFAULT NULL,
  `service_type` varchar(150) DEFAULT NULL,
  `eps_origin` varchar(150) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `personnel_reports`
--

CREATE TABLE `personnel_reports` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `analysis_text` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `users`
--

INSERT INTO `users` (`id`, `username`, `password_hash`, `created_at`) VALUES
(1, 'admin', '$2y$10$Td0fiYW66ic/TMklALxuTOxiXWGjcKDIXWsHjm2QuINSTneBJB2Tq', '2025-06-14 02:29:16');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `computer_inventory`
--
ALTER TABLE `computer_inventory`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `personnel_entries`
--
ALTER TABLE `personnel_entries`
  ADD PRIMARY KEY (`id`),
  ADD KEY `report_id` (`report_id`);

--
-- Indices de la tabla `personnel_reports`
--
ALTER TABLE `personnel_reports`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indices de la tabla `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `computer_inventory`
--
ALTER TABLE `computer_inventory`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `personnel_entries`
--
ALTER TABLE `personnel_entries`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT de la tabla `personnel_reports`
--
ALTER TABLE `personnel_reports`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT de la tabla `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `personnel_entries`
--
ALTER TABLE `personnel_entries`
  ADD CONSTRAINT `personnel_entries_ibfk_1` FOREIGN KEY (`report_id`) REFERENCES `personnel_reports` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `personnel_reports`
--
ALTER TABLE `personnel_reports`
  ADD CONSTRAINT `personnel_reports_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
