<?php

//require_once '../layouts/bodylogin.php';
require_once 'conexion.php';
require_once 'functions.php';
require_once 'perspectivas/configModule.php';

$dbh = new Conexion();

$nombre=$_POST["nombre"];
$cargo=$_POST["cargo"];

$codEstado="1";
//echo $table; 
// Prepare
$stmt = $dbh->prepare("INSERT INTO responsables (nombre, cargo, cod_estado) VALUES (:nombre, :cargo,  :cod_estado)");
// Bind
$stmt->bindParam(':nombre', $nombre);
$stmt->bindParam(':cargo', $cargo);

$stmt->bindParam(':cod_estado', $codEstado);

$flagSuccess=$stmt->execute();
showAlertSuccessError($flagSuccess,$urlList3);


?>
