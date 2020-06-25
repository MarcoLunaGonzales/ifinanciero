<?php
require_once '../layouts/bodylogin.php';
require_once '../conexion.php';
require_once '../functions.php';
require_once '../functionsGeneral.php';
require_once 'configModule.php';

$dbh = new Conexion();

//RECIBIMOS LAS VARIABLES
$codigo=$_POST['codigo'];
$cod_personal=$_POST['cod_personal'];
$correo=$_POST['correo_otro'];
// Prepare
$stmt = $dbh->prepare("UPDATE $table set correo_alternativo='$correo' where codigo=:codigo;
	                   UPDATE personal set email_empresa='$correo' where codigo=:cod_personal");
// Bind
$stmt->bindParam(':codigo', $codigo);
$stmt->bindParam(':cod_personal', $cod_personal);
$flagSuccess=$stmt->execute();
showAlertSuccessError($flagSuccess,"../".$urlList);	

?>