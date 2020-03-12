<?php
session_start();
require_once '../conexion.php';
require_once '../styles.php';
require_once '../functionsGeneral.php';
require_once '../functions.php';
require_once 'configModule.php';

$dbh = new Conexion();

$sqlX="SET NAMES 'utf8'";
$stmtX = $dbh->prepare($sqlX);
$stmtX->execute();

$globalNombreGestion=$_SESSION["globalNombreGestion"];
$globalUser=$_SESSION["globalUser"];
$globalGestion=$_SESSION["globalGestion"];
$globalUnidad=$_SESSION["globalUnidad"];
$globalNombreUnidad=$_SESSION['globalNombreUnidad'];
$globalArea=$_SESSION["globalArea"];
$globalAdmin=$_SESSION["globalAdmin"];

if(isset($_GET['cod_solicitud'])){
	$cod_solicitud=$_GET['cod_solicitud'];
  $cod_pagoproveedor=$_GET['cod_pagoproveedor'];
  $monto=$_GET['monto'];
  $saldo=$_GET['saldo'];
  $fecha=date("Y-m-d");
  $tipo_pago=$_GET['tipo_pago'];
  $proveedores_pago=$_GET['proveedores_pago'];
  $observaciones_pago=$_GET['observaciones_pago'];
  if($cod_pagoproveedor==0){
    $cod_pagoproveedor=obtenerCodigoPagoProveedor();
   $sqlInsert="INSERT INTO pagos_proveedores (codigo, fecha,observaciones) 
  VALUES ('".$cod_pagoproveedor."','".$fecha."','".$observaciones_pago."')";
  $stmtInsert = $dbh->prepare($sqlInsert);
  $stmtInsert->execute();
  }
  $fecha2=date("Y-m-d H:i:s");
  $sqlInsert2="INSERT INTO pagos_proveedoresdetalle (cod_pagoproveedor,cod_proveedor,cod_solicitudrecursos,cod_tipopagoproveedor,monto,observaciones,fecha) 
  VALUES ('".$cod_pagoproveedor."','".$proveedores_pago."','".$cod_solicitud."','".$tipo_pago."','".$monto."','".$observaciones_pago."','".$fecha2."')";
  $stmtInsert2 = $dbh->prepare($sqlInsert2);
  $stmtInsert2->execute();
  
}

?>
