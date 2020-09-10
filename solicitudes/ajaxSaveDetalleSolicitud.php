<?php
session_start();
require_once '../conexion.php';
require_once '../functions.php';

$dbh = new Conexion();
$sqlX="SET NAMES 'utf8'";
$stmtX = $dbh->prepare($sqlX);
$stmtX->execute();

$codigo=$_POST['codigo'];
$detalles=json_decode($_POST['detalles']);

foreach ($detalles as $datos) {
  $codDetalle=$datos->codigo;
  $codActividad=$datos->cod_actividad;
  $codAccNum=$datos->cod_accnum;
  $sqlDetalle="UPDATE solicitud_recursosdetalle set cod_actividadproyecto='$codActividad',acc_num='$codAccNum' where codigo=$codDetalle";
  $stmt = $dbh->prepare($sqlDetalle);
  $stmt->execute();
}

