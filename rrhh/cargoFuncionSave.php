<?php
require_once '../conexion.php';
require_once '../functions.php';

$result=0;
$dbhU = new Conexion();

$sqlX="SET NAMES 'utf8'";
$stmtX = $dbhU->prepare($sqlX);
$stmtX->execute();

//RECIBIMOS LAS VARIABLES
$cod_cargo 		= $_POST['cod_cargo'];
$cod_funcion	= $_POST['cod_funcion'];
$nombre_funcion = trim(str_replace("\n", '', $_POST['nombre_funcion']));
$orden			= $_POST['orden'];
$cod_estadoreferencial=$_POST['cod_estadoreferencial'];

if($cod_estadoreferencial==1){//insertar
	$sql="INSERT INTO cargos_funciones(cod_cargo,nombre_funcion,orden,cod_estado) values($cod_cargo,'$nombre_funcion',$orden,$cod_estadoreferencial) ";	
	$stmtU = $dbhU->prepare($sql);
	$flagsucces=$stmtU->execute();

	
}elseif($cod_estadoreferencial==2){//actualizar
	$sql="UPDATE cargos_funciones set nombre_funcion='$nombre_funcion',orden=$orden where cod_funcion=$cod_funcion";
	$stmtU = $dbhU->prepare($sql);
	$flagsucces=$stmtU->execute();
}elseif ($cod_estadoreferencial==3) {//eliminar
	$sql="UPDATE cargos_funciones set cod_estado=2 where cod_funcion=$cod_funcion";
	$stmtU = $dbhU->prepare($sql);
	$flagsucces=$stmtU->execute();	
}

if($flagsucces){
      $result =1;
 }
echo $result;
$dbhU=null;

?>
