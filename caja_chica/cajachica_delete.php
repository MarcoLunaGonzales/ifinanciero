<?php
require_once 'conexion.php';
require_once 'styles.php';
require_once 'configModule.php';

$dbh = new Conexion();

//RECIBIMOS LAS VARIABLES
$codigo=$codigo;
$cod_tcc=$cod_tcc;

// Prepare
$stmt = $dbh->prepare("UPDATE caja_chica set cod_estadoreferencial=2 where codigo=$codigo");
$flagSuccess=$stmt->execute();
showAlertSuccessError($flagSuccess,$urlListCajaChica."&codigo=".$cod_tcc);

?>