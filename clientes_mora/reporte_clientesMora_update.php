<?php

require_once '../conexion.php';
require_once '../functionsGeneral.php';
require_once '../layouts/bodylogin.php';
$dbh = new Conexion();
$codigo=$_GET['codigo'];
$sql_add=" ";
if($codigo<>0){
	$sql_add="where codigo=$codigo";
}
$sql="UPDATE clientes_mora set cod_estado=2 $sql_add";
// echo $sql;
$stmt = $dbh->prepare($sql);
$flagSuccess=$stmt->execute();
showAlertSuccessError($flagSuccess,"../clientes_mora/reporte_clientesMora.php");

?>