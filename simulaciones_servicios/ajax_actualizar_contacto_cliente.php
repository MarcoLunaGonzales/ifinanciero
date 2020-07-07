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

//lista de contactos
$lista=obtenerListaClientesWS();



$contador_contactos=0;
$cod_cliente=$_GET['cod_cliente'];
//contactos clientes
$lista=obtenerListaContactosEmpresaDelServicio($cod_cliente);		
foreach ($lista->contactos as $listas) {
	if($contador_contactos==0){
		$sql="DELETE FROM clientes_contactos where cod_cliente=$cod_cliente";
        $stmt = $dbh->prepare($sql);
        $stmt->execute(); 
	}
	$vigencia=$listas->Vigencia;
	if($vigencia==1){
		$codigo=$listas->IdContacto;	
		$cod_cliente=$listas->IdCliente;
		$nombre=$listas->NombreContacto;
		$paterno=$listas->PaternoContacto;
		$materno=$listas->MaternoContacto;
		$cargo=$listas->CargoContacto;
		$telefono=$listas->FonoContacto;
		if(isset($listas->Identificacion))$identificacion=$listas->Identificacion;
		else $identificacion=0;
		if(isset($listas->Correo))$correo=$listas->Correo;
		else $correo="";

		$sql="INSERT INTO clientes_contactos(codigo,cod_cliente,nombre,paterno,materno,cargo,telefono,identificacion,correo,cod_estadoreferencial)
	        VALUES ('$codigo','$cod_cliente','$nombre','$paterno','$materno','$cargo','$telefono','$identificacion','$correo','$vigencia')";
	     $stmt = $dbh->prepare($sql);
	     $stmt->execute();  
	     $contador_contactos++;
	}
}
$listaPersona=obtenerListaContactosClientesDelServicio($cod_cliente);
foreach ($listaPersona->contactos as $listas) {
	if($contador_contactos==0){
		$sql="DELETE FROM clientes_contactos where cod_cliente=$cod_cliente";
        $stmt = $dbh->prepare($sql);
        $stmt->execute(); 
	}
	$vigencia=$listas->Vigencia;
	if($vigencia==1){
		$codigo=$listas->IdContacto;	
		$cod_cliente=$listas->IdCliente;
		$nombre=$listas->NombreContacto;
		$paterno=$listas->PaternoContacto;
		$materno=$listas->MaternoContacto;
		$cargo=$listas->CargoContacto;
		$telefono=$listas->FonoContacto;
	    $identificacion=$listas->Identificacion;	    
	    if(isset($listas->Correo))$correo=$listas->Correo;
		else $correo="";
		$sql="INSERT INTO clientes_contactos(codigo,cod_cliente,nombre,paterno,materno,cargo,telefono,identificacion,correo,cod_estadoreferencial)
	        VALUES ('$codigo','$cod_cliente','$nombre','$paterno','$materno','$cargo','$telefono','$identificacion','$correo','$vigencia')";
	     $stmt = $dbh->prepare($sql);
	     $stmt->execute();  
	     $contador_contactos++;
	}
}
// $contador_clientes++;


?>