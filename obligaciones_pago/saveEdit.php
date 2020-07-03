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
$cantidadFilas=$_POST['cantidad_filas'];
$proveedorItem = explode("####", $_POST['proveedor']);
$proveedor=$proveedorItem[0];
$porFecha = explode("/", $_POST['fecha_pago']);
$fecha_pago=$porFecha[2]."-".$porFecha[1]."-".$porFecha[0];
$observaciones_pago=$_POST['observaciones_pago'];

   $cod_pagoproveedor=$_POST['cod_pagoproveedoredit'];
   $sqlInsert="UPDATE pagos_proveedores SET fecha='$fecha_pago',observaciones='$observaciones_pago' where codigo=$cod_pagoproveedor";
  $stmtInsert = $dbh->prepare($sqlInsert);
  $stmtInsert->execute();
$totalPago=0;
$contadorCheque=0;$contadorChequeFilas=0;
for ($i=1;$i<=$cantidadFilas;$i++){
  $proveedor=$_POST['codigo_proveedor'.$i];  	    	
	$monto_pago=$_POST["monto_pago".$i];
  $totalPago+=$monto_pago;
  $cod_solicitud=$_POST["codigo_solicitud".$i];
  $codigo_detalle=$_POST["codigo_solicitudDetalle".$i];
  $glosa_detalle=$_POST["glosa_detalle".$i];
  $codigo_detallepago=$_POST["codigo_detallepago".$i];
	if(!($monto_pago==0 || $monto_pago=="")){
    $contadorChequeFilas++;
    $porFecha2 = explode("/", $_POST["fecha_pago".$i]);
    $fecha_pagoDet=$porFecha2[2]."-".$porFecha2[1]."-".$porFecha2[0];
		$tipo_pago=$_POST["tipo_pago".$i];

    $cod_pagoproveedordetalle=$codigo_detallepago;
    $sqlInsert2="UPDATE pagos_proveedoresdetalle SET monto='$monto_pago',observaciones='$observaciones_pago',fecha='$fecha_pagoDet' WHERE codigo=$cod_pagoproveedordetalle";
    $stmtInsert2 = $dbh->prepare($sqlInsert2);
    $flagSuccess=$stmtInsert2->execute();
             
	}
}        

if($flagSuccess==true){
	showAlertSuccessError(true,"../".$urlListPago);	
}else{
	showAlertSuccessError(false,"../".$urlListPago);
}


?>
