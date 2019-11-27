/*
Navicat MySQL Data Transfer

Source Server         : ibnoedu
Source Server Version : 50644
Source Host           : www.minkasoftware.com:3306
Source Database       : ibnoadm

Target Server Type    : MYSQL
Target Server Version : 50644
File Encoding         : 65001

Date: 2019-11-07 16:26:57
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `areas`
-- ----------------------------
DROP TABLE IF EXISTS `areas`;
CREATE TABLE `areas` (
  `codigo` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) DEFAULT NULL,
  `abreviatura` varchar(20) DEFAULT NULL,
  `cod_estado` int(11) DEFAULT NULL,
  `centro_costos` int(11) DEFAULT NULL,
  PRIMARY KEY (`codigo`)
) ENGINE=InnoDB AUTO_INCREMENT=1236 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of areas
-- ----------------------------
INSERT INTO `areas` VALUES ('11', 'ORGANISMOS DE INSPECCION', 'OI', '1', '1');
INSERT INTO `areas` VALUES ('12', 'NORMALIZACION', 'NO', '1', '1');
INSERT INTO `areas` VALUES ('13', 'SERVICIOS EXTERNOS Y CAPACITACION', 'SEC', '1', '1');
INSERT INTO `areas` VALUES ('14', 'POLITICAS Y RELACIONES INTERNACIONALES', 'PR', '1', '0');
INSERT INTO `areas` VALUES ('15', 'DIRECCION NACIONAL', 'DN', '1', '0');
INSERT INTO `areas` VALUES ('38', 'CERTIFICACION DE SISTEMAS', 'TCS', '1', '1');
INSERT INTO `areas` VALUES ('39', 'CERTIFICACION DE PRODUCTO', 'TCP', '1', '1');
INSERT INTO `areas` VALUES ('40', 'LABORATORIO', 'TLQ', '1', '1');
INSERT INTO `areas` VALUES ('78', 'DIRECCION REGIONAL', 'DR', '1', '0');
INSERT INTO `areas` VALUES ('137', 'NORMATECA', 'CD', '1', '0');
INSERT INTO `areas` VALUES ('273', 'DIRECCION NACIONAL DE ADMINISTRACION Y FINANZAS', 'DNAF', '1', '0');
INSERT INTO `areas` VALUES ('826', 'TECNOLOGIA DE INFORMACION', 'TI', '1', '0');
INSERT INTO `areas` VALUES ('846', 'DIRECCION ASESORIA GENERAL', 'DAS', '1', '0');
INSERT INTO `areas` VALUES ('847', 'DIRECCION EJECUTIVA', 'DE', '1', '0');
INSERT INTO `areas` VALUES ('871', 'GESTION ESTRATEGICA', 'GES', '1', '0');
INSERT INTO `areas` VALUES ('872', 'DIRECCION NACIONAL DE SERVICIOS', 'DNS', '1', '0');
INSERT INTO `areas` VALUES ('873', 'CERTIFICACION', 'CER', '1', '0');
INSERT INTO `areas` VALUES ('874', 'SERVICIOS ADMINISTRATIVOS', 'SA', '1', '0');
INSERT INTO `areas` VALUES ('1200', 'DNS - GESTION INTEGRADA', 'GI', '1', '0');
INSERT INTO `areas` VALUES ('1235', 'PROYECTO SIS', 'SIS', '1', '0');
