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

    $dbh = new Conexion();

    $data_detail = '';
    $sqlArea     = '';
    /**
     * DETALLE DE AREAS GRAL
     */
    $cadena_inicio = '<b class="text-warning">';
    $cadena_fin    = '</b>';
    $sqlArea = "SELECT a.codigo, UPPER(a.nombre) as nombre, a.cod_padre, a.abreviatura,
                    GROUP_CONCAT(DISTINCT CONCAT(
                            c.abreviatura, 
                            IFNULL(CONCAT(' ".$cadena_inicio."[', UPPER(personal_list), ']".$cadena_fin."'), '')
                        ) SEPARATOR ', ') AS cargos
                FROM areas a
                LEFT JOIN cargos_areasorganizacion ca ON ca.cod_areaorganizacion = a.codigo 
                LEFT JOIN cargos c ON c.codigo = ca.cod_cargo 
                LEFT JOIN (
                SELECT p.cod_area, p.cod_cargo, GROUP_CONCAT(DISTINCT CONCAT(p.primer_nombre, ' ', p.paterno) SEPARATOR ', ') AS personal_list
                FROM personal p
                GROUP BY p.cod_area, p.cod_cargo
                ) AS p ON p.cod_cargo = c.codigo AND p.cod_area = a.codigo
                WHERE a.cod_estado = 1
                GROUP BY a.codigo, a.nombre, a.cod_padre, a.abreviatura";
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