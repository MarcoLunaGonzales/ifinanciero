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
set_time_limit(300);
$cod_cliente=$_GET['cod_cliente'];

$lista=obtenerListaContactosEmpresaDelServicio($cod_cliente);
$contador=0;
$idUsuario=$_SESSION['globalUser'];
foreach ($lista->contactos as $listas) {
	if($contador==0){
		$sql="DELETE FROM clientes_contactos";
        $stmt = $dbh->prepare($sql);
        $stmt->execute(); 
	}
	$codigo=$listas->IdContacto;	
	$cod_cliente=$listas->IdCliente;
	$nombre=$listas->NombreContacto;
	$paterno=$listas->PaternoContacto;
	$materno=$listas->MaternoContacto;
	$cargo=$listas->CargoContacto;
	$telefono=$listas->FonoContacto;
	$vigencia=$listas->Vigencia;
    $identificacion=$listas->Identificacion;


	$sql="INSERT INTO clientes_contactos(codigo,cod_cliente,nombre,paterno,materno,cargo,telefono,identificacion,cod_estadoreferencial)
        VALUES ('$codigo','$cod_cliente','$nombre','$paterno','$materno','$cargo','$telefono','$identificacion','$vigencia')";
     $stmt = $dbh->prepare($sql);
     $stmt->execute();  
     $contador++;
}
$listaPersona=obtenerListaContactosClientesDelServicio($cod_cliente);
foreach ($listaPersona->contactos as $listas) {
	if($contador==0){
		$sql="DELETE FROM clientes_contactos";
        $stmt = $dbh->prepare($sql);
        $stmt->execute(); 
	}
	$codigo=$listas->IdContacto;	
	$cod_cliente=$listas->IdCliente;
	$nombre=$listas->NombreContacto;
	$paterno=$listas->PaternoContacto;
	$materno=$listas->MaternoContacto;
	$cargo=$listas->CargoContacto;
	$telefono=$listas->FonoContacto;
	$vigencia=$listas->Vigencia;
    $identificacion=$listas->Identificacion;

	$sql="INSERT INTO clientes_contactos(codigo,cod_cliente,nombre,paterno,materno,cargo,telefono,identificacion,cod_estadoreferencial)
        VALUES ('$codigo','$cod_cliente','$nombre','$paterno','$materno','$cargo','$telefono','$identificacion','$vigencia')";
     $stmt = $dbh->prepare($sql);
     $stmt->execute();  
     $contador++;
}
