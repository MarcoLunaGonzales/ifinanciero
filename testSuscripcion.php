<?php
require_once 'conexion.php';
require_once 'functions.php';
date_default_timezone_set('America/La_Paz');

echo "Inicio el proceso suscripción";
$codigo            = 0; // (solicitudes_facturacion: codigo) | cod_solicitudfacturacion
$stringFacturasCod = 0; // (facturas_venta: codigo) | cod_factura

global $url_servicio;
$url_servicio = obtenerValorConfiguracion(112);
wsGenerarSuscripcion($codigo, $stringFacturasCod);

function wsGenerarSuscripcion($codigo, $stringFacturasCod){
    global $url_servicio;
    // Datos a enviar al servicio
    $datos = json_encode(array(
        "codigo"            => $codigo,
        "stringFacturasCod" => $stringFacturasCod
    ));
    // URL del servicio
    $url_servicio = $url_servicio.'ws_generarSuscripcion.php'."?codigo=$codigo&stringFacturasCod=$stringFacturasCod";
    echo $url_servicio;
    // Inicializa cURL
    $ch = curl_init($url_servicio);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $datos);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    // Evita bloquear la ejecución y ejecuta la solicitud en segundo plano
    curl_setopt($ch, CURLOPT_TIMEOUT, 1);
    curl_setopt($ch, CURLOPT_NOSIGNAL, 1);
    curl_exec($ch);
    curl_close($ch);
}
?>
