CREATE TABLE `lineas` (
  `id_linea` int(11) PRIMARY KEY AUTO_INCREMENT,
  `linea` text,
  `rfc` text,
  `razon_social` text,
  `troncal` int(11)
);

CREATE TABLE `llamadas` (
  `id_llamada` int(11) PRIMARY KEY AUTO_INCREMENT,
  `id_cdr` int(11),
  `origen` text,
  `destino` text,
  `poblacion_destino` text,
  `fecha` date,
  `duracion` int(11),
  `monto_final` float,
  `tarifa_base` float,
  `tipo_trafico` text,
  `tipo_tel_destino` text,
  `rfc` text,
  `razon_social` text
);

CREATE TABLE `full_llamadas` (
  `id_llamada` int(11) PRIMARY KEY AUTO_INCREMENT,
  `id_cdr` int(11),
  `origen` text,
  `destino` text,
  `poblacion_destino` text,
  `fecha` date,
  `duracion` int(11),
  `monto_final` float,
  `tarifa_base` float,
  `tipo_trafico` text,
  `tipo_tel_destino` text,
  `rfc` text,
  `razon_social` text
);

CREATE TABLE `tipo_usuario` (
  `id_tipo_usuario` int(11) PRIMARY KEY AUTO_INCREMENT,
  `tipo_usuario` varchar(50)
);

INSERT INTO `tipo_usuario` (`id_tipo_usuario`, `tipo_usuario`) VALUES
(1, 'Super Administrador'),
(2, 'Administrador'),
(3, 'Normal');

CREATE TABLE `troncales` (
  `id_troncal` int(11) PRIMARY KEY AUTO_INCREMENT,
  `troncal` int(11),
  `rfc` text DEFAULT NULL
);

CREATE TABLE `usuarios` (
  `id_usuario` int(11) PRIMARY KEY AUTO_INCREMENT,
  `usu_nombres` varchar(50),
  `usu_apaterno` varchar(255),
  `usu_amaterno` varchar(100),
  `usu_telefono` varchar(100),
  `usu_email` varchar(100),
  `usu_password` varchar(100),
  `usu_fecha_creacion` datetime,
  `usu_foto` varchar(100),
  `id_tipo_usuario` int(11)
);

INSERT INTO `usuarios` (`id_usuario`, `usu_nombres`, `usu_apaterno`, `usu_amaterno`, `usu_telefono`, `usu_email`, `usu_password`, `usu_fecha_creacion`, `usu_foto`, `id_tipo_usuario`) VALUES
(1, 'Francisco Javier', 'Ferruzca', 'Rojas', '4423117551', 'admin@admin.com', '32a346820d1792eb66d8fb834539d048', '2020-07-23 10:29:58', 'prueba', 1),
(2, 'Luisa', 'Alvarez', 'Campos', '12345', 'lalvarez@abacom.com.mx', '32a346820d1792eb66d8fb834539d048', '2020-07-23 10:29:58', 'prueba foto', 1),
(3, 'Miguel', 'Infante', 'Barriga', '12345', 'minfante@abacom.com.mx', '32a346820d1792eb66d8fb834539d048', '2020-07-23 10:29:58', 'prueba foto', 1);