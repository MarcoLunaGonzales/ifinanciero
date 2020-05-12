<?php
require_once '../conexion.php';
require_once '../functions.php';

$result=0;
$dbhU = new Conexion();

$sqlX="SET NAMES 'utf8'";
$stmtX = $dbhU->prepare($sqlX);
$stmtX->execute();

//RECIBIMOS LAS VARIABLES
$cod_area=$_POST['cod_area'];
$cod_cuenta=$_POST['cod_cuenta'];

if($cod_cuenta!=0 || $cod_cuenta!=null){
	//borramos la cuenta asociada actual
	$sqlDelete="UPDATE areas set cod_cuenta_ingreso=$cod_cuenta where codigo=$cod_area";
	$stmtDelete = $dbhU->prepare($sqlDelete);
	$flagsucces=$stmtDelete->execute();	
}else{
	$flagsucces=false;
	$result=2;
}


if($flagsucces){
    $result =1;
}
echo $result;
$dbhU=null;

?>
