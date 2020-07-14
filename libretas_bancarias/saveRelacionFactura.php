<?php
require_once '../conexion.php';
require_once '../functions.php';
require_once 'configModule.php';

$dbh = new Conexion();

//RECIBIMOS LAS VARIABLES
$cod_factura=$_POST['cod_factura'];
$cod_libretabancariadetalle=$_POST['cod_libretabancariadetalle'];
// Prepare
$stmt = $dbh->prepare("INSERT into libretas_bancariasdetalle_facturas (cod_libretabancariadetalle,cod_facturaventa) values($cod_libretabancariadetalle,$cod_facturaventa)");
$flagSuccess=$stmt->execute();
if($flagSuccess==true){
	echo 1;
}else{
	echo 2;
}
?>

