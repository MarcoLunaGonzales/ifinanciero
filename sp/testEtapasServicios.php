<?php 
//SERVICIOS
//2021-05-20
//LLAVES DE ACCESO AL WS
$sIde = "ifinanciero";
$sKey = "ce94a8dabdf0b112eafa27a5aa475751";
$parametros=array();

/*PARAMETROS PARA LISTA DE ETAPAS DE SERVICIOS POR ID TIPO */

 		$parametros=array("sIdentificador"=>$sIde, "sKey"=>$sKey, 
						"accion"=>"ListarEtapasxTipoServicio",
						"IdTipoServicio" =>108
						); 
	
					
		$parametros=json_encode($parametros);
		// abrimos la sesión cURL
		$ch = curl_init();
		// definimos la URL a la que hacemos la petición
		//curl_setopt($ch, CURLOPT_URL,"http://ibnored.ibnorca.org/wsibno/servicio/ws-servicio.php"); // OFFICIAL
		curl_setopt($ch, CURLOPT_URL,"http://ibnored.ibnorca.org/wsibnob/servicio/ws-servicio.php"); // PRUEBA
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