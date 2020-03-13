<?php
require_once '../layouts/bodylogin.php';
require_once '../conexion.php';
require_once '../functions.php';
require_once '../functionsGeneral.php';
require_once 'configModule.php';

$dbh = new Conexion();

//RECIBIMOS LAS VARIABLES
$codigo=$_POST['codigo'];
$inicio=$_POST['inicio'];
$final=$_POST['final'];
$cheque=$_POST['cheque'];
$serie=$_POST['serie'];
// Prepare
$stmt = $dbh->prepare("UPDATE $table set nro_inicio='$inicio',nro_final='$final',nro_cheque='$cheque',nro_serie='$serie' where codigo=:codigo");
// Bind
$stmt->bindParam(':codigo', $codigo);
$flagSuccess=$stmt->execute();
showAlertSuccessError($flagSuccess,"../".$urlList);	

?>