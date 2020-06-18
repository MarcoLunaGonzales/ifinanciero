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
$nro_cuenta=$_POST['nro_cuenta'];
// Prepare
$stmt = $dbh->prepare("UPDATE $table set nombre='$nombre',nro_cuenta='$nro_cuenta' where codigo=:codigo");
// Bind
$stmt->bindParam(':codigo', $codigo);
$flagSuccess=$stmt->execute();
showAlertSuccessError($flagSuccess,"../".$urlList);	

?>