<?php

require_once '../layouts/bodylogin.php';
require_once '../conexion.php';
require_once '../functions.php';
require_once '../functionsGeneral.php';
require_once 'configModule.php';

$dbh = new Conexion();

$codigo=$_POST["codigo"];
$numero=$_POST["numero"];
$padre=$_POST["padre"];
$codigoPadre=$_POST["codigo_padre"];
$nombre=$_POST["nombre"];
$tipoCuenta=$_POST["tipocuenta"];
$moneda=$_POST["moneda"];
$codEstado="1";
$observaciones="";
$cuentaAuxiliar=$_POST["cuenta_auxiliar"];

$nivelCuenta=buscarNivelCuenta($numero);

$numero=str_replace(".", "", $numero);


// Prepare
$sql="UPDATE $table SET numero=:numero, cod_padre=:cod_padre, nombre=:nombre, cod_moneda=:cod_moneda, nivel=:nivel, cod_tipocuenta=:cod_tipocuenta, observaciones=:observaciones, cuenta_auxiliar=:cuenta_auxiliar WHERE codigo=:codigo";
$stmt = $dbh->prepare($sql);
$stmt->bindParam(':codigo', $codigo);
$stmt->bindParam(':numero', $numero);
$stmt->bindParam(':cod_padre', $codigoPadre);
$stmt->bindParam(':nombre', $nombre);
$stmt->bindParam(':cod_moneda', $moneda);
$stmt->bindParam(':nivel', $nivelCuenta);
$stmt->bindParam(':cod_tipocuenta', $tipoCuenta);
$stmt->bindParam(':observaciones', $observaciones);
$stmt->bindParam(':cuenta_auxiliar', $cuentaAuxiliar);

$flagSuccess=$stmt->execute();
showAlertSuccessError($flagSuccess,$urlList);

?>