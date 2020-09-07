<?php
require_once '../conexion.php';
require_once '../functions.php';
require_once '../styles.php';

$dbh = new Conexion();

$sqlX="SET NAMES 'utf8'";
$stmtX = $dbh->prepare($sqlX);
$stmtX->execute();

session_start();
$globalAdmin=$_SESSION["globalAdmin"];
$globalGestion=$_SESSION["globalGestion"];
$globalUnidad=$_SESSION["globalUnidad"];
$globalArea=$_SESSION["globalArea"];

$fechaActual=date("d/m/Y");
$codigo=$_GET['codigo'];

$haberBasico=calcularHaberBasicoPorPersona($codigo);
if($haberBasico==null){
  echo "NN";
}else{
  echo $haberBasico;
}