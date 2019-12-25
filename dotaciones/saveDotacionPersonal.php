<?php

require_once '../layouts/bodylogin.php';
require_once '../conexion.php';
require_once '../functionsGeneral.php';
require_once '../functions.php';
require_once 'configModule.php';

$dbh = new Conexion();


$codPersona=$_POST["personal"];
$monto=$_POST["monto"];
$codDotacion=$_POST["codDotacion"];
$codGestion=$_POST["codGestion"];
$codEstado="1";

// Prepare
$stmt = $dbh->prepare("INSERT INTO $table_dotaciones_personal (cod_dotacion, cod_personal,monto,cod_estadoreferencial) VALUES (:cod_dotacion,:cod_personal,:monto,:cod_estado)");
// Bind
$stmt->bindParam(':cod_dotacion', $codDotacion);
$stmt->bindParam(':cod_personal', $codPersona);
$stmt->bindParam(':monto', $monto);
$stmt->bindParam(':cod_estado', $codEstado);

$flagSuccess=$stmt->execute();


$stmtb = $dbh->prepare("select dp.codigo as codDotPer,dp.cod_dotacion as codDotacion,dp.cod_personal as codPersonal,dp.monto as monto,d.nro_meses as nroMeses,d.fecha_inicio as fecha
from dotaciones_personal dp,dotaciones d where d.codigo=dp.cod_dotacion and d.cod_estadoreferencial=1 and dp.cod_estadoreferencial=1 and dp.cod_dotacion=$codDotacion and dp.cod_personal=$codPersona");
$stmtb->execute();
$stmtb->bindColumn('codDotPer', $codigoDotacionPersona);
$stmtb->bindColumn('codDotacion', $codDotacion);
$stmtb->bindColumn('codPersonal', $codPersonal);
$stmtb->bindColumn('monto', $monto);
$stmtb->bindColumn('nroMeses', $nroMeses);
$stmtb->bindColumn('fecha', $fecha);

while ($row = $stmtb->fetch(PDO::FETCH_BOUND)) {
  insertarDotacionPersonaMes($codigoDotacionPersona,$monto,$nroMeses, $fecha,$codGestion);
}






showAlertSuccessError($flagSuccess,"../".$urlListDotacionPersonal."&cod_dot=".$codDotacion);

?>
