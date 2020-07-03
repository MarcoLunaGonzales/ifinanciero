<?php
// require_once 'layouts/bodylogin.php';
require_once '../conexion.php';
require_once '../functions.php';
require_once '../functionsGeneral.php';
require_once 'configModule.php';

$dbh = new Conexion();

//RECIBIMOS LAS VARIABLES
$codigo_factura=$_POST['codigo_factura'];
$observaciones=$_POST['observaciones'];
$cod_solicitudfacturacion=$_POST['cod_solicitudfacturacion'];
$cod_comprobante=$_POST['codigo_comprobante'];
$estado_factura=$_POST['estado_factura'];
session_start();
$globalUser=$_SESSION["globalUser"];
// Prepare
$sql="UPDATE facturas_venta set cod_estadofactura='$estado_factura' where codigo=$codigo_factura";
// echo $sql;
$stmt = $dbh->prepare($sql);
$flagSuccess=$stmt->execute();
if($flagSuccess){
	//volvemos al estado pre aprobacion de la sol fac
	$sqlUpdate="UPDATE solicitudes_facturacion SET cod_estadosolicitudfacturacion=1,obs_devolucion='$observaciones' where codigo=$cod_solicitudfacturacion";
	$stmtUpdate = $dbh->prepare($sqlUpdate);
	$flagSuccess=$stmtUpdate->execute();
	$sqlUpdateComprobante="UPDATE comprobantes SET  cod_estadocomprobante=2 where codigo=$cod_comprobante";
	$stmtUpdateComprobante = $dbh->prepare($sqlUpdateComprobante);
	$flagSuccess=$stmtUpdateComprobante->execute();
	//enviar propuestas para la actualizacion de ibnorca
	$fechaHoraActual=date("Y-m-d H:i:s");
	$idTipoObjeto=2709;
	$idObjeto=2823; //regristado
	$obs="En Registro Solicitud rechazada";
	if(isset($_GET['u'])){
		$u=$_GET['u'];
		actualizarEstadosObjetosIbnorca($idTipoObjeto,$idObjeto,$u,$cod_solicitudfacturacion,$fechaHoraActual,$obs);    
	}else{
		actualizarEstadosObjetosIbnorca($idTipoObjeto,$idObjeto,$globalUser,$cod_solicitudfacturacion,$fechaHoraActual,$obs);    
	} 
}
if($flagSuccess)echo 1;
else echo 2;
// showAlertSuccessError($flagSuccess,$urllistFacturasServicios);	

?>