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
 

$sIde = "facifin";
$sKey = "rrf656nb2396k6g6x44434h56jzx5g6";

$Objeto_detalle = new stdClass();
$Objeto_detalle->suscripcionId = 815;
$Objeto_detalle->pagoCursoId = 0;
$Objeto_detalle->detalle = 'NB.ISO 9001:2018';
$Objeto_detalle->precioUnitario = 1000;
$Objeto_detalle->cantidad = 2;

$Array= array($Objeto_detalle);

$parametros=array("sIdentificador"=>$sIde, "sKey"=>$sKey, 
              "accion"=>"GenerarFactura", //nombre de la accion
              "sucursalId"=>1, // ID Sucursal
              "pasarelaId"=>1, // pasalela
              "fechaFactura"=>'2020-06-09', // fecha a factura
              "nitciCliente"=>4868422016, //nit o ci de cliente
              "razonSocial"=>'Luna', //razon social
              "importeTotal"=>2000, //importe total
              "items"=>$Array // array con el detalle           
              );
    $parametros=json_encode($parametros);
    // abrimos la sesiรณn cURL
    $ch = curl_init();
    
    curl_setopt($ch, CURLOPT_URL,"http://200.105.199.164:8008/ifinanciero/wsifin/ws_generar_factura.php");

    curl_setopt($ch, CURLOPT_POST, TRUE);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $parametros);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $remote_server_output = curl_exec ($ch);
    curl_close ($ch);
    
    $respuesta=json_decode($remote_server_output);
    header('Content-type: application/json');   
    print_r($remote_server_output);   

?>
