<?php
session_start();
require_once '../conexion.php';
require_once '../functions.php';

$ws_peticion      = "registro/ws-cuentabanco.php";
$globalUser       = $_SESSION["globalUser"];

$id_cuenta_banco    = $_POST['id_cuenta_banco'];
$cod_cliente_prov = $_POST['cod_proveedor'];
$banco            = $_POST['banco'];
$cuenta           = $_POST['cuenta'];
$nombre           = $_POST['nombre'];
$apellido         = "‎‏‏‎";//$_GET['apellido']

if(empty($id_cuenta_banco)){
    // INSERTA en el servicio web CUENTAS BANCARIAS
    $parametros=array(
        "accion"                => "RegistrarCuentaBanco",
        "IdCliente"             => $cod_cliente_prov, //Id del Proveedor o Cliente
        "IdBanco"               => $banco, //valor numerico determinado por id del clasificador perteneciente a Entidades Bancarias (idpadre=319), poner 0 en caso de que sea otra entidad financiera
        "OtroBanco"             => NULL, // valor textual empleado en caso de no encontrar el Banco en el clasificador Entidades Bancarias. Caso contrario enviar NULL
        "IdTipoCuenta"          => 2842, // valor numerico determinado por Id del clasificador de Tipo Cuenta Bancaria (idPadre=2841), poner o en caso de no encontrar el tipo requerido                       "OtroTipoCuenta"=>NULL, 
        "OtroTipoCuenta"        => NULL, // valor textual empleado en caso de requerir otro tipo de cuenta que no este en el clasificador Tipo Cuenta. Caso contrario enviar NULL
        "IdTipoMoneda"          => 322, // valor numerico determinado por el Id del clasificador Monedas (idPadre=320)
        "NroCuenta"             => $cuenta, //valor textual para el envio del numero de cuenta
        "BeneficiarioNombre"    => $nombre, 
        "BeneficiarioApellido"  => $apellido, 
        "BeneficiarioIdentificacion" => NULL, // valor textual en el caso de requerir el registro de la identificacion. Caso contrario enviar NULL
        "BancoIntermediario"         => NULL, // valor textual en caso de hacer uso del campo. Caso contrario NULL
        "IdUsuarioReg"               => $globalUser // valor numerico obtenido del id del usuario autenticado. Usar 0 en caso de no tener el id
        );
}else{
    $parametros=array(
            "accion"            =>"EditarCuentaBanco",
            "IdCuentaBanco"     => $id_cuenta_banco, //Id del registro de cuenta bancaria
            "IdCliente"         => $cod_cliente_prov, //Id del Proveedor o Cliente
            "IdBanco"           => $banco, //valor numerico determinado por id del clasificador perteneciente a Entidades Bancarias (idpadre=319), poner 0 en caso de que sea otra entidad financiera
            "OtroBanco"         => NULL, // valor textual empleado en caso de no encontrar el Banco en el clasificador Entidades Bancarias. Caso contrario enviar NULL
            "IdTipoCuenta"      => 2842, // valor numerico determinado por Id del clasificador de Tipo Cuenta Bancaria (idPadre=2841), poner o en caso de no encontrar el tipo requerido						"OtroTipoCuenta"=>NULL, 
            "OtroTipoCuenta"    => NULL, // valor textual empleado en caso de requerir otro tipo de cuenta que no este en el clasificador Tipo Cuenta. Caso contrario enviar NULL
            "IdTipoMoneda"      => 322, // valor numerico determinado por el Id del clasificador Monedas (idPadre=320)
            "NroCuenta"         => $cuenta, //valor textual para el envio del numero de cuenta
            "BeneficiarioNombre"=> $nombre, 
            "BeneficiarioApellido"      => $apellido, 
            "BeneficiarioIdentificacion"=> NULL, // valor textual en el caso de requerir el registro de la identificacion. Caso contrario enviar NULL
            "BancoIntermediario"        => NULL, // valor textual en caso de hacer uso del campo. Caso contrario NULL
            "IdUsuarioReg"              => $globalUser, // valor numerico obtenido del id del usuario autenticado. Usar 0 en caso de no tener el id
            "Vigencia"                  => 1 //valor recuperado de los datos de cuenta
        );
}

$response = servicioCuentaBancaria($ws_peticion, $parametros);

header('Content-Type: application/json');
echo json_encode($response);