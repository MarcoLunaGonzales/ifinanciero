<?php
require_once __DIR__.'/../conexion.php';
require_once __DIR__.'/../functions.php';
require_once __DIR__.'/../functionsGeneral.php';
require_once '../layouts/bodylogin.php';
$dbh = new Conexion();
$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);//try
set_time_limit(0);
// $globalUser=$_SESSION["globalUser"];
//RECIBIMOS LAS VARIABLES
$cod_solicitud_e=$_POST['cod_solicitud_e'];
$total_items_tipopago=$_POST['total_items_tipopago'];

$sqlDeleteTiposPago="DELETE from solicitudes_facturacion_tipospago where cod_solicitudfacturacion=$cod_solicitud_e";
$stmtDelTiposPago = $dbh->prepare($sqlDeleteTiposPago);
$flagSuccess=$stmtDelTiposPago->execute();
$flagSuccess=true;
if($flagSuccess){
  $nF=$total_items_tipopago;
  if($nF>0){
    $tipo_pago_mayor=0;//varibale que alamcenara el tipo de pago en la solictud
    $monto_bob_mayor=0;

    $sw_auxiliar_tp=1;
    for($j=0;$j<$nF;$j++){
        $codigo_tipopago=$_POST['codigo_tipopago'.$j];
        $monto_porcentaje=$_POST['monto_porcentaje_tipopago'.$j];
        $monto_bob=$_POST['monto_bob_tipopago'.$j];          
        $monto_bob=round($monto_bob,2);
        if($monto_bob_mayor<$monto_bob){
            $monto_bob_mayor=$monto_bob;
            $tipo_pago_mayor=$codigo_tipopago;
        }
        
        // echo "codigo_tipopago:".$codigo_tipopago."<br>";
        // echo "monto_porcentaje:".$monto_porcentaje."<br>";        
        // echo "monto_bob:".$monto_bob."<br>";                      
        if($monto_porcentaje!=0 && $monto_porcentaje!="" ){
          // echo "una<br><br><br>";
          $sqlTiposPago="INSERT INTO solicitudes_facturacion_tipospago(cod_solicitudfacturacion, cod_tipopago, porcentaje, monto) VALUES ('$cod_solicitud_e','$codigo_tipopago','$monto_porcentaje','$monto_bob')";
          $stmtTiposPago = $dbh->prepare($sqlTiposPago);
          $stmtTiposPago->execute();
        }
        
    }
    $stmtUpdateFormaPago = $dbh->prepare("UPDATE solicitudes_facturacion set cod_tipopago='$tipo_pago_mayor'
    where codigo = $cod_solicitud_e");      
    $flagSuccess=$stmtUpdateFormaPago->execute();
  }
}
showAlertSuccessError($flagSuccess,"../index.php?opcion=listFacturasServicios_conta");



