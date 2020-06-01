<?php
require_once 'layouts/bodylogin.php';
require_once 'conexion.php';
require_once 'functions.php';
require_once 'functionsGeneral.php';
require_once 'configModule.php';

$dbh = new Conexion();

//RECIBIMOS LAS VARIABLES
$codigo=$codigo;
$cod_solicitudfacturacion=$cod_solicitudfacturacion;
$cod_comprobante=$cod_comprobante;

// Prepare
$sql="UPDATE facturas_venta set cod_estadofactura='2' where codigo=$codigo";
echo $sql;
$stmt = $dbh->prepare($sql);
$flagSuccess=$stmt->execute();
if($flagSuccess){
	//volvemos al estado pre aprobacion de la sol fac
	$sqlUpdate="UPDATE solicitudes_facturacion SET  cod_estadosolicitudfacturacion=6 where codigo=$cod_solicitudfacturacion";
	$stmtUpdate = $dbh->prepare($sqlUpdate);
	$flagSuccess=$stmtUpdate->execute();
	$sqlUpdateComprobante="UPDATE comprobantes SET  cod_estadocomprobante=2 where codigo=$cod_comprobante";
	$stmtUpdateComprobante = $dbh->prepare($sqlUpdateComprobante);
	$flagSuccess=$stmtUpdateComprobante->execute();
	//enviar propuestas para la actualizacion de ibnorca
	$fechaHoraActual=date("Y-m-d H:i:s");
	$idTipoObjeto=2709;
	$idObjeto=2823; //regristado
	$obs="En Pre Aprobacion Solicitud";
	if(isset($_GET['u'])){
		$u=$_GET['u'];
		actualizarEstadosObjetosIbnorca($idTipoObjeto,$idObjeto,$u,$codigo,$fechaHoraActual,$obs);    
	}else{
		actualizarEstadosObjetosIbnorca($idTipoObjeto,$idObjeto,$globalUser,$codigo,$fechaHoraActual,$obs);    
	} 
}

showAlertSuccessError($flagSuccess,$urllistFacturasServicios);	

?>