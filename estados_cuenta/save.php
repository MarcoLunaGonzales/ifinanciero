<?php

require_once '../layouts/bodylogin.php';
require_once '../conexion.php';
require_once '../functions.php';
require_once '../functionsGeneral.php';
require_once 'configModule.php';

$dbh = new Conexion();
$cuenta=$_POST["cuenta_auto_id"];
$tipo=$_POST["credito"];

// Prepare
$stmt = $dbh->prepare("INSERT INTO $table (cod_plancuenta,tipo) VALUES ( :cod_plancuenta,:tipo)");
// Bind
$stmt->bindParam(':cod_plancuenta', $cuenta);
$stmt->bindParam(':tipo', $tipo);
$flagSuccess=$stmt->execute();
if($flagSuccess==true){
	showAlertSuccessError(true,"../".$urlList2);	
}else{
	showAlertSuccessError(false,"../".$urlList2);
}
?>
