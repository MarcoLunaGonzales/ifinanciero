<?php

require_once '../layouts/bodylogin.php';
require_once '../conexion.php';
require_once '../functions.php';
require_once '../functionsGeneral.php';
require_once 'configModule.php';

$dbh = new Conexion();

$codigo=$_POST["codigo"];
$cuenta=$_POST["cuenta_auto_id"];
$tipo=$_POST["tipo"];
$credito=$_POST["credito"];

$porciones = explode("$$", $cuenta);
if($porciones[1]=="AUX"){
	$codPlan="";
	$codAux=$porciones[0];
}else{
	$codPlan=$porciones[0];
	$codAux="";
}
// Prepare
$stmt = $dbh->prepare("UPDATE $table SET tipo='$credito', cod_tipoestadocuenta='$tipo' WHERE codigo='$codigo'");
// Bind
$flagSuccess=$stmt->execute();


if($flagSuccess==true){
	showAlertSuccessError(true,"../".$urlList2);	
}else{
	showAlertSuccessError(false,"../".$urlList2);
}
?>
