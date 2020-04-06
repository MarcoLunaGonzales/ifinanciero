<?php
set_time_limit(0);

require_once 'call_services.php';
require_once '../../conexion.php';
require_once '../../functions.php';
//require_once '../../functions.php';

$dbh = new Conexion();
$direccion=obtenerValorConfiguracion(42);
$sIde = "monitoreo"; 
$sKey = "837b8d9aa8bb73d773f5ef3d160c9b17";

//SERVICIOS TLQ
$parametros=array("sIdentificador"=>$sIde, "sKey"=>$sKey, "lista"=>"Niveles", "padre"=>"80");
$url=$direccion."clasificador/ws-clasificador-post.php";

$json=callService($parametros, $url);

// imprimir en formato JSON
header('Content-type: application/json'); 	
print_r($json); 			

?>