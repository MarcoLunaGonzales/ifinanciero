<?php
require_once '../layouts/bodylogin.php';
require_once '../conexion.php';
require_once '../functions.php';
require_once '../functionsGeneral.php';
require_once 'configModule.php';

$dbh = new Conexion();

//RECIBIMOS LAS VARIABLES
$codigo=$_POST['codigo'];
$nombre=$_POST['nombre'];
$porcentaje=$_POST['porcentaje'];
// Prepare
$stmt = $dbh->prepare("UPDATE $table set nombre='$nombre',porcentaje_cuentaorigen='$porcentaje' where codigo=:codigo");
// Bind
$stmt->bindParam(':codigo', $codigo);
$flagSuccess=$stmt->execute();
showAlertSuccessError($flagSuccess,"../".$urlList2);	

?>