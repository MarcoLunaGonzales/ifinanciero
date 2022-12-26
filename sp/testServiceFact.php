<?php
require_once '../conexion.php';
require_once '../functionsGeneral.php';
require_once '../functions.php';

$sIde = "facifin";
$sKey = "AX546321asbhy347bhas191001bn0rc4654";


//echo "ingressando.";

$Objeto_detalle4 = new stdClass();
$Objeto_detalle4->suscripcionId = 0;   /*SUSCRIPCION ID  (SE MANTIENE)*/
$Objeto_detalle4->pagoCursoId = 148;  /*PAGOCURSO ID (SE MANTIENE)*/
$Objeto_detalle4->moduloId = 6412;  /*HACE REFERENCIA AL MODULO ID*/
$Objeto_detalle4->codClaServicio = 707;   /*HACE REFERENCIA AL CODIGO DE CLASERVICIOS*/
$Objeto_detalle4->detalle = "Material";
$Objeto_detalle4->precioUnitario = "34.8";
$Objeto_detalle4->cantidad = 1;

$Array= array($Objeto_detalle4);

$parametros=array("sIdentificador"=>$sIde, "sKey"=>$sKey, 
              "accion"=>"NewGenerateInvoice", //nombre de la accion
              "sucursalId"=>1, // ID Sucursal
              "pasarelaId"=>1, // ID Pasarela 1 para la tienda
              "fechaFactura"=>'2022-12-26', // fecha de la factura
              "nitciCliente"=>'4868422016', //nit o ci de cliente
              "razonSocial"=>'Luna', //razon social
              "importeTotal"=>"34.8", //importe total
              "tipoPago"=>4, // codigo tipo de pago
              "codLibretaDetalle"=>'0', // codigo de libreta bancaria
              

              "usuario"=>"Tienda virtual",
              "idCliente"=>146,
              "idIdentificacion"=>5,
              "complementoCiCliente"=>"",
              "nroTarjeta"=>"",
              "CorreoCliente"=>"",


              "items"=>$Array // array con el detalle    
              );
/*$tipoPago=5 Y $tipoPago=6;//deposito en cuenta
$tipoPago=4;//tarjetas*/
//$direccion=obtenerValorConfiguracion(56);//direccion del servicio web ifinanciero
    

    // $direccion="http://lpsit.ibnorca.org:8008/ifinanciero/wsifin/";
$direccion="http://localhost:8090/ifinanciero/wsifin/";
    $parametros=json_encode($parametros);
    // abrimos la sesiรณn cURL
    $ch = curl_init();
    
    curl_setopt($ch, CURLOPT_URL,$direccion."ws_generate_invoice.php");
    // curl_setopt($ch, CURLOPT_URL,$direccion."ws_generar_factura.php");
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



