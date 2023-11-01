<?php

require_once '../conexion.php';
date_default_timezone_set('America/La_Paz');
session_start();

try {
    $dbh = new Conexion();
    $cod_cargo     = $_POST["cod_cargo"];
    $cod_personal  = $_POST["cod_personal"];
    $fecha_inicio  = $_POST["fecha_inicio"];
    $fecha_fin     = $_POST["fecha_fin"];
    $fecha         = date("Y-m-d H:i:s");

    $codigo        = $_POST["codigo"];

    if (!empty($codigo)) {
        // Si el campo codigo no está vacío, actualiza los datos existentes
        $stmt_actualizar = $dbh->prepare("UPDATE cargos_interinos_historicos 
                                          SET cod_cargo = :cod_cargo,
                                                 cod_personal = :cod_personal, 
                                                fecha_inicio = :fecha_inicio, 
                                                fecha_fin = :fecha_fin, 
                                                fecha_actualizacion = :fecha_actualizacion
                                          WHERE codigo = :codigo");
        $stmt_actualizar->bindParam(':cod_cargo', $cod_cargo);
        $stmt_actualizar->bindParam(':cod_personal', $cod_personal);
        $stmt_actualizar->bindParam(':fecha_inicio', $fecha_inicio);
        $stmt_actualizar->bindParam(':fecha_fin', $fecha_fin);
        $stmt_actualizar->bindParam(':fecha_actualizacion', $fecha);
        $stmt_actualizar->bindParam(':codigo', $codigo);
        $flagSuccess = $stmt_actualizar->execute();
        $tipo_accion = "Modificación realizado correctamente";
    } else {
        // Si el campo codigo está vacío, registra un nuevo dato
        $stmt_insertar = $dbh->prepare("INSERT INTO cargos_interinos_historicos (cod_cargo, cod_personal, fecha_inicio, fecha_fin, fecha_registro) 
                                       VALUES (:cod_cargo, :cod_personal, :fecha_inicio, :fecha_fin, :fecha_registro)");
        $stmt_insertar->bindParam(':cod_cargo', $cod_cargo);
        $stmt_insertar->bindParam(':cod_personal', $cod_personal);
        $stmt_insertar->bindParam(':fecha_inicio', $fecha_inicio);
        $stmt_insertar->bindParam(':fecha_fin', $fecha_fin);
        $stmt_insertar->bindParam(':fecha_registro', $fecha);
        $flagSuccess = $stmt_insertar->execute();
        $tipo_accion = "Registro realizado correctamente";
    }

    if ($flagSuccess) {
        $response['status']  = true;
        $response['message'] = $tipo_accion;
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
