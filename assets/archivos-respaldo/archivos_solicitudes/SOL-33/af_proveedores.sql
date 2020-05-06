# SQL-Front 5.1  (Build 4.16)

/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE */;
/*!40101 SET SQL_MODE='NO_ENGINE_SUBSTITUTION' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES */;
/*!40103 SET SQL_NOTES='ON' */;


# Host: www.minkasoftware.com    Database: ibno_conta
# ------------------------------------------------------
# Server version 5.6.44-cll-lve

#
# Source for table af_proveedores
#

DROP TABLE IF EXISTS `af_proveedores`;
CREATE TABLE `af_proveedores` (
  `codigo` int(11) NOT NULL AUTO_INCREMENT,
  `cod_empresa` int(11) DEFAULT '0',
  `nombre` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `created_by` varchar(500) DEFAULT NULL,
  `modified_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `modified_by` varchar(500) DEFAULT NULL,
  `direccion` varchar(500) DEFAULT NULL,
  `telefono` varchar(50) DEFAULT NULL,
  `email` varchar(50) DEFAULT NULL,
  `personacontacto` varchar(250) DEFAULT NULL,
  `email_personacontacto` varchar(255) DEFAULT NULL,
  `cod_estado` int(11) NOT NULL,
  PRIMARY KEY (`codigo`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;

#
# Dumping data for table af_proveedores
#

INSERT INTO `af_proveedores` VALUES (2,1,'PROVEEDOR DE COMPUTADORAS','2019-09-29 16:44:28','1','2019-11-26 08:18:03','1','CALLE SANTA CRUZ 1234','2649789','MIPROV@PROV.COM','CARLOS SANCHEZ',NULL,1);
INSERT INTO `af_proveedores` VALUES (3,1,'PROVEEDOR DE MUEBLES','2019-10-02 08:13:11','1','2019-11-26 08:18:15','1','SIN DIRECCION','0','PRUEBA@GMAIL.COM','JUAN PEREZ',NULL,1);

/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
