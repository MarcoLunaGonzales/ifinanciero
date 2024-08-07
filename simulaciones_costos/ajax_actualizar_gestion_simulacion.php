<?php
require_once '../conexion.php';
require_once '../functions.php';
require_once '../functionsGeneral.php';
require_once 'configModule.php';
set_time_limit(0);
session_start();
$dbh = new Conexion();

$gestion1 = empty($_POST["gestion1"]) ? NULL : $_POST["gestion1"];
$codigo   = $_POST["codigo"];

$sql = "UPDATE simulaciones_costos 
        SET propuesta_gestion  = :gestion1
        WHERE codigo = :codigo";
$stmt = $dbh->prepare($sql);
$stmt->bindParam(':gestion1', $gestion1);
$stmt->bindParam(':codigo', $codigo);
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