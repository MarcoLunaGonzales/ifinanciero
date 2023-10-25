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
  // $sql = "SELECT DISTINCT(a.codigo), a.cod_padre
  //         FROM cargos_areasorganizacion ca
  //         LEFT JOIN areas a ON a.codigo = ca.cod_areaorganizacion
  //         WHERE a.codigo IS NOT NULL AND ca.cod_areaorganizacion = :cod_area";
  $sql = "SELECT a.codigo, a.cod_padre
          FROM areas a
          WHERE a.codigo = '$cod_area'";
  $stmt = $dbh->prepare($sql);
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

/**
 * Registro de Seguimiento de Manual de Aprobación
 */
function guardarSeguimiento($cod_manual, $cod_etapa, $cod_personal, $cod_seguimiento_estado, $fecha, $observacion, $detalle_descriptivo)
{
    $dbh = new Conexion();
    $sql = "INSERT INTO manuales_aprobacion_seguimiento (cod_manual,cod_etapa,cod_personal,cod_seguimiento_estado,fecha,observacion,detalle_descriptivo) VALUES (:cod_manual,:cod_etapa,:cod_personal,:cod_seguimiento_estado,:fecha,:observacion,:detalle_descriptivo)";
    $stmt = $dbh->prepare($sql);
    $stmt->bindParam(':cod_manual', $cod_manual);
    $stmt->bindParam(':cod_etapa', $cod_etapa);
    $stmt->bindParam(':cod_personal', $cod_personal);
    $stmt->bindParam(':cod_seguimiento_estado', $cod_seguimiento_estado);
    $stmt->bindParam(':fecha', $fecha);
    $stmt->bindParam(':observacion', $observacion);
    $stmt->bindParam(':detalle_descriptivo', $detalle_descriptivo);
    $stmt->execute();
}

try {
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
  $sql = "SELECT nro_version FROM manuales_aprobacion WHERE cod_cargo = :cod_cargo AND (cod_estado = 2 OR cod_estado = 3) ORDER BY codigo DESC LIMIT 1";
  $stmt = $dbh->prepare($sql);
  $stmt->bindParam(':cod_cargo', $cod_cargo);
  $stmt->execute();
  $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
  if ($resultado) {
    $nro_version = $resultado['nro_version'] + 1;
  } else {
    $nro_version = 1;
  }
  // Registro
  $sql  = "INSERT INTO manuales_aprobacion (cod_etapa,cod_cargo,cod_area,nro_version,fecha_inicio) VALUES (:cod_etapa,:cod_cargo,:cod_area,:nro_version,:fecha_inicio)";
  $stmt = $dbh->prepare($sql);
  $stmt->bindParam(':cod_etapa', $cod_etapa);
  $stmt->bindParam(':cod_cargo', $cod_cargo);
  $stmt->bindParam(':cod_area', $cod_area);
  $stmt->bindParam(':nro_version', $nro_version);
  $stmt->bindParam(':fecha_inicio', $fecha_inicio);
  $stmt->execute();

  // Seguimiento
  $cod_manual_aprobacion = $dbh->lastInsertId();
  $actual_cod_etapa      = 0;
  $cod_personal          = empty($_SESSION["globalUser"]) ? $_GET['q'] : $_SESSION["globalUser"];
  $manual_estado         = 0;
  $fecha                 = date('Y-m-d H:i:s');
  $manual_observacion    = "";
  $detalle_descriptivo   = "";

  guardarSeguimiento($cod_manual_aprobacion, $actual_cod_etapa, $cod_personal, $manual_estado, $fecha, $manual_observacion, $detalle_descriptivo);

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