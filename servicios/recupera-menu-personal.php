<?php

	//parametros de acceso al WS
	$sIdentificador = "ifinanciero";
	$sKey = "ce94a8dabdf0b112eafa27a5aa475751";
	$datos=array();
	/******************************************
	*   parametros Login de usuario           *
	******************************************/
	/*Administrador*/
		$nombreuser="ivonne.casas@ibnorca.org";
		$claveuser="120e4ada5128ba5d61e5165c9e5911cb";
	
	//Usuario DNS
	//	$nombreuser="glicet.osco@ibnorca.org";
	//	$claveuser="963775a9f4326afcb14ae372759d9458";
	//
	//preparar array de parametros	
	/* descomentar	*/
	
//	$datos=array("sIdentificador"=>$sIdentificador, "sKey"=>$sKey, 
//				 "operacion"=>"Login", "nombreUser"=>$nombreuser, "claveUser"=>$claveuser);
			 
			 
	/******************************************
	*   parametros obtener de Menu de usuario *
	******************************************/
	/* descomentar 
	*/
	$datos=array("sIdentificador"=>$sIdentificador, "sKey"=>$sKey, 
				 "operacion"=>"Menu", "IdUsuario"=>227);
	
	
	$datos=json_encode($datos);
	
	//METODO CURL PARA ACCESO AL WEB SERVICE Y ENVIO DE PARAMETROS POR POST
	// abrimos la sesión cURL
	$ch = curl_init();
	// definimos la URL a la que hacemos la petición
	curl_setopt($ch, CURLOPT_URL,"http://ibnored.ibnorca.org/wsibno/verifica/ws-user-personal.php");
	// indicamos el tipo de petición: POST
	curl_setopt($ch, CURLOPT_POST, TRUE);
	// definimos cada uno de los parámetros
	curl_setopt($ch, CURLOPT_POSTFIELDS, $datos);
	// recibimos la respuesta y la guardamos en una variable
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$remote_server_output = curl_exec ($ch);
	// cerramos la sesión cURL
	curl_close ($ch);
	 
	//RECUPERACION DE RESPUESTA DEL WEB SERVICE

	header('Content-type: application/json'); 	
	print_r($remote_server_output); 
	
?>