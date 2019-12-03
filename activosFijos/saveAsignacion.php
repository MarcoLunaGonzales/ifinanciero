<?php
require_once '../conexion.php';

require_once '../functions.php';
require_once '../perspectivas/configModule.php';

$result=0;


$dbhU = new Conexion();

$sqlX="SET NAMES 'utf8'";
$stmtX = $dbhU->prepare($sqlX);
$stmtX->execute();

//RECIBIMOS LAS VARIABLES
$cod_af=$_POST['cod_af'];
$cod_personal=$_POST['cod_personal'];
$cod_estadoasignacionaf=$_POST['cod_estadoasignacionaf'];
$observacion=$_POST['observacion'];

//echo "llega ".$cod_estadoasignacionaf;

$fecha_recepcion=date("Y-m-d H:i:s");
if($cod_estadoasignacionaf==5){
	//cuando devuelve AF
	// Prepare
	$stmtU = $dbhU->prepare("UPDATE activofijos_asignaciones 
	set cod_estadoasignacionaf=:cod_estadoasignacionaf,observaciones_devolucion=:observacion,fecha_devolucion=:fecha_devolucion
	where cod_activosfijos=:cod_af and cod_personal = :cod_personal");
	// Bind
	$stmtU->bindParam(':cod_af', $cod_af);
	$stmtU->bindParam(':cod_personal', $cod_personal);
	$stmtU->bindParam(':cod_estadoasignacionaf', $cod_estadoasignacionaf);
	$stmtU->bindParam(':fecha_devolucion', $fecha_recepcion);
	$stmtU->bindParam(':observacion', $observacion);

}elseif($cod_estadoasignacionaf==6){
		//cuando se acepta devolucion de AF
		
		// Prepare
		$stmtU = $dbhU->prepare("UPDATE activofijos_asignaciones 
		set cod_estadoasignacionaf=4
		where cod_activosfijos=:cod_af and cod_personal = :cod_personal");
		// Bind
		$stmtU->bindParam(':cod_af', $cod_af);
		$stmtU->bindParam(':cod_personal', $cod_personal);
		

		
	//$stmtU->execute();

	}elseif($cod_estadoasignacionaf==7){
		//cuando se rechaza devolucion AF
		// Prepare
		$stmtU = $dbhU->prepare("UPDATE activofijos_asignaciones 
		set cod_estadoasignacionaf=2
		where cod_activosfijos=:cod_af and cod_personal = :cod_personal");
		// Bind
		$stmtU->bindParam(':cod_af', $cod_af);
		$stmtU->bindParam(':cod_personal', $cod_personal);
		
	//$stmtU->execute();

	}else{
		// Prepare
		$stmtU = $dbhU->prepare("UPDATE activofijos_asignaciones 
		set cod_estadoasignacionaf=:cod_estadoasignacionaf,observaciones_recepcion=:observacion,fecha_recepcion=:fecha_recepcion
		where cod_activosfijos=:cod_af and cod_personal = :cod_personal");
		// Bind
		$stmtU->bindParam(':cod_af', $cod_af);
		$stmtU->bindParam(':cod_personal', $cod_personal);
		$stmtU->bindParam(':cod_estadoasignacionaf', $cod_estadoasignacionaf);
		$stmtU->bindParam(':fecha_recepcion', $fecha_recepcion);
		$stmtU->bindParam(':observacion', $observacion);
		//$stmtU->execute();

}


if($stmtU->execute()){
      $result =1;
    }
echo $result;
$dbhU=null;

?>