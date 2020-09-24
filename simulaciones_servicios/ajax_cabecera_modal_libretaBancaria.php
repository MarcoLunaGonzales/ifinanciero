<?php
session_start();
require_once '../conexion.php';
require_once '../functions.php';
require_once '../styles.php';

$saldo=$_GET['saldo'];
$razon_social=$_GET['razon_social'];

/*if(strlen($razon_social)>24){
	$inicio=substr($razon_social,0,24);
	$fin=substr($razon_social,24);
	$razon_social=$inicio."<br>".$fin;
}*/
?>
<center><b><small><?=$razon_social?></small> <small>Monto: <?=number_format($saldo,2)?> Bs.</small></b></center>