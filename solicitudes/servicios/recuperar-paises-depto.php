<?php 
/*ACCESO AL SERVICIO WEB PAISES ESTADO CIUDAD*/
//LLAVES DE ACCESO AL WS
$sIde = "ifinanciero";
$sKey = "ce94a8dabdf0b112eafa27a5aa475751";

/*PARAMETROS PARA LA OBTENCION DE LISTADOS*/
// cambiar esta linea por las demas opciones
	$parametros=array("sIdentificador"=>$sIde, "sKey"=>$sKey, "TipoLista"=>"paises"); //Lista todos los paises
	//$parametros=array("sIdentificador"=>$sIde, "sKey"=>$sKey, "TipoLista"=>"estados", "IdPais"=>"26"); // Lista de estados o departamentos de un pais x
	//$parametros=array("sIdentificador"=>$sIde, "sKey"=>$sKey, "TipoLista"=>"ciudades", "IdEstado"=>480); // Lista de las ciudades de un estado x

		$parametros=json_encode($parametros);
		// abrimos la sesión cURL
		$ch = curl_init();
		// definimos la URL a la que hacemos la petición
		curl_setopt($ch, CURLOPT_URL,"http://ibnored.ibnorca.org/wsibno/clasificador/ws-paises.php"); // OFICIAL
		// curl_setopt($ch, CURLOPT_URL,"http://ibnored.ibnorca.org/wsibnob/clasificador/ws-paises.php"); // PRUEBA
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