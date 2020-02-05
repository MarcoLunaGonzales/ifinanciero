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


if(isset($_GET['cod_plantillacosto'])){
	$codigo=$_GET['cod_plantillacosto'];
  $codPartida=$_GET['cod_partida'];
  $detalle=$_GET['detalle'];
  $monto=$_GET['monto'];
  $cuenta=$_GET['cuenta'];
  $tipo=$_GET['tipo'];
  
  if($tipo==3){
  	$alumno=$_GET['monto_al'];
  }else{
  	$alumno=0;
  }
  $dbh = new Conexion();
  $sqlInsert="INSERT INTO plantillas_servicios_detalle (cod_plantillacosto, cod_partidapresupuestaria, cod_cuenta,glosa,monto_unitario,cantidad,monto_total,cod_estadoreferencial,editado_alumno) VALUES ('".$codigo."','".$codPartida."','".$cuenta."', '".$detalle."','".$monto."','1','".$monto."',1,'".$alumno."')";
  $stmtInsert = $dbh->prepare($sqlInsert);
  $stmtInsert->execute();

}

?>
