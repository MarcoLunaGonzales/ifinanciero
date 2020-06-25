<?php

require_once '../layouts/bodylogin.php';
require_once '../conexion.php';
require_once '../functions.php';
require_once '../functionsGeneral.php';
require_once 'configModule.php';

$dbh = new Conexion();
$codInstancia=$_POST["instancia"];
$codPersonal=explode("$$$", $_POST["personal"])[0];
$correoAlternativo=$_POST["correo_otro"];
$correo=$_POST["correo"];
$codEstadoReferencial="1";

// Prepare
$stmt = $dbh->prepare("INSERT INTO $table (cod_instancia_envio,cod_personal,correo_alternativo,cod_estadoreferencial) 
	VALUES ( $codInstancia,$codPersonal,'$correoAlternativo',$codEstadoReferencial)");
$flagSuccess=$stmt->execute();

$stmt = $dbh->prepare("UPDATE personal SET email_empresa='$correo' where codigo=$codPersonal");
$flagSuccess=$stmt->execute();

if($flagSuccess==true){
	showAlertSuccessError(true,"../".$urlList);	
}else{
	showAlertSuccessError(false,"../".$urlList);
}
?>