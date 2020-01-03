<?php

require_once 'conexion.php';
require_once 'functions.php';
require_once 'configModule.php';

$dbh = new Conexion();
$codigoP=$codigo;
$stmt = $dbh->prepare("UPDATE $table_personalfin set cod_estado_referencial=2 where codigo=:codigo");
$stmt->bindParam(':codigo', $codigoP);

$flagSuccess=$stmt->execute();
showAlertSuccessError($flagSuccess,$urlList);

?>