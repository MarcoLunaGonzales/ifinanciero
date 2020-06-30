<?php
require_once '../conexion.php';
require_once '../functions.php';
require_once 'configModule.php';

$dbh = new Conexion();

//RECIBIMOS LAS VARIABLES
$codigo=$_POST['codigo'];
// Prepare
$stmt = $dbh->prepare("UPDATE facturas_venta set cod_libretabancariadetalle=null where codigo=$codigo");
$flagSuccess=$stmt->execute();
if($flagSuccess==true){
	echo 1;
}else{
	echo 2;
}
?>

