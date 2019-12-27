<?php

require_once 'conexion.php';
require_once 'functions.php';
require_once 'configModule.php';

$dbh = new Conexion();

$codDotacion=$cod_dot;

$stmt = $dbh->prepare("UPDATE $table_dotaciones set cod_estadoreferencial=2 where codigo=:codigo");

$stmt->bindParam(':codigo', $codDotacion);

$flagSuccess=$stmt->execute();
showAlertSuccessError($flagSuccess,$urlList);

?>