<?php

require_once '../layouts/bodylogin.php';
require_once '../conexion.php';
require_once '../functionsGeneral.php';
require_once 'configModule.php';

$dbh = new Conexion();

$codPoliticaDescuento=$_POST["codigo"];
$nombre=$_POST["nombre"];
$minutosInicio=$_POST["minutos_inicio"];
$minutosFin=$_POST["minutos_final"];
$porcentaje=$_POST["porcentaje_diahaber"];
$codEstado="1";

// Prepare
$stmt = $dbh->prepare("UPDATE $table_politicaDescuento SET nombre=:nombre, minutos_inicio=:minutos_inicio, minutos_final=:minutos_final,porcentaje_diahaber=:porcentaje_diahaber
                     WHERE codigo=:codigo AND cod_estadoreferencial=$codEstado");
// Bind
$stmt->bindParam(':codigo', $codPoliticaDescuento);
$stmt->bindParam(':nombre', $nombre);
$stmt->bindParam(':minutos_inicio', $minutosInicio);
$stmt->bindParam(':minutos_final', $minutosFin);
$stmt->bindParam(':porcentaje_diahaber', $porcentaje);

$flagSuccess=$stmt->execute();
showAlertSuccessError($flagSuccess,$urlList2);

?>