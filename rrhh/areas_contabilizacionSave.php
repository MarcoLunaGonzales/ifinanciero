<?php

require_once 'layouts/bodylogin.php';
require_once 'conexion.php';
require_once 'functionsGeneral.php';
require_once 'configModule.php';

$dbh = new Conexion();

$codigo=$_POST["codigo"];
$nombre=$_POST["nombre"];
$abreviatura=$_POST["abreviatura"];
$contabilizacion_vista=$_POST["contabilizacion_vista"];
$cod_estado=1;
$created_by=1;
$modified_by=1;

if($codigo>0){
	// Prepare
	$stmt = $dbh->prepare("UPDATE areas_contabilizacion set nombre=:nombre, abreviatura=:abreviatura ,contabilizacion_vista=:contabilizacion_vista where codigo=:codigo");
	// Bind
	$stmt->bindParam(':codigo', $codigo);
	$stmt->bindParam(':nombre', $nombre);
	$stmt->bindParam(':abreviatura', $abreviatura);
	$stmt->bindParam(':contabilizacion_vista', $contabilizacion_vista);

	$flagSuccess=$stmt->execute();	
}else{
	$stmt = $dbh->prepare("INSERT INTO areas_contabilizacion(nombre, abreviatura,cod_estado_referencial,created_by,modified_by,contabilizacion_vista) VALUES(:nombre,:abreviatura,:cod_estado_referencial,:created_by,:modified_by,:contabilizacion_vista)");
	// Bind	
	$stmt->bindParam(':nombre', $nombre);
	$stmt->bindParam(':abreviatura', $abreviatura);
	$stmt->bindParam(':cod_estado_referencial', $cod_estado);
	$stmt->bindParam(':created_by', $created_by);
	$stmt->bindParam(':modified_by', $modified_by);
	$stmt->bindParam(':contabilizacion_vista', $contabilizacion_vista);
	$flagSuccess=$stmt->execute();
}

showAlertSuccessError($flagSuccess,$urlListAreas_contabilizacion);

?>
