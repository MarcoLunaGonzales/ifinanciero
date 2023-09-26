<?php

require_once '../conexion.php';
date_default_timezone_set('America/La_Paz');
session_start();

try {
    $dbh = new Conexion();
    $codigo = $_POST["codigo"];

    // Si el campo codigo no está vacío, actualiza los datos existentes
    $stmt_actualizar = $dbh->prepare("UPDATE control_versiones 
                            SET estado = CASE 
                                WHEN estado = 1 THEN 0 
                                ELSE 1 
                            END
                            WHERE codigo = :codigo");
    $stmt_actualizar->bindParam(':codigo', $codigo);
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
