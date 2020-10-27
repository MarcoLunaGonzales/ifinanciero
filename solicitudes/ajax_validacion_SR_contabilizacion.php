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
$mensajeError="";
//echo "1####no hay solicitud weon ".$codigo;

//validacion si no retenciones
$sqlRetencion="SELECT count(*) as sin_retencion from solicitud_recursosdetalle where cod_solicitudrecurso=$codigo and cod_confretencion=0 or cod_confretencion is null";
$stmt = $dbh->prepare($sqlRetencion);
$stmt->execute();
$error_retencion=0;
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
  if($row['sin_retencion']>0){
   $error_retencion=1;
  }
}
//validacion si tiene retencion IVA y no factura
$sqlRetencionFacturas="SELECT sd.codigo,(select count(*) from facturas_compra where cod_solicitudrecursodetalle=sd.codigo) as facturas from solicitud_recursosdetalle sd where sd.cod_solicitudrecurso=$codigo and sd.cod_confretencion=8;";
$stmt = $dbh->prepare($sqlRetencionFacturas);
$stmt->execute();
$error_facturas=0;
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
  if($row['facturas']==0){
   $error_facturas=1;
  }    
}

//validacion si tiene factura en fecha diferente a la sesion
$sqlRetencionFacturas="SELECT sd.codigo,(select count(*) from facturas_compra where cod_solicitudrecursodetalle=sd.codigo and fecha BETWEEN '$globalNombreGestion-$globalMes-01' and '$globalNombreGestion-$globalMes-31') as facturas from solicitud_recursosdetalle sd where sd.cod_solicitudrecurso=$codigo and sd.cod_confretencion=8;";
$stmt = $dbh->prepare($sqlRetencionFacturas);
$stmt->execute();
$error_facturas_fecha=0;
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
  if($row['facturas']==0&&$error_facturas==0){
   $error_facturas_fecha=1;
  }    
}
//validacion montos
$sqlRetencionFacturas="SELECT sd.codigo,(select count(*) from facturas_compra where cod_solicitudrecursodetalle=sd.codigo) as facturas,(select sum(importe) from facturas_compra where cod_solicitudrecursodetalle=sd.codigo) as monto_facturas,sum(sd.importe) as monto_solicitud from solicitud_recursosdetalle sd  where sd.cod_solicitudrecurso=$codigo and sd.cod_confretencion=8;";
$stmt = $dbh->prepare($sqlRetencionFacturas);
$stmt->execute();
$error_facturas_monto=0;
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
  if($row['monto_facturas']!=$row['monto_solicitud']&&$error_facturas_fecha==0&&$error_facturas==0){
   $error_facturas_monto=1;
  }    
}

if($error_retencion>0){
  $mensajeError.="No tiene asignada la rentención, ";
}
if($error_facturas>0){
  $mensajeError.="No Existe ninguna Factura Asociada para el tipo de Retencion, ";
}
if($error_facturas_fecha>0){
  $mensajeError.="La fecha de la Factura no corresponde al Mes y Gestión de Trabajo, ";
}
if($error_facturas_monto>0){
  $mensajeError.="El monto de la solicitud no iguala al de las facturas,";
}

if($error_retencion>0||$error_facturas>0||$error_facturas_fecha>0||$error_facturas_monto>0){
  echo "1####".$mensajeError;
}else{
  echo "0####Satisfactorio";
}
