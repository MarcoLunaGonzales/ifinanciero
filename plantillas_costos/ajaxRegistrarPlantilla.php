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
  $abrev=$_GET['abrev'];
  $unidad=$_GET['unidad'];
  $area=$_GET['area'];
  $utilidadLocal=$_GET['utilidad_local'];
  $utilidadExterno=1;
  $alumnosLocal=$_GET['alumnos_local'];
  $alumnosExterno=1;
  $precioLocal=$_GET['precio_local'];
  $precioExterno=1;
  $codPlanCosto=obtenerCodigoPlanCosto();
  $dbh = new Conexion();
  $sqlInsert="INSERT INTO plantillas_costo (codigo, nombre, abreviatura, cod_unidadorganizacional, cod_area,utilidad_minimalocal,utilidad_minimaexterno,cantidad_alumnoslocal,cantidad_alumnosexterno) 
  VALUES ('".$codPlanCosto."','".$nombre."','".$abrev."', '".$unidad."', '".$area."','".$utilidadLocal."','".$utilidadExterno."','".$alumnosLocal."','".$alumnosExterno."')";
  $stmtInsert = $dbh->prepare($sqlInsert);
  $stmtInsert->execute();

  $dbh2 = new Conexion();
  $sqlInsert2="INSERT INTO precios_plantillacosto (venta_local, venta_externo, cod_plantillacosto) VALUES ('".$precioLocal."','".$precioExterno."', '".$codPlanCosto."')";
  $stmtInsert2 = $dbh2->prepare($sqlInsert2);
  $stmtInsert2->execute();
  echo $codPlanCosto;
}

?>
