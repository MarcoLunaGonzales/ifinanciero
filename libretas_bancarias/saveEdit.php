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

$cod_cuenta=$_POST['cod_cuenta'];
$cod_contracuenta=$_POST['cod_contracuenta'];
// Prepare
$stmt = $dbh->prepare("UPDATE $table set nombre='$nombre',nro_cuenta='$nro_cuenta',cod_cuenta='$cod_cuenta',cod_contracuenta='$cod_contracuenta' where codigo=:codigo");
// Bind
$stmt->bindParam(':codigo', $codigo);
$flagSuccess=$stmt->execute();
showAlertSuccessError($flagSuccess,"../".$urlList);	

?>