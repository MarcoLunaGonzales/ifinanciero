<?php
require_once 'conexion.php';
require_once 'functions.php';
require_once 'functionsGeneral.php';
require_once 'functionsDepreciacion.php';

$dbh = new Conexion();

$ufv=obtenerUFV("2019-01-01");
echo "UFV: ".$ufv;

$fecha1="2020-01-01";
$fecha2="2020-03-01";

$difMeses=diferenciaMeses($fecha1, $fecha2);
echo "la diferencia es: ".$difMeses;

?>

