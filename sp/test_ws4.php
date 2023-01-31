<?php 
    $sIde = "ifinanciero";
    $sKey = "ce94a8dabdf0b112eafa27a5aa475751";
    $parametros=array("sIdentificador"=>$sIde, "sKey"=>$sKey, "accion"=>"ListarPersonaxAtributo", "IdAtributo"=>1621);
    $parametros=json_encode($parametros);
    $ch = curl_init();
      // definimos la URL a la que hacemos la petición
      curl_setopt($ch, CURLOPT_URL,"http://ibnored.ibnorca.org/wsibno/rrhh/ws-personal-listas.php"); 
      // indicamos el tipo de petición: POST
      curl_setopt($ch, CURLOPT_POST, TRUE);
      // definimos cada uno de los parámetros
      curl_setopt($ch, CURLOPT_POSTFIELDS, $parametros);
      // recibimos la respuesta y la guardamos en una variable
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      $remote_server_output = curl_exec ($ch);
      // cerramos la sesión cURL
      curl_close ($ch);  

  header('Content-type: application/json');   
  print_r($remote_server_output);       
  
?>
