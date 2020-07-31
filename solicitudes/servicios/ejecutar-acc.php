<?php
  $sIde = "";
  $sKey = "";
  $codigo_proyecto=1;

  $parametros=array("sIdentificador"=>$sIde, "sKey"=>$sKey, "accion"=>"ListarAccNum","codigo_proyecto"=>$codigo_proyecto);
  //Lista todos los componentes
  $parametros=json_encode($parametros);
    $ch = curl_init();
	curl_setopt($ch, CURLOPT_URL,"http://ibnored.ibnorca.org/ifinanciero/wsifin/ws_accnum_proyectos.php"); 
	curl_setopt($ch, CURLOPT_POST, TRUE);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $parametros);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$remote_server_output = curl_exec ($ch);
	curl_close ($ch);	
	// imprimir en formato JSON
	header('Content-type: application/json'); 	
	print_r($remote_server_output); 	

?>