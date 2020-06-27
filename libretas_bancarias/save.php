<?php

require_once '../layouts/bodylogin.php';
require_once '../conexion.php';
require_once '../functions.php';
require_once '../functionsGeneral.php';
require_once 'configModule.php';

$dbh = new Conexion();
$fechaActual=date("Y-m-d");
$nombre_libreta=$_POST["nombre_libreta"];
$banco_libreta=$_POST["banco_libreta"];
$nro_cuenta=$_POST["nro_cuenta"];
$cod_cuenta=$_POST["cod_cuenta"];
$cod_contracuenta=$_POST["cod_contracuenta"];

$codEstadoReferencial="1";

// Prepare
$stmt = $dbh->prepare("INSERT INTO $table (nombre,cod_banco,nro_cuenta,fecha_registro,cod_estadoreferencial,cod_cuenta,cod_contracuenta) 
	VALUES ( :nombre,:banco,:cuenta,:fecha,:estado,:cod_cuenta,:cod_contracuenta)");
// Bind
$stmt->bindParam(':nombre', $nombre_libreta);
$stmt->bindParam(':fecha', $fechaActual);
$stmt->bindParam(':cuenta', $nro_cuenta);
$stmt->bindParam(':banco', $banco_libreta);
$stmt->bindParam(':estado', $codEstadoReferencial);
$stmt->bindParam(':cod_cuenta', $cod_cuenta);
$stmt->bindParam(':cod_contracuenta', $cod_contracuenta);
$flagSuccess=$stmt->execute();
if($flagSuccess==true){
	showAlertSuccessError(true,"../".$urlList);	
}else{
	showAlertSuccessError(false,"../".$urlList);
}
?>