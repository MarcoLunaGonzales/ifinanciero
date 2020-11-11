<?php

require_once '../layouts/bodylogin.php';
require_once '../conexion.php';
require_once '../functions.php';
require_once '../functionsGeneral.php';
require_once 'configModule.php';

$dbh = new Conexion();

session_start();

$flujo=$_POST["flujo_grupo"];
$cuentasX=json_decode($_POST["cuentas2"]);
$flagSuccessDetail=false;
if(obtenerNombreFlujoEfectivoGrupo($flujo)!=""){
 $flagSuccessDetail=true;

 $stmtDel = $dbh->prepare("DELETE FROM flujo_efectivo_gruposcuentas where cod_flujoefectivogrupo=$flujo");
 $stmtDel->execute();

 for ($i=0;$i<count($cuentasX);$i++){ 	    
	 echo $cuentasX[$i]->codigo."<br>";
	$stmt = $dbh->prepare("INSERT INTO flujo_efectivo_gruposcuentas(cod_plancuenta,cod_flujoefectivogrupo) VALUES (:cod_plancuenta,:cod_flujo)");
	$stmt->bindParam(':cod_plancuenta', buscarCuentaAnterior($cuentasX[$i]->numero));	
    $stmt->bindParam(':cod_flujo', $flujo);

	$flagSuccess2=$stmt->execute();
	if($flagSuccess2==false){
		$flagSuccessDetail=false;
	}
  }
}

if($flagSuccessDetail==true){
	showAlertSuccessError(true,"../".$urlListCC2);	
}else{
    showAlertSuccessError(false,"../".$urlListCC2);
}
?>
