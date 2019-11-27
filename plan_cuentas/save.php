<?php

require_once '../layouts/bodylogin.php';
require_once '../conexion.php';
require_once '../functions.php';
require_once '../functionsGeneral.php';
require_once 'configModule.php';

$dbh = new Conexion();

$numero=$_POST["numero"];
$padre=$_POST["padre"];
$nombre=$_POST["nombre"];
$tipoCuenta=$_POST["tipocuenta"];
$moneda=$_POST["moneda"];
$codEstado="1";
$observaciones="";
$cuentaAuxiliar=$_POST["cuenta_auxiliar"];

$nivelCuenta=buscarNivelCuenta($numero);

$numero=str_replace(".", "", $numero);


// Prepare
$stmt = $dbh->prepare("INSERT INTO $table (numero, cod_padre, nombre, cod_moneda, cod_estadoreferencial, nivel, cod_tipocuenta, observaciones, cuenta_auxiliar) VALUES (:numero, :cod_padre, :nombre ,:cod_moneda, :cod_estado,:nivel,:cod_tipocuenta, :observaciones, :cuenta_auxiliar)");
// Bind
$stmt->bindParam(':numero', $numero);
$stmt->bindParam(':cod_padre', $padre);
$stmt->bindParam(':nombre', $nombre);
$stmt->bindParam(':cod_moneda', $moneda);
$stmt->bindParam(':cod_estado', $codEstado);
$stmt->bindParam(':nivel', $nivelCuenta);
$stmt->bindParam(':cod_tipocuenta', $tipoCuenta);
$stmt->bindParam(':observaciones', $observaciones);
$stmt->bindParam(':cuenta_auxiliar', $cuentaAuxiliar);

$flagSuccess=$stmt->execute();
showAlertSuccessError($flagSuccess,$urlList);

?>
