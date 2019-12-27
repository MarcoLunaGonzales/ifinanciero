<?php

require_once '../layouts/bodylogin.php';
require_once '../conexion.php';
require_once '../functionsGeneral.php';
require_once 'configModule.php';

$dbh = new Conexion();

$codigo=$_POST["codigo"];
$monto=$_POST["monto"];
$monto_iva=$_POST["monto_iva"];
$codEstado="1";

// Prepare
$stmt = $dbh->prepare("UPDATE $table_rcivaPersonal set monto=:monto, monto_iva=:monto_iva where codigo=:codigo AND cod_estadoreferencial=$codEstado");
// Bind
$stmt->bindParam(':codigo', $codigo);
$stmt->bindParam(':monto', $monto);
$stmt->bindParam(':monto_iva', $monto_iva);

$flagSuccess=$stmt->execute();
showAlertSuccessError($flagSuccess,$urlList2);

?>