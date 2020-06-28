<?php
session_start();
require_once '../conexion.php';

require_once 'configModule.php';

$dbh = new Conexion();

$sqlX="SET NAMES 'utf8'";
$stmtX = $dbh->prepare($sqlX);
$stmtX->execute();

$codigo=$_GET['codigo'];
  
  $sqlInsert="UPDATE simulaciones_servicios SET estado_registro=1 where codigo=$codigo";
  $stmtInsert = $dbh->prepare($sqlInsert);
  $flagsuccess=$stmtInsert->execute();

if($flagsuccess==true){
  echo "<label class='text-success'>Propuesta Registrada</label>";
}else{
  echo "<label class='text-danger'>No se actualizo la propuesta</label>";
}
