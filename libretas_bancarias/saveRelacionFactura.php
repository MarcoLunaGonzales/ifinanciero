<?php
require_once '../conexion.php';
require_once '../functions.php';
require_once 'configModule.php';

$dbh = new Conexion();

//RECIBIMOS LAS VARIABLES
$cod_factura=$_POST['cod_factura'];
$cod_libretabancariadetalle=$_POST['cod_libretabancariadetalle'];
// Prepare
$stmt = $dbh->prepare("UPDATE facturas_venta set cod_libretabancariadetalle=$cod_libretabancariadetalle where codigo=$cod_factura");
$flagSuccess=$stmt->execute();
if($flagSuccess==true){
	echo 1;
}else{
	echo 2;
}
?>

