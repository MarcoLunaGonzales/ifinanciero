<?php

require_once 'layouts/bodylogin.php';
require_once 'conexion.php';
require_once 'functionsGeneral.php';
require_once 'configModule.php';

$dbh = new Conexion();

$cod_areacontabilizacion=$_POST["codigo"];
$cod_unidadorganizacional=$_POST["cod_uo"];
$cod_area=$_POST["cod_area"];
$cod_estadoreferencial=1;
$stmt = $dbh->prepare("INSERT INTO areas_contabilizacion_detalle(cod_areacontabilizacion, cod_unidadorganizacional,cod_area,cod_estadoreferencial) VALUES(:cod_areacontabilizacion,:cod_unidadorganizacional,:cod_area,:cod_estadoreferencial)");
	// Bind	
	$stmt->bindParam(':cod_areacontabilizacion', $cod_areacontabilizacion);
	$stmt->bindParam(':cod_unidadorganizacional', $cod_unidadorganizacional);
	$stmt->bindParam(':cod_area', $cod_area);
	$stmt->bindParam(':cod_estadoreferencial', $cod_estadoreferencial);

	$flagSuccess=$stmt->execute();

$list_areas_contabilizacion_Detalle_x="index.php?opcion=list_areas_contabilizacion_Detalle&codigo=$cod_areacontabilizacion";
showAlertSuccessError($flagSuccess,$list_areas_contabilizacion_Detalle_x);

?>
