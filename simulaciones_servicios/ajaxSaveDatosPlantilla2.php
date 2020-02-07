<?php
require_once '../conexion.php';
require_once '../functions.php';
require_once '../functionsGeneral.php';
require_once 'configModule.php';

$dbh = new Conexion();

$codigo=$_GET["codigo"];
session_start();

$codPlantillaCosto=$_GET["plantilla"];
$codSimulacion=$_GET["simulacion"];
$ut_i=$_GET['utilidad'];
$dia=$_GET['dia'];


$monto=$_GET['monto'];
$cantidad=$_GET['cantidad'];
$habilitado=$_GET['habilitado'];

$sqlUpdatePlantilla="UPDATE simulaciones_servicios SET  utilidad_minima='$ut_i',dias_auditoria='$dia' where codigo=$codSimulacion";
$stmtUpdatePlantilla = $dbh->prepare($sqlUpdatePlantilla);
$stmtUpdatePlantilla->execute();


$sqlDetalles="UPDATE simulaciones_servicios_tiposervicio SET cantidad_editado=$cantidad,monto=$monto,habilitado=$habilitado where codigo=$codigo";
$stmtDetalles = $dbh->prepare($sqlDetalles);
$stmtDetalles->execute();

echo "OK";
?>
