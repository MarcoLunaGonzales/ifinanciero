<?php
require_once '../conexion.php';
require_once '../functions.php';
require_once '../functionsGeneral.php';
require_once 'configModule.php';

$dbh = new Conexion();

$codigo=$_GET["codigo"];
session_start();
$nombreSimu=$_GET['nombre'];
$ibnorca=$_GET['ibnorca'];
$sqlUpdate="UPDATE simulaciones_costos SET  nombre='$nombreSimu',ibnorca='$ibnorca' where codigo=$codigo";
$stmtUpdate = $dbh->prepare($sqlUpdate);
$flagSuccess=$stmtUpdate->execute();
echo $flagSuccess;
?>
