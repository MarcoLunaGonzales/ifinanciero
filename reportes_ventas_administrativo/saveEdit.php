<?php
error_reporting(-1);
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
  
  if(isset($_POST['codigo_factura'])){
    $codigo=$_POST['codigo_factura'];
    $nit=$_POST['nit_fac'];
    $nroFac=$_POST['nro_fac'];
      
    $fechaFac=$_POST['fecha_fac'];
    $razonFac=$_POST['razon_fac'];
    $impFac=$_POST['imp_fac'];            
    $autFac=$_POST['aut_fac'];
    $conFac=$_POST['con_fac'];
            
    $exeFac=$_POST['exe_fac'];
    $tipoFac=$_POST['tipo_fac'];
    $tazaFac=$_POST['taza_fac'];
    $iceFac=$_POST['ice_fac'];
    
    $sqlDetalle="UPDATE facturas_compra SET nit='$nit', nro_factura='$nroFac', fecha='$fechaFac', 
    razon_social='$razonFac', importe='$impFac', exento='$exeFac', nro_autorizacion='$autFac', codigo_control='$conFac',
    ice='$iceFac',tasa_cero='$tazaFac',tipo_compra='$tipoFac' WHERE codigo=$codigo";
    $stmtDetalle = $dbh->prepare($sqlDetalle);
    $flagSuccessDetalle=$stmtDetalle->execute();
  }

if($flagSuccessDetalle==true){
	showAlertSuccessError(true,"../".$urlList);	
}else{
	showAlertSuccessError(false,"../".$urlList);
}


?>