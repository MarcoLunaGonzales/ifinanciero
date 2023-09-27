<?php

require_once '../conexion.php';
date_default_timezone_set('America/La_Paz');

$dbh = new Conexion();

try {
    $nombre = $_POST["nombre"];
    $fecha  = date("Y-m-d H:i:s");

    $archivo         = $_FILES["archivo"];
    $archivo_nombre  = $archivo["name"];
    $archivo_tmp     = $archivo["tmp_name"];
    $carpeta_destino = "../assets/archivos_mof/";

    // Verificar si la carpeta de destino existe, si no, créala
    if (!file_exists($carpeta_destino)) {
        mkdir($carpeta_destino, 0755, true);
    }

    $archivo_destino = $carpeta_destino . $archivo_nombre;

    if ($_POST["codigo"] == 0) {
        // Insertar un nuevo registro
        // Mover el archivo a la carpeta destino
        if (move_uploaded_file($archivo_tmp, $archivo_destino)) {
            $stmt_insertar = $dbh->prepare("INSERT INTO mof (nombre, archivo, fecha) VALUES (:nombre, :archivo, :fecha)");
            $stmt_insertar->bindParam(':nombre', $nombre);
            $stmt_insertar->bindParam(':archivo', $archivo_nombre);
            $stmt_insertar->bindParam(':fecha', $fecha);
            $flagSuccess = $stmt_insertar->execute();

            $response['status']  = true;
            $response['message'] = 'Registro insertado correctamente.';
        } else {
            $response['status']  = false;
            $response['message'] = 'Error al subir el archivo.';
        }
    } else {
        // Actualizar un registro existente

        $codigo = $_POST["codigo"];
        // Actualiza la información del registro
        $stmt_actualizar = $dbh->prepare("UPDATE mof SET nombre = :nombre, archivo = :archivo WHERE codigo = :codigo");
        $stmt_actualizar->bindParam(':nombre', $nombre);
        $stmt_actualizar->bindParam(':archivo', $archivo_nombre);
        $stmt_actualizar->bindParam(':codigo', $codigo);
        
        // Mover el archivo a la carpeta destino
        if (move_uploaded_file($archivo_tmp, $archivo_destino)) {
            $flagSuccess = $stmt_actualizar->execute();
            $response['status']  = true;
            $response['message'] = 'Registro actualizado correctamente.';
        } else {
            $response['status']  = false;
            $response['message'] = 'Error al subir el archivo.';
        }
    }
} catch (PDOException $ex) {
    $response['status'] = 'error';
    $response['message'] = 'Un error ocurrió: ' . $ex->getMessage();
}

// Devuelve la respuesta en formato JSON
header('Content-Type: application/json');
echo json_encode($response);
?>
