<?php

require_once 'conexion.php';
require_once 'functions.php';
require_once 'configModule.php';

$dbh = new Conexion();


$codPoliticaDescuento=$cod_pol_desc;

$stmt = $dbh->prepare("UPDATE $table_politicaDescuento set cod_estadoreferencial=2 where codigo=:codigo");

$stmt->bindParam(':codigo', $codPoliticaDescuento);

$flagSuccess=$stmt->execute();
showAlertSuccessError($flagSuccess,$urlList);

?>