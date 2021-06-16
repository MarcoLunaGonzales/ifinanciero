<?php
require_once '../conexion.php';
require_once '../functions.php';

    $sIde = "ifinanciero";
    $sKey="ce94a8dabdf0b112eafa27a5aa475751";    

    $ci_estudiante="7919645";
    $IdCurso="3114";
   
     $parametros=array("sIdentificador"=>$sIde, "sKey"=>$sKey, 
            "accion"=>"ObtenerModuloxPagarPagadoySaldo", 
            "Identificacion"=>$ci_estudiante, //7666922 ci del alumno
            "IdCurso"=>$IdCurso); //1565
    $parametros=json_encode($parametros);
    $ch = curl_init();
    // curl_setopt($ch, CURLOPT_URL,"http://ibnored.ibnorca.org/wsibnob/capacitacion/ws-inscribiralumno.php"); //PRUEBA
    curl_setopt($ch, CURLOPT_URL,"http://ibnored.ibnorca.org/wsibno/capacitacion/ws-inscribiralumno.php");
    curl_setopt($ch, CURLOPT_POST, TRUE);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $parametros);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $remote_server_output = curl_exec ($ch);
    curl_close ($ch);
    $obj=json_decode($remote_server_output);

    header('Content-type: application/json');     
    print_r($remote_server_output); 


?>