<?php
session_start();
require_once '../conexion.php';
require_once '../functionsGeneral.php';
require_once '../functions.php';
require_once '../styles.php';

$dbh = new Conexion();
$direccion=obtenerValorConfiguracion(42);//direccion des servicio web
$sqlX="SET NAMES 'utf8'";
$stmtX = $dbh->prepare($sqlX);
$stmtX->execute();
 

$sIde = "ifinanciero";
$sKey = "ce94a8dabdf0b112eafa27a5aa475751";

$Objeto_detalle = new stdClass();
$Objeto_detalle->suscripcionId = 1;
$Objeto_detalle->pagoCursoId = 1;
$Objeto_detalle->detalle = "detalle del item";
$Objeto_detalle->precioUnitario = 100;
$Objeto_detalle->cantidad = 1;
$Objeto_detalle2 = new stdClass();
$Objeto_detalle2->suscripcionId = 2;
$Objeto_detalle2->pagoCursoId = 2;
$Objeto_detalle2->detalle = "detalle del item2";
$Objeto_detalle2->precioUnitario = 100;
$Objeto_detalle2->cantidad = 1;
$Detalle= array($Objeto_detalle,$Objeto_detalle2);

$parametros=array("sIdentificador"=>$sIde, "sKey"=>$sKey, 
              "accion"=>"GenerarFactura", //nombre de la accion
              "IdSucursal"=>829, // ID Sucursal
              "FechaFactura"=>'2020-05-08', // fecha a factura
              "Identificacion"=>10101010, //nit o ci de cliente
              "RazonSocial"=>'Esta es la razón social', //razon social
              "ImporteTotal"=>260.5, //importe total
              "Detalle"=>$Detalle // array con el detalle           
              );
    $parametros=json_encode($parametros);
    // abrimos la sesiรณn cURL
    $ch = curl_init();
    
    curl_setopt($ch, CURLOPT_URL,"http://localhost/ifinanciero/tienda_virtual_ws/ws_generar_factura.php");//prueba
    //curl_setopt($ch, CURLOPT_URL,"http://ibnored.ibnorca.org/imonitoreo/componentesSIS/compartir_servicio.php");//oficial    

    // indicamos el tipo de peticiรณn: POST
    curl_setopt($ch, CURLOPT_POST, TRUE);
    // definimos cada uno de los parรกmetros
    curl_setopt($ch, CURLOPT_POSTFIELDS, $parametros);
    // recibimos la respuesta y la guardamos en una variable
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $remote_server_output = curl_exec ($ch);
    curl_close ($ch);
    
    $respuesta=json_decode($remote_server_output);
    // imprimir en formato JSON
    header('Content-type: application/json');   
    print_r($remote_server_output);   
?>
