<?php

require_once '../conexion.php';
date_default_timezone_set('America/La_Paz');

$dbh = new Conexion();

try {
    $nombre = $_POST["nombre"];
    $codigo = $_POST["codigo"];

    if ($codigo == 0) {
        // Insertar un nuevo registro
        $stmt_insertar = $dbh->prepare("INSERT INTO responsabilidades_generales (nombre) VALUES (:nombre)");
        $stmt_insertar->bindParam(':nombre', $nombre);
        $flagSuccess = $stmt_insertar->execute();

        $response['status']  = true;
        $response['message'] = 'Registro insertado correctamente.';
    } else {
        // Actualizar un registro existente
        $stmt_actualizar = $dbh->prepare("UPDATE responsabilidades_generales SET nombre = :nombre WHERE codigo = :codigo");
        $stmt_actualizar->bindParam(':nombre', $nombre);
        $stmt_actualizar->bindParam(':codigo', $codigo);

        $flagSuccess = $stmt_actualizar->execute();

        $response['status']  = true;
        $response['message'] = 'Registro actualizado correctamente.';
    }
} catch (PDOException $ex) {
    $response['status'] = 'error';
    $response['message'] = 'Un error ocurrió: ' . $ex->getMessage();
}

// Devuelve la respuesta en formato JSON
header('Content-Type: application/json');
echo json_encode($response);
?>
