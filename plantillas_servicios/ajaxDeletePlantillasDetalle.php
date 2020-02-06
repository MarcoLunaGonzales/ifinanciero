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


if(isset($_GET['cod'])){
	$cod=$_GET['cod'];
  $dbh = new Conexion();
  $sqlInsert="DELETE FROM plantillas_servicios_detalle WHERE codigo=$cod";
  $stmtInsert = $dbh->prepare($sqlInsert);
  $stmtInsert->execute();
}

?>