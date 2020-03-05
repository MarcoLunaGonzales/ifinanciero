<?php

require_once 'conexion.php';
require_once 'functions.php';
require_once 'configModule.php';

$dbh = new Conexion();

$codigo=$codigo;

$stmtSelect = $dbh->prepare("SELECT estado from distribucion_gastosporcentaje where codigo=$codigo");
$stmtSelect->execute();
$result=$stmtSelect->fetch();
$sw=$result['estado'];
if($sw==0){
	$stmt = $dbh->prepare("UPDATE distribucion_gastosporcentaje set cod_estadoreferencial=2 where codigo=:codigo");

	$stmt->bindParam(':codigo', $codigo);

	$flagSuccess=$stmt->execute();
}else
	$flagSuccess=false;


showAlertSuccessError($flagSuccess,$urlList);

?>