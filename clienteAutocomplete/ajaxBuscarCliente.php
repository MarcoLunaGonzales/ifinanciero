<?php
require("../conexion.php");

$dbh = new Conexion();
// Obtener el parámetro de búsqueda
$cliente_nombre = $_GET['cliente_nombre'];
$queryAreas = "SELECT codigo AS value, nombre AS label 
               FROM clientes 
               WHERE cod_estadoreferencial = 1
               AND nombre LIKE :cliente_nombre 
               ORDER BY nombre
               LIMIT 100";

try {
    $stmtAreas = $dbh->prepare($queryAreas);
    $stmtAreas->bindValue(':cliente_nombre', '%' . $cliente_nombre . '%', PDO::PARAM_STR);
    $stmtAreas->execute();
    $result = $stmtAreas->fetchAll(PDO::FETCH_ASSOC);

    $response = [
        'status'  => true,
        'message' => 'Clientes encontrados',
        'data'    => $result
    ];
    echo json_encode($response);
} catch (PDOException $e) {
    // Preparar la respuesta de error
    $response = [
        'status'  => false,
        'message' => 'Error en la consulta: ' . $e->getMessage(),
        'data'    => []
    ];
    echo json_encode($response);
}
?>
