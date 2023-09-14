<?php
require_once '../conexion.php';

date_default_timezone_set('America/La_Paz');
session_start();

$dbh = new Conexion();
$cod_mof_aprobacion = $_POST['cod_mof_aprobacion'];

try {
  // Verificación de numero de versión del Manual
  $sql = "SELECT ase.nombre as estado, DATE_FORMAT(mas.fecha,'%d-%m-%Y %H:%i:%s') as fecha, CONCAT(p.primer_nombre, ' ', p.paterno, ' ', p.materno) as personal, mas.observacion, ma.nro_version
        FROM mof_aprobacion_seguimiento mas
        LEFT JOIN mof_aprobacion ma ON ma.codigo = mas.cod_mof
        LEFT JOIN personal p ON p.codigo = mas.cod_personal
        LEFT JOIN mof_aprobacion_seguimiento_estados ase ON ase.codigo = mas.cod_seguimiento_estado
        WHERE mas.cod_mof = :cod_mof_aprobacion
        ORDER BY mas.codigo DESC
        LIMIT 1";
  $stmt = $dbh->prepare($sql);
  $stmt->bindParam(':cod_mof_aprobacion', $cod_mof_aprobacion);
  $stmt->execute();
  $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
  if ($resultado) {
    $detalleArray = array(
      'verf_row'    => 1,
      'estado'      => $resultado['estado'],
      'fecha'       => $resultado['fecha'],
      'personal'    => $resultado['personal'],
      'observacion' => $resultado['observacion'],
      'nro_version' => $resultado['nro_version']
    );
  } else {
    $detalleArray = array(
      'verf_row'    => 0,
      'estado'      => '',
      'fecha'       => '',
      'personal'    => '',
      'observacion' => '',
      'nro_version' => ''
    );
  }
	echo json_encode(array(
    'data'    => $detalleArray,
		'status'  => true
	));
} catch (Exception $e) {
    echo json_encode(array(
      'status'  => false
    ));
}

?>