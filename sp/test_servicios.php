<?php
  $sIde = "";
  $sKey = "";
  $parametros=array("sIdentificador"=>$sIde, "sKey"=>$sKey, "accion"=>"ListarComponentes","codigo_proyecto"=>0);
  //Lista todos los componentes
  $parametros=json_encode($parametros);
    $ch = curl_init();
    // definimos la URL a la que hacemos la petición    
    //curl_setopt($ch, CURLOPT_URL,"http://localhost/imonitoreo/componentesSIS/compartir_servicio.php");//prueba
    curl_setopt($ch, CURLOPT_URL,"http://ibnored.ibnorca.org/imonitoreo/componentesSIS/compartir_servicio.php");//prueba    
    // indicamos el tipo de petición: POST
    curl_setopt($ch, CURLOPT_POST, TRUE);
    // definimos cada uno de los parámetros
    curl_setopt($ch, CURLOPT_POSTFIELDS, $parametros);
    // recibimos la respuesta y la guardamos en una variable
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $remote_server_output = curl_exec ($ch);
    curl_close ($ch);
    
    // imprimir en formato JSON  
    //print_r($remote_server_output);
	header('Content-type: application/json'); 	
		print_r($remote_server_output); 			

    // $obj= json_decode($remote_server_output);
    // $detalle=$obj->lstComponentes;
    // return $detalle;
    // foreach ($detalle as $objDet){
    //   echo $objDet->codigo."<br>";
    // }


?>