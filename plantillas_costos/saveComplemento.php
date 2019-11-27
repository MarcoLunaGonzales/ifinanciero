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
$precioIbnorca=$_POST["precio_venta_ibnorca"];
$precioFuera=$_POST["precio_venta_fuera"];
$cantAlumnos=$_POST["cantidad_alumnos"];

$sqlUpdate="UPDATE plantillas_costo SET  precio_ventalocal=$precioIbnorca, precio_ventaexterno=$precioFuera,cantidad_alumnos=$cantAlumnos where codigo=$codPlantillaCosto";
echo $sqlUpdate;
$stmtUpdate = $dbh->prepare($sqlUpdate);
$flagSuccess=$stmtUpdate->execute();

if($flagSuccess==true){
	showAlertSuccessError(true,"../".$urlList);	
}else{
	showAlertSuccessError(false,"../".$urlList);
}


?>
