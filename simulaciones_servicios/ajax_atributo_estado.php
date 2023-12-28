<?php
require_once '../conexion.php';
require_once '../functions.php';
require_once '../functionsGeneral.php';
require_once 'configModule.php';
set_time_limit(0);
session_start();
$dbh = new Conexion();

$cod_simulacionservicio_atributo = $_POST["cod_simulacionservicio_atributo"];

$sqlDetalleAtributos = "UPDATE simulaciones_servicios_atributos SET 
                        cod_estado = '2'
                        WHERE codigo = '$cod_simulacionservicio_atributo'";
$stmtDetalleAtributos = $dbh->prepare($sqlDetalleAtributos);

// Ejecutar la consulta
$flagsuccess = $stmtDetalleAtributos->execute();

// Verificar si la consulta fue exitosa
if ($flagsuccess) {
     echo json_encode(array("success" => true, "message" => "Datos guardados exitosamente."));
} else {
    echo json_encode(array("success" => false, "message" => "Error al guardar los datos."));
}
?>