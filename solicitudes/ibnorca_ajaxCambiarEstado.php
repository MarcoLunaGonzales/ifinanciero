<?php
require_once '../layouts/bodylogin.php';
require_once '../conexion.php';
require_once '../functions.php';
require_once '../functionsGeneral.php';
require_once 'configModule.php';

$dbh = new Conexion();


session_start();

$globalUser=$_SESSION["globalUser"];
$globalGestion=$_SESSION["globalGestion"];
$globalUnidad=$_SESSION["globalUnidad"];
$globalArea=$_SESSION["globalArea"];
$globalAdmin=$_SESSION["globalAdmin"];

$codigo=$_GET["codigo"];
$estado=$_GET["estado"];

$iEstado=obtenerEstadoIfinancieroSolicitudes($estado);
$fechaHoraActual=date("Y-m-d H:i:s");

$sqlUpdate="UPDATE solicitud_recursos SET  cod_estadosolicitudrecurso=$iEstado where codigo=$codigo";
$stmtUpdate = $dbh->prepare($sqlUpdate);
$flagSuccess=$stmtUpdate->execute();

//enviar propuestas para la actualizacion de ibnorca
    $fechaHoraActual=date("Y-m-d H:i:s");
    $idTipoObjeto=2708;
    $idObjeto=$estado; //variable desde get
    $obs=$_GET['obs']; //$obs="Registro de propuesta";
    if(isset($_GET['u'])){
    	actualizarEstadosObjetosIbnorca($idTipoObjeto,$idObjeto,$u,$codigo,$fechaHoraActual,$obs);
    }else{
    	actualizarEstadosObjetosIbnorca($idTipoObjeto,$idObjeto,$globalUser,$codigo,$fechaHoraActual,$obs);
    }
 if($estado=3){
 	//  CREAR EL COMPROBANTE DEBENGADO
 	// Preparamos
$stmtSolicitud = $dbh->prepare("SELECT sr.*,es.nombre as estado,u.abreviatura as unidad,a.abreviatura as area from solicitud_recursos sr join estados_solicitudrecursos es on sr.cod_estadosolicitudrecurso=es.codigo join unidades_organizacionales u on sr.cod_unidadorganizacional=u.codigo join areas a on sr.cod_area=a.codigo where sr.cod_estadoreferencial=1 and sr.codigo=$codigo");
// Ejecutamos
$stmtSolicitud->execute();
// bindColumn
$stmtSolicitud->bindColumn('unidad', $unidadX);
$stmtSolicitud->bindColumn('area', $areaX);
$stmtSolicitud->bindColumn('cod_simulacion', $codSimulacion);
$stmtSolicitud->bindColumn('cod_proveedor', $codProveedor);
$stmtSolicitud->bindColumn('cod_simulacionservicio', $codSimulacionServicio);
$stmtSolicitud->bindColumn('numero', $numeroSol);

while ($rowSolicitud = $stmtSolicitud->fetch(PDO::FETCH_BOUND)) {
      $unidadX=$unidadX;
      $areaX=$areaX;
      $codSimulacion=$codSimulacion;
      $codProveedor=$codProveedor;
      $codSimulacionServicio=$codSimulacionServicio;
      $numeroSol=$numeroSol;

      if($codSimulacion!=0){
        $nombreCliente="Sin Cliente";
        $nombreSimulacion=nameSimulacion($codSimulacion);
      }else{
        $nombreCliente=nameClienteSimulacionServicio($codSimulacionServicio);
        $nombreSimulacion=nameSimulacionServicio($codSimulacionServicio);
      }
}

 	//crear el comprobante
    $codComprobante=obtenerCodigoComprobante();

    $codGestion=date("Y");
    $tipoComprobante=3;
    $nroCorrelativo=numeroCorrelativoComprobante($globalGestion,$globalUnidad,3);
    $fechaHoraActual=date("Y-m-d H:i:s");
    $glosa="SOL:".$numeroSol." - ".$areaX." - ".$nombreSimulacion." COMPROBANTE DEVENGADOS";
    $userSolicitud=obtenerPersonalSolicitanteRecursos($codigo);
    $unidadSol=obtenerUnidadSolicitanteRecursos($codSolicitud);
    $areaSol=obtenerAreaSolicitanteRecursos($codSolicitud);

    $sqlInsert="INSERT INTO comprobantes (codigo, cod_empresa, cod_unidadorganizacional, cod_gestion, cod_moneda, cod_estadocomprobante, cod_tipocomprobante, fecha, numero, glosa, created_at, created_by, modified_at, modified_by) 
    VALUES ('$codComprobante', '1', '$globalUnidad', '$codGestion', '1', '1', '$tipoComprobante', '$fechaHoraActual', '$nroCorrelativo', '$glosa', '$fechaHoraActual', '$userSolicitud', '$fechaHoraActual', '$userSolicitud')";
    echo $sqlInsert;
    $stmtInsert = $dbh->prepare($sqlInsert);
    $flagSuccessComprobante=$stmtInsert->execute();
    //insertar en detalle comprobante
    $nuevosDetalles=obtenerDetalleSolicitudParaComprobante($codigo); 

        $sqlDelete="";
        $sqlDelete="DELETE from comprobantes_detalle where cod_comprobante='$codComprobante'";
        $stmtDel = $dbh->prepare($sqlDelete);
        $flagSuccess=$stmtDel->execute();
    $i=0;$codProveedor=0;$sumaDevengado=0;$nombresProveedor="";$nombreProveedor="";
    while ($rowNuevo = $nuevosDetalles->fetch(PDO::FETCH_ASSOC)) {
        $i++;
        
        if($codProveedor!=$rowNuevo['cod_proveedor']){
           if($codProveedor!=0){
            //proveedor devengado
          
          $cuentaProv=obtenerValorConfiguracion(36);
          $cuentaAuxiliarProv=obtenerCodigoCuentaAuxiliarProveedorCliente(1,$codProveedor);
          $numeroCuentaProv=trim(obtieneNumeroCuenta($cuentaProv));
          $inicioNumeroProv=$numeroCuentaProv[0];
          $unidadareaProv=obtenerUnidadAreaCentrosdeCostos($inicioNumeroProv);
          if($unidadareaProv[0]==0){
              $unidadDetalleProv=$unidadSol;
              $areaProv=$areaSol;
          }else{
              $unidadDetalleProv=$unidadareaProv[0];
              $areaProv=$unidadareaProv[1];
          }

            $debeProv=0;
            $haberProv=$sumaDevengado;
            $glosaDetalleProv=$glosa." - PROVEEDOR ".nameProveedor($codProveedor);
        
            $codComprobanteDetalle=obtenerCodigoComprobanteDetalle();
            $sqlDetalle="INSERT INTO comprobantes_detalle (codigo,cod_comprobante, cod_cuenta, cod_cuentaauxiliar, cod_unidadorganizacional, cod_area, debe, haber, glosa, orden) 
            VALUES ('$codComprobanteDetalle','$codComprobante', '$cuentaProv', '$cuentaAuxiliarProv', '$unidadDetalleProv', '$areaProv', '$debeProv', '$haberProv', '$glosaDetalleProv', '$i')";
            $stmtDetalle = $dbh->prepare($sqlDetalle);
            $flagSuccessDetalle=$stmtDetalle->execute();
           
            $sumaDevengado=0;
            $i++; 
            
           } 
         $codProveedor=$rowNuevo['cod_proveedor'];
         /*if($cambio==1){
          $cambio=0;
         }else{
           $cambio=1; 
         } */
        }
        
        $cuenta=$rowNuevo['cod_plancuenta'];
        $cuentaAuxiliar=0;
        $numeroCuenta=trim(obtieneNumeroCuenta($cuenta));
        $inicioNumero=$numeroCuenta[0];
        $unidadarea=obtenerUnidadAreaCentrosdeCostos($inicioNumero);
        if($unidadarea[0]==0){
            $unidadDetalle=$unidadSol;
            $area=$areaSol;
        }else{
            $unidadDetalle=$unidadarea[0];
            $area=$unidadarea[1];
        }
        
        $debe=$rowNuevo['monto'];
        $haber=0;
        /*if($facturaNueva==){
          $detalleFac="F/";
        }*/
        $glosaDetalle=$glosa." D/".$rowNuevo['glosa'];
        $codSolicitudDetalle=$rowNuevo['codigo'];
        if($rowNuevo['cod_confretencion']==0){
          $sumaDevengado+=$debe;
          $codComprobanteDetalle=obtenerCodigoComprobanteDetalle();
        $sqlDetalle="INSERT INTO comprobantes_detalle (codigo,cod_comprobante, cod_cuenta, cod_cuentaauxiliar, cod_unidadorganizacional, cod_area, debe, haber, glosa, orden) 
        VALUES ('$codComprobanteDetalle','$codComprobante', '$cuenta', '$cuentaAuxiliar', '$unidadDetalle', '$area', '$debe', '$haber', '$glosaDetalle', '$i')";
        $stmtDetalle = $dbh->prepare($sqlDetalle);
        $flagSuccessDetalle=$stmtDetalle->execute();  
        }else{
            //
            $codigoRet=$rowNuevo['cod_confretencion'];
            $importeOriginal=$rowNuevo['monto'];
            $ii=$i;
          // retencion de costos
      $nom_cuenta_auxiliar="";
      $importeOriginal2=0;$j=0;
      $totalRetencion=0;
    //obtener datos de retenciones
    $stmtRetenciones = $dbh->prepare("SELECT cd.*,c.porcentaje_cuentaorigen from configuracion_retenciones c join configuracion_retencionesdetalle cd on cd.cod_configuracionretenciones=c.codigo where cd.cod_configuracionretenciones=$codigoRet order by cd.codigo");
    $stmtRetenciones->execute();
   while ($rowRet = $stmtRetenciones->fetch(PDO::FETCH_ASSOC)) {
   $ii++;                         
    $porcentajeX=$rowRet['porcentaje'];                         
    $glosaX=$rowRet['glosa'];
    $debehaberX=$rowRet['debe_haber'];

    $porcentajeCuentaX=$rowRet['porcentaje_cuentaorigen'];
    if($porcentajeCuentaX>100){
      $importe=($porcentajeCuentaX/100)*$importeOriginal;
    }else{
      $importeOriginal2=($porcentajeCuentaX/100)*$importeOriginal;
      $importe=$importeOriginal;
    }
    $montoRetencion=($porcentajeX/100)*$importe;
    
    //$totalRetencion+=$montoRetencion;

    $montoRetencion=number_format($montoRetencion, 2, '.', '');   

   if($debehaberX==1){
      $debeRet=$montoRetencion;
      $haberRet=0;    
    }else{
      $debeRet=0;
      $haberRet=$montoRetencion;
    }
    $importe=number_format($importe, 2, '.', '');
    /*if($rowRet['cod_cuenta']==0){
      $n_cuenta="";
      $nom_cuenta="";
      include "addFilaVacio.php";
    }else{*/
      $cuentaRetencion=$rowRet['cod_cuenta'];  
      $cuentaAuxiliar=0;
      $n_cuenta=trim(obtieneNumeroCuenta($cuentaRetencion));
      $nom_cuenta=nameCuenta($cuentaRetencion);
      $inicioNumeroRet=$n_cuenta[0];
      $unidadareaRet=obtenerUnidadAreaCentrosdeCostos($inicioNumeroRet);
      if($unidadareaRet[0]==0){
            $unidadDetalleRet=$unidadSol;
            $areaRet=$areaSol;
      }else{
           $unidadDetalleRet=$unidadareaRet[0];
            $areaRet=$unidadareaRet[1];
      }
    
      $retenciones[$j]['cuenta']=$cuentaRetencion;
      $retenciones[$j]['unidad']=$unidadDetalleRet;
      $retenciones[$j]['area']=$areaRet;
      $retenciones[$j]['debe']=$debeRet;
      $retenciones[$j]['haber']=$haberRet;
      $retenciones[$j]['glosa']=$glosaX." D/".$rowNuevo['glosa'];
      $retenciones[$j]['numero']=$ii; 
      $retenciones[$j]['debe_haber']=$debehaberX;

    /*}*/
    $j++;

   // $sumaDevengado+=$totalRetencion;
  }

  $i=$ii;     

        
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

        //$sumaDevengado+=$totalRetencion;   
        }

          // fin de retencion 
        $haber=0;
        
        //$glosaDetalle.="( ".$totalRetencion." --- ".$rowNuevo['monto'].")"; 
        if($porcentajeCuentaX<=100){
          $debe=$importeOriginal2;
          $sumaDevengado+=$importeOriginal; 
          $debe=number_format($debe, 2, '.', ''); 
        }else{
          $debe=$importe;
          $sumaDevengado+=$importeOriginal; 
          $debe=number_format($debe, 2, '.', ''); 
        }
        
        $codComprobanteDetalle=obtenerCodigoComprobanteDetalle();
        $sqlDetalle="INSERT INTO comprobantes_detalle (codigo,cod_comprobante, cod_cuenta, cod_cuentaauxiliar, cod_unidadorganizacional, cod_area, debe, haber, glosa, orden) 
        VALUES ('$codComprobanteDetalle','$codComprobante', '$cuenta', '$cuentaAuxiliar', '$unidadDetalle', '$area', '$debe', '$haber', '$glosaDetalle', '$i')";
        $stmtDetalle = $dbh->prepare($sqlDetalle);
        $flagSuccessDetalle=$stmtDetalle->execute();

      //}
      $totalRetencion=0;
     } //fin else
          
        
    }  

     if($sumaDevengado!=0){
        //proveedor devengado
          $i++;
          $cuentaProv=obtenerValorConfiguracion(36);
          $cuentaAuxiliarProv=obtenerCodigoCuentaAuxiliarProveedorCliente(1,$codProveedor);
          $numeroCuentaProv=trim(obtieneNumeroCuenta($cuentaProv));
          $inicioNumeroProv=$numeroCuentaProv[0];
          $unidadareaProv=obtenerUnidadAreaCentrosdeCostos($inicioNumeroProv);
          if($unidadareaProv[0]==0){
              $unidadDetalleProv=$unidadSol;
              $areaProv=$areaSol;
          }else{
              $unidadDetalleProv=$unidadareaProv[0];
              $areaProv=$unidadareaProv[1];
          }

            $debeProv=0;
            $haberProv=$sumaDevengado;
            $glosaDetalleProv=$glosa." - PROVEEDOR: ".nameProveedor($codProveedor);
        
            $codComprobanteDetalle=obtenerCodigoComprobanteDetalle();
            $sqlDetalle="INSERT INTO comprobantes_detalle (codigo,cod_comprobante, cod_cuenta, cod_cuentaauxiliar, cod_unidadorganizacional, cod_area, debe, haber, glosa, orden) 
            VALUES ('$codComprobanteDetalle','$codComprobante', '$cuentaProv', '$cuentaAuxiliarProv', '$unidadDetalleProv', '$areaProv', '$debeProv', '$haberProv', '$glosaDetalleProv', '$i')";
            $stmtDetalle = $dbh->prepare($sqlDetalle);
            $flagSuccessDetalle=$stmtDetalle->execute();

            $codProveedorEstado=$codProveedor;
            //estado de cuentas devengado
            $codEstadoCuenta=obtenerCodigoEstadosCuenta();
              $sqlDetalleEstadoCuenta="INSERT INTO estados_cuenta (codigo,cod_comprobantedetalle, cod_plancuenta, monto, cod_proveedor, fecha,cod_comprobantedetalleorigen,cod_cuentaaux) 
              VALUES ('$codEstadoCuenta','$codComprobanteDetalle', '0', '$haberProv', '$codProveedorEstado', '$fechaHoraActual','0','$cuentaAuxiliarProv')";
              $stmtDetalleEstadoCuenta = $dbh->prepare($sqlDetalleEstadoCuenta);
              $stmtDetalleEstadoCuenta->execute();
             
             echo $sqlDetalleEstadoCuenta."";
            //

          //actualizamos con el codigo de comprobante detalle la solicitud recursos detalle
          
          $sqlUpdateSolicitudRecursoDetalle="UPDATE solicitud_recursosdetalle SET cod_proveedor=$codProveedorEstado,cod_estadocuenta=$codEstadoCuenta where codigo=$codSolicitudDetalle";
          $stmtUpdateSolicitudRecursoDetalle = $dbh->prepare($sqlUpdateSolicitudRecursoDetalle);
          $stmtUpdateSolicitudRecursoDetalle->execute();

          echo $sqlUpdateSolicitudRecursoDetalle."";
    }    
        

    //fin de crear comprobante
 }   
  

?>
