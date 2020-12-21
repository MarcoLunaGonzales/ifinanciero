<?php 
//$direccion='http://ibnored.ibnorca.org/ifinanciero/wsifin/';
$direccion='http://127.0.0.1/ifinanciero/wsifin/';
$sIde = "facifin";
$sKey = "rrf656nb2396k6g6x44434h56jzx5g6";

//$parametros=array("sIdentificador"=>$sIde, "sKey"=>$sKey, "accion"=>"VerificacionPagosCurso");
$parametros=array("sIdentificador"=>$sIde, "sKey"=>$sKey, "accion"=>"VerificacionPagosCurso","IdCurso"=>2572,"Ci"=>"1149564568699");
		
		$parametros=json_encode($parametros);
		// abrimos la sesión cURL
		$ch = curl_init();
		// definimos la URL a la que hacemos la petición
		curl_setopt($ch, CURLOPT_URL,$direccion."ws_obtener_factura.php"); 
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

		