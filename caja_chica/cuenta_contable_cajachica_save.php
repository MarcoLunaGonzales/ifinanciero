<?php
require_once '../conexion.php';
require_once '../functions.php';

$result=0;
$dbhU = new Conexion();

$sqlX="SET NAMES 'utf8'";
$stmtX = $dbhU->prepare($sqlX);
$stmtX->execute();

//RECIBIMOS LAS VARIABLES
$cod_tipocajachica=$_POST['cod_tipocajachica'];
$cod_cuenta=$_POST['cod_cuenta'];

if($cod_cuenta!=0 || $cod_cuenta!=null){
	//borramos la cuenta asociada actual
	$sqlDelete="DELETE from configuraciones_cuentas_cajachica where cod_tipo_cajachica=$cod_tipocajachica";
	$stmtDelete = $dbhU->prepare($sqlDelete);
	$flagsucces=$stmtDelete->execute();
	if($flagsucces){
		$sql="INSERT INTO configuraciones_cuentas_cajachica(cod_cuenta,cod_tipo_cajachica) values('$cod_cuenta','$cod_tipocajachica')";
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
