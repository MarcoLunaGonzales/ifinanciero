<?php 
require_once '../functions.php';
$direccion=obtenerValorConfiguracion(45);//direccion del Server del Servicio

	$sIde = "monitoreo"; 
	$sKey="101010"; 

/*PARAMETROS PARA LA OBTENCION DE LISTAS DE PERSONAL*/
	$oficina="0";
	$area="13";
	$anio="2020";
	$mes="12";
	$cuenta="0";

	$parametros=array("sIdentificador"=>$sIde, "sKey"=>$sKey, "oficina"=>$oficina, "area"=>$area, "anio"=>$anio, "mes"=>$mes, "accion"=>"listar"); //

	$parametros=json_encode($parametros);
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL,$direccion."ws/wsPresupuestoIngresosTotal.php");
	curl_setopt($ch, CURLOPT_POST, TRUE);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $parametros);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$remote_server_output = curl_exec ($ch);
	curl_close ($ch);
	
	header('Content-type: application/json'); 	
	print_r($remote_server_output); 			

?>