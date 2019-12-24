<?php
require_once '../conexion.php';

require_once '../functions.php';
require_once '../rrhh/configModule.php';

$result=0;


$dbhU = new Conexion();

$sqlX="SET NAMES 'utf8'";
$stmtX = $dbhU->prepare($sqlX);
$stmtX->execute();

//RECIBIMOS LAS VARIABLES
$cod_planilla=$_POST['cod_planilla'];
$cod_estadoplanilla=$_POST['sw'];
$sw=$_POST['sw'];


//echo "llega ".$cod_estadoasignacionaf;

if($sw==2){//procesar	
	// Prepare
	$stmtU = $dbhU->prepare("UPDATE planillas 
	set cod_estadoplanilla=:cod_estadoplanilla
	where codigo=:cod_planilla");
	// Bind
	$stmtU->bindParam(':cod_planilla', $cod_planilla);
	$stmtU->bindParam(':cod_estadoplanilla', $cod_estadoplanilla);	

}elseif($sw==3){
	//cuando se rechaza devolucion AF
	// Prepare
	$stmtU = $dbhU->prepare("UPDATE planillas 
	set cod_estadoplanilla=:cod_estadoplanilla
	where codigo=:cod_planilla");
	// Bind
	$stmtU->bindParam(':cod_planilla', $cod_planilla);
	$stmtU->bindParam(':cod_estadoplanilla', $cod_estadoplanilla);	
	
//$stmtU->execute();
}


if($stmtU->execute()){
      $result =1;
    }
echo $result;
$dbhU=null;

?>