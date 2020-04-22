<?php
session_start();
require_once '../conexion.php';
require_once '../functionsGeneral.php';
require_once '../functions.php';
require_once '../styles.php';

$dbh = new Conexion();
$direccion=obtenerValorConfiguracion(42);//direccion des servicio web
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
if($_GET['identificacion']==""){
	$identificacion=NULL;
}else{
	$identificacion=(int)$_GET['identificacion'];
}



  // Tipo P=Persona, E=Empresa
if($_GET['tipo']=='E'){
	$parametros=array("sIdentificador"=>$sIde, "sKey"=>$sKey, 
						  "accion"=>"RegistrarProveedor", //0 para nuevo registro
						  "tipoCliente"=>$_GET['tipo'], // Tipo P=Persona, E=Empresa
						  "claseCliente"=>$_GET['nacional'], // Clase N=Nacional, I=Internacional
						  "nombreRazon"=>$_GET['nombre'], //Nombre de la empresa
						  "identificacion"=>$identificacion, //NIT de la empresa
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

						  "optFactura"=>1 // 1=datos de factura, 0 = sin datos de factura
						  "facturaRazon"=>$_GET['nombre'], // Razon Social de la factura Puede ser el mismo nombre empresa, NULL en caso de optFactura=0
						  "facturaNIT"=>$identificacion // NIT para factura el mismo del IDENTIFICACION, NULL en caso de optFactura=0
						  );

}else{//para el cliente
	if($_GET['nacional']=='N'){
		/*PARAMETROS PARA REGISTRAR UNA PERSONA PROVEEDORA NACIONAL */
		$parametros=array("sIdentificador"=>$sIde, "sKey"=>$sKey, 
						  "accion"=>"RegistrarProveedor", 
						  "tipoCliente"=>"P", // Tipo P=Persona
						  "claseCliente"=>"N", // Clase N=Nacional, I=Internacional
						  "nombreRazon"=>$_GET['nombre_p'], //Nombre de la persona o empresa
						  "paterno"=>$_GET['paterno_p'], //Apellido paterno del cliente persona
						  "materno"=>$_GET['materno_p'], //Apellido materno del cliente persona
						  "tipoId"=>$_GET['tipo_id'], //tipo de identificacion valor numerico determinado por el clasificador tipo de documento id padre 1580 (1581=CI, 1582=Pasaporte)
						  "tipoIdOtro"=>$_GET['tipo_id_otro'], // Otro tipo de identificacion campo VARCHAR, se debe habilitar en el caso de seleccionar otro en el campo tipoId
						  "identificacion"=>$identificacion, // Numero de identificacion, campo VARCHAR
						  "emision"=>480, //Lugar de emision en Bolivia, Valor numerico determinado por el ws-paises opcion estados y idPais 26=Bolivia(Ej. 480=La Paz)
						  "emisionOtro"=>NULL, //Otro lugar de emision campo VARCHAR, se emplea en el caso de seleccionar otro en el campo emision
						  "nacionalidad"=>26, //Pais de origen valor numerico, determinado por el listado de paises (web service paises) 26=Bolivia
						  "pais"=>(int)$_GET['pais'], //Valor numerico determinado por el ws-paises opcion pais, el valor = 26 para Bolivia
						  "depto"=>(int)$_GET['estado'], //Valor numerico determinado por el ws-paises opcion estados y id pais =26 para deptos de bolivia(Ej. 480=La Paz)
						  "ciudad"=>$ciudad, //Valor numerico determinado por el ws-paises opcion ciudad y idEstado=480 para La Paz (Ej. 72=ciudad El Alto)
						  "ciudadOtro"=>$otra, // campo VARCHAR, se emplea en el caso de seleccionar otro en el campo Ciudad 
						  "direccion"=>$_GET['direccion'], //Direccion del Cliente, campo VARCHAR 
						  "telefono"=>$_GET['telefono'], //Telefono fijo del cliente, campo VARCHAR 
						  "movil"=>NULL, //Telefono Movil o Celular, campo VARCHAR 
						  "correo"=>$_GET['correo'], //correo que posteriormente servira de nombre de usuario para el acceso a la cuenta, campo varchar
						  "optFactura"=>1 // 1=datos de factura, 0 = sin datos de factura
						  "facturaRazon"=>$_GET['nombre_p'], // Razon Social de la factura, NULL en caso de optFactura=0
						  "facturaNIT"=>$nit // NIT para factura, NULL en caso de optFactura=0
						  );
	

	}else{

	}

}
/*PARAMETROS PARA REGISTRAR UNA EMPRESA PROVEEDORA*/

 		











 		

		// $parametros=json_encode($parametros);
		// // abrimos la sesión cURL
		// $ch = curl_init();
		// //curl_setopt($ch, CURLOPT_URL,$direccion."registro/ws-registro-proveedor.php"); // OFFICIAL
		// curl_setopt($ch, CURLOPT_URL,$direccion."registro/ws-registro-proveedor.php"); // PRUEBA
		// // indicamos el tipo de petición: POST
		// curl_setopt($ch, CURLOPT_POST, TRUE);
		// // definimos cada uno de los parámetros
		// curl_setopt($ch, CURLOPT_POSTFIELDS, $parametros);
		// // recibimos la respuesta y la guardamos en una variable
		// curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		// $remote_server_output = curl_exec ($ch);
		// curl_close ($ch);
		
		// $respuesta=json_decode($remote_server_output);
		// if($respuesta->estado==true){
  //         echo "1";
		// }else{
  //         echo "0";
		// }