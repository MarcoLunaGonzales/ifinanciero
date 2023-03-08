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

//OBTENER MODULOS PAGADOS x CURSO
$parametros=array("sIdentificador"=>$sIde, "sKey"=>$sKey, 
					"accion"=>"ObtenerModulosPagados", 
					"Identificacion"=>1447493, //7666922 ci del alumno
					"IdCurso"=>7048); //1565 


/* OBTENER MODULOS  X PAGAR, PAGADO Y SALDO DE UN CURSO*/
// $parametros=array("sIdentificador"=>$sIde, "sKey"=>$sKey, 
// 					"accion"=>"ObtenerModuloxPagarPagadoySaldo", 
// 					"Identificacion"=>1447493, //7666922 ci del alumno
// 					"IdCurso"=>3952); //1565


 // $parametros=array("sIdentificador"=>$sIde, "sKey"=>$sKey, 
// 					"accion"=>"RegistrarControlPago", 
// 					"Identificacion"=>1447493, //ci del alumno
// 					"IdCurso"=>3952,
// 					"IdModulo"=>7048, 
// 					"MontoPago"=> 525, 
// 					"IdSolicitudFactura"=>24783,
// 					"Plataforma"=>13 // 13=Sistema Financiero
// 					);
			

		$parametros=json_encode($parametros);
		// abrimos la sesión cURL
		$ch = curl_init();
		// definimos la URL a la que hacemos la petición
		curl_setopt($ch, CURLOPT_URL,"http://ibnored.ibnorca.org/wsibno/capacitacion/ws-inscribiralumno.php"); //OFICIAL
		//curl_setopt($ch, CURLOPT_URL,"http://ibnored.ibnorca.org/wsibnob/capacitacion/ws-inscribiralumno.php"); //PRUEBA
		// indicamos el tipo de petición: POST
		curl_setopt($ch, CURLOPT_POST, TRUE);
		// definimos cada uno de los parámetros
		curl_setopt($ch, CURLOPT_POSTFIELDS, $parametros);
		// recibimos la respuesta y la guardamos en una variable
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$remote_server_output = curl_exec ($ch);
		// cerramos la sesión cURL
		curl_close ($ch);
		
		header('Content-type: application/json'); 	
		print_r($remote_server_output);


?>