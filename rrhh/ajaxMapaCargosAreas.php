<?php
require_once '../conexion.php';

date_default_timezone_set('America/La_Paz');
session_start();

/**
 * FUNCIÓN RECURSIVIDAD
 * Generación de Arbol
 */
function buildTree($data, $parent = '') {
    $tree = array();
    foreach ($data as $item) {
        // Se Verifican los SOLO los cargos PADRE
        if ($item['cod_padre'] == $parent) {
            $item['id']       = $item['codigo'];
            $item['text']     = '<b class="text-primary">['.$item['abreviatura'].']</b> '.$item['nombre'];
            $item['children'] = buildTree($data, $item['codigo']);
            $tree[] = $item;
        }
    }
    return $tree;
}

try {
    // cod_area
    $cod_area = empty($_POST['cod_area']) ? '' : $_POST['cod_area'];

    $dbh = new Conexion();

    /**
     * DETALLE DE CARGOS DE ACUERDO AL ÁREA SELECCIONADA
     */
    $sqlArea = "SELECT c.codigo, c.nombre, c.abreviatura, c.cod_padre
                FROM cargos_areasorganizacion ca
                LEFT JOIN cargos c ON c.codigo = ca.cod_cargo
                WHERE ca.cod_areaorganizacion = '$cod_area'
                AND c.cod_estadoreferencial = 1
                ORDER BY c.cod_padre DESC";
    $stmt    = $dbh->prepare($sqlArea);
    $stmt->execute();
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    // Construir el árbol
    $jsonTree = buildTree($data);

    echo json_encode(array(
        'status'      => true,
        'data'        => $jsonTree,
    ));
} catch (Exception $e) {
    echo json_encode(array(
        'status' => false
    ));
}

?>