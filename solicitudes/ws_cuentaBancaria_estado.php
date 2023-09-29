<?php
session_start();
require_once '../conexion.php';
require_once '../functions.php';

$ws_peticion      = "registro/ws-cuentabanco.php";
$globalUser       = $_SESSION["globalUser"];

$id_cuenta_banco  = $_POST['id_cuenta_banco'];
$cod_cliente_prov = $_POST['cod_proveedor'];
$vigencia         = ($_POST['vigencia'] == 1 ? 0 : 1);


$parametros=array(
    "accion"        => "EditarVigenciaCuentaBanco", 
    "IdCuentaBanco" => $id_cuenta_banco, //Id del registro de cuenta bancaria
    "IdCliente"     => $cod_cliente_prov, //Id del Proveedor o Cliente 
    "Vigencia"      => $vigencia, //0=No Vigente; 1=Vigente 
    "IdUsuarioReg"  => $globalUser // valor del id del usuario retornado en el login						  
    );

$response = servicioCuentaBancaria($ws_peticion, $parametros);

header('Content-Type: application/json');
echo json_encode($response);