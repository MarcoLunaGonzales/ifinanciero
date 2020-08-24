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
$sql="UPDATE cuentas_auxiliares set cod_estadoreferencial=2 where codigo=$codigoX";
// echo $sql; 
$stmt = $dbh->prepare($sql);
$flagSuccess=$stmt->execute();

showAlertSuccessError($flagSuccess,$urlList2);

?>