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
  $codigo_detalle=$_GET['codigo_detalle'];
  $cod_pagoproveedor=$_GET['cod_pagoproveedor'];
  $monto=$_GET['monto'];
  $saldo=$_GET['saldo'];
  $fecha=date("Y-m-d");
  $tipo_pago=$_GET['tipo_pago'];
  
  $proveedores_pago=$_GET['proveedores_pago'];
  $observaciones_pago=$_GET['observaciones_pago'];
  if($cod_pagoproveedor==0){
    $cod_pagoproveedor=obtenerCodigoPagoProveedor();
   $sqlInsert="INSERT INTO pagos_proveedores (codigo, fecha,observaciones,cod_estadopago) 
  VALUES ('".$cod_pagoproveedor."','".$fecha."','".$observaciones_pago."',1)";
  $stmtInsert = $dbh->prepare($sqlInsert);
  $stmtInsert->execute();
  }

  $cod_pagoproveedordetalle=obtenerCodigoPagoProveedorDetalle();
  $fecha2=date("Y-m-d H:i:s");
  $sqlInsert2="INSERT INTO pagos_proveedoresdetalle (codigo,cod_pagoproveedor,cod_proveedor,cod_solicitudrecursos,cod_solicitudrecursosdetalle,cod_tipopagoproveedor,monto,observaciones,fecha) 
  VALUES ('".$cod_pagoproveedordetalle."','".$cod_pagoproveedor."','".$proveedores_pago."','".$cod_solicitud."','".$codigo_detalle."','".$tipo_pago."','".$monto."','".$observaciones_pago."','".$fecha2."')";
  $stmtInsert2 = $dbh->prepare($sqlInsert2);
  $stmtInsert2->execute();
  

  if($tipo_pago==1){
   $banco=$_GET['banco'];
   $cheque=$_GET['cheque'];
   $numero_cheque=$_GET['numero_cheque'];
   $nombre_ben=$_GET['nombre_ben'];

   $sqlInsert3="INSERT INTO cheques_emitidos (cod_cheque,fecha,nombre_beneficiario,monto,cod_pagodetalle,cod_estadoreferencial) 
  VALUES ('".$cheque."','".$fecha."','".$nombre_ben."','".$monto."','".$cod_pagoproveedordetalle."',1)";
  $stmtInsert3 = $dbh->prepare($sqlInsert3);
  $stmtInsert3->execute();

  $sqlInsert4="UPDATE cheques SET nro_cheque=$numero_cheque where codigo=$cheque";
  $stmtInsert4 = $dbh->prepare($sqlInsert4);
  $stmtInsert4->execute();
  }
}

?>
