<?php 
/*ACCESO A WEB SERVICE INSCRIPCION ALUMNO - FINANCIERO*/
//23/05/2020;  
//LLAVES DE ACCESO AL WS
$sIde = "ifinanciero";
$sKey = "ce94a8dabdf0b112eafa27a5aa475751";
/*METODOS*/

//Los valores de los parametros son obtenidos del alumno o los datos del curso
/* VERIFICAR INSCRIPCION ALUMNO 
$parametros=array("sIdentificador"=>$sIde, "sKey"=>$sKey, 
					"accion"=>"VerificarInscripcionAlumno", 
					"Identificacion"=>49384419, //ci, obtenido del alumno
					"IdCurso"=>2272, //obtenido del curso
					);
*/
/* INSCRIBIR ALUMNO 
$parametros=array("sIdentificador"=>$sIde, "sKey"=>$sKey, 
					"accion"=>"InscribirAlumno", 
					"Identificacion"=>1001871028, //obtenido del login
					"IdCurso"=>1775, //obtenido del curso
					"IdNivel"=>1715, //obtenido del curso
					"IdDescuento"=>239, //por ahora valor fijo hasta determinar metodo de descuento
					"Convalidacion"=>0, //por ahora valor fijo hasta determinar metodo de covalidacion
					"IdUsuarioReg"=>12345, //el id del usuario que registra
					"Plataforma"=>13, // 13=Sistema Financiero
					"IdComoSeEntero"=>1000 // Valor Id del Clasificador Como Se Entero IdPadre=225
					);
*/
/* ASIGNAR MODULO ALUMNO 
$parametros=array("sIdentificador"=>$sIde, "sKey"=>$sKey, 
					"accion"=>"AsignarModulo", 
					"Identificacion"=>49384419, 	//datos del ci del alumno
					"IdCurso"=>1775, 	//obtenido del curso
					"IdModulo"=>3645, 	//obtenido del curso 
					"IdUsuarioReg"=>12345, // el id del usuario qu registra
					"Plataforma"=>13 // 13=Sistema Financiero
					);
*/
/* LISTA DE CURSOS DEL ALUMNO 
$parametros=array("sIdentificador"=>$sIde, "sKey"=>$sKey, 
					"accion"=>"ListarInscripcionCursosAlumno", 
					"Identificacion"=>9453088, //Ci del alumno
					);
*/
/* LISTA DE MODULOS ASIGNADOS AL ALUMNO 
$parametros=array("sIdentificador"=>$sIde, "sKey"=>$sKey, 
					"accion"=>"ListarModulosAsignadosAlumno", 
					"Identificacion"=>9453088, //ci del alumno
					"IdCurso"=>534,
					);
*/
/* OBTENER MODULOS DESCUENTO 
$parametros=array("sIdentificador"=>$sIde, "sKey"=>$sKey, 
					"accion"=>"ObtenerModuloDescuento", 
					"Identificacion"=>1001871028, //ci del alumno
					"IdCurso"=>1775);
*/

// OBTENER MODULOS PAGADOS x CURSO
// $parametros=array("sIdentificador"=>$sIde, "sKey"=>$sKey, 
// 					"accion"=>"ObtenerModulosPagados", 
// 					"Identificacion"=>6089959, //7666922 ci del alumno
// 					"IdCurso"=>232); //1565 


/* OBTENER MODULOS  X PAGAR, PAGADO Y SALDO DE UN CURSO*/
/*$parametros=array("sIdentificador"=>$sIde, "sKey"=>$sKey, 
					"accion"=>"ObtenerModuloxPagarPagadoySaldo", 
					"Identificacion"=>6089959, //7666922 ci del alumno
					"IdCurso"=>2322); //1565*/


//s REGISTRAR CONTROL PAGOS 

/*$parametros=array("sIdentificador"=>$sIde, "sKey"=>$sKey, 
					"accion"=>"RegistrarControlPago", 
					"Identificacion"=>9044977, //ci del alumno
					"IdCurso"=>2536,
					"IdModulo"=>4836, 
					"MontoPago"=> -510, 
					"IdSolicitudFactura"=>255,
					"Plataforma"=>13 // 13=Sistema Financiero
					);*/
			



$carnet	[	28	]=	'E-0005852'	;$curso	[	28	]=	2811	;$modulo	[	28	]=	5206	;$solicitud	[	28	]=	2299	;$monto	[	28	]=	337.5;

for ($i=28; $i < 29; $i++) { 	
$ci=$carnet[$i];
$cursox=$curso[$i];
$modulox=$modulo[$i];
$montox=$monto[$i];
$solicitudx=$solicitud[$i];
$parametros=array("sIdentificador"=>$sIde, "sKey"=>$sKey, 
					"accion"=>"AnulacionDePago", 
					"Identificacion"=>$ci, //ci del alumno 
					"IdCurso"=>$cursox, 
					"IdModulo"=>$modulox, 
					"Monto"=> $montox, // valor positivo
					"IdSolicitudFactura"=>$solicitudx, //si se tiene el dato
					"Plataforma"=>13, // 13=Sistema Financiero
					"IdUsuario"=>222 // id del usuario del sistema
					);
	
		$parametros=json_encode($parametros);
		// abrimos la sesión cURL
		$ch = curl_init();
		// definimos la URL a la que hacemos la petición
		//curl_setopt($ch, CURLOPT_URL,"http://ibnored.ibnorca.org/wsibno/capacitacion/ws-inscribiralumno.php"); //OFICIAL
		curl_setopt($ch, CURLOPT_URL,"http://ibnored.ibnorca.org/wsibnob/capacitacion/ws-inscribiralumno.php"); //PRUEBA
		// indicamos el tipo de petición: POST
		curl_setopt($ch, CURLOPT_POST, TRUE);
		// definimos cada uno de los parámetros
		curl_setopt($ch, CURLOPT_POSTFIELDS, $parametros);
		// recibimos la respuesta y la guardamos en una variable
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$remote_server_output = curl_exec ($ch);
		// cerramos la sesión cURL
		curl_close ($ch);
}

		
		// imprimir en formato JSON
		header('Content-type: application/json'); 	
		print_r($remote_server_output); 

?>