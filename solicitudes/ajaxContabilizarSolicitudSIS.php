<?php
require_once '../conexion.php';
require_once '../functions.php';
require_once '../functionsGeneral.php';
require_once 'configModule.php';

session_start();
$dbh = new Conexion();

$listaSR=json_decode($_POST["solicitudes"]);

$globalUser=$_SESSION["globalUser"];
$globalGestion=$_SESSION["globalGestion"];
$globalMes=$_SESSION['globalMes'];
$globalUnidad=$_SESSION["globalUnidad"];
$globalArea=$_SESSION["globalArea"];
$globalAdmin=$_SESSION["globalAdmin"];
$globalNombreGestion=$_SESSION["globalNombreGestion"];
$fechaHoraActual=date("Y-m-d H:i:s");
$userAdmin=obtenerValorConfiguracion(74);




//  CREAR EL COMPROBANTE

//INICIO DE VARIABLES
$glosaDetalleGeneral="";
$tipoComprobante=2;
$sumaDevengado=0;
$codComprobante=obtenerCodigoComprobante();
if(isset($_POST['existe'])&&verificarEdicionComprobanteUsuario($globalUser)!=0){
  $codComprobante=$_POST['existe'];  
}

//personal encargado

$fechaHoraActualSitema=date("Y-m-d H:i:s");

//fecha hora actual para el comprobante (SESIONES)
$anioActual=date("Y");
$mesActual=date("m");
$diaActual=date("d");
$codMesActiva=$_SESSION['globalMes']; 
$month = $globalNombreGestion."-".$codMesActiva;
$aux = date('Y-m-d', strtotime("{$month} + 1 month"));
$diaUltimo = date('d', strtotime("{$aux} - 1 day"));
if((int)$globalNombreGestion<(int)$anioActual){
  $fechaHoraActual=$globalNombreGestion."-".$codMesActiva."-".$diaUltimo;
}else{
  if((int)$mesActual==(int)$codMesActiva){
      $fechaHoraActual=date("Y-m-d");
  }else{
    $fechaHoraActual=$globalNombreGestion."-".$codMesActiva."-".$diaUltimo;
  } 
}
// FIN DE LA FECHA


//crear comprobante
$cod_unidadX=3000;
$nroCorrelativo=numeroCorrelativoComprobante($globalGestion,$cod_unidadX,$tipoComprobante,$globalMes);    
$datosServicio="";

//CREACION DEL COMPROBANTE
    if(isset($_POST['existe'])&&verificarEdicionComprobanteUsuario($globalUser)!=0){
       $sqlUpdateComprobantes="UPDATE comprobantes SET modified_at='$fechaHoraActualSitema',modified_by=$globalUser where codigo=$codComprobante";
       $stmtUpdateComprobante = $dbh->prepare($sqlUpdateComprobantes);
       $stmtUpdateComprobante->execute();

       $sqlEstadosCuenta="SELECT codigo from comprobantes_detalle where cod_comprobante=$codComprobante";
       $stmtEstadosCuenta = $dbh->prepare($sqlEstadosCuenta);
       $stmtEstadosCuenta->execute();
       while ($rowEsta = $stmtEstadosCuenta->fetch(PDO::FETCH_ASSOC)) {
        $codigoDetalle=$rowEsta['codigo'];
        $sqlDelete="DELETE from estados_cuenta where cod_comprobantedetalle='$codigoDetalle'";
        $stmtDel = $dbh->prepare($sqlDelete);
        $stmtDel->execute();
       }
      
    }else{
      $sqlInsert="INSERT INTO comprobantes (codigo, cod_empresa, cod_unidadorganizacional, cod_gestion, cod_moneda, cod_estadocomprobante, cod_tipocomprobante, fecha, numero, glosa, created_at, created_by, modified_at, modified_by) 
      VALUES ('$codComprobante', '1', '$cod_unidadX', '$globalNombreGestion', '1', '1', '$tipoComprobante', '$fechaHoraActual', '$nroCorrelativo', '', '$fechaHoraActualSitema', '$globalUser', '$fechaHoraActualSitema', '$globalUser')";
      $stmtInsert = $dbh->prepare($sqlInsert);
      $flagSuccessComprobante=$stmtInsert->execute();
    }


    $sqlDelete="";
    $sqlDelete="DELETE from comprobantes_detalle where cod_comprobante='$codComprobante'";
    $stmtDel = $dbh->prepare($sqlDelete);
    $flagSuccess=$stmtDel->execute();

$glosaResumido="";
$glosaResumidoArray=[];
//inicio de for
foreach ($listaSR as $liSR) {
  $codigo=$liSR->codigo_item;


//DATOS DE LA SOLICITUD CABECERA
  // Preparamos
$stmtSolicitud = $dbh->prepare("SELECT sr.*,es.nombre as estado,u.abreviatura as unidad,a.abreviatura as area from solicitud_recursos sr join estados_solicitudrecursos es on sr.cod_estadosolicitudrecurso=es.codigo join unidades_organizacionales u on sr.cod_unidadorganizacional=u.codigo join areas a on sr.cod_area=a.codigo where sr.cod_estadoreferencial=1 and sr.codigo=$codigo");
// Ejecutamos
$stmtSolicitud->execute();
// bindColumn
$stmtSolicitud->bindColumn('unidad', $unidadX);
$stmtSolicitud->bindColumn('area', $areaX);
$stmtSolicitud->bindColumn('cod_unidadorganizacional', $cod_unidadX);
$stmtSolicitud->bindColumn('cod_area', $cod_areaX);
$stmtSolicitud->bindColumn('cod_simulacion', $codSimulacion);
$stmtSolicitud->bindColumn('cod_proveedor', $codProveedor);
$stmtSolicitud->bindColumn('cod_simulacionservicio', $codSimulacionServicio);
$stmtSolicitud->bindColumn('numero', $numeroSol);
$stmtSolicitud->bindColumn('idServicio', $idServicioX);

while ($rowSolicitud = $stmtSolicitud->fetch(PDO::FETCH_BOUND)) {
      $cod_unidadX=3000;
      $cod_areaX=obtenerValorConfiguracion(65);
      /*$cod_unidadX=$cod_unidadX;
      $cod_areaX=$cod_areaX;*/
      $codSimulacion=$codSimulacion;
      $codProveedor=$codProveedor;
      $codSimulacionServicio=$codSimulacionServicio;
      $numeroSol=$numeroSol;
      if($codSimulacion!=0){
        $nombreCliente="";
        $nombreSimulacion=nameSimulacion($codSimulacion);
      }else{
        $nombreCliente="";//nameClienteSimulacionServicio($codSimulacionServicio);
        $nombreSimulacion=nameSimulacionServicio($codSimulacionServicio);
      }
      $glosa=$nombreCliente." SR ".$numeroSol;
}



//FIN DE LA SOLICITUD CABECERA

$facturaCabecera=obtenerNumeroFacturaSolicitudRecursos($codigo);
    $userSolicitud=obtenerPersonalSolicitanteRecursos($codigo);
    $unidadSol=$cod_unidadX;
    $areaSol=$cod_areaX;

    $sqlUpdateSolicitud="UPDATE solicitud_recursos SET cod_comprobante=$codComprobante where codigo=$codigo";
    $stmtUpdateSolicitudRecurso = $dbh->prepare($sqlUpdateSolicitud);
    $stmtUpdateSolicitudRecurso->execute();


//INICIO CREACION DE DETALLES EN COMPROBANTE
    /*Lista detalles de la solicitud de recursos*/
    $nuevosDetalles=obtenerDetalleSolicitudParaComprobante($codigo); 

//VARIABLES INICIO DETALLES
    $i=0;
    $codProveedor=0;

    $nombresProveedor="";
    $nombreProveedor="";
    $sumaRetencionDiferido=0;
    $sumaProveedorPasivo=0;
    $unidadDetalleGrupal=0;
    $areaDetalleGrupal=0;

//CARGAR SI HAY UNA RETENCION (PARA AGRUPAR TODA LA SOLUCITUD)   

    $numeroRetencionFactura=2; //numeroDeRetencionesIVA($codigo)[0];
    $codRetencionGlobal=numeroDeRetencionesIVA($codigo)[1];
    $porcentajeRetencionGlobal=porcentRetencion($codRetencionGlobal);

    while ($rowNuevo = $nuevosDetalles->fetch(PDO::FETCH_ASSOC)) {
        
        //DATOS PARA EL REGISTRO DE LA CUENTA DE GASTO
        $cuentaAuxiliar=0;
        $i++;
        $cuenta=$rowNuevo['cod_plancuenta'];
        $numeroCuenta=trim(obtieneNumeroCuenta($cuenta));
        $inicioNumero=$numeroCuenta[0];
        $unidadarea=obtenerUnidadAreaCentrosdeCostos($inicioNumero);
        $codSolicitudDetalle=$rowNuevo['codigo'];
        $codSolicitudDetalleOrigen=$rowNuevo['codigo'];
        $codActividadproyecto=$rowNuevo['cod_actividadproyecto'];
        $codAccNum=$rowNuevo['acc_num'];
        $tituloFactura="";
        if(obtenerNumeroFacturaSolicitudRecursoDetalle($rowNuevo['codigo'])!=""){
          $numeroFacturas=obtenerFacturasSolicitudDetalleArray($rowNuevo['codigo']);
          $numerosFacturasDetalle=[];
          for ($y=0; $y < count($numeroFacturas); $y++) { 
            $numerosFacturasDetalle[$y]=$numeroFacturas[$y][1];
          }
          $tituloFactura="F/ ".implode($numerosFacturasDetalle,',')." - ";
        }
        $detalleActividadFila="";
        if(obtenerNombreDirectoActividadServicio($codActividadproyecto)[0]!=""){
          $detalleActividadFila="Actividad: ".obtenerNombreDirectoActividadServicio($codActividadproyecto)[0]."\n"; //." ".obtenerNombreDirectoActividadServicio($codActividadproyecto)[1].
        }
        $glosaDetalle=$detalleActividadFila."Beneficiario: ".nameProveedor($rowNuevo['cod_proveedor'])." ".str_replace("-", "",$rowNuevo['glosa'])." ".$tituloFactura." ".$datosServicio." ".$glosa;
        $glosaDetalleRetencion="Beneficiario: ".nameProveedor($rowNuevo['cod_proveedor'])." ".str_replace("-", "",$rowNuevo['glosa'])." ".$datosServicio." ".$glosa;
        //$glosaResumido.=obtenerNombreDirectoActividadServicio($codActividadproyecto)[0]." ".obtenerNombreDirectoActividadServicio($codActividadproyecto)[1].". Beneficiario: ".nameProveedor($rowNuevo['cod_proveedor'])." ".$glosa;
        $glosaResumido=nameProveedor($rowNuevo['cod_proveedor'])." - ".$glosa;
        array_push($glosaResumidoArray, $glosaResumido);

        if($codProveedor!=$rowNuevo['cod_proveedor']){
           if($codProveedor!=0){
            //proveedor devengado //para cuando cambie de proveedor (ULTIMO PROVEEEDOR)
          
            //$sumaDevengado=0;
            $i++; 
            
           } 
         $codProveedor=$rowNuevo['cod_proveedor'];
        }
        
         //CARGAR UNIDAD Y AREA DEL DETALLE CENTRO DE COSTOS
        if($unidadarea[0]==0){
            $unidadDetalle=3000;
            $area=obtenerValorConfiguracion(65);
            /*$rowNuevo['cod_unidadorganizacional'];
            $area=$rowNuevo['cod_area'];*/
        }else{
            $unidadDetalle=3000;
            $area=obtenerValorConfiguracion(65);
        }

        $unidadDetalleGlobal=$unidadDetalle;
        $areaDetalleGlobal=$area;

        $debe=$rowNuevo['monto'];
        $sumaProveedorPasivo+=$debe;
        $haber=0;
        if($numeroRetencionFactura==1){
          $sumaRetencionDiferido+=($debe-(($porcentajeRetencionGlobal/100)*$debe));
          $debe=($porcentajeRetencionGlobal/100)*$debe;          
        }

      //REGISTRO DE LA CUENTA DE GASTO

        //SIN RETENCION     
        if($rowNuevo['cod_confretencion']==0||$rowNuevo['cod_confretencion']==8){
          if(verificarListaDistribucionGastoSolicitudRecurso($codigo)==0){
            //detalle comprobante SIN RETENCION ///////////////////////////////////////////////////////////////
            $sumaDevengado+=$debe;
            $codComprobanteDetalle=obtenerCodigoComprobanteDetalle(); 
            $sqlDetalle="INSERT INTO comprobantes_detalle (codigo,cod_comprobante, cod_cuenta, cod_cuentaauxiliar, cod_unidadorganizacional, cod_area, debe, haber, glosa, orden,cod_actividadproyecto,cod_accnum) 
            VALUES ('$codComprobanteDetalle','$codComprobante', '$cuenta', '$cuentaAuxiliar', '$unidadDetalle', '$area', '$debe', '$haber', '$glosaDetalle', '$i','$codActividadproyecto','$codAccNum')";
            $stmtDetalle = $dbh->prepare($sqlDetalle);
            $flagSuccessDetalle=$stmtDetalle->execute();  
          }else{
            //distribuir gastos
             include "distribucionComprobanteDevengado.php";
          }
        }else{
        // CON RETENCION 
            $codigoRet=$rowNuevo['cod_confretencion'];
            $importeOriginal=$rowNuevo['monto'];
            $importeRetencion=(porcentRetencion($codigoRet)/100)*$importeOriginal;

            $importeRetencionGasto=$importeRetencion;
            
            $importePasivoFila=$importeRetencionGasto;
            $ii=$i;
            $nom_cuenta_auxiliar="";
            $importeOriginal2=0;
            $totalRetencion=0;
            //obtener datos de retenciones
            $stmtRetenciones = $dbh->prepare("SELECT cd.*,c.porcentaje_cuentaorigen from configuracion_retenciones c join configuracion_retencionesdetalle cd on cd.cod_configuracionretenciones=c.codigo where cd.cod_configuracionretenciones=$codigoRet and cd.cod_cuenta!=0 order by cd.codigo");
            $stmtRetenciones->execute();
            $j=0;
            $retenciones=[];

            //INICIO CARGAR EL DETALLE DE LAS RETENCIONES AL ARRAY
            while ($rowRet = $stmtRetenciones->fetch(PDO::FETCH_ASSOC)) {
            $ii++;                         
             $porcentajeX=$rowRet['porcentaje'];                         
             $glosaX=$rowRet['glosa'];
             $debehaberX=$rowRet['debe_haber'];
             $porcentajeCuentaX=$rowRet['porcentaje_cuentaorigen'];
             
             //SACAR EL EL MONTO DEL GASTO PARA APLICAR EL % DE LA RETENCION
             if($porcentajeCuentaX>100){
               $importe=$importeRetencion;
             }else{
               $importe=$importeOriginal;
             }
            

             //MONTO DE LA RETENCION
             $montoRetencion=($porcentajeX/100)*$importe;
            
             //SUMAR DATOS O RESTAR PARA LA CUENTA PASIVO
             if($debehaberX==1){
                $importePasivoFila=$importePasivoFila+$montoRetencion;
                $debeRet=$montoRetencion;
                $haberRet=0;    
              }else{
                $importePasivoFila=$importePasivoFila-$montoRetencion;
                $debeRet=0;
                $haberRet=$montoRetencion;
              }
             
             //DATOS PARA EL DETALLE DEL COMPROBANTE CUENTA - RETENCION  
             $montoRetencion=number_format(($montoRetencion), 2, '.', '');   
             $importe=number_format(($importe), 2, '.', '');
             $cuentaRetencion=$rowRet['cod_cuenta'];  
             $cuentaAuxiliar=0;
             $n_cuenta=trim(obtieneNumeroCuenta($cuentaRetencion));
             $nom_cuenta=nameCuenta($cuentaRetencion);
             $inicioNumeroRet=$n_cuenta[0];
             $unidadareaRet=obtenerUnidadAreaCentrosdeCostos($inicioNumeroRet);////////////////////////unidad y area para el detalle
             if($unidadareaRet[0]==0){
                   $unidadDetalleRet=$unidadDetalle;
                   $areaRet=$area;
             }else{
                  $unidadDetalleRet=$unidadDetalle;
                   $areaRet=$area;
             }
             
             $unidadDetalleGrupal=$unidadDetalleRet;
             $areaDetalleGrupal=$areaRet;
             //AGREGAR LOS DATOS AL ARRAY DE LA RETENCION ($J POR SI HAY MAS DE UN DETALLE DE LA RETENCION)
             $retenciones[$j]['cuenta']=$cuentaRetencion;
             $retenciones[$j]['unidad']=$unidadDetalleRet;
             $retenciones[$j]['area']=$areaRet;
             $retenciones[$j]['debe']=$debeRet;
             $retenciones[$j]['haber']=$haberRet;
             $retenciones[$j]['glosa']=$glosaX." - ".$glosaDetalle;
             $retenciones[$j]['numero']=$ii; 
             $retenciones[$j]['debe_haber']=$debehaberX;
             $retenciones[$j]['conf_retencion']=$rowNuevo['cod_confretencion'];
             $j++;
          }

          //FIN CARGAR DETALLE DE LAS RETENCIONES AL ARRAY

         $i=$ii;     
            
            
          //DATOS PARA AGREGAR CUENTA DE GASTO CON RETENCION
            $haber=0;
            $debe=$importeRetencionGasto;
            $sumaDevengado+=$importePasivoFila;  
            $debe=number_format(($debe), 2, '.', ''); 

          //INSERTAR CUENTA DE GASTO  
           if(verificarListaDistribucionGastoSolicitudRecurso($codigo)==0){
              $codComprobanteDetalle=obtenerCodigoComprobanteDetalle();
              $sqlDetalle="INSERT INTO comprobantes_detalle (codigo,cod_comprobante, cod_cuenta, cod_cuentaauxiliar, cod_unidadorganizacional, cod_area, debe, haber, glosa, orden,cod_actividadproyecto,cod_accnum) 
              VALUES ('$codComprobanteDetalle','$codComprobante', '$cuenta', '$cuentaAuxiliar', '$unidadDetalle', '$area', '$debe', '$haber', '$glosaDetalle', '$i','$codActividadproyecto','$codAccNum')";
              $stmtDetalle = $dbh->prepare($sqlDetalle);
              $flagSuccessDetalle=$stmtDetalle->execute();
           }else{
              include "distribucionComprobanteDevengado.php";
           }           

           $totalRetencion=0;  

          //AGREGAR DETALLE DE LAS RETENCIONES AL COMPROBANTE

              for ($j=0; $j < count($retenciones); $j++) { 
              $cuentaRetencion=$retenciones[$j]['cuenta'];
              $unidadDetalleRet=$retenciones[$j]['unidad'];
              $areaRet=$retenciones[$j]['area'];
              $debeRet=$retenciones[$j]['debe'];
              $haberRet=$retenciones[$j]['haber'];
              $glosaX=$retenciones[$j]['glosa'];
              $ii=$retenciones[$j]['numero']; 
              
              if($cuentaRetencion!=0){ //SOLO LOS DETALLES DE LA RETENCION
               if($retenciones[$j]['debe_haber']==1){
                 $totalRetencion+=(float)$debeRet;
               }else{
                 $totalRetencion+=(float)$haberRet;
               }   
              
               if($numeroRetencionFactura!=1){ //poner UNA SOLA RETENCION
                 $codComprobanteDetalle=obtenerCodigoComprobanteDetalle();
                 $sqlDetalle="INSERT INTO comprobantes_detalle (codigo,cod_comprobante, cod_cuenta, cod_cuentaauxiliar, cod_unidadorganizacional, cod_area, debe, haber, glosa, orden) 
               VALUES ('$codComprobanteDetalle','$codComprobante', '$cuentaRetencion', '$cuentaAuxiliar', '$unidadDetalleRet', '$areaRet', '$debeRet', '$haberRet', '$glosaX', '$ii')";
                 $stmtDetalle = $dbh->prepare($sqlDetalle);
                 $flagSuccessDetalle=$stmtDetalle->execute();

                 /*$sqlActualizarFaturas="UPDATE facturas_compra set cod_comprobantedetalle=$codComprobanteDetalle  where cod_solicitudrecursodetalle=$codSolicitudDetalleOrigen";
                 $stmtFacturas = $dbh->prepare($sqlActualizarFaturas);
                 $stmtFacturas->execute();*/
                }
               }
             }//FIN DE FOR RETENCIONES

        $totalRetencion=0;
      } // FIN IF CON RETENCION

       //PASIVO A DETALLE DEL COMPROBANTE
       //datos para el pasivo
          $i++;            

            $tituloFactura="";
            if(obtenerNumeroFacturaSolicitudRecursoDetalle($rowNuevo['codigo'])!=""){
              $tituloFactura="F/".obtenerNumeroFacturaSolicitudRecursoDetalle($rowNuevo['codigo'])." - ";
            }

            $glosaDetalleProv="Beneficiario: ".nameProveedor($rowNuevo['cod_proveedor'])." ".str_replace("-", "",$rowNuevo['glosa'])." ".$tituloFactura." ".$datosServicio." ".$glosa;

            $unidadDetalleProv=$unidadDetalle;
            $areaProv=$area;


            $codProveedorEstado=$codProveedor;
            //   

             $glosaDetalleGeneral=" ".$glosaDetalleProv;
  
    }//FIN WHILE DETALLES DE SOLICITUD

$glosaResumidoTit="Pago Proyecto Beneficiario: (".implode(",", $glosaResumidoArray).")";
//ACTUALIZAR LA GLOSA DEL COMPROBANTE CABECERA 
    $sqlUpdate="UPDATE comprobantes SET glosa='$glosaResumidoTit' WHERE codigo=$codComprobante";
    $stmtUpdate = $dbh->prepare($sqlUpdate);
    $flagSuccessCompro=$stmtUpdate->execute();

//FIN DE CREACION DEL COPMROBANTE


/* ACTUALIZAR ESTADOS DE LA SOLICITUD A CONTABILIZADO*/  
if($flagSuccessCompro==true){
  $sqlUpdate="UPDATE solicitud_recursos SET  cod_estadosolicitudrecurso=5,devengado=0 where codigo=$codigo";
  $stmtUpdate = $dbh->prepare($sqlUpdate);
  $flagSuccess=$stmtUpdate->execute();

//enviar propuestas para la actualizacion de ibnorca
    $fechaHoraActual=date("Y-m-d H:i:s");
    $idTipoObjeto=2708;
    $idObjeto=2725; //regristado
    $obs="Solicitud Contabilizada";
    if(isset($_POST['u'])){
       $u=$_POST['u'];
       actualizarEstadosObjetosIbnorca($idTipoObjeto,$idObjeto,$u,$codigo,$fechaHoraActual,$obs);    
     }else{
       actualizarEstadosObjetosIbnorca($idTipoObjeto,$idObjeto,$globalUser,$codigo,$fechaHoraActual,$obs);    
     }

  //actualizar al estado pagado 
    $sqlUpdate="UPDATE solicitud_recursos SET  cod_estadosolicitudrecurso=8 where codigo=$codigo";
    $stmtUpdate = $dbh->prepare($sqlUpdate);
    $flagSuccess=$stmtUpdate->execute();
  }


    $glosaResumido.="\n";
}//FOR SOLICITUDES


    //ACTUALIZAR LA GLOSA DEL COMPROBANTE CABECERA 
    $sqlUpdate="UPDATE comprobantes SET glosa='$glosaResumidoTit' WHERE codigo=$codComprobante";
    $stmtUpdate = $dbh->prepare($sqlUpdate);
    $flagSuccessCompro=$stmtUpdate->execute();

$cuentaProv=obtenerValorConfiguracion(83);
$cuentaAuxiliarProv=0;
$debeProv=0;
$haberProv=$sumaDevengado;

$codComprobanteDetalle=obtenerCodigoComprobanteDetalle();
$sqlDetalle="INSERT INTO comprobantes_detalle (codigo,cod_comprobante, cod_cuenta, cod_cuentaauxiliar, cod_unidadorganizacional, cod_area, debe, haber, glosa, orden) 
            VALUES ('$codComprobanteDetalle','$codComprobante', '$cuentaProv', '$cuentaAuxiliarProv', '$unidadDetalleProv', '$areaProv', '$debeProv', '$haberProv', '$glosaResumidoTit', '$i')";
$stmtDetalle = $dbh->prepare($sqlDetalle);
$flagSuccessDetalle=$stmtDetalle->execute();

echo "0";


?>