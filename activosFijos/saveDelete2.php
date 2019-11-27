<?php

require_once 'conexion.php';
require_once 'functions.php';
require_once 'activosFijos/configModule.php';

$dbh = new Conexion();

//RECIBIMOS LAS VARIABLES
$codigo=$codigo;
// Prepare
$stmt = $dbh->prepare("UPDATE activosfijos set cod_estadoactivofijo=2 where codigo=:codigo");
// Bind
$stmt->bindParam(':codigo', $codigo);

$flagSuccess=$stmt->execute();
showAlertSuccessError($flagSuccess,$urlList6);

?>