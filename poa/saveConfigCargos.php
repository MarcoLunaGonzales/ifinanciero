<?php

require_once '../layouts/bodylogin.php';
require_once '../conexion.php';
require_once '../functions.php';

$dbh = new Conexion();

$codigoObjetivo=$_POST["cod_objetivo"];
$propiedad_cargo=$_POST["propiedad_cargo"];

$urlRedirect="../index.php?opcion=listPOA";

session_start();

$codigo=$_POST["cod_indicador"];

$flagSuccessDetail=true;

$sqlDel="DELETE FROM indicadores_areascargos where cod_indicador=:codigo_indicador";
$stmtDel = $dbh->prepare($sqlDel);
$stmtDel->bindParam(':codigo_indicador', $codigo);
$stmtDel->execute();

for ($i=0;$i<count($propiedad_cargo);$i++){ 	    
	list($codArea, $codCargo)=explode("|",$propiedad_cargo[$i]);
	$stmt = $dbh->prepare("INSERT INTO indicadores_areascargos (cod_indicador, cod_area, cod_cargo) VALUES (:cod_indicador, :cod_area, :cod_cargo)");
	$stmt->bindParam(':cod_indicador', $codigo);
	$stmt->bindParam(':cod_area', $codArea);
	$stmt->bindParam(':cod_cargo', $codCargo);

	$flagSuccess2=$stmt->execute();
	if($flagSuccess2==false){
		$flagSuccessDetail=false;
	}
}


if($flagSuccessDetail==true){
	showAlertSuccessError(true,$urlRedirect);	
}else{
	showAlertSuccessError(false,$urlRedirect);
}


?>
