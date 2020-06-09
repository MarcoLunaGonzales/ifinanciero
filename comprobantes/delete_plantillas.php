<?php

require_once '../conexion.php';
require_once '../functions.php';
require_once '../functionsGeneral.php';
require_once 'configModule.php';

$dbh = new Conexion();

//RECIBIMOS LAS VARIABLES
$cod_plantilla=$_POST['cod_plantilla'];
// Prepare
$stmt = $dbh->prepare("UPDATE plantillas_comprobante set cod_estadoreferencial=2 where codigo=$cod_plantilla");
// Bind
$flagSuccess=$stmt->execute();
if($flagSuccess){
	echo 1;
}else{
	echo 0;
}
?>