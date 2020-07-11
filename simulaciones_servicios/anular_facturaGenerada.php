<?php
// require_once 'layouts/bodylogin.php';
require_once '../conexion.php';
require_once '../functions.php';
require_once '../functionsGeneral.php';
require_once 'configModule.php';

$dbh = new Conexion();

//RECIBIMOS LAS VARIABLES
$codigos_facturas_x=$_POST['codigo_factura'];//array de facturas
$observaciones=$_POST['observaciones'];
$cod_solicitudfacturacion=$_POST['cod_solicitudfacturacion'];
// $cod_comprobante=$_POST['codigo_comprobante'];
$estado_factura=$_POST['estado_factura'];//1 normal, 2 devolucion
session_start();
$globalUser=$_SESSION["globalUser"];
$stmtFActuras = $dbh->prepare("SELECT codigo,nro_factura,nit,razon_social,cod_comprobante,cod_unidadorganizacional,cod_area from facturas_venta where codigo in ($codigos_facturas_x)");
$stmtFActuras->execute();	
$stmtFActuras->bindColumn('codigo', $codigo_factura);  	
$stmtFActuras->bindColumn('nro_factura', $nro_factura); 
$stmtFActuras->bindColumn('nit', $nit_factura); 
$stmtFActuras->bindColumn('razon_social', $rs_factura); 
$stmtFActuras->bindColumn('cod_comprobante', $cod_comprobante); 
$stmtFActuras->bindColumn('cod_unidadorganizacional', $cod_unidadorganizacional); 
$stmtFActuras->bindColumn('cod_area', $cod_area); 
// $stmtFActuras->bindColumn('cod_solicitudfacturacion', $cod_solicitudfacturacion); 
$cadenaFacturas="";
while ($row = $stmtFActuras->fetch()) {
	$cadenaFacturas.="F ".$nro_factura.", ";
	if($estado_factura!=2){
		$sqlUpdateComprobante="UPDATE comprobantes SET  cod_estadocomprobante=2 where codigo=$cod_comprobante";
		$stmtUpdateComprobante = $dbh->prepare($sqlUpdateComprobante);
		$flagSuccess=$stmtUpdateComprobante->execute();
		//actualizamos facturas	
	}
}
$cadenaFacturas=trim($cadenaFacturas,", ");
if($estado_factura==2){ //tipo devolucion tiene contabilizacion
	$cod_uo_unico=5;
	$globalUser=$_SESSION["globalUser"];
	$mesTrabajo=$_SESSION['globalMes'];
	$gestionTrabajo=$_SESSION['globalNombreGestion'];
	$codEmpresa=1;
	$codAnio=$_SESSION["globalNombreGestion"];
	$codMoneda=1;
	$codEstadoComprobante=1;
	$fechaActual=date("Y-m-d H:i:s");		
	$cod_libretabancaria=obtenerLibretaBancariaFacturaVenta($codigo_factura);
	$glosa_libreta=obtenerGlosaLibretaBancariaDetalle($cod_libretabancaria);
	$tipoComprobante=3;//traspaso
	$numeroComprobante=obtenerCorrelativoComprobante2($tipoComprobante);	
	if($cod_solicitudfacturacion!=-100){
		$sql="cod_unidadorganizacional";
		$cod_uo_solicitud = obtenerCodUOSolFac($cod_solicitudfacturacion,$sql); 
		$sql="cod_area";
		$cod_area_solicitud = obtenerCodUOSolFac($cod_solicitudfacturacion,$sql); 
	}else{					
		$cod_uo_solicitud = $cod_unidadorganizacional;		
		$cod_area_solicitud = $cod_area; 
	}
	
	$concepto_contabilizacion="Anulación de ".$cadenaFacturas."/ RS: ".$rs_factura.", Nit: ".$nit_factura."&#010;";	
	$concepto_contabilizacion.=" Detalle: ".$glosa_libreta;
	$codComprobante=obtenerCodigoComprobante();
	//insertamos cabecera
	$flagSuccess=insertarCabeceraComprobante($codComprobante,$codEmpresa,$cod_uo_unico,$codAnio,$codMoneda,$codEstadoComprobante,$tipoComprobante,$fechaActual,$numeroComprobante,$concepto_contabilizacion,$globalUser,$globalUser);	
	if($flagSuccess){	
		//listado del detalle tipo pago
		$monto_tipopago_total=0;
		$cod_cuenta_pasivo=0;
		if($cod_solicitudfacturacion!=-100){
			//sacamos el tipo de pago de la sol facturacion y el monto total en caso de que tenga solicitud
			$stmtDetalleTipoPago = $dbh->prepare("SELECT t.monto,(select p.cod_cuenta from tipos_pago_contabilizacion p where p.cod_tipopago=t.cod_tipopago)as cod_cuenta from solicitudes_facturacion_tipospago t where t.cod_solicitudfacturacion=$cod_solicitudfacturacion order by codigo desc");
			$stmtDetalleTipoPago->execute();			
			$stmtDetalleTipoPago->bindColumn('monto', $monto_tipopago);	  
			$stmtDetalleTipoPago->bindColumn('cod_cuenta', $cod_cuenta_pasivo);  		
			while ($row_detTipopago = $stmtDetalleTipoPago->fetch()) {				
				$monto_tipopago_total+=$monto_tipopago;
				$cod_cuenta_pasivo=$cod_cuenta_pasivo;
			}
		}else{//caso de que la factura sea de la tienda
			$stmtDetalleTipoPago = $dbh->prepare("SELECT f.cod_tipopago,(select p.cod_cuenta from tipos_pago_contabilizacion p where p.cod_tipopago=f.cod_tipopago)as cod_cuenta  from facturas_venta f where f.codigo=$codigo_factura");
			// echo $codigo_factura."--";
			$stmtDetalleTipoPago->execute();			
			$stmtDetalleTipoPago->bindColumn('cod_cuenta', $cod_cuenta_x);
			while ($row_detTipopago = $stmtDetalleTipoPago->fetch()) {						
				$cod_cuenta_pasivo=$cod_cuenta_x;
			}
			$monto_tipopago_total=sumatotaldetallefactura($codigo_factura);			
		}	
		$ordenDetalle=1;//
		$descripcion=$concepto_contabilizacion;	
		$flagSuccessDet=insertarDetalleComprobante($codComprobante,$cod_cuenta_pasivo,0,$cod_uo_solicitud,$cod_area_solicitud,0,$monto_tipopago_total,$descripcion,$ordenDetalle);
		$ordenDetalle++;
		if($flagSuccessDet){
			$descripcion=$concepto_contabilizacion;
			$cod_cuenta=obtenerValorConfiguracion(62);//cod defecto para la anulacion de facturas
			$cuenta_axiliar=obtenerValorConfiguracion(63);//cod cuenta auxiliar por defecto para la anulacion de facturas		
			$cod_proveedor=obtenerCodigoProveedorCuentaAux($cuenta_axiliar);	
			$flagSuccessDet=insertarDetalleComprobante($codComprobante,$cod_cuenta,$cuenta_axiliar,$cod_uo_solicitud,$cod_area_solicitud,$monto_tipopago_total,0,$descripcion,$ordenDetalle);	
		}
		//buscamos el cod_Detalle_comprobante
		$cod_comprobante_detalle=0;
		$stmtdetalleCom = $dbh->prepare("SELECT codigo from comprobantes_detalle where cod_comprobante=$codComprobante and orden=2");
		$stmtdetalleCom->execute();			
		$stmtdetalleCom->bindColumn('codigo', $cod_comprobante_detalle);
		while ($row = $stmtdetalleCom->fetch()) {						
			$cod_comprobante_detalle=$cod_comprobante_detalle;			
		}

		if($flagSuccessDet){
			$sql="UPDATE facturas_venta set cod_estadofactura='2' where codigo in ($codigos_facturas_x)";	
			$stmt = $dbh->prepare($sql);
			$flagSuccess=$stmt->execute();
			//insertamos la el estado de cuenta
			$sqlEstadoCuenta="INSERT into estados_cuenta(cod_comprobantedetalle,cod_plancuenta,monto,cod_proveedor,fecha,cod_comprobantedetalleorigen,cod_cuentaaux,cod_tipoestadocuenta,glosa_auxiliar) values($cod_comprobante_detalle,$cod_cuenta_pasivo,$monto_tipopago_total,$cod_proveedor,'$fechaActual','0',$cuenta_axiliar,'1','$concepto_contabilizacion')";
			// echo $sqlEstadoCuenta;
			$stmtEstadoCuenta = $dbh->prepare($sqlEstadoCuenta);
			$flagSuccess=$stmtEstadoCuenta->execute();			
		}else{			
            $sqldeletecomprobante="DELETE from comprobantes where codigo=$codComprobante";
            $stmtDeleteCopmprobante = $dbh->prepare($sqldeletecomprobante);
            $flagSuccess=$stmtDeleteCopmprobante->execute();

		}
		
	}
	$obs="Factura Anulada, registro anticipo";
}else{
	$obs="Factura Anulada, transaccion no valida";
}
if($flagSuccess){
	if($cod_solicitudfacturacion!=-100){
		//volvemos al estado de registro de la sol fac.
		$sqlUpdate="UPDATE solicitudes_facturacion SET cod_estadosolicitudfacturacion=1,obs_devolucion='$observaciones' where codigo=$cod_solicitudfacturacion";
		$stmtUpdate = $dbh->prepare($sqlUpdate);
		$flagSuccess=$stmtUpdate->execute();
		//enviar propuestas para la actualizacion de ibnorca
		$fechaHoraActual=date("Y-m-d H:i:s");
		$idTipoObjeto=2709;
		$idObjeto=2726; //regristado
		// $obs="Factura Anulada Normal";
		actualizarEstadosObjetosIbnorca($idTipoObjeto,$idObjeto,$globalUser,$cod_solicitudfacturacion,$fechaHoraActual,$obs);
	}
}
if($flagSuccess)echo 1;
else echo 2;


?>