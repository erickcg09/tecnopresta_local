-- phpMyAdmin SQL Dump
-- version 4.8.4
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost
-- Tiempo de generación: 27-07-2020 a las 05:43:49
-- Versión del servidor: 10.1.37-MariaDB
-- Versión de PHP: 7.3.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `pntm`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `login`
--

CREATE TABLE `login` (
  `id_login` int(11) NOT NULL,
  `pin` varchar(5) NOT NULL,
  `password` varchar(150) NOT NULL,
  `correo` varchar(50) NOT NULL,
  `nombre` varchar(80) NOT NULL,
  `cedula` varchar(20) NOT NULL,
  `tipo` int(4) NOT NULL,
  `codigo` int(4) NOT NULL,
  `soporte` varchar(2) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `login`
--

INSERT INTO `login` (`id_login`, `pin`, `password`, `correo`, `nombre`, `cedula`, `tipo`, `codigo`, `soporte`) VALUES
(1, 'rootx', 'a3b89335ae35b6d205be854fdddc0439fdea4b84', 'sandro.yee.vasquez@mep.go.cr', 'Administrador del Sistema', '000000000', 1, 0, ''),
(8, 'QOPE', 'a3b89335ae35b6d205be854fdddc0439fdea4b84', 'sandro.yee.vasquez@mep.go.cr', 'Sandro Fernando Yee Vásquez', '303460987', 2, 4071, 'Si'),
(12, '0CKI', 'a3b89335ae35b6d205be854fdddc0439fdea4b84', 'sandro.yee.vasquez@mep.go.cr', 'Pablo Perico Palote', '301110111', 2, 4071, 'Si'),
(11, 'PH73', 'a3b89335ae35b6d205be854fdddc0439fdea4b84', 'sandro.yee.vasquez@mep.go.cr', 'Carlos Yee Vasquez', '303460988', 3, 0, 'No');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `t_activo`
--

CREATE TABLE `t_activo` (
  `id_activo` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `id_marca` int(4) NOT NULL,
  `modelo` varchar(50) NOT NULL,
  `id_color` int(4) NOT NULL,
  `imagen` varchar(50) NOT NULL,
  `alias_id` int(11) NOT NULL,
  `numero_activo` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `t_activo`
--

INSERT INTO `t_activo` (`id_activo`, `nombre`, `id_marca`, `modelo`, `id_color`, `imagen`, `alias_id`, `numero_activo`) VALUES
(1, 'Impresora', 1, 'XP3100', 2, 'inventario.png', 1, 1),
(2, 'Proyector', 4, 'WP3520', 1, 'inventario.png', 2, 2),
(3, 'Impresora', 6, 'XP2100', 1, 'inventario.png', 3, 3),
(4, 'Impresora', 7, 'Pixma 720', 4, 'inventario.png', 4, 4),
(5, 'Tableta', 3, 'Kangaro X2', 7, 'inventario.png', 5, 5),
(6, 'Impresora', 2, 'DeskJet 720', 3, 'inventario.png', 6, 6),
(7, 'Impresora', 2, 'DeskJet 650', 4, 'inventario.png', 7, 7),
(8, ' PC Dell OptiPlex ', 1, 'OptiPlex 745', 1, 'inventario.png', 10, 20),
(9, 'Dell Inspiron', 1, 'Inspiron 15 3567', 1, 'inventario.png', 9, 21),
(10, 'PC Dell OptiPlex', 1, 'OptiPlex 745', 1, 'inventario.png', 10, 22),
(11, 'PC Dell OptiPlex', 1, 'OptiPlex 745', 1, 'inventario.png', 10, 23),
(12, 'Dell Inspiron', 1, 'Inspiron 15 3567', 1, 'inventario.png', 9, 25),
(13, 'Dell Inspiron', 1, 'Inspiron 15 3567', 1, 'inventario.png', 9, 26),
(14, 'PC Dell', 1, 'Optiplex 360 Core 2 Duo e7500', 2, 'inventario.png', 11, 30),
(15, 'PC Dell', 1, 'Optiplex 360 Core 2 Duo e7500', 2, 'inventario.png', 11, 31),
(16, 'Parlante Amplificado Ericson', 4, 'Ericson 3000 St 600 Watts', 7, 'inventario.png', 12, 56),
(17, 'Parlante Subwoofer', 5, 'Subwoofer De 500 Watts Rms', 7, 'inventario.png', 12, 57),
(18, 'Parlantes Logitech', 3, 'Z313 2.1 25w(rms) 110v Icbtech', 2, 'inventario.png', 12, 14),
(19, 'Parlantes Logitech Z506', 3, 'Sistema De Altavoces , 150w, 75(rms)', 2, 'inventario.png', 12, 15);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `t_alias`
--

CREATE TABLE `t_alias` (
  `alias_id` int(11) NOT NULL,
  `alias` varchar(255) NOT NULL,
  `alias_imagen` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `t_alias`
--

INSERT INTO `t_alias` (`alias_id`, `alias`, `alias_imagen`) VALUES
(1, 'Impresora XP3100\r\n', 'inventario.png'),
(2, 'Proyector WP3520\r\n', 'inventario.png'),
(3, 'Impresora XP2100\r\n', 'inventario.png'),
(4, 'Impresora Pixma 720\r\n', 'inventario.png'),
(5, 'Tableta	Kangaro X2', 'inventario.png'),
(6, 'Impresora DeskJet 720', 'inventario.png'),
(7, 'Impresora DeskJet 650\r\n', 'inventario.png'),
(9, 'Laptop Dell BilioCra', 'inventario.png'),
(10, 'PC Laboratorio Innovaciones', 'inventario.png'),
(11, 'PC Dell Laboratorio Inglés', 'inventario.png'),
(12, 'Parlantes', 'inventario.png');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `t_boleta`
--

CREATE TABLE `t_boleta` (
  `id_boleta` varchar(50) NOT NULL,
  `codigo_pre` int(11) NOT NULL,
  `cedula` varchar(20) NOT NULL,
  `fecha_s` date NOT NULL,
  `fecha_d` date NOT NULL,
  `hora_i` time NOT NULL,
  `hora_f` time NOT NULL,
  `uso_educativo` int(4) NOT NULL,
  `destino_equipo` int(4) NOT NULL,
  `comentario` text NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `t_boleta`
--

INSERT INTO `t_boleta` (`id_boleta`, `codigo_pre`, `cedula`, `fecha_s`, `fecha_d`, `hora_i`, `hora_f`, `uso_educativo`, `destino_equipo`, `comentario`) VALUES
('PNTM-0001-4071-2020', 4071, '303460987', '2020-05-07', '2020-05-07', '13:00:00', '14:00:00', 2, 2, 'Nada'),
('PNTM-0002-4071-2020', 4071, '303460987', '2020-05-07', '2020-05-07', '15:00:00', '16:00:00', 1, 1, 'Nada de nada'),
('PNTM-0003-4040-2020', 4040, '301110111', '2020-05-07', '2020-05-07', '07:00:00', '08:00:00', 1, 1, 'De todo'),
('PNTM-0004-4071-2020', 4071, '303460987', '2020-05-07', '2020-05-07', '08:00:00', '09:00:00', 2, 2, 'Nada'),
('PNTM-0005-4071-2020', 4071, '303460987', '2020-05-21', '2020-05-21', '12:00:00', '13:00:00', 2, 2, 'NR');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `t_color`
--

CREATE TABLE `t_color` (
  `id_color` int(4) NOT NULL,
  `color` varchar(30) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `t_color`
--

INSERT INTO `t_color` (`id_color`, `color`) VALUES
(1, 'Blanco'),
(2, 'Negro'),
(3, 'Verde'),
(4, 'Rojo'),
(5, 'Amarillo'),
(6, 'Gris'),
(7, 'Marrón'),
(8, 'Azul'),
(9, 'Plateado'),
(10, 'Dorado'),
(11, 'Naranja'),
(12, 'Rosado'),
(13, 'Morado'),
(14, 'Beis'),
(15, 'Turquesa');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `t_destino`
--

CREATE TABLE `t_destino` (
  `id_destino` int(4) NOT NULL,
  `destino` varchar(50) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `t_destino`
--

INSERT INTO `t_destino` (`id_destino`, `destino`) VALUES
(1, 'Sala de Innovación 1'),
(2, 'Laboratio de Inglés INCO');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `t_disponible`
--

CREATE TABLE `t_disponible` (
  `alias_id` int(11) NOT NULL,
  `disponible_cantidad` int(11) NOT NULL,
  `disponible_prestado` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `t_disponible`
--

INSERT INTO `t_disponible` (`alias_id`, `disponible_cantidad`, `disponible_prestado`) VALUES
(1, 1, 0),
(2, 1, 0),
(3, 1, 0),
(4, 1, 0),
(5, 1, 0),
(6, 1, 0),
(7, 1, 0),
(8, 1, 0),
(9, 3, 0),
(10, 3, 0),
(11, 2, 0),
(12, 4, 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `t_estado`
--

CREATE TABLE `t_estado` (
  `id_estado` int(4) NOT NULL,
  `estado` varchar(15) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `t_estado`
--

INSERT INTO `t_estado` (`id_estado`, `estado`) VALUES
(1, 'Bueno'),
(2, 'Regular'),
(3, 'Malo');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `t_marca`
--

CREATE TABLE `t_marca` (
  `id_marca` int(11) NOT NULL,
  `marca` varchar(50) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `t_marca`
--

INSERT INTO `t_marca` (`id_marca`, `marca`) VALUES
(1, 'Dell'),
(2, 'HP'),
(3, 'Lenovo'),
(4, 'dji'),
(5, 'Lexmark'),
(6, 'Epson'),
(7, 'Canon');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `t_padron`
--

CREATE TABLE `t_padron` (
  `cedula` int(9) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `apellidop` varchar(50) NOT NULL,
  `apellidom` varchar(50) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `t_padron`
--

INSERT INTO `t_padron` (`cedula`, `nombre`, `apellidop`, `apellidom`) VALUES
(303460987, 'Sandro Fernando', 'Yee', 'Vásquez'),
(101110222, 'Franklin', 'Jimenez', 'Montero'),
(303460988, 'Carlos', 'Yee', 'Vasquez'),
(301110111, 'Pablo', 'Perico', 'Palote');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `t_placa`
--

CREATE TABLE `t_placa` (
  `id_placa` int(11) NOT NULL,
  `placa` varchar(50) NOT NULL,
  `serial` varchar(50) NOT NULL,
  `id_activo` int(11) NOT NULL,
  `codigo` int(4) NOT NULL,
  `id_estado` int(4) NOT NULL,
  `prestar` int(4) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `t_placa`
--

INSERT INTO `t_placa` (`id_placa`, `placa`, `serial`, `id_activo`, `codigo`, `id_estado`, `prestar`) VALUES
(1, '123', '321', 5, 4071, 3, 1),
(2, 'qw1', 'qw1', 4, 4071, 1, 1),
(3, 'we1', 'we1', 4, 4071, 1, 1),
(4, 'rd14', 'rd14', 4, 4071, 1, 1),
(5, 'qa', 'aq', 4, 4071, 1, 1),
(6, 'sd', 'ds', 4, 4071, 1, 1),
(7, 'fr', 'rf', 4, 4071, 1, 1),
(8, 'lol', 'lol', 5, 4071, 1, 1),
(9, 'LOL', 'LOL', 5, 4071, 1, 1),
(10, 'qwwq', 'qwwqw', 1, 4071, 1, 1),
(11, 'qwqeeee', 'qwqwqweee', 1, 4071, 1, 1),
(12, 'rtryryry', '47848574875847', 3, 4071, 1, 1),
(13, 'ryeureuiruiwe', 'rererwrwerwer', 3, 4071, 1, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `t_prestamo`
--

CREATE TABLE `t_prestamo` (
  `prestamo_Id` int(11) NOT NULL,
  `prestamo_fecha` date NOT NULL,
  `prestamo_fechaDevolucion` date NOT NULL,
  `prestamo_fechaRetiro` date NOT NULL,
  `seccion_Id` int(11) NOT NULL,
  `software_Id` int(11) NOT NULL,
  `prestamo_uso` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `t_prestamo`
--

INSERT INTO `t_prestamo` (`prestamo_Id`, `prestamo_fecha`, `prestamo_fechaDevolucion`, `prestamo_fechaRetiro`, `seccion_Id`, `software_Id`, `prestamo_uso`) VALUES
(1, '2020-06-25', '2020-06-25', '2020-06-25', 1, 1, 'prueba de uso 1'),
(2, '2020-06-26', '2020-06-26', '2020-06-26', 2, 2, 'uso de prueba 2'),
(3, '2020-06-28', '2020-06-28', '2020-06-28', 3, 3, 'prueba de uso 3'),
(4, '2020-06-27', '2020-06-27', '2020-06-27', 4, 4, 'uso de prueba 4');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `t_prestamo_detalle`
--

CREATE TABLE `t_prestamo_detalle` (
  `prestamo_Id` int(11) NOT NULL,
  `prestamo_detalle_Id` int(11) NOT NULL,
  `prestamo_detalle_id_activo` int(11) NOT NULL,
  `prestamo_detalle_devuelto` tinyint(1) NOT NULL,
  `prestamo_detalle_irregularidad` tinyint(1) NOT NULL,
  `prestamo_detalle_observacion` varchar(255) NOT NULL,
  `prestamo_detalle_fechaDevolucion` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `t_prestamo_detalle`
--

INSERT INTO `t_prestamo_detalle` (`prestamo_Id`, `prestamo_detalle_Id`, `prestamo_detalle_id_activo`, `prestamo_detalle_devuelto`, `prestamo_detalle_irregularidad`, `prestamo_detalle_observacion`, `prestamo_detalle_fechaDevolucion`) VALUES
(1, 1, 1, 0, 0, '', NULL),
(2, 2, 2, 1, 0, '', '2020-06-25'),
(3, 3, 3, 0, 0, '', NULL),
(2, 4, 4, 1, 1, 'irregularida prueba', '2020-06-30');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `t_prestamo_solicitud`
--

CREATE TABLE `t_prestamo_solicitud` (
  `prestamo_Id` int(11) NOT NULL,
  `solicitud_Id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `t_seccion`
--

CREATE TABLE `t_seccion` (
  `seccion_Id` int(11) NOT NULL,
  `seccion_Cantidad` int(11) NOT NULL,
  `seccion_Descripcion` varchar(50) NOT NULL,
  `seccion_Nivel` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `t_seccion`
--

INSERT INTO `t_seccion` (`seccion_Id`, `seccion_Cantidad`, `seccion_Descripcion`, `seccion_Nivel`) VALUES
(1, 10, '7-1', 7),
(2, 15, '8-1', 8),
(3, 11, '9-1', 9),
(4, 18, '10-1', 10);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `t_softwareEducativo`
--

CREATE TABLE `t_softwareEducativo` (
  `software_Id` int(11) NOT NULL,
  `software_Descripcion` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `t_softwareEducativo`
--

INSERT INTO `t_softwareEducativo` (`software_Id`, `software_Descripcion`) VALUES
(1, 'Arduino'),
(2, 'Geogebra'),
(3, 'WPS Office'),
(4, 'Youtube');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `t_solicitud`
--

CREATE TABLE `t_solicitud` (
  `solicitud_Id` int(11) NOT NULL,
  `solicitud_fecha` date NOT NULL,
  `solicitud_fechaRetiro` date NOT NULL,
  `solicitud_fechaDevolucion` date NOT NULL,
  `solicitud_cantidad` int(11) NOT NULL,
  `alias_Id` int(11) NOT NULL,
  `seccion_Id` int(11) NOT NULL,
  `software_Id` int(11) NOT NULL,
  `solicitud_uso` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `t_solicitud`
--

INSERT INTO `t_solicitud` (`solicitud_Id`, `solicitud_fecha`, `solicitud_fechaRetiro`, `solicitud_fechaDevolucion`, `solicitud_cantidad`, `alias_Id`, `seccion_Id`, `software_Id`, `solicitud_uso`) VALUES
(1, '0000-00-00', '2020-06-29', '2020-06-30', 1, 1, 0, 0, ''),
(2, '0000-00-00', '2020-06-28', '2020-06-29', 1, 2, 0, 0, ''),
(3, '0000-00-00', '2020-06-16', '2020-06-30', 1, 12, 0, 0, ''),
(4, '0000-00-00', '0000-00-00', '2020-06-29', 1, 11, 0, 0, ''),
(5, '0000-00-00', '2020-06-11', '2020-06-12', 1, 3, 0, 0, ''),
(6, '0000-00-00', '2020-06-15', '2020-06-16', 1, 10, 0, 0, '');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `t_uso`
--

CREATE TABLE `t_uso` (
  `id_uso` int(4) NOT NULL,
  `uso` varchar(50) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `t_uso`
--

INSERT INTO `t_uso` (`id_uso`, `uso`) VALUES
(1, 'Taller de inglés conversacional'),
(2, 'Práctica de tema');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `login`
--
ALTER TABLE `login`
  ADD PRIMARY KEY (`id_login`);

--
-- Indices de la tabla `t_activo`
--
ALTER TABLE `t_activo`
  ADD PRIMARY KEY (`id_activo`),
  ADD KEY `alias_id` (`alias_id`);

--
-- Indices de la tabla `t_alias`
--
ALTER TABLE `t_alias`
  ADD PRIMARY KEY (`alias_id`),
  ADD KEY `alias` (`alias`);

--
-- Indices de la tabla `t_boleta`
--
ALTER TABLE `t_boleta`
  ADD PRIMARY KEY (`id_boleta`);

--
-- Indices de la tabla `t_color`
--
ALTER TABLE `t_color`
  ADD PRIMARY KEY (`id_color`);

--
-- Indices de la tabla `t_destino`
--
ALTER TABLE `t_destino`
  ADD PRIMARY KEY (`id_destino`);

--
-- Indices de la tabla `t_disponible`
--
ALTER TABLE `t_disponible`
  ADD PRIMARY KEY (`alias_id`) USING BTREE;

--
-- Indices de la tabla `t_estado`
--
ALTER TABLE `t_estado`
  ADD PRIMARY KEY (`id_estado`);

--
-- Indices de la tabla `t_marca`
--
ALTER TABLE `t_marca`
  ADD PRIMARY KEY (`id_marca`);

--
-- Indices de la tabla `t_padron`
--
ALTER TABLE `t_padron`
  ADD PRIMARY KEY (`cedula`);

--
-- Indices de la tabla `t_placa`
--
ALTER TABLE `t_placa`
  ADD PRIMARY KEY (`id_placa`);

--
-- Indices de la tabla `t_prestamo`
--
ALTER TABLE `t_prestamo`
  ADD PRIMARY KEY (`prestamo_Id`),
  ADD KEY `indSeccion` (`seccion_Id`),
  ADD KEY `indSoftware` (`software_Id`);

--
-- Indices de la tabla `t_prestamo_detalle`
--
ALTER TABLE `t_prestamo_detalle`
  ADD UNIQUE KEY `prestamo_detalle_Id` (`prestamo_detalle_Id`),
  ADD UNIQUE KEY `indPrincipal` (`prestamo_Id`,`prestamo_detalle_Id`),
  ADD KEY `prestamo_Id` (`prestamo_Id`),
  ADD KEY `indArticuloPrestamo` (`prestamo_detalle_id_activo`,`prestamo_detalle_devuelto`);

--
-- Indices de la tabla `t_prestamo_solicitud`
--
ALTER TABLE `t_prestamo_solicitud`
  ADD UNIQUE KEY `infPrestamoSolicitud` (`prestamo_Id`,`solicitud_Id`);

--
-- Indices de la tabla `t_seccion`
--
ALTER TABLE `t_seccion`
  ADD PRIMARY KEY (`seccion_Id`);

--
-- Indices de la tabla `t_softwareEducativo`
--
ALTER TABLE `t_softwareEducativo`
  ADD PRIMARY KEY (`software_Id`);

--
-- Indices de la tabla `t_solicitud`
--
ALTER TABLE `t_solicitud`
  ADD PRIMARY KEY (`solicitud_Id`),
  ADD KEY `fechaDevolucion` (`solicitud_fechaDevolucion`),
  ADD KEY `alias_Id` (`alias_Id`),
  ADD KEY `fechaRetiro` (`solicitud_fechaRetiro`),
  ADD KEY `indSeccion` (`seccion_Id`),
  ADD KEY `indSoftware` (`software_Id`);

--
-- Indices de la tabla `t_uso`
--
ALTER TABLE `t_uso`
  ADD PRIMARY KEY (`id_uso`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `login`
--
ALTER TABLE `login`
  MODIFY `id_login` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT de la tabla `t_activo`
--
ALTER TABLE `t_activo`
  MODIFY `id_activo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT de la tabla `t_alias`
--
ALTER TABLE `t_alias`
  MODIFY `alias_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT de la tabla `t_color`
--
ALTER TABLE `t_color`
  MODIFY `id_color` int(4) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT de la tabla `t_destino`
--
ALTER TABLE `t_destino`
  MODIFY `id_destino` int(4) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `t_estado`
--
ALTER TABLE `t_estado`
  MODIFY `id_estado` int(4) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `t_marca`
--
ALTER TABLE `t_marca`
  MODIFY `id_marca` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `t_placa`
--
ALTER TABLE `t_placa`
  MODIFY `id_placa` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT de la tabla `t_prestamo`
--
ALTER TABLE `t_prestamo`
  MODIFY `prestamo_Id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `t_prestamo_detalle`
--
ALTER TABLE `t_prestamo_detalle`
  MODIFY `prestamo_detalle_Id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `t_seccion`
--
ALTER TABLE `t_seccion`
  MODIFY `seccion_Id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `t_softwareEducativo`
--
ALTER TABLE `t_softwareEducativo`
  MODIFY `software_Id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `t_solicitud`
--
ALTER TABLE `t_solicitud`
  MODIFY `solicitud_Id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `t_uso`
--
ALTER TABLE `t_uso`
  MODIFY `id_uso` int(4) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
