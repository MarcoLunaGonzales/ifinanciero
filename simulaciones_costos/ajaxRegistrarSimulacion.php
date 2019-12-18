<?php
session_start();
require_once '../conexion.php';
require_once '../styles.php';

require_once '../functionsGeneral.php';
require_once '../functions.php';
require_once '../functionsPOSIS.php';
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

  //insertar datos en la tabla cuentas_simulacion
  $anio=date("Y");
  $anio_pasado=((int)$anio)-1;
  $mes=date("m");
  //seleccionar las partidas variables 
  $partidasPlan=obtenerPartidasPlantillaCostos($plantilla_costo,2);
  while ($rowPartida = $partidasPlan->fetch(PDO::FETCH_ASSOC)) {
     $idp=$rowPartida['cod_partidapresupuestaria'];
     $unidad=$rowPartida['cod_unidadorganizacional'];
     $area=$rowPartida['cod_area'];
     $montoTotal = calcularCostosPresupuestarios($idp,$unidad,$area,$anio_pasado);
     $cuentasPlan=obtenerCuentaPlantillaCostos($idp);
      while ($rowCuenta = $cuentasPlan->fetch(PDO::FETCH_ASSOC)) {
      $codCuenta=$rowCuenta['cod_cuenta'];
      $numero=trim($rowCuenta['numero']);
      $montoCuenta=trim(ejecutadoEgresosMes($unidad,$anio_pasado, $mes, $area, 0, $numero));
      $porcentaje=((float)$montoCuenta*100)/(float)$montoTotal;
      $sqlInsertPorcentaje="INSERT INTO cuentas_simulacion (cod_plancuenta, monto_calculado, monto_modificado, porcentaje,cod_partidapresupuestaria,cod_simulacioncostos) 
      VALUES ('".$codCuenta."','".$montoCuenta."','".$montoCuenta."', '".$porcentaje."', '".$idp."','".$codSimCosto."')";
      $stmtInsertPorcentaje = $dbh->prepare($sqlInsertPorcentaje);
      $stmtInsertPorcentaje->execute();
     }
  }
  
  ?>
  <script>window.location.href="../simulaciones_costos/registerSimulacion.php?cod="+<?=$codSimCosto?></script>
  <?php
  echo "Registro Satisfactorio";
}

?>
