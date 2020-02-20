<?php

require_once 'conexion.php';
require_once 'functions.php';
require_once 'functionsGeneral.php';

$dbh = new Conexion();

//RECIBIMOS LAS VARIABLES
$codigoX=$codigo;
$codigoPadre=$codigo_padre;
$codigo=$codigoPadre;

require_once 'configModule.php';

// Prepare
$stmt = $dbh->prepare("UPDATE cuentas_auxiliares_cajachica set cod_estadoreferencial=2 where codigo=:codigo");
// Bind
$stmt->bindParam(':codigo', $codigoX);

$flagSuccess=$stmt->execute();
showAlertSuccessError($flagSuccess,$urlListCC2);

?>