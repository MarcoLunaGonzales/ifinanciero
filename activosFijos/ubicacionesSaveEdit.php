<?php

//require_once './layouts/bodylogin.php';
require_once 'conexion.php';
require_once 'functions.php';
require_once 'configModule.php';

$dbh = new Conexion();

$codigo=$_POST["codigo"];
$cod_unidades_organizacionales=$_POST["cod_unidades_organizacionales"];
$edificio=$_POST["edificio"];
$oficina=$_POST["oficina"];
$codEstado="1";
//var_dump($_POST);
// Prepare
$stmt = $dbh->prepare("UPDATE ubicaciones set cod_unidades_organizacionales=:cod_unidades_organizacionales,edificio=:edificio,oficina=:oficina where codigo=:codigo");
// Bind
$stmt->bindParam(':cod_unidades_organizacionales', $cod_unidades_organizacionales);
$stmt->bindParam(':edificio', $edificio);
$stmt->bindParam(':oficina', $oficina);
$stmt->bindParam(':codigo', $codigo);
$flagSuccess=$stmt->execute();
showAlertSuccessError($flagSuccess,$urlList2);
?>