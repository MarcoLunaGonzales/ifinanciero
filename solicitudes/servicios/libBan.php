<?php 

//$direccion='http://127.0.0.1/ifinanciero/wsifin/';

$direccion='http://200.105.199.164:8008/ifinanciero/wsifin/';
$sIde = "libBan";
$sKey = "89i6u32v7xda12jf96jgi30lh";

//para obtener el detalle de las libretas con idLibreta<>0
	//$parametros=array("sIdentificador"=>$sIde, "sKey"=>$sKey, "accion"=>"ObtenerLibretaBancaria","idLibreta"=>4); 


//LIBRETAS BANCARIAS DETALLE CON FILTROS
// variables para filtros
// anio -> numero (Busca el anio de detalle)
// fecha -> texto (Busca la fecha del detalle)
// monto -> numero entero o numero decimal (Busca el monto del detalle)
// nombre -> texto (Busca en la descripcion y en informacion complementaria del detalle)


//$parametros=array("sIdentificador"=>$sIde, "sKey"=>$sKey, "accion"=>"ObtenerLibretaBancaria","idLibreta"=>4,"anio"=>2020,"fecha"=>"2020-06-30","monto"=>320.00,"nombre"=>"FLORENTINO");
    //Ej:
$parametros=array("sIdentificador"=>$sIde, "sKey"=>$sKey, "accion"=>"ObtenerLibretaBancaria","idLibreta"=>4,"nombre"=>"FLORENTINO"); 

//para obtener la libreta por el idFactura
	//$parametros=array("sIdentificador"=>$sIde, "sKey"=>$sKey, "accion"=>"ObtenerLibretaBancariaPorFactura","idFactura"=>300); 

//para obtener la lista de libretas
	//$parametros=array("sIdentificador"=>$sIde, "sKey"=>$sKey, "accion"=>"ObtenerListaLibretaBancaria"); 


		$parametros=json_encode($parametros);
		// abrimos la sesión cURL
		$ch = curl_init();
		// definimos la URL a la que hacemos la petición
		curl_setopt($ch, CURLOPT_URL,$direccion."ws_obtener_libreta_bancaria.php"); 
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