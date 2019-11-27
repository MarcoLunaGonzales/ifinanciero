<?php
session_start();
require_once '../conexion.php';
require_once '../functionsGeneral.php';
require_once '../functions.php';
require_once '../styles.php';

$dbh = new Conexion();

$sqlX="SET NAMES 'utf8'";
$stmtX = $dbh->prepare($sqlX);
$stmtX->execute();


$globalAdmin=$_SESSION["globalAdmin"];
$globalGestion=$_SESSION["globalGestion"];
$globalUnidad=$_SESSION["globalUnidad"];
$globalArea=$_SESSION["globalArea"];

$idp=$_GET['idp'];
$unidad=$_GET['unidad'];
$area=$_GET['area'];
$anio=date("Y");
$anio_pasado=((int)$anio)-1;
 
$monto = calcularCostosPresupuestarios($idp,$unidad,$area,$anio_pasado);
echo trim($monto);
?>


