<?php

require_once '../layouts/bodylogin.php';
require_once '../conexion.php';
require_once '../functions.php';
require_once '../functionsGeneral.php';

$dbh = new Conexion();


$urlRedirect="../index.php?opcion=configuracion_edit_sf";
if (isset($_POST["modal_check_f"])) {
	$modal_check_f=$_POST["modal_check_f"];	
}else{
	$modal_check_f=false;
}
if (isset($_POST["modal_check_sf"])) {
	$modal_check_sf=$_POST["modal_check_sf"];
}else{
	$modal_check_sf=false;
}

if($modal_check_sf){
	$modal_check_sf_x=1;
}else{
	$modal_check_sf_x=0;
}
if($modal_check_f){
	$modal_check_f_x=1;
}else{
	$modal_check_f_x=0;
}

$stmtSF = $dbh->prepare("UPDATE configuraciones set valor_configuracion='$modal_check_sf_x' where id_configuracion=76");//VARIABLE CONFIGURACION PARA ACTIVAR EDIT FORMA DE PAGO SF
$flagSuccess=$stmtSF->execute();
$stmtF = $dbh->prepare("UPDATE configuraciones set valor_configuracion='$modal_check_f_x' where id_configuracion=77");//VARIABLE CONFIGURACION PARA ACTIVAR EDIT RS FACTURAS
$flagSuccess=$stmtF->execute();

showAlertSuccessError($flagSuccess,$urlRedirect);	

?>
