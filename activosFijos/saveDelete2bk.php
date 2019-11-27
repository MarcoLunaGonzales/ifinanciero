<?php

require_once 'conexion.php';
require_once 'functions.php';
require_once 'perspectivas/configModule.php';

$dbh = new Conexion();

//RECIBIMOS LAS VARIABLES
$codigo=$codigo;
// Prepare
$stmt = $dbh->prepare("UPDATE ubicaciones set cod_estado=2 where codigo=:codigo");
// Bind
$stmt->bindParam(':codigo', $codigo);

$flagSuccess=$stmt->execute();
showAlertSuccessError($flagSuccess,$urlList2);

?>