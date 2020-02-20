<?php
require_once 'conexion.php';
require_once 'styles.php';
require_once 'configModule.php';

$dbh = new Conexion();

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
$stmt = $dbh->prepare("UPDATE caja_chicadetalle set cod_estadoreferencial=2 where codigo=$cod_dcc");
$flagSuccess=$stmt->execute();
if($flagSuccess){
	$stmtRendicion = $dbh->prepare("UPDATE rendiciones set cod_estadoreferencial=2 where codigo=$cod_dcc");
	$flagSuccess=$stmtRendicion->execute();

}

showAlertSuccessError($flagSuccess,$urlListDetalleCajaChica.'&codigo='.$cod_cc.'&cod_tcc='.$cod_tcc);

?>