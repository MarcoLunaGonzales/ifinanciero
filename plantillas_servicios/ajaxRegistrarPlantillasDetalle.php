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
  $montoe=$_GET['montoe'];
  $cuenta=$_GET['cuenta'];
  $tipo=$_GET['tipo'];
  
  if($tipo==3){
  	$alumno=$_GET['monto_al'];
    $alumnoe=$_GET['monto_ale'];
  }else{
  	$alumno=0;
    $alumnoe=0;
  }
  $dbh = new Conexion();
  $sqlInsert="INSERT INTO plantillas_servicios_detalle (cod_plantillatcp, cod_partidapresupuestaria, cod_cuenta,glosa,monto_unitario,cantidad,monto_total,cod_estadoreferencial,editado_alumno,tipo_registro,editado_alumnoext,monto_totalext,cod_externolocal) 
  VALUES ('".$codigo."','".$codPartida."','".$cuenta."', '".$detalle."','".$monto."','1','".$monto."',1,'".$alumno."','".$tipo."','".$alumnoe."','".$montoe."',1)";
  $stmtInsert = $dbh->prepare($sqlInsert);
  $stmtInsert->execute();

}

?>
