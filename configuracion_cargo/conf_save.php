<?php

require_once '../layouts/bodylogin.php';
require_once '../conexion.php';
require_once '../functionsGeneral.php';
require_once 'configModule.php';
date_default_timezone_set('America/La_Paz');

$dbh = new Conexion();

$nombre = $_POST["nombre"];

/**
 * Registro de Configuración de Cargos Autoridades
 * NOTA: Se considera el primer "registro" estado 1
 */
$date = date('Y-m-d H:i:s');
$sql  = "INSERT INTO aprobacion_configuraciones_cargos (nombre, fecha_registro, cod_estadoaprobacion) VALUES (:nombre, '$date', 1)";
$stmt = $dbh->prepare($sql);
$stmt->bindParam(':nombre', $nombre);
$flagSuccess = $stmt->execute();

showAlertSuccessError($flagSuccess,$urlList2);

?>
