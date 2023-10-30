<?php

require_once '../conexion.php';
date_default_timezone_set('America/La_Paz');
session_start();

try {
    $dbh = new Conexion();
    $cod_cargo           = $_POST["cod_cargo"];
    $nro_version         = $_POST["nro_version"];
    $codigo_doc          = $_POST["codigo_doc"];
    $descripcion_cambios = $_POST["descripcion_cambios"];
    $fecha               = date("Y-m-d H:i:s");
    $cod_personal        = $_SESSION["globalUser"];
    $codigo              = $_POST["codigo"];

    if (!empty($codigo)) {
        // Si el campo codigo no está vacío, actualiza los datos existentes
        $stmt_actualizar = $dbh->prepare("UPDATE control_versiones 
                                          SET cod_cargo = :cod_cargo, 
                                              nro_version = :nro_version, 
                                              codigo_doc = :codigo_doc, 
                                              descripcion_cambios = :descripcion_cambios, 
                                              fecha = :fecha, 
                                              cod_personal = :cod_personal 
                                          WHERE codigo = :codigo");
        $stmt_actualizar->bindParam(':cod_cargo', $cod_cargo);
        $stmt_actualizar->bindParam(':nro_version', $nro_version);
        $stmt_actualizar->bindParam(':codigo_doc', $codigo_doc);
        $stmt_actualizar->bindParam(':descripcion_cambios', $descripcion_cambios);
        $stmt_actualizar->bindParam(':fecha', $fecha);
        $stmt_actualizar->bindParam(':cod_personal', $cod_personal);
        $stmt_actualizar->bindParam(':codigo', $codigo);
        $flagSuccess = $stmt_actualizar->execute();
        $tipo_accion = "Modificación realizado correctamente";
    } else {
        // Si el campo codigo está vacío, registra un nuevo dato
        $stmt_insertar = $dbh->prepare("INSERT INTO control_versiones (cod_cargo, nro_version, codigo_doc, descripcion_cambios, fecha, cod_personal) 
                                       VALUES (:cod_cargo, :nro_version, :codigo_doc, :descripcion_cambios, :fecha, :cod_personal)");
        $stmt_insertar->bindParam(':cod_cargo', $cod_cargo);
        $stmt_insertar->bindParam(':nro_version', $nro_version);
        $stmt_insertar->bindParam(':codigo_doc', $codigo_doc);
        $stmt_insertar->bindParam(':descripcion_cambios', $descripcion_cambios);
        $stmt_insertar->bindParam(':fecha', $fecha);
        $stmt_insertar->bindParam(':cod_personal', $cod_personal);
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
