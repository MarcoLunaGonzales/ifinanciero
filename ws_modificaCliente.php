<?php
require_once 'conexion.php';
require_once 'functions.php';
session_start();
date_default_timezone_set('America/La_Paz');

/**
 * Servicio para actualizar los datos de un cliente
 **/
try{
    $data            = json_decode(file_get_contents('php://input'), true);
    $idCliente       = $data['idCliente'];
    $razonSocial     = $data['razonSocial'];
    $nit             = $data['nit'];
    $idTipoDocumento = $data['idTipoDocumento'];
    $idSolicitud     = $data['idSolicitud'];

    $dbh  = new Conexion();
    /**
     * Validación de datos del CLIENTE
     */
    $sql = "SELECT COUNT(1) as cantidad
            FROM clientes c
            WHERE c.clRazonSocial = '$razonSocial'
            AND c.clNit = '$nit'";
    // echo $sql;
    $stmt = $dbh->prepare($sql);
    $stmt->execute();
    $registro = $stmt->fetch(PDO::FETCH_ASSOC);
    $verifica_cliente = $registro['cantidad'];
    // En caso de tener una diferencia en los datos del cliente se ACTUALIZA con el servicio
    if($verifica_cliente == 0){
        // Parametros de entrada
        $idUsuario = $_SESSION["globalUser"];
        //LLAVES DE ACCESO AL WS
        $sIde      = "ifinanciero";
        $sKey      = "ce94a8dabdf0b112eafa27a5aa475751";
        /*PARAMETROS PARA EJECUTAR LAS OPERACIONES*/
        $parametros=array("sIdentificador"    => $sIde, 
                            "sKey"            => $sKey, 
                            "accion"          => "ActualizacionDatosFacturacion", 
                            "IdCliente"       => $idCliente, 	        // ID del registrado de la tabla cliente, recuperado de los datos de cliente
                            "RazonSocial"     => $razonSocial,          // Razon social de facturación 
                            "Nit"             => $nit,                  // Número de NIT 
                            "IdTipoDocumento" => $idTipoDocumento,      // Id tipo documento facturacion 1,2,3,4,5
                            "IdSolicitud"     => $idSolicitud,          // Id de la solicitud de la facturacion 
                            "IdUsuario"       => $idUsuario, 	        // Id usuario del sistema
                            "IdSistema"       => 13 		            // Id sistema financiero 
                            );
        $datos=json_encode($parametros);
        // abrimos la sesión cURL
        $ch = curl_init();
        // definimos la URL a la que hacemos la petición
        //curl_setopt($ch, CURLOPT_URL,"https://ibnored.ibnorca.org/wsibno/registro/ws-fin-cliente-contacto.php"); // on line 
        curl_setopt($ch, CURLOPT_URL,"http://ibnored.ibnorca.org/wsibnob/registro/ws-fin-cliente-contacto.php"); // db pruebas
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $datos);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $remote_server_output = curl_exec ($ch);
        curl_close ($ch);
        // Obtener valores
        // json_decode($remote_server_output)->estado
        // imprimir en formato JSON
        // header('Content-type: application/json'); 	
        // print_r(json_encode($remote_server_output)); 
        $response = json_encode($remote_server_output);
        if ($response === null) {
            throw new Exception("Error en la respuesta del servidor");
        }

        /*************************************
         * Almacenamiento de LOG de solicitud
         *************************************/
        $fechaHora = date("Y-m-d H:i:s");
        $sql  = "INSERT INTO log_cliente_upd(cod_solicitudfacturacion,json_enviado,json_recibido,fecha)
                VALUES ('$idSolicitud','$datos','$response','$fechaHora') ";
        // echo $sql;
        $stmt = $dbh->prepare($sql);
        $stmt->execute();
    }
    $response = [
        'message' => "Se finalizo el proceso exitosamente",
        'status'  => false
    ];
    // Retorna respuesta JSON
    // header('Content-Type: application/json');
    // echo json_encode($response);
    exit;
} catch (Exception $e) {
    $response = [
        'message' => "Error en el servicio: " . $e->getMessage(),
        'status'  => false
    ];
    // Enviar respuesta JSON
    // header('Content-Type: application/json');
    // echo json_encode($response);
    exit;
}