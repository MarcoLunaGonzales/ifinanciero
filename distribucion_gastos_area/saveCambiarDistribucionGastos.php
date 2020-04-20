<?php
require_once 'conexion.php';
require_once 'configModule.php';


$dbh = new Conexion();
$codigo=$codigo;

$stmtD = $dbh->prepare("UPDATE distribucion_gastosarea set estado=1 where codigo='$codigo'");	
$flagSuccess=$stmtD->execute();

showAlertSuccessError($flagSuccess,$urlList);

?>
