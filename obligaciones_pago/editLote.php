<?php
require_once '../layouts/bodylogin.php';
require_once '../conexion.php';
require_once '../functions.php';
require_once '../functionsGeneral.php';
require_once 'configModule.php';

$dbh = new Conexion();

$codigo=$_GET["cod"];
$estado=$_GET["estado"];
session_start();

$globalUser=$_SESSION["globalUser"];
$globalGestion=$_SESSION["globalGestion"];
$globalUnidad=$_SESSION["globalUnidad"];
$globalArea=$_SESSION["globalArea"];
$globalAdmin=$_SESSION["globalAdmin"];

$fechaHoraActual=date("Y-m-d H:i:s");

$sqlUpdate="UPDATE pagos_lotes SET  cod_estadopagolote=$estado where codigo=$codigo;
            UPDATE pagos_proveedores SET  cod_estadopago=$estado where cod_pagolote=$codigo;";
$stmtUpdate = $dbh->prepare($sqlUpdate);
$flagSuccess=$stmtUpdate->execute();

if($codEstado==5){
  //generar comprobante
}
$admin=$_GET["admin"];
if($admin==1){
	$urlListPagoLotes=$urlListPagoAdmin;
}
	if($flagSuccess==true){
	showAlertSuccessError(true,"../".$urlListPagoLotes);	
   }else{
	showAlertSuccessError(false,"../".$urlListPagoLotes);
   }
?>