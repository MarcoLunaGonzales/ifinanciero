<?php

require_once '../layouts/bodylogin.php';
require_once '../conexion.php';
require_once '../functionsGeneral.php';
require_once 'configModule.php';

$dbh = new Conexion();

$codigo=$_POST["codigo"];
$nombre=$_POST["nombre"];
$abreviatura=$_POST["abreviatura"];
$observaciones=$_POST["observaciones"];
$codCuenta=$_POST["cuenta"];


// Prepare
$stmt = $dbh->prepare("UPDATE $table set nombre=:nombre, abreviatura=:abreviatura ,observaciones=:observaciones, cod_cuenta=:cod_cuenta where codigo=:codigo");
// Bind
$stmt->bindParam(':codigo', $codigo);
$stmt->bindParam(':nombre', $nombre);
$stmt->bindParam(':abreviatura', $abreviatura);
$stmt->bindParam(':observaciones', $observaciones);
$stmt->bindParam(':cod_cuenta', $codCuenta);

$flagSuccess=$stmt->execute();
showAlertSuccessError($flagSuccess,$urlList2);

?>
