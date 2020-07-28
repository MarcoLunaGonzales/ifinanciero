<?php
error_reporting(E_ALL);
require_once '../layouts/bodylogin2.php';
require_once '../conexion.php';
require_once '../styles.php';
require_once '../functions.php';
require_once '../functionsGeneral.php';

$dbh = new Conexion();

$codigoLibreta=4021;

//echo $codigoLibreta;

$saldo=obtenerSaldoLibretaBancariaDetalle($codigoLibreta);

echo $saldo;
?>