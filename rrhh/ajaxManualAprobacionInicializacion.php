<?php
require_once '../conexion.php';

date_default_timezone_set('America/La_Paz');
session_start();

$cod_direccion = 847;   // Codigo AREA: DIRECCIÓN EJECUTIVA
/**
 * Se realiza la busque del área de segundo nivel que depende de
 * DIRECCIÓN EJECUTIVA
 * Esta función permite hacer una busqueda escalada
 */
function buscarArea($cod_area){
  global $cod_direccion;
  $dbh = new Conexion();
  $sql = "SELECT DISTINCT(a.codigo), a.cod_padre
          FROM cargos_areasorganizacion ca
          LEFT JOIN areas a ON a.codigo = ca.cod_areaorganizacion
          WHERE a.codigo IS NOT NULL AND ca.cod_areaorganizacion = :cod_area";
  $stmt = $dbh->prepare($sql);
  $stmt->bindParam(':cod_area', $cod_area);
  $stmt->execute();
  $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
  foreach ($resultados as $resultado) {
    $cod_area       = $resultado['codigo'];
    $cod_area_padre = $resultado['cod_padre'];
    if($cod_area == $cod_direccion || $cod_area_padre == $cod_direccion){
      $cod_area = $cod_area;
      return $cod_area;
    }else{
      buscarArea($cod_area_padre);
    }
  }
}

$dbh = new Conexion();
// Variables
$cod_etapa    = 1;                  // Etapa inicial
$fecha_inicio = date('Y-m-d H:i:s');
$cod_cargo    = $_POST['cod_cargo'];

// Verificación de AREA del CARGO SELECCIONADO
$sql = "SELECT DISTINCT(a.codigo), a.cod_padre
        FROM cargos_areasorganizacion ca
        LEFT JOIN areas a ON a.codigo = ca.cod_areaorganizacion
        WHERE a.codigo IS NOT NULL AND ca.cod_cargo = :cod_cargo";
$stmt = $dbh->prepare($sql);
$stmt->bindParam(':cod_cargo', $cod_cargo);
$stmt->execute();
$resultado = $stmt->fetch(PDO::FETCH_ASSOC);
if ($resultado) {
  $cod_area       = $resultado['codigo'];
  $cod_area_padre = $resultado['cod_padre'];
  $cod_area = ($cod_area == $cod_direccion || $cod_area_padre == $cod_direccion)
              ? $cod_area
              : buscarArea($cod_area_padre);
} else {
  $cod_area = 0;
}

// Verificación de numero de versión del Manual
$sql = "SELECT nro_version FROM manuales_aprobacion WHERE cod_cargo = :cod_cargo AND estado = 2 ORDER BY codigo DESC LIMIT 1";
$stmt = $dbh->prepare($sql);
$stmt->bindParam(':cod_cargo', $cod_cargo);
$stmt->execute();
$resultado = $stmt->fetch(PDO::FETCH_ASSOC);
if ($resultado) {
  $nro_version = $resultado['nro_version'] + 1;
} else {
  $nro_version = 1;
}

try {
  // Registro
  $sql  = "INSERT INTO manuales_aprobacion (cod_etapa,cod_cargo,nro_version,fecha_inicio) VALUES (:cod_etapa,:cod_cargo,:nro_version,:fecha_inicio)";
  $stmt = $dbh->prepare($sql);
  $stmt->bindParam(':cod_etapa', $cod_etapa);
  $stmt->bindParam(':cod_cargo', $cod_cargo);
  $stmt->bindParam(':nro_version', $nro_version);
  $stmt->bindParam(':fecha_inicio', $fecha_inicio);
  $stmt->execute();
	echo json_encode(array(
		'message' => 'Se inicializó la aprobación de manual correctamente.',
		'status'  => true
	));
} catch (Exception $e) {
    echo json_encode(array(
    	'message' => 'Ocurrió un error al guardar los datos.',
        'status'  => false
    ));
}

?>