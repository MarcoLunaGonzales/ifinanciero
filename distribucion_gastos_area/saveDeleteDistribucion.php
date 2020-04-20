<?php

require_once 'conexion.php';
require_once 'functions.php';
require_once 'configModule.php';

$dbh = new Conexion();

$codigo=$codigo;


	$stmt = $dbh->prepare("UPDATE distribucion_gastosarea set cod_estadoreferencial=2 where codigo=:codigo");
	$stmt->bindParam(':codigo', $codigo);
	$flagSuccess=$stmt->execute();

showAlertSuccessError($flagSuccess,$urlList);

?>