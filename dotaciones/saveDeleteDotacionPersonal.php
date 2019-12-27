<?php

require_once 'conexion.php';
require_once 'functions.php';
require_once 'configModule.php';

$dbh = new Conexion();

$codDotacion=$cod_dot;
$codDotacionPersonal=$cod_dot_per;

$stmt = $dbh->prepare("UPDATE $table_dotaciones_personal set cod_estadoreferencial=2 where codigo=:codigo");

$stmt->bindParam(':codigo', $codDotacionPersonal);

$flagSuccess=$stmt->execute();
showAlertSuccessError($flagSuccess,$urlListDotacionPersonal."&cod_dot=".$codDotacion);

?>