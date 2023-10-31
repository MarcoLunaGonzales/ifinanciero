<?php

require_once '../conexion.php';
date_default_timezone_set('America/La_Paz');
session_start();

try {
    $dbh = new Conexion();
    $codigo = $_POST["codigo"];
    $fecha  = date("Y-m-d H:i:s");

    // Si el campo codigo no está vacío, actualiza los datos existentes
    $stmt_actualizar = $dbh->prepare("UPDATE cargos_interinos_historicos 
                            SET estado = CASE 
                                WHEN estado = 1 THEN 0
                                ELSE 1
                            END,
                            fecha_actualizacion = :fecha_actualizacion
                            WHERE codigo = :codigo");
    $stmt_actualizar->bindParam(':codigo', $codigo);
    $stmt_actualizar->bindParam(':fecha_actualizacion', $fecha);
    $flagSuccess = $stmt_actualizar->execute();

    if ($flagSuccess) {
        $response['status']  = true;
        $response['message'] = 'Modificación de estado correctamente.';
    } else {
        $response['status']  = false;
        $response['message'] = 'No se pudo realizar la operación.';
    }
} catch (PDOException $ex) {
    $response['status']  = false;
    $response['message'] = 'Error: ' . $ex->getMessage();
}

// Devuelve la respuesta en formato JSON
header('Content-Type: application/json');
echo json_encode($response);
?>
