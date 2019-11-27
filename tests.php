<?php
require_once 'conexion.php';
require_once 'functions.php';
require_once 'functionsGeneral.php';

$dbh = new Conexion();

$ufv=obtenerUFV("2019-01-01");

echo "UFV: ".$ufv;

?>