<?php
require_once '../conexion.php';
require_once '../functionsLibretaBancaria.php';

$dbh = new Conexion();

$codigoLibreta=4636;

$codFacturaRaiz=buscarFacturaLibretaRaiz($codigoLibreta,'2018-01-01','2021-12-31');

echo "resultado: ".$codFacturaRaiz."<br>";


$saldoFactura=8190;
$saldoDeposito=14657.40;

$saldoBuscado=saldoLibretaBancariaDesdeRaiz($codFacturaRaiz,$saldoFactura,$saldoDeposito);

echo "codDepo: ".$saldoBuscado[0]." saldo: ".$saldoBuscado[1];

?>