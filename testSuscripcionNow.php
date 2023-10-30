<?php //ESTADO FINALIZADO

require_once 'conexion.php';
require_once 'functions.php';

$url_servicio = obtenerValorConfiguracion(112);
// echo $url_servicio;
// exit;

// Datos a enviar al servicio
$datos = json_encode(array(
    "codigo" => 31123,
));
// URL del servicio
$url_servicio = $url_servicio.'ws_generarSuscripcion.php';
// Inicializa cURL
$ch = curl_init($url_servicio);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $datos);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 1);
curl_setopt($ch, CURLOPT_NOSIGNAL, 1);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

$respuesta = curl_exec($ch);

// if ($respuesta === false) {
//     // Manejar errores en la solicitud
//     echo 'Error cURL: ' . curl_error($ch);
// } else {
//     // Procesar la respuesta
//     echo 'Respuesta: ' . $respuesta;
// }

curl_close($ch);

echo "hola";