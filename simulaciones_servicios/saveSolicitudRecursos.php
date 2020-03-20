<?php
session_start();
require_once '../layouts/bodylogin2.php';
require_once '../conexion.php';
require_once '../styles.php';
require_once '../functions.php';
require_once '../functionsGeneral.php';
require_once 'configModule.php';


setlocale(LC_TIME, "Spanish");
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

$dbh = new Conexion();

$anteriorCod=obtenerCodigoSolicitudRecursosSimulacion(2,$_GET['cod']);
if($anteriorCod==0){
$unidad=$_SESSION['globalUnidad'];
$sql="SELECT IFNULL(max(c.codigo)+1,1)as codigo from solicitud_recursos c where c.cod_unidadorganizacional=$unidad";
$stmt = $dbh->prepare($sql);
$stmt->execute();
$numero=0;
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
  $numero=$row['codigo'];
}

$codProv=0;
$codSim=0;
$codSimServ=$_GET['cod'];


$codCont=0;//CODIGO DE CONTRATO
  $fecha= date("Y-m-d h:m:s");
  $codSolicitud=obtenerCodigoSolicitudRecursos();
  $dbh = new Conexion();
  $sqlInsert="INSERT INTO solicitud_recursos (codigo, cod_personal,cod_unidadorganizacional,cod_area,fecha,numero,cod_simulacion,cod_proveedor,cod_simulacionservicio,cod_contrato) 
  VALUES ('".$codSolicitud."','".$globalUser."','".$globalUnidad."', '".$globalArea."', '".$fecha."','".$numero."','".$codSim."','".$codProv."','".$codSimServ."','".$codCont."')";
  $stmtInsert = $dbh->prepare($sqlInsert);
  $stmtInsert->execute();

}else{
  $codSolicitud=$anteriorCod;	
}
if(isset($_GET['q'])){
  $q=$_GET['q'];
  ?>
  <script>window.location.href="../solicitudes/registerSolicitud.php?cod="+<?=$codSolicitud?>+"&q="+<?=$q?></script>
  <?php
}else{
  ?>
  <script>window.location.href="../solicitudes/registerSolicitud.php?cod="+<?=$codSolicitud?></script>
  <?php
}
?>
