<?php

require_once '../layouts/bodylogin.php';
require_once '../conexion.php';
require_once '../functionsGeneral.php';
require_once 'configModule.php';

$dbh = new Conexion();

$codigoAnticipoPersonal=$_POST["codigo"];
$monto=$_POST["monto"];
$codEstado="1";

// Prepare
$stmt = $dbh->prepare("UPDATE $table_anticiposPersonal set monto=:monto where codigo=:codigo AND cod_estadoreferencial=$codEstado");
// Bind
$stmt->bindParam(':codigo', $codigoAnticipoPersonal);
$stmt->bindParam(':monto', $monto);

$flagSuccess=$stmt->execute();
showAlertSuccessError($flagSuccess,$urlList2);

?>