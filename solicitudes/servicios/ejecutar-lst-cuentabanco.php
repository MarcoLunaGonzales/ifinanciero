<?php 
/*RECUPERAR LISTA O DATOS DE CUENTAS BANCARIAS DE PROVEEDORES O CLIENTES*/
//Creado 06-05-2020
//LLAVES DE ACCESO AL WS
$sIde = "ifinanciero";
$sKey = "ce94a8dabdf0b112eafa27a5aa475751";
$parametros=array();

//PARAMETROS PARA LISTA LAS CUENTAS BANCARIAS DE UN PROVEEDOR O CLIENTE 

 		$parametros=array("sIdentificador"=>$sIde, "sKey"=>$sKey, 
						"accion"=>"ListaCuentaBancoxCliente",
						"IdCliente" => 5, //Id del Proveedor o Cliente
						); 
	
					
//PARAMETROS PARA RECUPERAR LOS DATOS DE UNA CUENTA BANCARIA DE UN PROVEEDOR O CLIENTE 

 		/*$parametros=array("sIdentificador"=>$sIde, "sKey"=>$sKey, 
						"accion"=>"DatosCuentaBanco",
						"IdCuentaBanco" => 2, //Id del registro de cuenta bancaria
						"IdCliente" => 5 //Id del Proveedor o Cliente
						);*/



		$parametros=json_encode($parametros);
		// abrimos la sesión cURL
		$ch = curl_init();
		// definimos la URL a la que hacemos la petición
		//curl_setopt($ch, CURLOPT_URL,"http://ibnored.ibnorca.org/wsibno/lista/ws-lst-cuentabanco.php"); // OFFICIAL
		curl_setopt($ch, CURLOPT_URL,"http://ibnored.ibnorca.org/wsibnob/lista/ws-lst-cuentabanco.php"); // PRUEBA
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