-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 10-06-2022 a las 18:46:37
-- Versión del servidor: 10.4.21-MariaDB
-- Versión de PHP: 7.4.24

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `worksisoporte`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `clientes`
--

CREATE TABLE `clientes` (
  `id` int(11) NOT NULL,
  `email` varchar(50) DEFAULT 'NULL',
  `nombre` varchar(50) DEFAULT NULL,
  `apellido` varchar(50) DEFAULT NULL,
  `cuit` int(11) DEFAULT NULL,
  `usuario` varchar(50) NOT NULL,
  `password` varchar(100) DEFAULT NULL,
  `hash` varchar(33) DEFAULT NULL,
  `isActive` tinyint(4) NOT NULL DEFAULT 1,
  `last_login` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `estado`
--

CREATE TABLE `estado` (
  `id` int(11) NOT NULL,
  `descripcion` varchar(20) NOT NULL,
  `color` varchar(25) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `estado`
--

INSERT INTO `estado` (`id`, `descripcion`, `color`) VALUES
(1, 'Abierto', 'table-success'),
(2, 'En proceso', 'table-warning'),
(3, 'Cerrado', 'table-danger');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `nivelesboton`
--

CREATE TABLE `nivelesboton` (
  `id` int(4) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `detalle` varchar(50) DEFAULT NULL,
  `seccion_id` int(4) NOT NULL,
  `nivelesboton_id` int(4) NOT NULL,
  `roles_id` int(4) NOT NULL DEFAULT 8,
  `nivel` int(2) NOT NULL,
  `enlace` varchar(50) DEFAULT NULL,
  `classbtn` varchar(20) DEFAULT NULL,
  `classicon` varchar(30) DEFAULT NULL,
  `orden` int(4) NOT NULL,
  `badge_color` varchar(20) DEFAULT NULL,
  `badge_texto` varchar(20) DEFAULT NULL,
  `titulo` int(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `nivelesboton`
--

INSERT INTO `nivelesboton` (`id`, `nombre`, `detalle`, `seccion_id`, `nivelesboton_id`, `roles_id`, `nivel`, `enlace`, `classbtn`, `classicon`, `orden`, `badge_color`, `badge_texto`, `titulo`) VALUES
(1, 'Raiz', '', 0, 1, 1, 9, '', '', '', 0, '', '', 0),
(3, 'clientes', '', 1, 2, 1, 6, 'backoffice/table/clientes/data', '', 'fa fa-user', 200, 'info', '', 0),
(5, 'Dashboard', '', 1, 1, 5, 6, 'dashboard', '', 'icon-speedometer', 1, 'warning', '', 0),
(7, 'nivelesboton', '', 1, 2, 1, 9, 'backoffice/table/nivelesboton/data', '', 'icon-list', 210, '', '', 0),
(29, 'agregar', NULL, 2, 1, 8, 9, 'agregar', 'warning', 'fa fa-plus', 0, '', '', 1),
(30, 'borrar', NULL, 2, 1, 8, 9, 'borrar', 'danger', 'fa fa-trash', 3, '', '', 0),
(31, 'editar', NULL, 2, 1, 8, 9, 'editar', 'primary', 'fa fa-pencil', 0, '', '', 0),
(32, 'subir', NULL, 2, 1, 8, 9, 'subir', 'dark', 'fa fa-upload', 2, '', '', 0),
(41, 'agregar', NULL, 4, 1, 8, 9, 'agregar', 'warning', 'fa fa-plus', 0, '', '', 1),
(42, 'borrar', NULL, 4, 1, 8, 9, 'borrar', 'danger', 'fa fa-trash', 3, '', '', 0),
(43, 'editar', NULL, 4, 1, 8, 9, 'editar', 'primary', 'fa fa-pencil', 0, '', '', 0),
(44, 'subir', NULL, 4, 1, 8, 9, 'subir', 'dark', 'fa fa-upload', 2, '', '', 0),
(45, 'agregar', NULL, 3, 1, 8, 9, 'agregar', 'warning', 'fa fa-plus', 0, '', '', 1),
(46, 'borrar', NULL, 3, 1, 8, 9, 'borrar', 'danger', 'fa fa-trash', 3, '', '', 0),
(47, 'editar', NULL, 3, 1, 8, 9, 'editar', 'primary', 'fa fa-pencil', 0, '', '', 0),
(48, 'subir', NULL, 3, 1, 8, 9, 'subir', 'dark', 'fa fa-upload', 2, '', '', 0),
(49, 'destinos', '', 1, 2, 1, 9, 'backoffice/table/destinos/data', '', 'icon-location-pin', 9, '', '', 0),
(50, 'tablaaux', '', 1, 2, 1, 9, 'backoffice/table/tablaaux/data', '', 'icon-list', 99, '', '', 0),
(51, 'agregar', NULL, 5, 1, 8, 9, 'agregar', 'warning', 'fa fa-plus', 0, '', '', 1),
(52, 'borrar', NULL, 5, 1, 8, 9, 'borrar', 'danger', 'fa fa-trash', 3, '', '', 0),
(53, 'editar', NULL, 5, 1, 8, 9, 'editar', 'primary', 'fa fa-pencil', 0, '', '', 0),
(54, 'subir', NULL, 5, 1, 8, 9, 'subir', 'dark', 'fa fa-upload', 2, '', '', 0),
(55, 'user', '', 1, 2, 1, 9, 'backoffice/table/user/data', '', 'icon-user', 50, '', '', 0),
(56, 'agregar', NULL, 6, 1, 8, 9, 'agregar', 'warning', 'fa fa-plus', 0, '', '', 1),
(57, 'borrar', NULL, 6, 1, 8, 9, 'borrar', 'danger', 'fa fa-trash', 3, '', '', 0),
(58, 'editar', NULL, 6, 1, 8, 9, 'editar', 'primary', 'fa fa-pencil', 0, '', '', 0),
(59, 'subir', NULL, 6, 1, 8, 9, 'subir', 'dark', 'fa fa-upload', 2, '', '', 0),
(60, 'roles', '', 1, 2, 2, 9, 'backoffice/table/roles/data', '', 'icon-key', 60, '', '', 0),
(61, 'agregar', NULL, 7, 1, 8, 9, 'agregar', 'warning', 'fa fa-plus', 0, '', '', 1),
(62, 'borrar', NULL, 7, 1, 8, 9, 'borrar', 'danger', 'fa fa-trash', 3, '', '', 0),
(63, 'editar', NULL, 7, 1, 8, 9, 'editar', 'primary', 'fa fa-pencil', 0, '', '', 0),
(64, 'subir', NULL, 7, 1, 8, 9, 'subir', 'dark', 'fa fa-upload', 2, '', '', 0),
(65, 'micros', '', 1, 2, 1, 9, 'backoffice/table/micros/data', '', 'fa fa-bus', 55, '', '', 0),
(66, 'agregar', NULL, 8, 1, 8, 9, 'agregar', 'warning', 'fa fa-plus', 0, '', '', 1),
(67, 'borrar', NULL, 8, 1, 8, 9, 'borrar', 'danger', 'fa fa-trash', 3, '', '', 0),
(68, 'editar', NULL, 8, 1, 8, 9, 'editar', 'primary', 'fa fa-pencil', 0, '', '', 0),
(69, 'subir', NULL, 8, 1, 8, 9, 'subir', 'dark', 'fa fa-upload', 2, '', '', 0),
(70, 'plantillas', '', 1, 2, 1, 9, 'backoffice/table/plantillas/data', '', 'fa fa-ambulance', 56, '', '', 0),
(71, 'agregar', NULL, 9, 1, 8, 9, 'agregar', 'warning', 'fa fa-plus', 0, '', '', 1),
(72, 'borrar', NULL, 9, 1, 8, 9, 'borrar', 'danger', 'fa fa-trash', 3, '', '', 0),
(73, 'editar', NULL, 9, 1, 8, 9, 'editar', 'primary', 'fa fa-pencil', 0, '', '', 0),
(74, 'subir', NULL, 9, 1, 8, 9, 'subir', 'dark', 'fa fa-upload', 2, '', '', 0),
(75, 'operadores', '', 1, 2, 1, 9, 'backoffice/table/operadores/data', '', 'fa fa-users', 27, '', '', 0),
(76, 'agregar', NULL, 10, 1, 8, 9, 'agregar', 'warning', 'fa fa-plus', 0, '', '', 1),
(77, 'borrar', NULL, 10, 1, 8, 9, 'borrar', 'danger', 'fa fa-trash', 3, '', '', 0),
(78, 'editar', NULL, 10, 1, 8, 9, 'editar', 'primary', 'fa fa-pencil', 0, '', '', 0),
(79, 'subir', NULL, 10, 1, 8, 9, 'subir', 'dark', 'fa fa-upload', 2, '', '', 0),
(80, 'itinerarios', '', 1, 2, 1, 9, 'backoffice/table/itinerarios/data', '', 'fa fa-map-o', 50, '', '', 0),
(81, 'agregar', NULL, 11, 1, 8, 9, 'agregar', 'warning', 'fa fa-plus', 0, '', '', 1),
(82, 'borrar', NULL, 11, 1, 8, 9, 'borrar', 'danger', 'fa fa-trash', 3, '', '', 0),
(83, 'editar', NULL, 11, 1, 8, 9, 'editar', 'primary', 'fa fa-pencil', 0, '', '', 0),
(84, 'subir', NULL, 11, 1, 8, 9, 'subir', 'dark', 'fa fa-upload', 2, '', '', 0),
(86, 'agregar', NULL, 12, 1, 8, 9, 'agregar', 'warning', 'fa fa-plus', 0, '', '', 1),
(87, 'borrar', NULL, 12, 1, 8, 9, 'borrar', 'danger', 'fa fa-trash', 3, '', '', 0),
(88, 'editar', NULL, 12, 1, 8, 9, 'editar', 'primary', 'fa fa-pencil', 0, '', '', 0),
(89, 'subir', NULL, 12, 1, 8, 9, 'subir', 'dark', 'fa fa-upload', 2, '', '', 0),
(90, 'Reservas', '', 1, 1, 1, 6, 'backoffice/table/reservas/data', '', 'fa fa-book', 3, '', '', 0),
(91, 'agregar', NULL, 13, 1, 8, 9, 'agregar', 'warning', 'fa fa-plus', 0, '', '', 1),
(92, 'borrar', NULL, 13, 1, 8, 9, 'borrar', 'danger', 'fa fa-trash', 3, '', '', 0),
(93, 'editar', NULL, 13, 1, 8, 9, 'editar', 'primary', 'fa fa-pencil', 0, '', '', 0),
(94, 'subir', NULL, 13, 1, 8, 9, 'subir', 'dark', 'fa fa-upload', 2, '', '', 0),
(95, 'Backoffice', '', 1, 1, 1, 6, '', '', '', 1, '', '', 1),
(96, 'pasajeros', '', 1, 2, 5, 7, 'backoffice/table/pasajeros/data', '', 'fa fa-address-card-o', 75, '', '', 0),
(97, 'agregar', NULL, 14, 1, 8, 9, 'agregar', 'warning', 'fa fa-plus', 0, '', '', 1),
(98, 'borrar', NULL, 14, 1, 8, 9, 'borrar', 'danger', 'fa fa-trash', 3, '', '', 0),
(99, 'editar', NULL, 14, 1, 8, 9, 'editar', 'primary', 'fa fa-pencil', 0, '', '', 0),
(100, 'subir', NULL, 14, 1, 8, 9, 'subir', 'dark', 'fa fa-upload', 2, '', '', 0),
(101, 'Extranet', '', 1, 1, 5, 1, '', '', '', 100, '', '', 1),
(102, 'Salidas', '', 1, 1, 5, 1, 'extranet', '', 'fa fa-plane	', 101, '', '', 0),
(103, 'botones', NULL, 0, 0, 1, 1, 'botones', NULL, NULL, 0, NULL, NULL, 0),
(104, 'buttons', NULL, 0, 1, 1, 1, 'buttons', NULL, NULL, 0, NULL, NULL, 0),
(105, 'generalDataExtranetReservas', NULL, 0, 1, 1, 1, 'generalDataExtranetReservas', NULL, NULL, 0, NULL, NULL, 0),
(106, 'extranetSalidas', NULL, 0, 1, 1, 1, 'extranetSalidas', NULL, NULL, 0, NULL, NULL, 0),
(107, 'getDataTable', NULL, 0, 1, 1, 1, 'getDataTable', NULL, NULL, 0, NULL, NULL, 0),
(108, 'dataTable', NULL, 0, 1, 1, 1, 'dataTable', NULL, NULL, 0, NULL, NULL, 0),
(109, 'getform', NULL, 0, 1, 1, 1, 'getform', NULL, NULL, 0, NULL, NULL, 0),
(110, 'addRegister', NULL, 0, 1, 1, 1, 'addRegister', NULL, NULL, 0, NULL, NULL, 0),
(111, 'extranetDetalleSalida', NULL, 0, 1, 1, 1, 'extranetDetalleSalida', NULL, NULL, 0, NULL, NULL, 0),
(112, 'sexos', '', 1, 2, 5, 9, 'backoffice/table/sexos/data', '', 'fa fa-venus-mars', 70, '', '', 0),
(113, 'updateRegister', NULL, 0, 1, 1, 1, 'updateRegister', NULL, NULL, 0, NULL, NULL, 0),
(114, 'paises', '', 1, 2, 2, 9, 'backoffice/table/paises/data', '', 'fa fa-globe', 50, '', '', 0),
(115, 'agregar', NULL, 15, 1, 8, 9, 'agregar', 'warning', 'fa fa-plus', 0, '', '', 1),
(116, 'borrar', NULL, 15, 1, 8, 9, 'borrar', 'danger', 'fa fa-trash', 3, '', '', 0),
(117, 'editar', NULL, 15, 1, 8, 9, 'editar', 'primary', 'fa fa-pencil', 0, '', '', 0),
(118, 'subir', NULL, 15, 1, 8, 9, 'subir', 'dark', 'fa fa-upload', 2, '', '', 0),
(119, 'agregar', NULL, 16, 1, 8, 9, 'agregar', 'warning', 'fa fa-plus', 0, '', '', 1),
(120, 'borrar', NULL, 16, 1, 8, 9, 'borrar', 'danger', 'fa fa-trash', 3, '', '', 0),
(121, 'editar', NULL, 16, 1, 8, 9, 'editar', 'primary', 'fa fa-pencil', 0, '', '', 0),
(122, 'subir', NULL, 16, 1, 8, 9, 'subir', 'dark', 'fa fa-upload', 2, '', '', 0),
(123, 'deleteRegister/pasajeros/1332980', NULL, 0, 1, 1, 1, 'deleteRegister/pasajeros/1332980', NULL, NULL, 0, NULL, NULL, 0),
(124, 'refresh-token', NULL, 0, 1, 1, 1, 'refresh-token', NULL, NULL, 0, NULL, NULL, 0),
(125, 'dataTableReservas', NULL, 0, 1, 1, 1, 'dataTableReservas', NULL, NULL, 0, NULL, NULL, 0),
(126, 'Mis Reservas', '', 1, 1, 5, 1, 'backoffice/table/reservas/data', '', 'fa fa-book', 102, '', '', 0),
(128, 'deleteRegister/nivelesboton/127', NULL, 0, 1, 1, 1, 'deleteRegister/nivelesboton/127', NULL, NULL, 0, NULL, NULL, 0),
(129, 'textos', '', 1, 2, 1, 7, 'backoffice/table/textos/data', '', 'fa fa-file-text', 60, '', '', 0),
(130, 'Salidas', NULL, 1, 1, 1, 6, 'backoffice/table/salidas/data', NULL, 'fa fa-plane', 2, NULL, NULL, 0),
(131, 'Configuracion', NULL, 12, 1, 8, 0, 'management', 'warning', 'fa fa-cogs', 0, NULL, NULL, 0),
(132, 'Servicios', '', 1, 2, 1, 7, 'backoffice/table/servicios/data', '', 'fa fa-cogs', 57, '', '', 0),
(133, 'agregar', NULL, 17, 1, 8, 9, 'agregar', 'warning', 'fa fa-plus', 0, '', '', 1),
(134, 'borrar', NULL, 17, 1, 8, 9, 'borrar', 'danger', 'fa fa-trash', 3, '', '', 0),
(135, 'editar', NULL, 17, 1, 8, 9, 'editar', 'primary', 'fa fa-pencil', 0, '', '', 0),
(136, 'subir', NULL, 17, 1, 8, 9, 'subir', 'dark', 'fa fa-upload', 2, '', '', 0),
(137, 'agregar', NULL, 18, 1, 8, 9, 'agregar', 'warning', 'fa fa-plus', 0, '', '', 1),
(138, 'borrar', NULL, 18, 1, 8, 9, 'borrar', 'danger', 'fa fa-trash', 3, '', '', 0),
(139, 'editar', NULL, 18, 1, 8, 9, 'editar', 'primary', 'fa fa-pencil', 0, '', '', 0),
(140, 'subir', NULL, 18, 1, 8, 9, 'subir', 'dark', 'fa fa-upload', 2, '', '', 0),
(141, 'agregar', NULL, 19, 1, 8, 9, 'agregar', 'warning', 'fa fa-plus', 0, '', '', 1),
(142, 'borrar', NULL, 19, 1, 8, 9, 'borrar', 'danger', 'fa fa-trash', 3, '', '', 0),
(143, 'editar', NULL, 19, 1, 8, 9, 'editar', 'primary', 'fa fa-pencil', 0, '', '', 0),
(144, 'subir', NULL, 19, 1, 8, 9, 'subir', 'dark', 'fa fa-upload', 2, '', '', 0),
(145, 'configuracion', '', 1, 1, 1, 6, 'backoffice/management', '', 'icon-settings', 99, '', '', 0),
(146, 'agregar', NULL, 20, 1, 8, 9, 'agregar', 'warning', 'fa fa-plus', 0, '', '', 1),
(147, 'borrar', NULL, 20, 1, 8, 9, 'borrar', 'danger', 'fa fa-trash', 3, '', '', 0),
(148, 'editar', NULL, 20, 1, 8, 9, 'editar', 'primary', 'fa fa-pencil', 0, '', '', 0),
(149, 'subir', NULL, 20, 1, 8, 9, 'subir', 'dark', 'fa fa-upload', 2, '', '', 0),
(150, 'Caja', '', 1, 1, 4, 6, 'finance/money-flow', '', 'fa fa-money', 201, '', '', 0),
(151, 'agregar', NULL, 21, 1, 8, 9, 'agregar', 'warning', 'fa fa-plus', 0, '', '', 1),
(152, 'borrar', NULL, 21, 1, 8, 9, 'borrar', 'danger', 'fa fa-trash', 3, '', '', 0),
(153, 'editar', NULL, 21, 1, 8, 9, 'editar', 'primary', 'fa fa-pencil', 0, '', '', 0),
(154, 'subir', NULL, 21, 1, 8, 9, 'subir', 'dark', 'fa fa-upload', 2, '', '', 0),
(155, 'agregar', NULL, 36, 1, 8, 9, 'agregar', 'warning', 'fa fa-plus', 0, '', '', 1),
(156, 'borrar', NULL, 36, 1, 8, 9, 'borrar', 'danger', 'fa fa-trash', 3, '', '', 0),
(157, 'editar', NULL, 36, 1, 8, 9, 'editar', 'primary', 'fa fa-pencil', 0, '', '', 0),
(158, 'subir', NULL, 36, 1, 8, 9, 'subir', 'dark', 'fa fa-upload', 2, '', '', 0),
(159, 'agregar', NULL, 37, 1, 8, 9, 'agregar', 'warning', 'fa fa-plus', 0, '', '', 1),
(160, 'borrar', NULL, 37, 1, 8, 9, 'borrar', 'danger', 'fa fa-trash', 3, '', '', 0),
(161, 'editar', NULL, 37, 1, 8, 9, 'editar', 'primary', 'fa fa-pencil', 0, '', '', 0),
(162, 'subir', NULL, 37, 1, 8, 9, 'subir', 'dark', 'fa fa-upload', 2, '', '', 0),
(163, 'agregar', NULL, 38, 1, 8, 9, 'agregar', 'warning', 'fa fa-plus', 0, '', '', 1),
(164, 'borrar', NULL, 38, 1, 8, 9, 'borrar', 'danger', 'fa fa-trash', 3, '', '', 0),
(165, 'editar', NULL, 38, 1, 8, 9, 'editar', 'primary', 'fa fa-pencil', 0, '', '', 0),
(166, 'subir', NULL, 38, 1, 8, 9, 'subir', 'dark', 'fa fa-upload', 2, '', '', 0),
(167, 'agregar', NULL, 39, 1, 8, 9, 'agregar', 'warning', 'fa fa-plus', 0, '', '', 1),
(168, 'borrar', NULL, 39, 1, 8, 9, 'borrar', 'danger', 'fa fa-trash', 3, '', '', 0),
(169, 'editar', NULL, 39, 1, 8, 9, 'editar', 'primary', 'fa fa-pencil', 0, '', '', 0),
(170, 'subir', NULL, 39, 1, 8, 9, 'subir', 'dark', 'fa fa-upload', 2, '', '', 0),
(171, 'Finanzas', '', 1, 1, 4, 7, '', '', '', 200, '', '', 1),
(172, 'Pagos', '', 1, 1, 4, 7, 'finance/payments', '', 'fa fa-usd', 202, '', '', 0),
(173, 'Cobros', '', 1, 1, 4, 7, 'finance/charges', '', 'fa fa-undo', 203, '', '', 0),
(174, 'agregar', NULL, 40, 1, 8, 9, 'agregar', 'warning', 'fa fa-plus', 0, '', '', 1),
(175, 'borrar', NULL, 40, 1, 8, 9, 'borrar', 'danger', 'fa fa-trash', 3, '', '', 0),
(176, 'editar', NULL, 40, 1, 8, 9, 'editar', 'primary', 'fa fa-pencil', 0, '', '', 0),
(177, 'subir', NULL, 40, 1, 8, 9, 'subir', 'dark', 'fa fa-upload', 2, '', '', 0),
(178, 'agregar', NULL, 41, 1, 8, 9, 'agregar', 'warning', 'fa fa-plus', 0, '', '', 1),
(179, 'borrar', NULL, 41, 1, 8, 9, 'borrar', 'danger', 'fa fa-trash', 3, '', '', 0),
(180, 'editar', NULL, 41, 1, 8, 9, 'editar', 'primary', 'fa fa-pencil', 0, '', '', 0),
(181, 'subir', NULL, 41, 1, 8, 9, 'subir', 'dark', 'fa fa-upload', 2, '', '', 0),
(182, 'agregar', NULL, 42, 1, 8, 9, 'agregar', 'warning', 'fa fa-plus', 0, '', '', 1),
(183, 'borrar', NULL, 42, 1, 8, 9, 'borrar', 'danger', 'fa fa-trash', 3, '', '', 0),
(184, 'editar', NULL, 42, 1, 8, 9, 'editar', 'primary', 'fa fa-pencil', 0, '', '', 0),
(185, 'subir', NULL, 42, 1, 8, 9, 'subir', 'dark', 'fa fa-upload', 2, '', '', 0),
(186, 'Micro - Rooming', '', 12, 1, 0, 7, 'availability', 'info', 'fa fa-bus', 0, '', '', 0),
(187, 'agregar', NULL, 43, 1, 8, 9, 'agregar', 'warning', 'fa fa-plus', 0, '', '', 1),
(188, 'borrar', NULL, 43, 1, 8, 9, 'borrar', 'danger', 'fa fa-trash', 3, '', '', 0),
(189, 'editar', NULL, 43, 1, 8, 9, 'editar', 'primary', 'fa fa-pencil', 0, '', '', 0),
(190, 'subir', NULL, 43, 1, 8, 9, 'subir', 'dark', 'fa fa-upload', 2, '', '', 0),
(191, 'agregar', NULL, 44, 1, 8, 9, 'agregar', 'warning', 'fa fa-plus', 0, '', '', 1),
(192, 'borrar', NULL, 44, 1, 8, 9, 'borrar', 'danger', 'fa fa-trash', 3, '', '', 0),
(193, 'editar', NULL, 44, 1, 8, 9, 'editar', 'primary', 'fa fa-pencil', 0, '', '', 0),
(194, 'subir', NULL, 44, 1, 8, 9, 'subir', 'dark', 'fa fa-upload', 2, '', '', 0),
(195, 'agregar', NULL, 45, 1, 8, 9, 'agregar', 'warning', 'fa fa-plus', 0, '', '', 1),
(196, 'borrar', NULL, 45, 1, 8, 9, 'borrar', 'danger', 'fa fa-trash', 3, '', '', 0),
(197, 'editar', NULL, 45, 1, 8, 9, 'editar', 'primary', 'fa fa-pencil', 0, '', '', 0),
(198, 'subir', NULL, 45, 1, 8, 9, 'subir', 'dark', 'fa fa-upload', 2, '', '', 0),
(199, 'agregar', NULL, 46, 1, 8, 9, 'agregar', 'warning', 'fa fa-plus', 0, '', '', 1),
(200, 'borrar', NULL, 46, 1, 8, 9, 'borrar', 'danger', 'fa fa-trash', 3, '', '', 0),
(201, 'editar', NULL, 46, 1, 8, 9, 'editar', 'primary', 'fa fa-pencil', 0, '', '', 0),
(202, 'subir', NULL, 46, 1, 8, 9, 'subir', 'dark', 'fa fa-upload', 2, '', '', 0),
(206, 'subir', '', 47, 1, 8, 9, 'detail', 'primary', 'fa fa-search', 2, '', '', 0),
(207, 'agregar', NULL, 48, 1, 8, 9, 'agregar', 'warning', 'fa fa-plus', 0, '', '', 1),
(208, 'borrar', NULL, 48, 1, 8, 9, 'borrar', 'danger', 'fa fa-trash', 3, '', '', 0),
(209, 'editar', NULL, 48, 1, 8, 9, 'editar', 'primary', 'fa fa-pencil', 0, '', '', 0),
(210, 'subir', NULL, 48, 1, 8, 9, 'subir', 'dark', 'fa fa-upload', 2, '', '', 0),
(211, 'agregar', NULL, 49, 1, 8, 9, 'agregar', 'warning', 'fa fa-plus', 0, '', '', 1),
(212, 'borrar', NULL, 49, 1, 8, 9, 'borrar', 'danger', 'fa fa-trash', 3, '', '', 0),
(213, 'editar', NULL, 49, 1, 8, 9, 'editar', 'primary', 'fa fa-pencil', 0, '', '', 0),
(214, 'subir', NULL, 49, 1, 8, 9, 'subir', 'dark', 'fa fa-upload', 2, '', '', 0),
(215, 'agregar', NULL, 50, 1, 8, 9, 'agregar', 'warning', 'fa fa-plus', 0, '', '', 1),
(216, 'borrar', NULL, 50, 1, 8, 9, 'borrar', 'danger', 'fa fa-trash', 3, '', '', 0),
(217, 'editar', NULL, 50, 1, 8, 9, 'editar', 'primary', 'fa fa-pencil', 0, '', '', 0),
(218, 'subir', NULL, 50, 1, 8, 9, 'subir', 'dark', 'fa fa-upload', 2, '', '', 0),
(219, 'agregar', NULL, 51, 1, 8, 9, 'agregar', 'warning', 'fa fa-plus', 0, '', '', 1),
(220, 'borrar', NULL, 51, 1, 8, 9, 'borrar', 'danger', 'fa fa-trash', 3, '', '', 0),
(221, 'editar', NULL, 51, 1, 8, 9, 'editar', 'primary', 'fa fa-pencil', 0, '', '', 0),
(222, 'subir', NULL, 51, 1, 8, 9, 'subir', 'dark', 'fa fa-upload', 2, '', '', 0),
(223, 'agregar', NULL, 52, 1, 8, 9, 'agregar', 'warning', 'fa fa-plus', 0, '', '', 1),
(224, 'borrar', NULL, 52, 1, 8, 9, 'borrar', 'danger', 'fa fa-trash', 3, '', '', 0),
(225, 'editar', NULL, 52, 1, 8, 9, 'editar', 'primary', 'fa fa-pencil', 0, '', '', 0),
(226, 'subir', NULL, 52, 1, 8, 9, 'subir', 'dark', 'fa fa-upload', 2, '', '', 0),
(227, 'agregar', NULL, 53, 1, 8, 9, 'agregar', 'warning', 'fa fa-plus', 0, '', '', 1),
(228, 'borrar', NULL, 53, 1, 8, 9, 'borrar', 'danger', 'fa fa-trash', 3, '', '', 0),
(229, 'editar', NULL, 53, 1, 8, 9, 'editar', 'primary', 'fa fa-pencil', 0, '', '', 0),
(230, 'subir', NULL, 53, 1, 8, 9, 'subir', 'dark', 'fa fa-upload', 2, '', '', 0),
(231, 'agregar', NULL, 54, 1, 8, 9, 'agregar', 'warning', 'fa fa-plus', 0, '', '', 1),
(232, 'borrar', NULL, 54, 1, 8, 9, 'borrar', 'danger', 'fa fa-trash', 3, '', '', 0),
(233, 'editar', NULL, 54, 1, 8, 9, 'editar', 'primary', 'fa fa-pencil', 0, '', '', 0),
(234, 'subir', NULL, 54, 1, 8, 9, 'subir', 'dark', 'fa fa-upload', 2, '', '', 0),
(235, 'agregar', NULL, 55, 1, 8, 9, 'agregar', 'warning', 'fa fa-plus', 0, '', '', 1),
(236, 'borrar', NULL, 55, 1, 8, 9, 'borrar', 'danger', 'fa fa-trash', 3, '', '', 0),
(237, 'editar', NULL, 55, 1, 8, 9, 'editar', 'primary', 'fa fa-pencil', 0, '', '', 0),
(238, 'subir', NULL, 55, 1, 8, 9, 'subir', 'dark', 'fa fa-upload', 2, '', '', 0),
(239, 'agregar', NULL, 56, 1, 8, 9, 'agregar', 'warning', 'fa fa-plus', 0, '', '', 1),
(240, 'borrar', NULL, 56, 1, 8, 9, 'borrar', 'danger', 'fa fa-trash', 3, '', '', 0),
(241, 'editar', NULL, 56, 1, 8, 9, 'editar', 'primary', 'fa fa-pencil', 0, '', '', 0),
(242, 'subir', NULL, 56, 1, 8, 9, 'subir', 'dark', 'fa fa-upload', 2, '', '', 0),
(243, 'agregar', NULL, 57, 1, 8, 9, 'agregar', 'warning', 'fa fa-plus', 0, '', '', 1),
(244, 'borrar', NULL, 57, 1, 8, 9, 'borrar', 'danger', 'fa fa-trash', 3, '', '', 0),
(245, 'editar', NULL, 57, 1, 8, 9, 'editar', 'primary', 'fa fa-pencil', 0, '', '', 0),
(246, 'subir', NULL, 57, 1, 8, 9, 'subir', 'dark', 'fa fa-upload', 2, '', '', 0),
(247, 'agregar', NULL, 58, 1, 8, 9, 'agregar', 'warning', 'fa fa-plus', 0, '', '', 1),
(248, 'borrar', NULL, 58, 1, 8, 9, 'borrar', 'danger', 'fa fa-trash', 3, '', '', 0),
(249, 'editar', NULL, 58, 1, 8, 9, 'editar', 'primary', 'fa fa-pencil', 0, '', '', 0),
(250, 'subir', NULL, 58, 1, 8, 9, 'subir', 'dark', 'fa fa-upload', 2, '', '', 0),
(251, 'ventas', '', 1, 1, 4, 6, 'operation/detail/venta', '', 'fa fa-shopping-cart', 301, '', '', 0),
(252, 'compras', '', 1, 1, 4, 6, 'operation/detail/compra', '', 'fa fa-shopping-basket', 302, '', '', 0),
(253, 'operaciones', '', 1, 1, 4, 6, '', '', '', 300, '', '', 1),
(254, 'agregar', NULL, 59, 1, 8, 9, 'agregar', 'warning', 'fa fa-plus', 0, '', '', 1),
(255, 'borrar', NULL, 59, 1, 8, 9, 'borrar', 'danger', 'fa fa-trash', 3, '', '', 0),
(256, 'editar', NULL, 59, 1, 8, 9, 'editar', 'primary', 'fa fa-pencil', 0, '', '', 0),
(257, 'subir', NULL, 59, 1, 8, 9, 'subir', 'dark', 'fa fa-upload', 2, '', '', 0),
(258, 'agregar', NULL, 60, 1, 8, 9, 'agregar', 'warning', 'fa fa-plus', 0, '', '', 1),
(259, 'borrar', NULL, 60, 1, 8, 9, 'borrar', 'danger', 'fa fa-trash', 3, '', '', 0),
(260, 'editar', NULL, 60, 1, 8, 9, 'editar', 'primary', 'fa fa-pencil', 0, '', '', 0),
(261, 'subir', NULL, 60, 1, 8, 9, 'subir', 'dark', 'fa fa-upload', 2, '', '', 0),
(262, 'agregar', NULL, 61, 1, 8, 9, 'agregar', 'warning', 'fa fa-plus', 0, '', '', 1),
(263, 'borrar', NULL, 61, 1, 8, 9, 'borrar', 'danger', 'fa fa-trash', 3, '', '', 0),
(264, 'editar', NULL, 61, 1, 8, 9, 'editar', 'primary', 'fa fa-pencil', 0, '', '', 0),
(265, 'subir', NULL, 61, 1, 8, 9, 'subir', 'dark', 'fa fa-upload', 2, '', '', 0),
(266, 'agregar', NULL, 62, 1, 8, 9, 'agregar', 'warning', 'fa fa-plus', 0, '', '', 1),
(267, 'borrar', NULL, 62, 1, 8, 9, 'borrar', 'danger', 'fa fa-trash', 3, '', '', 0),
(268, 'editar', NULL, 62, 1, 8, 9, 'editar', 'primary', 'fa fa-pencil', 0, '', '', 0),
(269, 'subir', NULL, 62, 1, 8, 9, 'subir', 'dark', 'fa fa-upload', 2, '', '', 0),
(270, 'agregar', NULL, 63, 1, 8, 9, 'agregar', 'warning', 'fa fa-plus', 0, '', '', 1),
(271, 'borrar', NULL, 63, 1, 8, 9, 'borrar', 'danger', 'fa fa-trash', 3, '', '', 0),
(272, 'editar', NULL, 63, 1, 8, 9, 'editar', 'primary', 'fa fa-pencil', 0, '', '', 0),
(273, 'subir', NULL, 63, 1, 8, 9, 'subir', 'dark', 'fa fa-upload', 2, '', '', 0),
(274, 'agregar', NULL, 64, 1, 8, 9, 'agregar', 'warning', 'fa fa-plus', 0, '', '', 1),
(275, 'borrar', NULL, 64, 1, 8, 9, 'borrar', 'danger', 'fa fa-trash', 3, '', '', 0),
(276, 'editar', NULL, 64, 1, 8, 9, 'editar', 'primary', 'fa fa-pencil', 0, '', '', 0),
(277, 'subir', NULL, 64, 1, 8, 9, 'subir', 'dark', 'fa fa-upload', 2, '', '', 0),
(278, 'agregar', NULL, 65, 1, 8, 9, 'agregar', 'warning', 'fa fa-plus', 0, '', '', 1),
(279, 'borrar', NULL, 65, 1, 8, 9, 'borrar', 'danger', 'fa fa-trash', 3, '', '', 0),
(280, 'editar', NULL, 65, 1, 8, 9, 'editar', 'primary', 'fa fa-pencil', 0, '', '', 0),
(281, 'subir', NULL, 65, 1, 8, 9, 'subir', 'dark', 'fa fa-upload', 2, '', '', 0),
(290, 'agregar', NULL, 66, 1, 8, 9, 'agregar', 'warning', 'fa fa-plus', 0, '', '', 1),
(291, 'borrar', NULL, 66, 1, 8, 9, 'borrar', 'danger', 'fa fa-trash', 3, '', '', 0),
(292, 'editar', NULL, 66, 1, 8, 9, 'editar', 'primary', 'fa fa-pencil', 0, '', '', 0),
(293, 'subir', NULL, 66, 1, 8, 9, 'subir', 'dark', 'fa fa-upload', 2, '', '', 0),
(294, 'agregar', NULL, 67, 1, 8, 9, 'agregar', 'warning', 'fa fa-plus', 0, '', '', 1),
(295, 'borrar', NULL, 67, 1, 8, 9, 'borrar', 'danger', 'fa fa-trash', 3, '', '', 0),
(296, 'editar', NULL, 67, 1, 8, 9, 'editar', 'primary', 'fa fa-pencil', 0, '', '', 0),
(297, 'subir', NULL, 67, 1, 8, 9, 'subir', 'dark', 'fa fa-upload', 2, '', '', 0),
(298, 'agregar', NULL, 68, 1, 8, 9, 'agregar', 'warning', 'fa fa-plus', 0, '', '', 1),
(299, 'borrar', NULL, 68, 1, 8, 9, 'borrar', 'danger', 'fa fa-trash', 3, '', '', 0),
(300, 'editar', NULL, 68, 1, 8, 9, 'editar', 'primary', 'fa fa-pencil', 0, '', '', 0),
(301, 'subir', NULL, 68, 1, 8, 9, 'subir', 'dark', 'fa fa-upload', 2, '', '', 0),
(302, 'agregar', NULL, 69, 1, 8, 9, 'agregar', 'warning', 'fa fa-plus', 0, '', '', 1),
(303, 'borrar', NULL, 69, 1, 8, 9, 'borrar', 'danger', 'fa fa-trash', 3, '', '', 0),
(304, 'editar', NULL, 69, 1, 8, 9, 'editar', 'primary', 'fa fa-pencil', 0, '', '', 0),
(305, 'subir', NULL, 69, 1, 8, 9, 'subir', 'dark', 'fa fa-upload', 2, '', '', 0),
(309, 'comprobante', '', 70, 1, 8, 9, 'comprobante', 'dark', 'fa fa-print', 2, '', '', 0),
(313, 'comprobante', '', 71, 1, 8, 9, 'comprobante', 'dark', 'fa fa-print', 2, '', '', 0),
(314, 'agregar', NULL, 72, 1, 8, 9, 'agregar', 'warning', 'fa fa-plus', 0, '', '', 1),
(315, 'borrar', NULL, 72, 1, 8, 9, 'borrar', 'danger', 'fa fa-trash', 3, '', '', 0),
(316, 'editar', NULL, 72, 1, 8, 9, 'editar', 'primary', 'fa fa-pencil', 0, '', '', 0),
(317, 'subir', NULL, 72, 1, 8, 9, 'subir', 'dark', 'fa fa-upload', 2, '', '', 0),
(318, 'agregar', NULL, 73, 1, 8, 9, 'agregar', 'warning', 'fa fa-plus', 0, '', '', 1),
(319, 'borrar', NULL, 73, 1, 8, 9, 'borrar', 'danger', 'fa fa-trash', 3, '', '', 0),
(320, 'editar', NULL, 73, 1, 8, 9, 'editar', 'primary', 'fa fa-pencil', 0, '', '', 0),
(321, 'subir', NULL, 73, 1, 8, 9, 'subir', 'dark', 'fa fa-upload', 2, '', '', 0),
(322, 'Accesos', '', 1, 1, 1, 5, '', '', '', 30, '', '', 1),
(323, 'Clientes', '', 1, 1, 1, 6, 'backoffice/table/clientes/data', '', 'fa fa-user', 50, '', '', 0),
(324, 'Operadores', '', 1, 1, 1, 6, 'backoffice/table/operadores/data', '', 'fa fa-user-plus', 52, '', '', 0),
(325, 'cuentas', '', 2, 323, 1, 6, 'cuentasBancarias', 'warning', 'fa fa-university', 30, '', '', 0),
(326, 'cuentas', '', 10, 323, 1, 6, 'cuentasBancarias', 'warning', 'fa fa-university', 30, '', '', 0),
(327, 'Ret/Per', '', 1, 1, 4, 6, 'finance/retenciones', '', 'fa fa-retweet', 204, '', '', 0),
(328, 'Listados', '', 1, 1, 4, 5, 'finance/listados', '', 'fa fa-print', 205, '', '', 0),
(329, 'Rutas', '', 1, 1, 5, 6, 'backoffice/routes', '', 'fa fa-map-signs', 99, '', '', 0),
(330, 'Itinerarios', '', 1, 1, 5, 6, 'backoffice/itinerarios', '', 'fa fa-newspaper-o', 99, '', '', 0),
(331, 'itinerario', 'Ver itinerario', 12, 1, 1, 7, 'itinerario', 'success', 'fa fa-newspaper-o', 10, '', '', 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `seccion`
--

CREATE TABLE `seccion` (
  `id` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `nivel` int(2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `seccion`
--

INSERT INTO `seccion` (`id`, `nombre`, `nivel`) VALUES
(1, 'sidebar', 0),
(2, 'Tablas clientes', 9),
(3, 'Tablas nivelesboton', 9),
(4, 'Tablas destinos', 9),
(5, 'Tablas tablaaux', 9),
(6, 'Tablas user', 9),
(7, 'Tablas roles', 9),
(8, 'Tablas micros', 9),
(9, 'Tablas plantillas', 9),
(10, 'Tablas operadores', 9),
(11, 'Tablas itinerarios', 9),
(12, 'Tablas salidas', 9),
(13, 'Tablas ', 9),
(14, 'Tablas pasajeros', 9),
(15, 'Tablas paises', 9),
(16, 'Tablas sexos', 9),
(17, 'Tablas textos', 9),
(18, 'Tablas servicios', 9),
(19, 'Tablas cuentas', 9),
(20, 'Tablas butacas', 9),
(21, 'Tablas rutas', 9),
(36, 'Tablas rutas_terminales', 9),
(37, 'Tablas caja', 9),
(38, 'Tablas cuenta', 9),
(39, 'Tablas parametros', 9),
(40, 'Tablas porcentajes', 9),
(41, 'Tablas porcentajes_alicuotas', 9),
(42, 'Tablas alicuotas', 9),
(43, 'Tablas anulaciones', 9),
(44, 'Tablas Terminales', 9),
(45, 'Tablas grupotarifas', 9),
(46, 'Tablas grupostarifas', 9),
(47, 'Tablas reservas', 9),
(48, 'Tablas promociones', 9),
(49, 'Tablas categoriasclientes', 9),
(50, 'Tablas categoriaclientes', 9),
(51, 'Tablas cuentasbancarias', 9),
(52, 'Tablas cuentasbancarias_operadores', 9),
(53, 'Tablas formapago', 9),
(54, 'Tablas Tiposervicios', 9),
(55, 'Tablas Tiposservicios', 9),
(56, 'Tablas salidas_terminales', 9),
(57, 'Tablas salidas_grupales', 9),
(58, 'Tablas empresa', 9),
(59, 'Tablas empleados', 9),
(60, 'Tablas choferes', 9),
(61, 'Tablas tipocomp', 9),
(62, 'Tablas imagendetalle', 9),
(63, 'Tablas planviaje', 9),
(64, 'Tablas planviajedia', 9),
(65, 'Tablas e_tarjetas', 9),
(66, 'Tablas provincias', 9),
(67, 'Tablas localidades', 9),
(68, 'Tablas precios', 9),
(69, 'Tablas operadores_tipostock', 9),
(70, 'Tablas venta', 9),
(71, 'Tablas compra', 9),
(72, 'Tablas cotizaciones', 9),
(73, 'Tablas proveedores', 9);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tablaaux`
--

CREATE TABLE `tablaaux` (
  `id` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `nivel` int(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `tablaaux`
--

INSERT INTO `tablaaux` (`id`, `nombre`, `nivel`) VALUES
(1, 'clientes', 6),
(2, 'user', 9),
(3, 'tablaaux', 9),
(4, 'seccion', 9),
(5, 'nivelesboton', 9),
(6, 'cuenta', 8),
(7, 'destinos', 6),
(8, 'roles', 9),
(9, 'micros', 9),
(10, 'micros', 9),
(11, 'plantillas', 9),
(12, 'operadores', 9),
(13, 'itinerarios', 9),
(14, 'salidas', 7),
(16, 'reservas', 7),
(17, 'pasajeros', 7),
(18, 'salidas', 6),
(19, 'sexos', 9),
(20, 'paises', 9),
(21, 'textos', 7),
(22, 'servicios', 7),
(23, 'butacas', 7),
(24, 'rutas', 6),
(25, 'rutas_terminales', 6),
(26, 'caja', 8),
(27, 'parametros', 8),
(28, 'alicuotas', 7),
(29, 'porcentajes', 7),
(30, 'porcentajes_alicuotas', 7),
(31, 'anulaciones', 7),
(32, 'terminales', 7),
(33, 'grupostarifas', 7),
(34, 'promociones', 7),
(35, 'categoriaclientes', 7),
(36, 'cuentasbancarias', 7),
(37, 'cuentasbancarias_operadores', 7),
(38, 'cuentasbancarias_clientes', 7),
(39, 'formapago', 7),
(40, 'tiposervicios', 8),
(41, 'salidas_grupales', 8),
(42, 'empresa', 7),
(43, 'empleados', 7),
(44, 'choferes', 7),
(45, 'tipocomp', 6),
(46, 'imagen', 7),
(47, 'imagendetalle', 7),
(48, 'planviaje', 7),
(49, 'planviajedia', 7),
(50, 'e_tarjetas', 7),
(51, 'provincias', 7),
(52, 'localidades', 7),
(53, 'precios', 7),
(54, 'serviciostipostock', 7),
(55, 'operadores_tipostock', 7),
(56, 'cotizaciones', 7),
(57, 'proveedores', 7);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ticket`
--

CREATE TABLE `ticket` (
  `id` int(11) NOT NULL,
  `titulo` varchar(100) NOT NULL,
  `dispositivo` varchar(50) NOT NULL,
  `user_id` int(11) NOT NULL,
  `fecha` datetime NOT NULL DEFAULT current_timestamp(),
  `estado_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `ticket`
--

INSERT INTO `ticket` (`id`, `titulo`, `dispositivo`, `user_id`, `fecha`, `estado_id`) VALUES
(38, 'No funciona disco solido', 'Recepcion', 2, '2022-06-08 13:36:39', 1),
(39, 'Test1', 'IMPRESOR', 14, '2022-06-10 11:36:18', 1),
(40, 'PruebaBrown', 'celular recepcion', 15, '2022-06-10 13:25:28', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ticketrenglon`
--

CREATE TABLE `ticketrenglon` (
  `id` int(11) NOT NULL,
  `ticket_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `detalle` varchar(150) NOT NULL,
  `fecha` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `ticketrenglon`
--

INSERT INTO `ticketrenglon` (`id`, `ticket_id`, `user_id`, `detalle`, `fecha`) VALUES
(1, 1, 2, 'Buen dia, hoy al comenzar el dia note que la impresora no funciona, la cola de impresion se llena pero no imprime. gracias', '2022-06-01 12:18:26'),
(2, 1, 7, 'Buen dia, me podrias pasar el codigo de any desk de la compu? asi entro y trato de solucionar', '2022-06-01 12:19:12'),
(3, 1, 2, '245 307 909', '2022-06-01 12:19:33'),
(6, 1, 2, 'Hola el problema me sigue pasando, gracias', '2022-06-06 12:36:44'),
(7, 1, 2, 'Necesito una respuesta rapido, gracias', '2022-06-06 12:46:01'),
(8, 1, 2, 'weeep', '2022-06-06 12:46:27'),
(9, 1, 8, 'Hola operador tu respuesta esta siendo procesada', '2022-06-06 12:47:23'),
(10, 1, 2, 'Buenass', '2022-06-06 13:46:12'),
(11, 1, 2, 'Ya lo viste _ ', '2022-06-06 13:46:31'),
(12, 1, 2, 'asdasdasdasdasd', '2022-06-06 13:46:52'),
(13, 32, 2, 'La balanza funciona mal', '2022-06-06 14:02:31'),
(14, 1, 2, 'AAAA', '2022-06-08 09:00:38'),
(15, 33, 2, 'no anda', '2022-06-08 11:45:36'),
(16, 1, 2, 'El estado del ticket fue cambiado a : Abierto', '2022-06-08 13:13:13'),
(17, 1, 2, 'El estado del ticket fue cambiado a : En proceso', '2022-06-08 13:13:25'),
(18, 34, 2, 'AAAAAAAAAAAAAAAAAAAAA', '2022-06-08 13:29:01'),
(19, 35, 2, 'a', '2022-06-08 13:33:07'),
(20, 36, 2, 'aasdasdasdasd', '2022-06-08 13:33:20'),
(21, 37, 2, 'ZZZZZZZZZZZZZZZZZZ', '2022-06-08 13:33:57'),
(22, 38, 2, 'Prende la pc y no reconoce el disco', '2022-06-08 13:36:39'),
(23, 39, 14, 'ASDASDASD', '2022-06-10 11:36:18'),
(24, 40, 15, 'asdasdasdasdasd', '2022-06-10 13:25:28');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `user`
--

CREATE TABLE `user` (
  `id` int(11) NOT NULL,
  `username` varchar(180) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `mail` varchar(180) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `telefono` varchar(15) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `last_login` datetime DEFAULT NULL,
  `roles_id` int(11) NOT NULL,
  `nivel` int(2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

--
-- Volcado de datos para la tabla `user`
--

INSERT INTO `user` (`id`, `username`, `mail`, `password`, `telefono`, `last_login`, `roles_id`, `nivel`) VALUES
(12, 'Roberto', 'rogworksi@gmail.com', '$2y$10$c.Z.4I9AY57OopAJzxA70uHeFGy94wLR.wNMISxBvGIZSQfpXGwQS', '2235812967', '2022-06-10 13:22:33', 2, 9),
(13, 'Pipo', 'pipoworksi@gmail.com', '$2y$10$kpM50GWPz2C4fv42blnVWuQj9uWIlRczllosdrLYycKu0O/EKEhpm', '2234563313', '2022-06-10 13:24:39', 2, 9),
(14, 'Benja', 'oriozabalabenja@gmail.com', '$2y$10$bNSc6Ttg9mABnjH7RngoWeY9GkRcpmboOnoCahw3gSmGIMYe2jArS', '2314501359', '2022-06-10 13:25:51', 2, 9),
(15, 'BrownBolivar', 'brown@bolivar.com', '$2y$10$WCDc8I.6JXkaGRMYv7G0oeqWpjiJboLoD6afxac8X3kfvT3Lm8ODu', '0000000000', '2022-06-10 13:25:07', 2, 6),
(16, 'LavalleBolivar', 'LavalleBolivar@lavalle.com', '$2y$10$Ier2lg.Cw2uqpXJhnP34kOrRfvgP7hR8glz2SBlNMaZsttu6wDYDi', '00000000000', '2022-06-10 13:25:42', 2, 6),
(17, 'ActualLasFlores', 'ActualLasFlores@gmail.com', '$2y$10$repb8SOsYnuKZGAB4dsK7OZTzu0oYpn.YH29wdCFO21t6w82saBWW', '000000000', '2022-06-10 13:21:40', 2, 6),
(18, 'ActualAdministracion', 'ActualAdministracion@gmail.com', '$2y$10$vurB5VyrFVJpMvdGQD.oLusnfyxrnVQLNDOO1T1/86mucYYBo7g1e', '00000000000', '2022-06-10 16:32:04', 2, 6),
(19, 'ActualDx', 'ActualDx@gmail.com', '$2y$10$7DUUD1vJtwvW3Q6gBS670OATQLhJUJf3yWvPneYuJN7i7sWLdU05C', '0000000000', '2022-06-10 16:33:31', 2, 6),
(20, 'Actual9deJulio', 'Actual9deJulio@gmail.com', '$2y$10$tXtHFiWfrTxquU6uJ33N6.dxX8RHxx..xmLFoqfXsWTOuQ2XLFriK', '0000000000', '2022-06-10 11:36:33', 2, 6),
(22, 'ActualDeposito', 'ActualDeposito@gmail.com', '$2y$10$YK7HWfXwkea/tiGdNv36WuEA4wMiQaAdxNZvLxLuSuRc0md61/wJa', '0000000000', '2022-06-10 16:34:52', 2, 6);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `estado`
--
ALTER TABLE `estado`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `nivelesboton`
--
ALTER TABLE `nivelesboton`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `seccion`
--
ALTER TABLE `seccion`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `tablaaux`
--
ALTER TABLE `tablaaux`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `ticket`
--
ALTER TABLE `ticket`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `ticketrenglon`
--
ALTER TABLE `ticketrenglon`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_user_roles` (`roles_id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `estado`
--
ALTER TABLE `estado`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `nivelesboton`
--
ALTER TABLE `nivelesboton`
  MODIFY `id` int(4) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=332;

--
-- AUTO_INCREMENT de la tabla `seccion`
--
ALTER TABLE `seccion`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=74;

--
-- AUTO_INCREMENT de la tabla `tablaaux`
--
ALTER TABLE `tablaaux`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=58;

--
-- AUTO_INCREMENT de la tabla `ticket`
--
ALTER TABLE `ticket`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT de la tabla `ticketrenglon`
--
ALTER TABLE `ticketrenglon`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT de la tabla `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
