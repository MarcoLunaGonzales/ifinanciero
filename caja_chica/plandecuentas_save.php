<?php

require_once '../layouts/bodylogin.php';
require_once '../conexion.php';
require_once '../functions.php';
require_once '../functionsGeneral.php';
require_once 'configModule.php';

$dbh = new Conexion();

session_start();

//$cuentasX=$_POST["cuentas"];
$cuentasX=json_decode($_POST["cuentas2"]);

// $cuentasX=$_POST["cuentas"];
// $codPartida=$_POST["cod_partida"];

$stmtDel = $dbh->prepare("DELETE FROM plan_cuentas_cajachica ");
$stmtDel->execute();
$flagSuccessDetail=true;


for ($i=0;$i<count($cuentasX);$i++){
	$numero_x=$cuentasX[$i]->numero;
	$cod_cuenta=buscarCuentaAnterior($numero_x);
	 // echo $cod_cuenta."<br>";
	$stmt = $dbh->prepare("INSERT INTO plan_cuentas_cajachica(cod_cuenta) VALUES (:cod_cuenta)");
	$stmt->bindParam(':cod_cuenta', $cod_cuenta);
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
