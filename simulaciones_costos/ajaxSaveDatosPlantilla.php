<?php
require_once '../conexion.php';
require_once '../functions.php';
require_once '../functionsGeneral.php';
require_once 'configModule.php';

$dbh = new Conexion();

$codigo=$_GET["codigo"];
session_start();

$codPlantillaCosto=$_GET["codigo"];
$codSimulacion=$_GET["cod_sim"];
$ut_i=$_GET['ut_i'];
$ut_f=$_GET['ut_f'];
$al_i=$_GET['al_i'];
$al_f=$_GET['al_f'];
$precio_p=$_GET['precio_p'];

$sqlUpdate="UPDATE plantillas_costo SET  utilidad_minimalocal='$ut_i',utilidad_minimaexterno='$ut_f',cantidad_alumnoslocal='$al_i',cantidad_alumnosexterno='$al_f' where codigo=$codPlantillaCosto";
$stmtUpdate = $dbh->prepare($sqlUpdate);
$flagSuccess=$stmtUpdate->execute();

$sqlUpdatePlantilla="UPDATE simulaciones_costos SET  cod_precioplantilla='$precio_p' where codigo=$codSimulacion";
$stmtUpdatePlantilla = $dbh->prepare($sqlUpdatePlantilla);
$stmtUpdatePlantilla->execute();

$precios=obtenerPreciosPorCodigo($precio_p);
echo $precios[0]."$$$".$precios[1];
?>
