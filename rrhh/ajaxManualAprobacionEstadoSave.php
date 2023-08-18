<?php
require_once '../conexion.php';

date_default_timezone_set('America/La_Paz');
session_start();

/**
 * Registro de Seguimiento de Manual de Aprobación
 */
function guardarSeguimiento($cod_manual, $cod_etapa, $cod_personal, $cod_seguimiento_estado, $fecha, $observacion, $detalle_descriptivo)
{
    $dbh = new Conexion();
    $sql = "INSERT INTO manuales_aprobacion_seguimiento (cod_manual,cod_etapa,cod_personal,cod_seguimiento_estado,fecha,observacion,detalle_descriptivo) VALUES (:cod_manual,:cod_etapa,:cod_personal,:cod_seguimiento_estado,:fecha,:observacion,:detalle_descriptivo)";
    $stmt = $dbh->prepare($sql);
    $stmt->bindParam(':cod_manual', $cod_manual);
    $stmt->bindParam(':cod_etapa', $cod_etapa);
    $stmt->bindParam(':cod_personal', $cod_personal);
    $stmt->bindParam(':cod_seguimiento_estado', $cod_seguimiento_estado);
    $stmt->bindParam(':fecha', $fecha);
    $stmt->bindParam(':observacion', $observacion);
    $stmt->bindParam(':detalle_descriptivo', $detalle_descriptivo);
    $stmt->execute();
}

try {$dbh = new Conexion();

    // Variables
    $cod_manual_aprobacion = $_POST['cod_manual_aprobacion'];
    $manual_estado         = $_POST['manual_estado'];           // cod_seguimiento_estado
    $manual_observacion    = $_POST['manual_observacion'];
    $cod_personal          = $_SESSION["globalUser"];
    $fecha                 = date('Y-m-d H:i:s');
    $detalle_descriptivo   = '';
    
    // Verificar ETAPA de Manual de Aprobación 
    $sql = "SELECT ma.cod_etapa
            FROM manuales_aprobacion ma
            WHERE ma.codigo = :cod_manual_aprobacion";
    $stmt = $dbh->prepare($sql);
    $stmt->bindParam(':cod_manual_aprobacion', $cod_manual_aprobacion);
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
              FROM manuales_aprobacion_etapas mae
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
            $sql = "UPDATE manuales_aprobacion
                SET estado = 2,
                    fecha_fin = :fecha_fin
                WHERE codigo = :cod_manual_aprobacion";
            $stmt = $dbh->prepare($sql);
            $stmt->bindParam(':fecha_fin', $fecha);
            $stmt->bindParam(':cod_manual_aprobacion', $cod_manual_aprobacion);
            $stmt->execute();
        } else {
            $sql = "UPDATE manuales_aprobacion
                SET cod_etapa = :nuevo_cod_etapa
                WHERE codigo = :cod_manual_aprobacion";
            $stmt = $dbh->prepare($sql);
            $stmt->bindParam(':nuevo_cod_etapa', $nuevo_cod_etapa);
            $stmt->bindParam(':cod_manual_aprobacion', $cod_manual_aprobacion);
            $stmt->execute();
        }
    } else {
        /*************************
         * OTRO ESTADO (RECHAZADO)
         *************************/
        $sql = "SELECT mae.cod_etapa
                FROM manuales_aprobacion_etapas mae
                WHERE mae.codigo = :cod_etapa";
        $stmt = $dbh->prepare($sql);
        $stmt->bindParam(':cod_etapa', $actual_cod_etapa);
        $stmt->execute();
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
        $nuevo_cod_etapa = '';
        if ($resultado) {
            $nuevo_cod_etapa = $resultado['cod_etapa'];
        }
        // Si nuevo_cod_etapa está vacío o esta con valor en CERO(0) no cambia Actualiza etapa
        if (!empty($nuevo_cod_etapa) && $nuevo_cod_etapa != 0) {
            // Retrocede un nivel
            $sql = "UPDATE manuales_aprobacion
                SET cod_etapa = :nuevo_cod_etapa
                WHERE codigo = :cod_manual_aprobacion";
            $stmt = $dbh->prepare($sql);
            $stmt->bindParam(':nuevo_cod_etapa', $nuevo_cod_etapa);
            $stmt->bindParam(':cod_manual_aprobacion', $cod_manual_aprobacion);
            $stmt->execute();
        }
    }
    // Seguimiento
    guardarSeguimiento($cod_manual_aprobacion, $actual_cod_etapa, $cod_personal, $manual_estado, $fecha, $manual_observacion, $detalle_descriptivo);
    
    echo json_encode(array(
        'message' => 'Se inicializó la aprobación de manual correctamente.',
        'status' => true
    ));
} catch (Exception $e) {
    echo json_encode(array(
        'message' => 'Ocurrió un error al guardar los datos.',
        'status' => false
    ));
}
?>
