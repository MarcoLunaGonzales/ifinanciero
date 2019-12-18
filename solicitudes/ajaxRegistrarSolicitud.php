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

$globalNombreGestion=$_SESSION["globalNombreGestion"];
$globalUser=$_SESSION["globalUser"];
$globalGestion=$_SESSION["globalGestion"];
$globalUnidad=$_SESSION["globalUnidad"];
$globalNombreUnidad=$_SESSION['globalNombreUnidad'];
$globalArea=$_SESSION["globalArea"];
$globalAdmin=$_SESSION["globalAdmin"];

if(isset($_GET['numero'])){
	$numero=$_GET['numero'];
  $codSim=$_GET['cod_sim'];
  $codProv=$_GET['cod_prov'];
  $codCont=0;//CODIGO DE CONTRATO
  $fecha= date("Y-m-d h:m:s");
  $codSolicitud=obtenerCodigoSolicitudRecursos();
  $dbh = new Conexion();
  $sqlInsert="INSERT INTO solicitud_recursos (codigo, cod_personal,cod_unidadorganizacional,cod_area,fecha,numero,cod_simulacion,cod_proveedor,cod_contrato) 
  VALUES ('".$codSolicitud."','".$globalUser."','".$globalUnidad."', '".$globalArea."', '".$fecha."','".$numero."','".$codSim."','".$codProv."','".$codCont."')";
  $stmtInsert = $dbh->prepare($sqlInsert);
  $stmtInsert->execute();

  ?>
  <script>window.location.href="../solicitudes/registerSolicitud.php?cod="+<?=$codSolicitud?></script>
  <?php
  echo "Registro Satisfactorio";
}

?>
