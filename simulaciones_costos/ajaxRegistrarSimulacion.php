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
  $monto_norma=$_GET['monto_norma'];
  $codPrecio=$_GET['precio'];
  $tipoCurso=$_GET['tipo_curso'];
  $normas=$_GET['normas'];

  $festimada=explode("/", $_GET['fecha_estimada']);
  $fecha_estimada=$festimada[2]."-".$festimada[1]."-".$festimada[0];
  $cantidad_dias=$_GET['cantidad_dias'];
  $ibnorca=1;
  $cantidadAlumnos=obtenerPlantillaCostoAlumnos($plantilla_costo);
  $utilidadMin=obtenerPlantillaCostoUtilidad($plantilla_costo);
  $cantidadCursosMes=obtenerPlantillaCostoCursosMes($plantilla_costo);
  $fecha= date("Y-m-d");
  $codSimCosto=obtenerCodigoSimCosto();
  $dbh = new Conexion();
  $sqlInsert="INSERT INTO simulaciones_costos (codigo, nombre, fecha, cod_plantillacosto, cod_responsable,cod_precioplantilla,ibnorca,cantidad_alumnoslocal,utilidad_minimalocal,cantidad_cursosmes,cantidad_modulos,monto_norma,habilitado_norma,cod_tipocurso,fecha_curso,dias_curso) 
  VALUES ('".$codSimCosto."','".$nombre."','".$fecha."', '".$plantilla_costo."', '".$globalUser."','".$codPrecio."','".$ibnorca."','".$cantidadAlumnos."','".$utilidadMin."','".$cantidadCursosMes."','".$cantidad_modulos."','".$monto_norma."',1,'".$tipoCurso."','".$fecha_estimada."','".$cantidad_dias."')";
  $stmtInsert = $dbh->prepare($sqlInsert);
  $stmtInsert->execute();

  $dbhD = new Conexion();
  $sqlD="DELETE FROM simulaciones_detalle where cod_simulacioncosto=$codSimCosto";
  $stmtD = $dbhD->prepare($sqlD);
  $stmtD->execute();

  for ($i=0; $i < count($normas); $i++) { 
     $codNorma=$normas[$i];
     $sqlInsertNorma="INSERT INTO simulaciones_costosnormas (cod_simulacion, cod_norma,cantidad,precio) 
     VALUES ('".$codSimCosto."','".$codNorma."',1,10)";
     $stmtInsertNorma = $dbh->prepare($sqlInsertNorma);
     $stmtInsertNorma->execute();
  }
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
      $tipoD=$rowDetallesPlan['tipo_registro'];
      if($tipoD==1||$tipoD==2){
        $cantidadAlumnosAux=1;
        $codTipoD=5;
      }else{
        $cantidadAlumnosAux=$cantidadAlumnos;
        $codTipoD=1;
      }
      $dbhID = new Conexion();
      $sqlID="INSERT INTO simulaciones_detalle (cod_simulacioncosto,cod_plantillacosto, cod_partidapresupuestaria, cod_cuenta,cod_tipo,glosa,monto_unitario,cantidad,monto_total,cod_estadoreferencial,editado_alumno) 
      VALUES ('".$codSimCosto."','".$codPC."','".$codPP."','".$codC."','".$codTipoD."', '".$glosaD."','".$montoD."','".$cantidadAlumnosAux."','".$montoD."',1,'".$editD."')";
      $stmtID = $dbhID->prepare($sqlID);
      $stmtID->execute();
     }
  }
   //volcado de datos a la tabla simulaciones_costos

    $preciosPlan=obtenerListaPreciosPlantillaCosto($plantilla_costo,$codPrecio);
     while ($rowPrePlan = $preciosPlan->fetch(PDO::FETCH_ASSOC)) {
      $codPrecioPropuestaCosto=obtenerCodigoPrecioCosto();
      $codCS=$rowPrePlan['codigo'];
      $venCS=$rowPrePlan['venta_local'];
      $veneS=$rowPrePlan['venta_externo'];
      $codPS=$rowPrePlan['cod_plantillacosto'];
      $dbhAU = new Conexion();
      $sqlAU="INSERT INTO precios_simulacioncosto (codigo,venta_local, venta_externo,cod_simulacioncosto) 
      VALUES ('".$codPrecioPropuestaCosto."','".$venCS."','".$veneS."','".$codSimCosto."')";
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
      $sqlUpdate="UPDATE simulaciones_costos SET cod_precioplantilla=$codPrecioPropuestaCosto where codigo=$codSimCosto";
      $stmtUpdate = $dbh->prepare($sqlUpdate);
      $stmtUpdate->execute();
     }

     $preciosPlan=obtenerListaPreciosPlantillaCosto($plantilla_costo,$codPrecio);
     while ($rowPrePlan = $preciosPlan->fetch(PDO::FETCH_ASSOC)) {
      $codPrecioPropuestaCosto=obtenerCodigoPrecioCosto();
      $codCS=$rowPrePlan['codigo'];
      $venCS=$rowPrePlan['venta_local'];
      $veneS=$rowPrePlan['venta_externo'];
      $codPS=$rowPrePlan['cod_plantillacosto'];
      $dbhAU = new Conexion();
      $sqlAU="INSERT INTO precios_simulacioncosto (codigo,venta_local, venta_externo,cod_simulacioncosto) 
      VALUES ('".$codPrecioPropuestaCosto."','".$venCS."','".$veneS."','".$codSimCosto."')";
      $stmtAU = $dbhAU->prepare($sqlAU);
      $stmtAU->execute();
      
      $sqlUpdate="UPDATE simulaciones_costos SET cod_precioplantilla=$codPrecioPropuestaCosto where codigo=$codSimCosto";
      $stmtUpdate = $dbh->prepare($sqlUpdate);
      $stmtUpdate->execute();


      //costos Fijos en tabla
      $cuentasFijas=obtenerListaCuentasPlantillasCostoFijo($plantilla_costo);
      while ($rowFijo = $cuentasFijas->fetch(PDO::FETCH_ASSOC)) {
         $nombreCuentaFijo=$rowFijo['nombre'];
         $numeroCuentaFijo=$rowFijo['numero'];
         $codCuentaFijo=$rowFijo['cod_cuenta'];
         $codPartidaFijo=$rowFijo['cod_partidapresupuestaria'];
         $tipoFijo=$rowFijo['tipo'];

         $precioLocalX=obtenerPrecioSimulacionCostoGeneral($codSimCosto);
         $precioRegistrado=obtenerPrecioRegistradoPlantillaCosto($plantilla_costo);
         $nCursos=obtenerCantidadCursosPlantillaCosto($plantilla_costo); 
         $porcentPrecios=($precioLocalX)/($precioRegistrado);
         if($tipoFijo==1){ 
         $anioSim= date("Y");  
         $monto=ejecutadoEgresosMes($globalUnidad,((int)$anioSim-1),12,13,1,$numeroCuentaFijo);          
         }else{
          $monto=obtenerListaCuentasPlantillasCostoFijoManual($codCuentaFijo,$codPartidaFijo,$plantilla_costo);
         }
         $montoUnidad=$monto*$porcentPrecios; 
         $dbh = new Conexion();
         $sqlFijos="INSERT INTO simulaciones_cf (cod_simulacionservicio, cod_simulacioncosto,cod_partidapresupuestaria,cod_cuenta,monto,cantidad,monto_total) 
         VALUES (0,'".$codSimCosto."','".$codPartidaFijo."','".$codCuentaFijo."','".$montoUnidad."',1,'".$montoUnidad."')";
         $stmtFijos = $dbh->prepare($sqlFijos);
         $stmtFijos->execute();
      } 
     }
  echo $codSimCosto;
}

?>
