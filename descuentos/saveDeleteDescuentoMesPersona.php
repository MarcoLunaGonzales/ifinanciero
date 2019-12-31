<?php

require_once 'conexion.php';
require_once 'functions.php';
require_once 'configModule.php';

$dbh = new Conexion();
$codDescuento=$codigo_descuento;
$codMes=$codigo_mes;

//RECIBIMOS LAS VARIABLES
$codigo=$codigo;
// Prepare
$stmt = $dbh->prepare("UPDATE descuentos_personal_mes set cod_estadoreferencial=2 where codigo=$codigo");
// Bind


$flagSuccess=$stmt->execute();
showAlertSuccessError($flagSuccess,$urlListMesPersona."&cod_descuento=".$codDescuento."&cod_mes=".$codMes);

?>