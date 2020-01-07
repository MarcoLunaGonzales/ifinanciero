<?php

require_once '../layouts/bodylogin.php';
require_once '../conexion.php';
require_once '../functionsGeneral.php';
require_once 'configModule.php';

$dbh = new Conexion();

$monto=$_POST["monto"];
$monto_iva=$_POST["monto_iva"];
$codPersona=$_POST["personal"];
$codMes=$_POST["codMes"];
$codGestion=$_POST["codGestion"];
$codEstado="1";

$fechaActual= date("Y-m-d H:i:s");
// Prepare
$stmt = $dbh->prepare("INSERT INTO $table_rcivaPersonal ( cod_personal,cod_gestion,cod_mes,monto,monto_iva,fecha_registro, cod_estadoreferencial) 
                        VALUES (:codPersona,:codGestion,:codMes,:monto,:monto_iva,:fecha_registro, :cod_estado)");
// Bind
$stmt->bindParam(':monto', $monto);
$stmt->bindParam(':cod_estado', $codEstado);
$stmt->bindParam(':codPersona',$codPersona);
$stmt->bindParam(':codGestion',$codGestion);
$stmt->bindParam(':monto_iva',$monto_iva);
$stmt->bindParam(':fecha_registro',$fechaActual);
$stmt->bindParam(':codMes',$codMes);

$flagSuccess=$stmt->execute();
showAlertSuccessError($flagSuccess,"../".$urlListMesPersona."&cod_mes=".$codMes);
?>
