/*
Navicat MySQL Data Transfer

Source Server         : LOCAL XAMPP
Source Server Version : 50505
Source Host           : localhost:3307
Source Database       : rrhhedu

Target Server Type    : MYSQL
Target Server Version : 50505
File Encoding         : 65001

Date: 2019-11-03 09:58:27
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `aguinaldos`
-- ----------------------------
DROP TABLE IF EXISTS `aguinaldos`;                         --transaccion
CREATE TABLE `aguinaldos` (
  `codigo` int(11) NOT NULL AUTO_INCREMENT,
  `cod_gestion` int(11) DEFAULT NULL,
  `cod_mes` int(11) DEFAULT NULL,
  `descripcion` varchar(255) DEFAULT NULL,
  `cod_estadoplanilla` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `created_by` int(11) DEFAULT NULL,
  `modified_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `modified_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`codigo`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of aguinaldos
-- ----------------------------

-- ----------------------------
-- Table structure for `aguinaldo_personal_gestion`
-- ----------------------------
DROP TABLE IF EXISTS `aguinaldo_personal_gestion`;              --transaccion detalle
CREATE TABLE `aguinaldo_personal_gestion` (
  `codigo` int(11) NOT NULL AUTO_INCREMENT,
  `cod_aguinaldo` int(11) DEFAULT NULL,
  `cod_personal` int(11) DEFAULT NULL,
  `dias_trabajados` int(11) DEFAULT NULL,
  `meses_trabajados` int(11) DEFAULT NULL,
  `sueldo1` decimal(14,2) DEFAULT NULL,
  `sueldo2` decimal(14,2) DEFAULT NULL,
  `sueldo3` decimal(14,2) DEFAULT NULL,
  `sueldopromedio` decimal(14,2) DEFAULT NULL,
  `aguinaldo` decimal(14,2) DEFAULT NULL,
  `cod_estadoreferencial` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `created_by` int(11) DEFAULT NULL,
  `modified_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `modified_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`codigo`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of aguinaldo_personal_gestion
-- ----------------------------

-- ----------------------------
-- Table structure for `aportes_laborales`
-- ----------------------------
DROP TABLE IF EXISTS `aportes_laborales`;                         --config
CREATE TABLE `aportes_laborales` (
  `codigo` int(11) NOT NULL AUTO_INCREMENT,
  `salario_minimo_nacional` double DEFAULT NULL,
  `cuenta_individual_vejez` double DEFAULT NULL,
  `seguro_invalidez` double DEFAULT NULL,
  `comision_afp` double DEFAULT NULL,
  `provivienda` double DEFAULT NULL,
  `iva` double DEFAULT NULL,
  `asa` double DEFAULT NULL,
  `aporte_nac_solidario_13` double DEFAULT NULL,
  `aporte_nac_solidario_25` double DEFAULT NULL,
  `aporte_nac_solidario_35` double DEFAULT NULL,
  `estado` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `created_by` int(11) DEFAULT NULL,
  `modified_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `modified_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`codigo`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of aportes_laborales
-- ----------------------------

-- ----------------------------
-- Table structure for `aportes_patronales`
-- ----------------------------
DROP TABLE IF EXISTS `aportes_patronales`;                         --config
CREATE TABLE `aportes_patronales` (
  `codigo` int(11) NOT NULL AUTO_INCREMENT,
  `seguro_riesgo_profesional` double DEFAULT NULL,
  `provivienda` double DEFAULT NULL,
  `infocal` double DEFAULT NULL,
  `cns` double DEFAULT NULL,
  `aporte_patronal_solidario` double DEFAULT NULL,
  `estado` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `created_by` int(11) DEFAULT NULL,
  `modified_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `modified_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`codigo`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of aportes_patronales
-- ----------------------------

-- ----------------------------
-- Table structure for `areas`
-- ----------------------------
DROP TABLE IF EXISTS `areas`;                                       --#hecho 
CREATE TABLE `areas` (
  `codigo` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) DEFAULT NULL,
  `abreviatura` varchar(255) DEFAULT NULL,
  `observaciones` varchar(1000) DEFAULT NULL,
  `cod_estadoreferencial` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `created_by` int(11) DEFAULT NULL,
  `modified_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `modified_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`codigo`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of areas
-- ----------------------------
INSERT INTO `areas` VALUES ('1', 'DIRECCION NACIONAL', 'DN', null, '1', '2019-11-03 09:48:28', null, '2019-11-03 09:53:53', null);
INSERT INTO `areas` VALUES ('2', 'CERTIFICACION SISTEMAS', 'TCP', null, '1', '2019-11-03 09:48:43', null, '2019-11-03 09:48:45', null);
INSERT INTO `areas` VALUES ('3', 'CERTIFICACION PRODUCTO', 'TCS', null, '1', '2019-11-03 09:49:26', null, '2019-11-03 09:49:26', null);
INSERT INTO `areas` VALUES ('4', 'CAPACITACION', 'SEC', null, '1', '2019-11-03 09:49:43', null, '2019-11-03 09:49:45', null);
INSERT INTO `areas` VALUES ('5', 'DIRECCION NACIONAL DE SERVICIOS', 'DNS', null, '1', '2019-11-03 09:49:57', null, '2019-11-03 09:49:59', null);
INSERT INTO `areas` VALUES ('6', 'DIRECCION NACIONAL DE ADM Y FINANZAS', 'DNAF', null, '1', '2019-11-03 09:50:11', null, '2019-11-03 09:50:11', null);
INSERT INTO `areas` VALUES ('7', 'GESTION ESTRATEGICA', 'GES', null, '1', '2019-11-03 09:50:58', null, '2019-11-03 09:50:58', null);
INSERT INTO `areas` VALUES ('8', 'ORGANISMO DE INSPECCION', 'OI', null, '1', '2019-11-03 09:53:36', null, '2019-11-03 09:53:48', null);

-- ----------------------------
-- Table structure for `areas_organizacion`
-- ----------------------------
DROP TABLE IF EXISTS `areas_organizacion`;                          --HECHO
CREATE TABLE `areas_organizacion` (
  `codigo` int(11) NOT NULL AUTO_INCREMENT,
  `cod_unidad` int(11) DEFAULT NULL,--unidades_organizacionales
  `cod_area` int(11) DEFAULT NULL, --areas
  `cod_areaorganizacion_padre` int(11) DEFAULT NULL, --misma
  `cod_estadoreferencial` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `created_by` int(11) DEFAULT NULL,
  `modified_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `modified_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`codigo`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of areas_organizacion
-- ----------------------------
INSERT INTO `areas_organizacion` VALUES ('1', '1', '1', '1', '1', '2019-11-03 09:48:28', null, '2019-11-03 09:54:08', null);
INSERT INTO `areas_organizacion` VALUES ('2', '1', '2', '1', '1', '2019-11-03 09:48:43', null, '2019-11-03 09:54:08', null);
INSERT INTO `areas_organizacion` VALUES ('3', '1', '3', '1', '1', '2019-11-03 09:49:26', null, '2019-11-03 09:54:08', null);
INSERT INTO `areas_organizacion` VALUES ('4', '1', '4', '1', '1', '2019-11-03 09:49:43', null, '2019-11-03 09:54:09', null);
INSERT INTO `areas_organizacion` VALUES ('5', '1', '5', '1', '1', '2019-11-03 09:49:57', null, '2019-11-03 09:54:09', null);
INSERT INTO `areas_organizacion` VALUES ('6', '1', '6', '1', '1', '2019-11-03 09:50:11', null, '2019-11-03 09:54:09', null);
INSERT INTO `areas_organizacion` VALUES ('7', '1', '7', '1', '1', '2019-11-03 09:50:58', null, '2019-11-03 09:54:10', null);
INSERT INTO `areas_organizacion` VALUES ('8', '1', '8', '1', '1', '2019-11-03 09:54:04', null, '2019-11-03 09:54:10', null);
INSERT INTO `areas_organizacion` VALUES ('9', '2', '3', '5', '1', '2019-11-03 09:54:36', null, '2019-11-03 09:54:57', null);
INSERT INTO `areas_organizacion` VALUES ('10', '2', '4', '5', '1', '2019-11-03 09:54:40', null, '2019-11-03 09:54:58', null);
INSERT INTO `areas_organizacion` VALUES ('11', '2', '8', '5', '1', '2019-11-03 09:54:44', null, '2019-11-03 09:54:58', null);

-- ----------------------------
-- Table structure for `bonos`
-- ----------------------------
DROP TABLE IF EXISTS `bonos`;                  --transaccion
CREATE TABLE `bonos` (
  `codigo` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) DEFAULT NULL,
  `abreviatura` varchar(255) DEFAULT NULL,
  `observaciones` varchar(1000) DEFAULT NULL,
  `cod_estadoreferencial` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `created_by` int(11) DEFAULT NULL,
  `modified_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `modified_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`codigo`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of bonos
-- ----------------------------

-- ----------------------------
-- Table structure for `bonos_personal_mes`
-- ----------------------------
DROP TABLE IF EXISTS `bonos_personal_mes`;            --transaccion detalle
CREATE TABLE `bonos_personal_mes` (
  `codigo` int(11) NOT NULL AUTO_INCREMENT,
  `cod_bono` int(11) DEFAULT NULL,
  `cod_personalcargo` int(11) DEFAULT NULL,
  `cod_gestion` int(11) DEFAULT NULL,
  `cod_mes` int(11) DEFAULT NULL,
  `monto` decimal(14,2) DEFAULT NULL,
  `cod_estadoreferencial` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `created_by` int(11) DEFAULT NULL,
  `modified_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `modified_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`codigo`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of bonos_personal_mes
-- ----------------------------

-- ----------------------------
-- Table structure for `cargos`                        hecho
-- ----------------------------
DROP TABLE IF EXISTS `cargos`;
CREATE TABLE `cargos` (
  `codigo` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) DEFAULT NULL,
  `abreviatura` varchar(50) DEFAULT NULL,
  `cod_estadoreferencial` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `created_by` int(11) DEFAULT NULL,
  `modified_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `modified_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`codigo`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of cargos
-- ----------------------------
INSERT INTO `cargos` VALUES ('1', 'DIRECTOR NACIONAL', 'DN', '1', '2019-11-03 09:57:32', null, '2019-11-03 09:57:32', null);
INSERT INTO `cargos` VALUES ('2', 'JEFE GESTION ESTRATEGICA', 'JGES', '1', '2019-11-03 09:57:45', null, '2019-11-03 09:57:45', null);
INSERT INTO `cargos` VALUES ('3', 'PROFESIONAL GESTION ESTRATEGICA', 'PGES', '1', '2019-11-03 09:57:55', null, '2019-11-03 09:58:04', null);
INSERT INTO `cargos` VALUES ('4', 'JEFE DNAF', 'JDNAF', '1', '2019-11-03 09:58:02', null, '2019-11-03 09:58:05', null);
INSERT INTO `cargos` VALUES ('5', 'DIRECTOR REGIGONAL', 'DR', '1', '2019-11-03 09:58:15', null, '2019-11-03 09:58:15', null);

-- ----------------------------
-- Table structure for `descuentos` -----transaccion
-- ----------------------------
DROP TABLE IF EXISTS `descuentos`;
CREATE TABLE `descuentos` (
  `codigo` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) DEFAULT NULL,
  `abreviatura` varchar(50) DEFAULT NULL,
  `observaciones` varchar(1000) DEFAULT NULL,
  `cod_estadoreferencial` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `created_by` int(11) DEFAULT NULL,
  `modified_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `modified_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`codigo`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of descuentos
-- ----------------------------

-- ----------------------------
-- Table structure for `descuento_personal_mes`  -----transaccion
-- ----------------------------
DROP TABLE IF EXISTS `descuento_personal_mes`;
CREATE TABLE `descuento_personal_mes` (
  `codigo` int(11) NOT NULL AUTO_INCREMENT,
  `codigo_descuento` int(11) DEFAULT NULL,
  `codigo_personalcargo` int(11) DEFAULT NULL,
  `gestionmes` int(11) DEFAULT NULL,
  `monto` decimal(14,2) DEFAULT NULL,
  `estado` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `created_by` varchar(500) DEFAULT NULL,
  `modified_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `modified_by` varchar(500) DEFAULT NULL,
  PRIMARY KEY (`codigo`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of descuento_personal_mes
-- ----------------------------

-- ----------------------------
-- Table structure for `dotaciones`         -----transaccion
-- ----------------------------
DROP TABLE IF EXISTS `dotaciones`;
CREATE TABLE `dotaciones` (
  `codigo` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) DEFAULT NULL,
  `abreviatura` varchar(255) DEFAULT NULL,
  `descripcion` varchar(1000) DEFAULT NULL,
  `nro_meses` int(11) DEFAULT NULL,
  `fecha_inicio` date DEFAULT NULL,
  `fecha_fin` date DEFAULT NULL,
  `cod_estadoreferencial` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `created_by` int(11) DEFAULT NULL,
  `modified_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `modified_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`codigo`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of dotaciones
-- ----------------------------

-- ----------------------------
-- Table structure for `dotaciones_personal_mes`              -----transaccion
-- ----------------------------
DROP TABLE IF EXISTS `dotaciones_personal_mes`;
CREATE TABLE `dotaciones_personal_mes` (
  `codigo` int(11) NOT NULL AUTO_INCREMENT,
  `cod_dotacion` int(11) DEFAULT NULL,
  `cod_personalcargo` int(11) DEFAULT NULL,
  `cod_gestion` int(11) DEFAULT NULL,
  `cod_mes` int(11) DEFAULT NULL,
  `monto` double(14,2) DEFAULT NULL,
  `cod_estadoreferencial` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `created_by` int(11) DEFAULT NULL,
  `modified_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `modified_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`codigo`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of dotaciones_personal_mes
-- ----------------------------

-- ----------------------------
-- Table structure for `estados_personal`
-- ----------------------------
DROP TABLE IF EXISTS `estados_personal`;                            --hecho
CREATE TABLE `estados_personal` (
  `codigo` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) DEFAULT NULL,
  `abreviatura` varchar(50) DEFAULT NULL,
  `cod_estadoreferencial` int(11) DEFAULT NULL,
  PRIMARY KEY (`codigo`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of estados_personal
-- ----------------------------
INSERT INTO `estados_personal` VALUES ('1', 'ACTIVO', 'ACT', '1');
INSERT INTO `estados_personal` VALUES ('2', 'RETIRADO', 'RET', '1');

-- ----------------------------
-- Table structure for `estados_planilla`
-- ----------------------------
DROP TABLE IF EXISTS `estados_planilla`;                                --hecho
CREATE TABLE `estados_planilla` (
  `codigo` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) DEFAULT NULL,
  `abreviatura` varchar(50) DEFAULT NULL,
  `cod_estadoreferencial` int(11) DEFAULT NULL,
  PRIMARY KEY (`codigo`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of estados_planilla
-- ----------------------------
INSERT INTO `estados_planilla` VALUES ('1', 'REGISTRADO', 'REG', '1');
INSERT INTO `estados_planilla` VALUES ('2', 'APROBADO', 'APRO', '1');
INSERT INTO `estados_planilla` VALUES ('3', 'CERRADO', 'CERRA', '1');

-- ----------------------------
-- Table structure for `personal`
-- ----------------------------
DROP TABLE IF EXISTS `personal`;               --haciendo
CREATE TABLE `personal` (
  `codigo` int(11) NOT NULL AUTO_INCREMENT,
  `ci` varchar(20) DEFAULT NULL,
  `ci_lugar_emision` varchar(2) DEFAULT NULL,
  `fecha_nacimiento` date DEFAULT NULL,
  `cod_cargo` int(11) DEFAULT NULL,
  `cod_unidadorganizacional` int(11) DEFAULT NULL,
  `cod_area` int(11) DEFAULT NULL,
  `jubilado` int(11) DEFAULT NULL,--1si, 0 no
  `cod_genero` int(11) DEFAULT NULL,
  `cod_tipopersonal` int(11) DEFAULT NULL,
  `haber_basico` double DEFAULT NULL,
  `paterno` varchar(100) DEFAULT NULL,
  `materno` varchar(100) DEFAULT NULL,
  `apellido_casada` varchar(100) DEFAULT NULL,
  `primer_nombre` varchar(100) DEFAULT NULL,
  `otros_nombres` varchar(100) DEFAULT NULL,
  `nua_cua_asignado` varchar(100) DEFAULT NULL,
  `direccion` varchar(1000) DEFAULT NULL,
  `cod_tipoafp` varchar(10) DEFAULT NULL, #prevision, bolivia
  `cod_tipoaporteafp` varchar(10) DEFAULT NULL,
  `nro_seguro` varchar(25) DEFAULT NULL,
  `cod_estadopersonal` int(11) DEFAULT NULL,
  `telefono` varchar(15) DEFAULT NULL,
  `celular` varchar(15) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL, 
  `persona_contacto` varchar(250) DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `created_by` int(11) DEFAULT NULL,
  `modified_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `modified_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`codigo`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of personal
-- ----------------------------

-- ----------------------------
-- Table structure for `personal_area_distribucion`
-- ----------------------------
--esto gestina la distribucion del salario
DROP TABLE IF EXISTS `personal_area_distribucion`; --detalle arriba
CREATE TABLE `personal_area_distribucion` (
  `codigo` int(11) NOT NULL AUTO_INCREMENT,
  `cod_personal` int(11) DEFAULT NULL,
  `cod_area` int(11) DEFAULT NULL,
  `porcentaje` double(5,3) DEFAULT NULL,
  `cod_estadoreferencial` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `created_by` int(11) DEFAULT NULL,
  `modified_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `modified_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`codigo`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of personal_area_distribucion
-- ----------------------------

-- ----------------------------
-- Table structure for `personal_cargos`
-- ----------------------------
--apenas se cambie el cargo,  el basico, o el area se inserta aca
DROP TABLE IF EXISTS `personal_cargos`;                     --detalle arriba
CREATE TABLE `personal_cargos` (
  `codigo` int(11) NOT NULL AUTO_INCREMENT,
  `cod_personal` int(11) DEFAULT NULL,
  `cod_area` int(11) DEFAULT NULL,
  `cod_cargo` int(11) DEFAULT NULL,
  `fecha_inicio` date DEFAULT NULL,
  `fecha_fin` date DEFAULT NULL,
  `haber_basico` double DEFAULT NULL,
  `cod_estadoreferencial` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `created_by` int(11) DEFAULT NULL,
  `modified_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `modified_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`codigo`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of personal_cargos
-- ----------------------------

-- ----------------------------
-- Table structure for `planillas`
-- ----------------------------
DROP TABLE IF EXISTS `planillas`;        --transaccion
CREATE TABLE `planillas` (
  `codigo` int(11) NOT NULL AUTO_INCREMENT,
  `cod_gestion` int(11) DEFAULT NULL,
  `cod_mes` int(11) DEFAULT NULL,
  `cod_estadoplanilla` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `created_by` int(11) DEFAULT NULL,
  `modified_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `modified_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`codigo`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of planillas
-- ----------------------------

-- ----------------------------
-- Table structure for `planillas_personal_mes`
-- ----------------------------
DROP TABLE IF EXISTS `planillas_personal_mes`;        --detalle transaccion
CREATE TABLE `planillas_personal_mes` (
  `codigo` int(11) NOT NULL AUTO_INCREMENT,
  `cod_planilla` int(11) DEFAULT NULL,
  `cod_personalcargo` int(11) DEFAULT NULL,
  `dias_trabajados` int(11) DEFAULT NULL,
  `horas_pagadas` int(11) DEFAULT NULL,
  `haber_basico` decimal(14,2) DEFAULT NULL,
  `bono_antiguedad` decimal(14,2) DEFAULT NULL,
  `horas_extra` decimal(14,2) DEFAULT NULL,
  `comisiones` decimal(14,2) DEFAULT NULL,
  ` monto_bonos` decimal(14,2) DEFAULT NULL,
  `total_ganado` decimal(14,2) DEFAULT NULL,
  `monto_descuentos` decimal(14,2) DEFAULT NULL,
  `otros_descuentos` decimal(14,2) DEFAULT NULL,
  `total_descuentos` decimal(14,2) DEFAULT NULL,
  `liquido_pagable` decimal(14,2) DEFAULT NULL,
  `cod_estadoreferencial` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `created_by` int(11) DEFAULT NULL,
  `modified_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `modified_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`codigo`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ---------------------------- ---------------------------- ---------------------------- ---------------------------- ---------------------------- ---------------------------- ----------------------------
-- Records of planillas_personal_mes
-- ---------------------------- ---------------------------- ---------------------------- ---------------------------- ---------------------------- ---------------------------- ----------------------------

-- ----------------------------
-- Table structure for `tipos_aporteafp`
-- ----------------------------
DROP TABLE IF EXISTS `tipos_aporteafp`;                     --hecho
CREATE TABLE `tipos_aporteafp` (
  `codigo` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) DEFAULT NULL,
  `abreviatura` int(11) DEFAULT NULL,
  `cod_estadoreferencial` int(11) DEFAULT NULL,
  `porcentaje_aporte` double DEFAULT NULL,
  `porcentaje_riesgoprofesional` double DEFAULT NULL,
  `porcentaje_provivienda` double DEFAULT NULL,
  PRIMARY KEY (`codigo`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


-- ----------------------------
-- Table structure for `tipos_afp` 
-- ----------------------------
DROP TABLE IF EXISTS `tipos_afp`;                           --hecho AQUI VA AFP PREVISION Y FUTURO;
CREATE TABLE `tipos_afp`(
  `codigo` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) DEFAULT NULL,
  `abreviatura` varchar(50) DEFAULT NULL,
  `cod_estadoreferencial` int(11) DEFAULT NULL,
  PRIMARY KEY (`codigo`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


-- ----------------------------
-- Records of tipos_aporteafp
-- ----------------------------

-- ----------------------------
-- Table structure for `tipos_genero`
-- ----------------------------
DROP TABLE IF EXISTS `tipos_genero`;                 --hecho      
CREATE TABLE `tipos_genero` (
  `codigo` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) DEFAULT NULL,
  `abreviatura` varchar(50) DEFAULT NULL,
  `cod_estadoreferencial` int(11) DEFAULT NULL,
  PRIMARY KEY (`codigo`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of tipos_genero
-- ----------------------------

-- ----------------------------
-- Table structure for `tipos_personal`                      hecho
-- ----------------------------
DROP TABLE IF EXISTS `tipos_personal`;
CREATE TABLE `tipos_personal` (
  `codigo` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) DEFAULT NULL,
  `abreviatura` varchar(50) DEFAULT NULL,
  `cod_estadoreferencial` int(11) DEFAULT NULL,
  PRIMARY KEY (`codigo`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of tipos_personal
-- ----------------------------

-- ----------------------------
-- Table structure for `unidades_organizacionales`                HECHOOOOOO
-- ----------------------------
DROP TABLE IF EXISTS `unidades_organizacionales`;
CREATE TABLE `unidades_organizacionales` (
  `codigo` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) DEFAULT NULL,
  `abreviatura` varchar(255) DEFAULT NULL,
  `observaciones` varchar(1000) DEFAULT NULL,
  `cod_estadoreferencial` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `created_by` int(11) DEFAULT NULL,
  `modified_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `modified_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`codigo`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of unidades_organizacionales
-- ----------------------------
INSERT INTO `unidades_organizacionales` VALUES ('1', 'DN - CENTRAL', 'DN', 'UNIDAD CENTRAL', '1', '2019-11-03 09:47:12', null, '2019-11-03 09:47:12', null);
INSERT INTO `unidades_organizacionales` VALUES ('2', 'OF. LPZ', 'LPZ', 'LA PAZ', '1', '2019-11-03 09:47:22', null, '2019-11-03 09:47:22', null);
INSERT INTO `unidades_organizacionales` VALUES ('3', 'OF SCZ', 'SCZ', 'SCZ', '1', '2019-11-03 09:47:31', null, '2019-11-03 09:47:31', null);
INSERT INTO `unidades_organizacionales` VALUES ('4', 'OF CBBA', 'CBBA', 'CBBA', '1', '2019-11-03 09:47:40', null, '2019-11-03 09:47:40', null);
