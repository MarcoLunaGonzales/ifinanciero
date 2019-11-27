<?php

//require_once '../layouts/bodylogin.php';
require_once 'conexion.php';
require_once 'functions.php';
require_once 'configModule.php';

$dbh = new Conexion();

//la ufv tengo q obtener de la funcion 

//TENGO Q AVERIGUAR EL PRIMER Y ULTIMO DIA DEL MES
//$fecha = '2010-02-04';
$fecha = $_POST["gestion"].'-'.$_POST["mes"].'-01';//ARMO UNA FECHA

// First day of the month.
$fecha_primerdia = date('Y-m-01', strtotime($fecha));
// Last day of the month.
$fecha_ultimodia = date('Y-m-t', strtotime($fecha));


//$cod_empresa=$_POST["cod_empresa"];
$mes=$_POST["mes"];
$gestion=$_POST["gestion"];
//$ufvinicio=$_POST["ufvinicio"];
$ufvinicio=obtenerUFV($fecha_primerdia);
//$ufvfinal=$_POST["ufvfinal"];
$ufvfinal=obtenerUFV($fecha_ultimodia);
$estado=1;
//Prepare
$stmt = $dbh->prepare("call crear_depreciacion_mensual(:mes, :gestion, :ufvinicio, :ufvfinal)");

//$stmt = $dbh->prepare("INSERT INTO depreciaciones(cod_empresa,nombre,vida_util,coeficiente,deprecia,actualiza,cod_estado) 
//values (:cod_empresa, :nombre, :vida_util, :coeficiente, :deprecia, :actualiza, :cod_estado);");
//Bind
//$stmt->bindParam(':codigo', $codigo);
$stmt->bindParam(':mes', $mes);
$stmt->bindParam(':gestion', $gestion);
$stmt->bindParam(':ufvinicio', $ufvinicio);
$stmt->bindParam(':ufvfinal', $ufvfinal);

$flagSuccess=$stmt->execute();
//$tabla_id = $dbh->lastInsertId();;

showAlertSuccessError($flagSuccess,$urlList7);
?>
