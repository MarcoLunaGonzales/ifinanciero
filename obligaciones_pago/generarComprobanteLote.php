<?php
require_once '../layouts/bodylogin.php';
require_once '../conexion.php';
require_once '../functions.php';
require_once '../functionsGeneral.php';
require_once 'configModule.php';

$dbh = new Conexion();

$dbh_detalle = new Conexion();


$codigo=$_GET["cod"];
session_start();

$globalUser=$_SESSION["globalUser"];
$globalGestion=$_SESSION["globalGestion"];
$globalUnidad=$_SESSION["globalUnidad"];
$globalArea=$_SESSION["globalArea"];
$globalAdmin=$_SESSION["globalAdmin"];
$globalMes=$_SESSION['globalMes'];
$globalNombreGestion=$_SESSION["globalNombreGestion"];
$fecha_pago=date("Y-m-d H:i:s");

$total_monto_debe=0;
$total_monto_haber=0;

//creacion del comprobante de pago
    $codComprobante=obtenerCodigoComprobante();
    $anioActual=date("Y");
    $mesActual=date("m");
    $diaActual=date("d");
    $codMesActiva=$_SESSION['globalMes']; 
    $month = $globalNombreGestion."-".$codMesActiva;
    $aux = date('Y-m-d', strtotime("{$month} + 1 month"));
    $diaUltimo = date('d', strtotime("{$aux} - 1 day"));
    $horasActual=date("H:i:s");
    if((int)$globalNombreGestion<(int)$anioActual){
      $fechaHoraActual=$globalNombreGestion."-".$codMesActiva."-".$diaUltimo." ".$horasActual;
    }else{
      if((int)$mesActual==(int)$codMesActiva){
          $fechaHoraActual=date("Y-m-d H:i:s");
      }else{
        $fechaHoraActual=$globalNombreGestion."-".$codMesActiva."-".$diaUltimo." ".$horasActual;
      } 
    }

    
    $tipoComprobante=2;
    $nroCorrelativo=numeroCorrelativoComprobante($globalGestion,$globalUnidad,2,$globalMes);
    $glosa="PAGOS  ";
    $userSolicitud=$globalUser;
    $unidadSol=$globalUnidad;
    $areaSol=$globalArea;

    $sqlInsert="INSERT INTO comprobantes (codigo, cod_empresa, cod_unidadorganizacional, cod_gestion, cod_moneda, cod_estadocomprobante, cod_tipocomprobante, fecha, numero, glosa, created_at, created_by, modified_at, modified_by) 
    VALUES ('$codComprobante', '1', '$globalUnidad', '$globalNombreGestion', '1', '1', '$tipoComprobante', '$fechaHoraActual', '$nroCorrelativo', '$glosa', '$fecha_pago', '$globalUser', '$fecha_pago', '$globalUser')";
    $stmtInsert = $dbh->prepare($sqlInsert);
    $flagSuccessComprobante=$stmtInsert->execute();
    
    $sqlDelete="";
    $sqlDelete="DELETE from comprobantes_detalle where cod_comprobante='$codComprobante'";
    $stmtDel = $dbh->prepare($sqlDelete);
    $flagSuccess=$stmtDel->execute();
        //fin de comprobante

    $sqlInsert4="UPDATE pagos_proveedores SET cod_comprobante=$codComprobante,cod_estadopago=5 where cod_pagolote=$codigo;
                 UPDATE pagos_lotes SET cod_comprobante=$codComprobante,cod_estadopagolote=5 where codigo=$codigo;";
    $stmtInsert4 = $dbh->prepare($sqlInsert4);
    $stmtInsert4->execute();
    $indexCompro=1;
    $datosPago = listaDetallePagosProveedoresLote($codigo);
    $obs_cabecera="PAGOS PROVEDORES";
    while ($row = $datosPago->fetch(PDO::FETCH_ASSOC)) {
        $cod_plancuenta=$row['cod_cuenta'];

        $cuentaAuxiliar=$row['cod_cuentaauxiliar'];

        
        $proveedor=$row['cod_proveedor'];  
       $monto_pago=$row["monto"];
       // $codigo_detalle=$row["cod_solicitudrecursosdetalle"];
       // $glosa_detalle=$row["detalle"];

       $glosa_detalle=$row["observaciones"];       
       $obs_cabecera=$row["obs_cabecera"];
       $cod_solicitudrecursos=$row["cod_solicitudrecursos"];//se encuentra el estado de cuenta
       // $cod_solicitudrecursosdetalle=$row["cod_solicitudrecursosdetalle"];//se encuentra el codigo de detalle comprobante 
       //comprobante detalle
       // $cuenta=obtenerCuentaPasivaSolicitudesRecursos($cod_plancuenta);
       // $cuentaAuxiliar=obtenerCodigoCuentaAuxiliarProveedorCliente(1,$proveedor);
       // $cuentaAuxiliar=obtenerCodigoCuentaAuxiliarProveedorClienteCuenta(1,$proveedor,$cod_plancuenta);
        //$cuentaAuxiliar=0;
        $numeroCuenta=trim(obtieneNumeroCuenta($cod_plancuenta));
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
        $total_monto_debe+=$debe;
        // $total_monto_haber+=$haber;
        // $glosaDetalle=obtenerGlosaSolicitudRecursoDetalle($codigo_detalle);
        // if($glosaDetalle==""){
        $glosaDetalle=$glosa_detalle;

        // }

        $codComprobanteDetalle=obtenerCodigoComprobanteDetalle();
        $sqlDetalle="INSERT INTO comprobantes_detalle (codigo,cod_comprobante, cod_cuenta, cod_cuentaauxiliar, cod_unidadorganizacional, cod_area, debe, haber, glosa, orden) 
        VALUES ('$codComprobanteDetalle','$codComprobante', '$cod_plancuenta', '$cuentaAuxiliar', '$unidadDetalle', '$area', '$debe', '$haber', '$glosaDetalle', '$indexCompro')";
        // echo $sqlDetalle."DDDDD";
        $stmtDetalle = $dbh_detalle->prepare($sqlDetalle);
        $flagSuccessDetalle=$stmtDetalle->execute();
       
    
       //fin comprobante detalle
        // $codComprobanteDetalleOrigen=obtenerCodigoEstadoCuentaSolicitudRecursosDetalle($codigo_detalle);
        //estado de cuentas devengado
          $sqlDetalleEstadoCuenta="INSERT INTO estados_cuenta (cod_comprobantedetalle, cod_plancuenta, monto, cod_proveedor, fecha,cod_comprobantedetalleorigen,cod_cuentaaux,glosa_auxiliar) 
          VALUES ('$codComprobanteDetalle', '$cod_plancuenta', '$debe','$proveedor', '$fecha_pago','$cod_solicitudrecursos','$cuentaAuxiliar','$glosaDetalle')";
          $stmtDetalleEstadoCuenta = $dbh_detalle->prepare($sqlDetalleEstadoCuenta);
          $stmtDetalleEstadoCuenta->execute();
        $indexCompro++;   
    }


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
    // $haber=$monto_pago;
    $haber=$total_monto_debe;
    // $glosaDetalle=obtenerGlosaSolicitudRecursoDetalle($codigo_detalle);
    // if($glosaDetalle==""){
    //   $glosaDetalle=$glosa." - ".$glosa_detalle;
    // }    
    $codComprobanteDetalle=obtenerCodigoComprobanteDetalle();
    $sqlDetalle="INSERT INTO comprobantes_detalle (codigo,cod_comprobante, cod_cuenta, cod_cuentaauxiliar, cod_unidadorganizacional, cod_area, debe, haber, glosa, orden) 
    VALUES ('$codComprobanteDetalle','$codComprobante', '$cuenta', '$cuentaAuxiliar', '$unidadDetalle', '$area', '$debe', '$haber', '$obs_cabecera', '$indexCompro')";
    //echo $sqlDetalle."RRRR"; 
    $stmtDetalle = $dbh_detalle->prepare($sqlDetalle);
    $flagSuccessDetalle=$stmtDetalle->execute();

    
    $sqlUpdate="UPDATE comprobantes SET glosa='$obs_cabecera' where codigo=$codComprobante";
    $stmtUpdate = $dbh_detalle->prepare($sqlUpdate);
    $stmtUpdate->execute();

$dbh="";
$dbh_detalle="";
if($flagSuccess==true){
	showAlertSuccessError(true,"../".$urlListPagoLotes);	
   }else{
	showAlertSuccessError(false,"../".$urlListPagoLotes);
   }
?>