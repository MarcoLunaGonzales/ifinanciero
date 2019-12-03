<?php

require_once '../layouts/bodylogin.php';
require_once '../conexion.php';
require_once '../functions.php';
require_once '../functionsGeneral.php';
require_once 'configModule.php';

$dbh = new Conexion();

$nombre=$_POST["nombre"];
$codEstado="1";

// Prepare
$stmt = $dbh->prepare("INSERT INTO $table (nombre, cod_estadoreferencial) VALUES (:nombre, :cod_estado)");
// Bind
$stmt->bindParam(':nombre', $nombre);
$stmt->bindParam(':cod_estado', $codEstado);
$flagSuccess=$stmt->execute();
if($flagSuccess==true){
	showAlertSuccessError(true,"../".$urlList2);	
}else{
	showAlertSuccessError(false,"../".$urlList2);
}
?>
