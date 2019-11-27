# SQL-Front 5.1  (Build 4.16)

/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE */;
/*!40101 SET SQL_MODE='NO_ZERO_IN_DATE,NO_ZERO_DATE,NO_ENGINE_SUBSTITUTION' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES */;
/*!40103 SET SQL_NOTES='ON' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS */;
/*!40014 SET FOREIGN_KEY_CHECKS=0 */;


# Host: localhost    Database: ibnorca3
# ------------------------------------------------------
# Server version 5.5.5-10.4.8-MariaDB

DROP DATABASE IF EXISTS `ibnorca3`;
CREATE DATABASE `ibnorca3` /*!40100 DEFAULT CHARACTER SET utf8 */;
USE `ibnorca3`;

#
# Source for table activofijos_asignaciones
#

DROP TABLE IF EXISTS `activofijos_asignaciones`;
CREATE TABLE `activofijos_asignaciones` (
  `codigo` int(11) NOT NULL AUTO_INCREMENT,
  `cod_activosfijos` int(11) DEFAULT NULL,
  `fechaasignacion` varchar(255) DEFAULT NULL,
  `cod_ubicaciones` int(11) DEFAULT NULL,
  `cod_personal` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `created_by` varchar(500) DEFAULT NULL,
  `modified_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `modified_by` varchar(500) DEFAULT NULL,
  PRIMARY KEY (`codigo`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

#
# Dumping data for table activofijos_asignaciones
#

LOCK TABLES `activofijos_asignaciones` WRITE;
/*!40000 ALTER TABLE `activofijos_asignaciones` DISABLE KEYS */;
/*!40000 ALTER TABLE `activofijos_asignaciones` ENABLE KEYS */;
UNLOCK TABLES;

#
# Source for table activosfijos
#

DROP TABLE IF EXISTS `activosfijos`;
CREATE TABLE `activosfijos` (
  `codigo` int(11) NOT NULL AUTO_INCREMENT,
  `codigoactivo` varchar(255) DEFAULT NULL,
  `tipoalta` varchar(10) DEFAULT NULL,
  `fechalta` date DEFAULT NULL,
  `indiceufv` decimal(16,6) DEFAULT NULL,
  `tipocambio` decimal(12,2) DEFAULT NULL,
  `moneda` int(1) DEFAULT NULL,
  `valorinicial` decimal(12,2) DEFAULT NULL,
  `depreciacionacumulada` decimal(12,2) DEFAULT NULL,
  `valorresidual` decimal(12,2) DEFAULT NULL,
  `cod_depreciaciones` int(11) DEFAULT NULL,
  `cod_tiposbienes` int(11) DEFAULT NULL,
  `vidautilmeses` int(11) DEFAULT NULL,
  `estadobien` varchar(20) DEFAULT NULL,
  `otrodato` varchar(255) DEFAULT NULL,
  `cod_ubicaciones` int(11) DEFAULT NULL,
  `cod_empresa` int(11) DEFAULT NULL,
  `activo` varchar(255) DEFAULT NULL,
  `cod_responsables_responsable` int(11) DEFAULT NULL,
  `cod_responsables_autorizadopor` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `created_by` varchar(500) DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  `modified_by` varchar(500) DEFAULT NULL,
  `vidautilmeses_restante` int(11) DEFAULT NULL,
  `cod_af_proveedores` int(11) DEFAULT NULL,
  `numerofactura` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`codigo`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

#
# Dumping data for table activosfijos
#

LOCK TABLES `activosfijos` WRITE;
/*!40000 ALTER TABLE `activosfijos` DISABLE KEYS */;
/*!40000 ALTER TABLE `activosfijos` ENABLE KEYS */;
UNLOCK TABLES;

#
# Source for table af_proveedores
#

DROP TABLE IF EXISTS `af_proveedores`;
CREATE TABLE `af_proveedores` (
  `codigo` int(11) NOT NULL AUTO_INCREMENT,
  `cod_empresa` int(11) DEFAULT 0,
  `nombre` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `created_by` varchar(500) DEFAULT NULL,
  `modified_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `modified_by` varchar(500) DEFAULT NULL,
  PRIMARY KEY (`codigo`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

#
# Dumping data for table af_proveedores
#

LOCK TABLES `af_proveedores` WRITE;
/*!40000 ALTER TABLE `af_proveedores` DISABLE KEYS */;
INSERT INTO `af_proveedores` VALUES (1,1,'PROVEEDOR 1','2019-09-12 20:07:45','1','2019-09-12 20:07:45','1');
/*!40000 ALTER TABLE `af_proveedores` ENABLE KEYS */;
UNLOCK TABLES;

#
# Source for table areas
#

DROP TABLE IF EXISTS `areas`;
CREATE TABLE `areas` (
  `codigo` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) DEFAULT NULL,
  `abreviatura` varchar(20) DEFAULT NULL,
  `cod_estado` int(11) DEFAULT NULL,
  `centro_costos` int(11) DEFAULT NULL,
  PRIMARY KEY (`codigo`)
) ENGINE=InnoDB AUTO_INCREMENT=1236 DEFAULT CHARSET=latin1;

#
# Dumping data for table areas
#

LOCK TABLES `areas` WRITE;
/*!40000 ALTER TABLE `areas` DISABLE KEYS */;
INSERT INTO `areas` VALUES (11,'ORGANISMOS DE INSPECCION','OI',1,1);
INSERT INTO `areas` VALUES (12,'NORMALIZACION','NO',1,1);
INSERT INTO `areas` VALUES (13,'SERVICIOS EXTERNOS Y CAPACITACION','SEC',1,1);
INSERT INTO `areas` VALUES (14,'POLITICAS Y RELACIONES INTERNACIONALES','PR',1,0);
INSERT INTO `areas` VALUES (15,'DIRECCION NACIONAL','DN',1,0);
INSERT INTO `areas` VALUES (38,'CERTIFICACION DE SISTEMAS','TCS',1,1);
INSERT INTO `areas` VALUES (39,'CERTIFICACION DE PRODUCTO','TCP',1,1);
INSERT INTO `areas` VALUES (40,'LABORATORIO','TLQ',1,1);
INSERT INTO `areas` VALUES (78,'DIRECCION REGIONAL','DR',1,0);
INSERT INTO `areas` VALUES (137,'NORMATECA','CD',1,0);
INSERT INTO `areas` VALUES (273,'DIRECCION NACIONAL DE ADMINISTRACION Y FINANZAS','DNAF',1,0);
INSERT INTO `areas` VALUES (826,'TECNOLOGIA DE INFORMACION','TI',1,0);
INSERT INTO `areas` VALUES (846,'DIRECCION ASESORIA GENERAL','DAS',1,0);
INSERT INTO `areas` VALUES (847,'DIRECCION EJECUTIVA','DE',1,0);
INSERT INTO `areas` VALUES (871,'GESTION ESTRATEGICA','GES',1,0);
INSERT INTO `areas` VALUES (872,'DIRECCION NACIONAL DE SERVICIOS','DNS',1,0);
INSERT INTO `areas` VALUES (873,'CERTIFICACION','CER',1,0);
INSERT INTO `areas` VALUES (874,'SERVICIOS ADMINISTRATIVOS','SA',1,0);
INSERT INTO `areas` VALUES (1200,'DNS - GESTION INTEGRADA','GI',1,0);
INSERT INTO `areas` VALUES (1235,'PROYECTO SIS','SIS',1,0);
/*!40000 ALTER TABLE `areas` ENABLE KEYS */;
UNLOCK TABLES;

#
# Source for table bancos
#

DROP TABLE IF EXISTS `bancos`;
CREATE TABLE `bancos` (
  `codigo` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) DEFAULT NULL,
  `abreviatura` varchar(50) DEFAULT NULL,
  `cod_estadoreferencial` int(11) DEFAULT NULL,
  PRIMARY KEY (`codigo`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=latin1;

#
# Dumping data for table bancos
#

LOCK TABLES `bancos` WRITE;
/*!40000 ALTER TABLE `bancos` DISABLE KEYS */;
INSERT INTO `bancos` VALUES (1,'BANCO NACIONAL DE BOLIVIA','BNB',1);
INSERT INTO `bancos` VALUES (2,'BANCO BISA','BISA',1);
INSERT INTO `bancos` VALUES (3,'BANCO GANADERO','GAN',1);
INSERT INTO `bancos` VALUES (4,'BANCO MERCANTIL SANTA CRUZ','MSC',1);
INSERT INTO `bancos` VALUES (5,'BANCO DE CREDITO','BCP',1);
/*!40000 ALTER TABLE `bancos` ENABLE KEYS */;
UNLOCK TABLES;

#
# Source for table comprobantes
#

DROP TABLE IF EXISTS `comprobantes`;
CREATE TABLE `comprobantes` (
  `codigo` int(11) NOT NULL,
  `cod_empresa` int(11) DEFAULT NULL,
  `cod_unidadorganizacional` int(11) DEFAULT NULL,
  `cod_gestion` int(11) DEFAULT NULL,
  `cod_moneda` int(11) DEFAULT NULL,
  `cod_estadocomprobante` int(11) DEFAULT NULL,
  `cod_tipocomprobante` int(11) DEFAULT NULL,
  `fecha` datetime DEFAULT NULL,
  `numero` int(11) DEFAULT NULL,
  `numero_cheque` varchar(255) DEFAULT NULL,
  `numero_factura` varchar(255) DEFAULT NULL,
  `glosa` text DEFAULT NULL,
  `cod_emisioncheque` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`codigo`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

#
# Dumping data for table comprobantes
#

LOCK TABLES `comprobantes` WRITE;
/*!40000 ALTER TABLE `comprobantes` DISABLE KEYS */;
INSERT INTO `comprobantes` VALUES (1,1,2,2019,1,1,1,'2019-08-26 17:18:53',1,NULL,NULL,'ingreso tcp',NULL,'2019-08-26 17:18:53',90,'2019-08-26 17:18:53',90);
INSERT INTO `comprobantes` VALUES (2,1,2,2019,1,1,1,'2019-10-29 01:04:43',2,NULL,NULL,'test',NULL,'2019-10-29 01:04:43',90,'2019-10-29 01:04:43',90);
INSERT INTO `comprobantes` VALUES (3,1,2,2019,1,1,2,'2019-11-07 17:02:16',1,NULL,NULL,'glosa de prueba',NULL,'2019-11-07 17:02:16',90,'2019-11-07 17:02:16',90);
/*!40000 ALTER TABLE `comprobantes` ENABLE KEYS */;
UNLOCK TABLES;

#
# Source for table comprobantes_detalle
#

DROP TABLE IF EXISTS `comprobantes_detalle`;
CREATE TABLE `comprobantes_detalle` (
  `codigo` int(11) NOT NULL AUTO_INCREMENT,
  `cod_comprobante` int(11) NOT NULL,
  `cod_cuenta` int(11) NOT NULL,
  `cod_cuentaauxiliar` int(11) DEFAULT NULL,
  `cod_unidadorganizacional` int(11) NOT NULL,
  `cod_area` int(11) NOT NULL,
  `debe` double DEFAULT NULL,
  `haber` double DEFAULT NULL,
  `glosa` text DEFAULT NULL,
  `monto` double DEFAULT NULL,
  `orden` int(11) DEFAULT NULL,
  PRIMARY KEY (`codigo`,`cod_comprobante`,`cod_cuenta`,`cod_unidadorganizacional`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=latin1;

#
# Dumping data for table comprobantes_detalle
#

LOCK TABLES `comprobantes_detalle` WRITE;
/*!40000 ALTER TABLE `comprobantes_detalle` DISABLE KEYS */;
INSERT INTO `comprobantes_detalle` VALUES (1,1,18,NULL,0,3,1000,0,'FAC 10 BANCO BISA',NULL,1);
INSERT INTO `comprobantes_detalle` VALUES (2,1,66,NULL,0,3,0,1000,'SEC F/44 HANSA LTDA pago Curso Auditor Lider ISO 9001:2015                                                                                                                                                                                                   ',NULL,2);
INSERT INTO `comprobantes_detalle` VALUES (3,2,18,NULL,1,36,100,0,'testdetalle',NULL,1);
INSERT INTO `comprobantes_detalle` VALUES (4,2,440,NULL,5,2,0,100,'',NULL,2);
INSERT INTO `comprobantes_detalle` VALUES (5,3,441,0,4,36,60,0,'detalle de prueba',NULL,1);
INSERT INTO `comprobantes_detalle` VALUES (6,3,441,0,5,36,45,0,'detalle de prueba',NULL,2);
INSERT INTO `comprobantes_detalle` VALUES (7,3,441,0,6,36,120,0,'detalle de prueba',NULL,3);
INSERT INTO `comprobantes_detalle` VALUES (8,3,441,0,2,36,30,0,'detalle de prueba',NULL,4);
INSERT INTO `comprobantes_detalle` VALUES (9,3,441,0,2,36,15,0,'detalle de prueba',NULL,5);
INSERT INTO `comprobantes_detalle` VALUES (10,3,441,0,2,36,15,0,'detalle de prueba',NULL,6);
INSERT INTO `comprobantes_detalle` VALUES (11,3,441,0,2,36,15,0,'detalle de prueba',NULL,7);
INSERT INTO `comprobantes_detalle` VALUES (12,3,441,0,4,1,0,300,'detalle',NULL,8);
/*!40000 ALTER TABLE `comprobantes_detalle` ENABLE KEYS */;
UNLOCK TABLES;

#
# Source for table configuracion_cuentas
#

DROP TABLE IF EXISTS `configuracion_cuentas`;
CREATE TABLE `configuracion_cuentas` (
  `codigo` int(11) NOT NULL,
  `nivel` int(11) DEFAULT NULL,
  `cantidad_digitos` int(11) DEFAULT NULL,
  PRIMARY KEY (`codigo`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 PACK_KEYS=0;

#
# Dumping data for table configuracion_cuentas
#

LOCK TABLES `configuracion_cuentas` WRITE;
/*!40000 ALTER TABLE `configuracion_cuentas` DISABLE KEYS */;
INSERT INTO `configuracion_cuentas` VALUES (1,1,1);
INSERT INTO `configuracion_cuentas` VALUES (2,2,2);
INSERT INTO `configuracion_cuentas` VALUES (3,3,2);
INSERT INTO `configuracion_cuentas` VALUES (4,4,2);
INSERT INTO `configuracion_cuentas` VALUES (5,5,3);
/*!40000 ALTER TABLE `configuracion_cuentas` ENABLE KEYS */;
UNLOCK TABLES;

#
# Source for table cuentas_auxiliares
#

DROP TABLE IF EXISTS `cuentas_auxiliares`;
CREATE TABLE `cuentas_auxiliares` (
  `codigo` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) DEFAULT NULL,
  `cod_banco` int(11) DEFAULT NULL,
  `nro_cuenta` varchar(50) DEFAULT NULL,
  `direccion` varchar(50) DEFAULT NULL,
  `telefono` varchar(50) DEFAULT NULL,
  `referencia1` varchar(255) DEFAULT NULL,
  `referencia2` varchar(255) DEFAULT NULL,
  `cod_cuenta` int(11) DEFAULT NULL,
  `cod_estadoreferencial` int(11) DEFAULT NULL,
  PRIMARY KEY (`codigo`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=latin1;

#
# Dumping data for table cuentas_auxiliares
#

LOCK TABLES `cuentas_auxiliares` WRITE;
/*!40000 ALTER TABLE `cuentas_auxiliares` DISABLE KEYS */;
INSERT INTO `cuentas_auxiliares` VALUES (9,'AUXILIAR TEST 4',NULL,'','','','','',440,2);
INSERT INTO `cuentas_auxiliares` VALUES (10,'CUENTA AUXILIAR 2',NULL,'','','','','',440,2);
INSERT INTO `cuentas_auxiliares` VALUES (11,'CUENTA AUXILIAR 3',NULL,'','','','','',440,1);
INSERT INTO `cuentas_auxiliares` VALUES (12,'CUENTA AUXILIAR9999999',2,'1564646999999','VICTOR EDUARDO NRO. 2299999999','999999999','009999','9999',440,1);
INSERT INTO `cuentas_auxiliares` VALUES (13,'TIGO',2,'150054545454','CALLE X','2845785','JUAN FLORES','RAMIRO MARQUEZ',441,1);
/*!40000 ALTER TABLE `cuentas_auxiliares` ENABLE KEYS */;
UNLOCK TABLES;

#
# Source for table depreciaciones
#

DROP TABLE IF EXISTS `depreciaciones`;
CREATE TABLE `depreciaciones` (
  `codigo` int(11) NOT NULL AUTO_INCREMENT,
  `cod_empresa` int(11) DEFAULT 0,
  `nombre` varchar(255) DEFAULT NULL,
  `vida_util` double DEFAULT NULL,
  `cod_estado` int(11) DEFAULT NULL,
  PRIMARY KEY (`codigo`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

#
# Dumping data for table depreciaciones
#

LOCK TABLES `depreciaciones` WRITE;
/*!40000 ALTER TABLE `depreciaciones` DISABLE KEYS */;
INSERT INTO `depreciaciones` VALUES (1,1,'VEHICULOS',10,1);
/*!40000 ALTER TABLE `depreciaciones` ENABLE KEYS */;
UNLOCK TABLES;

#
# Source for table distribucion_gastosporcentaje
#

DROP TABLE IF EXISTS `distribucion_gastosporcentaje`;
CREATE TABLE `distribucion_gastosporcentaje` (
  `codigo` int(11) NOT NULL AUTO_INCREMENT,
  `cod_unidadorganizacional` int(11) DEFAULT NULL,
  `porcentaje` double DEFAULT NULL,
  PRIMARY KEY (`codigo`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

#
# Dumping data for table distribucion_gastosporcentaje
#

LOCK TABLES `distribucion_gastosporcentaje` WRITE;
/*!40000 ALTER TABLE `distribucion_gastosporcentaje` DISABLE KEYS */;
INSERT INTO `distribucion_gastosporcentaje` VALUES (1,5,20);
INSERT INTO `distribucion_gastosporcentaje` VALUES (2,8,15);
INSERT INTO `distribucion_gastosporcentaje` VALUES (3,9,35);
INSERT INTO `distribucion_gastosporcentaje` VALUES (4,10,10);
INSERT INTO `distribucion_gastosporcentaje` VALUES (5,270,5);
INSERT INTO `distribucion_gastosporcentaje` VALUES (6,271,5);
INSERT INTO `distribucion_gastosporcentaje` VALUES (7,272,5);
INSERT INTO `distribucion_gastosporcentaje` VALUES (8,829,5);
/*!40000 ALTER TABLE `distribucion_gastosporcentaje` ENABLE KEYS */;
UNLOCK TABLES;

#
# Source for table empresas
#

DROP TABLE IF EXISTS `empresas`;
CREATE TABLE `empresas` (
  `codigo` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) DEFAULT NULL,
  `abreviatura` varchar(50) DEFAULT NULL,
  `cod_estadoreferencial` int(11) DEFAULT NULL,
  PRIMARY KEY (`codigo`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;

#
# Dumping data for table empresas
#

LOCK TABLES `empresas` WRITE;
/*!40000 ALTER TABLE `empresas` DISABLE KEYS */;
INSERT INTO `empresas` VALUES (1,'IBNORCA','IBN',1);
INSERT INTO `empresas` VALUES (2,'SIS','SIS',1);
/*!40000 ALTER TABLE `empresas` ENABLE KEYS */;
UNLOCK TABLES;

#
# Source for table estados_comprobantes
#

DROP TABLE IF EXISTS `estados_comprobantes`;
CREATE TABLE `estados_comprobantes` (
  `codigo` int(11) NOT NULL,
  `nombre` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`codigo`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ROW_FORMAT=DYNAMIC;

#
# Dumping data for table estados_comprobantes
#

LOCK TABLES `estados_comprobantes` WRITE;
/*!40000 ALTER TABLE `estados_comprobantes` DISABLE KEYS */;
INSERT INTO `estados_comprobantes` VALUES (1,'Registrado');
INSERT INTO `estados_comprobantes` VALUES (2,'Anulado');
INSERT INTO `estados_comprobantes` VALUES (3,'Aprobado');
/*!40000 ALTER TABLE `estados_comprobantes` ENABLE KEYS */;
UNLOCK TABLES;

#
# Source for table estados_referenciales
#

DROP TABLE IF EXISTS `estados_referenciales`;
CREATE TABLE `estados_referenciales` (
  `codigo` int(11) NOT NULL,
  `nombre` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`codigo`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

#
# Dumping data for table estados_referenciales
#

LOCK TABLES `estados_referenciales` WRITE;
/*!40000 ALTER TABLE `estados_referenciales` DISABLE KEYS */;
/*!40000 ALTER TABLE `estados_referenciales` ENABLE KEYS */;
UNLOCK TABLES;

#
# Source for table facturas_compra
#

DROP TABLE IF EXISTS `facturas_compra`;
CREATE TABLE `facturas_compra` (
  `codigo` int(11) NOT NULL AUTO_INCREMENT,
  `cod_comprobantedetalle` int(11) DEFAULT NULL,
  `nit` varchar(255) DEFAULT NULL,
  `nro_factura` int(11) DEFAULT NULL,
  `fecha` date DEFAULT NULL,
  `razon_social` varchar(255) DEFAULT NULL,
  `importe` double DEFAULT NULL,
  `exento` double DEFAULT NULL,
  `nro_autorizacion` varchar(255) DEFAULT NULL,
  `codigo_control` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`codigo`)
) ENGINE=InnoDB AUTO_INCREMENT=182 DEFAULT CHARSET=latin1;

#
# Dumping data for table facturas_compra
#

LOCK TABLES `facturas_compra` WRITE;
/*!40000 ALTER TABLE `facturas_compra` DISABLE KEYS */;
/*!40000 ALTER TABLE `facturas_compra` ENABLE KEYS */;
UNLOCK TABLES;

#
# Source for table gestiones
#

DROP TABLE IF EXISTS `gestiones`;
CREATE TABLE `gestiones` (
  `codigo` int(11) NOT NULL,
  `nombre` varchar(255) DEFAULT NULL,
  `abreviatura` varchar(20) DEFAULT NULL,
  `cod_estado` int(11) DEFAULT NULL,
  PRIMARY KEY (`codigo`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

#
# Dumping data for table gestiones
#

LOCK TABLES `gestiones` WRITE;
/*!40000 ALTER TABLE `gestiones` DISABLE KEYS */;
INSERT INTO `gestiones` VALUES (116,'2010','2010',1);
INSERT INTO `gestiones` VALUES (117,'2011','2011',1);
INSERT INTO `gestiones` VALUES (118,'2012','2012',1);
INSERT INTO `gestiones` VALUES (119,'2013','1013',1);
INSERT INTO `gestiones` VALUES (120,'2014','2014',1);
INSERT INTO `gestiones` VALUES (121,'2015','2015',1);
INSERT INTO `gestiones` VALUES (122,'2016','2016',1);
INSERT INTO `gestiones` VALUES (123,'2017','2017',1);
INSERT INTO `gestiones` VALUES (124,'2018','2018',1);
INSERT INTO `gestiones` VALUES (1204,'2019','2019',1);
INSERT INTO `gestiones` VALUES (1205,'2020','2020',1);
/*!40000 ALTER TABLE `gestiones` ENABLE KEYS */;
UNLOCK TABLES;

#
# Source for table gestiones_datosadicionales
#

DROP TABLE IF EXISTS `gestiones_datosadicionales`;
CREATE TABLE `gestiones_datosadicionales` (
  `cod_gestion` int(11) NOT NULL,
  `cod_estado` int(255) DEFAULT NULL,
  `cod_estadopoa` int(20) DEFAULT NULL,
  PRIMARY KEY (`cod_gestion`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

#
# Dumping data for table gestiones_datosadicionales
#

LOCK TABLES `gestiones_datosadicionales` WRITE;
/*!40000 ALTER TABLE `gestiones_datosadicionales` DISABLE KEYS */;
INSERT INTO `gestiones_datosadicionales` VALUES (1204,1,1);
INSERT INTO `gestiones_datosadicionales` VALUES (1205,2,1);
/*!40000 ALTER TABLE `gestiones_datosadicionales` ENABLE KEYS */;
UNLOCK TABLES;

#
# Source for table mesdepreciaciones
#

DROP TABLE IF EXISTS `mesdepreciaciones`;
CREATE TABLE `mesdepreciaciones` (
  `codigo` int(11) NOT NULL AUTO_INCREMENT,
  `mes` int(11) DEFAULT NULL,
  `gestion` int(11) DEFAULT NULL,
  `ufvinicio` decimal(16,6) DEFAULT NULL,
  `ufvfinal` decimal(16,6) DEFAULT NULL,
  `estado` int(11) DEFAULT NULL,
  PRIMARY KEY (`codigo`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

#
# Dumping data for table mesdepreciaciones
#

LOCK TABLES `mesdepreciaciones` WRITE;
/*!40000 ALTER TABLE `mesdepreciaciones` DISABLE KEYS */;
/*!40000 ALTER TABLE `mesdepreciaciones` ENABLE KEYS */;
UNLOCK TABLES;

#
# Source for table mesdepreciaciones_detalle
#

DROP TABLE IF EXISTS `mesdepreciaciones_detalle`;
CREATE TABLE `mesdepreciaciones_detalle` (
  `codigo` int(11) NOT NULL AUTO_INCREMENT,
  `cod_mesdepreciaciones` int(11) DEFAULT NULL,
  `cod_activosfijos` int(11) DEFAULT NULL,
  `d2_valorresidual` decimal(12,2) DEFAULT NULL,
  `d3_factoractualizacion` decimal(16,6) DEFAULT NULL,
  `d4_valoractualizado` decimal(12,2) DEFAULT NULL,
  `d5_incrementoporcentual` decimal(12,2) DEFAULT NULL,
  `d6_depreciacionacumuladaanterior` decimal(12,2) DEFAULT NULL,
  `d7_incrementodepreciacionacumulada` decimal(12,2) DEFAULT NULL,
  `d8_depreciacionperiodo` decimal(12,2) DEFAULT NULL,
  `d9_depreciacionacumuladaactual` decimal(12,2) DEFAULT NULL,
  `d10_valornetobs` decimal(12,2) DEFAULT NULL,
  `d11_vidarestante` int(11) DEFAULT NULL,
  PRIMARY KEY (`codigo`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

#
# Dumping data for table mesdepreciaciones_detalle
#

LOCK TABLES `mesdepreciaciones_detalle` WRITE;
/*!40000 ALTER TABLE `mesdepreciaciones_detalle` DISABLE KEYS */;
/*!40000 ALTER TABLE `mesdepreciaciones_detalle` ENABLE KEYS */;
UNLOCK TABLES;

#
# Source for table monedas
#

DROP TABLE IF EXISTS `monedas`;
CREATE TABLE `monedas` (
  `codigo` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) DEFAULT NULL,
  `abreviatura` varchar(50) DEFAULT NULL,
  `cod_estadoreferencial` int(11) DEFAULT NULL,
  PRIMARY KEY (`codigo`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;

#
# Dumping data for table monedas
#

LOCK TABLES `monedas` WRITE;
/*!40000 ALTER TABLE `monedas` DISABLE KEYS */;
INSERT INTO `monedas` VALUES (1,'BOLIVIANOS','Bs.',1);
INSERT INTO `monedas` VALUES (2,'DOLARES AMERICANOS','$us',1);
INSERT INTO `monedas` VALUES (3,'EUROS','Eu',1);
INSERT INTO `monedas` VALUES (4,'UFV','UFV',1);
/*!40000 ALTER TABLE `monedas` ENABLE KEYS */;
UNLOCK TABLES;

#
# Source for table partidas_presupuestarias
#

DROP TABLE IF EXISTS `partidas_presupuestarias`;
CREATE TABLE `partidas_presupuestarias` (
  `codigo` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) DEFAULT NULL,
  `observaciones` varchar(255) DEFAULT NULL,
  `cod_estadoreferencial` int(11) DEFAULT NULL,
  PRIMARY KEY (`codigo`)
) ENGINE=InnoDB AUTO_INCREMENT=434 DEFAULT CHARSET=latin1;

#
# Dumping data for table partidas_presupuestarias
#

LOCK TABLES `partidas_presupuestarias` WRITE;
/*!40000 ALTER TABLE `partidas_presupuestarias` DISABLE KEYS */;
INSERT INTO `partidas_presupuestarias` VALUES (430,'GASTOS GENERALES','\t\t\t\t\t  GASTOS GENERALES\r\n\t  ',2);
INSERT INTO `partidas_presupuestarias` VALUES (431,'GASTOS GENERALES','GASTOS GENERALES DETAIL\r\n',1);
INSERT INTO `partidas_presupuestarias` VALUES (432,'PARTIDA PRESUPUESTARIA 2','\t\t\t\t\t  \t\r\n\t\t\t\t\t  ',1);
INSERT INTO `partidas_presupuestarias` VALUES (433,'HOSPEDAJES','ESTA PARTIDA ESTA ORIENTADA A AGLUTINAR TODAS LAS CUENTAS DE HOSPEDAJE.\t\t\t\t\t  \t\r\n\t\t\t\t\t  ',1);
/*!40000 ALTER TABLE `partidas_presupuestarias` ENABLE KEYS */;
UNLOCK TABLES;

#
# Source for table partidaspresupuestarias_cuentas
#

DROP TABLE IF EXISTS `partidaspresupuestarias_cuentas`;
CREATE TABLE `partidaspresupuestarias_cuentas` (
  `cod_partidapresupuestaria` int(11) NOT NULL AUTO_INCREMENT,
  `cod_cuenta` int(255) NOT NULL,
  PRIMARY KEY (`cod_partidapresupuestaria`,`cod_cuenta`)
) ENGINE=InnoDB AUTO_INCREMENT=434 DEFAULT CHARSET=latin1;

#
# Dumping data for table partidaspresupuestarias_cuentas
#

LOCK TABLES `partidaspresupuestarias_cuentas` WRITE;
/*!40000 ALTER TABLE `partidaspresupuestarias_cuentas` DISABLE KEYS */;
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (1,1000000000);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (2,2000000000);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (3,2147483647);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (4,2147483647);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (5,2147483647);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (6,1010000000);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (7,1010100000);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (8,1010101000);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (9,1010101001);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (10,1010102000);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (11,1010102001);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (12,1010102002);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (13,1010102003);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (14,1010102004);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (15,1010102005);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (16,1010102006);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (17,1010103000);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (18,1010103011);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (19,1010103012);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (20,1010103013);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (21,1010103014);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (22,1010103024);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (23,1010103025);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (24,1010103027);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (25,1010103028);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (26,1010103029);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (27,1010103030);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (28,1010104000);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (29,1010104012);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (30,1010104011);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (31,1010104013);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (32,1010104014);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (33,1010102008);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (34,2147483647);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (35,1010200000);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (36,1010201000);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (37,1010201012);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (38,1010201011);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (39,1010201013);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (40,1010201024);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (41,1010201035);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (42,1010201046);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (43,1010201830);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (44,1010300000);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (45,1010310000);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (46,1010230000);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (47,1010230010);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (48,1010230020);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (49,1010240000);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (50,1010240030);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (51,1010250000);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (52,1010250010);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (53,1010250020);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (54,1010260000);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (55,1010260011);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (56,1010260012);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (57,1010260013);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (58,1010260014);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (59,1010260015);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (60,1010260016);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (61,1010270000);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (62,1010270010);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (63,1010270020);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (64,1010270030);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (65,1010220000);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (66,1010220010);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (67,2015020030);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (68,1010310010);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (69,1010320000);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (70,1010320011);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (71,1010320012);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (72,1010320013);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (73,1010320014);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (74,1010320015);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (75,1010320016);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (76,1010320017);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (77,1010320018);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (78,1010320019);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (79,1010320020);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (80,1010330000);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (81,1010330011);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (82,1010330012);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (83,1010330013);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (84,1010330014);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (85,1010330015);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (86,1010330030);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (87,1020000000);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (88,1020100000);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (89,1022000000);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (90,1022010000);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (91,1022010011);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (92,1022010012);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (93,1022010013);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (94,1022010030);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (95,1023000000);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (96,1023010000);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (97,1023010011);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (98,1023010012);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (99,1024000000);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (100,1024010000);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (101,1024010011);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (102,1024010012);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (103,1024010013);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (104,1024010014);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (105,1024010030);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (106,2010000000);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (107,2020000000);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (108,2011000000);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (109,2011010000);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (110,2011010011);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (111,2011010012);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (112,2011010013);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (113,2011010014);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (114,2011010015);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (115,2011010016);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (116,2011010017);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (117,2011010030);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (118,2012000000);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (119,2012010000);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (120,2012010011);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (121,2012010012);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (122,2012010013);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (123,2012010014);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (124,2012010015);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (125,2012010016);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (126,2012010017);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (127,2012020000);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (128,2012020011);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (129,2012020012);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (130,2012020013);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (131,2012020014);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (132,2147483647);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (133,2013000000);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (134,2013010000);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (135,2013010011);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (136,2013010012);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (137,2013010013);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (138,2013010014);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (139,2013010030);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (140,2013020000);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (141,2013020011);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (142,2013020012);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (143,2014000000);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (144,2014010000);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (145,2014010010);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (146,2014020000);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (147,2014020010);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (148,2014030000);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (149,2014030011);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (150,2015000000);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (151,2015010000);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (152,2015010010);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (153,2015010030);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (154,2015020000);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (155,2015020011);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (156,2015020012);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (157,2015020013);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (158,2016000000);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (159,2016010000);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (160,2016010011);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (161,2016010012);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (162,2016010030);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (163,2016020000);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (164,2016020011);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (165,2016020012);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (166,2016020030);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (167,2017000000);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (168,2017010000);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (169,2017010011);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (170,2021000000);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (171,2021010000);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (172,2021010011);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (173,2021010012);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (174,2021010030);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (175,2147483647);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (176,2147483647);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (177,2147483647);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (178,2147483647);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (179,2147483647);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (180,2147483647);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (181,2147483647);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (182,2147483647);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (183,2147483647);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (184,2147483647);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (185,2147483647);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (186,2147483647);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (187,2147483647);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (188,2147483647);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (189,2147483647);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (190,2147483647);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (191,2147483647);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (192,2147483647);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (193,2147483647);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (194,2147483647);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (195,2147483647);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (196,2147483647);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (197,2147483647);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (198,2147483647);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (199,2147483647);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (200,2147483647);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (201,2147483647);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (202,2147483647);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (203,2147483647);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (204,2147483647);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (205,2147483647);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (206,2147483647);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (207,2147483647);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (208,2147483647);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (209,2147483647);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (210,2147483647);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (211,2147483647);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (212,2147483647);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (213,2147483647);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (214,2147483647);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (215,2147483647);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (216,2147483647);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (217,2147483647);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (218,2147483647);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (219,2147483647);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (220,2147483647);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (221,2147483647);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (222,2147483647);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (223,2147483647);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (224,2147483647);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (225,2147483647);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (226,2147483647);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (227,2147483647);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (228,2147483647);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (229,2147483647);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (230,2147483647);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (231,2147483647);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (232,2147483647);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (233,2147483647);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (234,2147483647);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (235,2147483647);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (236,2147483647);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (237,2147483647);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (238,2147483647);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (239,2147483647);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (240,2147483647);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (241,2147483647);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (242,2147483647);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (243,2147483647);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (244,2147483647);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (245,2147483647);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (246,2147483647);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (247,2147483647);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (248,2147483647);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (249,2147483647);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (250,2147483647);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (251,2147483647);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (252,2147483647);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (253,2147483647);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (254,2147483647);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (255,2147483647);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (256,2147483647);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (257,2147483647);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (258,2147483647);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (259,2147483647);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (260,2147483647);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (261,2147483647);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (262,2147483647);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (263,2147483647);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (264,2147483647);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (265,2147483647);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (266,2147483647);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (267,2147483647);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (268,2147483647);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (269,2147483647);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (270,2147483647);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (271,2147483647);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (272,2147483647);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (273,2147483647);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (274,2147483647);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (275,2147483647);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (276,2147483647);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (277,2147483647);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (278,2147483647);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (279,2147483647);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (280,2147483647);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (281,2147483647);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (282,2147483647);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (283,2147483647);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (284,2147483647);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (285,2147483647);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (286,2147483647);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (287,2147483647);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (288,2147483647);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (289,2147483647);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (290,2147483647);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (291,2147483647);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (292,2147483647);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (293,2147483647);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (294,2147483647);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (295,2147483647);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (296,2147483647);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (297,2147483647);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (298,2147483647);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (299,2147483647);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (300,2147483647);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (301,2147483647);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (302,2147483647);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (303,2147483647);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (304,2147483647);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (305,2147483647);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (306,2147483647);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (307,2147483647);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (308,2147483647);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (309,2147483647);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (310,2147483647);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (311,2147483647);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (312,2147483647);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (313,2147483647);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (314,2147483647);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (315,2147483647);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (316,2147483647);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (317,2147483647);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (318,2147483647);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (319,2147483647);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (320,2147483647);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (321,2147483647);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (322,2147483647);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (323,2147483647);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (324,2147483647);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (325,2147483647);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (326,2147483647);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (327,2147483647);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (328,1020101000);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (329,1020101001);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (330,1020101002);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (331,1020102000);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (332,1020102001);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (333,1020102002);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (334,1020103000);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (335,1020103001);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (336,1020103002);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (337,1020104000);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (338,1020104001);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (339,1020104002);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (340,1020105000);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (341,1020105001);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (342,1020105002);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (343,1020106000);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (344,1020106001);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (345,1020106002);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (346,1020107000);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (347,1020107001);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (348,1020107002);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (349,1020108000);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (350,1020108001);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (351,1020108002);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (352,1020109000);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (353,1020109001);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (354,1020109002);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (355,1020110000);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (356,1020110001);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (357,1020130000);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (358,1020130001);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (359,1023010018);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (360,1010230030);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (361,1010103021);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (362,2147483647);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (363,2147483647);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (364,2147483647);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (365,1010103015);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (366,2021020000);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (367,2021020001);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (368,2015020001);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (369,2147483647);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (370,2021020002);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (371,2013010010);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (372,2147483647);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (373,2147483647);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (374,1023010013);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (375,2016020013);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (376,2015020002);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (377,1010230040);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (378,1023010014);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (379,1010260020);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (380,1010201950);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (381,1022010014);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (382,2017010012);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (383,2147483647);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (384,2147483647);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (385,1010103040);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (386,2147483647);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (387,1010230050);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (388,1022010015);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (389,2021030000);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (390,2021030001);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (391,2147483647);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (392,2147483647);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (393,1010103016);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (394,2021010013);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (395,1022010101);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (396,1022010102);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (397,1022010201);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (398,1022010202);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (399,1022010301);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (400,1022010302);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (401,1022010401);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (402,2021010014);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (403,2015020003);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (404,2147483647);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (405,1022010402);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (406,1010102050);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (407,1010260017);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (408,1010102007);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (409,2015030000);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (410,2015030001);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (411,2015030002);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (412,2016010013);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (413,2147483647);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (414,2147483647);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (415,2147483647);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (416,2147483647);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (423,1010101003);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (424,0);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (425,0);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (426,1010101009);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (427,1010101005);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (428,1010101008);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (429,1010101004);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (430,9);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (430,429);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (431,11);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (431,12);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (433,219);
INSERT INTO `partidaspresupuestarias_cuentas` VALUES (433,226);
/*!40000 ALTER TABLE `partidaspresupuestarias_cuentas` ENABLE KEYS */;
UNLOCK TABLES;

#
# Source for table personal_areas
#

DROP TABLE IF EXISTS `personal_areas`;
CREATE TABLE `personal_areas` (
  `cod_personal` int(11) NOT NULL,
  `cod_area` int(11) NOT NULL,
  PRIMARY KEY (`cod_personal`,`cod_area`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

#
# Dumping data for table personal_areas
#

LOCK TABLES `personal_areas` WRITE;
/*!40000 ALTER TABLE `personal_areas` DISABLE KEYS */;
/*!40000 ALTER TABLE `personal_areas` ENABLE KEYS */;
UNLOCK TABLES;

#
# Source for table personal_datosadicionales
#

DROP TABLE IF EXISTS `personal_datosadicionales`;
CREATE TABLE `personal_datosadicionales` (
  `cod_personal` int(11) NOT NULL,
  `cod_estado` int(11) DEFAULT NULL,
  `perfil` int(11) DEFAULT NULL,
  `usuario` varchar(50) DEFAULT NULL,
  `contrasena` varchar(50) DEFAULT NULL,
  `cod_cargo` int(11) DEFAULT NULL,
  `usuario_pon` int(11) DEFAULT 0 COMMENT '0 si no es pon y 1 si es usuario pon',
  PRIMARY KEY (`cod_personal`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

#
# Dumping data for table personal_datosadicionales
#

LOCK TABLES `personal_datosadicionales` WRITE;
/*!40000 ALTER TABLE `personal_datosadicionales` DISABLE KEYS */;
INSERT INTO `personal_datosadicionales` VALUES (1,1,4,'projectsis','123456',NULL,0);
INSERT INTO `personal_datosadicionales` VALUES (5,1,2,'wmiranda','123456',5,0);
INSERT INTO `personal_datosadicionales` VALUES (14,1,2,'vespinoza','123456',5,1);
INSERT INTO `personal_datosadicionales` VALUES (16,1,2,'rllano','123456',5,2);
INSERT INTO `personal_datosadicionales` VALUES (17,1,3,'ycastro','123456',5,0);
INSERT INTO `personal_datosadicionales` VALUES (31,1,6,'cbeltran','123456',24,0);
INSERT INTO `personal_datosadicionales` VALUES (32,1,2,'jpalomo','123456',5,0);
INSERT INTO `personal_datosadicionales` VALUES (34,1,3,'btorrico','123456',6,0);
INSERT INTO `personal_datosadicionales` VALUES (37,1,3,'gvaldez','123456',5,0);
INSERT INTO `personal_datosadicionales` VALUES (38,1,3,'mvillafane','123456',5,0);
INSERT INTO `personal_datosadicionales` VALUES (40,1,3,'arendon','123456',5,0);
INSERT INTO `personal_datosadicionales` VALUES (48,1,3,'scuevas','123456',15,0);
INSERT INTO `personal_datosadicionales` VALUES (53,1,3,'erodriguez','123456',5,0);
INSERT INTO `personal_datosadicionales` VALUES (58,1,3,'jsolares','123456',5,0);
INSERT INTO `personal_datosadicionales` VALUES (63,1,3,'mlinares','123456',5,0);
INSERT INTO `personal_datosadicionales` VALUES (71,1,2,'apaukner','123456',5,0);
INSERT INTO `personal_datosadicionales` VALUES (81,1,2,'asandi','123456',NULL,0);
INSERT INTO `personal_datosadicionales` VALUES (85,2,NULL,NULL,'123456',NULL,0);
INSERT INTO `personal_datosadicionales` VALUES (90,1,1,'jquenallata','123456',5,0);
INSERT INTO `personal_datosadicionales` VALUES (92,1,3,'caldunate','123456',5,0);
INSERT INTO `personal_datosadicionales` VALUES (93,1,2,'mgallardo','123456',NULL,0);
INSERT INTO `personal_datosadicionales` VALUES (98,1,3,'atorrelio','123456',5,0);
INSERT INTO `personal_datosadicionales` VALUES (99,1,2,'mmendez','123456',5,0);
INSERT INTO `personal_datosadicionales` VALUES (114,1,3,'jvillarroel','123456',5,0);
INSERT INTO `personal_datosadicionales` VALUES (118,1,3,'malmanza','123456',5,0);
INSERT INTO `personal_datosadicionales` VALUES (178,1,2,'ovargas','123456',5,1);
INSERT INTO `personal_datosadicionales` VALUES (183,1,1,'lrojas','123456',5,2);
INSERT INTO `personal_datosadicionales` VALUES (195,1,2,'iaruquipa','123456',NULL,0);
INSERT INTO `personal_datosadicionales` VALUES (201,1,2,'fnoriega','123456',5,0);
/*!40000 ALTER TABLE `personal_datosadicionales` ENABLE KEYS */;
UNLOCK TABLES;

#
# Source for table personal_unidadesorganizacionales
#

DROP TABLE IF EXISTS `personal_unidadesorganizacionales`;
CREATE TABLE `personal_unidadesorganizacionales` (
  `cod_personal` int(11) NOT NULL,
  `cod_unidad` int(11) NOT NULL,
  PRIMARY KEY (`cod_personal`,`cod_unidad`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

#
# Dumping data for table personal_unidadesorganizacionales
#

LOCK TABLES `personal_unidadesorganizacionales` WRITE;
/*!40000 ALTER TABLE `personal_unidadesorganizacionales` DISABLE KEYS */;
/*!40000 ALTER TABLE `personal_unidadesorganizacionales` ENABLE KEYS */;
UNLOCK TABLES;

#
# Source for table personal2
#

DROP TABLE IF EXISTS `personal2`;
CREATE TABLE `personal2` (
  `codigo` int(11) NOT NULL,
  `nombre` varchar(255) DEFAULT NULL,
  `cod_area` int(11) DEFAULT NULL,
  `cod_unidad` int(11) DEFAULT NULL,
  PRIMARY KEY (`codigo`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

#
# Dumping data for table personal2
#

LOCK TABLES `personal2` WRITE;
/*!40000 ALTER TABLE `personal2` DISABLE KEYS */;
INSERT INTO `personal2` VALUES (1,'ADM',15,1);
INSERT INTO `personal2` VALUES (5,'WILLY MIRANDA',826,829);
INSERT INTO `personal2` VALUES (6,'ADMT',15,1);
INSERT INTO `personal2` VALUES (7,'ANNELIESSE PRUDENCIO',13,4);
INSERT INTO `personal2` VALUES (8,'PEDRO CARDOZO',13,1624);
INSERT INTO `personal2` VALUES (9,'GONZALO ROMERO',826,829);
INSERT INTO `personal2` VALUES (10,'CONSULTA NORMAS',137,1);
INSERT INTO `personal2` VALUES (12,'RODRIGO VELASCO',873,5);
INSERT INTO `personal2` VALUES (13,'RENE CASTRO',39,1624);
INSERT INTO `personal2` VALUES (14,'VALERIA ESPINOZA',12,5);
INSERT INTO `personal2` VALUES (15,'CRISTINA MEJIA',826,1625);
INSERT INTO `personal2` VALUES (16,'ROXANA LLANO',12,829);
INSERT INTO `personal2` VALUES (17,'YSNAIDER CASTRO',11,4);
INSERT INTO `personal2` VALUES (20,'EFRAIN MARISCAL',13,10);
INSERT INTO `personal2` VALUES (21,'JANETH CAYO',13,271);
INSERT INTO `personal2` VALUES (22,'CARLOS TEJERINA',11,10);
INSERT INTO `personal2` VALUES (23,'JUSTO ORELLANA',11,10);
INSERT INTO `personal2` VALUES (26,'GRACIELA CHAVEZ',11,10);
INSERT INTO `personal2` VALUES (27,'JOSUE ANTONIO BARROSO CORDOVA',11,271);
INSERT INTO `personal2` VALUES (28,'CARLOS ALARCON',13,5);
INSERT INTO `personal2` VALUES (29,'CARLOS SERRUDO',13,1624);
INSERT INTO `personal2` VALUES (30,'VICTOR AMURRIO',13,2);
INSERT INTO `personal2` VALUES (31,'CARMEN BELTRAN',78,1625);
INSERT INTO `personal2` VALUES (32,'JULIO PALOMO',78,2);
INSERT INTO `personal2` VALUES (33,'MIRIAM LINARES',78,1625);
INSERT INTO `personal2` VALUES (34,'BENJAMIN TORRICO',11,1625);
INSERT INTO `personal2` VALUES (35,'JORGE MEJIA NAVIA',11,1625);
INSERT INTO `personal2` VALUES (36,'DANIEL GUERRERO',11,9);
INSERT INTO `personal2` VALUES (37,'GABRIELA VALDEZ',13,4);
INSERT INTO `personal2` VALUES (38,'MARIANA VILLAFANE',13,1625);
INSERT INTO `personal2` VALUES (39,'DIEGO ANTEZANA',13,3);
INSERT INTO `personal2` VALUES (40,'ALEJANDRO RENDON',11,1624);
INSERT INTO `personal2` VALUES (41,'KATIA CASTELLON',874,9);
INSERT INTO `personal2` VALUES (42,'ROXANA RAMIREZ',874,9);
INSERT INTO `personal2` VALUES (43,'RAMIRO GOMEZ',11,1625);
INSERT INTO `personal2` VALUES (44,'ANA BUENO',273,270);
INSERT INTO `personal2` VALUES (45,'ANA BUENO',13,270);
INSERT INTO `personal2` VALUES (46,'RAMIRO GOMEZ',13,1625);
INSERT INTO `personal2` VALUES (47,'CARLA LINARES',39,4);
INSERT INTO `personal2` VALUES (48,'SILVIA CUEVAS',38,4);
INSERT INTO `personal2` VALUES (49,'MARIA SERRANO',874,10);
INSERT INTO `personal2` VALUES (50,'YESSICA SEGOVIA',874,10);
INSERT INTO `personal2` VALUES (51,'MIGUEL BALLON',11,1624);
INSERT INTO `personal2` VALUES (53,'ERIKA RODRIGUEZ',78,4);
INSERT INTO `personal2` VALUES (54,'GABRIELA MUNOZ A',38,4);
INSERT INTO `personal2` VALUES (58,'JANIS SOLARES',874,1624);
INSERT INTO `personal2` VALUES (59,'JOSE LUIS ARROYO',38,8);
INSERT INTO `personal2` VALUES (60,'JOSE LUIS ARROYO',39,1624);
INSERT INTO `personal2` VALUES (62,'TERESA CUBA',11,5);
INSERT INTO `personal2` VALUES (63,'MIRIAM LINARES',39,1625);
INSERT INTO `personal2` VALUES (64,'MIRIAM LINARES',38,1625);
INSERT INTO `personal2` VALUES (65,'JANIS SOLARES',13,1624);
INSERT INTO `personal2` VALUES (68,'FABRICIO NORIEGA',872,829);
INSERT INTO `personal2` VALUES (69,'DANNY DAVILA',40,5);
INSERT INTO `personal2` VALUES (70,'KATHERIN OROPEZA',40,5);
INSERT INTO `personal2` VALUES (71,'ANNELIESE PAUKNER',872,829);
INSERT INTO `personal2` VALUES (72,'JOSE LARREA',874,5);
INSERT INTO `personal2` VALUES (73,'GABRIELA TUDELA',14,1);
INSERT INTO `personal2` VALUES (74,'MIRKO JEAN PEREDO RIVAS',38,9);
INSERT INTO `personal2` VALUES (75,'MIRKO JEAN PEREDO RIVAS',39,9);
INSERT INTO `personal2` VALUES (77,'LEONARDO BURGOS MENDOZA',13,10);
INSERT INTO `personal2` VALUES (78,'ALEJANDRA TORRELIO (TCS)',38,1624);
INSERT INTO `personal2` VALUES (80,'JANETH CAYO',874,271);
INSERT INTO `personal2` VALUES (81,'ANDREA SANDI',78,1624);
INSERT INTO `personal2` VALUES (82,'RAMIRO GOMEZ',38,1625);
INSERT INTO `personal2` VALUES (83,'RAMIRO GOMEZ',39,1625);
INSERT INTO `personal2` VALUES (84,'JOSE DURAN',137,1);
INSERT INTO `personal2` VALUES (85,'INVITADO',13,5);
INSERT INTO `personal2` VALUES (86,'RENE CASTRO',38,5);
INSERT INTO `personal2` VALUES (87,'HELEN HINOJOSA CUSICANQUI',13,1625);
INSERT INTO `personal2` VALUES (89,'JULIO MAMANI',273,829);
INSERT INTO `personal2` VALUES (90,'JUAN QUENALLATA',273,2);
INSERT INTO `personal2` VALUES (91,'SANDRA SIERRA',847,829);
INSERT INTO `personal2` VALUES (92,'CARLA ALDUNATE',1200,1624);
INSERT INTO `personal2` VALUES (93,'MARIA RENEE GALLARDO',871,829);
INSERT INTO `personal2` VALUES (94,'JOSE LUIS ARROYO',11,8);
INSERT INTO `personal2` VALUES (96,'GLICET OSCO',872,829);
INSERT INTO `personal2` VALUES (97,'CARLA NOELIA COCHI TARQUI',13,5);
INSERT INTO `personal2` VALUES (98,'ALEJANDRA TORRELIO (TCP)',39,1624);
INSERT INTO `personal2` VALUES (99,'MARTHA MENDEZ LECLERE',13,1);
INSERT INTO `personal2` VALUES (101,'VERONICA LLANQUIPACHA ROJAS',874,1625);
INSERT INTO `personal2` VALUES (114,'JANET VILLARROEL',12,10);
INSERT INTO `personal2` VALUES (118,'MARIELA ALMANZA AGUIRRE',12,9);
INSERT INTO `personal2` VALUES (126,'SERGIO MALDONADO',871,829);
INSERT INTO `personal2` VALUES (127,'SILVIA CUEVAS',39,4);
INSERT INTO `personal2` VALUES (141,'IVAN PADILLA AVALOS',11,10);
INSERT INTO `personal2` VALUES (142,'RODRIGO JAIMES PORTUGAL',13,5);
INSERT INTO `personal2` VALUES (143,'CTN 329',137,1);
INSERT INTO `personal2` VALUES (156,'OPERADOR VIRTUAL RLP',13,1103);
INSERT INTO `personal2` VALUES (157,'OPERADOR VIRTUAL RSC',13,1103);
INSERT INTO `personal2` VALUES (158,'OPERADOR VIRTUAL RCB',13,1103);
INSERT INTO `personal2` VALUES (168,'ROCIO MALLEA ORTIZ',874,5);
INSERT INTO `personal2` VALUES (172,'VALIDARLP',13,1103);
INSERT INTO `personal2` VALUES (173,'JOSE ARMANDO ZAPATA AVILES',11,5);
INSERT INTO `personal2` VALUES (176,'OSCAR VARGAS',872,829);
INSERT INTO `personal2` VALUES (177,'CINTYA ZARATE CAZAS',38,1624);
INSERT INTO `personal2` VALUES (178,'OSCAR VARGAS',1235,829);
INSERT INTO `personal2` VALUES (181,'ELIZABETH SEJAS GALLARDO',11,9);
INSERT INTO `personal2` VALUES (182,'EDVING GUSTAVO VELASQUEZ CAMARGO',11,5);
INSERT INTO `personal2` VALUES (183,'LUIS FERNANDO ROJAS URQUIZO',871,5);
INSERT INTO `personal2` VALUES (185,'NAIR PAMELA SANTA CRUZ QUISBERT',13,5);
INSERT INTO `personal2` VALUES (187,'LAURA MONTAN ARCE',13,9);
INSERT INTO `personal2` VALUES (195,'IVETH ARUQUIPA',273,829);
INSERT INTO `personal2` VALUES (203,'ALEJANDRA CAMPOS',13,5);
INSERT INTO `personal2` VALUES (206,'MAURICIO GONZALO CATACORA ROMERO',12,829);
INSERT INTO `personal2` VALUES (207,'NORMATECARLP',137,5);
INSERT INTO `personal2` VALUES (208,'NORMATECAROR',137,8);
INSERT INTO `personal2` VALUES (209,'NORMATECARPT',137,272);
INSERT INTO `personal2` VALUES (210,'NORMATECARCB',137,9);
INSERT INTO `personal2` VALUES (211,'NORMATECARSC',137,10);
INSERT INTO `personal2` VALUES (212,'NORMATECARCH',137,270);
INSERT INTO `personal2` VALUES (213,'NORMATECARTJ',137,271);
INSERT INTO `personal2` VALUES (221,'FRANCO GABIN MICHEL LUZARAZU',13,9);
INSERT INTO `personal2` VALUES (222,'MARIBEL FLORES',13,10);
INSERT INTO `personal2` VALUES (224,'ALEJANDRO RENDON (TCS)',38,1624);
INSERT INTO `personal2` VALUES (225,'ALEJANDRO RENDON(P)',39,1624);
INSERT INTO `personal2` VALUES (226,'PAOLA ANDREA DURAN MARTINEZ',13,8);
INSERT INTO `personal2` VALUES (227,'IVONNE CASAS',273,829);
INSERT INTO `personal2` VALUES (228,'CARMEN ANDREA PENARANDA MARINO',874,8);
INSERT INTO `personal2` VALUES (241,'ARIEL TEO MAMANI JAVIER',13,272);
INSERT INTO `personal2` VALUES (243,'ARIEL TEO MAMANI JAVIER',11,272);
INSERT INTO `personal2` VALUES (244,'CINTYA ZARATE CAZAS',39,1624);
INSERT INTO `personal2` VALUES (251,'ADRIAN CADIMA',12,829);
INSERT INTO `personal2` VALUES (252,'CARLA LINARES',11,4);
INSERT INTO `personal2` VALUES (257,'DANNY DAVILA',78,1624);
INSERT INTO `personal2` VALUES (264,'ADRIANA LENIN ESPINOZA TORREZ',13,10);
INSERT INTO `personal2` VALUES (273,'CECILIA ALEJANDRA JIMENEZ CUBA',12,829);
INSERT INTO `personal2` VALUES (274,'JORGE CUSTODIO PENA GOMEZ',13,10);
/*!40000 ALTER TABLE `personal2` ENABLE KEYS */;
UNLOCK TABLES;

#
# Source for table perspectivas
#

DROP TABLE IF EXISTS `perspectivas`;
CREATE TABLE `perspectivas` (
  `codigo` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) DEFAULT NULL,
  `abreviatura` varchar(20) DEFAULT NULL,
  `cod_estadoreferencial` int(11) DEFAULT NULL,
  PRIMARY KEY (`codigo`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;

#
# Dumping data for table perspectivas
#

LOCK TABLES `perspectivas` WRITE;
/*!40000 ALTER TABLE `perspectivas` DISABLE KEYS */;
INSERT INTO `perspectivas` VALUES (1,'INSTITUCIONAL1','INS1',2);
INSERT INTO `perspectivas` VALUES (2,'PROCESOS INTERNOS','PI',1);
INSERT INTO `perspectivas` VALUES (3,'CLIENTES','CL',1);
INSERT INTO `perspectivas` VALUES (4,'FINANCIERA','FIN',1);
/*!40000 ALTER TABLE `perspectivas` ENABLE KEYS */;
UNLOCK TABLES;

#
# Source for table plan_cuentas
#

DROP TABLE IF EXISTS `plan_cuentas`;
CREATE TABLE `plan_cuentas` (
  `codigo` int(11) NOT NULL AUTO_INCREMENT,
  `numero` varchar(255) DEFAULT NULL,
  `nombre` varchar(255) DEFAULT NULL,
  `cod_padre` int(11) DEFAULT NULL,
  `cod_moneda` int(11) DEFAULT NULL,
  `cod_estadoreferencial` int(11) DEFAULT NULL,
  `cod_tipocuenta` int(11) DEFAULT NULL COMMENT 'cuenta de balance o de resultados',
  `nivel` int(11) DEFAULT NULL,
  `observaciones` varchar(255) DEFAULT NULL,
  `cuenta_auxiliar` int(11) DEFAULT NULL,
  PRIMARY KEY (`codigo`)
) ENGINE=InnoDB AUTO_INCREMENT=442 DEFAULT CHARSET=latin1;

#
# Dumping data for table plan_cuentas
#

LOCK TABLES `plan_cuentas` WRITE;
/*!40000 ALTER TABLE `plan_cuentas` DISABLE KEYS */;
INSERT INTO `plan_cuentas` VALUES (1,'1000000000      ','ACTIVO                                                                                                                          ',NULL,1,1,1,1,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (2,'2000000000      ','PASIVO                                                                                                                          ',NULL,1,1,1,1,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (3,'3000000000      ','PATRIMONIO                                                                                                                      ',NULL,1,1,1,1,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (4,'4000000000      ','INGRESOS                                                                                                                        ',NULL,1,1,1,1,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (5,'5000000000      ','EGRESOS                                                                                                                         ',NULL,1,1,1,1,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (6,'1010000000      ','ACTIVO CORRIENTE                                                                                                                ',1000000000,1,1,1,2,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (7,'1010100000      ','DISPONIBLE                                                                                                                      ',1010000000,1,1,1,3,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (8,'1010101000      ','CAJA                                                                                                                            ',1010100000,1,1,1,4,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (9,'1010101001      ','Caja General                                                                                                                    ',1010101000,1,1,1,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (10,'1010102000      ','CAJA CHICA                                                                                                                      ',1010100000,1,1,1,4,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (11,'1010102001      ','Caja Chica La Paz                                                                                                               ',1010102000,1,1,1,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (12,'1010102002      ','Caja Chica Santa Cruz                                                                                                           ',1010102000,1,1,1,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (13,'1010102003      ','Caja Chica Cochabamba                                                                                                           ',1010102000,1,1,1,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (14,'1010102004      ','Caja Chica Sucre                                                                                                                ',1010102000,1,1,2,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (15,'1010102005      ','Caja Chica Tarija                                                                                                               ',1010102000,1,1,1,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (16,'1010102006      ','Caja Chica Oruro                                                                                                                ',1010102000,1,1,1,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (17,'1010103000      ','BANCOS                                                                                                                          ',1010100000,1,1,1,4,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (18,'1010103011      ','Banco BISA Cta. Cte. 0131670010 M/N',1010103000,1,1,1,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (19,'1010103012      ','Banco BISA Cta. Cte. 0131672012 M/E                                                                                             ',1010103000,1,1,1,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (20,'1010103013      ','Banco Unión Cta. Cte. 10000001578993 M/N',1010103000,1,1,1,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (21,'1010103014      ','Banco Mercantil Santa Cruz Cta. Cte. 4010417532 M/N                                                                             ',1010103000,1,1,1,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (22,'1010103024      ','Banco BISA Caja Ahorro 0131674015 M/N RLP',1010103000,1,1,1,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (23,'1010103025      ','Banco BISA Caja Ahorro 0131674023 M/N RCB',1010103000,1,1,1,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (24,'1010103027      ','Banco BISA Caja Ahorro 0131674031 M/N RSR',1010103000,1,1,1,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (25,'1010103028      ','Banco BISA Caja Ahorro 0131674040 M/N RSC',1010103000,1,1,1,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (26,'1010103029      ','Banco BISA Caja Ahorro 0131674058 M/N ROR',1010103000,1,1,1,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (27,'1010103030      ','Banco BISA Caja Ahorro 0131674066 M/N RTJ',1010103000,1,1,1,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (28,'1010104000      ','DEPOSITOS A PLAZO FIJO                                                                                                          ',1010100000,1,1,1,4,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (29,'1010104012      ','DPF PRODEM S.A. M/N                                                                                                             ',1010104000,1,1,1,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (30,'1010104011      ','DPF Banco BISA S.A. M/E                                                                                                         ',1010104000,1,1,1,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (31,'1010104013      ','DPF Banco BISA S.A. M/N                                                                                                         ',1010104000,1,1,1,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (32,'1010104014      ','DPF Banco BISA S.A. UFV                                                                                                         ',1010104000,1,1,1,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (33,'1010102008','caja chica sis',1010102000,1,1,1,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (34,'5020103003','GASTOS PERSONAL TEST',2147483647,1,1,1,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (35,'1010200000      ','EXIGIBLE                                                                                                                        ',1010000000,1,1,1,3,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (36,'1010201000      ','CUENTAS POR COBRAR',1010200000,1,1,1,4,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (37,'1010201012      ','Asociados Cuotas Personal                                                                                                       ',1010201000,1,1,1,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (38,'1010201011      ','Asociados Cuotas Asociados                                                                                                      ',1010201000,1,1,1,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (39,'1010201013      ','Aporte Socio Fundadores                                                                                                         ',1010201000,1,1,1,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (40,'1010201024      ','Proyectos                                                                                                                       ',1010201000,1,1,1,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (41,'1010201035      ','Préstamos al Personal                                                                                                           ',1010201000,1,1,1,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (42,'1010201046      ','Depósitos en Garantía                                                                                                           ',1010201000,1,1,1,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (43,'1010201830      ','Otras Cuentas por Cobrar                                                                                                        ',1010201000,1,1,1,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (44,'1010300000      ','BIENES DE CAMBIO O REALIZABLES                                                                                                  ',1010000000,1,1,1,3,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (45,'1010310000      ','MATERIALES EN TRANSITO                                                                                                          ',1010300000,1,1,1,4,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (46,'1010230000      ','OTRAS CUENTAS POR COBRAR                                                                                                        ',1010200000,1,1,1,4,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (47,'1010230010      ','Cuentas por Cobra FOMIN                                                                                                         ',1010230000,1,1,1,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (48,'1010230020      ','Cuentas por Cobrar NOREXPORT                                                                                                    ',1010230000,1,1,1,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (49,'1010240000      ','ENTREGAS C/RENDICION                                                                                                            ',1010200000,1,1,1,4,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (50,'1010240030      ','Otras entregas C/Rendición                                                                                                      ',1010240000,1,1,1,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (51,'1010250000      ','CUENTAS INCOBRABLES                                                                                                             ',1010200000,1,1,1,4,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (52,'1010250010      ','Cuentas Incobrables                                                                                                             ',1010250000,1,1,1,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (53,'1010250020      ','(-) Previsión Cuentas Incobrables                                                                                               ',1010250000,1,1,1,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (54,'1010260000      ','REGIONALES CTA. CTE.                                                                                                            ',1010200000,1,1,1,4,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (55,'1010260011      ','Regional Cta. Cte. La Paz                                                                                                       ',1010260000,1,1,1,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (56,'1010260012      ','Regional Cta. Cte. Santa Cruz                                                                                                   ',1010260000,1,1,1,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (57,'1010260013      ','Regional Cta. Cte. Cochabamba                                                                                                   ',1010260000,1,1,1,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (58,'1010260014      ','Regional Cta. Cte. Sucre                                                                                                        ',1010260000,1,1,1,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (59,'1010260015      ','Regional Cta. Cte. Tarija                                                                                                       ',1010260000,1,1,1,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (60,'1010260016      ','Regional Cta. Cte. Oruro                                                                                                        ',1010260000,1,1,1,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (61,'1010270000      ','CREDITO FISCAL                                                                                                                  ',1010200000,1,1,1,4,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (62,'1010270010      ','Crédito Fiscal - IVA                                                                                                            ',1010270000,1,1,1,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (63,'1010270020      ','Crédito Fiscal - IVA Diferido                                                                                                   ',1010270000,1,1,1,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (64,'1010270030      ','Crédito Fiscal - IVA por Recuperar                                                                                              ',1010270000,1,1,1,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (65,'1010220000      ','CLIENTES                                                                                                                        ',1010200000,1,1,1,4,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (66,'1010220010      ','Clientes                                                                                                                        ',1010220000,1,1,1,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (67,'2015020030      ','Fondos a Rendir SIS-IBNORCA                                                                                                     ',2015020000,1,1,1,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (68,'1010310010      ','Materiales en Transito                                                                                                          ',1010310000,1,1,1,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (69,'1010320000      ','INVENTARIOS                                                                                                                     ',1010300000,1,1,1,4,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (70,'1010320011      ','Material para la Venta                                                                                                          ',1010320000,1,1,1,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (71,'1010320012      ','Reactivo Orgánico                                                                                                               ',1010320000,1,1,1,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (72,'1010320013      ','Suministros y Material de Oficina                                                                                               ',1010320000,1,1,1,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (73,'1010320014      ','Precintos Metálicos (Garrafas)                                                                                                  ',1010320000,1,1,1,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (74,'1010320015      ','Precintos Plasticos (Café)                                                                                                      ',1010320000,1,1,1,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (75,'1010320016      ','Remaches para Garrafas                                                                                                          ',1010320000,1,1,1,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (76,'1010320017      ','Precintos Plasticos (Refrig)                                                                                                    ',1010320000,1,1,1,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (77,'1010320018      ','Película Radiografica                                                                                                           ',1010320000,1,1,1,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (78,'1010320019      ','Material de Laboratorio                                                                                                         ',1010320000,1,1,1,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (79,'1010320020      ','Sacos de Café                                                                                                                   ',1010320000,1,1,1,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (80,'1010330000      ','ANTICIPOS                                                                                                                       ',1010300000,1,1,1,4,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (81,'1010330011      ','Proveedores de Servicios                                                                                                        ',1010330000,1,1,1,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (82,'1010330012      ','Depósitos en Garantía                                                                                                           ',1010330000,1,1,1,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (83,'1010330013      ','Anticipo Sueldos                                                                                                                ',1010330000,1,1,1,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (84,'1010330014      ','Alquileres                                                                                                                      ',1010330000,1,1,1,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (85,'1010330015      ','Impuesto a las Transacciones Financieras                                                                                        ',1010330000,1,1,1,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (86,'1010330030      ','Otros anticipos                                                                                                                 ',1010330000,1,1,1,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (87,'1020000000      ','ACTIVO NO CORRIENTE                                                                                                             ',1000000000,1,1,1,2,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (88,'1020100000      ','BIENES DE USO                                                                                                                   ',1020000000,1,1,1,3,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (89,'1022000000      ','INTANGIBLES                                                                                                                     ',1020000000,1,1,1,3,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (90,'1022010000      ','LICENCIAS - DERECHOS OTROS                                                                                                      ',1022000000,1,1,1,4,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (91,'1022010011      ','Paquete Contable                                                                                                                ',1022010000,1,1,1,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (92,'1022010012      ','Acreditaciones                                                                                                                  ',1022010000,1,1,1,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (93,'1022010013      ','Licencias                                                                                                                       ',1022010000,1,1,1,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (94,'1022010030      ','Otros                                                                                                                           ',1022010000,1,1,1,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (95,'1023000000      ','INVERSIONES                                                                                                                     ',1020000000,1,1,1,3,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (96,'1023010000      ','PARTICIPACIONES                                                                                                                 ',1023000000,1,1,1,4,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (97,'1023010011      ','Cuotas de Participación COTEL                                                                                                   ',1023010000,1,1,1,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (98,'1023010012      ','Cuotas de Participación COTAS                                                                                                   ',1023010000,1,1,1,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (99,'1024000000      ','DIFERIDOS                                                                                                                       ',1020000000,1,1,1,3,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (100,'1024010000      ','GASTOS PAGADOS POR ANTICIPADO                                                                                                   ',1024000000,1,1,1,4,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (101,'1024010011      ','Seguros                                                                                                                         ',1024010000,1,1,1,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (102,'1024010012      ','Suscripciones                                                                                                                   ',1024010000,1,1,1,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (103,'1024010013      ','Cuotas Anuales                                                                                                                  ',1024010000,1,1,1,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (104,'1024010014      ','Subsidios                                                                                                                       ',1024010000,1,1,1,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (105,'1024010030      ','Otros                                                                                                                           ',1024010000,1,1,1,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (106,'2010000000      ','PASIVO CORRIENTE                                                                                                                ',2000000000,1,1,1,2,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (107,'2020000000      ','PASIVO NO CORRIENTE                                                                                                             ',2000000000,1,1,1,2,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (108,'2011000000      ','OBLIGACIONES A CORTO PLAZO                                                                                                      ',2010000000,1,1,1,3,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (109,'2011010000      ','OBLIGACIONES GENERALES                                                                                                          ',2011000000,1,1,1,4,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (110,'2011010011      ','Sueldos por Pagar                                                                                                               ',2011010000,1,1,1,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (111,'2011010012      ','Servicios Básicos                                                                                                               ',2011010000,1,1,1,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (112,'2011010013      ','Honorarios Profesionales                                                                                                        ',2011010000,1,1,1,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (113,'2011010014      ','Proyectos                                                                                                                       ',2011010000,1,1,1,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (114,'2011010015      ','Cuotas Anuales                                                                                                                  ',2011010000,1,1,1,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (115,'2011010016      ','Alquileres por Pagar                                                                                                            ',2011010000,1,1,1,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (116,'2011010017      ','Descuentos al Personal                                                                                                          ',2011010000,1,1,1,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (117,'2011010030      ','Otros                                                                                                                           ',2011010000,1,1,1,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (118,'2012000000      ','APORTES Y RETENCIONES                                                                                                           ',2010000000,1,1,1,3,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (119,'2012010000      ','APORTES SOCIALES                                                                                                                ',2012000000,1,1,1,4,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (120,'2012010011      ','Caja Petrolera                                                                                                                  ',2012010000,1,1,1,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (121,'2012010012      ','AFP-Previsión BBV                                                                                                               ',2012010000,1,1,1,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (122,'2012010013      ','AFP-Futuro de Bolivia                                                                                                           ',2012010000,1,1,1,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (123,'2012010014      ','Provivienda                                                                                                                     ',2012010000,1,1,1,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (124,'2012010015      ','INFOCAL                                                                                                                         ',2012010000,1,1,1,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (125,'2012010016      ','Aporte Solidario del Asegurado 0,5%                                                                                             ',2012010000,1,1,1,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (126,'2012010017      ','Aporte Nacional Solidario 1%, 5%, 10%                                                                                           ',2012010000,1,1,1,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (127,'2012020000      ','RETENCIONES                                                                                                                     ',2012000000,1,1,1,4,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (128,'2012020011      ','Retención IUE Servicios 12,5%                                                                                                   ',2012020000,1,1,1,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (129,'2012020012      ','Retención IUE Compras 5%                                                                                                        ',2012020000,1,1,1,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (130,'2012020013      ','Retención I.T. 3%                                                                                                               ',2012020000,1,1,1,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (131,'2012020014      ','Retención RC-IVA 13%                                                                                                            ',2012020000,1,1,1,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (132,'4010104010      ','Ingresos IRAM-IBNORCA SEC                                                                                                       ',2147483647,1,1,1,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (133,'2013000000      ','OBLIGACIONES TRIBUTARIAS                                                                                                        ',2010000000,1,1,1,3,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (134,'2013010000      ','IMPUESTOS POR PAGAR                                                                                                             ',2013000000,1,1,1,4,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (135,'2013010011      ','Impuesto a las Transacciones                                                                                                    ',2013010000,1,1,1,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (136,'2013010012      ','Remesas al Exterior                                                                                                             ',2013010000,1,1,1,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (137,'2013010013      ','Impuesto a la propiedad de Bienes Inmuebles                                                                                     ',2013010000,1,1,1,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (138,'2013010014      ','Impuesto sobre las Utilidades                                                                                                   ',2013010000,1,1,1,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (139,'2013010030      ','Otros Impuestos por Pagar                                                                                                       ',2013010000,1,1,1,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (140,'2013020000      ','DEBITO FISCAL                                                                                                                   ',2013000000,1,1,1,4,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (141,'2013020011      ','Débito Fiscal IVA                                                                                                               ',2013020000,1,1,1,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (142,'2013020012      ','Débito Fiscal Diferido                                                                                                          ',2013020000,1,1,1,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (143,'2014000000      ','PROVISIONES                                                                                                                     ',2010000000,1,1,1,3,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (144,'2014010000      ','PROVISIONES PARA AGUINALDOS                                                                                                     ',2014000000,1,1,1,4,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (145,'2014010010      ','Aguinaldos                                                                                                                      ',2014010000,1,1,1,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (146,'2014020000      ','PROVISIONES PRIMAS Y BONOS                                                                                                      ',2014000000,1,1,1,4,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (147,'2014020010      ','Primas y Bonos                                                                                                                  ',2014020000,1,1,1,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (148,'2014030000      ','PROV. CUENTAS INCOBRABLES                                                                                                       ',2014000000,1,1,1,4,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (149,'2014030011      ','Cuentas incobrables                                                                                                             ',2014030000,1,1,1,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (150,'2015000000      ','OBLIGACIONES COMERCIALES                                                                                                        ',2010000000,1,1,1,3,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (151,'2015010000      ','PROVEEDORES                                                                                                                     ',2015000000,1,1,1,4,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (152,'2015010010      ','Proveedores por Servicios                                                                                                       ',2015010000,1,1,1,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (153,'2015010030      ','Otros proveedores                                                                                                               ',2015010000,1,1,1,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (154,'2015020000      ','OTRAS CUENTAS POR PAGAR                                                                                                         ',2015000000,1,1,1,4,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (155,'2015020011      ','Cuentas por Pagar FOMIN                                                                                                         ',2015020000,1,1,1,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (156,'2015020012      ','Cuentas por Pagar NOREXPORT                                                                                                     ',2015020000,1,1,1,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (157,'2015020013      ','Cuenta por Pagar FAT                                                                                                            ',2015020000,1,1,1,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (158,'2016000000      ','INGRESOS ANTICIPADOS                                                                                                            ',2010000000,1,1,1,3,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (159,'2016010000      ','ANTICIPO CLIENTES                                                                                                               ',2016000000,1,1,1,4,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (160,'2016010011      ','Anticipo de Clientes                                                                                                            ',2016010000,1,1,1,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (161,'2016010012      ','Depósitos por Identificar Cta. BISA Regional',2016010000,1,1,1,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (162,'2016010030      ','Otros Anticipos de Clientes                                                                                                     ',2016010000,1,1,1,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (163,'2016020000      ','INGRESOS ANTICIPADOS                                                                                                            ',2016000000,1,1,1,4,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (164,'2016020011      ','Ingresos por Capacitación                                                                                                       ',2016020000,1,1,1,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (165,'2016020012      ','Ingresos por Servicios                                                                                                          ',2016020000,1,1,1,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (166,'2016020030      ','Otros                                                                                                                           ',2016020000,1,1,1,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (167,'2017000000      ','OTRAS OBLIGACIONES                                                                                                              ',2010000000,1,1,1,3,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (168,'2017010000      ','OTROS                                                                                                                           ',2017000000,1,1,1,4,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (169,'2017010011      ','Control C.F. Diferido                                                                                                           ',2017010000,1,1,1,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (170,'2021000000      ','OBLIGACIONES A LARGO PLAZO                                                                                                      ',2020000000,1,1,1,3,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (171,'2021010000      ','PREVISIONES                                                                                                                     ',2021000000,1,1,1,4,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (172,'2021010011      ','Previsión para Indemnizaciones                                                                                                  ',2021010000,1,1,1,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (173,'2021010012      ','Previsión Futuros Contingentes                                                                                                  ',2021010000,1,1,1,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (174,'2021010030      ','Otros                                                                                                                           ',2021010000,1,1,1,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (175,'3010000000      ','CAPITAL                                                                                                                         ',2147483647,1,1,1,2,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (176,'3010100000      ','CAPITAL SOCIAL                                                                                                                  ',2147483647,1,1,1,3,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (177,'3010101000      ','CAPITAL SOCIAL                                                                                                                  ',2147483647,1,1,1,4,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (178,'3010101001      ','Capital Social                                                                                                                  ',2147483647,1,1,1,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (179,'3010101002      ','Aporte por Donaciones                                                                                                           ',2147483647,1,1,1,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (180,'3010101003      ','Donaciones                                                                                                                      ',2147483647,1,1,1,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (181,'3010200000      ','AJUSTE DE CAPITAL                                                                                                               ',2147483647,1,1,1,3,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (182,'3010201000      ','AJUSTES DE CAPITAL                                                                                                              ',2147483647,1,1,1,4,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (183,'3010201001      ','Ajuste de Capital                                                                                                               ',2147483647,1,1,1,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (184,'3020000000      ','RESERVAS                                                                                                                        ',2147483647,1,1,1,2,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (185,'3020100000      ','RESERVA LEGAL                                                                                                                   ',2147483647,1,1,1,3,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (186,'3020101000      ','RESERVA LEGAL                                                                                                                   ',2147483647,1,1,1,4,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (187,'3020101001      ','Reserva Legal                                                                                                                   ',2147483647,1,1,1,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (188,'3020200000      ','RESERVA POR REVALUO TECNICO                                                                                                     ',2147483647,1,1,1,3,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (189,'3020201000      ','RESERVA POR REVALUO TECNICO                                                                                                     ',2147483647,1,1,1,4,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (190,'3020201001      ','Reserva por Revalúo Técnico                                                                                                     ',2147483647,1,1,1,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (191,'3020300000      ','AJUSTE GLOBAL AL PATRIMONIO                                                                                                     ',2147483647,1,1,1,3,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (192,'3020301000      ','AJUSTE GLOBAL AL PATRIMONIO                                                                                                     ',2147483647,1,1,1,4,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (193,'3020301001      ','Ajustes Global al Patrimonio                                                                                                    ',2147483647,1,1,1,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (194,'3020500000      ','AJUSTE DE RESERVAS DE CAPITAL                                                                                                   ',2147483647,1,1,1,3,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (195,'3020501000      ','AJUSTES  DE RESERVAS DE CAPITAL                                                                                                 ',2147483647,1,1,1,4,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (196,'3020501001      ','Ajuste de Reservas de Capital                                                                                                   ',2147483647,1,1,1,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (197,'3030000000      ','RESULTADOS                                                                                                                      ',2147483647,1,1,1,2,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (198,'3030100000      ','RESULTADOS ACUMULADOS                                                                                                           ',2147483647,1,1,1,3,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (199,'3030101000      ','RESULTADOS ACUMULADOS                                                                                                           ',2147483647,1,1,1,4,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (200,'3030101001      ','Resultados Acumulados                                                                                                           ',2147483647,1,1,1,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (201,'3030200000      ','RESULTADO DE LA GESTION                                                                                                         ',2147483647,1,1,1,3,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (202,'3030201000      ','RESULTADO DE LA GESTION                                                                                                         ',2147483647,1,1,1,4,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (203,'3030201001      ','Resultado de Gestión',2147483647,1,1,1,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (204,'5020000000      ','GASTOS                                                                                                                          ',2147483647,1,1,1,2,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (205,'5020100000      ','GASTOS OPERATIVOS                                                                                                               ',2147483647,1,1,1,3,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (206,'5020106000      ','IMPUESTOS Y PATENTES                                                                                                            ',2147483647,1,1,1,4,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (207,'4010000000      ','INGRESOS OPERATIVOS                                                                                                             ',2147483647,1,1,1,2,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (208,'5010000000      ','CUENTAS DE COSTO                                                                                                                ',2147483647,1,1,1,2,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (209,'5010100000      ','COSTO DE MATERIALES                                                                                                             ',2147483647,1,1,1,3,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (210,'5010101000      ','COSTO DE VENTAS                                                                                                                 ',2147483647,1,1,1,4,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (211,'5010101001      ','Costo de materiales O.I.M.                                                                                                      ',2147483647,1,1,1,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (212,'5010101002      ','Costo de materiales O.I.Q.                                                                                                      ',2147483647,1,1,1,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (213,'5010101003      ','Costo de materiales para la venta                                                                                               ',2147483647,1,1,1,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (214,'5020101000      ','COSTOS DE SERVICIO                                                                                                              ',2147483647,1,1,1,4,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (215,'5020101001      ','Honorarios Auditores Externos                                                                                                   ',2147483647,1,1,1,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (216,'5020101002      ','Honorarios por Docencia                                                                                                         ',2147483647,1,1,1,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (217,'5020101003      ','Servicios Externos                                                                                                              ',2147483647,1,1,1,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (218,'5020101004      ','Alquiler Otros P/Servicios                                                                                                      ',2147483647,1,1,1,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (219,'5020101005      ','Pasajes Viáticos por Servicios                                                                                                  ',2147483647,1,1,1,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (220,'5020101006      ','Refrigerios Cursos - Comites y Otros                                                                                            ',2147483647,1,1,1,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (221,'5020101007      ','Servicios Publicitarios por Servicios                                                                                           ',2147483647,1,1,1,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (222,'5020101008      ','Gastos de Imprenta                                                                                                              ',2147483647,1,1,1,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (223,'5020101010      ','Auspicios y Eventos                                                                                                             ',2147483647,1,1,1,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (224,'5020101030      ','Otros Gastos                                                                                                                    ',2147483647,1,1,1,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (225,'5020102000      ','GASTOS EN PERSONAL DEPENDIENTE                                                                                                  ',2147483647,1,1,1,4,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (226,'5020102001      ','Sueldos al Personal                                                                                                             ',2147483647,1,1,1,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (227,'5020102002      ','Aportes                                                                                                                         ',2147483647,1,1,1,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (228,'5020102003      ','Aguinaldos                                                                                                                      ',2147483647,1,1,1,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (229,'5020102004      ','Indemnizaciones - Vacaciones                                                                                                    ',2147483647,1,1,1,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (230,'5020102005      ','Desahucios                                                                                                                      ',2147483647,1,1,1,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (231,'5020102006      ','Subsidios                                                                                                                       ',2147483647,1,1,1,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (232,'5020102007      ','Refrigerios al Personal                                                                                                         ',2147483647,1,1,1,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (233,'5020103000      ','GASTOS EN PERSONAL EXTERNO                                                                                                      ',2147483647,1,1,1,4,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (234,'5020103001      ','Honorarios Profesionales                                                                                                        ',2147483647,1,1,1,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (235,'5020103002      ','Refrigerios Personal Externo                                                                                                    ',2147483647,1,1,1,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (236,'5020104000      ','GASTOS ADMINISTRATIVOS                                                                                                          ',2147483647,1,1,1,4,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (237,'5020104001      ','Material de escritorio                                                                                                          ',2147483647,1,1,1,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (238,'5020104002      ','Servicios de Courrier                                                                                                           ',2147483647,1,1,1,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (239,'5020104003      ','Servicios y Comunicaciones                                                                                                      ',2147483647,1,1,1,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (240,'5020104004      ','Servicios de Seguridad                                                                                                          ',2147483647,1,1,1,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (241,'5020104005      ','Alquiler de Oficinas                                                                                                            ',2147483647,1,1,1,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (242,'5020104006      ','Seguros                                                                                                                         ',2147483647,1,1,1,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (243,'5020104007      ','Servicios de Fotocopias, Anillados y Otros                                                                                      ',2147483647,1,1,1,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (244,'5020104008      ','Gastos de Imprenta, Enmarcados y Otros                                                                                          ',2147483647,1,1,1,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (245,'5020104009      ','Servicio y Material de Limpieza                                                                                                 ',2147483647,1,1,1,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (246,'5020104010      ','Gastos de Movilidad                                                                                                             ',2147483647,1,1,1,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (247,'5020104011      ','Gastos de representación                                                                                                        ',2147483647,1,1,1,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (248,'5020104012      ','Suscripciones y Cuotas                                                                                                          ',2147483647,1,1,1,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (249,'5020104013      ','Capacitación al Personal                                                                                                        ',2147483647,1,1,1,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (250,'5020104014      ','Intereses y Multas                                                                                                              ',2147483647,1,1,1,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (251,'5020104015      ','Gastos Bancarios                                                                                                                ',2147483647,1,1,1,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (252,'5020104021      ','Reparación y Mantenimiento de Equipos                                                                                           ',2147483647,1,1,1,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (253,'5020104022      ','Reparación y Mantenimiento Instalaciones                                                                                        ',2147483647,1,1,1,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (254,'5020104030      ','Gastos Varios                                                                                                                   ',2147483647,1,1,1,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (255,'5020105000      ','DEPRECIACIONES, AMORTIZACIONES                                                                                                  ',2147483647,1,1,1,4,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (256,'5020105001      ','Depreciación de Activos Fijos                                                                                                   ',2147483647,1,1,1,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (257,'5020105002      ','Amortización de Otros Activos                                                                                                   ',2147483647,1,1,1,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (258,'5020106001      ','Impuesto a las Transacciones Financieras                                                                                        ',2147483647,1,1,1,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (259,'5020106002      ','Impuesto a las Utilidades                                                                                                       ',2147483647,1,1,1,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (260,'5020106003      ','Impuesto a las Transacciones                                                                                                    ',2147483647,1,1,1,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (261,'5020106004      ','Patentes y Licencias                                                                                                            ',2147483647,1,1,1,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (262,'5020111000      ','EGRESOS POR PROYECTOS                                                                                                           ',2147483647,1,1,1,4,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (263,'5020111004      ','Egresos NOREXPORT                                                                                                               ',2147483647,1,1,1,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (264,'5020111005      ','Egresos IRAM - IBNORCA                                                                                                          ',2147483647,1,1,1,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (265,'5020111006      ','Egresos Hecho a Mano                                                                                                            ',2147483647,1,1,1,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (266,'5020111007      ','Egresos CBH - IBNORCA                                                                                                           ',2147483647,1,1,1,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (267,'5020111008      ','Egresos FAT                                                                                                                     ',2147483647,1,1,1,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (268,'4010100000      ','INGRESOS POR SERVICIOS                                                                                                          ',2147483647,1,1,1,3,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (269,'4010101000      ','INGRESOS OI                                                                                                                     ',2147483647,1,1,1,4,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (270,'4010101001      ','Ingresos por Certificación OIM                                                                                                  ',2147483647,1,1,1,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (271,'4010101002      ','Ingresos por Certificación OIQ                                                                                                  ',2147483647,1,1,1,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (272,'4010102000      ','INGRESOS SERVICIOS TECNICOS                                                                                                     ',2147483647,1,1,1,4,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (273,'4010102001      ','Ingresos por Certificación SELLO                                                                                                ',2147483647,1,1,1,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (274,'4010102002      ','Ingresos por Certificación SISTEMAS                                                                                             ',2147483647,1,1,1,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (275,'4010102003      ','Ingresos por  Laboratorio                                                                                                       ',2147483647,1,1,1,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (276,'4010103000      ','INGRESOS VENTAS-NO                                                                                                              ',2147483647,1,1,1,4,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (277,'4010103002      ','Auspicios y Eventos                                                                                                             ',2147483647,1,1,1,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (278,'4010104001      ','Ingresos por Capacitación                                                                                                       ',2147483647,1,1,1,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (279,'4010104000      ','INGRESOS SERVICIOS EXTERNOS                                                                                                     ',2147483647,1,1,1,4,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (280,'4010104002      ','Ingresos cuotas Asociados                                                                                                       ',2147483647,1,1,1,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (281,'4010103001      ','Ingresos Ventas                                                                                                                 ',2147483647,1,1,1,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (282,'4020000000      ','INGRESOS NO OPERATIVOS                                                                                                          ',2147483647,1,1,1,2,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (283,'4020100000      ','INGRESOS MONETARIOS                                                                                                             ',2147483647,1,1,1,3,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (284,'4020101000      ','INGRESOS FINANCIEROS Y OTROS                                                                                                    ',2147483647,1,1,1,4,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (285,'4020101001      ','Intereses Ganados                                                                                                               ',2147483647,1,1,1,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (286,'4020101002      ','Comisiones Ganadas                                                                                                              ',2147483647,1,1,1,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (287,'4020101003      ','Donaciones                                                                                                                      ',2147483647,1,1,1,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (288,'4020101010      ','Otros Ingresos                                                                                                                  ',2147483647,1,1,1,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (289,'4020200000      ','INGRESOS NO MONETARIOS                                                                                                          ',2147483647,1,1,1,3,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (290,'4020210000      ','OTROS INGRESOS                                                                                                                  ',2147483647,1,1,1,4,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (291,'4020210030      ','Otros                                                                                                                           ',2147483647,1,1,1,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (292,'5100000000      ','OTROS INGRESOS Y/O EGRESOS NO OPERATIVOS                                                                                        ',2147483647,1,1,1,2,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (293,'5100100000      ','GANACIAS Y/O PERDIDAS EN VENTAS DE ACTIVOS  FIJOS                                                                               ',2147483647,1,1,1,3,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (294,'5100101000      ','VENTA ACTIVOS FIJOS                                                                                                             ',2147483647,1,1,1,4,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (295,'5100101001      ','Venta Activos Fijos                                                                                                             ',2147483647,1,1,1,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (296,'5100500000      ','AJUSTE POR REEXPRESION MONETARIA                                                                                                ',2147483647,1,1,1,3,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (297,'5100501000      ','AJUSTE POR INFLACION Y TENENCIA DE BIENES                                                                                       ',2147483647,1,1,1,4,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (298,'5100501001      ','Ajuste por Inflacion y Tenencia de Bienes                                                                                       ',2147483647,1,1,1,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (299,'5100501002      ','Resultados por Exposicion  a la Inflacion                                                                                       ',2147483647,1,1,1,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (300,'5100501003      ','Ajuste por Diferencia de Cambio                                                                                                 ',2147483647,1,1,1,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (301,'5100501004      ','Mantenimiento de Valor                                                                                                          ',2147483647,1,1,1,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (302,'5100501005      ','Corrección Monetaria                                                                                                            ',2147483647,1,1,1,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (303,'5100600000      ','PERDIDAS Y GANANCIAS                                                                                                            ',2147483647,1,1,1,3,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (304,'5100601000      ','PERDIDAS Y GANANCIAS                                                                                                            ',2147483647,1,1,1,4,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (305,'5100601001      ','Perdidas y Ganancias                                                                                                            ',2147483647,1,1,1,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (306,'7000000000      ','CUENTAS DE ORDEN                                                                                                                ',NULL,1,1,1,1,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (307,'7010000000      ','CUENTAS DE ORDEN                                                                                                                ',2147483647,1,1,1,2,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (308,'7010100000      ','CUENTAS DE ORDEN  DEUDORAS                                                                                                      ',2147483647,1,1,1,3,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (309,'7010101000      ','CUENTAS DE ORDEN REGIONALES                                                                                                     ',2147483647,1,1,1,4,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (310,'7010101002      ','Regional Cochabamba                                                                                                             ',2147483647,1,1,1,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (311,'7010101003      ','Regional Santa Cruz                                                                                                             ',2147483647,1,1,1,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (312,'7010101004      ','Regional La Paz                                                                                                                 ',2147483647,1,1,1,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (313,'7010101005      ','Regional Chuquisaca                                                                                                             ',2147483647,1,1,1,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (314,'7010101006      ','Regional Tarija                                                                                                                 ',2147483647,1,1,1,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (315,'7010101008      ','Regional Oruro                                                                                                                  ',2147483647,1,1,1,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (316,'7010101009      ','Regional El Alto                                                                                                                ',2147483647,1,1,1,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (317,'8000000000      ','CUENTAS DE ORDEN                                                                                                                ',NULL,1,1,1,1,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (318,'8010000000      ','CUENTAS DE ORDEN                                                                                                                ',2147483647,1,1,1,2,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (319,'8010100000      ','CUENTAS DE ORDEN ACREEDORAS                                                                                                     ',2147483647,1,1,1,3,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (320,'8010101000      ','CUENTAS DE ORDEN REGIONALES                                                                                                     ',2147483647,1,1,1,4,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (321,'8010101002      ','Regional Cochabamba                                                                                                             ',2147483647,1,1,1,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (322,'8010101003      ','Regional Santa Cruz                                                                                                             ',2147483647,1,1,1,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (323,'8010101004      ','Regional La Paz                                                                                                                 ',2147483647,1,1,1,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (324,'8010101005      ','Regional Chuquisaca                                                                                                             ',2147483647,1,1,1,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (325,'8010101006      ','Regional Tarija                                                                                                                 ',2147483647,1,1,1,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (326,'8010101007      ','Regional Oruro                                                                                                                  ',2147483647,1,1,1,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (327,'8010101008      ','Regional El Alto                                                                                                                ',2147483647,1,1,1,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (328,'1020101000      ','MUEBLES Y ENSERES                                                                                                               ',1020100000,1,1,1,4,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (329,'1020101001      ','Muebles y Enseres                                                                                                               ',1020101000,1,1,1,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (330,'1020101002      ','(-) Dep. Acumulada Muebles y Enseres                                                                                            ',1020101000,1,1,1,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (331,'1020102000      ','EQUIPOS DE COMPUTACION                                                                                                          ',1020100000,1,1,1,4,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (332,'1020102001      ','Equipos de Computación                                                                                                          ',1020102000,1,1,1,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (333,'1020102002      ','(-) Dep. Acumulada quipos de Computación                                                                                        ',1020102000,1,1,1,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (334,'1020103000      ','EQUIPOS DE COMUNICACION                                                                                                         ',1020100000,1,1,1,4,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (335,'1020103001      ','Equipos de Comunicación                                                                                                         ',1020103000,1,1,1,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (336,'1020103002      ','(-) Dep. Acumulada Equipos de Comunicación                                                                                      ',1020103000,1,1,1,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (337,'1020104000      ','EQUIPOS DE OFICINA                                                                                                              ',1020100000,1,1,1,4,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (338,'1020104001      ','Equipos de Oficina                                                                                                              ',1020104000,1,1,1,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (339,'1020104002      ','(-) Dep. Acumulada Equipos de oficina                                                                                           ',1020104000,1,1,1,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (340,'1020105000      ','EQUIPO CENT.NAC. DE SOLDADURA                                                                                                   ',1020100000,1,1,1,4,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (341,'1020105001      ','Equipo Cent.Nac. de Soldadura                                                                                                   ',1020105000,1,1,1,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (342,'1020105002      ','(-) Dep. Acumulada Equipos Lab.Soldadura                                                                                        ',1020105000,1,1,1,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (343,'1020106000      ','EQUIPO DE LABORATORIO QUIMICO                                                                                                   ',1020100000,1,1,1,4,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (344,'1020106001      ','Equipo de Laboratorio Quimico                                                                                                   ',1020106000,1,1,1,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (345,'1020106002      ','(-) Dep. Acumulada  Equipo de Lab. Quimico                                                                                      ',1020106000,1,1,1,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (346,'1020107000      ','MATERIAL DE CONSULTA                                                                                                            ',1020100000,1,1,1,4,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (347,'1020107001      ','Material de Consulta                                                                                                            ',1020107000,1,1,1,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (348,'1020107002      ','(-) Dep. Acumulada. Material de Consulta                                                                                        ',1020107000,1,1,1,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (349,'1020108000      ','MEJORAS EN PROPIEDAD ARRENDADA                                                                                                  ',1020100000,1,1,1,4,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (350,'1020108001      ','Mejoras en Popiedad                                                                                                             ',1020108000,1,1,1,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (351,'1020108002      ','(-) Amortiz. Acumulda Mejoras en Propiedad                                                                                      ',1020108000,1,1,1,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (352,'1020109000      ','EDIFICACIONES                                                                                                                   ',1020100000,1,1,1,4,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (353,'1020109001      ','Edificaciones                                                                                                                   ',1020109000,1,1,1,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (354,'1020109002      ','(-)Depreciación Acumulada Edificaciones                                                                                         ',1020109000,1,1,1,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (355,'1020110000      ','TERRENOS                                                                                                                        ',1020100000,1,1,1,4,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (356,'1020110001      ','Terrenos                                                                                                                        ',1020110000,1,1,1,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (357,'1020130000      ','OBRAS EN CONSTRUCCIÓN                                                                                                           ',1020100000,1,1,1,4,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (358,'1020130001      ','Obras en Construcción                                                                                                           ',1020130000,1,1,1,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (359,'1023010018      ','Cuotas de Participación COTEOR                                                                                                  ',1023010000,1,1,1,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (360,'1010230030      ','Otras Cuentas por Cobrar                                                                                                        ',1010230000,1,1,1,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (361,'1010103021      ','Banco BISA  S.A. Cta. Cte. 0131670036 M/N PROY',1010103000,1,1,1,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (362,'4010101010      ','Servicios IRAM-IBNORCA OI                                                                                                       ',2147483647,1,1,1,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (363,'4010102010      ','Servicios IRAM-IBNORCA                                                                                                          ',2147483647,1,1,1,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (364,'4010103010      ','Ingresos IRAM-IBNORCA  NO                                                                                                       ',2147483647,1,1,1,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (365,'1010103015      ','Banco Mercantil Santa Cruz Cta. Cte. M/E',1010103000,1,1,1,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (366,'2021020000      ','PRESTAMOS A LARGO PLAZO                                                                                                         ',2021000000,1,1,1,4,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (367,'2021020001      ','Prestamos Hipotecarios                                                                                                          ',2021020000,1,1,1,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (368,'2015020001      ','Cuentas por Pagar UTILIDAD 40 % SERVICIOS IRAM                                                                                  ',2015020000,1,1,1,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (369,'5020111009      ','Proyecto  CAF                                                                                                                   ',2147483647,1,1,1,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (370,'2021020002      ','Prestamos Bancarios                                                                                                             ',2021020000,1,1,1,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (371,'2013010010      ','Impuestos por Pagar                                                                                                             ',2013010000,1,1,1,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (372,'4010101020      ','Ingresos USAID - IBNORCA                                                                                                        ',2147483647,1,1,1,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (373,'5020111010      ','Egresos  Proyecto USAID                                                                                                         ',2147483647,1,1,1,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (374,'1023010013      ','Cuotas de Participación COMTECO                                                                                                 ',1023010000,1,1,1,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (375,'2016020013      ','Ingresos SEA                                                                                                                    ',2016020000,1,1,1,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (376,'2015020002      ','Cuentas por Pagar CAMARA DE INDUSTRIA ORURO                                                                                     ',2015020000,1,1,1,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (377,'1010230040      ','Cuentas Por Cobrar IRAM                                                                                                         ',1010230000,1,1,1,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (378,'1023010014      ','Cuotas de Participacion COTES                                                                                                   ',1023010000,1,1,1,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (379,'1010260020      ','Regional Prorrateos                                                                                                             ',1010260000,1,1,1,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (380,'1010201950      ','Cuentas Transitorias                                                                                                            ',1010201000,1,1,1,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (381,'1022010014      ','Marcas                                                                                                                          ',1022010000,1,1,1,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (382,'2017010012      ','Otras cuentas transitorias                                                                                                      ',2017010000,1,1,1,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (383,'5020104040      ','Gastos Foro Calidad                                                                                                             ',2147483647,1,1,1,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (384,'5020104041      ','Gastos Premio a la Calidad                                                                                                      ',2147483647,1,1,1,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (385,'1010103040      ','Banco Bisa  Libreta de Inversión 0131674074 M/N',1010103000,1,1,1,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (386,'5020106005      ','Impuestos Inmuebles                                                                                                             ',2147483647,1,1,1,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (387,'1010230050      ','Cuentas por Cobrar Proyectos                                                                                                    ',1010230000,1,1,1,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (388,'1022010015      ','Normateca                                                                                                                       ',1022010000,1,1,1,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (389,'2021030000      ','OTROS                                                                                                                           ',2021000000,1,1,1,4,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (390,'2021030001      ','Otras Obligaciones a Largo Plazo                                                                                                ',2021030000,1,1,1,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (391,'4010102020      ','Ingresos AFNOR-IBNORCA                                                                                                          ',2147483647,1,1,1,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (392,'5020104050      ','Gastos AFNOR-IBNORCA                                                                                                            ',2147483647,1,1,1,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (393,'1010103016      ','Banco Fortaleza S.A. Cta. Cte. 2041001930 M/N                                                                                   ',1010103000,1,1,1,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (394,'2021010013      ','Previsión para Responsabilidad Profesional                                                                                      ',2021010000,1,1,1,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (395,'1022010101      ','Paquete Contable y Software                                                                                                     ',1022010000,1,1,1,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (396,'1022010102      ','(-)Amortización Acumulada Paq.Contable y Software                                                                               ',1022010000,1,1,1,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (397,'1022010201      ','Acreditaciones                                                                                                                  ',1022010000,1,1,1,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (398,'1022010202      ','(-)Amortización Acumulada Acreditaciones                                                                                        ',1022010000,1,1,1,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (399,'1022010301      ','Marcas                                                                                                                          ',1022010000,1,1,1,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (400,'1022010302      ','(-)Amortización Acumulada Marcas                                                                                                ',1022010000,1,1,1,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (401,'1022010401      ','Normateca                                                                                                                       ',1022010000,1,1,1,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (402,'2021010014      ','Previsión para Contingencias Laborales y Otros                                                                                  ',2021010000,1,1,1,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (403,'2015020003      ','Cuentas por Pagar AFNOR                                                                                                         ',2015020000,1,1,1,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (404,'5020104023      ','Pérdidas en Cuentas Incobrables                                                                                                 ',2147483647,1,1,1,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (405,'1022010402      ','(-) Amortización Acumulada Normateca                                                                                            ',1022010000,1,1,1,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (406,'1010102050      ','Caja Chica Proyectos                                                                                                            ',1010102000,1,1,1,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (407,'1010260017','Regional Cta. Cte. Potosi',1010260000,1,1,1,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (408,'1010102007','Caja Chica Potosí',1010102000,1,1,1,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (409,'2015030000','PRESTAMOS A CORTO PLAZO',2015000000,1,1,1,4,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (410,'2015030001','Préstamos Hipotecarios',2015030000,1,1,1,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (411,'2015030002','Préstamos Bancarios',2015030000,1,1,1,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (412,'2016010013','Depósitos por Identificar Otros Bancos',2016010000,1,1,1,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (413,'5020104016','Contingencias Laborales y Otros',2147483647,1,1,1,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (414,'3010300000','PATRIMONIO EN BIENES',2147483647,1,1,1,3,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (415,'3010301000','Patrimonio en Bienes',2147483647,1,1,1,4,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (416,'4020101004','Contribuciones Proyectos',2147483647,1,1,1,5,NULL,NULL);
INSERT INTO `plan_cuentas` VALUES (431,'1010102051','CAJA CHICA X',1010102000,3,2,1,5,'',NULL);
INSERT INTO `plan_cuentas` VALUES (432,'1010102052','CAJA CHICA YY',1010102000,1,2,1,5,'',NULL);
INSERT INTO `plan_cuentas` VALUES (433,'1010102053','CAJA CHICA TEST',1010102000,1,2,3,5,'',NULL);
INSERT INTO `plan_cuentas` VALUES (434,'1010102054','CAJA CHICA TRINIDAD',1010102000,1,1,3,5,'',NULL);
INSERT INTO `plan_cuentas` VALUES (435,'1010102055','CAJA CHICA TEST',1010102000,1,1,1,5,'',NULL);
INSERT INTO `plan_cuentas` VALUES (436,'1010102056','CAJA CHICA TEST2',1010102000,1,1,2,5,'',NULL);
INSERT INTO `plan_cuentas` VALUES (437,'1010103041','BANCO TEST1',1010103000,1,1,1,5,'',NULL);
INSERT INTO `plan_cuentas` VALUES (438,'1010103042','BANCO TEST2',1010103000,1,1,1,5,'',NULL);
INSERT INTO `plan_cuentas` VALUES (439,'1010103043','BANCO TEST3',1010103000,1,1,1,5,'',1);
INSERT INTO `plan_cuentas` VALUES (440,'1010103044','BANCO TEST4',1010103000,2,1,1,5,'',1);
INSERT INTO `plan_cuentas` VALUES (441,'1010101002','CAJA TEST 2',1010101000,1,1,1,5,'',1);
/*!40000 ALTER TABLE `plan_cuentas` ENABLE KEYS */;
UNLOCK TABLES;

#
# Source for table plantillas_comprobante
#

DROP TABLE IF EXISTS `plantillas_comprobante`;
CREATE TABLE `plantillas_comprobante` (
  `codigo` int(11) NOT NULL AUTO_INCREMENT,
  `cod_unidadorganizacional` int(11) DEFAULT NULL,
  `titulo` varchar(255) COLLATE utf8_spanish2_ci DEFAULT NULL,
  `descripcion` varchar(800) COLLATE utf8_spanish2_ci DEFAULT NULL,
  `archivo_json` longtext CHARACTER SET utf8 DEFAULT NULL,
  `cod_personal` int(11) DEFAULT NULL,
  PRIMARY KEY (`codigo`)
) ENGINE=InnoDB AUTO_INCREMENT=66 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

#
# Dumping data for table plantillas_comprobante
#

LOCK TABLES `plantillas_comprobante` WRITE;
/*!40000 ALTER TABLE `plantillas_comprobante` DISABLE KEYS */;
/*!40000 ALTER TABLE `plantillas_comprobante` ENABLE KEYS */;
UNLOCK TABLES;

#
# Source for table tipos_comprobante
#

DROP TABLE IF EXISTS `tipos_comprobante`;
CREATE TABLE `tipos_comprobante` (
  `codigo` int(11) NOT NULL,
  `nombre` varchar(50) DEFAULT NULL,
  `abreviatura` varchar(20) DEFAULT NULL,
  `cod_estadoreferencial` int(11) DEFAULT NULL,
  PRIMARY KEY (`codigo`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

#
# Dumping data for table tipos_comprobante
#

LOCK TABLES `tipos_comprobante` WRITE;
/*!40000 ALTER TABLE `tipos_comprobante` DISABLE KEYS */;
INSERT INTO `tipos_comprobante` VALUES (1,'Ingreso','Ing',1);
INSERT INTO `tipos_comprobante` VALUES (2,'Egreso','Eg',1);
INSERT INTO `tipos_comprobante` VALUES (3,'Traspaso','Tras',1);
/*!40000 ALTER TABLE `tipos_comprobante` ENABLE KEYS */;
UNLOCK TABLES;

#
# Source for table tipos_cuenta
#

DROP TABLE IF EXISTS `tipos_cuenta`;
CREATE TABLE `tipos_cuenta` (
  `codigo` int(11) NOT NULL,
  `nombre` varchar(50) DEFAULT NULL,
  `abreviatura` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`codigo`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

#
# Dumping data for table tipos_cuenta
#

LOCK TABLES `tipos_cuenta` WRITE;
/*!40000 ALTER TABLE `tipos_cuenta` DISABLE KEYS */;
INSERT INTO `tipos_cuenta` VALUES (1,'Balance','Bal');
INSERT INTO `tipos_cuenta` VALUES (2,'Orden','Ord');
INSERT INTO `tipos_cuenta` VALUES (3,'Resultado','Res');
/*!40000 ALTER TABLE `tipos_cuenta` ENABLE KEYS */;
UNLOCK TABLES;

#
# Source for table tiposbienes
#

DROP TABLE IF EXISTS `tiposbienes`;
CREATE TABLE `tiposbienes` (
  `codigo` int(11) NOT NULL AUTO_INCREMENT,
  `cod_depreciaciones` int(11) DEFAULT NULL,
  `tipo_bien` varchar(255) DEFAULT '',
  PRIMARY KEY (`codigo`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

#
# Dumping data for table tiposbienes
#

LOCK TABLES `tiposbienes` WRITE;
/*!40000 ALTER TABLE `tiposbienes` DISABLE KEYS */;
/*!40000 ALTER TABLE `tiposbienes` ENABLE KEYS */;
UNLOCK TABLES;

#
# Source for table ubicaciones
#

DROP TABLE IF EXISTS `ubicaciones`;
CREATE TABLE `ubicaciones` (
  `codigo` int(11) NOT NULL AUTO_INCREMENT,
  `cod_unidades_organizacionales` int(11) DEFAULT NULL,
  `edificio` varchar(255) DEFAULT NULL,
  `oficina` varchar(255) DEFAULT NULL,
  `cod_estado` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `created_by` varchar(500) DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  `modified_by` varchar(500) DEFAULT NULL,
  `cod_areas` int(11) DEFAULT NULL,
  PRIMARY KEY (`codigo`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;

#
# Dumping data for table ubicaciones
#

LOCK TABLES `ubicaciones` WRITE;
/*!40000 ALTER TABLE `ubicaciones` DISABLE KEYS */;
INSERT INTO `ubicaciones` VALUES (1,3,'OBRAJES CALLE 7','GENERAL',1,NULL,NULL,NULL,NULL,3);
INSERT INTO `ubicaciones` VALUES (2,3,'OBRAJES EDIFICIO 2','GENERAL',2,NULL,NULL,NULL,NULL,273);
/*!40000 ALTER TABLE `ubicaciones` ENABLE KEYS */;
UNLOCK TABLES;

#
# Source for table ufvs
#

DROP TABLE IF EXISTS `ufvs`;
CREATE TABLE `ufvs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fecha` date DEFAULT NULL,
  `valor` double DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=315 DEFAULT CHARSET=latin1;

#
# Dumping data for table ufvs
#

LOCK TABLES `ufvs` WRITE;
/*!40000 ALTER TABLE `ufvs` DISABLE KEYS */;
INSERT INTO `ufvs` VALUES (1,'2019-01-01',2.29085);
INSERT INTO `ufvs` VALUES (2,'2019-01-02',2.29094);
INSERT INTO `ufvs` VALUES (3,'2019-01-03',2.29103);
INSERT INTO `ufvs` VALUES (4,'2019-01-04',2.29112);
INSERT INTO `ufvs` VALUES (5,'2019-01-05',2.29121);
INSERT INTO `ufvs` VALUES (6,'2019-01-06',2.2913);
INSERT INTO `ufvs` VALUES (7,'2019-01-07',2.29139);
INSERT INTO `ufvs` VALUES (8,'2019-01-08',2.29148);
INSERT INTO `ufvs` VALUES (9,'2019-01-09',2.29157);
INSERT INTO `ufvs` VALUES (10,'2019-01-10',2.29166);
INSERT INTO `ufvs` VALUES (11,'2019-01-11',2.29175);
INSERT INTO `ufvs` VALUES (12,'2019-01-12',2.29184);
INSERT INTO `ufvs` VALUES (13,'2019-01-13',2.29193);
INSERT INTO `ufvs` VALUES (14,'2019-01-14',2.29202);
INSERT INTO `ufvs` VALUES (15,'2019-01-15',2.29211);
INSERT INTO `ufvs` VALUES (16,'2019-01-16',2.2922);
INSERT INTO `ufvs` VALUES (17,'2019-01-17',2.29229);
INSERT INTO `ufvs` VALUES (18,'2019-01-18',2.29238);
INSERT INTO `ufvs` VALUES (19,'2019-01-19',2.29247);
INSERT INTO `ufvs` VALUES (20,'2019-01-20',2.29256);
INSERT INTO `ufvs` VALUES (21,'2019-01-21',2.29265);
INSERT INTO `ufvs` VALUES (22,'2019-01-22',2.29274);
INSERT INTO `ufvs` VALUES (23,'2019-01-23',2.29283);
INSERT INTO `ufvs` VALUES (24,'2019-01-24',2.29292);
INSERT INTO `ufvs` VALUES (25,'2019-01-25',2.29301);
INSERT INTO `ufvs` VALUES (26,'2019-01-26',2.2931);
INSERT INTO `ufvs` VALUES (27,'2019-01-27',2.29319);
INSERT INTO `ufvs` VALUES (28,'2019-01-28',2.29328);
INSERT INTO `ufvs` VALUES (29,'2019-01-29',2.29337);
INSERT INTO `ufvs` VALUES (30,'2019-01-30',2.29346);
INSERT INTO `ufvs` VALUES (31,'2019-01-31',2.29355);
INSERT INTO `ufvs` VALUES (32,'2019-02-01',2.29365);
INSERT INTO `ufvs` VALUES (33,'2019-02-02',2.29375);
INSERT INTO `ufvs` VALUES (34,'2019-02-03',2.29385);
INSERT INTO `ufvs` VALUES (35,'2019-02-04',2.29395);
INSERT INTO `ufvs` VALUES (36,'2019-02-05',2.29405);
INSERT INTO `ufvs` VALUES (37,'2019-02-06',2.29415);
INSERT INTO `ufvs` VALUES (38,'2019-02-07',2.29425);
INSERT INTO `ufvs` VALUES (39,'2019-02-08',2.29435);
INSERT INTO `ufvs` VALUES (40,'2019-02-09',2.29445);
INSERT INTO `ufvs` VALUES (41,'2019-02-10',2.29455);
INSERT INTO `ufvs` VALUES (42,'2019-02-11',2.29465);
INSERT INTO `ufvs` VALUES (43,'2019-02-12',2.29475);
INSERT INTO `ufvs` VALUES (44,'2019-02-13',2.29485);
INSERT INTO `ufvs` VALUES (45,'2019-02-14',2.29495);
INSERT INTO `ufvs` VALUES (46,'2019-02-15',2.29505);
INSERT INTO `ufvs` VALUES (47,'2019-02-16',2.29515);
INSERT INTO `ufvs` VALUES (48,'2019-02-17',2.29525);
INSERT INTO `ufvs` VALUES (49,'2019-02-18',2.29535);
INSERT INTO `ufvs` VALUES (50,'2019-02-19',2.29545);
INSERT INTO `ufvs` VALUES (51,'2019-02-20',2.29555);
INSERT INTO `ufvs` VALUES (52,'2019-02-21',2.29565);
INSERT INTO `ufvs` VALUES (53,'2019-02-22',2.29575);
INSERT INTO `ufvs` VALUES (54,'2019-02-23',2.29585);
INSERT INTO `ufvs` VALUES (55,'2019-02-24',2.29595);
INSERT INTO `ufvs` VALUES (56,'2019-02-25',2.29605);
INSERT INTO `ufvs` VALUES (57,'2019-02-26',2.29615);
INSERT INTO `ufvs` VALUES (58,'2019-02-27',2.29625);
INSERT INTO `ufvs` VALUES (59,'2019-02-28',2.29635);
INSERT INTO `ufvs` VALUES (60,'2019-03-01',2.29644);
INSERT INTO `ufvs` VALUES (61,'2019-03-02',2.29653);
INSERT INTO `ufvs` VALUES (62,'2019-03-03',2.29662);
INSERT INTO `ufvs` VALUES (63,'2019-03-04',2.29671);
INSERT INTO `ufvs` VALUES (64,'2019-03-05',2.2968);
INSERT INTO `ufvs` VALUES (65,'2019-03-06',2.29689);
INSERT INTO `ufvs` VALUES (66,'2019-03-07',2.29698);
INSERT INTO `ufvs` VALUES (67,'2019-03-08',2.29707);
INSERT INTO `ufvs` VALUES (68,'2019-03-09',2.29716);
INSERT INTO `ufvs` VALUES (69,'2019-03-10',2.29725);
INSERT INTO `ufvs` VALUES (70,'2019-03-11',2.29731);
INSERT INTO `ufvs` VALUES (71,'2019-03-12',2.29737);
INSERT INTO `ufvs` VALUES (72,'2019-03-13',2.29743);
INSERT INTO `ufvs` VALUES (73,'2019-03-14',2.29749);
INSERT INTO `ufvs` VALUES (74,'2019-03-15',2.29755);
INSERT INTO `ufvs` VALUES (75,'2019-03-16',2.29761);
INSERT INTO `ufvs` VALUES (76,'2019-03-17',2.29767);
INSERT INTO `ufvs` VALUES (77,'2019-03-18',2.29773);
INSERT INTO `ufvs` VALUES (78,'2019-03-19',2.29779);
INSERT INTO `ufvs` VALUES (79,'2019-03-20',2.29785);
INSERT INTO `ufvs` VALUES (80,'2019-03-21',2.29791);
INSERT INTO `ufvs` VALUES (81,'2019-03-22',2.29797);
INSERT INTO `ufvs` VALUES (82,'2019-03-23',2.29803);
INSERT INTO `ufvs` VALUES (83,'2019-03-24',2.29809);
INSERT INTO `ufvs` VALUES (84,'2019-03-25',2.29815);
INSERT INTO `ufvs` VALUES (85,'2019-03-26',2.29821);
INSERT INTO `ufvs` VALUES (86,'2019-03-27',2.29827);
INSERT INTO `ufvs` VALUES (87,'2019-03-28',2.29833);
INSERT INTO `ufvs` VALUES (88,'2019-03-29',2.29839);
INSERT INTO `ufvs` VALUES (89,'2019-03-30',2.29845);
INSERT INTO `ufvs` VALUES (90,'2019-03-31',2.29851);
INSERT INTO `ufvs` VALUES (91,'2019-04-01',2.29857);
INSERT INTO `ufvs` VALUES (92,'2019-04-02',2.29863);
INSERT INTO `ufvs` VALUES (93,'2019-04-03',2.29869);
INSERT INTO `ufvs` VALUES (94,'2019-04-04',2.29875);
INSERT INTO `ufvs` VALUES (95,'2019-04-05',2.29881);
INSERT INTO `ufvs` VALUES (96,'2019-04-06',2.29887);
INSERT INTO `ufvs` VALUES (97,'2019-04-07',2.29893);
INSERT INTO `ufvs` VALUES (98,'2019-04-08',2.29899);
INSERT INTO `ufvs` VALUES (99,'2019-04-09',2.29905);
INSERT INTO `ufvs` VALUES (100,'2019-04-10',2.29911);
INSERT INTO `ufvs` VALUES (101,'2019-04-11',2.29918);
INSERT INTO `ufvs` VALUES (102,'2019-04-12',2.29925);
INSERT INTO `ufvs` VALUES (103,'2019-04-13',2.29932);
INSERT INTO `ufvs` VALUES (104,'2019-04-14',2.29939);
INSERT INTO `ufvs` VALUES (105,'2019-04-15',2.29946);
INSERT INTO `ufvs` VALUES (106,'2019-04-16',2.29953);
INSERT INTO `ufvs` VALUES (107,'2019-04-17',2.2996);
INSERT INTO `ufvs` VALUES (108,'2019-04-18',2.29967);
INSERT INTO `ufvs` VALUES (109,'2019-04-19',2.29974);
INSERT INTO `ufvs` VALUES (110,'2019-04-20',2.29981);
INSERT INTO `ufvs` VALUES (111,'2019-04-21',2.29988);
INSERT INTO `ufvs` VALUES (112,'2019-04-22',2.29995);
INSERT INTO `ufvs` VALUES (113,'2019-04-23',2.30002);
INSERT INTO `ufvs` VALUES (114,'2019-04-24',2.30009);
INSERT INTO `ufvs` VALUES (115,'2019-04-25',2.30016);
INSERT INTO `ufvs` VALUES (116,'2019-04-26',2.30023);
INSERT INTO `ufvs` VALUES (117,'2019-04-27',2.3003);
INSERT INTO `ufvs` VALUES (118,'2019-04-28',2.30037);
INSERT INTO `ufvs` VALUES (119,'2019-04-29',2.30044);
INSERT INTO `ufvs` VALUES (120,'2019-04-30',2.30051);
INSERT INTO `ufvs` VALUES (121,'2019-05-01',2.30058);
INSERT INTO `ufvs` VALUES (122,'2019-05-02',2.30065);
INSERT INTO `ufvs` VALUES (123,'2019-05-03',2.30072);
INSERT INTO `ufvs` VALUES (124,'2019-05-04',2.30079);
INSERT INTO `ufvs` VALUES (125,'2019-05-05',2.30086);
INSERT INTO `ufvs` VALUES (126,'2019-05-06',2.30093);
INSERT INTO `ufvs` VALUES (127,'2019-05-07',2.301);
INSERT INTO `ufvs` VALUES (128,'2019-05-08',2.30107);
INSERT INTO `ufvs` VALUES (129,'2019-05-09',2.30114);
INSERT INTO `ufvs` VALUES (130,'2019-05-10',2.30121);
INSERT INTO `ufvs` VALUES (131,'2019-05-11',2.30129);
INSERT INTO `ufvs` VALUES (132,'2019-05-12',2.30137);
INSERT INTO `ufvs` VALUES (133,'2019-05-13',2.30145);
INSERT INTO `ufvs` VALUES (134,'2019-05-14',2.30153);
INSERT INTO `ufvs` VALUES (135,'2019-05-15',2.30161);
INSERT INTO `ufvs` VALUES (136,'2019-05-16',2.30169);
INSERT INTO `ufvs` VALUES (137,'2019-05-17',2.30177);
INSERT INTO `ufvs` VALUES (138,'2019-05-18',2.30185);
INSERT INTO `ufvs` VALUES (139,'2019-05-19',2.30193);
INSERT INTO `ufvs` VALUES (140,'2019-05-20',2.30201);
INSERT INTO `ufvs` VALUES (141,'2019-05-21',2.30209);
INSERT INTO `ufvs` VALUES (142,'2019-05-22',2.30217);
INSERT INTO `ufvs` VALUES (143,'2019-05-23',2.30225);
INSERT INTO `ufvs` VALUES (144,'2019-05-24',2.30233);
INSERT INTO `ufvs` VALUES (145,'2019-05-25',2.30241);
INSERT INTO `ufvs` VALUES (146,'2019-05-26',2.30249);
INSERT INTO `ufvs` VALUES (147,'2019-05-27',2.30257);
INSERT INTO `ufvs` VALUES (148,'2019-05-28',2.30265);
INSERT INTO `ufvs` VALUES (149,'2019-05-29',2.30273);
INSERT INTO `ufvs` VALUES (150,'2019-05-30',2.30281);
INSERT INTO `ufvs` VALUES (151,'2019-05-31',2.30289);
INSERT INTO `ufvs` VALUES (152,'2019-06-01',2.30298);
INSERT INTO `ufvs` VALUES (153,'2019-06-02',2.30307);
INSERT INTO `ufvs` VALUES (154,'2019-06-03',2.30316);
INSERT INTO `ufvs` VALUES (155,'2019-06-04',2.30325);
INSERT INTO `ufvs` VALUES (156,'2019-06-05',2.30334);
INSERT INTO `ufvs` VALUES (157,'2019-06-06',2.30343);
INSERT INTO `ufvs` VALUES (158,'2019-06-07',2.30352);
INSERT INTO `ufvs` VALUES (159,'2019-06-08',2.30361);
INSERT INTO `ufvs` VALUES (160,'2019-06-09',2.3037);
INSERT INTO `ufvs` VALUES (161,'2019-06-10',2.30379);
INSERT INTO `ufvs` VALUES (162,'2019-06-11',2.3039);
INSERT INTO `ufvs` VALUES (163,'2019-06-12',2.30401);
INSERT INTO `ufvs` VALUES (164,'2019-06-13',2.30412);
INSERT INTO `ufvs` VALUES (165,'2019-06-14',2.30423);
INSERT INTO `ufvs` VALUES (166,'2019-06-15',2.30434);
INSERT INTO `ufvs` VALUES (167,'2019-06-16',2.30445);
INSERT INTO `ufvs` VALUES (168,'2019-06-17',2.30456);
INSERT INTO `ufvs` VALUES (169,'2019-06-18',2.30467);
INSERT INTO `ufvs` VALUES (170,'2019-06-19',2.30478);
INSERT INTO `ufvs` VALUES (171,'2019-06-20',2.30489);
INSERT INTO `ufvs` VALUES (172,'2019-06-21',2.305);
INSERT INTO `ufvs` VALUES (173,'2019-06-22',2.30511);
INSERT INTO `ufvs` VALUES (174,'2019-06-23',2.30522);
INSERT INTO `ufvs` VALUES (175,'2019-06-24',2.30533);
INSERT INTO `ufvs` VALUES (176,'2019-06-25',2.30544);
INSERT INTO `ufvs` VALUES (177,'2019-06-26',2.30555);
INSERT INTO `ufvs` VALUES (178,'2019-06-27',2.30566);
INSERT INTO `ufvs` VALUES (179,'2019-06-28',2.30577);
INSERT INTO `ufvs` VALUES (180,'2019-06-29',2.30588);
INSERT INTO `ufvs` VALUES (181,'2019-06-30',2.30599);
INSERT INTO `ufvs` VALUES (182,'2019-07-01',2.30609);
INSERT INTO `ufvs` VALUES (183,'2019-07-02',2.30619);
INSERT INTO `ufvs` VALUES (184,'2019-07-03',2.30629);
INSERT INTO `ufvs` VALUES (185,'2019-07-04',2.30639);
INSERT INTO `ufvs` VALUES (186,'2019-07-05',2.30649);
INSERT INTO `ufvs` VALUES (187,'2019-07-06',2.30659);
INSERT INTO `ufvs` VALUES (188,'2019-07-07',2.30669);
INSERT INTO `ufvs` VALUES (189,'2019-07-08',2.30679);
INSERT INTO `ufvs` VALUES (190,'2019-07-09',2.30689);
INSERT INTO `ufvs` VALUES (191,'2019-07-10',2.30699);
INSERT INTO `ufvs` VALUES (192,'2019-07-11',2.3071);
INSERT INTO `ufvs` VALUES (193,'2019-07-12',2.30721);
INSERT INTO `ufvs` VALUES (194,'2019-07-13',2.30732);
INSERT INTO `ufvs` VALUES (195,'2019-07-14',2.30743);
INSERT INTO `ufvs` VALUES (196,'2019-07-15',2.30754);
INSERT INTO `ufvs` VALUES (197,'2019-07-16',2.30765);
INSERT INTO `ufvs` VALUES (198,'2019-07-17',2.30776);
INSERT INTO `ufvs` VALUES (199,'2019-07-18',2.30787);
INSERT INTO `ufvs` VALUES (200,'2019-07-19',2.30798);
INSERT INTO `ufvs` VALUES (201,'2019-07-20',2.30809);
INSERT INTO `ufvs` VALUES (202,'2019-07-21',2.3082);
INSERT INTO `ufvs` VALUES (203,'2019-07-22',2.30831);
INSERT INTO `ufvs` VALUES (204,'2019-07-23',2.30842);
INSERT INTO `ufvs` VALUES (205,'2019-07-24',2.30853);
INSERT INTO `ufvs` VALUES (206,'2019-07-25',2.30864);
INSERT INTO `ufvs` VALUES (207,'2019-07-26',2.30875);
INSERT INTO `ufvs` VALUES (208,'2019-07-27',2.30886);
INSERT INTO `ufvs` VALUES (209,'2019-07-28',2.30897);
INSERT INTO `ufvs` VALUES (210,'2019-07-29',2.30908);
INSERT INTO `ufvs` VALUES (211,'2019-07-30',2.30919);
INSERT INTO `ufvs` VALUES (212,'2019-07-31',2.3093);
INSERT INTO `ufvs` VALUES (213,'2019-08-01',2.30941);
INSERT INTO `ufvs` VALUES (214,'2019-08-02',2.30952);
INSERT INTO `ufvs` VALUES (215,'2019-08-03',2.30963);
INSERT INTO `ufvs` VALUES (216,'2019-08-04',2.30974);
INSERT INTO `ufvs` VALUES (217,'2019-08-05',2.30985);
INSERT INTO `ufvs` VALUES (218,'2019-08-06',2.30996);
INSERT INTO `ufvs` VALUES (219,'2019-08-07',2.31007);
INSERT INTO `ufvs` VALUES (220,'2019-08-08',2.31018);
INSERT INTO `ufvs` VALUES (221,'2019-08-09',2.31029);
INSERT INTO `ufvs` VALUES (222,'2019-08-10',2.3104);
INSERT INTO `ufvs` VALUES (223,'2019-08-11',2.31052);
INSERT INTO `ufvs` VALUES (224,'2019-08-12',2.31064);
INSERT INTO `ufvs` VALUES (225,'2019-08-13',2.31076);
INSERT INTO `ufvs` VALUES (226,'2019-08-14',2.31088);
INSERT INTO `ufvs` VALUES (227,'2019-08-15',2.311);
INSERT INTO `ufvs` VALUES (228,'2019-08-16',2.31112);
INSERT INTO `ufvs` VALUES (229,'2019-08-17',2.31124);
INSERT INTO `ufvs` VALUES (230,'2019-08-18',2.31136);
INSERT INTO `ufvs` VALUES (231,'2019-08-19',2.31148);
INSERT INTO `ufvs` VALUES (232,'2019-08-20',2.3116);
INSERT INTO `ufvs` VALUES (233,'2019-08-21',2.31172);
INSERT INTO `ufvs` VALUES (234,'2019-08-22',2.31184);
INSERT INTO `ufvs` VALUES (235,'2019-08-23',2.31196);
INSERT INTO `ufvs` VALUES (236,'2019-08-24',2.31208);
INSERT INTO `ufvs` VALUES (237,'2019-08-25',2.3122);
INSERT INTO `ufvs` VALUES (238,'2019-08-26',2.31232);
INSERT INTO `ufvs` VALUES (239,'2019-08-27',2.31244);
INSERT INTO `ufvs` VALUES (240,'2019-08-28',2.31256);
INSERT INTO `ufvs` VALUES (241,'2019-08-29',2.31268);
INSERT INTO `ufvs` VALUES (242,'2019-08-30',2.3128);
INSERT INTO `ufvs` VALUES (243,'2019-08-31',2.31292);
INSERT INTO `ufvs` VALUES (244,'2019-09-01',2.31304);
INSERT INTO `ufvs` VALUES (245,'2019-09-02',2.31316);
INSERT INTO `ufvs` VALUES (246,'2019-09-03',2.31328);
INSERT INTO `ufvs` VALUES (247,'2019-09-04',2.3134);
INSERT INTO `ufvs` VALUES (248,'2019-09-05',2.31352);
INSERT INTO `ufvs` VALUES (249,'2019-09-06',2.31364);
INSERT INTO `ufvs` VALUES (250,'2019-09-07',2.31376);
INSERT INTO `ufvs` VALUES (251,'2019-09-08',2.31388);
INSERT INTO `ufvs` VALUES (252,'2019-09-09',2.314);
INSERT INTO `ufvs` VALUES (253,'2019-09-10',2.31412);
INSERT INTO `ufvs` VALUES (254,'2019-09-11',2.31426);
INSERT INTO `ufvs` VALUES (255,'2019-09-12',2.3144);
INSERT INTO `ufvs` VALUES (256,'2019-09-13',2.31454);
INSERT INTO `ufvs` VALUES (257,'2019-09-14',2.31468);
INSERT INTO `ufvs` VALUES (258,'2019-09-15',2.31482);
INSERT INTO `ufvs` VALUES (259,'2019-09-16',2.31496);
INSERT INTO `ufvs` VALUES (260,'2019-09-17',2.3151);
INSERT INTO `ufvs` VALUES (261,'2019-09-18',2.31524);
INSERT INTO `ufvs` VALUES (262,'2019-09-19',2.31538);
INSERT INTO `ufvs` VALUES (263,'2019-09-20',2.31552);
INSERT INTO `ufvs` VALUES (264,'2019-09-21',2.31566);
INSERT INTO `ufvs` VALUES (265,'2019-09-22',2.3158);
INSERT INTO `ufvs` VALUES (266,'2019-09-23',2.31594);
INSERT INTO `ufvs` VALUES (267,'2019-09-24',2.31608);
INSERT INTO `ufvs` VALUES (268,'2019-09-25',2.31622);
INSERT INTO `ufvs` VALUES (269,'2019-09-26',2.31636);
INSERT INTO `ufvs` VALUES (270,'2019-09-27',2.3165);
INSERT INTO `ufvs` VALUES (271,'2019-09-28',2.31664);
INSERT INTO `ufvs` VALUES (272,'2019-09-29',2.31678);
INSERT INTO `ufvs` VALUES (273,'2019-09-30',2.31692);
INSERT INTO `ufvs` VALUES (274,'2019-10-01',2.31706);
INSERT INTO `ufvs` VALUES (275,'2019-10-02',2.3172);
INSERT INTO `ufvs` VALUES (276,'2019-10-03',2.31734);
INSERT INTO `ufvs` VALUES (277,'2019-10-04',2.31748);
INSERT INTO `ufvs` VALUES (278,'2019-10-05',2.31762);
INSERT INTO `ufvs` VALUES (279,'2019-10-06',2.31776);
INSERT INTO `ufvs` VALUES (280,'2019-10-07',2.3179);
INSERT INTO `ufvs` VALUES (281,'2019-10-08',2.31804);
INSERT INTO `ufvs` VALUES (282,'2019-10-09',2.31818);
INSERT INTO `ufvs` VALUES (283,'2019-10-10',2.31832);
INSERT INTO `ufvs` VALUES (284,'2019-10-11',2.31846);
INSERT INTO `ufvs` VALUES (285,'2019-10-12',2.3186);
INSERT INTO `ufvs` VALUES (286,'2019-10-13',2.31874);
INSERT INTO `ufvs` VALUES (287,'2019-10-14',2.31888);
INSERT INTO `ufvs` VALUES (288,'2019-10-15',2.31902);
INSERT INTO `ufvs` VALUES (289,'2019-10-16',2.31916);
INSERT INTO `ufvs` VALUES (290,'2019-10-17',2.3193);
INSERT INTO `ufvs` VALUES (291,'2019-10-18',2.31944);
INSERT INTO `ufvs` VALUES (292,'2019-10-19',2.31958);
INSERT INTO `ufvs` VALUES (293,'2019-10-20',2.31972);
INSERT INTO `ufvs` VALUES (294,'2019-10-21',2.31986);
INSERT INTO `ufvs` VALUES (295,'2019-10-22',2.32);
INSERT INTO `ufvs` VALUES (296,'2019-10-23',2.32014);
INSERT INTO `ufvs` VALUES (297,'2019-10-24',2.32028);
INSERT INTO `ufvs` VALUES (298,'2019-10-25',2.32042);
INSERT INTO `ufvs` VALUES (299,'2019-10-26',2.32056);
INSERT INTO `ufvs` VALUES (300,'2019-10-27',2.3207);
INSERT INTO `ufvs` VALUES (301,'2019-10-28',2.32084);
INSERT INTO `ufvs` VALUES (302,'2019-10-29',2.32098);
INSERT INTO `ufvs` VALUES (303,'2019-10-30',2.32112);
INSERT INTO `ufvs` VALUES (304,'2019-10-31',2.32126);
INSERT INTO `ufvs` VALUES (305,'2019-11-01',2.3214);
INSERT INTO `ufvs` VALUES (306,'2019-11-02',2.32154);
INSERT INTO `ufvs` VALUES (307,'2019-11-03',2.32168);
INSERT INTO `ufvs` VALUES (308,'2019-11-04',2.32182);
INSERT INTO `ufvs` VALUES (309,'2019-11-05',2.32196);
INSERT INTO `ufvs` VALUES (310,'2019-11-06',2.3221);
INSERT INTO `ufvs` VALUES (311,'2019-11-07',2.32224);
INSERT INTO `ufvs` VALUES (312,'2019-11-08',2.32238);
INSERT INTO `ufvs` VALUES (313,'2019-11-09',2.32252);
INSERT INTO `ufvs` VALUES (314,'2019-11-10',2.32266);
/*!40000 ALTER TABLE `ufvs` ENABLE KEYS */;
UNLOCK TABLES;

#
# Source for table unidades_organizacionales
#

DROP TABLE IF EXISTS `unidades_organizacionales`;
CREATE TABLE `unidades_organizacionales` (
  `codigo` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) DEFAULT NULL,
  `abreviatura` varchar(20) DEFAULT NULL,
  `cod_estado` int(11) DEFAULT NULL,
  `centro_costos` int(11) DEFAULT NULL,
  PRIMARY KEY (`codigo`)
) ENGINE=InnoDB AUTO_INCREMENT=1626 DEFAULT CHARSET=latin1;

#
# Dumping data for table unidades_organizacionales
#

LOCK TABLES `unidades_organizacionales` WRITE;
/*!40000 ALTER TABLE `unidades_organizacionales` DISABLE KEYS */;
INSERT INTO `unidades_organizacionales` VALUES (1,'REGIONAL NACIONAL','',1,0);
INSERT INTO `unidades_organizacionales` VALUES (4,'REGIONAL(RSCRTJ)','R3',1,0);
INSERT INTO `unidades_organizacionales` VALUES (5,'OF LA PAZ','RLP',1,1);
INSERT INTO `unidades_organizacionales` VALUES (8,'OF ORURO','ROR',1,1);
INSERT INTO `unidades_organizacionales` VALUES (9,'OF COCHABAMBA','RCB',1,1);
INSERT INTO `unidades_organizacionales` VALUES (10,'OF SANTA CRUZ','RSC',1,1);
INSERT INTO `unidades_organizacionales` VALUES (270,'OF SUCRE','RCH',1,1);
INSERT INTO `unidades_organizacionales` VALUES (271,'OF TARIJA','RTJ',1,1);
INSERT INTO `unidades_organizacionales` VALUES (272,'OF POTOSI','RPT',1,1);
INSERT INTO `unidades_organizacionales` VALUES (829,'OF DN LA PAZ','DN',1,1);
INSERT INTO `unidades_organizacionales` VALUES (1103,'OF VIRTUAL','OV',1,0);
INSERT INTO `unidades_organizacionales` VALUES (1624,'REGIONAL (RLPROR)','R1',1,0);
INSERT INTO `unidades_organizacionales` VALUES (1625,'REGIONAL (RCBRCHRPT)','R2',1,0);
/*!40000 ALTER TABLE `unidades_organizacionales` ENABLE KEYS */;
UNLOCK TABLES;

#
# Source for table v
#

DROP TABLE IF EXISTS `v`;
CREATE TABLE `v` (
  `codigo` int(11) NOT NULL AUTO_INCREMENT,
  `cod_depreciaciones` int(11) DEFAULT NULL,
  `tipo_bien` varchar(255) DEFAULT '',
  PRIMARY KEY (`codigo`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

#
# Dumping data for table v
#

LOCK TABLES `v` WRITE;
/*!40000 ALTER TABLE `v` DISABLE KEYS */;
/*!40000 ALTER TABLE `v` ENABLE KEYS */;
UNLOCK TABLES;

#
# Source for table v_af_cuentacontablepararubros
#

DROP TABLE IF EXISTS `v_af_cuentacontablepararubros`;
CREATE TABLE `v_af_cuentacontablepararubros` (
  `codigo` int(11) NOT NULL AUTO_INCREMENT,
  `codigocuenta` varchar(100) DEFAULT NULL,
  `cuentacontable` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`codigo`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=latin1;

#
# Dumping data for table v_af_cuentacontablepararubros
#

LOCK TABLES `v_af_cuentacontablepararubros` WRITE;
/*!40000 ALTER TABLE `v_af_cuentacontablepararubros` DISABLE KEYS */;
INSERT INTO `v_af_cuentacontablepararubros` VALUES (1,'123','ACTIVO FIJO');
INSERT INTO `v_af_cuentacontablepararubros` VALUES (2,'1231','ACTIVO FIJO EN OPERACION');
INSERT INTO `v_af_cuentacontablepararubros` VALUES (3,'12311','EDIFICIOS');
INSERT INTO `v_af_cuentacontablepararubros` VALUES (4,'12312','EQUIPO DE OFICINA Y MUEBLES');
INSERT INTO `v_af_cuentacontablepararubros` VALUES (5,'12313','MAQUINARIA Y EQUIPO DE PRODUCCION');
INSERT INTO `v_af_cuentacontablepararubros` VALUES (6,'12314','EQUIPO DE TRANSPORTE, TRACCION Y ELEVACION');
INSERT INTO `v_af_cuentacontablepararubros` VALUES (7,'12315','EQUIPO MEDICO Y LABORATORIO');
INSERT INTO `v_af_cuentacontablepararubros` VALUES (8,'12316','EQUIPO DE COMUNICACIONES');
INSERT INTO `v_af_cuentacontablepararubros` VALUES (9,'12317','EQUIPO EDUCACIONAL Y RECREATIVO');
INSERT INTO `v_af_cuentacontablepararubros` VALUES (10,'12318','OTRA MAQUINARIA Y EQUIPO');
INSERT INTO `v_af_cuentacontablepararubros` VALUES (11,'1232','TIERRAS Y TERRENOS');
INSERT INTO `v_af_cuentacontablepararubros` VALUES (12,'1233','SEMOVIENTES');
INSERT INTO `v_af_cuentacontablepararubros` VALUES (13,'1234','OTROS ACTIVOS FIJOS');
INSERT INTO `v_af_cuentacontablepararubros` VALUES (14,'1235','CONSTRUCCIONES EN PROCESO DE BIENES');
/*!40000 ALTER TABLE `v_af_cuentacontablepararubros` ENABLE KEYS */;
UNLOCK TABLES;

#
# Source for view v_activosfijos
#

DROP VIEW IF EXISTS `v_activosfijos`;
CREATE ALGORITHM=UNDEFINED SQL SECURITY DEFINER VIEW `v_activosfijos` AS select `activosfijos`.`codigo` AS `codigo`,`activosfijos`.`codigoactivo` AS `codigoactivo`,`activosfijos`.`tipoalta` AS `tipoalta`,`activosfijos`.`fechalta` AS `fechalta`,`activosfijos`.`indiceufv` AS `indiceufv`,`activosfijos`.`tipocambio` AS `tipocambio`,`activosfijos`.`moneda` AS `moneda`,`activosfijos`.`valorinicial` AS `valorinicial`,`activosfijos`.`depreciacionacumulada` AS `depreciacionacumulada`,`activosfijos`.`valorresidual` AS `valorresidual`,`activosfijos`.`cod_depreciaciones` AS `cod_depreciaciones`,`activosfijos`.`cod_tiposbienes` AS `cod_tiposbienes`,`activosfijos`.`vidautilmeses` AS `vidautilmeses`,`activosfijos`.`estadobien` AS `estadobien`,`activosfijos`.`otrodato` AS `otrodato`,`activosfijos`.`cod_ubicaciones` AS `cod_ubicaciones`,`activosfijos`.`cod_empresa` AS `cod_empresa`,`activosfijos`.`activo` AS `activo`,`activosfijos`.`cod_responsables_responsable` AS `cod_responsables_responsable`,`activosfijos`.`cod_responsables_autorizadopor` AS `cod_responsables_autorizadopor`,`activosfijos`.`created_at` AS `created_at`,`activosfijos`.`created_by` AS `created_by`,`activosfijos`.`modified_at` AS `modified_at`,`activosfijos`.`modified_by` AS `modified_by`,`activosfijos`.`vidautilmeses_restante` AS `vidautilmeses_restante`,`activosfijos`.`cod_af_proveedores` AS `cod_af_proveedores`,`activosfijos`.`numerofactura` AS `numerofactura`,`personal`.`nombre` AS `nombre_personal`,`depreciaciones`.`nombre` AS `nombre_depreciaciones`,`tiposbienes`.`tipo_bien` AS `tipo_bien`,`ubicaciones`.`edificio` AS `edificio`,`ubicaciones`.`oficina` AS `oficina`,`unidades_organizacionales`.`nombre` AS `nombre_uo` from (((((`activosfijos` join `personal2` `personal`) join `depreciaciones`) join `tiposbienes`) join `ubicaciones`) join `unidades_organizacionales`) where `activosfijos`.`cod_depreciaciones` = `depreciaciones`.`codigo` and `activosfijos`.`cod_tiposbienes` = `tiposbienes`.`codigo` and `activosfijos`.`cod_ubicaciones` = `ubicaciones`.`codigo` and `activosfijos`.`cod_responsables_responsable` = `personal`.`codigo` and `ubicaciones`.`cod_unidades_organizacionales` = `unidades_organizacionales`.`codigo`;

#
# Source for view v_activosfijos_asignaciones
#

DROP VIEW IF EXISTS `v_activosfijos_asignaciones`;
CREATE ALGORITHM=UNDEFINED SQL SECURITY DEFINER VIEW `v_activosfijos_asignaciones` AS select `activosfijos`.`codigo` AS `codigo`,`activosfijos`.`codigoactivo` AS `codigoactivo`,`activosfijos`.`tipoalta` AS `tipoalta`,`activosfijos`.`fechalta` AS `fechalta`,`activosfijos`.`indiceufv` AS `indiceufv`,`activosfijos`.`tipocambio` AS `tipocambio`,`activosfijos`.`moneda` AS `moneda`,`activosfijos`.`valorinicial` AS `valorinicial`,`activosfijos`.`depreciacionacumulada` AS `depreciacionacumulada`,`activosfijos`.`valorresidual` AS `valorresidual`,`activosfijos`.`cod_depreciaciones` AS `cod_depreciaciones`,`activosfijos`.`cod_tiposbienes` AS `cod_tiposbienes`,`activosfijos`.`vidautilmeses` AS `vidautilmeses`,`activosfijos`.`estadobien` AS `estadobien`,`activosfijos`.`otrodato` AS `otrodato`,`activosfijos`.`cod_ubicaciones` AS `cod_ubicaciones`,`activosfijos`.`cod_empresa` AS `cod_empresa`,`activosfijos`.`activo` AS `activo`,`activosfijos`.`cod_responsables_responsable` AS `cod_responsables_responsable`,`activosfijos`.`cod_responsables_autorizadopor` AS `cod_responsables_autorizadopor`,`activosfijos`.`created_at` AS `created_at`,`activosfijos`.`created_by` AS `created_by`,`activosfijos`.`modified_at` AS `modified_at`,`activosfijos`.`modified_by` AS `modified_by`,`activosfijos`.`vidautilmeses_restante` AS `vidautilmeses_restante`,`activosfijos`.`cod_af_proveedores` AS `cod_af_proveedores`,`activosfijos`.`numerofactura` AS `numerofactura`,`personal`.`nombre` AS `nombre_personal`,`depreciaciones`.`nombre` AS `nombre_depreciaciones`,`tiposbienes`.`tipo_bien` AS `tipo_bien`,`activofijos_asignaciones`.`codigo` AS `activofijosasignaciones_codigo`,`activofijos_asignaciones`.`fechaasignacion` AS `edificio`,`ubicaciones`.`oficina` AS `oficina`,`unidades_organizacionales`.`nombre` AS `nombre_uo` from ((((((`activosfijos` join `personal2` `personal`) join `depreciaciones`) join `tiposbienes`) join `ubicaciones`) join `unidades_organizacionales`) join `activofijos_asignaciones`) where `activosfijos`.`cod_depreciaciones` = `depreciaciones`.`codigo` and `activosfijos`.`cod_tiposbienes` = `tiposbienes`.`codigo` and `activofijos_asignaciones`.`cod_ubicaciones` = `ubicaciones`.`codigo` and `activofijos_asignaciones`.`cod_personal` = `personal`.`codigo` and `ubicaciones`.`cod_unidades_organizacionales` = `unidades_organizacionales`.`codigo` and `activofijos_asignaciones`.`cod_activosfijos` = `activosfijos`.`codigo`;

#
# Source for procedure crear_depreciacion_mensual
#

DROP PROCEDURE IF EXISTS `crear_depreciacion_mensual`;
CREATE DEFINER=`root`@`localhost` PROCEDURE `crear_depreciacion_mensual`(
	IN p_mes int, 
    IN p_gestion int,
    IN p_ufvinicio decimal(16,6),
    IN p_ufvfinal decimal(16,6)
)
begin
	declare numerofilascursor int default 0;
    declare contadoractualcursor int default 0;
	declare done int default 0;
	declare idmaestro INT;
    #declare factoractualizacion  decimal(16,6);
	#para fila de activo fijo
    declare t_af_codigo int default 0;    
	declare t_af_depreciacionacumulada decimal(12,2);
	declare t_af_valoresidual decimal(12,2);
    declare t_af_vidautilmeses int;
    declare t_af_vidautilmeses_restante int;
    declare t_af_valorinicial decimal(12,2);
    #para depreciacion anterio de un activo

    #para fila de depreciaciones detalle
    declare cod_mesdepreciaciones int;
    declare t_mdd_cod_activosfijos int;
    declare t_mdd_d2_valorresidual decimal(12,2);
    declare t_mdd_d3_factoractualizacion decimal(16,6); #ufvfinal/ufvinicial... primera vez es 1
    declare t_mdd_d4_valoractualizado decimal(12,2); #2*3
    declare t_mdd_d5_incrementoporcentual decimal(12,2); #4-2
    declare t_mdd_d6_depreciacionacumuladaanterior decimal(12,2); #9 fila anterior
    declare t_mdd_d7_incrementodepreciacionacumulada decimal(12,2); #col6*(col3-one)
    declare t_mdd_d8_depreciacionperiodo decimal(12,2);#4/primer(11) o en este caso obtengo el valor del bien
    declare t_mdd_d9_depreciacionacumuladaactual decimal(12,2); #6+7+8
    declare t_mdd_d10_valornetobs decimal(12,2);#4-9
    declare t_mdd_d11_vidarestante int;
    #para el nuevo calculo
    declare v_d2 decimal(12,2);
    declare v_d3 decimal(16,6);
    declare v_d4 decimal(12,2);
    declare v_d5 decimal(12,2);
    declare v_d6 decimal(12,2);
    declare v_d7 decimal(12,2);
    declare v_d8 decimal(12,2);
    declare v_d9 decimal(12,2);
    declare v_d10 decimal(12,2);
    declare v_d11 decimal(12,2);
    
    declare activos_cursor cursor for
		select codigo, valorinicial, depreciacionacumulada, valorresidual, vidautilmeses, vidautilmeses_restante from activosfijos;
    
    declare continue handler for not found set done = 1;
    
   
    #EMPIEZA
    #SET factoractualizacion = p_ufvfinal / p_ufvinicio;
	
    select count(codigo) into numerofilascursor from activosfijos;
     
    #si ya existe borrar porque esta reprocesando
    select codigo into idmaestro from mesdepreciaciones 
						where mes = p_mes and gestion = p_gestion;
    if idmaestro is not null then
		delete from mesdepreciaciones where codigo = idmaestro;
        #select * from mesdepreciaciones_detalle
        delete from mesdepreciaciones_detalle where cod_mesdepreciaciones = idmaestro;
    end if;
    #delete from mesdepreciaciones; delete from mesdepreciaciones_detalle
	#select 'LLEGA' as 'DEBUGG';
    #insertar maestro; select * from mesdepreciaciones; delete from mesdepreciaciones;
    insert into mesdepreciaciones (mes, gestion, ufvinicio, ufvfinal, estado)
    values (p_mes, p_gestion, p_ufvinicio, p_ufvfinal, 1);
    set idmaestro = last_insert_id();
    #insertar y calcular detalles				#select * from mesdepreciaciones
    #.........................................................................
    #por cada item en "activosfijos" haacer su depreciacion con sus meses obviamente
    set contadoractualcursor = 0;
	open activos_cursor;
		miforeach : loop
			fetch activos_cursor into t_af_codigo, t_af_valorinicial, 
				t_af_depreciacionacumulada, t_af_valoresidual, 
                                        t_af_vidautilmeses, t_af_vidautilmeses_restante;
            #.......eliminar el loop infinito
            if contadoractualcursor = numerofilascursor then
				leave miforeach;
            end if;
            set contadoractualcursor = contadoractualcursor + 1;
            #.......
            
            #su vida util, es superior a 0
			#select t_af_codigo as 'DEBUGG';
            if t_af_vidautilmeses > 0 then#A.
                if (t_af_vidautilmeses = t_af_vidautilmeses_restante) then
					select 'caso1' as 'DEBUGG';
					#C. si la depreciacion es igual a valor inicial... no hay q depreciar, es primera vez
                    #c1. insertar
                    select t_af_valorinicial+'>valinicial' as 'DEBUGG';
					INSERT into mesdepreciaciones_detalle (cod_mesdepreciaciones, cod_activosfijos, 
							d2_valorresidual, d3_factoractualizacion, d4_valoractualizado, 
                            d9_depreciacionacumuladaactual,d10_valornetobs, d11_vidarestante)
                    values (idmaestro, t_af_codigo, t_af_valorinicial, 1, t_af_valorinicial, 
                    0,t_af_valorinicial, t_af_vidautilmeses);
					#c2. actualizar valores en la depreciacion
                    #select contadoractualcursor as 'DEBUGG';
                    UPDATE activosfijos set vidautilmeses_restante = t_af_vidautilmeses_restante-1
										where codigo = t_af_codigo;#no toco deprecicacion acumulada
					commit;
                else 
					select 'caso2' as 'DEBUGG';
					#D. sino hay q sacar datos de la depreciacion anterior
					#d1. depreciacion anterior
					select d2_valorresidual, d3_factoractualizacion, d4_valoractualizado, d5_incrementoporcentual,
						d6_depreciacionacumuladaanterior, d7_incrementodepreciacionacumulada, d8_depreciacionperiodo,
                        d9_depreciacionacumuladaactual, d10_valornetobs, d11_vidarestante
					into t_mdd_d2_valorresidual, t_mdd_d3_factoractualizacion, t_mdd_d4_valoractualizado,
						t_mdd_d5_incrementoporcentual, t_mdd_d6_depreciacionacumuladaanterior, t_mdd_d7_incrementodepreciacionacumulada,
						t_mdd_d8_depreciacionperiodo, t_mdd_d9_depreciacionacumuladaactual, t_mdd_d10_valornetobs,
						t_mdd_d11_vidarestante
					from mesdepreciaciones_detalle where cod_activosfijos = t_af_codigo order by codigo desc limit 1;
                    
                    #d2. nueva depreciacion en base a la anterior
                    set v_d2 = t_mdd_d4_valoractualizado;
                    set v_d3 = p_ufvfinal / p_ufvinicio;
                    set v_d4 = t_mdd_d4_valoractualizado*v_d3;
                    set v_d5 = v_d4 - v_d2;#contable
                    #select v_d5 as 'DEBUGG';
                    set v_d6 = t_mdd_d9_depreciacionacumuladaactual;#el anterior
                    set v_d7 = v_d6 * (v_d3-1);#contable
                    set v_d8 = v_d4 / t_af_vidautilmeses;#contable
                    set v_d9 = v_d6 + v_d7 + v_d8;
                    set v_d10 = v_d4 - v_d9;
                    set v_d11 = t_mdd_d11_vidarestante - 1;
                    #select * from mesdepreciaciones_detalle; delete from mesdepreciaciones_detalle; commit;
                    insert into mesdepreciaciones_detalle (cod_mesdepreciaciones, cod_activosfijos, d2_valorresidual,
						d3_factoractualizacion, d4_valoractualizado, d5_incrementoporcentual, d6_depreciacionacumuladaanterior,
						d7_incrementodepreciacionacumulada, d8_depreciacionperiodo, d9_depreciacionacumuladaactual, 
						d10_valornetobs, d11_vidarestante) values 
						(idmaestro, t_af_codigo, v_d2, v_d3,
						v_d4, v_d5, v_d6, v_d7, v_d8, v_d9, v_d10, v_d11);
					#actualizar valores en activo fijo
                    #select 'llega' as 'DEBUGG';
                    UPDATE activosfijos SET vidautilmeses_restante = vidautilmeses_restante - 1,
											depreciacionacumulada = v_d8
                                            WHERE codigo = t_af_codigo;
					commit;
                end if;
            end if;
	end loop miforeach;
    close activos_cursor;
end;


#
# Source for procedure ordenar_componentes
#

DROP PROCEDURE IF EXISTS `ordenar_componentes`;
CREATE DEFINER=`root`@`localhost` PROCEDURE `ordenar_componentes`(usuario INT)
BLOCK1: begin
	DECLARE v_done BOOLEAN DEFAULT false;
	DECLARE indice INT;
	DECLARE codigo_componente INT;
	DECLARE abreviatura VARCHAR(255);
	DECLARE nombre VARCHAR(255);
	DECLARE cod_padre int;
	DECLARE nivel int;
	DECLARE partida varchar(255);

	DECLARE cursor1 CURSOR FOR
	SELECT c.codigo, c.abreviatura, c.nombre, c.cod_padre, c.nivel, c.partida
	FROM componentessis c where c.nivel=1;

	DECLARE CONTINUE HANDLER FOR NOT FOUND
		SET v_done:=true;
	
	DELETE FROM componentessis_orden;

	SET indice=0;
	open cursor1;
	LOOP1: loop
		SET indice=indice+1;
		FETCH cursor1 INTO codigo_componente, abreviatura, nombre, cod_padre, nivel, partida;
		if v_done then
			close cursor1;
			leave LOOP1;
		end if;
		
		INSERT INTO componentessis_orden (codigo, nombre, abreviatura, nivel, cod_padre, cod_estado, partida, indice, cod_usuario)
		values (codigo_componente, nombre, abreviatura, nivel, cod_padre, 1, partida, indice, usuario);

		BLOCK2: begin
			DECLARE codigo_componente2 INT;
			DECLARE abreviatura2 VARCHAR(255);
			DECLARE nombre2 VARCHAR(255);
			DECLARE cod_padre2 int;
			DECLARE nivel2 int;
			DECLARE partida2 varchar(255);

			declare v_done2 BOOLEAN DEFAULT false;
			DECLARE cursor2 CURSOR FOR
				SELECT c2.codigo, c2.abreviatura, c2.nombre, c2.cod_padre, c2.nivel, c2.partida
				FROM componentessis c2 where c2.cod_padre=codigo_componente;

				DECLARE CONTINUE HANDLER FOR NOT FOUND
				set v_done2:= TRUE;
				open cursor2;
				LOOP2: loop
					SET indice=indice+1;
					FETCH cursor2 INTO codigo_componente2, abreviatura2, nombre2, cod_padre2, nivel2, partida2;
					
					IF v_done2 THEN
						close cursor2;
						leave LOOP2;
					end if;

					INSERT INTO componentessis_orden (codigo, nombre, abreviatura, nivel, cod_padre, cod_estado, partida, indice, cod_usuario)
					values (codigo_componente2, nombre2, abreviatura2, nivel2, cod_padre2, 1, partida2, indice, usuario);

					BLOCK3: begin
						DECLARE codigo_componente3 INT;
						DECLARE abreviatura3 VARCHAR(255);
						DECLARE nombre3 VARCHAR(255);
						DECLARE cod_padre3 int;
						DECLARE nivel3 int;
						DECLARE partida3 varchar(255);

						declare v_done3 BOOLEAN DEFAULT false;
						DECLARE cursor3 CURSOR FOR
							SELECT c3.codigo, c3.abreviatura, c3.nombre, c3.cod_padre, c3.nivel, c3.partida
							FROM componentessis c3 where c3.cod_padre=codigo_componente2;

							DECLARE CONTINUE HANDLER FOR NOT FOUND
							set v_done3:= TRUE;
							open cursor3;
							LOOP3: loop
								SET indice=indice+1;
								FETCH cursor3 INTO codigo_componente3, abreviatura3, nombre3, cod_padre3, nivel3, partida3;
								
								IF v_done3 THEN
									close cursor3;
									leave LOOP3;
								end if;

								INSERT INTO componentessis_orden (codigo, nombre, abreviatura, nivel, cod_padre, cod_estado, partida, indice, cod_usuario)
								values (codigo_componente3, nombre3, abreviatura3, nivel3, cod_padre3, 1, partida3, indice, usuario);

							end loop LOOP3;
					end BLOCK3;

				end loop LOOP2;
		end BLOCK2;
	end loop LOOP1;
end BLOCK1;


#
# Source for procedure ordenar_componentes2
#

DROP PROCEDURE IF EXISTS `ordenar_componentes2`;
CREATE DEFINER=`root`@`localhost` PROCEDURE `ordenar_componentes2`()
BEGIN
-- Declaración de variables
DECLARE v_done BOOLEAN DEFAULT false;
DECLARE indice INT;
DECLARE codigo_componente INT;
DECLARE abreviatura VARCHAR(255);
DECLARE nombre VARCHAR(255);
DECLARE cod_padre int;
DECLARE nivel int;
DECLARE partida varchar(255);

DECLARE codigo_componente2 INT;
DECLARE abreviatura2 VARCHAR(255);
DECLARE nombre2 VARCHAR(255);
DECLARE cod_padre2 int;
DECLARE nivel2 int;
DECLARE partida2 varchar(255);

DECLARE cursor1 CURSOR FOR
SELECT c.codigo, c.abreviatura, c.nombre, c.cod_padre, c.nivel, c.partida
FROM componentessis c where c.nivel=1;

DECLARE cursor2 CURSOR FOR
SELECT c2.codigo, c2.abreviatura, c2.nombre, c2.cod_padre, c2.nivel, c2.partida
FROM componentessis c2 where c2.cod_padre=codigo_componente;

DECLARE CONTINUE HANDLER FOR NOT FOUND
	SET v_done:=true;

/*BORRAR LA TABLA*/
DELETE FROM componentessis_orden;

OPEN cursor1;
set indice=1;
readloop: LOOP

	FETCH cursor1 INTO codigo_componente, abreviatura, nombre, cod_padre, nivel, partida;
	
		IF v_done THEN
			CLOSE cursor1;
			LEAVE readloop;
		END IF;

	INSERT INTO componentessis_orden (codigo, nombre, abreviatura, nivel, cod_padre, cod_estado, partida, indice, cod_usuario)
	values (codigo_componente, nombre, abreviatura, nivel, cod_padre, 1, partida, indice, 1);

	OPEN cursor2;
	readloop2: LOOP

	FETCH cursor2 INTO codigo_componente2, abreviatura2, nombre2, cod_padre2, nivel2, partida2;
		IF v_done THEN
			CLOSE cursor2;
			LEAVE readloop2;
		END IF;
	
	set indice=indice+1;

	INSERT INTO componentessis_orden (codigo, nombre, abreviatura, nivel, cod_padre, cod_estado, partida, indice, cod_usuario)
	values (codigo_componente2, nombre2, abreviatura2, nivel2, cod_padre2, 1, partida2, indice, 1);

	END LOOP readloop2;

	/*CLOSE cursor2;*/

	set indice=indice+1;
END LOOP readloop;
/*CLOSE cursor1;*/

END;


#
# Source for procedure ordenar_componentes3
#

DROP PROCEDURE IF EXISTS `ordenar_componentes3`;
CREATE DEFINER=`root`@`localhost` PROCEDURE `ordenar_componentes3`()
BLOCK1: begin
	DECLARE v_done BOOLEAN DEFAULT false;
	DECLARE indice INT;
	DECLARE codigo_componente INT;
	DECLARE abreviatura VARCHAR(255);
	DECLARE nombre VARCHAR(255);
	DECLARE cod_padre int;
	DECLARE nivel int;
	DECLARE partida varchar(255);

	DECLARE cursor1 CURSOR FOR
	SELECT c.codigo, c.abreviatura, c.nombre, c.cod_padre, c.nivel, c.partida
	FROM componentessis c where c.nivel=1;

	DECLARE CONTINUE HANDLER FOR NOT FOUND
		SET v_done:=true;
	
	DELETE FROM componentessis_orden;

	open cursor1;
	LOOP1: loop
		FETCH cursor1 INTO codigo_componente, abreviatura, nombre, cod_padre, nivel, partida;
		if v_done then
			close cursor1;
			leave LOOP1;
		end if;
		
		INSERT INTO componentessis_orden (codigo, nombre, abreviatura, nivel, cod_padre, cod_estado, partida, indice, cod_usuario)
		values (codigo_componente, nombre, abreviatura, nivel, cod_padre, 1, partida, indice, 1);

		BLOCK2: begin
			DECLARE codigo_componente2 INT;
			DECLARE abreviatura2 VARCHAR(255);
			DECLARE nombre2 VARCHAR(255);
			DECLARE cod_padre2 int;
			DECLARE nivel2 int;
			DECLARE partida2 varchar(255);

			declare v_done2 BOOLEAN DEFAULT false;
			DECLARE cursor2 CURSOR FOR
				SELECT c2.codigo, c2.abreviatura, c2.nombre, c2.cod_padre, c2.nivel, c2.partida
				FROM componentessis c2 where c2.cod_padre=codigo_componente;

				DECLARE CONTINUE HANDLER FOR NOT FOUND
				set v_done2:= TRUE;
				open cursor2;
				LOOP2: loop
					FETCH cursor2 INTO codigo_componente2, abreviatura2, nombre2, cod_padre2, nivel2, partida2;
					
					IF v_done2 THEN
						close cursor2;
						leave LOOP2;
					end if;

					INSERT INTO componentessis_orden (codigo, nombre, abreviatura, nivel, cod_padre, cod_estado, partida, indice, cod_usuario)
					values (codigo_componente2, nombre2, abreviatura2, nivel2, cod_padre2, 1, partida2, indice2, 1);


				end loop LOOP2;
		end BLOCK2;
	end loop LOOP1;
end BLOCK1;


#
# Source for procedure ordenar_componentesfake
#

DROP PROCEDURE IF EXISTS `ordenar_componentesfake`;
CREATE DEFINER=`root`@`localhost` PROCEDURE `ordenar_componentesfake`()
BEGIN
-- Declaración de variables
DECLARE codigo_componente INT;
DECLARE abreviatura VARCHAR(255);
DECLARE nombre VARCHAR(255);
DECLARE cod_padre int;
DECLARE nivel int;

DECLARE codigo_componente2 INT;
DECLARE abreviatura2 VARCHAR(255);
DECLARE nombre2 VARCHAR(255);
DECLARE cod_padre2 int;
DECLARE nivel2 int;

DECLARE cursor1 CURSOR FOR
SELECT c.codigo, c.abreviatura, c.nombre, c.cod_padre, c.nivel
FROM componentessis c where c.nivel=1;

OPEN cursor1;

readloop: LOOP
	FETCH cursor1 INTO codigo_componente, abreviatura, nombre, cod_padre, nivel;

	select codigo_componente, abreviatura, nombre, cod_padre, nivel;
	
END LOOP;
CLOSE cursor1;

END;


/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
