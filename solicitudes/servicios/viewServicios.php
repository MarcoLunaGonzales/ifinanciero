<?php
set_time_limit(0);

require_once '../../conexion.php';
require_once '../../functions.php';

$dbh = new Conexion();

$sIde = "monitoreo"; 
$sKey = "837b8d9aa8bb73d773f5ef3d160c9b17";

$parametros=array("sIdentificador"=>$sIde, "sKey"=>$sKey, "lista"=>"Niveles", "padre"=>"80");
//SERVICIOS TLQ
$url="http://ibnored.ibnorca.org/wsibno/clasificador/ws-clasificador-post.php";

/*$parametros=array("sIdentificador"=>$sIde, "sKey"=>$sKey, "lista"=>"Clientes"); 
$url="http://ibnored.ibnorca.org/wsibno/cliente/ws-cliente-listas.php";
*/
$json=callService($parametros, $url);

// imprimir en formato JSON
header('Content-type: application/json'); 	
print_r($json);

?>