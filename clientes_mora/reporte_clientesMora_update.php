<?php

require_once '../conexion.php';
require_once '../functionsGeneral.php';
// require_once '../layouts/bodylogin.php';
$dbh = new Conexion();
$codigo=$_POST['codigo'];
$flagSuccess=false;
if($codigo>0){
	
	$sql="UPDATE clientes_mora set cod_estado=3 where codigo=$codigo";
	$stmt = $dbh->prepare($sql);
	$flagSuccess=$stmt->execute();
}

// echo $sql;


if($flagSuccess){
	echo 1;
}else{
	echo 0;
}
//showAlertSuccessError($flagSuccess,"../clientes_mora/reporte_clientesMora.php");

?>