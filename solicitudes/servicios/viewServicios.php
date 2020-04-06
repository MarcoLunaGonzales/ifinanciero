<?php
set_time_limit(0);

require_once '../../conexion.php';
require_once '../../functions.php';
$direccion=obtenerValorConfiguracion(42);//direccion des servicio web

$dbh = new Conexion();

$sIde = "monitoreo"; 
$sKey = "837b8d9aa8bb73d773f5ef3d160c9b17";

$parametros=array("sIdentificador"=>$sIde, "sKey"=>$sKey, "lista"=>"Niveles", "padre"=>"80");
//SERVICIOS TLQ
$url=$direccion."clasificador/ws-clasificador-post.php";

/*$parametros=array("sIdentificador"=>$sIde, "sKey"=>$sKey, "lista"=>"Clientes"); 
$url=$direccion."cliente/ws-cliente-listas.php";
*/
$json=callService($parametros, $url);

// imprimir en formato JSON
header('Content-type: application/json'); 	
print_r($json);

?>