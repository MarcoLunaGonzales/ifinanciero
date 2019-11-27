<?php

require_once '../layouts/bodylogin.php';
require_once '../conexion.php';
require_once '../functions.php';

$dbh = new Conexion();

$codigoIndicador=$_POST["cod_indicador"];

$table="actividades_poa";
$urlRedirect="../index.php?opcion=listActividadesPOA&codigo=$codigoIndicador";

session_start();

$orden="1";
$nombre=$_POST["actividad"];
$normaPriorizada=$_POST["norma_priorizada"];
$norma=$_POST["norma"];
$productoEsperado=$_POST["producto_esperado"];
$tipoDato=$_POST["tipo_dato"];
$codEstado="1";
$globalUser=$_SESSION["globalUser"];
$globalGestion=$_SESSION["globalGestion"];
$globalUnidad=$_SESSION["globalUnidad"];
$globalArea=$_SESSION["globalArea"];

$fechaHoraActual=date("Y-m-d H:i:s");

// Prepare
$stmt = $dbh->prepare("INSERT INTO $table (orden, nombre, cod_normapriorizada, cod_norma, cod_tipodatoactividad, producto_esperado, cod_indicador, cod_unidadorganizacional, cod_area, cod_estado, created_at, created_by) VALUES (:orden, :nombre, :cod_normapriorizada, :cod_norma, :cod_tipodatoactividad, :producto_esperado, :cod_indicador, :cod_unidadorganizacional, :cod_area, :cod_estado, :created_at, :created_by)");
// Bind
$stmt->bindParam(':orden', $orden);
$stmt->bindParam(':nombre', $nombre);
$stmt->bindParam(':cod_normapriorizada', $normaPriorizada);
$stmt->bindParam(':cod_norma', $norma);
$stmt->bindParam(':cod_tipodatoactividad', $tipoDato);
$stmt->bindParam(':producto_esperado', $productoEsperado);
$stmt->bindParam(':cod_indicador', $codigoIndicador);
$stmt->bindParam(':cod_unidadorganizacional', $globalUnidad);
$stmt->bindParam(':cod_area', $globalArea);
$stmt->bindParam(':cod_estado', $codEstado);
$stmt->bindParam(':created_at', $fechaHoraActual);
$stmt->bindParam(':created_by', $globalUser);

$flagSuccess=$stmt->execute();

if($flagSuccess==true){
	showAlertSuccessError(true,$urlRedirect);	
}else{
	showAlertSuccessError(false,$urlRedirect);
}


?>
