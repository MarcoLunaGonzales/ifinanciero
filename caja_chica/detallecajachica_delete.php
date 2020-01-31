<?php
require_once 'conexion.php';
require_once 'styles.php';
require_once 'configModule.php';

$dbh = new Conexion();

// RECIBIMOS LAS VARIABLES
$cod_cc=$cod_cc;
$cod_tcc=$cod_tcc;
$cod_dcc=$codigo;

// Prepare
$stmt = $dbh->prepare("UPDATE caja_chicadetalle set cod_estadoreferencial=2 where codigo=$cod_dcc");
$flagSuccess=$stmt->execute();
if($flagSuccess){
	$stmtRendicion = $dbh->prepare("UPDATE rendiciones set cod_estadoreferencial=2 where codigo=$cod_dcc");
	$flagSuccess=$stmtRendicion->execute();

}

showAlertSuccessError($flagSuccess,$urlListDetalleCajaChica.'&codigo='.$cod_cc.'&cod_tcc='.$cod_tcc);

?>