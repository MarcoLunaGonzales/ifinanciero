<?php
require_once 'conexion.php';
require_once 'styles.php';

$dbh = new Conexion();

//RECIBIMOS LAS VARIABLES
$cod_planilla = $_GET['cod_planilla'];
$cod_personal = $_GET['cod_personal'];

// Prepare
$sql = "DELETE FROM planillas_retroactivos_excepciones 
        WHERE cod_planilla = :cod_planilla 
          AND cod_personal = :cod_personal";

$stmt = $dbh->prepare($sql);
$stmt->bindParam(':cod_planilla', $cod_planilla);
$stmt->bindParam(':cod_personal', $cod_personal);
$stmt->execute();

showAlertSuccessError($stmt, "index.php?opcion=planillasRetroactivosExcepcionesList&cod_planilla=$cod_planilla");

?>