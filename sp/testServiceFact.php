<?php
require_once '../conexion.php';
require_once '../functionsGeneral.php';
require_once '../functions.php';

$sIde = "facifin";
$sKey = "rrf656nb2396k6g6x44434h56jzx5g6";
$Objeto_detalle = new stdClass();
$Objeto_detalle->suscripcionId = 0;
$Objeto_detalle->pagoCursoId = 123;
$Objeto_detalle->detalle = "Alimentos en Grano";
$Objeto_detalle->precioUnitario = 162;
$Objeto_detalle->cantidad = 1;

$Objeto_detalle2 = new stdClass();
$Objeto_detalle2->suscripcionId = 815;
$Objeto_detalle2->pagoCursoId = 0;
$Objeto_detalle2->detalle = "NB/ISO 10075-3:2006";
$Objeto_detalle2->precioUnitario = 101.00;
$Objeto_detalle2->cantidad = 1;

$Objeto_detalle3 = new stdClass();
$Objeto_detalle3->suscripcionId = 815;
$Objeto_detalle3->pagoCursoId = 0;
$Objeto_detalle3->detalle = "NB/ISO 22000:2018";
$Objeto_detalle3->precioUnitario = 214.00;
$Objeto_detalle3->cantidad = 1;

$Objeto_detalle4 = new stdClass();
$Objeto_detalle4->suscripcionId = 0;
$Objeto_detalle4->pagoCursoId = 70;
$Objeto_detalle4->detalle = "Curso OV-RLEC-CC-G1-2020 , Módulo 1 requisitos para laboratorio de ensayo y calibracion nb/iso 17025";
$Objeto_detalle4->precioUnitario = 510;
$Objeto_detalle4->cantidad = 1;

$Array= array($Objeto_detalle,$Objeto_detalle2,$Objeto_detalle3,$Objeto_detalle4);

$parametros=array("sIdentificador"=>$sIde, "sKey"=>$sKey, 
              "accion"=>"GenerarFactura", //nombre de la accion
              "sucursalId"=>1, // ID Sucursal
              "pasarelaId"=>1, // ID Pasarela 1 para la tienda
              "fechaFactura"=>'2020-07-11', // fecha de la factura
              "nitciCliente"=>'B00596460', //nit o ci de cliente
              "razonSocial"=>'Sullcamani', //razon social
              "importeTotal"=>70, //importe total
              "tipoPago"=>5, // array con el detalle    
              "codLibretaDetalle"=>5338, // array con el detalle
              "items"=>$Array // array con el detalle    
              );
$direccion=obtenerValorConfiguracion(56);//direccion del servicio web ifinanciero
    $parametros=json_encode($parametros);
    // abrimos la sesiรณn cURL
    $ch = curl_init();
    
    curl_setopt($ch, CURLOPT_URL,$direccion."ws_generar_factura.php");
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