<?php

//require_once './layouts/bodylogin.php';
require_once 'conexion.php';
require_once 'functions.php';
require_once 'perspectivas/configModule.php';

$dbh = new Conexion();

$codigo=$_POST["codigo"];


$nombre=$_POST["nombre"];
$cargo=$_POST["cargo"];

$codEstado="1";

// Prepare
$stmt = $dbh->prepare("UPDATE responsables set nombre=:nombre, cargo=:cargo where codigo=:codigo");
// Bind
$stmt->bindParam(':codigo', $codigo);
$stmt->bindParam(':nombre', $nombre);
$stmt->bindParam(':cargo', $cargo);

$flagSuccess=$stmt->execute();
showAlertSuccessError($flagSuccess,$urlList3);

?>