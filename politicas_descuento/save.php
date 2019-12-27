<?php

require_once '../layouts/bodylogin.php';
require_once '../conexion.php';
require_once '../functionsGeneral.php';
require_once 'configModule.php';

$dbh = new Conexion();

$nombre=$_POST["nombre"];
$minutosInicio=$_POST["minutos_inicio"];
$minutosFinal=$_POST["minutos_final"];
$porcentaje=$_POST["porcentaje_diahaber"];
$codEstado="1";

$stmt = $dbh->prepare("INSERT INTO $table_politicaDescuento (nombre, minutos_inicio,minutos_final,porcentaje_diahaber,cod_estadoreferencial) VALUES (:nombre,:minutos_inicio,:minutos_final, :porcentaje_diahaber,:cod_estado)");

$stmt->bindParam(':nombre', $nombre);
$stmt->bindParam(':minutos_inicio', $minutosInicio);
$stmt->bindParam(':minutos_final', $minutosFinal);
$stmt->bindParam(':porcentaje_diahaber', $porcentaje);
$stmt->bindParam(':cod_estado', $codEstado);

$flagSuccess=$stmt->execute();
showAlertSuccessError($flagSuccess,$urlList2);

?>
