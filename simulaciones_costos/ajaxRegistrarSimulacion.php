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
  $cantidad_modulos=$_GET['cantidad_modulos'];
  $codPrecio=$_GET['precio'];
  $ibnorca=1;
  $cantidadAlumnos=obtenerPlantillaCostoAlumnos($plantilla_costo);
  $utilidadMin=obtenerPlantillaCostoUtilidad($plantilla_costo);
  $cantidadCursosMes=obtenerPlantillaCostoCursosMes($plantilla_costo);
  $fecha= date("Y-m-d");
  $codSimCosto=obtenerCodigoSimCosto();
  $dbh = new Conexion();
  $sqlInsert="INSERT INTO simulaciones_costos (codigo, nombre, fecha, cod_plantillacosto, cod_responsable,cod_precioplantilla,ibnorca,cantidad_alumnoslocal,utilidad_minimalocal,cantidad_cursosmes,cantidad_modulos) VALUES ('".$codSimCosto."','".$nombre."','".$fecha."', '".$plantilla_costo."', '".$globalUser."','".$codPrecio."','".$ibnorca."','".$cantidadAlumnos."','".$utilidadMin."','".$cantidadCursosMes."','".$cantidad_modulos."')";
  $stmtInsert = $dbh->prepare($sqlInsert);
  $stmtInsert->execute();

  $dbhD = new Conexion();
  $sqlD="DELETE FROM simulaciones_detalle where cod_simulacioncosto=$codSimCosto";
  $stmtD = $dbhD->prepare($sqlD);
  $stmtD->execute();

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
     /*$montoTotal = calcularCostosPresupuestarios($idp,$unidad,$area,$anio_pasado);
     if($montoTotal==0){
      $montoTotal=1;
     }*/
     

     $cuentasPlan=obtenerMontosCuentasDetallePlantillaCostosPartida($plantilla_costo,$idp);

      while ($rowCuenta = $cuentasPlan->fetch(PDO::FETCH_ASSOC)) {
      $codCuenta=$rowCuenta['cod_cuenta'];
      $numero=trim($rowCuenta['numero']);
      $tipoSim=obtenerValorConfiguracion(13);
      //sacamos el porcentaje 
      /*if($tipoSim==1){
       $montoCuenta=trim(ejecutadoEgresosMes($unidad,$anio_pasado, 12, $area, 1, $numero));
       $montoCuenta=($montoCuenta/12)/obtenerValorConfiguracion(6);  
      }else{
        $montoCuenta=trim(ejecutadoEgresosMes($unidad,$anio_pasado, $mes, $area, 0, $numero));
        $montoCuenta=($montoCuenta)/obtenerValorConfiguracion(6);
      }*/
      $montoCuenta=$rowCuenta['monto'];
      $porcentaje=((float)$montoCuenta*100)/(float)$montoLocal;
      //ingresamos valores segun porcentaje al total de partida
      /*$montoIbnorca=($porcentaje*$montoLocal)/100;
      $montoFuera=($porcentaje*$montoExterno)/100;*/

      $sqlInsertPorcentaje="INSERT INTO cuentas_simulacion (cod_plancuenta, monto_local, monto_externo, porcentaje,cod_partidapresupuestaria,cod_simulacioncostos) 
      VALUES ('".$codCuenta."','".$montoCuenta."','".$montoCuenta."', '".$porcentaje."', '".$idp."','".$codSimCosto."')";
      $stmtInsertPorcentaje = $dbh->prepare($sqlInsertPorcentaje);
      $stmtInsertPorcentaje->execute();
     }
     $detallesPlan=obtenerDetallePlantillaCostosPartida($plantilla_costo,$idp);
     while ($rowDetallesPlan = $detallesPlan->fetch(PDO::FETCH_ASSOC)) {

      $codPC=$rowDetallesPlan['cod_plantillacosto'];
      $codPP=$rowDetallesPlan['cod_partidapresupuestaria'];
      $codC=$rowDetallesPlan['cod_cuenta'];
      $glosaD=$rowDetallesPlan['glosa'];
      $montoD=$rowDetallesPlan['monto_total'];
      $editD=$rowDetallesPlan['editado_alumno'];
      $dbhID = new Conexion();
      $sqlID="INSERT INTO simulaciones_detalle (cod_simulacioncosto,cod_plantillacosto, cod_partidapresupuestaria, cod_cuenta,glosa,monto_unitario,cantidad,monto_total,cod_estadoreferencial,editado_alumno) 
      VALUES ('".$codSimCosto."','".$codPC."','".$codPP."','".$codC."', '".$glosaD."','".$montoD."','1','".$montoD."',1,'".$editD."')";
      $stmtID = $dbhID->prepare($sqlID);
      $stmtID->execute();
     }
  }
   //volcado de datos a la tabla simulaciones_costos

    $preciosPlan=obtenerListaPreciosPlantillaCosto($plantilla_costo,$codPrecio);
     while ($rowPrePlan = $preciosPlan->fetch(PDO::FETCH_ASSOC)) {
      $codCS=$rowPrePlan['codigo'];
      $venCS=$rowPrePlan['venta_local'];
      $veneS=$rowPrePlan['venta_externo'];
      $codPS=$rowPrePlan['cod_plantillacosto'];
      $dbhAU = new Conexion();
      $sqlAU="INSERT INTO precios_simulacioncosto (venta_local, venta_externo,cod_simulacioncosto) 
      VALUES ('".$venCS."','".$veneS."','".$codSimCosto."')";
      $stmtAU = $dbhAU->prepare($sqlAU);
      $stmtAU->execute();

      $porcentajesPrecios=obtenerPorcentajesPreciosPlantillaCosto();
      while ($rowPreConf = $porcentajesPrecios->fetch(PDO::FETCH_ASSOC)) {
         $valorPor=$rowPreConf['valor'];
         $precioA=$venCS*($valorPor/100);
         $precioN=$venCS+$precioA;
         $dbhAUC = new Conexion();
         $sqlAUC="INSERT INTO precios_simulacioncosto (venta_local, venta_externo,cod_simulacioncosto) 
         VALUES ('".$precioN."','".$precioN."','".$codSimCosto."')";
         $stmtAUC = $dbhAUC->prepare($sqlAUC);
         $stmtAUC->execute();
      }
     }
  echo $codSimCosto;
}

?>
