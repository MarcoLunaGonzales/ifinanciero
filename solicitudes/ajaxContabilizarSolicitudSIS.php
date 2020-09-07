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
$tipoComprobante=3;
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
$nroCorrelativo=numeroCorrelativoComprobante($globalGestion,$cod_unidadX,3,$globalMes);    
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

$facturaCabecera=obtenerNumeroFacturaSolicitudRecursos($codigo);
$glosa="Beneficiario: ".obtenerProveedorSolicitudRecursos($codigo)." ".$datosServicio."  F/".$facturaCabecera." ".$nombreCliente." SR ".$numeroSol;
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
        if(verificarListaDistribucionGastoSolicitudRecurso($codigo)==0){
            //detalle comprobante SIN RETENCION ///////////////////////////////////////////////////////////////
            $sumaDevengado+=$debe;
            $codComprobanteDetalle=obtenerCodigoComprobanteDetalle(); 
            $sqlDetalle="INSERT INTO comprobantes_detalle (codigo,cod_comprobante, cod_cuenta, cod_cuentaauxiliar, cod_unidadorganizacional, cod_area, debe, haber, glosa, orden) 
            VALUES ('$codComprobanteDetalle','$codComprobante', '$cuenta', '$cuentaAuxiliar', '$unidadDetalle', '$area', '$debe', '$haber', '$glosaDetalle', '$i')";
            $stmtDetalle = $dbh->prepare($sqlDetalle);
            $flagSuccessDetalle=$stmtDetalle->execute();  
          }else{
            //distribuir gastos
             include "distribucionComprobanteDevengado.php";
          }


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

//ACTUALIZAR LA GLOSA DEL COMPROBANTE CABECERA 
    $sqlUpdate="UPDATE comprobantes SET glosa='$glosaDetalleGeneral' WHERE codigo=$codComprobante";
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

}//FOR SOLICITUDES

    //ACTUALIZAR LA GLOSA DEL COMPROBANTE CABECERA 
    $sqlUpdate="UPDATE comprobantes SET glosa='$glosaDetalleGeneral' WHERE codigo=$codComprobante";
    $stmtUpdate = $dbh->prepare($sqlUpdate);
    $flagSuccessCompro=$stmtUpdate->execute();

$cuentaProv=obtenerValorConfiguracion(83);
$cuentaAuxiliarProv=0;
$debeProv=0;
$haberProv=$sumaDevengado;

$codComprobanteDetalle=obtenerCodigoComprobanteDetalle();
$sqlDetalle="INSERT INTO comprobantes_detalle (codigo,cod_comprobante, cod_cuenta, cod_cuentaauxiliar, cod_unidadorganizacional, cod_area, debe, haber, glosa, orden) 
            VALUES ('$codComprobanteDetalle','$codComprobante', '$cuentaProv', '$cuentaAuxiliarProv', '$unidadDetalleProv', '$areaProv', '$debeProv', '$haberProv', '$glosaDetalleProv', '$i')";
$stmtDetalle = $dbh->prepare($sqlDetalle);
$flagSuccessDetalle=$stmtDetalle->execute();

echo "0";


?>