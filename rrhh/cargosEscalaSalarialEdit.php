<?php
require_once '../conexion.php';
require_once '../functions.php';

$result=0;
$dbhU = new Conexion();

$sqlX="SET NAMES 'utf8'";
$stmtX = $dbhU->prepare($sqlX);
$stmtX->execute();

//RECIBIMOS LAS VARIABLES
$cod_escala=$_POST['cod_escala'];
$monto=$_POST['monto'];
$sql="UPDATE cargos_escala_salarial set monto=$monto where codigo=$cod_escala";
$stmtU = $dbhU->prepare($sql);
$flagsucces=$stmtU->execute();

if($flagsucces){
      $result =1;
 }
echo $result;
$dbhU=null;

?>
