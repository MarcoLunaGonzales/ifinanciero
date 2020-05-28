<?php
require_once '../conexion.php';
require_once '../functions.php';
require_once '../functionsGeneral.php';
require_once 'configModule.php';

$dbh = new Conexion();

$codigo=$_GET["codigo"];
session_start();
$nombreSimu=$_GET['nombre'];
$festimada=explode("/", $_GET['fecha_curso']);
$fecha_estimada=$festimada[2]."-".$festimada[1]."-".$festimada[0];

$ibnorca=$_GET['ibnorca'];
$sqlUpdate="UPDATE simulaciones_costos SET  nombre='$nombreSimu',fecha_curso='$fecha_estimada',ibnorca='$ibnorca' where codigo=$codigo";
$stmtUpdate = $dbh->prepare($sqlUpdate);
$flagSuccess=$stmtUpdate->execute();
echo $flagSuccess;
?>
