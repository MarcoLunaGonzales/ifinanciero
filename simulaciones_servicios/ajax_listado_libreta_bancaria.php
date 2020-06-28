<?php
session_start();
require_once '../conexion.php';
require_once '../functions.php';
require_once '../styles.php';
require_once '../layouts/librerias.php';

$saldo=$_GET['saldo'];
?>
<input type="hidden" name="saldo_x" id="saldo_x" value="<?=$saldo?>">