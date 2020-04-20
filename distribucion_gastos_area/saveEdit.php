<?php

require_once '../layouts/bodylogin.php';
require_once '../conexion.php';
require_once '../functionsGeneral.php';
require_once 'configModule.php';

$dbh = new Conexion();

$codigoEscalaAntiguedad=$_POST["codigo"];
$nombre=$_POST["nombre"];
$aniosInicio=$_POST["anios_inicio"];
$aniosFin=$_POST["anios_fin"];
$porcentaje=$_POST["porcentaje"];
$codEstado="1";

// Prepare
$stmt = $dbh->prepare("UPDATE $table_escalaAntiguedad set nombre=:nombre, anios_inicio=:anios_inicio, anios_final=:anios_fin,porcentaje=:porcentaje
                     where codigo=:codigo AND cod_estadoreferencial=$codEstado");
// Bind
$stmt->bindParam(':codigo', $codigoEscalaAntiguedad);
$stmt->bindParam(':nombre', $nombre);
$stmt->bindParam(':anios_inicio', $aniosInicio);
$stmt->bindParam(':anios_fin', $aniosFin);
$stmt->bindParam(':porcentaje', $porcentaje);

$flagSuccess=$stmt->execute();
showAlertSuccessError($flagSuccess,$urlList2);

?>