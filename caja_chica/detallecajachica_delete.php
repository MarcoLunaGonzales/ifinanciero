<?php
require_once 'conexion.php';
require_once 'styles.php';
require_once 'configModule.php';

$dbh = new Conexion();
$globalUser=$_SESSION["globalUser"];
// RECIBIMOS LAS VARIABLES
$cod_cc=$cod_cc;
$cod_tcc=$cod_tcc;
$cod_dcc=$codigo;


$stmtMontoAnterior = $dbh->prepare("SELECT monto,(select cc.monto_reembolso from caja_chica cc where cc.codigo=cod_cajachica) as monto_reembolso from caja_chicadetalle where codigo=$cod_dcc");
$stmtMontoAnterior->execute();
$resultMontoAnterior = $stmtMontoAnterior->fetch();
$monto_anterior = $resultMontoAnterior['monto'];
$monto_reembolso_x = $resultMontoAnterior['monto_reembolso'];

$monto_reembolso=$monto_reembolso_x+$monto_anterior;
//acctualiazmos reembolso
$stmtReembolso = $dbh->prepare("UPDATE caja_chica set monto_reembolso=$monto_reembolso where codigo=$cod_cc");
$stmtReembolso->execute();
//================================================================
$monto_rendicion=0;


// Prepare
$stmt = $dbh->prepare("UPDATE caja_chicadetalle set cod_estadoreferencial=2,modified_by=$globalUser,modified_at=NOW() where codigo=$cod_dcc");
$flagSuccess=$stmt->execute();
if($flagSuccess){
	$stmtRendicion = $dbh->prepare("UPDATE rendiciones set cod_estadoreferencial=2 where codigo=$cod_dcc");
	$flagSuccess=$stmtRendicion->execute();
	

    //revertir el pago de SR
    //buscamos codigo estado cuenta origen
    $stmtEC = $dbh->prepare("SELECT cod_comprobantedetalleorigen from estados_cuenta where cod_cajachicadetalle = $cod_dcc");
	$stmtEC->execute();
	$resultEC = $stmtEC->fetch();
	$codigo_ec = $resultEC['cod_comprobantedetalleorigen'];
	//sacamos datos de SR
    $sqlDetalleX="SELECT sd.codigo,sd.cod_solicitudrecurso,sd.cod_proveedor,sd.cod_tipopagoproveedor 
    FROM solicitud_recursos s,solicitud_recursosdetalle sd
    WHERE s.codigo=sd.cod_solicitudrecurso and s.cod_comprobante in (select cd.cod_comprobante from estados_cuenta e,comprobantes_detalle cd where e.cod_comprobantedetalle=cd.codigo and e.codigo=$codigo_ec)";
    $stmtDetalleX = $dbh->prepare($sqlDetalleX);
    $stmtDetalleX->execute();                  
    $result_detalleX= $stmtDetalleX->fetch();
    $codigo_srd=$result_detalleX['codigo'];
    $cod_solicitudrecurso_sr=$result_detalleX['cod_solicitudrecurso'];
    $cod_proveedor_sr=$result_detalleX['cod_proveedor'];
    $cod_tipopagoproveedor_sr=$result_detalleX['cod_tipopagoproveedor'];
    
    $stmtCambioEstadoSR = $dbh->prepare("UPDATE solicitud_recursos set cod_estadosolicitudrecurso=5 where codigo=:codigo");
    $stmtCambioEstadoSR->bindParam(':codigo', $cod_solicitudrecurso_sr);
    $flagSuccess=$stmtCambioEstadoSR->execute();
    //si tiene algunpago de proveedor, borrar
    $stmtDELETEPagoProv = $dbh->prepare("UPDATE pagos_proveedores set cod_estadopago=2 where cod_cajachicadetalle=:codigo");
    $stmtDELETEPagoProv->bindParam(':codigo', $cod_dcc);
    $flagSuccess=$stmtDELETEPagoProv->execute();	
    //si tiene algun estado de cuentas registrado, borrar
	$stmtDeleteContraCuenta = $dbh->prepare("DELETE from estados_cuenta where cod_cajachicadetalle = $cod_dcc");
    $flagSuccess=$stmtDeleteContraCuenta->execute();

}

showAlertSuccessError($flagSuccess,$urlListDetalleCajaChica.'&codigo='.$cod_cc.'&cod_tcc='.$cod_tcc);

?>