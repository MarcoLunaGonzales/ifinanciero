

DROP TABLE IF EXISTS `chat`;
CREATE TABLE `chat` (
  `codigo` int(11) NOT NULL AUTO_INCREMENT,
  `cod_usuario` int(11) DEFAULT NULL,
  `descripcion` varchar(255) COLLATE utf8_spanish2_ci DEFAULT NULL,
  `cod_usuariorecep` int(11) DEFAULT NULL,
  PRIMARY KEY (`codigo`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

#
# Dumping data for table chat
#

INSERT INTO `chat` VALUES (1,0,'Bolivia',0);
INSERT INTO `chat` VALUES (4,4,'David Man Huarina Men -> Sandy ViGu',5);
INSERT INTO `chat` VALUES (5,4,'David Man Huarina Men -> Omar Mendoza',2);
INSERT INTO `chat` VALUES (6,9,'David Huarina -> Elvis Cruz',8);
INSERT INTO `chat` VALUES (7,8,'Elvis Cruz -> David Huarina',1);

#
# Source for table codigos
#

DROP TABLE IF EXISTS `codigos`;
CREATE TABLE `codigos` (
  `codigo` int(11) NOT NULL AUTO_INCREMENT,
  `cod` varchar(255) COLLATE utf8_spanish2_ci DEFAULT NULL,
  `codpar` varchar(255) COLLATE utf8_spanish2_ci DEFAULT NULL,
  `correo` varchar(255) COLLATE utf8_spanish2_ci DEFAULT NULL,
  PRIMARY KEY (`codigo`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

#
# Dumping data for table codigos
#

INSERT INTO `codigos` VALUES (16,'6503','1227','davidhuarina25@gmail.com');

#
# Source for table contactos
#

DROP TABLE IF EXISTS `contactos`;
CREATE TABLE `contactos` (
  `codigo` int(11) NOT NULL AUTO_INCREMENT,
  `cod_usuarioorigen` int(11) DEFAULT NULL,
  `cod_usuario` int(11) DEFAULT NULL,
  PRIMARY KEY (`codigo`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8;

#
# Dumping data for table contactos
#

INSERT INTO `contactos` VALUES (7,5,4);
INSERT INTO `contactos` VALUES (8,1,5);
INSERT INTO `contactos` VALUES (15,4,5);
INSERT INTO `contactos` VALUES (16,4,2);
INSERT INTO `contactos` VALUES (17,9,8);
INSERT INTO `contactos` VALUES (18,8,1);

#
# Source for table mensajes
#

DROP TABLE IF EXISTS `mensajes`;
CREATE TABLE `mensajes` (
  `codigo` int(11) NOT NULL AUTO_INCREMENT,
  `mensaje` text COLLATE utf8_spanish2_ci NOT NULL,
  `fecha` timestamp NOT NULL DEFAULT current_timestamp(),
  `cod_usuario` int(11) DEFAULT NULL,
  `cod_chat` varchar(255) COLLATE utf8_spanish2_ci DEFAULT NULL,
  `leido` int(11) DEFAULT 1,
  PRIMARY KEY (`codigo`)
) ENGINE=InnoDB AUTO_INCREMENT=180 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

#
# Dumping data for table mensajes
#

INSERT INTO `mensajes` VALUES (149,'hola','2020-01-25 20:25:20',9,'1',1);
INSERT INTO `mensajes` VALUES (150,'<img alt=\"?\" class=\"emojioneemoji\" src=\"https://cdn.jsdelivr.net/emojione/assets/3.1/png/32/1f601.png\"><img alt=\"?\" class=\"emojioneemoji\" src=\"https://cdn.jsdelivr.net/emojione/assets/3.1/png/32/1f601.png\">','2020-01-25 20:25:26',9,'1',1);
INSERT INTO `mensajes` VALUES (151,'holaa','2020-01-25 20:30:31',9,'6',0);
INSERT INTO `mensajes` VALUES (152,'apistolero','2020-01-25 20:30:38',9,'6',0);
INSERT INTO `mensajes` VALUES (153,'fuuu','2020-01-25 20:31:24',9,'6',0);
INSERT INTO `mensajes` VALUES (154,'fuuuu','2020-01-25 21:08:18',9,'6',0);
INSERT INTO `mensajes` VALUES (155,'fuuuu','2020-01-25 21:08:27',9,'1',1);
INSERT INTO `mensajes` VALUES (156,'tan alpedo','2020-01-25 21:08:35',9,'1',1);
INSERT INTO `mensajes` VALUES (157,'nove','2020-01-25 21:08:40',9,'1',1);
INSERT INTO `mensajes` VALUES (158,'<img alt=\"?\" class=\"emojioneemoji\" src=\"src/images/icons/tigre_w.png\">','2020-01-25 21:08:54',9,'1',1);
INSERT INTO `mensajes` VALUES (159,'david','2020-01-25 21:13:53',8,'1',1);
INSERT INTO `mensajes` VALUES (160,'david','2020-01-25 21:13:58',8,'1',1);
INSERT INTO `mensajes` VALUES (161,'he','2020-01-25 21:14:01',8,'1',1);
INSERT INTO `mensajes` VALUES (162,'voltea','2020-01-25 21:14:09',8,'1',1);
INSERT INTO `mensajes` VALUES (163,'voltea','2020-01-25 21:14:12',8,'1',1);
INSERT INTO `mensajes` VALUES (164,'voltea','2020-01-25 21:14:16',8,'1',1);
INSERT INTO `mensajes` VALUES (165,'voltea','2020-01-25 21:14:21',8,'1',1);
INSERT INTO `mensajes` VALUES (166,'voltea','2020-01-25 21:14:24',8,'1',1);
INSERT INTO `mensajes` VALUES (167,'voltea','2020-01-25 21:14:26',8,'1',1);
INSERT INTO `mensajes` VALUES (168,'voltea','2020-01-25 21:14:29',8,'1',1);
INSERT INTO `mensajes` VALUES (169,'voltea','2020-01-25 21:14:32',8,'1',1);
INSERT INTO `mensajes` VALUES (170,'volteavolteavolteavolteavolteavolteavolteavolteavolteavolteavolteavolteavolteavolteavolteavolteavolteavolteavolteavolteavolteavolteavolteavolteavolteavolteavolteavolteavolteavolteavolteavolteavolteavolteavolteavolteavolteavolteavolteavolteavolteavolteavolteavolteavolteavolteavolteavolteavolteavolteavolteavolteavolteavolteavolteavolteavolteavolteavolteavolteavolteavolteavolteavolteavolteavolteavolteavolteavolteavolteavolteavolteavolteavolteavolteavolteavolteavolteavolteavolteavolteavolteavolteavolteavoltea','2020-01-25 21:14:38',8,'1',1);
INSERT INTO `mensajes` VALUES (171,'voltea','2020-01-25 21:15:03',8,'1',1);
INSERT INTO `mensajes` VALUES (172,'voltea','2020-01-25 21:15:06',8,'1',1);
INSERT INTO `mensajes` VALUES (173,'voltea','2020-01-25 21:15:09',8,'1',1);
INSERT INTO `mensajes` VALUES (174,'voltea','2020-01-25 21:15:12',8,'1',1);
INSERT INTO `mensajes` VALUES (175,'quee','2020-01-25 21:28:13',9,'1',1);
INSERT INTO `mensajes` VALUES (176,'mmm','2020-02-24 23:55:40',9,'1',1);
INSERT INTO `mensajes` VALUES (177,'wtf','2020-02-29 15:08:24',8,'1',0);
INSERT INTO `mensajes` VALUES (178,'hola','2020-02-29 15:09:21',8,'7',0);
INSERT INTO `mensajes` VALUES (179,'mmm?','2020-02-29 15:09:25',8,'7',0);

#
# Source for table usuarios
#

DROP TABLE IF EXISTS `usuarios`;
CREATE TABLE `usuarios` (
  `codigo` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) COLLATE utf8_spanish2_ci DEFAULT NULL,
  `apellido` varchar(255) COLLATE utf8_spanish2_ci DEFAULT NULL,
  `correo` varchar(255) COLLATE utf8_spanish2_ci DEFAULT NULL,
  `contrasena` varchar(255) COLLATE utf8_spanish2_ci DEFAULT NULL,
  `cod_sexo` int(11) DEFAULT 1,
  `imagen` varchar(255) COLLATE utf8_spanish2_ci DEFAULT NULL,
  `estado` int(11) DEFAULT 0,
  PRIMARY KEY (`codigo`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

#
# Dumping data for table usuarios
#

INSERT INTO `usuarios` VALUES (1,'David','Huarina','davidhuarina250@gmail.com','73082343',1,NULL,1);
INSERT INTO `usuarios` VALUES (2,'Omar','Mendoza','davidhuarinamendoza250@gmail.com','67074505',1,NULL,1);
INSERT INTO `usuarios` VALUES (4,'Men','Doza','davidhuarina24@gmail.com','111',1,'src/images/user.jpg',1);
INSERT INTO `usuarios` VALUES (5,'Sandy','ViGu','davidhuarina23@gmail.com','david',1,'src/images/user.jpg',1);
INSERT INTO `usuarios` VALUES (6,'Isaac','Huarina','@isoto','123',1,NULL,1);
INSERT INTO `usuarios` VALUES (7,'Eduardo','Cruz','@eduardo','123',1,NULL,1);
INSERT INTO `usuarios` VALUES (8,'Elvis','Cruz','@elvis','123',1,NULL,1);
INSERT INTO `usuarios` VALUES (9,'David','Huarina','@davidtron','123',1,NULL,1);

/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
