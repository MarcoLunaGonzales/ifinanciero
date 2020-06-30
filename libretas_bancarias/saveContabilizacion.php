<?php
require_once '../layouts/bodylogin.php';
require_once '../conexion.php';
require_once '../functions.php';
require_once '../functionsGeneral.php';
require_once 'configModule.php';
session_start();
$globalUser=$_SESSION["globalUser"];
$globalGestion=$_SESSION["globalGestion"];
$globalUnidad=$_SESSION["globalUnidad"];
$globalArea=$_SESSION["globalArea"];
$globalAdmin=$_SESSION["globalAdmin"];

$dbh = new Conexion();

//RECIBIMOS LAS VARIABLES
$filas=$_POST['cantidad_filas'];
$monto=$_POST['monto_contabilizar'];
$mesLetra=$_POST['mes_conta'];
$codLibreta=$_POST['cod_libreta'];


$cod_gestion_x=$_POST['cod_gestion_x'];
$cod_mes_x=$_POST['cod_mes_x'];
$fecha_actual=date('Y-m-d');
set_time_limit(0);
//insertamos en tabla
$sqlInsert="INSERT INTO depositos_no_facturados (cod_libretabancaria, cod_gestion, cod_mes, fecha, cod_estadoreferencial) 
    VALUES ('$codLibreta', '$cod_gestion_x', '$cod_mes_x', '$fecha_actual','1')";
$stmtInsert = $dbh->prepare($sqlInsert);
$flagSuccessComprobante=$stmtInsert->execute();
$stmtDeposito = $dbh->prepare("SELECT codigo from depositos_no_facturados where cod_gestion=$cod_gestion_x and cod_mes=$cod_mes_x order by codigo desc");
$stmtDeposito->execute();
$resultDeposito = $stmtDeposito->fetch();
$cod_deposito_x = $resultDeposito['codigo'];


$flagSuccess=false;
for ($i=1; $i <=$filas ; $i++) { 
  $codigo=$_POST['cod_libretadetalle'.$i];	
  // Prepare
  $stmt = $dbh->prepare("UPDATE libretas_bancariasdetalle set cod_estado=1 where codigo=:codigo");
  $stmt->bindParam(':codigo', $codigo);
  $flagSuccess=$stmt->execute();

  
  $sqlInsertDetalle="INSERT INTO depositos_no_facturados_detalle (cod_depositonofacturado, cod_libretabancariadetalle, monto) 
    VALUES ('$cod_deposito_x', '$codigo', '$monto')";  
  $stmtInsertDetalle = $dbh->prepare($sqlInsertDetalle);
  $stmtInsertDetalle->execute();

}
if($filas>0){
	$nombreLibreta=nameLibretas($codLibreta);
//creacion del comprobante de pago
    $codComprobante=obtenerCodigoComprobante();
    $codGestion=date("Y");
    $tipoComprobante=1;
    $nroCorrelativo=numeroCorrelativoComprobante($globalGestion,$globalUnidad,1);
    $fechaHoraActual=date("Y-m-d H:i:s");
    $glosa="Capitalización de Intereses Correspondiente al mes de ".$mesLetra." y Contabilización de Depósitos No Facturados ".$nombreLibreta;
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

    $cuenta=cuentaLibreta($codLibreta);
    $cuentaAuxiliar=0;
        //fin de comprobante
    $numeroCuenta=trim(obtieneNumeroCuenta($cuenta));
    $inicioNumero=$numeroCuenta[0];
    $unidadarea=obtenerUnidadAreaCentrosdeCostos($inicioNumero);
     if($unidadarea[0]==0){
        $unidadDetalle=$globalUnidad;
        $area=$globalArea;
     }else{
        $unidadDetalle=$unidadarea[0];
        $area=$unidadarea[1];
     }
     $glosaDetalle=$glosa;
     $debe=$monto;
     $haber=0;

    $codComprobanteDetalle=obtenerCodigoComprobanteDetalle();
    $sqlDetalle="INSERT INTO comprobantes_detalle (codigo,cod_comprobante, cod_cuenta, cod_cuentaauxiliar, cod_unidadorganizacional, cod_area, debe, haber, glosa, orden) 
        VALUES ('$codComprobanteDetalle','$codComprobante', '$cuenta', '$cuentaAuxiliar', '$unidadDetalle', '$area', '$debe', '$haber', '$glosaDetalle', '1')";
    $stmtDetalle = $dbh->prepare($sqlDetalle);
    $flagSuccessDetalle=$stmtDetalle->execute();
    
    $cuenta=contraCuentaLibreta($codLibreta);
    $cuentaAuxiliar=0;
        //fin de comprobante
    $numeroCuenta=trim(obtieneNumeroCuenta($cuenta));
    $inicioNumero=$numeroCuenta[0];
    $unidadarea=obtenerUnidadAreaCentrosdeCostos($inicioNumero);
     if($unidadarea[0]==0){
        $unidadDetalle=$globalUnidad;
        $area=$globalArea;
     }else{
        $unidadDetalle=$unidadarea[0];
        $area=$unidadarea[1];
     }
     $glosaDetalle=$glosa;
     $debe=0;
     $haber=$monto;
    $codComprobanteDetalle=obtenerCodigoComprobanteDetalle();
    $sqlDetalle="INSERT INTO comprobantes_detalle (codigo,cod_comprobante, cod_cuenta, cod_cuentaauxiliar, cod_unidadorganizacional, cod_area, debe, haber, glosa, orden) 
        VALUES ('$codComprobanteDetalle','$codComprobante', '$cuenta', '$cuentaAuxiliar', '$unidadDetalle', '$area', '$debe', '$haber', '$glosaDetalle', '2')";
    $stmtDetalle = $dbh->prepare($sqlDetalle);
    $flagSuccessDetalle=$stmtDetalle->execute();

}

showAlertSuccessError($flagSuccess,"../".$urlList_2);	
?>