<?php
require_once '../conexion.php';
require_once '../functions.php';

  // $sKey = "c066ffc2a049cf11f9ee159496089a15";
$direccion=obtenerValorConfiguracion(42);//direccion des servicio web
  $sIde = "ifinanciero";
  $sKey = "ce94a8dabdf0b112eafa27a5aa475751";
  // $sIde = "monitoreo"; 
  // $sKey = "837b8d9aa8bb73d773f5ef3d160c9b17";

  /*Lista de Clientes Empresa*/
  $parametros=array("sIdentificador"=>$sIde, "sKey"=>$sKey, "lista"=>"Clientes");
    $parametros=json_encode($parametros);
    // abrimos la sesión cURL
    $ch = curl_init();
    // definimos la URL a la que hacemos la petición
    curl_setopt($ch, CURLOPT_URL,$direccion."cliente/ws-cliente-listas.php");     
    // indicamos el tipo de petición: POST
    curl_setopt($ch, CURLOPT_POST, TRUE);
    // definimos cada uno de los parámetros
    curl_setopt($ch, CURLOPT_POSTFIELDS, $parametros);
    // recibimos la respuesta y la guardamos en una variable
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $remote_server_output = curl_exec ($ch);
    // cerramos la sesión cURL
    curl_close ($ch);  
    // return json_decode($remote_server_output);   

    // imprimir en formato JSON  
    
    header('Content-type: application/json');   
    print_r($remote_server_output); 

?>