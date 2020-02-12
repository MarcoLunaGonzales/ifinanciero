<?php 

function callService($parametros, $url){
	$parametros=json_encode($parametros);
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL,$url);
	curl_setopt($ch, CURLOPT_POST, TRUE);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $parametros);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$remote_server_output = curl_exec ($ch);
	curl_close ($ch);
	
	return $remote_server_output; 	
}
?>