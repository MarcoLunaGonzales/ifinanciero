<?php
session_start();
require_once '../conexion.php';
require_once '../functionsGeneral.php';
require_once '../functions.php';
require_once '../styles.php';

$dbh = new Conexion();

$sqlX="SET NAMES 'utf8'";
$stmtX = $dbh->prepare($sqlX);
$stmtX->execute();
set_time_limit(1000);

//lista de contactos
$lista=obtenerListaClientesWS();
$contador_contactos=0;
$contador_clientes=0;
$idUsuario=$_SESSION['globalUser'];
foreach ($lista->lista as $listaCliente) {
	if($contador_clientes==0){
		$sql="DELETE FROM clientes where codigo<>0";
        $stmt = $dbh->prepare($sql);
        $stmt->execute(); 
	}
	$codigoX=$listaCliente->IdCliente;
	$nombreX=strtoupper($listaCliente->NombreRazon);
	if(isset($listaCliente->IdCiudad))
		$idCiudad=strtoupper($listaCliente->IdCiudad);		
	else $idCiudad=0;
	if(isset($listaCliente->Vigencia))
		$estadoX=$listaCliente->Vigencia;
	else $estadoX=0;
	if(isset($listaCliente->Identificacion))
		$identificacionX=$listaCliente->Identificacion;
	else $identificacionX=0;
	if(isset($listaCliente->DescuentoValor))
		$descuentoX=$listaCliente->DescuentoValor;
	else $descuentoX=0;
	//sacamos la unidad para insertar
	$stmt = $dbh->prepare("SELECT codigo, nombre, cod_unidad FROM ciudades where codigo=:codigo");
	$stmt->bindParam(':codigo',$idCiudad);
	$stmt->execute();
	$codigoUnidadX=0;
	while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
		$codigoUnidadX=$row['cod_unidad'];
	}
	$stmt = $dbh->prepare("INSERT INTO clientes (codigo, nombre,cod_unidad,identificacion,descuento,cod_estadoreferencial) VALUES (:codigo, :nombre, :cod_unidad, :identificacion, :descuento, :cod_estado)");
	$stmt->bindParam(':codigo', $codigoX);
	$stmt->bindParam(':nombre', $nombreX);
	$stmt->bindParam(':cod_unidad', $codigoUnidadX);
	$stmt->bindParam(':identificacion', $identificacionX);
	$stmt->bindParam(':descuento', $descuentoX);
	$stmt->bindParam(':cod_estado', $estadoX);
	$flagSuccess=$stmt->execute();
	$contador_clientes++;
}
?>


