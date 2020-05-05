<?php 
require_once '../functions.php';
$direccion=obtenerValorConfiguracion(45);//direccion del Server del Servicio

	$sIde = "monitoreo"; 
	$sKey="101010"; 

/*PARAMETROS PARA LA OBTENCION DE LISTAS DE PERSONAL*/
	$oficina="5";
	$area="38";
	$anio="2020";
	$mes="4";
	$cuenta="5020101001";

	$parametros=array("sIdentificador"=>$sIde, "sKey"=>$sKey, "oficina"=>$oficina, "area"=>$area, "anio"=>$anio, "mes"=>$mes, "cuenta"=>$cuenta, "accion"=>"listar"); //

	$parametros=json_encode($parametros);
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL,$direccion."ws/wsPresupuestoEjecucionCuenta.php");
	curl_setopt($ch, CURLOPT_POST, TRUE);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $parametros);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$remote_server_output = curl_exec ($ch);
	curl_close ($ch);
	
	header('Content-type: application/json'); 	
	print_r($remote_server_output); 			

?>