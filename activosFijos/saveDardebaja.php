<?php

require_once '../conexion.php';
require_once '../functions.php';
// require_once 'activosFijos/configModule.php';

$dbh = new Conexion();
//RECIBIMOS LAS VARIABLES
session_start();
$globalUser=$_SESSION["globalUser"];
$modified_at=date('Y-m-d h:m:s');
$modified_by=$globalUser;

$codigo=$_POST['codigo'];
$obs=$_POST['obs'];
$fecha_baja=$_POST['fecha_baja'];
// Prepare
$stmt = $dbh->prepare("UPDATE activosfijos set cod_estadoactivofijo=3,modified_at=now(),modified_by='$modified_by',fecha_baja='$fecha_baja',obs_baja='$obs'  where codigo=:codigo");
// Bind
$stmt->bindParam(':codigo', $codigo);
$flagSuccess=$stmt->execute();
if($flagSuccess){
	echo "1";
}else{
	echo "2";
}
//showAlertSuccessError($flagSuccess,$urlList6);

?>