<?php
session_start();
require_once '../conexion.php';
require_once '../functions.php';
require_once '../styles.php';

$saldo=$_GET['saldo'];
$razon_social=$_GET['razon_social'];
?>
<center><b><span><?=$razon_social?>, Monto: <?=number_format($saldo,2)?> Bs.</span></b></center>