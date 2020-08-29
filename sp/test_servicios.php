<?php
require_once '../conexion.php';
require_once '../functions.php';

  // $sKey = "c066ffc2a049cf11f9ee159496089a15";

$direccion=obtenerValorConfiguracion(42);//direccion des servicio web
    $sIde = "ifinanciero";
    $sKey = "ce94a8dabdf0b112eafa27a5aa475751";
    $parametros=array("sIdentificador"=>$sIde, "sKey"=>$sKey, "TipoLista"=>"estados", "IdPais"=>26);
    $parametros=json_encode($parametros);
      $ch = curl_init();
      //curl_setopt($ch, CURLOPT_URL,$direccion."clasificador/ws-paises.php"); // OFICIAL
      curl_setopt($ch, CURLOPT_URL,$direccion."clasificador/ws-paises.php"); // PRUEBA
      curl_setopt($ch, CURLOPT_POST, TRUE);
      curl_setopt($ch, CURLOPT_POSTFIELDS, $parametros);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      $remote_server_output = curl_exec ($ch);
      curl_close ($ch);
    header('Content-type: application/json');   
    print_r($remote_server_output); 

?>