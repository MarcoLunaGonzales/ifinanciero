<?php

require_once '../layouts/bodylogin.php';
require_once '../conexion.php';
require_once '../functionsGeneral.php';
require_once 'configModule.php';

$dbh = new Conexion();

$codigoP=$_POST["codigo"];
$monto=$_POST["monto"];
$codEstado="1";

// Prepare
$stmt = $dbh->prepare("UPDATE $table_personalfin set monto_subsidio=:monto where codigo=:codigo AND cod_estado_referencial=$codEstado");
// Bind
$stmt->bindParam(':codigo', $codigoP);
$stmt->bindParam(':monto', $monto);

$flagSuccess=$stmt->execute();
showAlertSuccessError($flagSuccess,"../".$urlList);
?>