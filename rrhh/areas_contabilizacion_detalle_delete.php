<?php

require_once 'conexion.php';
require_once 'styles.php';
require_once 'configModule.php';

$dbh = new Conexion();

//RECIBIMOS LAS VARIABLES
$codigo=$codigo;
$codigo_area_cont=$codigox;

// Prepare
$stmt = $dbh->prepare("UPDATE areas_contabilizacion_detalle set cod_estadoreferencial=2 where codigo=:codigo");
// Bind
$stmt->bindParam(':codigo', $codigo);

$flagSuccess=$stmt->execute();
//showAlertSuccessError($flagSuccess,$urlListAreas_contabilizacion);
showAlertSuccessError($flagSuccess,$list_areas_contabilizacion_Detalle."&codigo=".$codigo_area_cont);


?>