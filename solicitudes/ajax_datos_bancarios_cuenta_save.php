<?php
session_start();
require_once '../conexion.php';
require_once '../functionsGeneral.php';
require_once '../functions.php';
require_once '../styles.php';

$dbh = new Conexion();
$sqlX="SET NAMES 'utf8'";
$stmtX = $dbh->prepare($sqlX);
$stmtX->execute();
set_time_limit(300);
$globalUser=$_SESSION["globalUser"];
$codigo=$_GET['codigo'];
$banco=$_GET['banco'];
$cuenta=$_GET['cuenta'];
$nombre=$_GET['nombre'];
$apellido=$_GET['apellido'];

$direccion=obtenerValorConfiguracion(42);
//insertar cambios en el servicio web CUENTAS BANCARIAS
    $sIde = "ifinanciero";
    $sKey = "ce94a8dabdf0b112eafa27a5aa475751";
    $parametros=array("sIdentificador"=>$sIde, "sKey"=>$sKey, 
                        "accion"=>"RegistrarCuentaBanco",
                        "IdCliente" => $codigo, //Id del Proveedor o Cliente
                        "IdBanco"=>$banco, //valor numerico determinado por id del clasificador perteneciente a Entidades Bancarias (idpadre=319), poner 0 en caso de que sea otra entidad financiera
                        "OtroBanco"=>NULL, // valor textual empleado en caso de no encontrar el Banco en el clasificador Entidades Bancarias. Caso contrario enviar NULL
                        "IdTipoCuenta"=>2842, // valor numerico determinado por Id del clasificador de Tipo Cuenta Bancaria (idPadre=2841), poner o en caso de no encontrar el tipo requerido                       "OtroTipoCuenta"=>NULL, 
                        "OtroTipoCuenta"=>NULL, // valor textual empleado en caso de requerir otro tipo de cuenta que no este en el clasificador Tipo Cuenta. Caso contrario enviar NULL
                        "IdTipoMoneda"=> 322, // valor numerico determinado por el Id del clasificador Monedas (idPadre=320)
                        "NroCuenta"=>$cuenta, //valor textual para el envio del numero de cuenta
                        "BeneficiarioNombre"=>$nombre, 
                        "BeneficiarioApellido"=>$apellido, 
                        "BeneficiarioIdentificacion"=>NULL, // valor textual en el caso de requerir el registro de la identificacion. Caso contrario enviar NULL
                        "BancoIntermediario"=>NULL, // valor textual en caso de hacer uso del campo. Caso contrario NULL
                        "IdUsuarioReg"=>$globalUser // valor numerico obtenido del id del usuario autenticado. Usar 0 en caso de no tener el id
                        );
    $parametros=json_encode($parametros);
    // abrimos la sesión cURL
    $ch = curl_init();
    // definimos la URL a la que hacemos la petición
    //curl_setopt($ch, CURLOPT_URL,"http://ibnored.ibnorca.org/wsibno/registro/ws-cuentabanco.php"); // OFFICIAL
    curl_setopt($ch, CURLOPT_URL,$direccion."registro/ws-cuentabanco.php"); // PRUEBA
    // indicamos el tipo de petición: POST
    curl_setopt($ch, CURLOPT_POST, TRUE);
    // definimos cada uno de los parámetros
    curl_setopt($ch, CURLOPT_POSTFIELDS, $parametros);
    // recibimos la respuesta y la guardamos en una variable
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $remote_server_output = curl_exec ($ch);
    // cerramos la sesión cURL
    curl_close ($ch);

    $datos_respu= json_decode($remote_server_output);

    //print_r($datos_respu);