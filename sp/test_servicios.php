<?php
  $sIde = "";
  $sKey = "";
  $parametros=array("sIdentificador"=>$sIde, "sKey"=>$sKey, "accion"=>"ListarComponentes","codigo_proyecto"=>1);
  //Lista todos los componentes
  $parametros=json_encode($parametros);
    $ch = curl_init();
    // definimos la URL a la que hacemos la petición    
    //curl_setopt($ch, CURLOPT_URL,"http://localhost:8099/simcibnorca/componentesSIS/compartir_servicio.php");//prueba
    //curl_setopt($ch, CURLOPT_URL,"http://ibnored.ibnorca.org/imonitoreo/componentesSIS/
    curl_setopt($ch, CURLOPT_URL,"http://ibnored.ibnorca.org/ifinanciero/wsifin/ws_actividadesproyectos.php");//prueba    
    // indicamos el tipo de petición: POST
    curl_setopt($ch, CURLOPT_POST, TRUE);
    // definimos cada uno de los parámetros
    curl_setopt($ch, CURLOPT_POSTFIELDS, $parametros);
    // recibimos la respuesta y la guardamos en una variable
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $remote_server_output = curl_exec ($ch);
    curl_close ($ch);

    $respuesta=json_decode($remote_server_output);
    header('Content-type: application/json');   
    print_r($remote_server_output); 

   /* $obj= json_decode($remote_server_output);
     echo $obj;
    $detalle=$obj->lstComponentes;
    foreach ($detalle as $objDet){
      echo $objDet->nombre."<br>";
    }*/


?>