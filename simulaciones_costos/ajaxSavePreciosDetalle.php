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
set_time_limit(300);

$codigo=$_GET['codigo'];
$datos=json_decode($_GET['datos']);

$sqlDelete="DELETE FROM precios_simulacioncostodetalle where cod_preciosimulacion=$codigo";
$stmtDelete = $dbh->prepare($sqlDelete);
$stmtDelete->execute();

for ($i=0; $i <count($datos) ; $i++) { 
    $cantidad=$datos[$i]->cantidad;
    $porcentaje=$datos[$i]->porcentaje;
    $monto=$datos[$i]->monto;
    $sqlUpdate="INSERT INTO precios_simulacioncostodetalle (cod_preciosimulacion,cantidad,porcentaje,monto) VALUES($codigo,$cantidad,$porcentaje,$monto)";
    $stmtUpdate = $dbh->prepare($sqlUpdate);
    $flagSuccess=$stmtUpdate->execute();  
}

