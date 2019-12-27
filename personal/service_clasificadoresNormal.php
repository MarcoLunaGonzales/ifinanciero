<?php 
/*ACCESO A WEB SERVICE CLASIFICADORES*/
//LLAVES DE ACCESO AL WS
$sIde = "monitoreo"; // De acuerdo al sistema
$sKey = "837b8d9aa8bb73d773f5ef3d160c9b17"; // llave de acuerdo al sistema

//$codigoPadreX=$_GET["padre"];

/*PARAMETROS PARA LA OBTENCION DEL LISTADO DEL CLASIFICADOR*/
//$parametros=array("sIdentificador"=>$sIde, "sKey"=>$sKey, "padre"=>$codigoPadreX); // cambiar padre por los valores correspondientes
$parametros=array("sIdentificador"=>$sIde, "sKey"=>$sKey, "lista"=>"HijoPadre", "padre"=>"1580");
//$parametros=array("sIdentificador"=>$sIde, "sKey"=>$sKey, "lista"=>"HijoPadre", "padre"=>"403");
//$parametros=array("sIdentificador"=>$sIde, "sKey"=>$sKey, "lista"=>"HijoPadre", "padre"=>"821");


//PARA SACAR POR NIVELES
//$parametros=array("sIdentificador"=>$sIde, "sKey"=>$sKey, "lista"=>"Niveles", "padre"=>"80");

		$parametros=json_encode($parametros);
		// abrimos la sesión cURL
		$ch = curl_init();
		// definimos la URL a la que hacemos la petición
		curl_setopt($ch, CURLOPT_URL,"http://ibnored.ibnorca.org/wsibno/clasificador/ws-clasificador-post.php");
		//curl_setopt($ch, CURLOPT_URL,"http://localhost/wsibno/clasificador/ws-clasificador-post.php");
		// indicamos el tipo de petición: POST
		curl_setopt($ch, CURLOPT_POST, TRUE);
		// definimos cada uno de los parámetros
		curl_setopt($ch, CURLOPT_POSTFIELDS, $parametros);
		// recibimos la respuesta y la guardamos en un2a variable
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$remote_server_output = curl_exec ($ch);
		// cerramos la sesión cURL
		curl_close ($ch);
		
		// imprimir en formato JSON
		header('Content-type: application/json'); 	
		print_r($remote_server_output); 			

?>