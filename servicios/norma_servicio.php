<?php 
/*ACCESO AL CATALOGO DE NORMAS NACIONALES*/
//LLAVES DE ACCESO AL WS
$sIde = "monitoreo"; // De acuerdo al sistema
$sKey = "837b8d9aa8bb73d773f5ef3d160c9b17"; // llave de acuerdo al sistema

/*PARAMETROS PARA LA OPTENCION DEL CATALOGO*/
// cambiar esta linea por las demas opciones
	$parametros=array("sIdentificador"=>$sIde, "sKey"=>$sKey, "TipoLista"=>"Todos"); //Lista todas las normas
	//$parametros=array("sIdentificador"=>$sIde, "sKey"=>$sKey, "TipoLista"=>"Detalle", "IdNorma"=>"3347"); // Lista el detalle de una norma
	//$parametros=array("sIdentificador"=>$sIde, "sKey"=>$sKey, "TipoLista"=>"NormasxSector", "IdSector"=>"7"); // Lista las normas de un determinado Sector
	//$parametros=array("sIdentificador"=>$sIde, "sKey"=>$sKey, "TipoLista"=>"NormasxComite", "IdComite"=>"52"); // Lista las normas de un determinado Comite

		$parametros=json_encode($parametros);
		// abrimos la sesión cURL
		$ch = curl_init();
		// definimos la URL a la que hacemos la petición
		curl_setopt($ch, CURLOPT_URL,"http://ibnored.ibnorca.org/wsibnob/catalogo/ws-catalogo-nal.php");
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