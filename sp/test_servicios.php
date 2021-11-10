<?php 
/*REGISTRO DE CONTACTOS XLS FINANCIERO
WS: ws-fin-cliente-contacto.php 
*/
//LLAVES DE ACCESO AL WS
$sIde = "ifinanciero";
$sKey = "ce94a8dabdf0b112eafa27a5aa475751";

    $sIde = "ifinanciero";
    $sKey = "ce94a8dabdf0b112eafa27a5aa475751";  
    // OBTENER MODULOS PAGADOS x CURSO
    // $parametros=array("sIdentificador"=>$sIde, "sKey"=>$sKey, 
    //         "accion"=>"ObtenerModulosPagados", 
    //         "Identificacion"=>$ci_estudiante, //7666922 ci del alumno
    //         "IdCurso"=>$IdCurso); //1565 

    $parametros=array("sIdentificador"=>$sIde, "sKey"=>$sKey, 
            "accion"=>"ObtenerModuloxPagarPagadoySaldo", 
            "Identificacion"=>5396568, //7666922 ci del alumno
            "IdCurso"=>3319); //1565
    $parametros=json_encode($parametros);
    $ch = curl_init();
    // curl_setopt($ch, CURLOPT_URL,"http://ibnored.ibnorca.org/wsibnob/capacitacion/ws-inscribiralumno.php"); //PRUEBA
    curl_setopt($ch, CURLOPT_URL,"http://ibnored.ibnorca.org/wsibno/capacitacion/ws-inscribiralumno.php");
    curl_setopt($ch, CURLOPT_POST, TRUE);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $parametros);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $remote_server_output = curl_exec ($ch);
    curl_close ($ch);


  header('Content-type: application/json');   
  print_r($remote_server_output);       
  
?>
