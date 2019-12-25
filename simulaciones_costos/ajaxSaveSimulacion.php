<?php
require_once '../conexion.php';
require_once '../functions.php';
require_once '../functionsGeneral.php';
require_once 'configModule.php';

$dbh = new Conexion();

$codigo=$_GET["codigo"];
session_start();

$globalUser=$_SESSION["globalUser"];
$globalGestion=$_SESSION["globalGestion"];
$globalUnidad=$_SESSION["globalUnidad"];
$globalArea=$_SESSION["globalArea"];
$globalAdmin=$_SESSION["globalAdmin"];

$fechaHoraActual=date("Y-m-d H:i:s");

$codPlantillaCosto=$_GET["cod_plantilla"];
$nombreSimu=$_GET['nombre'];
$codPrecio=$_GET['precio'];
$alIbnorca=$_GET['alibnorca'];
$alFuera=$_GET['alfuera'];
$sqlUpdate="UPDATE simulaciones_costos SET  nombre='$nombreSimu',cod_plantillacosto='$codPlantillaCosto',cod_precioplantilla='$codPrecio' where codigo=$codigo";
$stmtUpdate = $dbh->prepare($sqlUpdate);
$flagSuccess=$stmtUpdate->execute();

$sqlUpdatePlantilla="UPDATE plantillas_costo SET  cantidad_alumnoslocal='$alIbnorca',cantidad_alumnosexterno='$alFuera' where codigo=$codPlantillaCosto";
$stmtUpdatePlantilla = $dbh->prepare($sqlUpdatePlantilla);
$stmtUpdatePlantilla->execute();

?>
