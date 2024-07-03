<?php
require_once '../conexion.php';
require_once '../functions.php';
require_once '../functionsGeneral.php';
require_once 'configModule.php';
set_time_limit(0);
session_start();
$dbh = new Conexion();

$cod_simulacion_servicio = $_POST["cod_simulacion_servicio"];
$nombreAtributo     = $_POST["nombre"];
$direccionAtributo  = $_POST["direccion"];
$procesosAtributo   = $_POST["procesos"];
$marcaAtributo      = $_POST["marca"];
$selloAtributo      = $_POST["sello"];
$tipo_atributo      = $_POST["tipo_atributo"];

$sqlDetalleAtributos = "INSERT INTO simulaciones_servicios_atributos (cod_simulacionservicio, nombre, direccion, cod_tipoatributo,marca,norma,nro_sello,cod_pais,cod_estado,cod_ciudad,procesos) 
                        VALUES ('$cod_simulacion_servicio', '$nombreAtributo', '$direccionAtributo', '$tipo_atributo', '$marcaAtributo', '', '$selloAtributo', '', '1', '', '$procesosAtributo')";
// echo $sqlDetalleAtributos;
$stmtDetalleAtributos = $dbh->prepare($sqlDetalleAtributos);

// Ejecutar la consulta
$flagsuccess = $stmtDetalleAtributos->execute();

// Verificar si la consulta fue exitosa
if ($flagsuccess) {
    $codSimulacionServicioAtributo = $dbh->lastInsertId(); // Obtener el ID del último registro insertado

    // Normas Nacionales
    if (isset($_POST["norma_nac_cod"])) {
        $normasFila = explode(",", $_POST["norma_nac_cod"]);
        foreach ($normasFila as $codNorma) {
            $sqlDetalleAtributosNormas = "INSERT INTO simulaciones_servicios_atributosnormas (cod_simulacionservicioatributo, cod_norma, precio, cantidad, catalogo) 
                                           VALUES (:codSimulacionServicioAtributo, :codNorma, '10', 1, 'L')";
            $stmtDetalleAtributosNormas = $dbh->prepare($sqlDetalleAtributosNormas);
            $stmtDetalleAtributosNormas->bindParam(':codSimulacionServicioAtributo', $codSimulacionServicioAtributo);
            $stmtDetalleAtributosNormas->bindParam(':codNorma', $codNorma);
            $stmtDetalleAtributosNormas->execute();
        }
    }

    // Normas Internacionales
    if (isset($_POST["norma_int_cod"])) {
        $normasFila = explode(",", $_POST["norma_int_cod"]);
        foreach ($normasFila as $codNorma) {
            $sqlDetalleAtributosNormas = "INSERT INTO simulaciones_servicios_atributosnormas (cod_simulacionservicioatributo, cod_norma, precio, cantidad, catalogo) 
                                           VALUES (:codSimulacionServicioAtributo, :codNorma, '10', 1, 'I')";
            $stmtDetalleAtributosNormas = $dbh->prepare($sqlDetalleAtributosNormas);
            $stmtDetalleAtributosNormas->bindParam(':codSimulacionServicioAtributo', $codSimulacionServicioAtributo);
            $stmtDetalleAtributosNormas->bindParam(':codNorma', $codNorma);
            $stmtDetalleAtributosNormas->execute();
        }
    }

    echo json_encode(array("success" => true, "message" => "Datos guardados exitosamente."));
} else {
    echo json_encode(array("success" => false, "message" => "Error al guardar los datos."));
}
?>