<?php
session_start();
require_once '../conexion.php';
require_once '../styles.php';
require_once '../functionsGeneral.php';
require_once '../functions.php';
require_once 'configModule.php';

$dbh = new Conexion();

$sqlX="SET NAMES 'utf8'";
$stmtX = $dbh->prepare($sqlX);
$stmtX->execute();

if(isset($_GET['codigo'])){
  $codigo=$_GET['codigo'];
  $nombre=$_GET['nombre'];
  $porcentajeOrigen=$_GET["cuenta_origen"];
  $sqlDelete="UPDATE configuracion_retenciones SET nombre='$nombre',porcentaje_cuentaorigen='$porcentajeOrigen' where codigo=$codigo";
  $stmtDelete = $dbh->prepare($sqlDelete);
  $stmtDelete->execute();
}
?>