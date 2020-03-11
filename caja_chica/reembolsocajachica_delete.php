<?php
require_once 'conexion.php';
require_once 'styles.php';
require_once 'configModule.php';

$dbh = new Conexion();

// RECIBIMOS LAS VARIABLES
$cod_cc=$cod_cc;
$cod_tcc=$cod_tcc;
$cod_rcc=$codigo;

//sacamos el monto de reembolso que se eliminara para devolver a saldo de caja chica
$stmtMontoAnterior = $dbh->prepare("SELECT monto,(select cc.monto_reembolso from caja_chica cc where cc.codigo=cod_cajachica) as monto_reembolso from caja_chicareembolsos where codigo=$cod_rcc");
$stmtMontoAnterior->execute();
$resultMontoAnterior = $stmtMontoAnterior->fetch();
$monto_anterior_reembolso = $resultMontoAnterior['monto'];
$monto_saldo_antereior_cc = $resultMontoAnterior['monto_reembolso'];

$monto_saldo_nuevo=$monto_saldo_antereior_cc-$monto_anterior_reembolso;
$monto_reembolso_nuevo=0;
//acctualiazmos reembolso
$stmtReembolso = $dbh->prepare("UPDATE caja_chica set monto_reembolso=$monto_saldo_nuevo,monto_reembolso_nuevo=$monto_reembolso_nuevo where codigo=$cod_cc");
$stmtReembolso->execute();
//================================================================
// Prepare
$stmt = $dbh->prepare("UPDATE caja_chicareembolsos set cod_estadoreferencial=2 where codigo=$cod_rcc");
$flagSuccess=$stmt->execute();

showAlertSuccessError($flagSuccess,$urlListDetalleCajaChica.'&codigo='.$cod_cc.'&cod_tcc='.$cod_tcc);

?>