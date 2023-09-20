<?php
require_once 'conexion.php';
date_default_timezone_set('America/La_Paz');
$data = json_decode(file_get_contents('php://input'), true);
// Establece la conexión a la base de datos
$dbh = new Conexion();
// carga de POST
$nombre_post = empty($data['nombre_post']) ? 'vacio' : $data['nombre_post'];
$stmtInsert = $dbh->prepare("INSERT INTO test (nombre) VALUES (:nombre)");
$stmtInsert->bindParam(':nombre', $nombre_post);
$stmtInsert->execute();
// Bucle para insertar 10 números con un retraso de 2 segundos entre cada inserción
for ($i = 2; $i <= 10; $i++) {
    $nombre = date('His')." - Número $i";

    // Prepara y ejecuta la inserción en la tabla "test"
    $stmtInsert = $dbh->prepare("INSERT INTO test (nombre) VALUES (:nombre)");
    $stmtInsert->bindParam(':nombre', $nombre);
    $stmtInsert->execute();

    // Espera 2 segundos antes de continuar con la siguiente inserción
    sleep(2);
}

// Cierra la conexión a la base de datos
$dbh = null;

echo "Se han registrado 10 números con un retraso de 2 segundos entre cada inserción.";
?>