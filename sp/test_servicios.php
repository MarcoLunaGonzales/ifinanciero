<?php
require_once '../conexion.php';
require_once '../functions.php';

  // $sKey = "c066ffc2a049cf11f9ee159496089a15";


// $direccion=obtenerValorConfiguracion(42);//direccion des servicio web
//     $sIde = "ifinanciero";
//     $sKey = "ce94a8dabdf0b112eafa27a5aa475751";
//     // $sIde = "monitoreo"; 
    // $sKey = "837b8d9aa8bb73d773f5ef3d160c9b17";
$sIde = "monitoreo"; 
$sKey = "837b8d9aa8bb73d773f5ef3d160c9b17";

//NORMAS
$parametros=array("sIdentificador"=>$sIde, "sKey"=>$sKey, "TipoLista"=>"Todos");
$url="http://ibnored.ibnorca.org/wsibno/catalogo/ws-catalogo-nal.php";
$tableInsert="normas";
$json=callService($parametros, $url);
$obj=json_decode($json);
// header('Content-type: application/json');   
// print_r($json); 
    header('Content-type: application/json');   
    print_r($json); 

?>