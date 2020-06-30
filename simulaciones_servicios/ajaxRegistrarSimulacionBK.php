
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
  $objeto_servicio=$_GET['objeto_servicio'];
  $productos="";//$productos=$_GET['producto'];
  $sitios="";   // $sitios=$_GET['sitios'];
  $atributos= json_decode($_GET['atributos']);
  $tipo_atributo=$_GET['tipo_atributo'];   
  $afnor=$_GET['afnor'];
  $norma=$_GET['norma'];
  $id_servicio=0; //$_GET['id_servicio']
  $cod_region=1; //$_GET['local_extranjero']
  $anios=$_GET['anios'];
  //$anios=obtenerAnioPlantillaServicio($plantilla_servicio);
  $fecha= date("Y-m-d");

  $codSimServ=obtenerCodigoSimServicio();
  $numeroCorrelativoCliente=obtenerNumeroClienteSimulacion($cliente);
  $nombreSecundario=obtenerNombreCliente($cliente)."(".($numeroCorrelativoCliente+1).")";
  $dbh = new Conexion();
  if(isset($_GET['tipo_servicio'])){
    $idTipoServicio=$_GET['tipo_servicio'];
  }else{
    $idTipoServicio=309; //para servicio TCP
  }
  
  if(isset($_GET['region_cliente'])){
    $regionCliente=$_GET['region_cliente'];
    $iafprimario=$_GET['iaf_primario'];
    $iafsecundario=$_GET['iaf_secundario'];
  }else{
    $regionCliente=1;
    $iafprimario=0;
    $iafsecundario=0;
  }
  $areaGeneralPlantilla=obtenerCodigoAreaPlantillasServicios($plantilla_servicio);

   if($areaGeneralPlantilla==39){
     $inicioAnio=1;
   }else{
     $inicioAnio=0;
   }

  $sqlInsert="INSERT INTO simulaciones_servicios (codigo, nombre, fecha, cod_plantillaservicio, cod_responsable,dias_auditoria,utilidad_minima,cod_cliente,productos,norma,idServicio,anios,porcentaje_fijo,sitios,afnor,porcentaje_afnor,id_tiposervicio,cod_objetoservicio,cod_tipoclientenacionalidad,cod_iaf_primario,cod_iaf_secundario) 
  VALUES ('".$codSimServ."','".$nombre."','".$fecha."', '".$plantilla_servicio."', '".$globalUser."','".$dias."','".$utilidad."','".$cliente."','".$productos."','".$norma."','".$id_servicio."','".$anios."','".obtenerValorConfiguracion(32)."','".$sitios."','".$afnor."','".obtenerValorConfiguracion(33)."','".$idTipoServicio."','".$objeto_servicio."','".$regionCliente."','".$iafprimario."','".$iafsecundario."')";
  $stmtInsert = $dbh->prepare($sqlInsert);
  $stmtInsert->execute();

  //enviar propuestas para la actualizacion de ibnorca
  $fechaHoraActual=date("Y-m-d H:i:s");
  $idTipoObjeto=2707;
  $idObjeto=2715; //regristado
  $obs="Registro de propuesta";
  //id de perfil para cambio de estado en ibnorca
  $id_perfil=$_GET['id_perfil'];
  if($id_perfil==0){
    actualizarEstadosObjetosIbnorca($idTipoObjeto,$idObjeto,$globalUser,$codSimServ,$fechaHoraActual,$obs);
  }else{
    actualizarEstadosObjetosIbnorca($idTipoObjeto,$idObjeto,$id_perfil,$codSimServ,$fechaHoraActual,$obs);
  }


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

      $montoCuenta=0; //para inicialiar valores en 0 costos variables

      $anioParaRegistroAuditor=-1;
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
       // antes de setear los variables a 0 
      $montoDE=$rowDetallesPlan['monto_totalext'];
      $editD=$rowDetallesPlan['editado_alumno'];
      $editDE=$rowDetallesPlan['editado_alumnoext'];
      $codBolLoc=$cod_region;
      $monto_generado=0;

       $auditoresPlantilla=obtenerDetallePlantillaServicioAuditores($plantilla_servicio);
       $cantidadAuditoriaPlan=obtenerDetallePlantillaServicioAuditoresCantidad($plantilla_servicio);
       while ($rowAudPlantilla = $auditoresPlantilla->fetch(PDO::FETCH_ASSOC)) {

         $codTIPA=$rowAudPlantilla['cod_tipoauditor'];
         $nombreTIPA=nameTipoAuditor($codTIPA);
         $cantidadS=$rowAudPlantilla['cantidad'];
         $montoS=$rowAudPlantilla['monto'];
         $montoSE=$rowAudPlantilla['monto_externo'];
         $codBolLocSE=$cod_region;
         $diasS=$rowAudPlantilla['dias'];

         if($anioParaRegistroAuditor!=$i){
            $codigoAuditorSimulacion=obtenerCodigoSimulacionServicioAuditor();
            $dbhAU = new Conexion();
            $sqlAU="INSERT INTO simulaciones_servicios_auditores (codigo,cod_simulacionservicio,cod_tipoauditor, cantidad, monto,cod_estadoreferencial,cantidad_editado,dias,monto_externo,cod_externolocal,cod_anio,habilitado,descripcion) 
             VALUES ('".$codigoAuditorSimulacion."','".$codSimServ."','".$codTIPA."','".$cantidadS."','0',1,'".$cantidadS."',1,'".$montoSE."','".$codBolLocSE."','".$i."',0,'".$nombreTIPA."')";
            $stmtAU = $dbhAU->prepare($sqlAU);
            $stmtAU->execute();
         }else{
            $codigoAuditorSimulacion=obtenerCodigoSimulacionServicioAuditorTipoAuditor($codSimServ,$codTIPA,$i);
         }

         //$codigoAuditorSimulacion=$rowAudPlantilla['cod_tipoauditor'];
         $codigoAuditorSimulacionDias=0; // antes de setear los costos variables a 0 $codigoAuditorSimulacionDias=$rowAudPlantilla['dias'];
         $codigoAuditorSimulacionCantidad=$rowAudPlantilla['cantidad'];
         $monto_generado+=$codigoAuditorSimulacionDias*$codigoAuditorSimulacionCantidad;
         $dbhSS = new Conexion();
         $sqlSS="INSERT INTO simulaciones_ssd_ssa (cod_simulacionservicio,cod_simulacionserviciodetalle,cod_simulacionservicioauditor,monto,dias,cantidad,monto_externo,cod_anio) 
                  VALUES ('".$codSimServ."','".$codigoDetalleSimulacion."','".$codigoAuditorSimulacion."','".$editD."','".$codigoAuditorSimulacionDias."', '".$codigoAuditorSimulacionCantidad."','".$editDE."','".$i."')";
         $stmtSS = $dbhSS->prepare($sqlSS);
         $stmtSS->execute(); 
       }
       $anioParaRegistroAuditor=$i;
       $cantidadPersonal=$monto_generado;
      $dbhID = new Conexion();
      $sqlID="INSERT INTO simulaciones_serviciodetalle (codigo,cod_simulacionservicio,cod_plantillatcp, cod_partidapresupuestaria, cod_cuenta,glosa,monto_unitario,cantidad,monto_total,cod_estadoreferencial,editado_personal,editado_personalext,monto_totalext,cod_externolocal,cod_anio,habilitado) 
      VALUES ('".$codigoDetalleSimulacion."','".$codSimServ."','".$codPC."','".$codPP."','".$codC."', '".$glosaD."','".$montoD."','".$cantidadPersonal."','".$montoD."',1,'".$editD."','".$editDE."','".$montoDE."','".$codBolLoc."','".$i."',0)";
      $stmtID = $dbhID->prepare($sqlID);
      $stmtID->execute();
     }
    }//fin de for
   } 
  }//fin de while
  
  
    //for ($jjjj=$inicioAnio; $jjjj<=$anios; $jjjj++) { 
     $jjjj=$anios;
     //volcado de datos a la tabla simulaciones_servicios_tiposervicio
     if(isset($_GET['region_cliente'])){
      $jjjj=1;
      $serviciosPlan=obtenerServiciosClaServicioTipo(309,1); //TCP 
     }else{
      $serviciosPlan=obtenerServiciosClaServicioTipo($idTipoServicio,0); //TCS $serviciosPlan=obtenerDetallePlantillaServicioTipoServicio($plantilla_servicio);
      $jjjj=0;
     }
     
     while ($rowServPlan = $serviciosPlan->fetch(PDO::FETCH_ASSOC)) {

      //$codCS=$rowServPlan['cod_claservicio'];
      //$obsCS=$rowServPlan['observaciones'];
      //$cantidadS=$rowServPlan['cantidad'];
      //$montoS=$rowServPlan['monto'];

      $codCS=$rowServPlan['IdClaServicio'];
      $obsCS=$rowServPlan['Descripcion'];
      $cantidadS=1;
      $montoS=1;

      if($montoS==0){
        $montoS=1;
      }
      $codTipoUnidad=1;

      //insertar valores pre definidos a los servicios de sello seleccionados
      $suma=0;$aux=0;$aux2=0;
      if(obtenerConfiguracionValorServicio($codCS)==true&&isset($_GET['region_cliente'])){
        //$productosLista=explode(",", $productos);
        if(isset($_GET['tipo_cliente'])){
          $codTC=$_GET['tipo_cliente'];
        }else{
          $codTC=obtenerTipoCliente($cliente);
        }        
        $nacional=obtenerTipoNacionalCliente($cliente);
        if(isset($_GET['region_cliente'])){
          $nacional=$_GET['region_cliente'];
        }
        /*if($nacional>1){
          if($codTC<=2){
             $codTC=4; //empresa MEDIANA
          }
        }*/
        
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
      $sqlAU="INSERT INTO simulaciones_servicios_tiposervicio (cod_simulacionservicio,cod_claservicio, observaciones,cantidad, monto,cod_estadoreferencial,cantidad_editado,cod_tipounidad,cod_anio,habilitado) 
      VALUES ('".$codSimServ."','".$codCS."','".$obsCS."','".$cantidadS."','".$montoS."',1,'".$cantidadS."','".$codTipoUnidad."','".$jjjj."',0)";
      $stmtAU = $dbhAU->prepare($sqlAU);
      $stmtAU->execute();
     }

   //} //fin de for anios  
     
    for ($jjjj=$inicioAnio; $jjjj<=$anios; $jjjj++) { 
        //costos Fijos en tabla
      $cuentasFijas=obtenerListaCuentasPlantillasCostoFijoServicio($plantilla_servicio);
      while ($rowFijo = $cuentasFijas->fetch(PDO::FETCH_ASSOC)) {
         $nombreCuentaFijo=$rowFijo['nombre'];
         $numeroCuentaFijo=$rowFijo['numero'];
         $codCuentaFijo=$rowFijo['cod_cuenta'];
         $codPartidaFijo=$rowFijo['cod_partidapresupuestaria'];
         $tipoFijo=$rowFijo['tipo'];

         $precioLocalX=obtenerPrecioServiciosSimulacionPorAnio($codSimServ,$jjjj);
         $precioRegistrado=obtenerPrecioRegistradoPlantilla($plantilla_servicio);
         $nCursos=obtenerCantidadAuditoriasPlantilla($plantilla_servicio); 
         $porcentPrecios=($precioLocalX)/($precioRegistrado);
         if($tipoFijo==1){ 
         $anioSim= date("Y");  
         $monto=ejecutadoEgresosMes($globalUnidad,((int)$anioSim-1),12,$areaGeneralPlantilla,1,$numeroCuentaFijo);          
         }else{
          $monto=obtenerListaCuentasPlantillasCostoFijoServicioManual($codCuentaFijo,$codPartidaFijo,$plantilla_servicio);
         }
         $montoUnidad=$monto*$porcentPrecios; 
         $dbh = new Conexion();
         $sqlFijos="INSERT INTO simulaciones_cf (cod_simulacionservicio, cod_simulacioncosto,cod_partidapresupuestaria,cod_cuenta,monto,cantidad,monto_total,cod_anio) 
         VALUES ('".$codSimServ."',0,'".$codPartidaFijo."','".$codCuentaFijo."','".$montoUnidad."',1,'".$montoUnidad."','".$jjjj."')";
         $stmtFijos = $dbh->prepare($sqlFijos);
         $stmtFijos->execute();
      } 
    } //fin de for anios  

    //simulaciones_serviciosauditores
          $nC=cantidadF($atributos);
          for($att=0;$att<$nC;$att++){
              $nombreAtributo=$atributos[$att]->nombre;
              $direccionAtributo=$atributos[$att]->direccion;
              $marcaAtributo=$atributos[$att]->marca;
              $normaAtributo=$atributos[$att]->norma;
              //
              $selloAtributo=$atributos[$att]->sello;

              $paisAtributo=$atributos[$att]->pais;
              $estadoAtributo=$atributos[$att]->estado;
              $ciudadAtributo=$atributos[$att]->ciudad;

              $codSimulacionServicioAtributo=obtenerCodigoSimulacionServicioAtributo();
              $sqlDetalleAtributos="INSERT INTO simulaciones_servicios_atributos (codigo,cod_simulacionservicio, nombre, direccion, cod_tipoatributo,marca,norma,nro_sello,cod_pais,cod_estado,cod_ciudad) 
              VALUES ('$codSimulacionServicioAtributo','$codSimServ', '$nombreAtributo', '$direccionAtributo', '$tipo_atributo','$marcaAtributo','$normaAtributo','$selloAtributo','$paisAtributo','$estadoAtributo','$ciudadAtributo')";
              $stmtDetalleAtributos = $dbh->prepare($sqlDetalleAtributos);
              $stmtDetalleAtributos->execute();

              if($tipo_atributo==1){
                $normaCodAtributo=$atributos[$att]->norma_cod;
                $normasFila=explode(",",$normaCodAtributo);
                for ($ni=0; $ni < count($normasFila); $ni++) { 
                 $codNorma=$normasFila[$ni];
                  $sqlDetalleAtributosNormas="INSERT INTO simulaciones_servicios_atributosnormas (cod_simulacionservicioatributo, cod_norma, precio,cantidad) 
                 VALUES ('$codSimulacionServicioAtributo', '$codNorma', '10',1)";
                 $stmtDetalleAtributosNormas = $dbh->prepare($sqlDetalleAtributosNormas);
                 $stmtDetalleAtributosNormas->execute(); 
                }
              }else{   
                for ($yyyy=$inicioAnio; $yyyy<=$anios; $yyyy++) {  
                 $sqlDetalleAtributosDias="INSERT INTO simulaciones_servicios_atributosdias (cod_simulacionservicioatributo, dias, cod_anio) 
                 VALUES ('$codSimulacionServicioAtributo', '0', '$yyyy')";
                 $stmtDetalleAtributosDias = $dbh->prepare($sqlDetalleAtributosDias);
                 $stmtDetalleAtributosDias->execute(); 

                   
                 //insertar auditores por sitios
                 $auditoresSim=obtenerAuditoresSimulacionPorAnio($codSimServ,$yyyy);
                 while ($rowAud = $auditoresSim->fetch(PDO::FETCH_ASSOC)) {
                     $codAuditorSim=$rowAud['codigo'];
                     $sqlDetalleAtributosAud="INSERT INTO simulaciones_servicios_atributosauditores (cod_simulacionservicioatributo, cod_auditor, cod_anio,estado) 
                     VALUES ('$codSimulacionServicioAtributo', '$codAuditorSim', '$yyyy',0)";
                     $stmtDetalleAtributosAud = $dbh->prepare($sqlDetalleAtributosAud);
                     $stmtDetalleAtributosAud->execute(); 
                 }
                }
              }         
              
         }
         //FIN simulaciones_serviciosauditores

  echo $codSimServ;
}

?>
