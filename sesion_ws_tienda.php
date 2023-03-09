<?php
  require_once 'conexion.php';

  $dbh = new Conexion();
  $direccion = 'https://prueba.ibnorca.org/ecommerce/backoffice/frontend/usuario/login.php';
  // $direccion=obtenerValorConfiguracion(42);//direccion des servicio web

  $user     = 'juan.quenallata@ibnorca.org';
  $password = md5('juanito2020');
  $parametros=array(
          "c"   => 'IBNTOK', 
          "md5" => 1, 
          "a"   => $user, 
          "b"   => $password);
  $parametros=json_encode($parametros);

  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL,$direccion);
  curl_setopt($ch, CURLOPT_POST, TRUE);
  curl_setopt($ch, CURLOPT_POSTFIELDS, $parametros);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  $remote_server_output = json_decode(curl_exec ($ch));
  curl_close ($ch); 
  header('Content-type: application/json');   
  print_r($remote_server_output->value->valor->token);   

?>
