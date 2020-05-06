<?php
require_once '../layouts/bodylogin.php';
require_once '../conexion.php';
require_once '../functions.php';
require_once '../functionsGeneral.php';
require_once 'configModule.php';

$dbh = new Conexion();

//RECIBIMOS LAS VARIABLES
$codigo=$_POST['codigo'];
$cuenta=$_POST['cuenta'];
// Prepare
$stmt = $dbh->prepare("UPDATE solicitud_recursoscuentas set cod_cuentapasivo='$cuenta' where cod_cuenta=:codigo");
// Bind
$stmt->bindParam(':codigo', $codigo);
$flagSuccess=$stmt->execute();
showAlertSuccessError($flagSuccess,"../".$urlListCC2);	

?>