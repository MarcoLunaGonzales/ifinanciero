<?php

require_once '../layouts/bodylogin.php';
require_once '../conexion.php';
require_once '../functions.php';
require_once '../functionsGeneral.php';
require_once 'configModule.php';

$dbh = new Conexion();

$cantidadFilas=$_POST["cantidad_filas"];
$detalles= json_decode($_POST['detalles']);
session_start();

$globalUser=$_SESSION["globalUser"];
$globalGestion=$_SESSION["globalGestion"];
$globalUnidad=$_SESSION["globalUnidad"];
$globalArea=$_SESSION["globalArea"];
$globalAdmin=$_SESSION["globalAdmin"];

$fechaHoraActual=date("Y-m-d H:i:s");

$codPlantillaCosto=$_POST["codigo_plantilla"];
$cantAlumnos=$_POST["cantidad_alumnos"];
$cantAlumnosExterno=$_POST["cantidad_alumnos_fuera"];

$sqlUpdate="UPDATE plantillas_costo SET cantidad_alumnoslocal=$cantAlumnos,cantidad_alumnosexterno=$cantAlumnosExterno where codigo=$codPlantillaCosto";
echo $sqlUpdate;
$stmtUpdate = $dbh->prepare($sqlUpdate);
$flagSuccess=$stmtUpdate->execute();

if($flagSuccess==true){
	showAlertSuccessError(true,"../".$urlList);	
}else{
	showAlertSuccessError(false,"../".$urlList);
}


?>
