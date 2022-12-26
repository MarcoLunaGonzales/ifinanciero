<?php 

// $direccion='http://ibnored.ibnorca.org/ifinanciero/wsifin/';

$direccion='http://localhost:8090/ifinanciero/wsifin/';
$sIde = "facifin";
$sKey = "rrf656nb2396k6g6x44434h56jzx5g6";
/*PARAMETROS PARA LA OBTENCION DE PDF FACTURA base 64*/	
	$parametros=array("sIdentificador"=>$sIde, "sKey"=>$sKey, "accion"=>"ObtenerFacturaPDF","idFactura"=>162); 
	//$parametros=array("sIdentificador"=>$sIde, "sKey"=>$sKey, "accion"=>"ObtenerFacturaArray","idFactura"=>197); 
	// $parametros=array("sIdentificador"=>$sIde, "sKey"=>$sKey, "accion"=>"ObtenerFacturaArray","idFactura"=>139); 

		$parametros=json_encode($parametros);
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL,$direccion."ws_obtener_factura.php"); 
		//curl_setopt($ch, CURLOPT_URL,$direccion."ws_obtener_datos_factura.php"); 
		curl_setopt($ch, CURLOPT_POST, TRUE);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $parametros);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$remote_server_output = curl_exec ($ch);
		//curl_close ($ch);
		
		header('Content-type: application/pdf'); 
		$array=json_decode($remote_server_output);
		$pdf=$array->factura64;
		//print_r($pdf);
		$pdfVer=base64_decode($pdf);
		echo $pdfVer; 			

?>