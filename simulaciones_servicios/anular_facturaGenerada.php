<?php
// require_once 'layouts/bodylogin.php';
require_once '../conexion.php';
require_once '../functions.php';
require_once '../functionsGeneral.php';
require_once 'configModule.php';

$dbh = new Conexion();

//RECIBIMOS LAS VARIABLES
// $codigo_factura=$_POST['codigo_factura'];
$observaciones=$_POST['observaciones'];
$cod_solicitudfacturacion=$_POST['cod_solicitudfacturacion'];
// $cod_comprobante=$_POST['codigo_comprobante'];
$estado_factura=$_POST['estado_factura'];//1 normal, 2 devolucion
session_start();
$globalUser=$_SESSION["globalUser"];
$stmtFActuras = $dbh->prepare("SELECT codigo,nro_factura,nit,razon_social,cod_comprobante from facturas_venta where cod_solicitudfacturacion=$cod_solicitudfacturacion");
$stmtFActuras->execute();	
$stmtFActuras->bindColumn('codigo', $codigo_factura);  	
$stmtFActuras->bindColumn('nro_factura', $nro_factura); 
$stmtFActuras->bindColumn('nit', $nit_factura); 
$stmtFActuras->bindColumn('razon_social', $rs_factura); 
$stmtFActuras->bindColumn('cod_comprobante', $cod_comprobante); 
$cadenaFacturas="";
while ($row = $stmtFActuras->fetch()) {
	$cadenaFacturas.="F ".$nro_factura.", ";
	if($estado_factura!=2){
		$sqlUpdateComprobante="UPDATE comprobantes SET  cod_estadocomprobante=2 where codigo=$cod_comprobante";
		$stmtUpdateComprobante = $dbh->prepare($sqlUpdateComprobante);
		$flagSuccess=$stmtUpdateComprobante->execute();
		//actualizamos facturas	
	}
	$sql="UPDATE facturas_venta set cod_estadofactura='2' where codigo = $codigo_factura";
	// echo $sql;
	$stmt = $dbh->prepare($sql);
	$flagSuccess=$stmt->execute();
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
	$tipoComprobante=4;//facturas	
	$numeroComprobante=obtenerCorrelativoComprobante2($tipoComprobante);	
	$sql="cod_unidadorganizacional";
	$cod_uo_solicitud = obtenerCodUOSolFac($cod_solicitudfacturacion,$sql); 
	$sql="cod_area";
	$cod_area_solicitud = obtenerCodUOSolFac($cod_solicitudfacturacion,$sql); 
	$concepto_contabilizacion="Anulación de ".$cadenaFacturas.", RS ".$rs_factura.", Nit ".$nit_factura."&#010;";
	$concepto_contabilizacion.=$glosa_libreta;
	$codComprobante=obtenerCodigoComprobante();
	//insertamos cabecera
	$flagSuccess=insertarCabeceraComprobante($codComprobante,$codEmpresa,$cod_uo_unico,$codAnio,$codMoneda,$codEstadoComprobante,$tipoComprobante,$fechaActual,$numeroComprobante,$concepto_contabilizacion,$globalUser,$globalUser);
	$ordenDetalle=1;//
	if($flagSuccess){
		//listado del detalle tipo pago
		$stmtDetalleTipoPago = $dbh->prepare("SELECT t.*,(select p.cod_cuenta from tipos_pago_contabilizacion p where p.cod_tipopago=t.cod_tipopago)as cod_cuenta from solicitudes_facturacion_tipospago t where t.cod_solicitudfacturacion=$cod_solicitudfacturacion");
		$stmtDetalleTipoPago->execute();
		$stmtDetalleTipoPago->bindColumn('cod_tipopago', $cod_tipopago);	 
		$stmtDetalleTipoPago->bindColumn('porcentaje', $porcentaje);	
		$stmtDetalleTipoPago->bindColumn('monto', $monto_tipopago);	  
		$stmtDetalleTipoPago->bindColumn('cod_cuenta', $cod_cuenta_pasivo);  
		$monto_tipopago_total=0;
		while ($row_detTipopago = $stmtDetalleTipoPago->fetch()) {
			$descripcion=$concepto_contabilizacion;
			$monto_tipopago_total+=$monto_tipopago;
			$flagSuccessDet=insertarDetalleComprobante($codComprobante,$cod_cuenta_pasivo,0,$cod_uo_solicitud,$cod_area_solicitud,0,$monto_tipopago,$descripcion,$ordenDetalle);
			$ordenDetalle++;
		}
		$descripcion=$concepto_contabilizacion;
		$cod_cuenta=obtenerValorConfiguracion(62);//cod defecto para la anulacion de facturas
		$flagSuccessDet=insertarDetalleComprobante($codComprobante,$cod_cuenta,0,$cod_uo_solicitud,$cod_area_solicitud,$monto_tipopago_total,0,$descripcion,$ordenDetalle);	
	}
	$obs="Factura Anulada Devolución";
}else{
	$obs="Factura Anulada Normal";
}
if($flagSuccess){
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
if($flagSuccess)echo 1;
else echo 2;


?>