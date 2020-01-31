<?php
require_once '../layouts/bodylogin.php';
require_once '../conexion.php';
require_once '../functions.php';
require_once '../functionsGeneral.php';
require_once 'configModule.php';

$dbh = new Conexion();

//RECIBIMOS LAS VARIABLES
$codigo=$_POST['codigo'];
$nombre=$_POST['nombre'];
/*if (isset($_POST['ibnorca_check']) && $_POST['ibnorca_check'] == 1){
	$ibnorca=$_POST['ibnorca_check'];
}else{
	$ibnorca=2;
}*/
$ibnorca=1;
// Prepare
$stmt = $dbh->prepare("UPDATE $table set nombre='$nombre',ibnorca='$ibnorca' where codigo=:codigo");
// Bind
$stmt->bindParam(':codigo', $codigo);
$flagSuccess=$stmt->execute();
showAlertSuccessError($flagSuccess,"../".$urlList);	

?>