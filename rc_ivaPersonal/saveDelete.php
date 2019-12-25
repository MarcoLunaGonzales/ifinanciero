<?php

require_once 'conexion.php';
require_once 'functions.php';
require_once 'configModule.php';

$dbh = new Conexion();


$codRciva=$cod_rciva;

$stmt = $dbh->prepare("UPDATE $table_rcivaPersonal set cod_estadoreferencial=2 where codigo=:codigo");

$stmt->bindParam(':codigo', $codRciva);

$flagSuccess=$stmt->execute();
showAlertSuccessError($flagSuccess,$urlList);

?>