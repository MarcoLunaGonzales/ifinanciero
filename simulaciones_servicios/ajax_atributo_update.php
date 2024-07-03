<?php
require_once '../conexion.php';
require_once '../functions.php';
require_once '../functionsGeneral.php';
require_once 'configModule.php';
set_time_limit(0);
session_start();
$dbh = new Conexion();

$cod_simulacionservicio_atributo = $_POST["cod_simulacionservicio_atributo"];
$nombreAtributo     = $_POST["nombre"];
$direccionAtributo  = $_POST["direccion"];
$procesosAtributo   = $_POST["procesos"];
$marcaAtributo      = $_POST["marca"];
$selloAtributo      = $_POST["sello"];

$sqlDetalleAtributos = "UPDATE simulaciones_servicios_atributos SET 
                        nombre = '$nombreAtributo',
                        direccion = '$direccionAtributo',
                        marca = '$marcaAtributo',
                        nro_sello = '$selloAtributo',
                        procesos = '$procesosAtributo'
                        WHERE codigo = '$cod_simulacionservicio_atributo'";
// echo $sqlDetalleAtributos;
$stmtDetalleAtributos = $dbh->prepare($sqlDetalleAtributos);

// Ejecutar la consulta
$flagsuccess = $stmtDetalleAtributos->execute();

// Verificar si la consulta fue exitosa
if ($flagsuccess) {
    $codSimulacionServicioAtributo = $cod_simulacionservicio_atributo;

    $sqlA="DELETE FROM simulaciones_servicios_atributosnormas where cod_simulacionservicioatributo='$codSimulacionServicioAtributo'";
    $stmtA = $dbh->prepare($sqlA);
    $stmtA->execute();
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