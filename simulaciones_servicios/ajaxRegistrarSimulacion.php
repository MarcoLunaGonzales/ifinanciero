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
  $plantilla_servicio=$_GET['plantilla_servicio'];
  $dias=$_GET['dias'];
  $utilidad=$_GET['utilidad'];
  $fecha= date("Y-m-d");

  $codSimServ=obtenerCodigoSimServicio();
  $dbh = new Conexion();
  $sqlInsert="INSERT INTO simulaciones_servicios (codigo, nombre, fecha, cod_plantillaservicio, cod_responsable,dias_auditoria,utilidad_minima) 
  VALUES ('".$codSimServ."','".$nombre."','".$fecha."', '".$plantilla_servicio."', '".$globalUser."','".$dias."','".$utilidad."')";
  $stmtInsert = $dbh->prepare($sqlInsert);
  $stmtInsert->execute();

  $dbhD = new Conexion();
  $sqlD="DELETE FROM simulaciones_serviciodetalle where cod_simulacionservicio=$codSimServ";
  $stmtD = $dbhD->prepare($sqlD);
  $stmtD->execute();

  $dbhA = new Conexion();
  $sqlA="DELETE FROM simulaciones_servicios_auditores where cod_simulacionservicio=$codSimServ";
  $stmtA = $dbhA->prepare($sqlA);
  $stmtA->execute();

  //borrar datos
  $dbhA = new Conexion();
  $sqlA="DELETE FROM simulaciones_servicios_tiposervicio where cod_simulacionservicio=$codSimServ";
  $stmtA = $dbhA->prepare($sqlA);
  $stmtA->execute();

  //insertar datos en la tabla cuentas_simulacion
  $anio=date("Y");
  $anio_pasado=((int)$anio)-1;
  $mes=date("m");
  //seleccionar las partidas variables con montos_ibnorca y fuera
  $partidasPlan=obtenerPartidasPlantillaServicio($plantilla_servicio,2);
  while ($rowPartida = $partidasPlan->fetch(PDO::FETCH_ASSOC)) {
     $idp=$rowPartida['cod_partidapresupuestaria'];
     $unidad=$rowPartida['cod_unidadorganizacional'];
     $area=$rowPartida['cod_area'];
     $tipoCalculo=$rowPartida['tipo_calculo'];
     $montoLocal=$rowPartida['monto_local'];
     $montoExterno=$rowPartida['monto_externo'];

     $cuentasPlan=obtenerMontosCuentasDetallePlantillaServicioPartida($plantilla_servicio,$idp);

      while ($rowCuenta = $cuentasPlan->fetch(PDO::FETCH_ASSOC)) {
      $codCuenta=$rowCuenta['cod_cuenta'];
      $numero=trim($rowCuenta['numero']);
      $tipoSim=obtenerValorConfiguracion(13);
      $montoCuenta=$rowCuenta['monto'];
      $porcentaje=((float)$montoCuenta*100)/(float)$montoLocal;

      $sqlInsertPorcentaje="INSERT INTO cuentas_simulacion (cod_plancuenta, monto_local, monto_externo, porcentaje,cod_partidapresupuestaria,cod_simulacionservicios) 
      VALUES ('".$codCuenta."','".$montoCuenta."','".$montoCuenta."', '".$porcentaje."', '".$idp."','".$codSimServ."')";
      $stmtInsertPorcentaje = $dbh->prepare($sqlInsertPorcentaje);
      $stmtInsertPorcentaje->execute();
     }
     $detallesPlan=obtenerDetallePlantillaServicioPartida($plantilla_servicio,$idp);
     $cantidadPersonal=obtenerCantidadPersonalPlantilla($plantilla_servicio);
     while ($rowDetallesPlan = $detallesPlan->fetch(PDO::FETCH_ASSOC)) {

      $codPC=$rowDetallesPlan['cod_plantillatcp'];
      $codPP=$rowDetallesPlan['cod_partidapresupuestaria'];
      $codC=$rowDetallesPlan['cod_cuenta'];
      $glosaD=$rowDetallesPlan['glosa'];
      $montoD=$rowDetallesPlan['monto_total'];
      $editD=$rowDetallesPlan['editado_alumno'];
      $dbhID = new Conexion();
      $sqlID="INSERT INTO simulaciones_serviciodetalle (cod_simulacionservicio,cod_plantillatcp, cod_partidapresupuestaria, cod_cuenta,glosa,monto_unitario,cantidad,monto_total,cod_estadoreferencial,editado_personal) 
      VALUES ('".$codSimServ."','".$codPC."','".$codPP."','".$codC."', '".$glosaD."','".$montoD."','".$cantidadPersonal."','".$montoD."',1,'".$editD."')";
      $stmtID = $dbhID->prepare($sqlID);
      $stmtID->execute();
     }

     
  }
  //volcado de datos a la tabla simulaciones_servicios_auditores
     $auditoresPlan=obtenerDetallePlantillaServicioAuditores($plantilla_servicio);
     while ($rowAudPlan = $auditoresPlan->fetch(PDO::FETCH_ASSOC)) {

      $codTIPA=$rowAudPlan['cod_tipoauditor'];
      $cantidadS=$rowAudPlan['cantidad'];
      $montoS=$rowAudPlan['monto'];
      $dbhAU = new Conexion();
      $sqlAU="INSERT INTO simulaciones_servicios_auditores (cod_simulacionservicio,cod_tipoauditor, cantidad, monto,cod_estadoreferencial,cantidad_editado) 
      VALUES ('".$codSimServ."','".$codTIPA."','".$cantidadS."','".$montoS."',1,'".$cantidadS."')";
      $stmtAU = $dbhAU->prepare($sqlAU);
      $stmtAU->execute();
     }

     //volcado de datos a la tabla simulaciones_servicios_tiposervicio
     $serviciosPlan=obtenerDetallePlantillaServicioTipoServicio($plantilla_servicio);
     while ($rowServPlan = $serviciosPlan->fetch(PDO::FETCH_ASSOC)) {

      $codCS=$rowServPlan['cod_claservicio'];
      $obsCS=$rowServPlan['observaciones'];
      $cantidadS=$rowServPlan['cantidad'];
      $montoS=$rowServPlan['monto'];
      $dbhAU = new Conexion();
      $sqlAU="INSERT INTO simulaciones_servicios_tiposervicio (cod_simulacionservicio,cod_claservicio, observaciones,cantidad, monto,cod_estadoreferencial,cantidad_editado) 
      VALUES ('".$codSimServ."','".$codCS."','".$obsCS."','".$cantidadS."','".$montoS."',1,'".$cantidadS."')";
      $stmtAU = $dbhAU->prepare($sqlAU);
      $stmtAU->execute();
     }
  
  echo $codSimServ;
}

?>
