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
  $local=$_GET['local'];
  $externo=$_GET['externo'];
  $dbh = new Conexion();
  $sqlInsert="INSERT INTO precios_plantillacosto (venta_local, venta_externo, cod_plantillacosto) VALUES ('".$local."','".$externo."', '".$codigo."')";
  $stmtInsert = $dbh->prepare($sqlInsert);
  $stmtInsert->execute();
  include "ajaxListPrecio.php";
}

?>
