<?php
session_start();
require_once '../conexion.php';
require_once '../functionsGeneral.php';
require_once '../functions.php';
$dbh = new Conexion();
$sqlX="SET NAMES 'utf8'";
$stmtX = $dbh->prepare($sqlX);
$stmtX->execute();
set_time_limit(300);
$globalUser=$_SESSION["globalUser"];
$globalMes=$_SESSION['globalMes'];
$globalNombreGestion=$_SESSION["globalNombreGestion"];

$codigoSolicitud=$_GET['codigo'];
$codCajaChica=$_GET['cod_cajachica'];
$codPago=$_GET['cod_pago'];
//$codPersonal=$_GET['cod_personal'];
$fechaActual=date("d/m/Y");

$fecha=date("Y-m-d");

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
$fecha=$fechaHoraActual;
// FIN DE LA FECHA


$fechaHora=date("Y-m-d H:i:s");

$instancia=obtenerCodigoInstanciaPorCajaChica($codCajaChica);

$solicitudDetalle=obtenerSolicitudRecursosDetalleAgrupadas($codigoSolicitud);
$codPersonal=obtenerPersonalSolicitanteRecursos($codigoSolicitud);
$numeroRecibo=obtenerNumeroReciboInstancia($instancia);
$numeroDocumento=obtenerNumeroDocumentoReciboCajaChica($codCajaChica);
$index=0;

$idServicioX=obtenerServicioCodigoSolicitudRecursos($codigoSolicitud);
$codSimulacionServicioX=obtenerSimulacionServicioCodigoSolicitudRecursos($codigoSolicitud);
$IdTipo=0;//obtenerTipoServicioPorIdServicio($idServicioX);
$codObjeto=obtenerCodigoObjetoServicioPorIdSimulacion($codSimulacionServicioX);
$datosServicio=obtenerServiciosTipoObjetoNombre($codObjeto)." - ".obtenerServiciosClaServicioTipoNombre($IdTipo);
$nombreCliente=obtenerNombreClienteSimulacion($codSimulacionServicioX);
$numeroSR="SR ".obtenerNumeroSolicitudRecursos($codigoSolicitud);
    while ($rowDetalles = $solicitudDetalle->fetch(PDO::FETCH_ASSOC)) {
    	$numeroCuentaX=trim($rowDetalles['numero']);
		  $nombreCuentaX=trim($rowDetalles['nombre']);
		  $proveedorX=nameProveedor($rowDetalles["cod_proveedor"]);
    $cod_facturaX=$rowDetalles["cod_factura"]; 
		$importeSolX=$rowDetalles["importe"];
    $retencionX=$rowDetalles["cod_confretencion"];
        if($retencionX!=0){
              $tituloImporte=abrevRetencion($retencionX);
              $porcentajeRetencion=100-porcentRetencionSolicitud($retencionX);
              $montoImporte=$importeSolX*($porcentajeRetencion/100);       
              if(($retencionX==8)||($retencionX==10)){ //validacion del descuento por retencion
                $montoImporte=$importeSolX;
              }
              $montoImporteRes=$importeSolX-$montoImporte;
            }else{
             $tituloImporte="Ninguno";
             $montoImporte=$importeSolX;
             $montoImporteRes=0; 
            }
		$detalleX=$rowDetalles["detalle"];
    $detalleX = str_replace("'", '\\\'',$detalleX);

		$codAreaXX=$rowDetalles['cod_area'];
    $codOficinaXX=$rowDetalles['cod_unidadorganizacional'];
    
    $codActividadX=$rowDetalles["cod_actividadproyecto"];
            $tituloActividad="";
            //$tituloActividad=obtenerCodigoActividadesServicioImonitoreo($codActividadX);   
            $detalleActividadFila="";
            if($codActividadX>0){
              if(obtenerNombreDirectoActividadServicio($codActividadX)[0]!=""){
                $detalleActividadFila.="<br><small class='text-dark small'> Actividad: ".obtenerNombreDirectoActividadServicio($codActividadX)[0]." - ".obtenerNombreDirectoActividadServicio($codActividadX)[1]."</small>";
             }
            }
            $codAccNum=$rowDetalles["acc_num"]; 
            if($codAccNum>0){
              if(obtenerNombreDirectoActividadServicioAccNum($codAccNum)[0]!=""){
                $detalleActividadFila.="<br><small class='text-dark small'> Acc Num: ".obtenerNombreDirectoActividadServicioAccNum($codAccNum)[0]." - ".obtenerNombreDirectoActividadServicioAccNum($codAccNum)[1]."</small>";
              }
            }

    $montoImporte=number_format($montoImporte, 2, '.', '');    

    $detalleX=" ".$proveedorX." ".str_replace("-", "", $detalleX)." ".$datosServicio." ".$nombreCliente." ".$detalleActividadFila." ".$numeroSR;  
    $nombreOficinaXX=abrevUnidad_solo($codOficinaXX);
    $nombreAreaXX=abrevArea_solo($codAreaXX);
    $codCuentaX=$rowDetalles['cod_plancuenta']; 
    $codProveedor=$rowDetalles["cod_proveedor"];
    $codigoSolicitudDetalle=$rowDetalles["codigo"];
    //crear recibo
    //Obtener el monto Rembolso
    $stmtMCC = $dbh->prepare("SELECT monto_reembolso from caja_chica where  codigo =$codCajaChica");
    $stmtMCC->execute();
    $resultMCC=$stmtMCC->fetch();    
    $monto_reembolso_x=$resultMCC['monto_reembolso'];
    $monto_reembolso=$monto_reembolso_x-$montoImporte;
    $codigoDetalle=obtenerCodigoReciboCajaChicaDetalle();
    $cod_estado=1;
    $cod_estadoreferencial=1;
    $monto_rendicion=0;
    $cod_actividad_sw=0;
    
    $codigosDetalleVarios=[];
    $codigosDetalleVarios=explode(",",$codigoSolicitudDetalle);
    if(count($codigosDetalleVarios)>1){
       $codigoSolicitudDetallePoner= $codigosDetalleVarios[0];
    }else{
      $codigoSolicitudDetallePoner=$codigoSolicitudDetalle;
    } 

    $sqlInsertar="INSERT INTO caja_chicadetalle(codigo,cod_cajachica,cod_cuenta,fecha,cod_tipodoccajachica,nro_documento,cod_personal,monto,observaciones,cod_estado,cod_estadoreferencial,cod_area,cod_uo,nro_recibo,cod_proveedores,cod_actividad_sw,created_at,created_by,cod_tipopago,cod_solicitudrecursodetalle) 
    VALUES ($codigoDetalle,$codCajaChica,$codCuentaX,'$fecha',$retencionX,$numeroDocumento,'$codPersonal',$importeSolX,'$detalleX',$cod_estado,$cod_estadoreferencial,'$codAreaXX','$codOficinaXX',$numeroRecibo,'$codProveedor','$cod_actividad_sw',NOW(),$globalUser,$codPago,$codigoSolicitudDetallePoner)";
    $stmt = $dbh->prepare($sqlInsertar);
    $flagSuccess=$stmt->execute();
    if($flagSuccess){//registramos rendiciones
      $stmtReembolso = $dbh->prepare("UPDATE caja_chica set monto_reembolso=$monto_reembolso where codigo=$codCajaChica");
      $stmtReembolso->execute();

      $stmtrendiciones = $dbh->prepare("INSERT INTO rendiciones(codigo,numero,cod_tipodoc,monto_a_rendir,monto_rendicion,cod_personal,observaciones,cod_estado,cod_cajachicadetalle,cod_estadoreferencial,fecha_dcc) values ($codigoDetalle,$numeroDocumento,$retencionX,$importeSolX,$monto_rendicion,'$codPersonal','$detalleX',$cod_estado,$codigoDetalle,$cod_estadoreferencial,'$fecha')");
      $flagSuccess=$stmtrendiciones->execute();
      
      $stmtSolicitudDetalle = $dbh->prepare("UPDATE solicitud_recursosdetalle set cod_cajachicadetalle=$codigoDetalle where codigo in ($codigoSolicitudDetalle)");
      $stmtSolicitudDetalle->execute();
      
      $stmtSolicitudFacturas = $dbh->prepare("INSERT INTO facturas_detalle_cajachica (cod_cajachicadetalle,nit,nro_factura,fecha,razon_social,importe,exento,nro_autorizacion,codigo_control,ice,tasa_cero) 
        (SELECT $codigoDetalle as cod_cajachicadetalle,nit,nro_factura,fecha,razon_social,importe,exento,nro_autorizacion,codigo_control,ice,tasa_cero  FROM facturas_compra where cod_solicitudrecursodetalle in ($codigoSolicitudDetalle) and codigo=$cod_facturaX)");
      $facturasOk=$stmtSolicitudFacturas->execute();
      
      $stmtCantidad = $dbh->prepare("SELECT count(*) cantidad FROM facturas_detalle_cajachica where cod_cajachicadetalle=$codigoDetalle");
      $stmtCantidad->execute();
      $resultado=$stmtCantidad->fetch();
      $cantidadFacturas=$resultado['cantidad'];


      //actualizar datos despues de factura
      if($facturasOk==true&&$cantidadFacturas>0){
        $suma_importe_fac=$importeSolX;
        $stmtCC = $dbh->prepare("SELECT cc.codigo,cc.monto_reembolso,ccd.monto
      from  caja_chicadetalle ccd,caja_chica cc
      where ccd.cod_cajachica=cc.codigo and ccd.codigo=$codigoDetalle");
      $stmtCC->execute();
      $resultCC=$stmtCC->fetch();
      $cod_cajachica=$resultCC['codigo'];
      $monto_reembolso=$resultCC['monto_reembolso'];
      $monto_a_rendir=$resultCC['monto'];
      $monto_faltante=$monto_a_rendir-$suma_importe_fac;
    
    //  //------
      
      $monto_reembolso=$monto_reembolso+$monto_faltante;

      //actualizamos el monto de reeembolso de caja chica
      $stmtCCUpdate = $dbh->prepare("UPDATE caja_chica set monto_reembolso=$monto_reembolso where codigo=$codCajaChica");
      $stmtCCUpdate->execute();

      //actualizamos estado en cajachjicadetalle
      $sqlCCD="UPDATE caja_chicadetalle set cod_estado=2,monto_rendicion=$suma_importe_fac where codigo=$codigoDetalle";
      $stmtCCD = $dbh->prepare($sqlCCD);
      $stmtCCD->execute();
      //estado de rendicion 
      $fecha_recepcion=date("Y-m-d H:i:s");
      $sql="UPDATE rendiciones set fecha='$fecha_recepcion',cod_estado=2,monto_rendicion=$suma_importe_fac where codigo=$codigoDetalle";
      $stmtUR = $dbh->prepare($sql);
      $flagSuccess=$stmtUR->execute();

     }

      $index++;
      $numeroRecibo++;
      $numeroDocumento++;
    }  
 
  } 


if($index==0){
 echo "0";
}else{
  $stmtSolicitud = $dbh->prepare("UPDATE solicitud_recursos set cod_estadosolicitudrecurso=9 where codigo=$codigoSolicitud");
  $stmtSolicitud->execute();
  $fechaHoraActual=date("Y-m-d H:i:s");
  $idTipoObjeto=2708;
  $idObjeto=2725; //regristado
  $obs="Solicitud Contabilizada";
  actualizarEstadosObjetosIbnorca($idTipoObjeto,$idObjeto,$globalUser,$codigoSolicitud,$fechaHoraActual,$obs);    
  echo "1";
}
     ?>