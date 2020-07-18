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
set_time_limit(0);

$contador_contactos=0;
$contador_clientes=0;
$idUsuario=$_SESSION['globalUser'];
$codigo_cliente=$_GET['codigo'];

//lista de contactos
$lista=obtenerListaClientesWS();
foreach ($lista->lista as $listaCliente) {
	if($contador_clientes==0){
		$sql="DELETE FROM clientes where codigo<>0";
        $stmtDeleteClientes = $dbh->prepare($sql);
        $stmtDeleteClientes->execute(); 
	}
	if(isset($listaCliente->IdCliente))$codigoX=$listaCliente->IdCliente;
	else $codigoX=0;
	if($codigoX!=0){
		if(isset($listaCliente->NombreRazon)){
			$nombreX=strtoupper($listaCliente->NombreRazon);
		}else $nombreX="";
		if(isset($listaCliente->IdCiudad)){
			$idCiudad=strtoupper($listaCliente->IdCiudad);		
		}else $idCiudad=0;
		if(isset($listaCliente->Vigencia)){
			$estadoX=$listaCliente->Vigencia;
		}else $estadoX=0;
		if(isset($listaCliente->Identificacion)){
			$identificacionX=$listaCliente->Identificacion;
		}else $identificacionX=0;
		if(isset($listaCliente->DescuentoValor)){
			$descuentoX=$listaCliente->DescuentoValor;
		}else $descuentoX=0;
		//sacamos la unidad para insertar
		$stmtCiudades = $dbh->prepare("SELECT codigo, nombre, cod_unidad FROM ciudades where codigo=:codigo");
		$stmtCiudades->bindParam(':codigo',$idCiudad);
		$stmtCiudades->execute();
		$codigoUnidadX=0;
		while ($rowCiudades = $stmtCiudades->fetch(PDO::FETCH_ASSOC)) {
			$codigoUnidadX=$rowCiudades['cod_unidad'];
		}
		// echo "codigo:".$codigoX." - ";
		// echo "nombre:".$nombreX." - ";
		// echo "estado:".$estadoX." - ";
		// echo "identificacion:".$identificacionX."<br>";

		$stmtInsertClientes = $dbh->prepare("INSERT INTO clientes (codigo, nombre,cod_unidad,identificacion,descuento,cod_estadoreferencial) VALUES (:codigo, :nombre, :cod_unidad, :identificacion, :descuento, :cod_estado)");
		$stmtInsertClientes->bindParam(':codigo', $codigoX);
		$stmtInsertClientes->bindParam(':nombre', $nombreX);
		$stmtInsertClientes->bindParam(':cod_unidad', $codigoUnidadX);
		$stmtInsertClientes->bindParam(':identificacion', $identificacionX);
		$stmtInsertClientes->bindParam(':descuento', $descuentoX);
		$stmtInsertClientes->bindParam(':cod_estado', $estadoX);
		$flagSuccess=$stmtInsertClientes->execute();
		$contador_clientes++;
	}	
}
$listaPersona=obtenerListaContactosClientesDelServicio($codigo_cliente);
foreach ($listaPersona->contactos as $listas) {		
	if($contador_contactos==0){
		$sql="DELETE FROM clientes_contactos where cod_cliente=$codigo_cliente";
        $stmtDelteCont = $dbh->prepare($sql);
        $stmtDelteCont->execute(); 
	}
	$vigencia=$listas->Vigencia;	
	if($vigencia==1){						
		$cod_cliente=$codigo_cliente;
		if(isset($listas->IdContacto))$codigo=$listas->IdContacto;
		else $codigo='';
		if(isset($listas->NombreContacto))$nombre=$listas->NombreContacto;
		else $nombre='';
		if(isset($listas->PaternoContacto))$paterno=$listas->PaternoContacto;
		else $paterno='';
		if(isset($listas->MaternoContacto))$materno=$listas->MaternoContacto;
		else $materno='';
		if(isset($listas->CargoContacto))$cargo=$listas->CargoContacto;
		else $cargo='';
		if(isset($listas->FonoContacto))$telefono=$listas->FonoContacto;
		else $telefono='';
		if(isset($listas->Identificacion))$identificacion=$listas->Identificacion;
		else $identificacion='';
		if(isset($listas->Correo))$correo=$listas->Correo;
		else $correo='';			

		// echo "codigo:".$codigo." - ";
		// echo "nombre:".$nombre." - ";
		// echo "correo:".$correo." - ";
		// echo "identificacion:".$identificacion." <br><br> ";			
		$sql="INSERT INTO clientes_contactos(codigo,cod_cliente,nombre,paterno,materno,cargo,telefono,identificacion,correo,cod_estadoreferencial)
	        VALUES ('$codigo','$cod_cliente','$nombre','$paterno','$materno','$cargo','$telefono','$identificacion','$correo','$vigencia')";
	     $stmtInsertContactos = $dbh->prepare($sql);
	     $stmtInsertContactos->execute();  
	     $contador_contactos++;
	}
}
	// contactos clientes
$lista=obtenerListaContactosEmpresaDelServicio($codigo_cliente);		
foreach ($lista->contactos as $listas) {		
	if($contador_contactos==0){
		$sql="DELETE FROM clientes_contactos where cod_cliente=$codigo_cliente";
        $stmtDelteCont = $dbh->prepare($sql);
        $stmtDelteCont->execute(); 
	}
	$vigencia=$listas->Vigencia;
	if($vigencia==1){
		$codigo=$listas->IdContacto;	
		$cod_cliente=$listas->IdCliente;
		if(isset($listas->NombreContacto))$nombre=$listas->NombreContacto;
		else $nombre='';
		if(isset($listas->PaternoContacto))$paterno=$listas->PaternoContacto;
		else $paterno='';
		if(isset($listas->MaternoContacto))$materno=$listas->MaternoContacto;
		else $materno='';
		if(isset($listas->CargoContacto))$cargo=$listas->CargoContacto;
		else $cargo='';
		if(isset($listas->FonoContacto))$telefono=$listas->FonoContacto;
		else $telefono='';
		if(isset($listas->Identificacion))$identificacion=$listas->Identificacion;
		else $identificacion='';
		if(isset($listas->Correo))$correo=$listas->Correo;
		else $correo='';

		$sql="INSERT INTO clientes_contactos(codigo,cod_cliente,nombre,paterno,materno,cargo,telefono,identificacion,correo,cod_estadoreferencial)
	        VALUES ('$codigo','$cod_cliente','$nombre','$paterno','$materno','$cargo','$telefono','$identificacion','$correo','$vigencia')";
	     $stmtInsertContactos = $dbh->prepare($sql);
	     $stmtInsertContactos->execute();  
	     $contador_contactos++;
	}
}
?>
