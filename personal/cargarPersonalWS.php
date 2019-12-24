<?php
set_time_limit(0);


require_once '../conexion.php';
require_once '../functions.php';

$dbh = new Conexion();

$sIde = "monitoreo"; 
$sKey = "837b8d9aa8bb73d773f5ef3d160c9b17";



//SERVICIOS TLQ
$parametros=array("sIdentificador"=>$sIde, "sKey"=>$sKey, "accion"=>"ListarPersonal");
$url="http://ibnored.ibnorca.org/wsibno/rrhh/ws-personal-listas.php";


$json=callService($parametros, $url);
$obj=json_decode($json);//decodificando json

$detalle=$obj->lstPersonal;
foreach ($detalle as $objDet){
	$codigoX=$objDet->IdCliente;//idcliente

	echo "TEST ID PERSONA ".$codigoX."<br>";
	
}

<<<<<<< HEAD
//volvemos al listado de personal
showAlertSuccessError3($flagSuccess,$urlListPersonal);
=======
echo "FIN WEB SERVICE <br>";
>>>>>>> 9665608161fbd74baa97b51d1230f7cda83c0916


?>