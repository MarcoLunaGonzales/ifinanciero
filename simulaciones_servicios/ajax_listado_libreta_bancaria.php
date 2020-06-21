<?php
session_start();
require_once '../conexion.php';
require_once '../functionsGeneral.php';
require_once '../functions.php';
require_once '../styles.php';
// require_once '../layouts/bodylogin2.php';
$dbh = new Conexion();

$direccion=obtenerValorConfiguracion(56);//direccion del servicio web ifinanciero
  $sIde = "libBan";
  $sKey = "89i6u32v7xda12jf96jgi30lh";
  //PARAMETROS PARA LA OBTENCION DE ARRAY LIBRETA
  $parametros=array("sIdentificador"=>$sIde, "sKey"=>$sKey, "accion"=>"ObtenerLibretaBancaria","idLibreta"=>0); 
  $parametros=json_encode($parametros);
  // abrimos la sesión cURL
  $ch = curl_init();
  // definimos la URL a la que hacemos la petición
  curl_setopt($ch, CURLOPT_URL,$direccion."ws_obtener_libreta_bancaria.php"); 
  // indicamos el tipo de petición: POST
  curl_setopt($ch, CURLOPT_POST, TRUE);
  // definimos cada uno de los parámetros
  curl_setopt($ch, CURLOPT_POSTFIELDS, $parametros);
  // recibimos la respuesta y la guardamos en una variable
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  $remote_server_output = curl_exec ($ch);
  // cerramos la sesión cURL
  curl_close ($ch);
  // return json_decode($remote_server_output);
  // imprimir en formato JSON
  header('Content-type: application/json');   
  print_r($remote_server_output);