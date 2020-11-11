<?php

require_once '../layouts/bodylogin.php';
require_once '../conexion.php';
require_once '../functions.php';
require_once '../functionsGeneral.php';
require_once 'configModule.php';

$dbh = new Conexion();

session_start();

$codigo=$_GET["codigo"];
$flagSuccessDetail=false;
if($codigo!=""||$codigo!=0){
 $stmtDel = $dbh->prepare("DELETE FROM flujo_efectivo_gruposcuentas where codigo=$codigo");
 $flagSuccessDetail=$stmtDel->execute();
}

if($flagSuccessDetail==true){
	showAlertSuccessError(true,"../".$urlListCC2);	
}else{
    showAlertSuccessError(false,"../".$urlListCC2);
}
?>
