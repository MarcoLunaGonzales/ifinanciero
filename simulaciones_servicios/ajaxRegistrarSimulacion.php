<?php
set_time_limit (0);
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
  $productos="";//$productos=$_GET['producto'];
  $sitios="";   // $sitios=$_GET['sitios'];
  $atributos= json_decode($_GET['atributos']);
  $tipo_atributo=$_GET['tipo_atributo'];
  $afnor=$_GET['afnor'];
  $norma=$_GET['norma'];
  $id_servicio=$_GET['id_servicio'];
  $cod_region=$_GET['local_extranjero'];
  $anios=$_GET['anios'];
  //$anios=obtenerAnioPlantillaServicio($plantilla_servicio);
  $fecha= date("Y-m-d");

  $codSimServ=obtenerCodigoSimServicio();
  $dbh = new Conexion();
  if(isset($_GET['tipo_servicio'])){
    $idTipoServicio=$_GET['tipo_servicio'];
  }else{
    $idTipoServicio=0;
  }

   if($tipo_atributo==1){
     $inicioAnio=1;
   }else{
     $inicioAnio=0;
   }

  $sqlInsert="INSERT INTO simulaciones_servicios (codigo, nombre, fecha, cod_plantillaservicio, cod_responsable,dias_auditoria,utilidad_minima,cod_cliente,productos,norma,idServicio,anios,porcentaje_fijo,sitios,afnor,porcentaje_afnor) 
  VALUES ('".$codSimServ."','".$nombre."','".$fecha."', '".$plantilla_servicio."', '".$globalUser."','".$dias."','".$utilidad."','".$cliente."','".$productos."','".$norma."','".$id_servicio."','".$anios."','".obtenerValorConfiguracion(32)."','".$sitios."','".$afnor."','".obtenerValorConfiguracion(33)."')";
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
  
  //SITIOS 0 PRODUCTOS
   $dbhA = new Conexion();
  $sqlA="DELETE FROM simulaciones_servicios_atributos where cod_simulacionservicio=$codSimServ";
  $stmtA = $dbhA->prepare($sqlA);
  $stmtA->execute();

  //simulaciones_serviciosauditores
          $nC=cantidadF($atributos);
          for($att=0;$att<$nC;$att++){
              $nombreAtributo=$atributos[$att]->nombre;
              if($tipo_atributo==1){
                $direccionAtributo="";
              }else{
                $direccionAtributo=$atributos[$att]->direccion;
              }         
              $sqlDetalleAtributos="INSERT INTO simulaciones_servicios_atributos (cod_simulacionservicio, nombre, direccion, cod_tipoatributo) 
              VALUES ('$codSimServ', '$nombreAtributo', '$direccionAtributo', '$tipo_atributo')";
              $stmtDetalleAtributos = $dbh->prepare($sqlDetalleAtributos);
              $stmtDetalleAtributos->execute();
         }
         //FIN simulaciones_serviciosauditores

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
  for ($i=$inicioAnio; $i<=$anios; $i++) { 
      $porcentaje=((float)$montoCuenta*100)/(float)$montoLocal;

      $sqlInsertPorcentaje="INSERT INTO cuentas_simulacion (cod_plancuenta, monto_local, monto_externo, porcentaje,cod_partidapresupuestaria,cod_simulacionservicios,cod_anio) 
      VALUES ('".$codCuenta."','".$montoCuenta."','".$montoCuenta."', '".$porcentaje."', '".$idp."','".$codSimServ."','".$i."')";
      $stmtInsertPorcentaje = $dbh->prepare($sqlInsertPorcentaje);
      $stmtInsertPorcentaje->execute();

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
         $sqlSS="INSERT INTO simulaciones_ssd_ssa (cod_simulacionservicio,cod_simulacionserviciodetalle,cod_simulacionservicioauditor,monto,dias,cantidad,monto_externo,cod_anio) 
                  VALUES ('".$codSimServ."','".$codigoDetalleSimulacion."','".$codigoAuditorSimulacion."','".$editD."','".$codigoAuditorSimulacionDias."', '".$codigoAuditorSimulacionCantidad."','".$editDE."','".$i."')";
         $stmtSS = $dbhSS->prepare($sqlSS);
         $stmtSS->execute(); 
       }
       $cantidadPersonal=$monto_generado;
      $dbhID = new Conexion();
      $sqlID="INSERT INTO simulaciones_serviciodetalle (codigo,cod_simulacionservicio,cod_plantillatcp, cod_partidapresupuestaria, cod_cuenta,glosa,monto_unitario,cantidad,monto_total,cod_estadoreferencial,editado_personal,editado_personalext,monto_totalext,cod_externolocal,cod_anio) 
      VALUES ('".$codigoDetalleSimulacion."','".$codSimServ."','".$codPC."','".$codPP."','".$codC."', '".$glosaD."','".$montoD."','".$cantidadPersonal."','".$montoD."',1,'".$editD."','".$editDE."','".$montoDE."','".$codBolLoc."','".$i."')";
      $stmtID = $dbhID->prepare($sqlID);
      $stmtID->execute();
     }
    }
   } 
  }
  
  for ($iiii=$inicioAnio; $iiii<=$anios; $iiii++) {  
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
      $sqlAU="INSERT INTO simulaciones_servicios_auditores (cod_simulacionservicio,cod_tipoauditor, cantidad, monto,cod_estadoreferencial,cantidad_editado,dias,monto_externo,cod_externolocal,cod_anio) 
      VALUES ('".$codSimServ."','".$codTIPA."','".$cantidadS."','".$montoS."',1,'".$cantidadS."','".$diasS."','".$montoSE."','".$codBolLocSE."','".$iiii."')";
      $stmtAU = $dbhAU->prepare($sqlAU);
      $stmtAU->execute();
     }
  
    }
    for ($jjjj=$inicioAnio; $jjjj<=$anios; $jjjj++) { 
     //volcado de datos a la tabla simulaciones_servicios_tiposervicio
     $serviciosPlan=obtenerDetallePlantillaServicioTipoServicio($plantilla_servicio);
     while ($rowServPlan = $serviciosPlan->fetch(PDO::FETCH_ASSOC)) {

      $codCS=$rowServPlan['cod_claservicio'];
      $obsCS=$rowServPlan['observaciones'];
      $cantidadS=$rowServPlan['cantidad'];
      $montoS=$rowServPlan['monto'];
      if($montoS==0){
        $montoS=1;
      }
      $codTipoUnidad=1;

      //insertar valores pre definidos a los servicios de sello seleccionados
      $suma=0;$aux=0;$aux2=0;
      if(obtenerConfiguracionValorServicio($codCS)==true){
        //$productosLista=explode(",", $productos);
        $codTC=obtenerTipoCliente($cliente);
        $nacional=obtenerTipoNacionalCliente($cliente);
        for ($i=0; $i < count($atributos); $i++) {
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
      $sqlAU="INSERT INTO simulaciones_servicios_tiposervicio (cod_simulacionservicio,cod_claservicio, observaciones,cantidad, monto,cod_estadoreferencial,cantidad_editado,cod_tipounidad,cod_anio) 
      VALUES ('".$codSimServ."','".$codCS."','".$obsCS."','".$cantidadS."','".$montoS."',1,'".$cantidadS."','".$codTipoUnidad."','".$jjjj."')";
      $stmtAU = $dbhAU->prepare($sqlAU);
      $stmtAU->execute();
     }

   } //fin de for anios  

  echo $codSimServ;
}

?>
