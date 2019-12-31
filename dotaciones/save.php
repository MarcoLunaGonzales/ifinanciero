<?php

require_once '../layouts/bodylogin.php';
require_once '../conexion.php';
require_once '../functionsGeneral.php';
require_once 'configModule.php';

$dbh = new Conexion();

$nombre=$_POST["nombre"];
$abreviatura=$_POST["abreviatura"];
$descripcion=$_POST["descripcion"];
$nroMeses=$_POST["nro_meses"];
$fechaInicio=$_POST["fecha_inicio"];
$fechaFin=$_POST["fecha_fin"];
$codEstado="1";

// Prepare
$stmt = $dbh->prepare("INSERT INTO $table_dotaciones (nombre, abreviatura,descripcion,nro_meses,fecha_inicio,fecha_fin,cod_estadoreferencial) VALUES (:nombre,:abreviatura,:descripcion, :nro_meses, :fecha_inicio, :fecha_fin, :cod_estado)");
// Bind
$stmt->bindParam(':nombre', $nombre);
$stmt->bindParam(':abreviatura', $abreviatura);
$stmt->bindParam(':descripcion', $descripcion);
$stmt->bindParam(':nro_meses', $nroMeses);
$stmt->bindParam(':fecha_inicio', $fechaInicio);
$stmt->bindParam(':fecha_fin', $fechaFin);
$stmt->bindParam(':cod_estado', $codEstado);

$flagSuccess=$stmt->execute();
showAlertSuccessError($flagSuccess,$urlList2);

?>
