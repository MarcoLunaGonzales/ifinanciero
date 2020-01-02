<?php

require_once 'layouts/bodylogin.php';
require_once 'conexion.php';
require_once 'functionsGeneral.php';
require_once 'configModule.php';

$dbh = new Conexion();

$cod_areacontabilizacion=$_POST["codigo"];
$cod_unidadorganizacional=$_POST["cod_uo"];
$cod_area=$_POST["cod_area"];
$stmt = $dbh->prepare("INSERT INTO areas_contabilizacion_detalle(cod_areacontabilizacion, cod_unidadorganizacional,cod_area) VALUES(:cod_areacontabilizacion,:cod_unidadorganizacional,:cod_area)");
	// Bind	
	$stmt->bindParam(':cod_areacontabilizacion', $cod_areacontabilizacion);
	$stmt->bindParam(':cod_unidadorganizacional', $cod_unidadorganizacional);
	$stmt->bindParam(':cod_area', $cod_area);

	$flagSuccess=$stmt->execute();

$list_areas_contabilizacion_Detalle_x="index.php?opcion=list_areas_contabilizacion_Detalle&codigo=$cod_areacontabilizacion";
showAlertSuccessError($flagSuccess,$list_areas_contabilizacion_Detalle_x);

?>
