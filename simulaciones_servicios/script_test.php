<?php
require_once '../functions.php';


$codigo_libreta_det=4021;
$monto_libreta_x=obtenerSaldoLibretaBancariaDetalle($codigo_libreta_det);

echo $monto_libreta_x;
?>