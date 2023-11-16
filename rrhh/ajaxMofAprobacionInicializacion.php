<?php
require_once '../conexion.php';

date_default_timezone_set('America/La_Paz');
session_start();

try {
  $dbh = new Conexion();
  // Variables
  $cod_etapa    = 1;                  // Etapa inicial
  $fecha_inicio = date('Y-m-d H:i:s');
  $cod_mof      = $_POST['cod_mof'];

  // Verificación de numero de versión del Manual
  $sql = "SELECT nro_version FROM mof_aprobacion WHERE cod_mof = :cod_mof AND (cod_estado = 2 OR cod_estado = 3) ORDER BY codigo DESC LIMIT 1";
  $stmt = $dbh->prepare($sql);
  $stmt->bindParam(':cod_mof', $cod_mof);
  $stmt->execute();
  $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
  if ($resultado) {
    $nro_version = $resultado['nro_version'] + 1;
  } else {
    $nro_version = 1;
  }
  // Registro
  $sql  = "INSERT INTO mof_aprobacion (cod_etapa,cod_mof,nro_version,fecha_inicio) VALUES (:cod_etapa,:cod_mof,:nro_version,:fecha_inicio)";
  $stmt = $dbh->prepare($sql);
  $stmt->bindParam(':cod_etapa', $cod_etapa);
  $stmt->bindParam(':cod_mof', $cod_mof);
  $stmt->bindParam(':nro_version', $nro_version);
  $stmt->bindParam(':fecha_inicio', $fecha_inicio);
  $stmt->execute();
  
  // Seguimiento
  $cod_mof                = $dbh->lastInsertId();
  $cod_etapa              = 0;
  $cod_personal           = empty($_SESSION["globalUser"]) ? $_GET['q'] : $_SESSION["globalUser"];
  $cod_seguimiento_estado = 0;
  $fecha                  = date('Y-m-d H:i:s');
  $observacion            = "";
  $detalle_descriptivo    = "";
  $sql = "INSERT INTO mof_aprobacion_seguimiento (cod_mof,cod_etapa,cod_personal,cod_seguimiento_estado,fecha,observacion,detalle_descriptivo) 
          VALUES ($cod_mof, $cod_etapa, $cod_personal, $cod_seguimiento_estado, '$fecha', '$observacion', '$detalle_descriptivo')";
//   echo $sql;
//   exit;
  $stmt = $dbh->prepare($sql);
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