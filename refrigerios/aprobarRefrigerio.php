<?php

require_once 'layouts/bodylogin.php';
require_once 'conexion.php';
require_once 'functionsGeneral.php';
require_once 'configModule.php';

$dbh = new Conexion();

$codRefrigerio=$cod_refrigerio;

// Prepare
$stmt = $dbh->prepare("UPDATE $table_refrigerios SET cod_estadoplanilla=2
                     WHERE codigo=:codigo");
// Bind
$stmt->bindParam(':codigo', $codRefrigerio);

$flagSuccess=$stmt->execute();
showAlertSuccessError($flagSuccess,$urlList);

?>