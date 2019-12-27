<?php

require_once '../layouts/bodylogin.php';
require_once '../conexion.php';
require_once '../functionsGeneral.php';
require_once 'configModule.php';

$dbh = new Conexion();

$codDotacion=$_POST["codigo"];
$nombre=$_POST["nombre"];
$abreviatura=$_POST["abreviatura"];
$descripcion=$_POST["descripcion"];
$nroMeses=$_POST["nro_meses"];
$fechaInicio=$_POST["fecha_inicio"];
$fechaFin=$_POST["fecha_fin"];
$codEstado="1";

// Prepare
$stmt = $dbh->prepare("UPDATE $table_dotaciones set nombre=:nombre, abreviatura=:abreviatura, descripcion=:descripcion, nro_meses=:nro_meses, fecha_inicio=:fecha_inicio , fecha_fin=:fecha_fin
                     where codigo=:codigo AND cod_estadoreferencial=$codEstado");
// Bind
$stmt->bindParam(':codigo', $codDotacion);
$stmt->bindParam(':nombre', $nombre);
$stmt->bindParam(':abreviatura', $abreviatura);
$stmt->bindParam(':descripcion', $descripcion);
$stmt->bindParam(':nro_meses', $nroMeses);
$stmt->bindParam(':fecha_inicio', $fechaInicio);
$stmt->bindParam(':fecha_fin', $fechaFin);

$flagSuccess=$stmt->execute();
showAlertSuccessError($flagSuccess,$urlList2);

?>