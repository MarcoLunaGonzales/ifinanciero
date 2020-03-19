<?php

require_once '../layouts/bodylogin.php';
require_once '../conexion.php';
require_once '../functions.php';
require_once '../functionsGeneral.php';

$dbh = new Conexion();

$codigo=$_POST["codigo"];
$nombre=$_POST["nombre"];
$tipo=$_POST["tipo"];
$proveedorCliente=$_POST["proveedor_cliente"];
$codEstado="1";


require_once 'configModule.php';

// Prepare
$stmt = $dbh->prepare("INSERT INTO $table (nombre, cod_estadoreferencial, cod_cuenta,  cod_tipoauxiliar, cod_proveedorcliente) VALUES (:nombre, :cod_estado, :cod_cuenta, :tipo, :proveedor_cliente)");
// Bind
$stmt->bindParam(':nombre', $nombre);
$stmt->bindParam(':cod_estado', $codEstado);
$stmt->bindParam(':cod_cuenta', $codigo);
$stmt->bindParam(':tipo', $tipo);
$stmt->bindParam(':proveedor_cliente', $proveedorCliente);

$flagSuccess=$stmt->execute();

showAlertSuccessError($flagSuccess,$urlList);
?>
