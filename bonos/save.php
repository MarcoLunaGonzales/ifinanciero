<?php

require_once '../layouts/bodylogin.php';
require_once '../conexion.php';
require_once '../functionsGeneral.php';
require_once 'configModule.php';

$dbh = new Conexion();

$nombre=$_POST["nombre"];
$abreviatura=$_POST["abreviatura"];
$observaciones=$_POST["observaciones"];
$tipo=$_POST["tipo"];
$codEstado="1";

// Prepare
$stmt = $dbh->prepare("INSERT INTO $table (nombre, abreviatura,observaciones,cod_tipocalculobono, cod_estadoreferencial) VALUES (:nombre, :abreviatura,:observaciones ,:cod_tipocalculobono, :cod_estado)");
// Bind
$stmt->bindParam(':nombre', $nombre);
$stmt->bindParam(':abreviatura', $abreviatura);
$stmt->bindParam(':observaciones', $observaciones);
$stmt->bindParam(':cod_tipocalculobono', $tipo);
$stmt->bindParam(':cod_estado', $codEstado);

$flagSuccess=$stmt->execute();
showAlertSuccessError($flagSuccess,$urlList2);

?>
