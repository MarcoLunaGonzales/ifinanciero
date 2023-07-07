<?php

require_once '../layouts/bodylogin.php';
require_once '../conexion.php';
require_once '../functionsGeneral.php';
require_once 'configModule.php';

$dbh = new Conexion();

$codigo = $_POST["codigo"];
$nombre = $_POST["nombre"];

/**
 * Actualizar de Configuración de Cargos Autoridades
 */
$sql  = "UPDATE aprobacion_configuraciones_cargos SET nombre = :nombre WHERE codigo = :codigo";
$stmt = $dbh->prepare($sql);
$stmt->bindParam(':nombre', $nombre);
$stmt->bindParam(':codigo', $codigo);
$flagSuccess = $stmt->execute();

showAlertSuccessError($flagSuccess,$urlList2);
?>