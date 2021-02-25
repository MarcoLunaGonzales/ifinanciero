<?php
require_once '../conexion.php';
require_once '../functions.php';

    $sIdentificador = "ifinanciero";
    $sKey="ce94a8dabdf0b112eafa27a5aa475751";
    $nombreuser="consultafinanzas@ibnorca.org";
    $password="consulta2021";
    $claveuser=$password;
    $claveuser=md5($password);
    $datos=array("sIdentificador"=>$sIdentificador, "sKey"=>$sKey, 
                 "operacion"=>"Login", "nombreUser"=>$nombreuser, "claveUser"=>$claveuser);
    $datos=json_encode($datos);
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL,"http://ibnored.ibnorca.org/wsibno/verifica/ws-user-personal.php");
    curl_setopt($ch, CURLOPT_POST, TRUE);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $datos);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $remote_server_output = curl_exec ($ch);
    curl_close ($ch);
    $obj=json_decode($remote_server_output);

    header('Content-type: application/json');     
    print_r($remote_server_output); 


?>