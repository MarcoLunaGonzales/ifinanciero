<?php
require_once '../conexion.php';

date_default_timezone_set('America/La_Paz');

try {
    $codigo = $_POST['codigo'];
    $dbh = new Conexion();
    $sql = "UPDATE responsabilidades_generales
        SET estado = CASE
                WHEN estado = 1 THEN 2
                ELSE 1
            END
        WHERE codigo = :codigo";
    $stmt = $dbh->prepare($sql);
    $stmt->bindParam(':codigo', $codigo);
    $stmt->execute();
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
