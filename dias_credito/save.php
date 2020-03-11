<?php

require_once '../layouts/bodylogin.php';
require_once '../conexion.php';
require_once '../functions.php';
require_once '../functionsGeneral.php';
require_once 'configModule.php';

$dbh = new Conexion();
$dias=$_POST["dias"];
$proveedor=$_POST["proveedor"];
$codEstadoReferencial="1";

// Prepare
$stmt = $dbh->prepare("INSERT INTO $table (cod_proveedor,cantidad_dias,cod_estadoreferencial) 
	VALUES ( :proveedor,:dias,:estado)");
// Bind
$stmt->bindParam(':dias', $dias);
$stmt->bindParam(':proveedor', $proveedor);
$stmt->bindParam(':estado', $codEstadoReferencial);
$flagSuccess=$stmt->execute();
if($flagSuccess==true){
	showAlertSuccessError(true,"../".$urlList);	
}else{
	showAlertSuccessError(false,"../".$urlList);
}
?>