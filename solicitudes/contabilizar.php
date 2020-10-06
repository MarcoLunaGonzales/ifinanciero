<?php
require_once '../layouts/bodylogin.php';
require_once '../conexion.php';
require_once '../functions.php';
require_once '../functionsGeneral.php';
require_once 'configModule.php';

$dbh = new Conexion();

$codigo=$_GET["cod"];
session_start();

$globalUser=$_SESSION["globalUser"];
$globalGestion=$_SESSION["globalGestion"];
$globalMes=$_SESSION['globalMes'];
$globalUnidad=$_SESSION["globalUnidad"];
$globalArea=$_SESSION["globalArea"];
$globalAdmin=$_SESSION["globalAdmin"];
$globalNombreGestion=$_SESSION["globalNombreGestion"];
$fechaHoraActual=date("Y-m-d H:i:s");
$userAdmin=obtenerValorConfiguracion(74);



$deven=1;
$tipoComprobante=3;
//comprobante devengado o pagado
if(isset($_GET['deven'])){
  $deven=(int)$_GET['deven']; 
  if($deven==0){
   $tipoComprobante=2;  
  }
}


//  CREAR EL COMPROBANTE DEVENGADO

//INICIO DE VARIABLES
$glosaDetalleGeneral="";


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


    //glosa detalle
    //"".." F/".$numeroFac." ".$proveedorX." ".$detalleX
    $IdTipo=obtenerTipoServicioPorIdServicio($idServicioX);
    $codObjeto=obtenerCodigoObjetoServicioPorIdSimulacion($codSimulacionServicio);
    $datosServicio="";
    if(obtenerServiciosTipoObjetoNombre($codObjeto)!=""){
      $datosServicio.=obtenerServiciosTipoObjetoNombre($codObjeto);  
    }

    if(obtenerServiciosClaServicioTipoNombre($IdTipo)!=""){
      $datosServicio.=obtenerServiciosClaServicioTipoNombre($IdTipo);  
    }
//
    $glosa="Beneficiario: ".obtenerProveedorSolicitudRecursos($codigo)." ".$datosServicio."  F/".$facturaCabecera." ".$nombreCliente." SR ".$numeroSol;
    $userSolicitud=obtenerPersonalSolicitanteRecursos($codigo);
    $unidadSol=$cod_unidadX;
    $areaSol=$cod_areaX;

//FIN DE DATOS



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
      $unidadX=$unidadX;
      $areaX=$areaX;
      $cod_unidadX=$cod_unidadX;
      $cod_areaX=$cod_areaX;
      $codSimulacion=$codSimulacion;
      $codProveedor=$codProveedor;
      $codSimulacionServicio=$codSimulacionServicio;
      $numeroSol=$numeroSol;
      if($codSimulacion!=0){
        $nombreCliente="";
        $nombreSimulacion=nameSimulacion($codSimulacion);
      }else{
        $nombreCliente=nameClienteSimulacionServicio($codSimulacionServicio);
        $nombreSimulacion=nameSimulacionServicio($codSimulacionServicio);
      }
      $glosa=$nombreCliente." SR ".$numeroSol;
}

//FIN DE LA SOLICITUD CABECERA


//CREAR COMPROBANTE EN LA OFICINA CONFIGURADA DEFECTO  LA PAZ
if($cod_unidadX!=3000){
  $cod_unidadX=obtenerValorConfiguracion(73);
}
$nroCorrelativo=numeroCorrelativoComprobante($globalGestion,$cod_unidadX,$tipoComprobante,$globalMes);    
$facturaCabecera=obtenerNumeroFacturaSolicitudRecursos($codigo);

//CREACION DEL COMPROBANTE
    if(isset($_GET['existe'])&&verificarEdicionComprobanteUsuario($globalUser)!=0){
      $codComprobante=$_GET['existe'];   
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
      $codComprobante=obtenerCodigoComprobante();
      $sqlInsert="INSERT INTO comprobantes (codigo, cod_empresa, cod_unidadorganizacional, cod_gestion, cod_moneda, cod_estadocomprobante, cod_tipocomprobante, fecha, numero, glosa, created_at, created_by, modified_at, modified_by) 
      VALUES ('$codComprobante', '1', '$cod_unidadX', '$globalNombreGestion', '1', '1', '$tipoComprobante', '$fechaHoraActual', '$nroCorrelativo', '$glosa', '$fechaHoraActualSitema', '$globalUser', '$fechaHoraActualSitema', '$globalUser')";
      //echo $sqlInsert;
      $stmtInsert = $dbh->prepare($sqlInsert);
      $flagSuccessComprobante=$stmtInsert->execute();
    }

    if($flagSuccessComprobante==true){
      if(isset($_GET["personal_encargado"])){
       //insertamos la distribucion
        $sqlDel="DELETE FROM solicitud_recursosencargado where cod_solicitudrecurso=$codigo";
        $stmtDel = $dbh->prepare($sqlDel);
        $stmtDel->execute();
  
        if($_GET["personal_encargado"]>0){
        $codEncargado=$_GET["personal_encargado"];
        $sqlInsert="INSERT INTO solicitud_recursosencargado (cod_solicitudrecurso,cod_personal) 
        VALUES ('$codigo','$codEncargado')";
        $stmtInsert = $dbh->prepare($sqlInsert);
        $stmtInsert->execute();  
        } 
      }


    $sqlUpdateSolicitud="UPDATE solicitud_recursos SET cod_comprobante=$codComprobante where codigo=$codigo";
    $stmtUpdateSolicitudRecurso = $dbh->prepare($sqlUpdateSolicitud);
    $stmtUpdateSolicitudRecurso->execute();
    
    $sqlDelete="";
    $sqlDelete="DELETE from comprobantes_detalle where cod_comprobante='$codComprobante'";
    $stmtDel = $dbh->prepare($sqlDelete);
    $flagSuccess=$stmtDel->execute();



//FIN CREACION CABECERA COMPROBANTE

//INICIO CREACION DE DETALLES EN COMPROBANTE
    /*Lista detalles de la solicitud de recursos*/
    $nuevosDetalles=obtenerDetalleSolicitudParaComprobante($codigo); 

//VARIABLES INICIO DETALLES
    $i=0;
    $codProveedor=0;
    $sumaDevengado=0;
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
        $tituloFactura="";
        if(obtenerNumeroFacturaSolicitudRecursoDetalle($rowNuevo['codigo'])!=""){
          $numeroFacturas=obtenerFacturasSolicitudDetalleArray($rowNuevo['codigo']);
          $numerosFacturasDetalle=[];
          for ($y=0; $y < count($numeroFacturas); $y++) { 
            $numerosFacturasDetalle[$y]=$numeroFacturas[$y][1];
          }
          $tituloFactura="F/ ".implode($numerosFacturasDetalle,',')." - ";
        }
        $glosaDetalle="Beneficiario: ".nameProveedor($rowNuevo['cod_proveedor'])." ".str_replace("-", "",$rowNuevo['glosa'])." ".$tituloFactura." ".$datosServicio." ".$glosa;
        $glosaDetalleRetencion="Beneficiario: ".nameProveedor($rowNuevo['cod_proveedor'])." ".str_replace("-", "",$rowNuevo['glosa'])." ".$datosServicio." ".$glosa;
        

        if($codProveedor!=$rowNuevo['cod_proveedor']){
           if($codProveedor!=0){
            //proveedor devengado //para cuando cambie de proveedor (ULTIMO PROVEEEDOR)
          
            $sumaDevengado=0;
            $i++; 
            
           } 
         $codProveedor=$rowNuevo['cod_proveedor'];
        }
        
         //CARGAR UNIDAD Y AREA DEL DETALLE CENTRO DE COSTOS
        if($unidadarea[0]==0){
            $unidadDetalle=$rowNuevo['cod_unidadorganizacional'];
            $area=$rowNuevo['cod_area'];
        }else{
            $unidadDetalle=$rowNuevo['cod_unidadorganizacional'];
            $area=$rowNuevo['cod_area'];
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
        if($rowNuevo['cod_confretencion']==0){
          if(verificarListaDistribucionGastoSolicitudRecurso($codigo)==0){
            //detalle comprobante SIN RETENCION ///////////////////////////////////////////////////////////////
                 $sumaDevengado=$debe;
                 $codComprobanteDetalle=obtenerCodigoComprobanteDetalle(); 
                 $sqlDetalle="INSERT INTO comprobantes_detalle (codigo,cod_comprobante, cod_cuenta, cod_cuentaauxiliar, cod_unidadorganizacional, cod_area, debe, haber, glosa, orden) 
                 VALUES ('$codComprobanteDetalle','$codComprobante', '$cuenta', '$cuentaAuxiliar', '$unidadDetalle', '$area', '$debe', '$haber', '$glosaDetalle', '$i')";
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
            //importe de la factura
            if($rowNuevo['cod_confretencion']==8){//||$rowNuevo['cod_confretencion']==10
              $importeOriginalAux=$importeOriginal;
  
              $importeOriginal=obtenerMontoTotalFacturasSolicituRecurso($codSolicitudDetalleOrigen);
              $importeRetencion=(porcentRetencion($codigoRet)/100)*$importeOriginal;
              $importeExentoIva=obtenerMontoGastoTotalFacturasSolicituRecurso($codSolicitudDetalleOrigen);  
              $importeRetencion=($importeRetencion)+$importeExentoIva;


              $importeRetencionGasto=(porcentRetencion($codigoRet)/100)*$importeOriginalAux;
              $importeRetencionGasto+=$importeExentoIva;
            }
            
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
             
             //IMPORTE POR FACTURA

             if($rowNuevo['cod_confretencion']==8){ //||$rowNuevo['cod_confretencion']==10
              //RETENCION POR FACTURAS
              $facturasSolicitud=obtenerFacturasSolicitudDetalleArray($codSolicitudDetalleOrigen);
                for ($fac=0; $fac < count($facturasSolicitud); $fac++) { 
                   //MONTO DE LA RETENCION
                   $montoRetencion=($porcentajeX/100)*$facturasSolicitud[$fac][0];
            
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
                  //$importe=number_format(($facturasSolicitud[$fac]['monto']), 2, '.', '');
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
                 $retenciones[$j]['glosa']=$glosaX." - ".$glosaDetalleRetencion. " F:".$facturasSolicitud[$fac][1];
                 $retenciones[$j]['numero']=$ii; 
                 $retenciones[$j]['debe_haber']=$debehaberX;
                 $retenciones[$j]['conf_retencion']=$rowNuevo['cod_confretencion'];
                 //echo "CODIGO FACTURA: ".$facturasSolicitud[$fac][1];
                 $j++;
                }
             }else{

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
           
          }

          //FIN CARGAR DETALLE DE LAS RETENCIONES AL ARRAY

         $i=$ii;     
            
           /* if($porcentajeCuentaX>100){
               $importeOriginalSinRetencion+=$montoRetencion;
               $importeOriginalSinRetencion+=$montoRetencion;
             }else{
               if($porcentajeCuentaX==100){
                 $importeOriginalSinRetencion+=0;
               }else{
                 $importeOriginalSinRetencion-=$montoRetencion;
               }
             }*/
            
          //DATOS PARA AGREGAR CUENTA DE GASTO CON RETENCION
            $haber=0;
            $debe=$importeRetencionGasto;
            $sumaDevengado=$importePasivoFila;  
            $debe=number_format(($debe), 2, '.', ''); 

          //INSERTAR CUENTA DE GASTO  
           if(verificarListaDistribucionGastoSolicitudRecurso($codigo)==0){
                 $codComprobanteDetalle=obtenerCodigoComprobanteDetalle();
                 $sqlDetalle="INSERT INTO comprobantes_detalle (codigo,cod_comprobante, cod_cuenta, cod_cuentaauxiliar, cod_unidadorganizacional, cod_area, debe, haber, glosa, orden) 
                 VALUES ('$codComprobanteDetalle','$codComprobante', '$cuenta', '$cuentaAuxiliar', '$unidadDetalle', '$area', '$debe', '$haber', '$glosaDetalle', '$i')";
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

                 $sqlActualizarFaturas="UPDATE facturas_compra set cod_comprobantedetalle=$codComprobanteDetalle  where cod_solicitudrecursodetalle=$codSolicitudDetalleOrigen";
                 $stmtFacturas = $dbh->prepare($sqlActualizarFaturas);
                 $stmtFacturas->execute();
                 if($retenciones[$j]['conf_retencion']==10&&$deven==1){
                   $codEstadoCuenta=obtenerCodigoEstadosCuenta();
                   //datos del proveedor para le estado de cuentas
                   $codProveedorEstado=$codProveedor;
                   $nomProveedor=nameProveedor($codProveedor);
                   //CREAR CUENTA AUXILIAR SI NO EXISTE 
                   if(obtenerCodigoCuentaAuxiliarProveedorClienteCuenta(1,$codProveedor,$cuentaRetencion)==0){
                     $codEstado="1";
                     $codReferencia1="0";
                     if($codProveedor==36272){
                       $codReferencia1="36272";
                     }
                     $stmtInsertAux = $dbh->prepare("INSERT INTO cuentas_auxiliares (nombre, cod_estadoreferencial, cod_cuenta,  cod_tipoauxiliar, cod_proveedorcliente,referencia1) 
                     VALUES ('$nomProveedor', $codEstado,$cuentaRetencion, 1, $codProveedor,$codReferencia1)");
                     $stmtInsertAux->execute();
                   }
                   $cuentaAuxiliarProv=obtenerCodigoCuentaAuxiliarProveedorClienteCuenta(1,$codProveedor,$cuentaRetencion); 

                   $sqlDetalleEstadoCuenta="INSERT INTO estados_cuenta (codigo,cod_comprobantedetalle, cod_plancuenta, monto, cod_proveedor, fecha,cod_comprobantedetalleorigen,cod_cuentaaux,glosa_auxiliar) 
                   VALUES ('$codEstadoCuenta','$codComprobanteDetalle', '$cuentaRetencion', '$debeRet', '$codProveedorEstado', '$fechaHoraActual','0','$cuentaAuxiliarProv','$glosaX')";
                   $stmtDetalleEstadoCuenta = $dbh->prepare($sqlDetalleEstadoCuenta);
                   $stmtDetalleEstadoCuenta->execute();  

                    //actualizar cuenta auxiliar al detalle del comprobante
                    $sqlDetalleAuxiliar="UPDATE comprobantes_detalle set cod_cuentaauxiliar='$cuentaAuxiliarProv' where codigo='$codComprobanteDetalle'"; 
                    $stmtDetalleAuxiliar = $dbh->prepare($sqlDetalleAuxiliar);
                    $stmtDetalleAuxiliar->execute();           
                 }
                }
               }
             }//FIN DE FOR RETENCIONES

        $totalRetencion=0;
      } // FIN IF CON RETENCION


       //PASIVO A DETALLE DEL COMPROBANTE
       //datos para el pasivo
          $i++;
          $cuentaProv=obtenerCuentaPasivaSolicitudesRecursos($cuenta);
          $nomProveedor=nameProveedor($codProveedor);
       //CREAR CUENTA AUXILIAR SI NO EXISTE 
          if(obtenerCodigoCuentaAuxiliarProveedorClienteCuenta(1,$codProveedor,$cuentaProv)==0){
            $codEstado="1";
            $codReferencia1="0";
            if($codProveedor==36272){
              $codReferencia1="36272";
            }
            $stmtInsertAux = $dbh->prepare("INSERT INTO cuentas_auxiliares (nombre, cod_estadoreferencial, cod_cuenta,  cod_tipoauxiliar, cod_proveedorcliente,referencia1) 
            VALUES ('$nomProveedor', $codEstado,$cuentaProv, 1, $codProveedor,$codReferencia1)");
            $stmtInsertAux->execute();
          }

          $cuentaAuxiliarProv=obtenerCodigoCuentaAuxiliarProveedorClienteCuenta(1,$codProveedor,$cuentaProv);
          $numeroCuentaProv=trim(obtieneNumeroCuenta($cuentaProv));
          $inicioNumeroProv=$numeroCuentaProv[0];
          $unidadareaProv=obtenerUnidadAreaCentrosdeCostos($inicioNumeroProv);
          if($unidadareaProv[0]==0){
              $unidadDetalleProv=$unidadDetalle;
              $areaProv=$area;
          }else{
              $unidadDetalleProv=$unidadDetalle;
              $areaProv=$area;
          }
            $debeProv=0;
            $haberProv=$sumaDevengado;
            
            $tituloFactura="";
            if(obtenerNumeroFacturaSolicitudRecursoDetalle($rowNuevo['codigo'])!=""){
              $tituloFactura="F/".obtenerNumeroFacturaSolicitudRecursoDetalle($rowNuevo['codigo'])." - ";
            }

            $glosaDetalleProv="Beneficiario: ".nameProveedor($rowNuevo['cod_proveedor'])." ".str_replace("-", "",$rowNuevo['glosa'])." ".$tituloFactura." ".$datosServicio." ".$glosa;

            //validacion si la solicitud es agrupada
            if($numeroRetencionFactura!=1){ 
            
            if($deven==0){
              $cuentaProv=obtenerValorConfiguracion(82);
              $cuentaAuxiliarProv=0;
            }
  
            $codigoDivision=obtenerDivisionCodigoDetalle($codSolicitudDetalleOrigen);
            if($codigoDivision!=0&&($deven==1)){
                 $sqlDivisionPago="SELECT porcentaje from solicitud_recursosdivisionpago_detalle where cod_divisionpago=$codigoDivision";
                 $stmtDivisionPago = $dbh->prepare($sqlDivisionPago);
                 $stmtDivisionPago->execute();
                 while ($rowDivision = $stmtDivisionPago->fetch(PDO::FETCH_ASSOC)) {
                  $porcentajeDivision=$rowDivision['porcentaje'];
                  $glosaDetalleDivision=$glosaDetalleProv." (".$porcentajeDivision." %)";
                  $haberDivision=$haberProv*($porcentajeDivision/100);
                  $codComprobanteDetalle=obtenerCodigoComprobanteDetalle();
                  $sqlDetalle="INSERT INTO comprobantes_detalle (codigo,cod_comprobante, cod_cuenta, cod_cuentaauxiliar, cod_unidadorganizacional, cod_area, debe, haber, glosa, orden) 
                  VALUES ('$codComprobanteDetalle','$codComprobante', '$cuentaProv', '$cuentaAuxiliarProv', '$unidadDetalleProv', '$areaProv', '$debeProv', '$haberDivision', '$glosaDetalleDivision', '$i')";
                  $stmtDetalle = $dbh->prepare($sqlDetalle);
                  $flagSuccessDetalle=$stmtDetalle->execute();
                  
                  print_r($sqlDetalle); 
                  $codProveedorEstado=$codProveedor;
                  if($deven==1){
                    //estado de cuentas devengado
                    $codEstadoCuenta=obtenerCodigoEstadosCuenta();
                    $sqlDetalleEstadoCuenta="INSERT INTO estados_cuenta (codigo,cod_comprobantedetalle, cod_plancuenta, monto, cod_proveedor, fecha,cod_comprobantedetalleorigen,cod_cuentaaux,glosa_auxiliar) 
                    VALUES ('$codEstadoCuenta','$codComprobanteDetalle', '$cuentaProv', '$haberDivision', '$codProveedorEstado', '$fechaHoraActual','0','$cuentaAuxiliarProv','$glosaDetalleDivision')";
                    $stmtDetalleEstadoCuenta = $dbh->prepare($sqlDetalleEstadoCuenta);
                    $stmtDetalleEstadoCuenta->execute();             
                    //actualizamos con el codigo de comprobante detalle la solicitud recursos detalle 
                    $sqlUpdateSolicitudRecursoDetalle="UPDATE solicitud_recursosdetalle SET cod_estadocuenta='$codEstadoCuenta',glosa_comprobantedetalle='$glosaDetalleDivision' where codigo='$codSolicitudDetalleOrigen'";
                    $stmtUpdateSolicitudRecursoDetalle = $dbh->prepare($sqlUpdateSolicitudRecursoDetalle);
                    $stmtUpdateSolicitudRecursoDetalle->execute();
                  }
                 }//fin de while division

            }else{
               $codComprobanteDetalle=obtenerCodigoComprobanteDetalle();
               $sqlDetalle="INSERT INTO comprobantes_detalle (codigo,cod_comprobante, cod_cuenta, cod_cuentaauxiliar, cod_unidadorganizacional, cod_area, debe, haber, glosa, orden) 
               VALUES ('$codComprobanteDetalle','$codComprobante', '$cuentaProv', '$cuentaAuxiliarProv', '$unidadDetalleProv', '$areaProv', '$debeProv', '$haberProv', '$glosaDetalleProv', '$i')";
               $stmtDetalle = $dbh->prepare($sqlDetalle);
               $flagSuccessDetalle=$stmtDetalle->execute();

               print_r($sqlDetalle); 
               $codProveedorEstado=$codProveedor;
               if($deven==1){
                 //estado de cuentas devengado
                 $codEstadoCuenta=obtenerCodigoEstadosCuenta();
                 $sqlDetalleEstadoCuenta="INSERT INTO estados_cuenta (codigo,cod_comprobantedetalle, cod_plancuenta, monto, cod_proveedor, fecha,cod_comprobantedetalleorigen,cod_cuentaaux,glosa_auxiliar) 
                 VALUES ('$codEstadoCuenta','$codComprobanteDetalle', '$cuentaProv', '$haberProv', '$codProveedorEstado', '$fechaHoraActual','0','$cuentaAuxiliarProv','$glosaDetalleProv')";
                 $stmtDetalleEstadoCuenta = $dbh->prepare($sqlDetalleEstadoCuenta);
                 $stmtDetalleEstadoCuenta->execute();             
                 //actualizamos con el codigo de comprobante detalle la solicitud recursos detalle 
                 $sqlUpdateSolicitudRecursoDetalle="UPDATE solicitud_recursosdetalle SET cod_estadocuenta='$codEstadoCuenta',glosa_comprobantedetalle='$glosaDetalleProv' where codigo='$codSolicitudDetalleOrigen'";
                 $stmtUpdateSolicitudRecursoDetalle = $dbh->prepare($sqlUpdateSolicitudRecursoDetalle);
                 $stmtUpdateSolicitudRecursoDetalle->execute();
               }
             }  

            }
            // FIN CUENTA PASIVA   

             $glosaDetalleGeneral=" ".$glosaDetalleProv;
  
    }//FIN WHILE DETALLES DE SOLICITUD



    //ACTUALIZAR LA GLOSA DEL COMPROBANTE CABECERA 
    $sqlUpdate="UPDATE comprobantes SET glosa='$glosaDetalleGeneral' WHERE codigo=$codComprobante";
    $stmtUpdate = $dbh->prepare($sqlUpdate);
    $flagSuccessCompro=$stmtUpdate->execute();

//FIN DE CREACION DEL COPMROBANTE



/* ACTUALIZAR ESTADOS DE LA SOLICITUD A CONTABILIZADO*/  
if($flagSuccessCompro==true){
  $datosSolicitud=obtenerDatosSolicitudRecursos($codigo);
    $correoPersonal=$datosSolicitud['email_empresa'];
    $descripcionEstado=obtenerNombreEstadoSol(5);
    if($correoPersonal!=""){
      $envioCorreoPersonal=enviarCorreoSimple($correoPersonal,'CAMBIO DE ESTADO - SOLICITUD DE RECURSOS, Nº : '.$datosSolicitud['numero'],'Estimado(a) '.$datosSolicitud['solicitante'].', el sistema IFINANCIERO le notifica que su Solicitud de Recursos cambio del estado <b>'.$datosSolicitud['estado'].'</b> a <b>'.$descripcionEstado.'</b>. <br> Personal que realizo el cambio:'.namePersonalCompleto($globalUser)."<br>Numero de Solicitud:".$datosSolicitud['numero']."<br>Estado Anterior: <b>".$datosSolicitud['estado']."</b><br>Estado Actual: <b>".$descripcionEstado."</b><br><br>Saludos - IFINANCIERO");  
    }

  $sqlUpdate="UPDATE solicitud_recursos SET  cod_estadosolicitudrecurso=5,devengado=$deven where codigo=$codigo";
  $stmtUpdate = $dbh->prepare($sqlUpdate);
  $flagSuccess=$stmtUpdate->execute();

//enviar propuestas para la actualizacion de ibnorca
    $fechaHoraActual=date("Y-m-d H:i:s");
    $idTipoObjeto=2708;
    $idObjeto=2725; //regristado
    $obs="Solicitud Contabilizada";
    if(isset($_GET['u'])){
       $u=$_GET['u'];
       actualizarEstadosObjetosIbnorca($idTipoObjeto,$idObjeto,$u,$codigo,$fechaHoraActual,$obs);    
     }else{
       actualizarEstadosObjetosIbnorca($idTipoObjeto,$idObjeto,$globalUser,$codigo,$fechaHoraActual,$obs);    
     }
    
  if($deven==0){
    $datosSolicitud=obtenerDatosSolicitudRecursos($codigo);
    $correoPersonal=$datosSolicitud['email_empresa'];
    $descripcionEstado=obtenerNombreEstadoSol(8);
    if($correoPersonal!=""){
      $envioCorreoPersonal=enviarCorreoSimple($correoPersonal,'CAMBIO DE ESTADO - SOLICITUD DE RECURSOS, Nº : '.$datosSolicitud['numero'],'Estimado(a) '.$datosSolicitud['solicitante'].', el sistema IFINANCIERO le notifica que su Solicitud de Recursos cambio del estado <b>'.$datosSolicitud['estado'].'</b> a <b>'.$descripcionEstado.'</b>. <br> Personal que realizo el cambio:'.namePersonalCompleto($globalUser)."<br>Numero de Solicitud:".$datosSolicitud['numero']."<br>Estado Anterior: <b>".$datosSolicitud['estado']."</b><br>Estado Actual: <b>".$descripcionEstado."</b><br><br>Saludos - IFINANCIERO");  
    }
    
    $sqlUpdate="UPDATE solicitud_recursos SET  cod_estadosolicitudrecurso=8 where codigo=$codigo";
    $stmtUpdate = $dbh->prepare($sqlUpdate);
    $flagSuccess=$stmtUpdate->execute();

    
  }
  
}

}else{
  $flagSuccess=false;
}//fin if($flagSuccessComprobante==true)

//       LINK DE RETORNO ("q" -> DESDE INTRANET)
if(isset($_GET['q'])){
  $q=$_GET['q'];
  $s=$_GET['s'];
  $u=$_GET['u'];
  $v=$_GET['v'];

}

if(isset($_GET['admin'])){
  if($_GET['admin']==4){
    $urlList4=$urlList7;
  }
  
  $urlList2=$urlList;
  $urlc="&q=".$q."&s=".$s."&u=".$u."&v=".$v;
  if(isset($_GET['reg'])){
    $urlList2=$urlList3;
  }
}else{
  $urlc="&q=".$q."&s=".$s."&u=".$u;
  if(isset($_GET['r'])){
    $urlc=$urlc."&r=".$_GET['r'];
  }
}
if(isset($_GET['q'])){
	$q=$_GET['q'];
  $s=$_GET['s'];
  $u=$_GET['u'];
  $v=$_GET['v'];
  if($flagSuccess==true){
	showAlertSuccessError(true,"../".$urlList4.$urlc);	
   }else{
	showAlertSuccessError(false,"../".$urlList4.$urlc);
   }
}else{
	if($flagSuccess==true){
	showAlertSuccessError(true,"../".$urlList4);	
   }else{
	showAlertSuccessError(false,"../".$urlList4);
   }
}

?>