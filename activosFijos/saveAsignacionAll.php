<?php
require_once '../conexion.php';

require_once '../functions.php';
require_once '../functionsGeneral.php';
require_once '../layouts/bodylogin2.php';
require_once '../perspectivas/configModule.php';

$result=0;


$dbh = new Conexion();
$sqlX="SET NAMES 'utf8'";
$stmtX = $dbh->prepare($sqlX);
$stmtX->execute();

//RECIBIMOS LAS VARIABLES
$cod_personal=$_POST['cod_personal'];
$cantidad_items=$_POST['cantidad_items'];



 // echo "cod_personal ".$cod_personal;
 // echo "items ".$cantidad_items;

$stmt = $dbh->prepare("SELECT cod_activosfijos,cod_personal
 FROM activofijos_asignaciones
  where cod_estadoasignacionaf=2 and cod_personal=:cod_personal ");
// Bind
$stmt->bindParam(':cod_personal', $cod_personal);
$stmt->execute();
$stmt->bindColumn('cod_activosfijos', $cod_activosfijos);
$stmt->bindColumn('cod_personal', $cod_personal);

$cont=1;
$cod_estadoasignacionaf=5;
$fecha_devolucion=date("Y-m-d H:i:s");

while ($row = $stmt->fetch(PDO::FETCH_BOUND)) { 
	$observacion=$_POST["observacionD".$cont];

	$stmtU = $dbh->prepare("UPDATE activofijos_asignaciones 
	set cod_estadoasignacionaf=:cod_estadoasignacionaf,observaciones_devolucion=:observacion,fecha_devolucion=:fecha_devolucion
	where cod_activosfijos=:cod_af and cod_personal = :cod_personal");
	// Bind
	$stmtU->bindParam(':cod_af', $cod_activosfijos);
	$stmtU->bindParam(':cod_personal', $cod_personal);
	$stmtU->bindParam(':cod_estadoasignacionaf', $cod_estadoasignacionaf);
	$stmtU->bindParam(':fecha_devolucion', $fecha_devolucion);
	$stmtU->bindParam(':observacion', $observacion);
	$stmtU->execute();
	$cont=$cont+1;

}

$a=true;
$b="../index.php?opcion=afEnCustodia";
showAlertSuccessError($a,$b);


?>
