<?php

require_once '../layouts/bodylogin.php';
require_once '../conexion.php';
require_once '../functionsGeneral.php';
require_once 'configModule.php';

$dbh = new Conexion();

$monto=$_POST["monto"];
$codPersona=$_POST["personal"];
$codMes=$_POST["codMes"];
$codGestion=$_POST["codGestion"];
$codEstado="1";

// Prepare
$stmt = $dbh->prepare("INSERT INTO anticipos_personal ( cod_personal,cod_gestion,cod_mes,monto, cod_estadoreferencial) 
                        VALUES (:codPersona,:codGestion,:codMes,:monto, :cod_estado)");
// Bind
$stmt->bindParam(':monto', $monto);
$stmt->bindParam(':cod_estado', $codEstado);
$stmt->bindParam(':codPersona',$codPersona);
$stmt->bindParam(':codGestion',$codGestion);
$stmt->bindParam(':codMes',$codMes);

$flagSuccess=$stmt->execute();
showAlertSuccessError($flagSuccess,"../".$urlListMesPersona."&cod_mes=".$codMes);

?>
