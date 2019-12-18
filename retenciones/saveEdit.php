<?php

require_once '../layouts/bodylogin.php';
require_once '../conexion.php';
require_once '../functions.php';
require_once '../functionsGeneral.php';
require_once 'configModule.php';

$dbh = new Conexion();

$codigo=$_POST["codigo"];
$nombre=$_POST["nombre_retencion"];
$porcentajeCuentaOrigen=$_POST["cuenta_origen"];

//$cuenta=$_POST["cuenta"];
$cuenta=$_POST["cuenta_auto_num_id"];
$cuentaText=$_POST["cuenta_auto_num"];
$porcentaje=$_POST["porcentaje"];
$debehaber=$_POST["credito"];
$glosa=$_POST["glosa_retencion"];

if($cuentaText==""){
	$cuenta=0;
}
if($debehaber==1){
  $porcentajeCuentaOrigen=$_POST["cuenta_origen"]-$porcentaje;
}else{
  $porcentajeCuentaOrigen=$_POST["cuenta_origen"]+$porcentaje;
}
$stmt = $dbh->prepare("UPDATE configuracion_retenciones set nombre='$nombre',porcentaje_cuentaorigen='$porcentajeCuentaOrigen' where codigo=$codigo");
$flagSuccess=$stmt->execute();

 $stmt2 = $dbh->prepare("INSERT INTO configuracion_retencionesdetalle (cod_configuracionretenciones,cod_cuenta,porcentaje,debe_haber,glosa) 
  values ('$codigo','$cuenta','$porcentaje','$debehaber','$glosa')");
 $flagSuccessDetalle=$stmt2->execute();

if($flagSuccess==true){
	showAlertSuccessError(true,"../".$urlRegister."?cod=".$codigo);	
}else{
	showAlertSuccessError(false,"../".$urlRegister."?cod=".$codigo);
}
?>
