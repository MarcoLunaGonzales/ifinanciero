<?php

require_once '../layouts/bodylogin.php';
require_once '../conexion.php';
require_once '../functionsGeneral.php';
require_once 'configModule.php';

$dbh = new Conexion();

$datos=$_POST["personal"];
$codBono=$_POST["codBono"];
$datosPorciones = explode("@", $datos);
$codigo=$datosPorciones[0];
$flagSuccess=false;
if($codigo!=""){

  $stmt = $dbh->prepare("UPDATE bonos_personal_mes SET indefinido=0 where codigo=$codigo");
  $flagSuccess=$stmt->execute();  

 }  
showAlertSuccessError($flagSuccess,"../".$urlListMes."&codigo=".$codBono);
?>
