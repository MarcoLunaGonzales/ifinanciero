<?php
require_once '../conexion.php';
require_once '../functions.php';

try {
	$cod_cargo = $_GET['codigo'];
	$dbh = new Conexion();
	$sql = "SELECT ma.codigo AS cod_manual_aprobacion, ma.cod_cargo, ma.nro_version, ma.fecha_inicio
			FROM manuales_aprobacion ma
			WHERE ma.cod_cargo = '$cod_cargo'
			AND ma.cod_estado = 2
			ORDER BY ma.codigo DESC
			LIMIT 1";
	// echo $sql;

	$stmt = $dbh->prepare($sql);
	$stmt->execute();

	if ($stmt->rowCount()) {
		$result = $stmt->fetch();

        $urlIbnorca = obtenerValorConfiguracion(112);

		$cod_manual_aprobacion = $result['cod_manual_aprobacion'];
		$cod_cargo             = $result['cod_cargo'];
		$nro_version           = $result['nro_version'];
		$fecha_inicio          = $result['fecha_inicio'];
		
		// Arma nombre de Archivo
		$nombreArchivo = $cod_cargo . '_' . 
				$cod_manual_aprobacion . '_' . 
				$nro_version . '_' . 
				date('YmdHi', strtotime($fecha_inicio)).'.pdf';
		$carpetaDestino = dirname(__DIR__) . '/doc_manuales_aprobados';
		$rutaArchivo = $carpetaDestino . '/' . $nombreArchivo;
		// echo $rutaArchivo;
		// exit;

		// Verifica existencia de Archivo
		$existeArchivo = file_exists($rutaArchivo);
		if ($existeArchivo) {
			// echo "Location: " .  $urlIbnorca . "doc_manuales_aprobados/$nombreArchivo";
			header("Location: " .  $urlIbnorca . "doc_manuales_aprobados/$nombreArchivo");
			exit();
		} else {
			// echo "Location: " .  $urlIbnorca . "rrhh/pdfGeneracion.php?codigo=$cod_cargo";
			header("Location: " .  $urlIbnorca . "rrhh/pdfGeneracion.php?codigo=$cod_cargo");
			exit();
		}
		echo json_encode(array(
			'message' => 'Se encontró manual de cargo.',
			'status'  => true
		));
	} else {
		echo '
			<div style="background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; padding: 10px; margin-bottom: 15px;">
				<strong>Mensaje:</strong> No se generó ningún proceso de aprobación del manual de cargos seleccionado.
			</div>
		';
	
	}
} catch (Exception $e) {
    echo json_encode(array(
    	'message' => 'No se encontraron datos del archivo',
        'status'  => false
    ));
}

?>