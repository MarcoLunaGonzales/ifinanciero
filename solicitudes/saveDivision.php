<?php
require_once '../layouts/bodylogin.php';
require_once '../conexion.php';
require_once '../functions.php';
require_once '../functionsGeneral.php';
require_once 'configModule.php';

$dbh = new Conexion();

//RECIBIMOS LAS VARIABLES
$codigo=$_GET['cod'];
$habilitar=$_GET['habilitar'];
// Prepare
$stmt = $dbh->prepare("UPDATE solicitud_recursoscuentas set division_porcentaje='$habilitar' where cod_cuenta=:codigo");
// Bind
$stmt->bindParam(':codigo', $codigo);
$flagSuccess=$stmt->execute();
showAlertSuccessError($flagSuccess,"../".$urlListCC2);	

?>