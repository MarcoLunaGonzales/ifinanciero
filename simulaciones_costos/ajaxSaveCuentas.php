<?php
require_once '../conexion.php';
require_once '../functions.php';
require_once '../functionsGeneral.php';
require_once 'configModule.php';

$dbh = new Conexion();

$codigo=$_GET["codigo"];
$monto=$_GET["monto"];
$ibnorca=$_GET["ibnorca"];
session_start();
if($ibnorca==1){
  $sqlUpdate="UPDATE cuentas_simulacion SET  monto_local='$monto' where codigo=$codigo";	
}else{
  $sqlUpdate="UPDATE cuentas_simulacion SET  monto_externo='$monto' where codigo=$codigo";
}
$stmtUpdate = $dbh->prepare($sqlUpdate);
$flagSuccess=$stmtUpdate->execute();

?>