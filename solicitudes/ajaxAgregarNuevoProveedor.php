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
 


$sIde = "ifinanciero";
$sKey = "ce94a8dabdf0b112eafa27a5aa475751";

$ciudad=(int)$_GET['ciudad'];
$otra=NULL;
if($_GET['ciudad']==""){
	$ciudad=NULL;
	$otra=$_GET['otra'];
}

 		$parametros=array("sIdentificador"=>$sIde, "sKey"=>$sKey, 
						  "accion"=>"RegistrarProveedor", //0 para nuevo registro
						  "tipoCliente"=>$_GET['tipo'], // Tipo P=Persona, E=Empresa
						  "claseCliente"=>$_GET['nacional'], // Clase N=Nacional, I=Internacional
						  "nombreRazon"=>$_GET['nombre'], //Nombre de la empresa
						  "identificacion"=>(int)$_GET['nit'], //NIT de la empresa
						  "pais"=>(int)$_GET['pais'], //Valor numerico determinado por el ws-paises opcion pais, el valor = 26 para Bolivia
						  "depto"=>(int)$_GET['estado'], //Valor numerico determinado por el ws-paises opcion estados y id pais =26 para deptos de bolivia(Ej. 480=La Paz)
						  "ciudad"=>$ciudad, //Valor numerico determinado por el ws-paises opcion ciudad y idEstado=480 para La Paz (Ej. 72=ciudad El Alto)
						  "ciudadOtro"=>$otra, // campo VARCHAR, se emplea en el caso de seleccionar otro en el campo Ciudad 
						  "direccion"=>$_GET['direccion'], //Direccion del Cliente, campo VARCHAR 
						  "telefono"=>$_GET['telefono'], //Telefono fijo del cliente, campo VARCHAR 
						  "correo"=>$_GET['correo'], //correo de la empresa
						  "nombreContacto"=>$_GET['nombre_contacto'], //Nombre del contacto que manejara la cuenta de la empresa
						  "apellidoContacto"=>$_GET['apellido_contacto'], //Apellido del contacto que manejara la cuenta de la empresa
						  "cargoContacto"=>$_GET['cargo_contacto'], //Cargo que ocupa el contacto dentro la empresa
						  "correoContacto"=>$_GET['correo_contacto'], //correo campo varchar
						  );

		$parametros=json_encode($parametros);
		// abrimos la sesión cURL
		$ch = curl_init();
		//curl_setopt($ch, CURLOPT_URL,"http://ibnored.ibnorca.org/wsibno/registro/ws-registro-proveedor.php"); // OFFICIAL
		curl_setopt($ch, CURLOPT_URL,"http://ibnored.ibnorca.org/wsibno/registro/ws-registro-proveedor.php"); // PRUEBA
		// indicamos el tipo de petición: POST
		curl_setopt($ch, CURLOPT_POST, TRUE);
		// definimos cada uno de los parámetros
		curl_setopt($ch, CURLOPT_POSTFIELDS, $parametros);
		// recibimos la respuesta y la guardamos en una variable
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$remote_server_output = curl_exec ($ch);
		curl_close ($ch);
		
		$respuesta=json_decode($remote_server_output);
		if($respuesta->estado==true){
          echo "1";
		}else{
          echo "0";
		}