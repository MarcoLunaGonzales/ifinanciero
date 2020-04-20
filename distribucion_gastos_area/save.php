<?php

require_once '../layouts/bodylogin.php';
require_once '../conexion.php';
require_once '../functionsGeneral.php';
require_once 'configModule.php';

$dbh = new Conexion();

$nombre=$_POST["nombre"];
$anios_inicio=$_POST["anios_inicio"];
$anios_fin=$_POST["anios_final"];
$porcentaje=$_POST["porcentaje"];
$codEstado="1";

// Prepare
$stmt = $dbh->prepare("INSERT INTO $table_escalaAntiguedad (nombre, anios_inicio,anios_final,porcentaje,cod_estadoreferencial) VALUES (:nombre,:anios_inicio,:anios_final, :porcentaje, :cod_estado)");
// Bind
$stmt->bindParam(':nombre', $nombre);
$stmt->bindParam(':anios_inicio', $anios_inicio);
$stmt->bindParam(':anios_final', $anios_fin);
$stmt->bindParam(':porcentaje', $porcentaje);
$stmt->bindParam(':cod_estado', $codEstado);

$flagSuccess=$stmt->execute();
showAlertSuccessError($flagSuccess,$urlList2);

?>
