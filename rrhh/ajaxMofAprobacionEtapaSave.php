<?php
require_once '../conexion.php';

date_default_timezone_set('America/La_Paz');
session_start();

$dbh = new Conexion();
$metodo      = $_POST['metodo'];
$cod_etapa   = $_POST['cod_etapa'];
$cod_cargo   = $_POST['cod_cargo'];
$nombre      = $_POST['nombre'];
$descripcion = $_POST['descripcion'];
$nro_etapa   = $_POST['nro_etapa'];
// Codigo para actualización
$codigo      = $_POST['codigo'];

try {
  if($metodo == 1){
    // Registro
    $sql  = "INSERT INTO mof_aprobacion_etapas (cod_etapa,cod_cargo,nombre,descripcion,nro_etapa) VALUES (:cod_etapa,:cod_cargo,:nombre,:descripcion,:nro_etapa)";
    $stmt = $dbh->prepare($sql);
    $stmt->bindParam(':cod_etapa', $cod_etapa);
    $stmt->bindParam(':cod_cargo', $cod_cargo);
    $stmt->bindParam(':nombre', $nombre);
    $stmt->bindParam(':descripcion', $descripcion);
    $stmt->bindParam(':nro_etapa', $nro_etapa);
    $stmt->execute();
    $message = 'Se registro exitosamente.';
  }else{
    // Actualización
    $sql = "UPDATE mof_aprobacion_etapas SET cod_etapa = :cod_etapa, cod_cargo = :cod_cargo, nombre = :nombre, descripcion = :descripcion, nro_etapa = :nro_etapa WHERE codigo = :codigo";
    $stmt = $dbh->prepare($sql);
    $stmt->bindParam(':cod_etapa', $cod_etapa);
    $stmt->bindParam(':cod_cargo', $cod_cargo);
    $stmt->bindParam(':nombre', $nombre);
    $stmt->bindParam(':descripcion', $descripcion);
    $stmt->bindParam(':nro_etapa', $nro_etapa);
    $stmt->bindParam(':codigo', $codigo); // Cambia esto al valor adecuado para identificar el registro que deseas actualizar
    $stmt->execute();
    $message = 'Se actualizó exitosamente.';
  }
	echo json_encode(array(
		'message' => $message,
		'status'  => true
	));
} catch (Exception $e) {
    echo json_encode(array(
    	'message' => 'Ocurrió un error al guardar los datos.',
        'status'  => false
    ));
}

?>