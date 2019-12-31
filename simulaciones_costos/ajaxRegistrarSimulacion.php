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
  //seleccionar las partidas variables con montos_ibnorca y fuera
  $partidasPlan=obtenerPartidasPlantillaCostos($plantilla_costo,2);
  while ($rowPartida = $partidasPlan->fetch(PDO::FETCH_ASSOC)) {
     $idp=$rowPartida['cod_partidapresupuestaria'];
     $unidad=$rowPartida['cod_unidadorganizacional'];
     $area=$rowPartida['cod_area'];
     $tipoCalculo=$rowPartida['tipo_calculo'];
     $montoLocal=$rowPartida['monto_local'];
     $montoExterno=$rowPartida['monto_externo'];
     $montoTotal = calcularCostosPresupuestarios($idp,$unidad,$area,$anio_pasado);
     if($montoTotal==0){
      $montoTotal=1;
     }
     $cuentasPlan=obtenerCuentaPlantillaCostos($idp);
      while ($rowCuenta = $cuentasPlan->fetch(PDO::FETCH_ASSOC)) {
      $codCuenta=$rowCuenta['cod_cuenta'];
      $numero=trim($rowCuenta['numero']);
      //sacamos el porcentaje 
      $montoCuenta=trim(ejecutadoEgresosMes($unidad,$anio_pasado, $mes, $area, 1, $numero));
      $montoCuenta=($montoCuenta/12)/obtenerValorConfiguracion(6);
      $porcentaje=((float)$montoCuenta*100)/(float)$montoTotal;
      //ingresamos valores segun porcentaje al total de partida
      $montoIbnorca=($porcentaje*$montoLocal)/100;
      $montoFuera=($porcentaje*$montoExterno)/100;

      $sqlInsertPorcentaje="INSERT INTO cuentas_simulacion (cod_plancuenta, monto_local, monto_externo, porcentaje,cod_partidapresupuestaria,cod_simulacioncostos) 
      VALUES ('".$codCuenta."','".$montoIbnorca."','".$montoFuera."', '".$porcentaje."', '".$idp."','".$codSimCosto."')";
      $stmtInsertPorcentaje = $dbh->prepare($sqlInsertPorcentaje);
      $stmtInsertPorcentaje->execute();
     }
  }
  
  echo $codSimCosto;
}

?>
