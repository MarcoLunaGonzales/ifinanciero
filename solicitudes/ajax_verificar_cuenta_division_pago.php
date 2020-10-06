<?php
require_once '../conexion.php';
require_once '../functions.php';
$dbh = new Conexion();
$cuenta=$_POST['codigo'];
echo VerificarCuentaDivisionPago($cuenta); 
