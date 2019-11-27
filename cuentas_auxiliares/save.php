<?php

require_once '../layouts/bodylogin.php';
require_once '../conexion.php';
require_once '../functions.php';
require_once '../functionsGeneral.php';

$dbh = new Conexion();

$codigo=$_POST["codigo"];
$codigoCuenta=$_POST["codigo"];
$nombre=$_POST["nombre"];
$banco=$_POST["banco"];
$nroCuenta=$_POST["nro_cuenta"];
$direccion=$_POST["direccion"];
$telefono=$_POST["telefono"];
$codEstado="1";
$referencia1=$_POST["referencia1"];
$referencia2=$_POST["referencia2"];


require_once 'configModule.php';


// Prepare
$stmt = $dbh->prepare("INSERT INTO $table (nombre, cod_banco, nro_cuenta, direccion, telefono, referencia1, referencia2, cod_estadoreferencial, cod_cuenta) VALUES (:nombre, :cod_banco, :nro_cuenta ,:direccion, :telefono, :referencia1, :referencia2, :cod_estadoreferencial, :cod_cuenta)");
// Bind
$stmt->bindParam(':nombre', $nombre);
$stmt->bindParam(':cod_banco', $banco);
$stmt->bindParam(':nro_cuenta', $nroCuenta);
$stmt->bindParam(':direccion', $direccion);
$stmt->bindParam(':telefono', $telefono);
$stmt->bindParam(':referencia1', $referencia1);
$stmt->bindParam(':referencia2', $referencia2);
$stmt->bindParam(':cod_estadoreferencial', $codEstado);
$stmt->bindParam(':cod_cuenta', $codigoCuenta);

$flagSuccess=$stmt->execute();


showAlertSuccessError($flagSuccess,$urlList);

?>
