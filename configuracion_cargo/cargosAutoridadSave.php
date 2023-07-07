<?php
require_once '../conexion.php';
require_once '../functions.php';

$result = 0;
$dbhU 	= new Conexion();

$sqlX="SET NAMES 'utf8'";
$stmtX = $dbhU->prepare($sqlX);
$stmtX->execute();

//RECIBIMOS LAS VARIABLES
$cod_cargo 				= $_POST['cod_cargo'];
$cod_autoridad 			= $_POST['cod_autoridad'];
$nombre_autoridad 		= $_POST['nombre_autoridad'];
$orden					= $_POST['orden'];
$cod_estadoreferencial 	= $_POST['cod_estadoreferencial']; // cod_estadoautoridad
// Cod configuracion
$cod_config_aprobacion=empty($_POST['config_aprob'])?'':$_POST['config_aprob'];

// PROCESO DE REGISTRO
if($cod_estadoreferencial==1){//insertar
	$sql="INSERT INTO cargos_autoridades(cod_cargo,nombre_autoridad,orden,cod_estadoautoridad,cod_configuracion) values($cod_cargo,'$nombre_autoridad',$orden,$cod_estadoreferencial,$cod_config_aprobacion) ";	
	$stmtU = $dbhU->prepare($sql);
	$flagsucces=$stmtU->execute();
}elseif($cod_estadoreferencial==2){//actualizar
	$sql="UPDATE cargos_autoridades set nombre_autoridad='$nombre_autoridad',orden=$orden where cod_autoridad=$cod_autoridad";
	$stmtU = $dbhU->prepare($sql);
	$flagsucces=$stmtU->execute();
}elseif ($cod_estadoreferencial==3) {//eliminar
	$sql="UPDATE cargos_autoridades set cod_estadoautoridad=2 where cod_autoridad=$cod_autoridad";
	$stmtU = $dbhU->prepare($sql);
	$flagsucces=$stmtU->execute();	
}

if($flagsucces){
      $result =1;
 }
echo $result;
$dbhU=null;

?>
