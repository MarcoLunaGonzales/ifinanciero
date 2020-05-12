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
$contador_clientes=0;
$idUsuario=$_SESSION['globalUser'];
foreach ($lista->lista as $listaCliente) {
	if($contador_clientes==0){
		$sql="DELETE FROM clientes";
        $stmt = $dbh->prepare($sql);
        $stmt->execute(); 
	}
	$codigoX=$listaCliente->IdCliente;
	$nombreX=strtoupper($listaCliente->NombreRazon);
	$idCiudad=strtoupper($listaCliente->IdCiudad);
	$estadoX=$listaCliente->Vigencia;
	$identificacionX=$listaCliente->Identificacion;
	$descuentoX=$listaCliente->DescuentoValor;
	//sacamos la unidad para insertar
	$stmt = $dbh->prepare("SELECT codigo, nombre, cod_unidad FROM ciudades where codigo=:codigo");
	$stmt->bindParam(':codigo',$idCiudad);
	$stmt->execute();
	while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
		$codigoUnidadX=$row['cod_unidad'];
	}
	// echo "codigo:".$codigoX."<br>";
	// echo "nombre:".$nombreX."<br>";
	// echo "estado:".$estadoX."<br>";
	// echo "identificacion:".$identificacionX."<br>";

	$stmt = $dbh->prepare("INSERT INTO clientes (codigo, nombre,cod_unidad,identificacion,descuento,cod_estadoreferencial) VALUES (:codigo, :nombre, :cod_unidad, :identificacion, :descuento, :cod_estado)");
	$stmt->bindParam(':codigo', $codigoX);
	$stmt->bindParam(':nombre', $nombreX);
	$stmt->bindParam(':cod_unidad', $codigoUnidadX);
	$stmt->bindParam(':identificacion', $identificacionX);
	$stmt->bindParam(':descuento', $descuentoX);
	$stmt->bindParam(':cod_estado', $estadoX);
	$flagSuccess=$stmt->execute();
	//contactos clientes
	$lista=obtenerListaContactosEmpresaDelServicio($codigoX);		
	foreach ($lista->contactos as $listas) {
		if($contador_contactos==0){
			$sql="DELETE FROM clientes_contactos";
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
		    $correo=$listas->Correo;

		    
			$sql="INSERT INTO clientes_contactos(codigo,cod_cliente,nombre,paterno,materno,cargo,telefono,identificacion,correo,cod_estadoreferencial)
		        VALUES ('$codigo','$cod_cliente','$nombre','$paterno','$materno','$cargo','$telefono','$identificacion','$correo','$vigencia')";
		     $stmt = $dbh->prepare($sql);
		     $stmt->execute();  
		     $contador_contactos++;
		}
	}
	$listaPersona=obtenerListaContactosClientesDelServicio($codigoX);
	foreach ($listaPersona->contactos as $listas) {
		if($contador_contactos==0){
			$sql="DELETE FROM clientes_contactos";
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
		    $correo=$listas->Correo;
			$sql="INSERT INTO clientes_contactos(codigo,cod_cliente,nombre,paterno,materno,cargo,telefono,identificacion,correo,cod_estadoreferencial)
		        VALUES ('$codigo','$cod_cliente','$nombre','$paterno','$materno','$cargo','$telefono','$identificacion','$correo','$vigencia')";
		     $stmt = $dbh->prepare($sql);
		     $stmt->execute();  
		     $contador_contactos++;
		}
	}
	$contador_clientes++;
}



