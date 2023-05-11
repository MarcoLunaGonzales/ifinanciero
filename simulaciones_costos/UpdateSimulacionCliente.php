<?php

require_once '../conexion.php';

date_default_timezone_set('America/La_Paz');
session_start();

$dbh = new Conexion();

$codigo      = $_POST['codigo'];
$cod_cliente = $_POST['cod_cliente'];
try {
    // Verificación de meta
    $sqlDel = "UPDATE simulaciones_costos SET cod_cliente = '$cod_cliente' WHERE codigo = '$codigo'";
    $stmtDel = $dbh->prepare($sqlDel);
    $stmtDel->execute();
    echo json_encode(array(
        'status'        => true
    ));
} catch (Exception $e) {
    echo json_encode(array(
        'status' => false
    ));
}

?>