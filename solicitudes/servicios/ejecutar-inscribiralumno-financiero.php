<?php 
/*ACCESO A WEB SERVICE INSCRIPCION ALUMNO - FINANCIERO*/
//23/05/2020; 02/10/2020;
//LLAVES DE ACCESO AL WS
//$direccion=obtenerValorConfiguracion(42);//direccion des servicio web
    $sIde = "ifinanciero";
    $sKey = "ce94a8dabdf0b112eafa27a5aa475751";  
    // OBTENER MODULOS PAGADOS x CURSO
    // $parametros=array("sIdentificador"=>$sIde, "sKey"=>$sKey, 
    //         "accion"=>"ObtenerModulosPagados", 
    //         "Identificacion"=>$ci_estudiante, //7666922 ci del alumno
    //         "IdCurso"=>$IdCurso); //1565 

    /*$parametros=array("sIdentificador"=>$sIde, "sKey"=>$sKey, 
                    "accion"=>"RegistrarControlPago", 
                    "Identificacion"=>5935110, //ci del alumno
                    "IdCurso"=>2867,
                    "IdModulo"=>5275, 
                    "MontoPago"=> 250, 
                    "IdSolicitudFactura"=>3053,
                    "Plataforma"=>13 // 13=Sistema Financiero
                    );*/

/*$parametros=array("sIdentificador"=>$sIde, "sKey"=>$sKey, 
                    "accion"=>"AnulacionDePago", 
                    "Identificacion"=>3028430, //ci del alumno 
                    "IdCurso"=>2867, 
                    "IdModulo"=>5275, 
                    "Monto"=> 250, // valor positivo
                    "IdSolicitudFactura"=>3053, //si se tiene el dato
                    "Plataforma"=>13, // 13=Sistema Financiero
                    "IdUsuario"=>222 // id del usuario del sistema
                    );*/

    /*$parametros=array("sIdentificador"=>$sIde, "sKey"=>$sKey, 
            "accion"=>"ObtenerModuloxPagarPagadoySaldo", 
            "Identificacion"=>7191727, //7666922 ci del alumno
            "IdCurso"=>2833); //1565*/
    $parametros=json_encode($parametros);
    $ch = curl_init();
    // curl_setopt($ch, CURLOPT_URL,"http://ibnored.ibnorca.org/wsibnob/capacitacion/ws-inscribiralumno.php"); //PRUEBA
    //curl_setopt($ch, CURLOPT_URL,"http://ibnored.ibnorca.org/wsibno/capacitacion/ws-inscribiralumno.php");
    curl_setopt($ch, CURLOPT_POST, TRUE);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $parametros);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $remote_server_output = curl_exec ($ch);
    curl_close ($ch);
		
		// imprimir en formato JSON
		header('Content-type: application/json'); 	
		print_r($remote_server_output); 

?>