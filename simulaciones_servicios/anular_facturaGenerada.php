<?php
require_once 'layouts/bodylogin.php';
require_once 'conexion.php';
require_once 'functions.php';
require_once 'functionsGeneral.php';
require_once 'configModule.php';

$dbh = new Conexion();

//RECIBIMOS LAS VARIABLES
$codigo=$codigo;
// Prepare
$sql="UPDATE facturas_venta set cod_estadofactura='2' where codigo=$codigo";
echo $sql;
$stmt = $dbh->prepare($sql);

$flagSuccess=$stmt->execute();
showAlertSuccessError($flagSuccess,$urllistFacturasServicios);	

?>