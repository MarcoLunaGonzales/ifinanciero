<?php
session_start();
require_once '../conexion.php';
require_once '../functionsGeneral.php';
require_once '../functions.php';
require_once '../styles.php';

$dbh = new Conexion();
$sqlX="SET NAMES 'utf8'";
$stmtX = $dbh->prepare($sqlX);
$stmtX->execute();
$globalNombreGestion=$_SESSION["globalNombreGestion"];
$globalMes=$_SESSION['globalMes'];

$codigo=$_POST['codigo'];
$cod_prov=$_POST['cod_prov'];
$monto=$_POST['monto'];

$mensajeError="";
$datosContrato=obtenerDatosContratoSolicitudCapacitacion($codigo);

if($datosContrato[4]<$monto){
  $error_monto=1;
}
if($datosContrato[1]!=$cod_prov){
  $error_proveedor=1;
}

if($error_monto>0){
  $mensajeError.="El monto del contrato ".number_format($datosContrato[0],2,'.',',')." (Saldo: ".number_format($datosContrato[4],2,'.',',').") es menor al monto de la solicitud, ";
}
if($error_proveedor>0){
  $mensajeError.="El proveedor seleccionado no es igual al Docente del contrato, ";
}


if($error_monto>0||$error_proveedor>0){
  echo "1#####".$mensajeError;
}else{
  echo "0#####Satisfactorio";
}
