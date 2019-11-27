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

$stmtDel = $dbh->prepare("DELETE FROM partidaspresupuestarias_cuentas where cod_partidapresupuestaria='$codPartida'");
$stmtDel->execute();
$flagSuccessDetail=true;
for ($i=0;$i<count($cuentasX);$i++){ 	    
	$stmt = $dbh->prepare("INSERT INTO partidaspresupuestarias_cuentas(cod_partidapresupuestaria, cod_cuenta) VALUES (:cod_partida, :cod_cuenta)");
	$stmt->bindParam(':cod_partida', $codPartida);
	$stmt->bindParam(':cod_cuenta', $cuentasX[$i]);

	$flagSuccess2=$stmt->execute();
	if($flagSuccess2==false){
		$flagSuccessDetail=false;
	}
}

if($flagSuccessDetail==true){
	showAlertSuccessError(true,$urlList);	
}else{
	showAlertSuccessError(false,$urlList);
}


?>
