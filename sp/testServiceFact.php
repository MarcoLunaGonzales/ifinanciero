<?php
require_once '../conexion.php';
require_once '../functionsGeneral.php';
require_once '../functions.php';

$sIde = "facifin";
$sKey = "rrf656nb2396k6g6x44434h56jzx5g6";


$Objeto_detalle4 = new stdClass();
<<<<<<< HEAD
<<<<<<< HEAD
$Objeto_detalle4->suscripcionId = 7825;
$Objeto_detalle4->pagoCursoId = "0";
$Objeto_detalle4->detalle = "NB 680:2016";
$Objeto_detalle4->precioUnitario = "70";
=======
$Objeto_detalle4->suscripcionId = 7981;
=======
$Objeto_detalle4->suscripcionId = 8063;
>>>>>>> 27024c608f4bbe634f525bd07f6f6300687c2557
$Objeto_detalle4->pagoCursoId = "0";
$Objeto_detalle4->detalle = "NB 338003:2009";
$Objeto_detalle4->precioUnitario = "70.00";
>>>>>>> 476a7acfa7822d3fb4d15970a0104ce4f74d47a9
$Objeto_detalle4->cantidad = 1;

$Array= array($Objeto_detalle4);

$parametros=array("sIdentificador"=>$sIde, "sKey"=>$sKey, 
              "accion"=>"GenerarFactura", //nombre de la accion
              "sucursalId"=>1, // ID Sucursal
              "pasarelaId"=>1, // ID Pasarela 1 para la tienda
<<<<<<< HEAD
<<<<<<< HEAD
              "fechaFactura"=>'2020-07-29', // fecha de la factura
              "nitciCliente"=>'1020149020', //nit o ci de cliente
              "razonSocial"=>'Rosaicela Marzana Tapeosi', //razon social
              "importeTotal"=>"400", //importe total
              "tipoPago"=>5, // array con el detalle    
              "codLibretaDetalle"=>'3855', // array con el detalle
=======
              "fechaFactura"=>'2020-07-28', // fecha de la factura
              "nitciCliente"=>'1020149020', //nit o ci de cliente
              "razonSocial"=>'Rosaicela Marzana Tapeosi', //razon social
              "importeTotal"=>"70", //importe total
              "tipoPago"=>4, // array con el detalle    
              "codLibretaDetalle"=>'0', // array con el detalle
>>>>>>> 476a7acfa7822d3fb4d15970a0104ce4f74d47a9
=======
              "fechaFactura"=>'2020-07-29', // fecha de la factura
              "nitciCliente"=>'8860067', //nit o ci de cliente
              "razonSocial"=>'orge Gutiu00e9rrez 26', //razon social
              "importeTotal"=>"70", //importe total
              "tipoPago"=>6, // array con el detalle    
              "codLibretaDetalle"=>'4239', // array con el detalle
>>>>>>> 27024c608f4bbe634f525bd07f6f6300687c2557

              "items"=>$Array // array con el detalle    
              );
$direccion=obtenerValorConfiguracion(56);//direccion del servicio web ifinanciero
// $direccion="200.105.199.164:8008/ifinanciero/wsifin/";
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