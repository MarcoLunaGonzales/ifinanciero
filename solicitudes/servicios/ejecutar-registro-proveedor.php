<?php 
require_once '../../functions.php';
$direccion=obtenerValorConfiguracion(42);//direccion des servicio web
/*REGISTRO DE CLIENTES*/
//21-01-2020, 28-01-2020
//LLAVES DE ACCESO AL WS
$sIde = "ifinanciero";
$sKey = "ce94a8dabdf0b112eafa27a5aa475751";

/*PARAMETROS PARA VERIFICAR EMPRESA PROVEEDORA*/

 		/*$parametros=array("sIdentificador"=>$sIde, "sKey"=>$sKey, 
						  "accion"=>"VerificaIdentificacionEmpresa", 
						  "identificacion"=>6298608011, //NIT de la empresa
						  );*/

/*PARAMETROS PARA REGISTRAR UNA EMPRESA PROVEEDORA*/

 		$parametros=array("sIdentificador"=>$sIde, "sKey"=>$sKey, 
						  "accion"=>"RegistrarProveedor", //0 para nuevo registro
						  "tipoCliente"=> "E",
	"claseCliente"=> "N",
	"nombreRazon"=> "PROVEEDOR DE PRUEBA",
	"identificacion"=> "97770907813",
	"pais"=> 26,
	"depto"=> 480,
	"ciudad"=> 62,
	"ciudadOtro"=> null,
	"direccion"=> null,
	"telefono"=> null,
	"correo"=> null,
	"nombreContacto"=> null,
	"apellidoContacto"=> null,
	"cargoContacto"=> null,
	"correoContacto"=> null,
	"optFactura"=> 1,
	"facturaRazon"=> "PROVEEDOR DE PRUEBA",
	"facturaNIT"=> "97770907813"
						  );


		$parametros=json_encode($parametros);
		// abrimos la sesión cURL
		$ch = curl_init();
		// definimos la URL a la que hacemos la petición
		curl_setopt($ch, CURLOPT_URL,"http://ibnored.ibnorca.org/wsibnob/registro/ws-registro-proveedor.php"); // OFFICIAL
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