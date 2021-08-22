<?php
require_once '../conexion.php';
require_once '../functions.php';
//require_once '../personal/cargarDatosWS.php';//tipos identificacion personal
//require_once '../personal/cargarGeneroWS.php';
//require_once '../personal/cargarEstadoCivilWS.php';

    $sIde = "monitoreo";
    $sKey="837b8d9aa8bb73d773f5ef3d160c9b17";    

//SERVICIOS TLQ
    /*$parametros=array("sIdentificador"=>$sIde, "sKey"=>$sKey, "accion"=>"ListarPersonal");
    $url=$direccion."rrhh/ws-personal-listas.php"; */  

    $parametros=array("sIdentificador"=>$sIde, "sKey"=>$sKey, "accion"=>"ListarPersonal");
    $url="http://ibnored.ibnorca.org/wsibno/rrhh/ws-personal-listas.php";
    
    $json=callService($parametros, $url);

    $obj=json_decode($json);//decodificando json

    $parametros=json_encode($parametros);
    $ch = curl_init();
    // curl_setopt($ch, CURLOPT_URL,"http://ibnored.ibnorca.org/wsibnob/capacitacion/ws-inscribiralumno.php"); //PRUEBA
    curl_setopt($ch, CURLOPT_URL,"http://ibnored.ibnorca.org/wsibno/rrhh/ws-personal-listas.php");
    curl_setopt($ch, CURLOPT_POST, TRUE);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $parametros);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $remote_server_output = curl_exec ($ch);
    curl_close ($ch);
    $obj=json_decode($remote_server_output);

    header('Content-type: application/json');     
    print_r($remote_server_output); 


?>