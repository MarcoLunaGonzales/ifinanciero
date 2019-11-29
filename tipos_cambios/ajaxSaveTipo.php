<?php
session_start();
require_once '../conexion.php';
require_once '../functions.php';
require_once '../functionsGeneral.php';
require_once 'configModule.php';

$dbh = new Conexion();

$fecha=date("Y-m-d");
$codigo=$_GET["codigo"];
$valor=$_GET['valor'];
	if($valor!=0 || $valor!=""){
     $sql="INSERT INTO tipo_cambiomonedas (cod_moneda,fecha,valor)VALUES ('$codigo','$fecha','$valor');";
     $stmt = $dbh->prepare($sql);
     $stmt->execute();    
}

?>
