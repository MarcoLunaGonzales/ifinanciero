<?php
session_start();
require_once '../conexion.php';
require_once '../functions.php';

// Tipo de servicio
$cod_cliente_prov = $_POST['cod_proveedor'];
$ws_peticion = "lista/ws-lst-cuentabanco.php";
$parametros  = array(
                "accion"    =>  "ListaCuentaBancoxCliente",
                "IdCliente" =>  $cod_cliente_prov
            ); 
$response = servicioCuentaBancaria($ws_peticion, $parametros);

header('Content-Type: application/json');
echo json_encode($response);