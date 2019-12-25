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
	$cod=$_GET['cod'];
  $codigo=$_GET['codigo'];
  $dbh = new Conexion();
  $sqlInsert="DELETE FROM precios_plantillacosto WHERE codigo=$cod";
  $stmtInsert = $dbh->prepare($sqlInsert);
  $stmtInsert->execute();
  include "ajaxListPrecio.php";
}

?>
