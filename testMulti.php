<?php
require_once 'conexion.php';
date_default_timezone_set('America/La_Paz');

echo "Inicio el proceso";

// Datos a enviar al servicio
$datos = json_encode(array("nombre_post"=>"prueba de envio de datos"));
// URL del servicio
$url_servicio = "http://localhost/ifinanciero/testMultiServicio.php";
// Inicializa cURL
$ch = curl_init($url_servicio);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $datos);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
// Evita bloquear la ejecución y ejecuta la solicitud en segundo plano
curl_setopt($ch, CURLOPT_TIMEOUT, 1);
curl_setopt($ch, CURLOPT_NOSIGNAL, 1);
// Inicia la ejecución de cURL
curl_exec($ch);
// Cierra la conexión cURL
curl_close($ch);

echo "</br>";
echo "TERMINO: El proceso continúa sin esperar la respuesta del cURL.";
?>
