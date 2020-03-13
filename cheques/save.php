<?php

require_once '../layouts/bodylogin.php';
require_once '../conexion.php';
require_once '../functions.php';
require_once '../functionsGeneral.php';
require_once 'configModule.php';

$dbh = new Conexion();
$inicio=$_POST["inicio"];
$final=$_POST["final"];
$serie=$_POST["serie"];
$cheque=$_POST["cheque"];

$banco=$_POST["banco"];
$codEstadoReferencial="1";

// Prepare
$stmt = $dbh->prepare("INSERT INTO $table (cod_banco,nro_inicio,nro_final,nro_cheque,nro_serie,cod_estadoreferencial) 
	VALUES ( :banco,:inicio,:final,:cheque,:serie,:estado)");
// Bind
$stmt->bindParam(':inicio', $inicio);
$stmt->bindParam(':final', $final);
$stmt->bindParam(':serie', $serie);
$stmt->bindParam(':cheque', $cheque);
$stmt->bindParam(':banco', $banco);
$stmt->bindParam(':estado', $codEstadoReferencial);
$flagSuccess=$stmt->execute();
if($flagSuccess==true){
	showAlertSuccessError(true,"../".$urlList);	
}else{
	showAlertSuccessError(false,"../".$urlList);
}
?>