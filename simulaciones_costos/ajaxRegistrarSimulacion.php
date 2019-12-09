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

if(isset($_GET['nombre'])){
	$nombre=$_GET['nombre'];
  $plantilla_costo=$_GET['plantilla_costo'];
  $codPrecio=$_GET['precio'];
  $ibnorca=$_GET['ibnorca'];
  $fecha= date("Y-m-d");
  $codSimCosto=obtenerCodigoSimCosto();
  $dbh = new Conexion();
  $sqlInsert="INSERT INTO simulaciones_costos (codigo, nombre, fecha, cod_plantillacosto, cod_responsable,cod_precioplantilla,ibnorca) VALUES ('".$codSimCosto."','".$nombre."','".$fecha."', '".$plantilla_costo."', '".$globalUser."','".$codPrecio."','".$ibnorca."')";
  $stmtInsert = $dbh->prepare($sqlInsert);
  $stmtInsert->execute();

  ?>
  <script>window.location.href="../simulaciones_costos/registerSimulacion.php?cod="+<?=$codSimCosto?></script>
  <?php
  echo "Registro Satisfactorio";
}

?>
