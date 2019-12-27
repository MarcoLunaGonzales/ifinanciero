<?php

require_once 'conexion.php';
require_once 'functions.php';
require_once 'configModule.php';

$dbh = new Conexion();

$codEscalaAntiguedad=$cod_esc_ant;

$stmt = $dbh->prepare("UPDATE $table_escalaAntiguedad set cod_estadoreferencial=2 where codigo=:codigo");

$stmt->bindParam(':codigo', $codEscalaAntiguedad);

$flagSuccess=$stmt->execute();
showAlertSuccessError($flagSuccess,$urlList);

?>