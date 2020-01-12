<?php

require_once '../layouts/bodylogin.php';
require_once '../conexion.php';
require_once '../functionsGeneral.php';
require_once 'configModule.php';

$dbh = new Conexion();

$dias=$_POST["dias"];
$monto=$_POST["monto"];
$codigo=$_POST["codigo"];
$mes=$_POST["cod_mes"];
$ref=$_POST["cod_ref"];
// Prepare
$stmt = $dbh->prepare("UPDATE refrigerios_detalle SET dias_asistidos=$dias, monto=$monto WHERE codigo=$codigo");

$flagSuccess=$stmt->execute();
showAlertSuccessError($flagSuccess,"../".$urlDetalle."&cod_ref=".$ref."&cod_mes=".$mes);

?>