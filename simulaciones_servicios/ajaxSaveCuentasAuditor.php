<?php
session_start();
require_once '../conexion.php';
require_once '../functions.php';
require_once '../functionsGeneral.php';
require_once 'configModule.php';

$dbh = new Conexion();


$simulaciones=$_GET["simulaciones"];
$codDet=$_GET["cod_detalle"];
$tipo=$_GET["cod_tipoau"];
$monto=$_GET["monto"];
$montoe=$_GET["montoe"];
$extlocal=$_GET["extlocal"];
$dias=$_GET["dias"];
$cantidad=$_GET["cantidad"];

$sqlUpdateDetalle="UPDATE simulaciones_ssd_ssa SET  monto='$monto',monto_externo='$montoe',dias='$dias',cantidad=$cantidad where cod_simulacionservicio=$simulaciones and cod_simulacionserviciodetalle='$codDet' and cod_simulacionservicioauditor='$tipo'";
$stmtUpdateDetalle = $dbh->prepare($sqlUpdateDetalle);
$stmtUpdateDetalle->execute();

?>