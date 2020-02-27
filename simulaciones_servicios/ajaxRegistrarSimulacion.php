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
  $cliente=$_GET['cliente'];
  $productos=$_GET['producto'];
  $norma=$_GET['norma'];
  $cod_region=$_GET['local_extranjero'];
  $fecha= date("Y-m-d");

  $codSimServ=obtenerCodigoSimServicio();
  $dbh = new Conexion();
  $sqlInsert="INSERT INTO simulaciones_servicios (codigo, nombre, fecha, cod_plantillaservicio, cod_responsable,dias_auditoria,utilidad_minima,cod_cliente,productos,norma) 
  VALUES ('".$codSimServ."','".$nombre."','".$fecha."', '".$plantilla_servicio."', '".$globalUser."','".$dias."','".$utilidad."','".$cliente."','".$productos."','".$norma."')";
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
      if($cod_region==1){
        $montoCuenta=$rowCuenta['monto'];
      }else{
        $montoCuenta=$rowCuenta['montoext'];
      }
      
      $porcentaje=((float)$montoCuenta*100)/(float)$montoLocal;

      $sqlInsertPorcentaje="INSERT INTO cuentas_simulacion (cod_plancuenta, monto_local, monto_externo, porcentaje,cod_partidapresupuestaria,cod_simulacionservicios) 
      VALUES ('".$codCuenta."','".$montoCuenta."','".$montoCuenta."', '".$porcentaje."', '".$idp."','".$codSimServ."')";
      $stmtInsertPorcentaje = $dbh->prepare($sqlInsertPorcentaje);
      $stmtInsertPorcentaje->execute();
     }
     $detallesPlan=obtenerDetallePlantillaServicioPartida($plantilla_servicio,$idp);
     $cantidadPersonal=obtenerCantidadPersonalPlantilla($plantilla_servicio);
     while ($rowDetallesPlan = $detallesPlan->fetch(PDO::FETCH_ASSOC)) {
      $codigoDetalleSimulacion=obtenerCodigoSimulacionServicioDetalle();
      $codPC=$rowDetallesPlan['cod_plantillatcp'];
      $codPP=$rowDetallesPlan['cod_partidapresupuestaria'];
      $codC=$rowDetallesPlan['cod_cuenta'];
      $glosaD=$rowDetallesPlan['glosa'];
      $montoD=$rowDetallesPlan['monto_total'];
      $montoDE=$rowDetallesPlan['monto_totalext'];
      $editD=$rowDetallesPlan['editado_alumno'];
      $editDE=$rowDetallesPlan['editado_alumnoext'];
      $codBolLoc=$cod_region;
      $monto_generado=0;

       $auditoresPlantilla=obtenerDetallePlantillaServicioAuditores($plantilla_servicio);
       while ($rowAudPlantilla = $auditoresPlantilla->fetch(PDO::FETCH_ASSOC)) {
         $codigoAuditorSimulacion=$rowAudPlantilla['cod_tipoauditor'];
         $codigoAuditorSimulacionDias=$rowAudPlantilla['dias'];
         $codigoAuditorSimulacionCantidad=$rowAudPlantilla['cantidad'];
         $monto_generado+=$codigoAuditorSimulacionDias*$codigoAuditorSimulacionCantidad;
         $dbhSS = new Conexion();
         $sqlSS="INSERT INTO simulaciones_ssd_ssa (cod_simulacionservicio,cod_simulacionserviciodetalle,cod_simulacionservicioauditor,monto,dias,cantidad,monto_externo) 
                  VALUES ('".$codSimServ."','".$codigoDetalleSimulacion."','".$codigoAuditorSimulacion."','".$editD."','".$codigoAuditorSimulacionDias."', '".$codigoAuditorSimulacionCantidad."','".$editDE."')";
         $stmtSS = $dbhSS->prepare($sqlSS);
         $stmtSS->execute(); 
       }
       $cantidadPersonal=$monto_generado;
      $dbhID = new Conexion();
      $sqlID="INSERT INTO simulaciones_serviciodetalle (codigo,cod_simulacionservicio,cod_plantillatcp, cod_partidapresupuestaria, cod_cuenta,glosa,monto_unitario,cantidad,monto_total,cod_estadoreferencial,editado_personal,editado_personalext,monto_totalext,cod_externolocal) 
      VALUES ('".$codigoDetalleSimulacion."','".$codSimServ."','".$codPC."','".$codPP."','".$codC."', '".$glosaD."','".$montoD."','".$cantidadPersonal."','".$montoD."',1,'".$editD."','".$editDE."','".$montoDE."','".$codBolLoc."')";
      $stmtID = $dbhID->prepare($sqlID);
      $stmtID->execute();
     }
     
  }
  //volcado de datos a la tabla simulaciones_servicios_auditores
     $auditoresPlan=obtenerDetallePlantillaServicioAuditores($plantilla_servicio);
     $cantidadAuditoriaPlan=obtenerDetallePlantillaServicioAuditoresCantidad($plantilla_servicio);
     while ($rowAudPlan = $auditoresPlan->fetch(PDO::FETCH_ASSOC)) {
      
      $codTIPA=$rowAudPlan['cod_tipoauditor'];
      $cantidadS=$rowAudPlan['cantidad'];
      $montoS=$rowAudPlan['monto'];
      $montoSE=$rowAudPlan['monto_externo'];
      $codBolLocSE=$cod_region;
      $diasS=$rowAudPlan['dias'];
      $dbhAU = new Conexion();
      $sqlAU="INSERT INTO simulaciones_servicios_auditores (cod_simulacionservicio,cod_tipoauditor, cantidad, monto,cod_estadoreferencial,cantidad_editado,dias,monto_externo,cod_externolocal) 
      VALUES ('".$codSimServ."','".$codTIPA."','".$cantidadS."','".$montoS."',1,'".$cantidadS."','".$diasS."','".$montoSE."','".$codBolLocSE."')";
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
      $codTipoUnidad=1;

      //insertar valores pre definidos a los servicios de sello seleccionados
      $suma=0;$aux=0;$aux2=0;
      if(obtenerConfiguracionValorServicio($codCS)==true){
        $productosLista=explode(",", $productos);
        $codTC=obtenerTipoCliente($cliente);
        $nacional=obtenerTipoNacionalCliente($cliente);
        for ($i=0; $i < count($productosLista); $i++) {
          $aux=obtenerCostoTipoClienteSello(($i+1),$codTC,$nacional);
           if($aux==0){
            $aux=$aux2;
           }else{            
            $aux2=$aux;
           }
           $suma+=$aux;          
        }
       $cantidadS=1;
       $montoS=$suma;        
      }
      $dbhAU = new Conexion();
      $sqlAU="INSERT INTO simulaciones_servicios_tiposervicio (cod_simulacionservicio,cod_claservicio, observaciones,cantidad, monto,cod_estadoreferencial,cantidad_editado,cod_tipounidad) 
      VALUES ('".$codSimServ."','".$codCS."','".$obsCS."','".$cantidadS."','".$montoS."',1,'".$cantidadS."','".$codTipoUnidad."')";
      $stmtAU = $dbhAU->prepare($sqlAU);
      $stmtAU->execute();
     }  

  echo $codSimServ;
}

?>
