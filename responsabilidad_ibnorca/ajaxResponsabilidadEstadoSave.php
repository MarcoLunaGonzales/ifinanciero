<?php
require_once '../conexion.php';

date_default_timezone_set('America/La_Paz');
session_start();

/**
 * Registro de Seguimiento de Manual de Aprobación
 */
function guardarSeguimiento($cod_mof, $cod_etapa, $cod_personal, $cod_seguimiento_estado, $fecha, $observacion, $detalle_descriptivo)
{
    $dbh = new Conexion();
    $sql = "INSERT INTO mof_aprobacion_seguimiento (cod_mof,cod_etapa,cod_personal,cod_seguimiento_estado,fecha,observacion,detalle_descriptivo) VALUES (:cod_mof,:cod_etapa,:cod_personal,:cod_seguimiento_estado,:fecha,:observacion,:detalle_descriptivo)";
    $stmt = $dbh->prepare($sql);
    $stmt->bindParam(':cod_mof', $cod_mof);
    $stmt->bindParam(':cod_etapa', $cod_etapa);
    $stmt->bindParam(':cod_personal', $cod_personal);
    $stmt->bindParam(':cod_seguimiento_estado', $cod_seguimiento_estado);
    $stmt->bindParam(':fecha', $fecha);
    $stmt->bindParam(':observacion', $observacion);
    $stmt->bindParam(':detalle_descriptivo', $detalle_descriptivo);
    $stmt->execute();
}

try {
    $dbh = new Conexion();

    // Variables
    $cod_mof_aprobacion  = $_POST['cod_mof_aprobacion'];
    $manual_estado       = $_POST['estado'];           // cod_seguimiento_estado
    $manual_observacion  = $_POST['observacion'];
    $cod_personal        = $_SESSION["globalUser"];
    $fecha               = date('Y-m-d H:i:s');
    $detalle_descriptivo = '';
    
    // Verificar ETAPA de Manual de Aprobación 
    $sql = "SELECT ma.cod_etapa
            FROM mof_aprobacion ma
            WHERE ma.codigo = :cod_mof_aprobacion";
    $stmt = $dbh->prepare($sql);
    $stmt->bindParam(':cod_mof_aprobacion', $cod_mof_aprobacion);
    $stmt->execute();
    $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($resultado) {
        $actual_cod_etapa = $resultado['cod_etapa'];
    } else {
        $actual_cod_etapa = 0;
    }
    
    // Verificación de Nueva etapa
    if ($manual_estado == 1) {
        /***********
         * APROBADO
         ***********/
        $sql = "SELECT mae.codigo
              FROM mof_aprobacion_etapas mae
              WHERE mae.cod_etapa = :cod_etapa";
        $stmt = $dbh->prepare($sql);
        $stmt->bindParam(':cod_etapa', $actual_cod_etapa);
        $stmt->execute();
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
        $nuevo_cod_etapa = '';
        if ($resultado) {
            $nuevo_cod_etapa = $resultado['codigo'];
        }
        // Si nuevo_cod_etapa está vacío FINALIZA PROCESO
        if (empty($nuevo_cod_etapa)) {
            $sql = "UPDATE mof_aprobacion
                SET cod_estado = 2,
                    fecha_fin = :fecha_fin
                WHERE codigo = :cod_mof_aprobacion";
            $stmt = $dbh->prepare($sql);
            $stmt->bindParam(':fecha_fin', $fecha);
            $stmt->bindParam(':cod_mof_aprobacion', $cod_mof_aprobacion);
            $stmt->execute();
        } else {
            $sql = "UPDATE mof_aprobacion
                SET cod_etapa = :nuevo_cod_etapa
                WHERE codigo = :cod_mof_aprobacion";
            $stmt = $dbh->prepare($sql);
            $stmt->bindParam(':nuevo_cod_etapa', $nuevo_cod_etapa);
            $stmt->bindParam(':cod_mof_aprobacion', $cod_mof_aprobacion);
            $stmt->execute();
        }
    } else {
        /*************************
         * OTRO ESTADO (RECHAZADO)
         *************************/
        $sql = "SELECT mae.cod_etapa
                FROM mof_aprobacion_etapas mae
                WHERE mae.codigo = :cod_etapa";
        $stmt = $dbh->prepare($sql);
        $stmt->bindParam(':cod_etapa', $actual_cod_etapa);
        $stmt->execute();
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
        $nuevo_cod_etapa = '';
        if ($resultado) {
            $nuevo_cod_etapa = $resultado['cod_etapa'];
        }
        
        // Terminará el proceso de aprobación de la versión: RECHAZADO "3"
        $sql = "UPDATE mof_aprobacion
            SET cod_estado = 3,
                fecha_fin = :fecha_fin
            WHERE codigo = :cod_mof_aprobacion";
        $stmt = $dbh->prepare($sql);
        $stmt->bindParam(':fecha_fin', $fecha);
        $stmt->bindParam(':cod_mof_aprobacion', $cod_mof_aprobacion);
        $stmt->execute();
    }
    // Seguimiento
    guardarSeguimiento($cod_mof_aprobacion, $actual_cod_etapa, $cod_personal, $manual_estado, $fecha, $manual_observacion, $detalle_descriptivo);
    
    echo json_encode(array(
        'message' => 'Se actualizó el estado correctamente.',
        'status' => true
    ));
} catch (Exception $e) {
    echo json_encode(array(
        'message' => 'Ocurrió un error al guardar los datos.',
        'status' => false
    ));
}
?>
