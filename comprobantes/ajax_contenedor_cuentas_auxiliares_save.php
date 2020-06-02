<?php

require_once '../conexion.php';
require_once '../functions.php';
require_once '../functionsGeneral.php';
// require_once 'configModule.php';

$dbh = new Conexion();

$cod_cuenta=$_POST["cod_cuenta"];
$nombre=$_POST["nombre"];
$tipo=$_POST["tipo"];
$proveedorCliente=$_POST["proveedor"];
$codEstado="1";
// Prepare

$stmt = $dbh->prepare("INSERT INTO cuentas_auxiliares (nombre, cod_estadoreferencial, cod_cuenta,  cod_tipoauxiliar, cod_proveedorcliente) VALUES (:nombre, :cod_estado, :cod_cuenta, :tipo, :proveedor_cliente)");
// Bind
$stmt->bindParam(':nombre', $nombre);
$stmt->bindParam(':cod_estado', $codEstado);
$stmt->bindParam(':cod_cuenta', $cod_cuenta);
$stmt->bindParam(':tipo', $tipo);
$stmt->bindParam(':proveedor_cliente', $proveedorCliente);
$flagSuccess=$stmt->execute();
if($flagSuccess){
	$cod_cuenta_auxiliar=0;
	$stmt = $dbh->prepare("SELECT codigo from cuentas_auxiliares where cod_cuenta=$cod_cuenta and cod_tipoauxiliar=$tipo and nombre='$nombre' ORDER BY codigo desc");
	$stmt->execute();
	while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
		$cod_cuenta_auxiliar=$row['codigo'];		
  	}
}
if($flagSuccess){
	echo "####1####".$cod_cuenta_auxiliar;
}else{
	echo "####0####";
}
?>

