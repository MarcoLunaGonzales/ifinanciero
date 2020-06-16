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

$codEstadoReferencial="1";

// Prepare
$stmt = $dbh->prepare("INSERT INTO $table (nombre,cod_banco,nro_cuenta,fecha_registro,cod_estadoreferencial) 
	VALUES ( :nombre,:banco,:cuenta,:fecha,:estado)");
// Bind
$stmt->bindParam(':nombre', $nombre_libreta);
$stmt->bindParam(':fecha', $fechaActual);
$stmt->bindParam(':cuenta', $nro_cuenta);
$stmt->bindParam(':banco', $banco_libreta);
$stmt->bindParam(':estado', $codEstadoReferencial);
$flagSuccess=$stmt->execute();
if($flagSuccess==true){
	showAlertSuccessError(true,"../".$urlList);	
}else{
	showAlertSuccessError(false,"../".$urlList);
}
?>