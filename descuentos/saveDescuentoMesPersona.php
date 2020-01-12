<?php

require_once '../layouts/bodylogin.php';
require_once '../conexion.php';
require_once '../functionsGeneral.php';
require_once 'configModule.php';

$dbh = new Conexion();

$monto=$_POST["monto"];
$codPersona=$_POST["personal"];
$codDescuento=$_POST["codDescuento"];
$codMes=$_POST["codMes"];
$codGestion=$_POST["codGestion"];
$obs=$_POST["obs"];
$codEstado="1";

// Prepare
$stmt = $dbh->prepare("INSERT INTO descuentos_personal_mes (cod_descuento, cod_personal,cod_gestion,cod_mes,monto,observaciones, cod_estadoreferencial) 
                        VALUES (:cod_descuento,:codPersona,:codGestion,:codMes,:monto,:observaciones, :cod_estado)");
// Bind
$stmt->bindParam(':monto', $monto);
$stmt->bindParam(':cod_estado', $codEstado);
$stmt->bindParam(':cod_descuento',$codDescuento);
$stmt->bindParam(':codPersona',$codPersona);
$stmt->bindParam(':codGestion',$codGestion);
$stmt->bindParam(':codMes',$codMes);
$stmt->bindParam(':observaciones',$obs);

$flagSuccess=$stmt->execute();
showAlertSuccessError($flagSuccess,"../".$urlListMesPersona."&cod_mes=".$codMes."&cod_descuento=".$codDescuento);

?>
