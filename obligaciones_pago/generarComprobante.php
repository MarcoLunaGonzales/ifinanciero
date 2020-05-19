<?php
require_once '../layouts/bodylogin.php';
require_once '../conexion.php';
require_once '../functions.php';
require_once '../functionsGeneral.php';
require_once 'configModule.php';

$dbh = new Conexion();
$codigo=$_GET["cod"];
session_start();

$globalUser=$_SESSION["globalUser"];
$globalGestion=$_SESSION["globalGestion"];
$globalUnidad=$_SESSION["globalUnidad"];
$globalArea=$_SESSION["globalArea"];
$globalAdmin=$_SESSION["globalAdmin"];

$fecha_pago=date("Y-m-d");

//creacion del comprobante de pago
    $codComprobante=obtenerCodigoComprobante();
    $codGestion=date("Y");
    $tipoComprobante=2;
    $nroCorrelativo=numeroCorrelativoComprobante($globalGestion,$globalUnidad,3);
    $fechaHoraActual=date("Y-m-d H:i:s");
    $glosa="PAGOS  COMPROBANTE (SOLICITUD - RECURSOS) ";
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

    $sqlInsert4="UPDATE pagos_proveedores SET cod_comprobante=$codComprobante,cod_estadopago=5 where codigo=$codigo";
    $stmtInsert4 = $dbh->prepare($sqlInsert4);
    $stmtInsert4->execute();

    $datosPago = listaDetallePagosProveedores($codigo);
    while ($row = $datosPago->fetch(PDO::FETCH_ASSOC)) {
       $monto_pago=$row["monto"];
       $codigo_detalle=$row["cod_solicitudrecursosdetalle"];
       $glosa_detalle=$row["detalle"];
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
        
        $glosaDetalle=$glosa." - ".$glosa_detalle;


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
        $glosaDetalle=$glosa." - ".$glosa_detalle;

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

if($flagSuccess==true){
	//showAlertSuccessError(true,"../".$urlListPago);	
   }else{
	//showAlertSuccessError(false,"../".$urlListPago);
   }
?>