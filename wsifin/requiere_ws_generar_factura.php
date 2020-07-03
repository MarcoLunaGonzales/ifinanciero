<?php
session_start();
require_once '../conexion.php';
require_once '../functionsGeneral.php';
require_once '../functions.php';
require_once '../styles.php';

$dbh = new Conexion();
// $direccion=obtenerValorConfiguracion(42);//direccion des servicio web
$codigo=0;
  //$direccion='http://127.0.0.1/ifinanciero/wsifin/';
  // $direccion='http://200.105.199.164:8008/ifinanciero/wsifin/';
  $direccion=obtenerValorConfiguracion(42);//direccion des servicio web
  $sIde = "ifinanciero";
  $sKey = "ce94a8dabdf0b112eafa27a5aa475751";  
  // OBTENER MODULOS PAGADOS x CURSO
  // $parametros=array("sIdentificador"=>$sIde, "sKey"=>$sKey, 
  //         "accion"=>"ObtenerModulosPagados", 
  //         "Identificacion"=>$ci_estudiante, //7666922 ci del alumno
  //         "IdCurso"=>$IdCurso); //1565 

  $parametros=array("sIdentificador"=>$sIde, "sKey"=>$sKey, 
          "accion"=>"ObtenerModuloxPagarPagadoySaldo", 
          "Identificacion"=>4096300, //7666922 ci del alumno
          "IdCurso"=>2601); //1565
  $parametros=json_encode($parametros);
  $ch = curl_init();
  // curl_setopt($ch, CURLOPT_URL,"http://ibnored.ibnorca.org/wsibnob/capacitacion/ws-inscribiralumno.php"); //PRUEBA
  curl_setopt($ch, CURLOPT_URL,$direccion."capacitacion/ws-inscribiralumno.php");
  curl_setopt($ch, CURLOPT_POST, TRUE);
  curl_setopt($ch, CURLOPT_POSTFIELDS, $parametros);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  $remote_server_output = curl_exec ($ch);
  curl_close ($ch);
  // return json_decode($remote_server_output);
  // imprimir en formato JSON
  // header('Content-type: application/json');   
  // print_r($remote_server_output);
    header('Content-type: application/json');   
    print_r($remote_server_output);   

?>
