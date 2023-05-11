<?php

require_once '../layouts/bodylogin.php';
require_once '../conexion.php';
require_once '../functionsGeneral.php';
require_once 'configModule.php';

$dbh = new Conexion();

$nombre=$_POST["nombre"];
$abreviatura=$_POST["abreviatura"];
$observaciones=$_POST["observaciones"];
$codCuenta=$_POST["cuenta"];
$codEstado="1";

// Prepare
$stmt = $dbh->prepare("INSERT INTO $table (nombre, abreviatura,observaciones, cod_estadoreferencial, cod_cuenta) VALUES (:nombre, :abreviatura,:observaciones , :cod_estado, :cod_cuenta)");
// Bind
$stmt->bindParam(':nombre', $nombre);
$stmt->bindParam(':abreviatura', $abreviatura);
$stmt->bindParam(':observaciones', $observaciones);
$stmt->bindParam(':cod_estado', $codEstado);
$stmt->bindParam(':cod_cuenta', $codCuenta);

$flagSuccess=$stmt->execute();
showAlertSuccessError($flagSuccess,$urlList2);

?>
