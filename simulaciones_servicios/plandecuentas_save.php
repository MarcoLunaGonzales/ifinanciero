<?php
require_once '../conexion.php';
require_once '../functions.php';

$result=0;
$dbhU = new Conexion();

$sqlX="SET NAMES 'utf8'";
$stmtX = $dbhU->prepare($sqlX);
$stmtX->execute();

//RECIBIMOS LAS VARIABLES
$cod_tipopago=$_POST['cod_tipopago'];
$cod_cuenta=$_POST['cod_cuenta'];

if($cod_cuenta!=0 || $cod_cuenta!=null){
	//borramos la cuenta asociada actual
	$sqlDelete="DELETE from tipos_pago_contabilizacion where cod_tipopago=$cod_tipopago";
	$stmtDelete = $dbhU->prepare($sqlDelete);
	$flagsucces=$stmtDelete->execute();
	if($flagsucces){
		$sql="INSERT INTO tipos_pago_contabilizacion(cod_tipopago,cod_cuenta) values('$cod_tipopago','$cod_cuenta')";
		$stmt = $dbhU->prepare($sql);
		$flagsucces=$stmt->execute();
	}
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
