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

$sqlUpdate="UPDATE solicitud_recursos SET  cod_estadosolicitudrecurso=5 where codigo=$codigo";
$stmtUpdate = $dbh->prepare($sqlUpdate);
$flagSuccess=$stmtUpdate->execute();

//enviar propuestas para la actualizacion de ibnorca
    $fechaHoraActual=date("Y-m-d H:i:s");
    $idTipoObjeto=2708;
    $idObjeto=2723; //regristado
    $obs="Solicitud Contabilizada";
    if(isset($_GET['u'])){
       $u=$_GET['u'];
       actualizarEstadosObjetosIbnorca($idTipoObjeto,$idObjeto,$u,$codigo,$fechaHoraActual,$obs);    
     }else{
       actualizarEstadosObjetosIbnorca($idTipoObjeto,$idObjeto,$globalUser,$codigo,$fechaHoraActual,$obs);    
     }

    //  CREAR EL COMPROBANTE DEBENGADOç
$glosaDetalleGeneral="";
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
}
$cod_unidadX=obtenerValorConfiguracion(73); //crear comprobante devengado en LA PAZ
  //crear el comprobante
    $codComprobante=obtenerCodigoComprobante();
    
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


    $tipoComprobante=3;
    $nroCorrelativo=numeroCorrelativoComprobante($globalGestion,$cod_unidadX,3,$globalMes);
    
    $facturaCabecera=obtenerNumeroFacturaSolicitudRecursos($codigo);

    
    //glosa detalle
    //"".." F/".$numeroFac." ".$proveedorX." ".$detalleX
    $IdTipo=obtenerTipoServicioPorIdServicio($idServicioX);
    $codObjeto=obtenerCodigoObjetoServicioPorIdSimulacion($codSimulacionServicio);

    $datosServicio=obtenerServiciosTipoObjetoNombre($codObjeto)." - ".obtenerServiciosClaServicioTipoNombre($IdTipo);


    $glosa="Beneficiario: ".obtenerProveedorSolicitudRecursos($codigo)." ".$datosServicio."  F/".$facturaCabecera." ".$nombreCliente." SR ".$numeroSol;
    $userSolicitud=obtenerPersonalSolicitanteRecursos($codigo);
    $unidadSol=$cod_unidadX;
    $areaSol=$cod_areaX;
   // $glosaDetalleGeneral=$glosa;

    $sqlInsert="INSERT INTO comprobantes (codigo, cod_empresa, cod_unidadorganizacional, cod_gestion, cod_moneda, cod_estadocomprobante, cod_tipocomprobante, fecha, numero, glosa, created_at, created_by, modified_at, modified_by) 
    VALUES ('$codComprobante', '1', '$cod_unidadX', '$globalNombreGestion', '1', '1', '$tipoComprobante', '$fechaHoraActual', '$nroCorrelativo', '$glosa', '$fechaHoraActual', '$userSolicitud', '$fechaHoraActual', '$userSolicitud')";
    //echo $sqlInsert;
    $stmtInsert = $dbh->prepare($sqlInsert);
    $flagSuccessComprobante=$stmtInsert->execute();

    $sqlUpdateSolicitud="UPDATE solicitud_recursos SET cod_comprobante=$codComprobante where codigo=$codigo";
    $stmtUpdateSolicitudRecurso = $dbh->prepare($sqlUpdateSolicitud);
    $stmtUpdateSolicitudRecurso->execute();
    
    $glosa=$nombreCliente." SR ".$numeroSol;

    //insertar en detalle comprobante
    $nuevosDetalles=obtenerDetalleSolicitudParaComprobante($codigo); 

        $sqlDelete="";
        $sqlDelete="DELETE from comprobantes_detalle where cod_comprobante='$codComprobante'";
        $stmtDel = $dbh->prepare($sqlDelete);
        $flagSuccess=$stmtDel->execute();
    $i=0;$codProveedor=0;$sumaDevengado=0;$nombresProveedor="";$nombreProveedor="";
    while ($rowNuevo = $nuevosDetalles->fetch(PDO::FETCH_ASSOC)) {
        

        $cuenta=$rowNuevo['cod_plancuenta'];
        $cuentaAuxiliar=0;

        $i++;
        
        if($codProveedor!=$rowNuevo['cod_proveedor']){
           if($codProveedor!=0){
            //proveedor devengado //para cuando cambie de proveedor (ULTIMO PROVEEEDOR)
          
            $sumaDevengado=0;
            $i++; 
            
           } 
         $codProveedor=$rowNuevo['cod_proveedor'];
        }
        
        
        $numeroCuenta=trim(obtieneNumeroCuenta($cuenta));
        $inicioNumero=$numeroCuenta[0];
        $unidadarea=obtenerUnidadAreaCentrosdeCostos($inicioNumero);               ////////////////////////unidad y area para el detalle
        if($unidadarea[0]==0){
            $unidadDetalle=$rowNuevo['cod_unidadorganizacional'];
            $area=$rowNuevo['cod_area'];
        }else{
            $unidadDetalle=$rowNuevo['cod_unidadorganizacional'];
            $area=$rowNuevo['cod_area'];
        }
        
        $debe=$rowNuevo['monto'];
        $haber=0;
        /*if($facturaNueva==){
          $detalleFac="F/";
        }*/
        $glosaDetalle="Beneficiario: ".nameProveedor($rowNuevo['cod_proveedor'])." ".str_replace("-", "",$rowNuevo['glosa'])." F/".obtenerNumeroFacturaSolicitudRecursoDetalle($rowNuevo['codigo'])." - ".$datosServicio." ".$glosa;
        $codSolicitudDetalle=$rowNuevo['codigo'];
        $codSolicitudDetalleOrigen=$rowNuevo['codigo'];
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
            // SI TIENE RETENCION **********************************************************************************

            $codigoRet=$rowNuevo['cod_confretencion'];
            $importeOriginal=$rowNuevo['monto'];
            $importeRetencion=(porcentRetencion($codigoRet)/100)*$importeOriginal;
            $importePasivoFila=$importeRetencion;
            $ii=$i;
          // retencion de costos
            $nom_cuenta_auxiliar="";
            $importeOriginal2=0;
            $totalRetencion=0;
            //obtener datos de retenciones
            $stmtRetenciones = $dbh->prepare("SELECT cd.*,c.porcentaje_cuentaorigen from configuracion_retenciones c join configuracion_retencionesdetalle cd on cd.cod_configuracionretenciones=c.codigo where cd.cod_configuracionretenciones=$codigoRet and cd.cod_cuenta!=0 order by cd.codigo");
            $stmtRetenciones->execute();
            $j=0;
            while ($rowRet = $stmtRetenciones->fetch(PDO::FETCH_ASSOC)) {
            $ii++;                         
             $porcentajeX=$rowRet['porcentaje'];                         
             $glosaX=$rowRet['glosa'];
             $debehaberX=$rowRet['debe_haber'];

             $porcentajeCuentaX=$rowRet['porcentaje_cuentaorigen'];
             
             if($porcentajeCuentaX>100){
               $importe=$importeRetencion;
             }else{
               //$importeOriginal2=($porcentajeCuentaX/100)*$importeOriginal2;
               $importe=$importeOriginal;
             }
             

             $montoRetencion=($porcentajeX/100)*$importe;
             


            

             if($debehaberX==1){
                $importePasivoFila=$importePasivoFila+$montoRetencion;
                $debeRet=$montoRetencion;
                $haberRet=0;    
              }else{
                $importePasivoFila=$importePasivoFila-$montoRetencion;
                $debeRet=0;
                $haberRet=$montoRetencion;
              }
             $montoRetencion=number_format($montoRetencion, 2, '.', '');   
             $importe=number_format($importe, 2, '.', '');
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
    
             $retenciones[$j]['cuenta']=$cuentaRetencion;
             $retenciones[$j]['unidad']=$unidadDetalleRet;
             $retenciones[$j]['area']=$areaRet;
             $retenciones[$j]['debe']=$debeRet;
             $retenciones[$j]['haber']=$haberRet;
             $retenciones[$j]['glosa']=$glosaX." - ".$glosaDetalle;
             $retenciones[$j]['numero']=$ii; 
             $retenciones[$j]['debe_haber']=$debehaberX;
           $j++;
          }

         $i=$ii;     
            
            if($porcentajeCuentaX>100){
               $importeOriginalSinRetencion+=$montoRetencion;
               $importeOriginalSinRetencion+=$montoRetencion;
             }else{
               if($porcentajeCuentaX==100){
                 $importeOriginalSinRetencion+=0;
               }else{
                 $importeOriginalSinRetencion-=$montoRetencion;
               }
             }

            
            //formula solicitud de recursos

            $haber=0;
            $debe=$importeRetencion;
            $sumaDevengado=$importePasivoFila;  
            if($porcentajeCuentaX<=100){
              //$debe=$importeOriginal2;
              //$sumaDevengado=$importeOriginal2;
              // 
              $debe=number_format($debe, 2, '.', ''); 
            }else{
              //$debe=$importe;
              //$sumaDevengado=$importeOriginal; 
              $debe=number_format($debe, 2, '.', ''); 
            }
           if(verificarListaDistribucionGastoSolicitudRecurso($codigo)==0){
             //detalle comprobante CON RETENCION //////////////////////////////////////////////////////////////7
              $codComprobanteDetalle=obtenerCodigoComprobanteDetalle();
              $sqlDetalle="INSERT INTO comprobantes_detalle (codigo,cod_comprobante, cod_cuenta, cod_cuentaauxiliar, cod_unidadorganizacional, cod_area, debe, haber, glosa, orden) 
              VALUES ('$codComprobanteDetalle','$codComprobante', '$cuenta', '$cuentaAuxiliar', '$unidadDetalle', '$area', '$debe', '$haber', '$glosaDetalle', '$i')";
              $stmtDetalle = $dbh->prepare($sqlDetalle);
              $flagSuccessDetalle=$stmtDetalle->execute();
           }else{
              include "distribucionComprobanteDevengado.php";
           }           

           $totalRetencion=0;  
            //if($totalRetencion!=0){
              for ($j=0; $j < count($retenciones); $j++) { 
              $cuentaRetencion=$retenciones[$j]['cuenta'];
              $unidadDetalleRet=$retenciones[$j]['unidad'];
              $areaRet=$retenciones[$j]['area'];
              $debeRet=$retenciones[$j]['debe'];
              $haberRet=$retenciones[$j]['haber'];
              $glosaX=$retenciones[$j]['glosa'];
              $ii=$retenciones[$j]['numero']; 
              
              if($cuentaRetencion!=0){ //validar cuenta 0
               if($retenciones[$j]['debe_haber']==1){
                 $totalRetencion+=(float)$debeRet;
               }else{
                 $totalRetencion+=(float)$haberRet;
               }   
               $codComprobanteDetalle=obtenerCodigoComprobanteDetalle();
               $sqlDetalle="INSERT INTO comprobantes_detalle (codigo,cod_comprobante, cod_cuenta, cod_cuentaauxiliar, cod_unidadorganizacional, cod_area, debe, haber, glosa, orden) 
               VALUES ('$codComprobanteDetalle','$codComprobante', '$cuentaRetencion', '$cuentaAuxiliar', '$unidadDetalleRet', '$areaRet', '$debeRet', '$haberRet', '$glosaX', '$ii')";
               $stmtDetalle = $dbh->prepare($sqlDetalle);
               $flagSuccessDetalle=$stmtDetalle->execute();

               $sqlActualizarFaturas="UPDATE facturas_compra set cod_comprobantedetalle=$codComprobanteDetalle  where cod_solicitudrecursodetalle=$codSolicitudDetalleOrigen";
               $stmtFacturas = $dbh->prepare($sqlActualizarFaturas);
               $stmtFacturas->execute();

              }

              //$sumaDevengado+=$totalRetencion;   
              }

            // fin de retencion
      //}
        $totalRetencion=0;
      } //fin else *********************************** SI TIENE RETENCION ****************************************************+    

       //ASOCIAR PASIVO A DETALLE CUENTA
       //proveedor devengado
          $i++;
          $cuentaProv=obtenerCuentaPasivaSolicitudesRecursos($cuenta);
          $nomProveedor=nameProveedor($codProveedor);
          //crear cuenta auxiliar si no existe 
          if(obtenerCodigoCuentaAuxiliarProveedorCliente(1,$codProveedor)==0){
            $codEstado="1";
            $stmtInsertAux = $dbh->prepare("INSERT INTO cuentas_auxiliares (nombre, cod_estadoreferencial, cod_cuenta,  cod_tipoauxiliar, cod_proveedorcliente) 
            VALUES ('$nomProveedor', $codEstado,$cuentaProv, 1, $codProveedor)");
            $stmtInsertAux->execute();
          }

          $cuentaAuxiliarProv=obtenerCodigoCuentaAuxiliarProveedorCliente(1,$codProveedor);
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
            $glosaDetalleProv="Beneficiario: ".nameProveedor($rowNuevo['cod_proveedor'])." ".str_replace("-", "",$rowNuevo['glosa'])." F/".obtenerNumeroFacturaSolicitudRecursoDetalle($rowNuevo['codigo'])." - ".$datosServicio." ".$glosa;
        
            $codComprobanteDetalle=obtenerCodigoComprobanteDetalle();
            $sqlDetalle="INSERT INTO comprobantes_detalle (codigo,cod_comprobante, cod_cuenta, cod_cuentaauxiliar, cod_unidadorganizacional, cod_area, debe, haber, glosa, orden) 
            VALUES ('$codComprobanteDetalle','$codComprobante', '$cuentaProv', '$cuentaAuxiliarProv', '$unidadDetalleProv', '$areaProv', '$debeProv', '$haberProv', '$glosaDetalleProv', '$i')";
            $stmtDetalle = $dbh->prepare($sqlDetalle);
            $flagSuccessDetalle=$stmtDetalle->execute();
            print_r($sqlDetalle); 
            $codProveedorEstado=$codProveedor;
              //estado de cuentas devengado
              $codEstadoCuenta=obtenerCodigoEstadosCuenta();
              $sqlDetalleEstadoCuenta="INSERT INTO estados_cuenta (codigo,cod_comprobantedetalle, cod_plancuenta, monto, cod_proveedor, fecha,cod_comprobantedetalleorigen,cod_cuentaaux) 
              VALUES ('$codEstadoCuenta','$codComprobanteDetalle', '0', '$haberProv', '$codProveedorEstado', '$fechaHoraActual','0','$cuentaAuxiliarProv')";
              $stmtDetalleEstadoCuenta = $dbh->prepare($sqlDetalleEstadoCuenta);
              $stmtDetalleEstadoCuenta->execute();             
             //echo $sqlDetalleEstadoCuenta."";
              //actualizamos con el codigo de comprobante detalle la solicitud recursos detalle 
              $sqlUpdateSolicitudRecursoDetalle="UPDATE solicitud_recursosdetalle SET cod_proveedor=$codProveedorEstado,cod_estadocuenta=$codEstadoCuenta,glosa_comprobantedetalle='$glosaDetalleProv' where codigo=$codSolicitudDetalle";
              $stmtUpdateSolicitudRecursoDetalle = $dbh->prepare($sqlUpdateSolicitudRecursoDetalle);
              $stmtUpdateSolicitudRecursoDetalle->execute();

             //echo $sqlUpdateSolicitudRecursoDetalle."";
              $glosaDetalleGeneral.=" ".$glosaDetalleProv;
              

    }  
    $sqlUpdate="UPDATE comprobantes SET glosa='$glosaDetalleGeneral' WHERE codigo=$codComprobante";
    $stmtUpdate = $dbh->prepare($sqlUpdate);
    $stmtUpdate->execute();

        if($sumaDevengado!=0){
        
         }    
        
    //fin de crear comprobante

if(isset($_GET['q'])){
  $q=$_GET['q'];
  $s=$_GET['s'];
  $u=$_GET['u'];
  $v=$_GET['v'];

}

if(isset($_GET['admin'])){
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