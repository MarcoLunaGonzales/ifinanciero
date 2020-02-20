<?php

require_once '../layouts/bodylogin.php';
require_once '../conexion.php';
require_once '../functions.php';
require_once '../functionsGeneral.php';
require_once 'configModule.php';

$dbh = new Conexion();

session_start();

//$cuentasX=$_POST["cuentas"];
$cuentasX=json_decode($_POST["cuentas2"]);;
$codPartida=$_POST["cod_partida"];

$stmtDel = $dbh->prepare("DELETE FROM plan_cuentas_cajachica ");
$stmtDel->execute();
$flagSuccessDetail=true;
for ($i=0;$i<count($cuentasX);$i++){ 	    
	$stmt = $dbh->prepare("INSERT INTO plan_cuentas_cajachica(cod_cuenta) VALUES (:cod_cuenta)");
	$stmt->bindParam(':cod_cuenta', $cuentasX[$i]);
	$flagSuccess2=$stmt->execute();
	if($flagSuccess2==false){
		$flagSuccessDetail=false;
	}
}

if($flagSuccessDetail==true){
	showAlertSuccessError(true,$urlListCC);	
}else{
	showAlertSuccessError(false,$urlListCC);
}
?>
