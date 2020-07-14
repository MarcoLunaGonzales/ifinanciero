
<?php
require_once '../conexion.php';
require_once '../functions.php';
require_once 'configModule.php';

$dbh = new Conexion();

//RECIBIMOS LAS VARIABLES
$cod_factura=$_POST['codigo'];
$cod_libretabancariadetalle=$_POST['codigo_libdet'];

// Prepare
$stmt = $dbh->prepare("DELETE from libretas_bancariasdetalle_facturas where cod_libretabancariadetalle=$cod_libretabancariadetalle and cod_facturaventa=$cod_factura");
$flagSuccess=$stmt->execute();
if($flagSuccess==true){
	echo 1;
}else{
	echo 2;
}
?>

