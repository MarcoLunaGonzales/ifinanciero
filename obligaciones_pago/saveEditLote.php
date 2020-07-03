<?php

require_once '../layouts/bodylogin.php';
require_once '../conexion.php';
require_once '../functions.php';
require_once '../functionsGeneral.php';
require_once 'configModule.php';

$dbh = new Conexion();
session_start();

$globalUser=$_SESSION["globalUser"];
$globalGestion=$_SESSION["globalGestion"];
$globalUnidad=$_SESSION["globalUnidad"];
$globalArea=$_SESSION["globalArea"];
$globalAdmin=$_SESSION["globalAdmin"];

//$fechaHoraActual=date("Y-m-d H:i:s");
$cantidadProveedores=$_POST['cantidad_proveedores'];
$nombre_lote=$_POST['nombre_lote'];
$proveedorItem = explode("####", $_POST['proveedor']);
$proveedor=$proveedorItem[0];
$porFecha = explode("/", $_POST['fecha_pago']);
$fecha_pago=$porFecha[2]."-".$porFecha[1]."-".$porFecha[0];
$observaciones_pago=$_POST['observaciones_pago'];

   $cod_pagolote=$_POST['cod_pagoloteedit'];
   $sqlInsert="UPDATE pagos_lotes SET nombre='$nombre_lote', fecha='$fecha_pago' WHERE codigo=$cod_pagolote"; 
   $stmtInsert = $dbh->prepare($sqlInsert);
   $stmtInsert->execute();
   

   $sqlDelete="DELETE FROM pagos_proveedoresdetalle where cod_pagoproveedor in (SELECT codigo from pagos_proveedores where cod_pagolote=$cod_pagolote)";
    $stmtInsert = $dbh->prepare($sqlDelete);
    $stmtInsert->execute();

   $sqlDelete="DELETE FROM pagos_proveedores where cod_pagolote=$cod_pagolote";
    $stmtInsert = $dbh->prepare($sqlDelete);
    $stmtInsert->execute();

$totalPago=0;
$contadorCheque=0;$contadorChequeFilas=0;

for ($pro=1; $pro <= $cantidadProveedores ; $pro++) {

  if(isset($_POST['cantidad_filas'.$pro])){
    $cod_pagoproveedor=obtenerCodigoPagoProveedor();
    $sqlInsert="INSERT INTO pagos_proveedores (codigo,cod_pagolote, fecha,observaciones,cod_comprobante,cod_estadopago,cod_ebisa) 
     VALUES ('".$cod_pagoproveedor."','".$cod_pagolote."','".$fecha_pago."','".$observaciones_pago."','0',1,0)";
    $stmtInsert = $dbh->prepare($sqlInsert);
    $stmtInsert->execute();

    $cantidadFilas=$_POST['cantidad_filas'.$pro];
    for ($i=1;$i<=$cantidadFilas;$i++){
  $proveedor=$_POST['codigo_proveedor'.$i."PPPP".$pro];         
  $monto_pago=$_POST["monto_pago".$i."PPPP".$pro];
  $totalPago+=$monto_pago;
  $cod_solicitud=$_POST["codigo_solicitud".$i."PPPP".$pro];
  $codigo_detalle=$_POST["codigo_solicitudDetalle".$i."PPPP".$pro];
  $glosa_detalle=$_POST["glosa_detalle".$i."PPPP".$pro];
  if(!($monto_pago==0 || $monto_pago=="")){
    $contadorChequeFilas++;
    $porFecha2 = explode("/", $_POST["fecha_pago".$i."PPPP".$pro]);
    $fecha_pagoDet=$porFecha2[2]."-".$porFecha2[1]."-".$porFecha2[0];
    $tipo_pago=$_POST["tipo_pago".$i."PPPP".$pro];

    $cod_pagoproveedordetalle=obtenerCodigoPagoProveedorDetalle();
    $sqlInsert2="INSERT INTO pagos_proveedoresdetalle (codigo,cod_pagoproveedor,cod_proveedor,cod_solicitudrecursos,cod_solicitudrecursosdetalle,cod_tipopagoproveedor,monto,observaciones,fecha) 
     VALUES ('".$cod_pagoproveedordetalle."','".$cod_pagoproveedor."','".$proveedor."','".$cod_solicitud."','".$codigo_detalle."','".$tipo_pago."','".$monto_pago."','".$observaciones_pago."','".$fecha_pagoDet."')";
    $stmtInsert2 = $dbh->prepare($sqlInsert2);
    $flagSuccess=$stmtInsert2->execute();
       
  }
}
  }//if isset 
  
}
          

if($flagSuccess==true){
	showAlertSuccessError(true,"../".$urlListPagoLotes);	
}else{
	showAlertSuccessError(false,"../".$urlListPagoLotes);
}

?>
