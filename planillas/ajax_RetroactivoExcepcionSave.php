<?php
require_once '../conexion.php';

date_default_timezone_set('America/La_Paz');
session_start();

$dbh = new Conexion();
$metodo          = $_POST['metodo'];
$cod_planilla    = $_POST['cod_planilla'];
$cod_personal    = $_POST['cod_personal'];
$haber_basico    = $_POST['haber_basico'];
$bono_antiguedad = $_POST['bono_antiguedad'];
$otros_bonos     = $_POST['otros_bonos'];
// Codigo para actualización
$codigo          = $_POST['codigo'];

try {
  if($metodo == 1){
    // Registro
    $sql  = "INSERT INTO planillas_retroactivos_excepciones (cod_planilla, cod_personal, haber_basico, bono_antiguedad, otros_bonos) 
          VALUES (:cod_planilla, :cod_personal, :haber_basico, :bono_antiguedad, :otros_bonos)";
    $stmt = $dbh->prepare($sql);
    $stmt->bindParam(':cod_planilla', $cod_planilla);
    $stmt->bindParam(':cod_personal', $cod_personal);
    $stmt->bindParam(':haber_basico', $haber_basico);
    $stmt->bindParam(':bono_antiguedad', $bono_antiguedad);
    $stmt->bindParam(':otros_bonos', $otros_bonos);
    $stmt->execute();
    $message = 'Se registro exitosamente.';
  }else{
    // Actualización
    $sql = "UPDATE planillas_retroactivos_excepciones 
            SET haber_basico = :haber_basico,
                bono_antiguedad = :bono_antiguedad,
                otros_bonos = :otros_bonos
            WHERE cod_planilla = :cod_planilla 
            AND cod_personal = :cod_personal";
    $stmt = $dbh->prepare($sql);
    $stmt->bindParam(':cod_planilla', $cod_planilla);
    $stmt->bindParam(':cod_personal', $cod_personal);
    $stmt->bindParam(':haber_basico', $haber_basico);
    $stmt->bindParam(':bono_antiguedad', $bono_antiguedad);
    $stmt->bindParam(':otros_bonos', $otros_bonos);
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