<?php
require_once '../conexion.php';
require_once '../functions.php';
require_once '../functionsGeneral.php';
require_once 'configModule.php';
set_time_limit(0);
session_start();
$dbh = new Conexion();

$gestion1               = empty($_POST["gestion1"]) ? NULL : $_POST["gestion1"];
$gestion2               = empty($_POST["gestion2"]) ? NULL : $_POST["gestion2"];
$gestion3               = empty($_POST["gestion3"]) ? NULL : $_POST["gestion3"];
$cod_simulacionservicio = $_POST["cod_simulacionservicio"];

$sql = "UPDATE simulaciones_servicios 
        SET propuesta_gestion  = :gestion1,
            propuesta_gestion2 = :gestion2,
            propuesta_gestion3 = :gestion3 
        WHERE codigo = :cod_simulacionservicio";
$stmt = $dbh->prepare($sql);
$stmt->bindParam(':gestion1', $gestion1);
$stmt->bindParam(':gestion2', $gestion2);
$stmt->bindParam(':gestion3', $gestion3);
$stmt->bindParam(':cod_simulacionservicio', $cod_simulacionservicio);
$stmt->execute();

// Ejecutar la consulta
$flagsuccess = $stmt->execute();

// Verificar si la consulta fue exitosa
if ($flagsuccess) {
     echo json_encode(array(
        "status"  => true, 
        "message" => "Registro actualizado exitosamente."
    ));
} else {
    echo json_encode(array(
        "status"  => false, 
        "message" => "Error al guardar los datos."
    ));
}
?>