<?php
require_once '../conexion.php';
require_once '../functionsGeneral.php';
require_once '../functions.php';

$sIde = "facifin";
$sKey = "rrf656nb2396k6g6x44434h56jzx5g6";


$Objeto_detalle4 = new stdClass();
$Objeto_detalle4->suscripcionId = 7825;
$Objeto_detalle4->pagoCursoId = "0";
$Objeto_detalle4->detalle = "NB 680:2016";
$Objeto_detalle4->precioUnitario = "70";
$Objeto_detalle4->cantidad = 1;

$Array= array($Objeto_detalle4);

$parametros=array("sIdentificador"=>$sIde, "sKey"=>$sKey, 
              "accion"=>"GenerarFactura", //nombre de la accion
              "sucursalId"=>1, // ID Sucursal
              "pasarelaId"=>1, // ID Pasarela 1 para la tienda
              "fechaFactura"=>'2020-07-29', // fecha de la factura
              "nitciCliente"=>'1020149020', //nit o ci de cliente
              "razonSocial"=>'Rosaicela Marzana Tapeosi', //razon social
              "importeTotal"=>"400", //importe total
              "tipoPago"=>5, // array con el detalle    
              "codLibretaDetalle"=>'3855', // array con el detalle

              "items"=>$Array // array con el detalle    
              );
$direccion=obtenerValorConfiguracion(56);//direccion del servicio web ifinanciero
    $parametros=json_encode($parametros);
    // abrimos la sesiรณn cURL
    $ch = curl_init();
    
    curl_setopt($ch, CURLOPT_URL,$direccion."ws_generar_factura_test.php");
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