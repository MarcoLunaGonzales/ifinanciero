<?php
require_once 'conexion.php';
require_once 'functions.php';
require_once 'configModule.php';

$dbh = new Conexion();

//RECIBIMOS LAS VARIABLES
$codigo=$_GET['codigo'];
$codLibreta=$_GET['c'];
// Prepare
$stmt = $dbh->prepare("UPDATE libretas_bancariasdetalle SET cod_estadoreferencial=2 where codigo=:codigo;UPDATE facturas_venta SET cod_libretabancariadetalle=0 where cod_libretabancariadetalle =:codigo;");
// Bind
$stmt->bindParam(':codigo', $codigo);
$flagSuccess=$stmt->execute();
if($flagSuccess==true){
	showAlertSuccessError(true,$urlList2."&codigo=".$codLibreta);	
}else{
	showAlertSuccessError(false,$urlList2."&codigo=".$codLibreta);
}
?>