<?php

require_once '../layouts/bodylogin.php';
require_once '../conexion.php';
require_once '../functionsGeneral.php';
require_once 'configModule.php';

$dbh = new Conexion();

$mes=$_POST["mes"];
$estadoPlanilla=$_POST["estado"];
$codGestion=$_POST["codGestion"];

$stmt = $dbh->prepare("INSERT INTO $table_refrigerios (cod_gestion, cod_mes,cod_estadoplanilla) VALUES (:cod_gestion,:cod_mes,:cod_estadoplanilla)");

$stmt->bindParam(':cod_gestion', $codGestion);
$stmt->bindParam(':cod_mes', $mes);
$stmt->bindParam(':cod_estadoplanilla', $estadoPlanilla);

$flagSuccess=$stmt->execute();
showAlertSuccessError($flagSuccess,$urlList2);

?>
