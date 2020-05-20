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

   $cod_pagoproveedor=obtenerCodigoPagoProveedor();
   $sqlInsert="INSERT INTO pagos_proveedores (codigo, fecha,observaciones,cod_comprobante,cod_estadopago,cod_ebisa) 
  VALUES ('".$cod_pagoproveedor."','".$fecha_pago."','".$observaciones_pago."','0',1,0)";
  $stmtInsert = $dbh->prepare($sqlInsert);
  $stmtInsert->execute();
$totalPago=0;
$contadorCheque=0;$contadorChequeFilas=0;
for ($i=1;$i<=$cantidadFilas;$i++){ 	    	
	$monto_pago=$_POST["monto_pago".$i];
  $totalPago+=$monto_pago;
  $cod_solicitud=$_POST["codigo_solicitud".$i];
  $codigo_detalle=$_POST["codigo_solicitudDetalle".$i];
  $glosa_detalle=$_POST["glosa_detalle".$i];
	if(!($monto_pago==0 || $monto_pago=="")){
    $contadorChequeFilas++;
    $porFecha2 = explode("/", $_POST["fecha_pago".$i]);
    $fecha_pagoDet=$porFecha2[2]."-".$porFecha2[1]."-".$porFecha2[0];
		$tipo_pago=$_POST["tipo_pago".$i];

    $cod_pagoproveedordetalle=obtenerCodigoPagoProveedorDetalle();
    $sqlInsert2="INSERT INTO pagos_proveedoresdetalle (codigo,cod_pagoproveedor,cod_proveedor,cod_solicitudrecursos,cod_solicitudrecursosdetalle,cod_tipopagoproveedor,monto,observaciones,fecha) 
     VALUES ('".$cod_pagoproveedordetalle."','".$cod_pagoproveedor."','".$proveedor."','".$cod_solicitud."','".$codigo_detalle."','".$tipo_pago."','".$monto_pago."','".$observaciones_pago."','".$fecha_pagoDet."')";
    $stmtInsert2 = $dbh->prepare($sqlInsert2);
    $flagSuccess=$stmtInsert2->execute();

    if($tipo_pago==1){
      $contadorCheque++;
     $banco=$_POST['banco_pago'.$i];
     $cheque=$_POST['emitidos_pago'.$i];
     $numero_cheque=$_POST['numero_cheque'.$i];
     $nombre_ben=$_POST['beneficiario'.$i];

     $sqlInsert3="INSERT INTO cheques_emitidos (cod_cheque,fecha,nombre_beneficiario,monto,cod_pagodetalle,cod_estadoreferencial) 
              VALUES ('".$cheque."','".$fecha_pagoDet."','".$nombre_ben."','".$monto_pago."','".$cod_pagoproveedordetalle."',1)";
     $stmtInsert3 = $dbh->prepare($sqlInsert3);
     $stmtInsert3->execute();

     $sqlInsert4="UPDATE cheques SET nro_cheque=$numero_cheque where codigo=$cheque";
     $stmtInsert4 = $dbh->prepare($sqlInsert4);
     $stmtInsert4->execute();
    }
             
	}
}

if($contadorCheque==$contadorChequeFilas){
  //creacion del comprobante de pago
    $codComprobante=obtenerCodigoComprobante();
    $codGestion=date("Y");
    $tipoComprobante=2;
    $nroCorrelativo=numeroCorrelativoComprobante($globalGestion,$globalUnidad,3);
    $fechaHoraActual=date("Y-m-d H:i:s");
    $glosa="PAGOS ".nameProveedor($proveedor)." ".$observaciones_pago;
    $userSolicitud=$globalUser;
    $unidadSol=$globalUnidad;
    $areaSol=$globalArea;

    $sqlInsert="INSERT INTO comprobantes (codigo, cod_empresa, cod_unidadorganizacional, cod_gestion, cod_moneda, cod_estadocomprobante, cod_tipocomprobante, fecha, numero, glosa, created_at, created_by, modified_at, modified_by) 
    VALUES ('$codComprobante', '1', '$globalUnidad', '$codGestion', '1', '1', '$tipoComprobante', '$fechaHoraActual', '$nroCorrelativo', '$glosa', '$fechaHoraActual', '$userSolicitud', '$fechaHoraActual', '$userSolicitud')";
    $stmtInsert = $dbh->prepare($sqlInsert);
    $flagSuccessComprobante=$stmtInsert->execute();
    
    $sqlDelete="";
    $sqlDelete="DELETE from comprobantes_detalle where cod_comprobante='$codComprobante'";
    $stmtDel = $dbh->prepare($sqlDelete);
    $flagSuccess=$stmtDel->execute();
        //fin de comprobante

    $sqlInsert4="UPDATE pagos_proveedores SET cod_comprobante=$codComprobante,cod_estadopago=5 where codigo=$cod_pagoproveedor";
    $stmtInsert4 = $dbh->prepare($sqlInsert4);
    $stmtInsert4->execute();

    for ($i=1;$i<=$cantidadFilas;$i++){         
  $monto_pago=$_POST["monto_pago".$i];
  $totalPago+=$monto_pago;
  $cod_solicitud=$_POST["codigo_solicitud".$i];
  $codigo_detalle=$_POST["codigo_solicitudDetalle".$i];
  $glosa_detalle=$_POST["glosa_detalle".$i];
  if(!($monto_pago==0 || $monto_pago=="")){
    $porFecha2 = explode("/", $_POST["fecha_pago".$i]);
    $fecha_pagoDet=$porFecha2[2]."-".$porFecha2[1]."-".$porFecha2[0];
    $tipo_pago=$_POST["tipo_pago".$i];

    //comprobante detalle
    $cuenta=obtenerValorConfiguracion(37);
        $cuentaAuxiliar=0;
        $numeroCuenta=trim(obtieneNumeroCuenta($cuenta));
        $inicioNumero=$numeroCuenta[0];
        $unidadarea=obtenerUnidadAreaCentrosdeCostos($inicioNumero);
        if($unidadarea[0]==0){
            $unidadDetalle=$unidadSol;
            $area=$areaSol;
        }else{
            $unidadDetalle=$unidadarea[0];
            $area=$unidadarea[1];
        }

        $debe=$monto_pago;
        $haber=0;
        
        $facturaCabecera=obtenerNumeroFacturaSolicitudRecursos($cod_solicitud);

        
        
        $glosaDetalle=obtenerGlosaSolicitudRecursoDetalle($codigo_detalle);
        if($glosaDetalle==""){
          $glosaDetalle=$glosa." - ".$glosa_detalle;
        }  

        $codComprobanteDetalle=obtenerCodigoComprobanteDetalle();
        $sqlDetalle="INSERT INTO comprobantes_detalle (codigo,cod_comprobante, cod_cuenta, cod_cuentaauxiliar, cod_unidadorganizacional, cod_area, debe, haber, glosa, orden) 
        VALUES ('$codComprobanteDetalle','$codComprobante', '$cuenta', '$cuentaAuxiliar', '$unidadDetalle', '$area', '$debe', '$haber', '$glosaDetalle', '1')";
        $stmtDetalle = $dbh->prepare($sqlDetalle);
        $flagSuccessDetalle=$stmtDetalle->execute();
       
       //haber
        $cuenta=obtenerValorConfiguracion(38);
        $cuentaAuxiliar=0;
        $numeroCuenta=trim(obtieneNumeroCuenta($cuenta));
        $inicioNumero=$numeroCuenta[0];
        $unidadarea=obtenerUnidadAreaCentrosdeCostos($inicioNumero);
        if($unidadarea[0]==0){
            $unidadDetalle=$unidadSol;
            $area=$areaSol;
        }else{
            $unidadDetalle=$unidadarea[0];
            $area=$unidadarea[1];
        }

        $debe=0;
        $haber=$monto_pago;
        $glosaDetalle=obtenerGlosaSolicitudRecursoDetalle($codigo_detalle);
        if($glosaDetalle==""){
          $glosaDetalle=$glosa." - ".$glosa_detalle;
        }

        $codComprobanteDetalle=obtenerCodigoComprobanteDetalle();
        $sqlDetalle="INSERT INTO comprobantes_detalle (codigo,cod_comprobante, cod_cuenta, cod_cuentaauxiliar, cod_unidadorganizacional, cod_area, debe, haber, glosa, orden) 
        VALUES ('$codComprobanteDetalle','$codComprobante', '$cuenta', '$cuentaAuxiliar', '$unidadDetalle', '$area', '$debe', '$haber', '$glosaDetalle', '2')";
        $stmtDetalle = $dbh->prepare($sqlDetalle);
        $flagSuccessDetalle=$stmtDetalle->execute();
       //fin comprobante detalle
        $codComprobanteDetalleOrigen=obtenerCodigoEstadoCuentaSolicitudRecursosDetalle($codigo_detalle);
            //estado de cuentas devengado
              $sqlDetalleEstadoCuenta="INSERT INTO estados_cuenta (cod_comprobantedetalle, cod_plancuenta, monto, cod_proveedor, fecha,cod_comprobantedetalleorigen,cod_cuentaaux) 
              VALUES ('$codComprobanteDetalle', '$cuenta', '$haber', '0', '$fecha_pago','$codComprobanteDetalleOrigen','$cuentaAuxiliar')";
              $stmtDetalleEstadoCuenta = $dbh->prepare($sqlDetalleEstadoCuenta);
              $stmtDetalleEstadoCuenta->execute();
             
      }
    }
}else{

}         

if($flagSuccess==true){
	showAlertSuccessError(true,"../".$urlList);	
}else{
	showAlertSuccessError(false,"../".$urlList);
}


?>
