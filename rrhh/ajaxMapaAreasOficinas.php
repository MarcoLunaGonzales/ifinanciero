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
      if ($item['cod_padre'] == $parent) {
        $item['id']       = $item['codigo'];
        $lista_cargos     = empty($item['cargos']) ? '-' : $item['cargos'];
        $item['text']     = '<b class="text-success">['.$item['abreviatura'].']</b> '.$item['nombre'].' | <b>Cargos: <b class="text-primary">('.$lista_cargos.')</b> </b>';
        $item['children'] = buildTree($data, $item['codigo']);
        $tree[] = $item;
      }
    }
    return $tree;
}

try {
    // COD_OFICINA
    $cod_oficina      = empty($_POST['cod_oficina']) ? '' : $_POST['cod_oficina'];

    $dbh = new Conexion();

    $data_detail = '';
    $sqlArea     = '';
    /**
     * DETALLE DE AREAS POR OFICINAS
     */
    if(!empty($cod_oficina)){
        // Detalle Oficina
        $sqlOf = "SELECT uo.codigo, uo.nombre, uo.abreviatura
            FROM unidades_organizacionales uo
            WHERE uo.codigo = '$cod_oficina'";
        $stmtOf  = $dbh->prepare($sqlOf);
        $stmtOf->execute();
        $detalle = $stmtOf->fetch(PDO::FETCH_ASSOC);
        $data_detail = '['.$detalle['abreviatura'].'] '.$detalle['nombre'];
        // Lista de Areas por Oficina
        $sqlArea = "SELECT a.codigo, a.nombre, a.cod_padre, a.abreviatura, GROUP_CONCAT(DISTINCT c.abreviatura SEPARATOR ', ') AS cargos
                    FROM areas_organizacion ao
                    LEFT JOIN areas a ON a.codigo = ao.cod_area
                    LEFT JOIN cargos_areasorganizacion ca ON ca.cod_areaorganizacion = a.codigo 
                    LEFT JOIN cargos c ON c.codigo = ca.cod_cargo 
                    WHERE a.cod_estado = 1 
                    AND ao.cod_unidad = '$cod_oficina'
                    GROUP BY a.codigo, a.nombre, a.cod_padre, a.abreviatura";
    }else{
    /**
     * DETALLE DE AREAS GRAL
     */
        // Lista de Areas
        $sqlArea = "SELECT a.codigo, a.nombre, a.cod_padre, a.abreviatura, GROUP_CONCAT(DISTINCT c.abreviatura SEPARATOR ', ') AS cargos
                    FROM areas a 
                    LEFT JOIN areas_organizacion ao ON ao.cod_area = a.codigo 
                    LEFT JOIN cargos_areasorganizacion ca ON ca.cod_areaorganizacion = a.codigo 
                    LEFT JOIN cargos c ON c.codigo = ca.cod_cargo 
                    WHERE a.cod_estado = 1
                    GROUP BY a.codigo, a.nombre, a.cod_padre, a.abreviatura";
    }
    $stmt    = $dbh->prepare($sqlArea);
    $stmt->execute();
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    // Construir el árbol
    $jsonTree = buildTree($data);

    echo json_encode(array(
        'status'      => true,
        'data'        => $jsonTree,
        'data_detail' => $data_detail
    ));
} catch (Exception $e) {
    echo json_encode(array(
        'status' => false
    ));
}

?>