<?php
require_once '../conexion.php';
require_once '../functions.php';
require_once '../functionsGeneral.php';
require_once 'configModule.php';

$dbh = new Conexion();

//RECIBIMOS LAS VARIABLES
$cod_solicitudfacturacion=$_POST['cod_solicitudfacturacion'];
$observaciones=$_POST['observaciones'];
// Prepare
$stmt = $dbh->prepare("UPDATE solicitudes_facturacion set obs_devolucion='$observaciones' where codigo=$cod_solicitudfacturacion");
$flagSuccess=$stmt->execute();
if($flagSuccess){
    echo 1;
}else echo 2;


?>