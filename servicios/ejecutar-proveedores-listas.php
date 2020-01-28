<?php 
/*ACCESO A WEB SERVICE LISTA DE PERSONAS Y PROVEEDOR IBNORCA*/
//21-01-2020
//LLAVES DE ACCESO AL WS
$sIde = "irrhh";
$sKey = "c066ffc2a049cf11f9ee159496089a15";

/*PARAMETROS PARA LA OBTENCION DE LISTAS DE PERSONAL
	$parametros=array("sIdentificador"=>$sIde, "sKey"=>$sKey, "accion"=>"ListarPersonal"); //
*/
/*PARAMETROS PARA LA OBTENCION DE DATOS DE PERSONAL
	$parametros=array("sIdentificador"=>$sIde, "sKey"=>$sKey, "accion"=>"DatosPersonal", "IdCliente"=>32897); 
*/

/****************************
*        NUEVOS METODOS     *
****************************/
/*PARAMETROS PARA LA OBTENCION DE LISTAS DE PERSONAS POR ATRIBUTO*/
/* Valores ID Atributo 
354 	Docente
1621 	Auditor
1622 	Consultor
1623 	Empleado
*/
	//$parametros=array("sIdentificador"=>$sIde, "sKey"=>$sKey, "accion"=>"ListarPersonaxAtributo", "IdAtributo"=>354); 


/*PARAMETROS PARA LA RECUPERACION Y OBTENCION DE DATOS DE PERSONA
	$parametros=array("sIdentificador"=>$sIde, "sKey"=>$sKey, "accion"=>"DatosPersona", "IdCliente"=>5); 
*/

//PARAMETROS PARA LA OBTENCION DE LISTA DE PROVEEDORES
	$parametros=array("sIdentificador"=>$sIde, "sKey"=>$sKey, "accion"=>"ListarProveedor"); 


/*PARAMETROS PARA LA RECUPERACION Y OBTENCION DE DATOS DE PROVEEDOR
	$parametros=array("sIdentificador"=>$sIde, "sKey"=>$sKey, "accion"=>"DatosProveedor", "IdCliente"=>123); 
*/

		$parametros=json_encode($parametros);
		// abrimos la sesión cURL
		$ch = curl_init();
		// definimos la URL a la que hacemos la petición
		curl_setopt($ch, CURLOPT_URL,"http://ibnored.ibnorca.org/wsibno/rrhh/ws-personal-listas.php"); 
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