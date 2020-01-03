<?php

require_once 'conexion.php';
require_once 'functions.php';
require_once 'configModule.php';

$dbh = new Conexion();

$codMes=$_GET['cod_mes'];
$codAnticipoPersonal=$cod_ant_per;

$stmt = $dbh->prepare("UPDATE $table_anticiposPersonal set cod_estadoreferencial=2 where codigo=:codigo");

$stmt->bindParam(':codigo', $codAnticipoPersonal);

$flagSuccess=$stmt->execute();
showAlertSuccessError($flagSuccess,$urlListMesPersona."&cod_mes=".$codMes);

?>