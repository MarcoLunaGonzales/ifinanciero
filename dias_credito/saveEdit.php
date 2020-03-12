<?php
require_once '../layouts/bodylogin.php';
require_once '../conexion.php';
require_once '../functions.php';
require_once '../functionsGeneral.php';
require_once 'configModule.php';

$dbh = new Conexion();

//RECIBIMOS LAS VARIABLES
$codigo=$_POST['codigo'];
$dias=$_POST['dias'];
// Prepare
$stmt = $dbh->prepare("UPDATE $table set cantidad_dias='$dias' where codigo=:codigo");
// Bind
$stmt->bindParam(':codigo', $codigo);
$flagSuccess=$stmt->execute();
showAlertSuccessError($flagSuccess,"../".$urlList);	

?>