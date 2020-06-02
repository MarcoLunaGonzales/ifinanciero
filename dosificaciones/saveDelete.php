<?php
require_once 'conexion.php';
require_once 'styles.php';
require_once 'configModule.php';
$dbh = new Conexion();

//RECIBIMOS LAS VARIABLES
$codigo=$codigo;
$sw=$sw;
$cod_sucursal=$cod_sucursal;

if($sw==1){//borrado
	// Prepare
	$stmt = $dbh->prepare("UPDATE dosificaciones_facturas set cod_estado=2 where codigo=$codigo");
	$flagSuccess=$stmt->execute();
	showAlertSuccessError($flagSuccess,$urlListDosificacion);
}else{//cambiar a activo
	$stmt = $dbh->prepare("UPDATE dosificaciones_facturas set cod_estado=0 where cod_estado=1 and cod_sucursal=$cod_sucursal");
	$flagSuccess=$stmt->execute();
	if($flagSuccess){
		$stmt = $dbh->prepare("UPDATE dosificaciones_facturas set cod_estado=1 where codigo=$codigo");
		$flagSuccess=$stmt->execute();
		showAlertSuccessError($flagSuccess,$urlListDosificacion);
	}
	
}

?>