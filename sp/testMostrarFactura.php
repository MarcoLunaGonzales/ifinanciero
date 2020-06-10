<?php 

$direccion='http://localhost/ifinanciero/wsifin/';
$sIde = "facifin";
$sKey = "rrf656nb2396k6g6x44434h56jzx5g6";
/*PARAMETROS PARA LA OBTENCION DE PDF FACTURA base 64*/
	//$parametros=array("sIdentificador"=>$sIde, "sKey"=>$sKey, "accion"=>"ObtenerFacturaPDF","idFactura"=>36); 
	$parametros=array("sIdentificador"=>$sIde, "sKey"=>$sKey, "accion"=>"ObtenerFacturaPDF","idFactura"=>38); 

		$parametros=json_encode($parametros);
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL,$direccion."ws_obtener_factura.php"); 
		//curl_setopt($ch, CURLOPT_URL,$direccion."ws_obtener_datos_factura.php"); 
		curl_setopt($ch, CURLOPT_POST, TRUE);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $parametros);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$remote_server_output = curl_exec ($ch);
		curl_close ($ch);
		
		header('Content-type: application/json'); 	
		print_r($remote_server_output); 			

?>