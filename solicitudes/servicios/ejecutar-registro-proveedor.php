<?php 
require_once '../../functions.php';
$direccion=obtenerValorConfiguracion(42);//direccion des servicio web
/*REGISTRO DE CLIENTES*/
//21-01-2020, 28-01-2020
//LLAVES DE ACCESO AL WS
$sIde = "ifinanciero";
$sKey = "ce94a8dabdf0b112eafa27a5aa475751";

/*PARAMETROS PARA VERIFICAR EMPRESA PROVEEDORA*/

 		$parametros=array("sIdentificador"=>$sIde, "sKey"=>$sKey, 
						  "accion"=>"VerificaIdentificacionEmpresa", 
						  "identificacion"=>3440079, //NIT de la empresa
						  );

/*PARAMETROS PARA REGISTRAR UNA EMPRESA PROVEEDORA

 		$parametros=array("sIdentificador"=>$sIde, "sKey"=>$sKey, 
						  "accion"=>"RegistrarProveedor", //0 para nuevo registro
						  "tipoCliente"=>"E", // Tipo P=Persona, E=Empresa
						  "claseCliente"=>"N", // Clase N=Nacional, I=Internacional
						  "nombreRazon"=>"Empresa Proveedora Dos", //Nombre de la empresa
						  "identificacion"=>124578, //NIT de la empresa
						  "pais"=>26, //Valor numerico determinado por el ws-paises opcion pais, el valor = 26 para Bolivia
						  "depto"=>480, //Valor numerico determinado por el ws-paises opcion estados y id pais =26 para deptos de bolivia(Ej. 480=La Paz)
						  "ciudad"=>72, //Valor numerico determinado por el ws-paises opcion ciudad y idEstado=480 para La Paz (Ej. 72=ciudad El Alto)
						  "ciudadOtro"=>NULL, // campo VARCHAR, se emplea en el caso de seleccionar otro en el campo Ciudad 
						  "direccion"=>"Miraflores, Av. Saavedra", //Direccion del Cliente, campo VARCHAR 
						  "telefono"=>"2457896", //Telefono fijo del cliente, campo VARCHAR 
						  "correo"=>"mail@dosempresa.com", //correo de la empresa
						  "nombreContacto"=>"Javier", //Nombre del contacto que manejara la cuenta de la empresa
						  "apellidoContacto"=>"Prueba", //Apellido del contacto que manejara la cuenta de la empresa
						  "cargoContacto"=>"Gerente", //Cargo que ocupa el contacto dentro la empresa
						  "correoContacto"=>"mailotrocontacto@empresa.com", //correo campo varchar
						  );
*/

		$parametros=json_encode($parametros);
		// abrimos la sesión cURL
		$ch = curl_init();
		// definimos la URL a la que hacemos la petición
		curl_setopt($ch, CURLOPT_URL,$direccion."registro/ws-registro-proveedor.php"); // OFFICIAL
		// curl_setopt($ch, CURLOPT_URL,"http://ibnored.ibnorca.org/wsibnob/registro/ws-registro-proveedor.php"); // PRUEBA
		// indicamos el tipo de petición: POST
		curl_setopt($ch, CURLOPT_POST, TRUE);
		// definimos cada uno de los parámetros
		curl_setopt($ch, CURLOPT_POSTFIELDS, $parametros);
		// recibimos la respuesta y la guardamos en una variable
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$remote_server_output = curl_exec ($ch);
		// cerramos la sesión cURL
		curl_close ($ch);
		
		// imprimir en formato JSON
		header('Content-type: application/json'); 	
		print_r($remote_server_output); 			

?>