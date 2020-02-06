<?php
require_once '../conexion.php';
require_once '../functions.php';
require_once '../functionsGeneral.php';
require_once 'configModule.php';

$dbh = new Conexion();

$codigo=$_GET["codigo"];
session_start();
$nombreSimu=$_GET['nombre'];
$ibnorca=1;
$sqlUpdate="UPDATE simulaciones_servicios SET  nombre='$nombreSimu' where codigo=$codigo";
$stmtUpdate = $dbh->prepare($sqlUpdate);
$flagSuccess=$stmtUpdate->execute();
echo $flagSuccess;
?>
