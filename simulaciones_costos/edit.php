<?php
require_once '../layouts/bodylogin.php';
require_once '../conexion.php';
require_once '../functions.php';
require_once '../functionsGeneral.php';
require_once 'configModule.php';

$dbh = new Conexion();

$codigo=$_GET["cod"];
$estado=$_GET["estado"];
session_start();

$globalUser=$_SESSION["globalUser"];
$globalGestion=$_SESSION["globalGestion"];
$globalUnidad=$_SESSION["globalUnidad"];
$globalArea=$_SESSION["globalArea"];
$globalAdmin=$_SESSION["globalAdmin"];

$fechaHoraActual=date("Y-m-d H:i:s");

$sqlUpdate="UPDATE simulaciones_costos SET  cod_estadosimulacion=$estado where codigo=$codigo";
$stmtUpdate = $dbh->prepare($sqlUpdate);
$flagSuccess=$stmtUpdate->execute();

if(isset($_GET['r'])){
    $urlR="&r=".$_GET['r'];
}
if(isset($_GET['admin'])){
  $urlList2=$urlList;
   $urlR="";
  //aprobar mediante servicio web
}

if(isset($_GET['q'])){
 if($flagSuccess==true){
	showAlertSuccessError(true,"../".$urlList2."&q=".$_GET['q']."&s=".$_GET['s']."&u=".$_GET['u'].$urlR);	
 }else{
	showAlertSuccessError(false,"../".$urlList2."&q=".$_GET['q']."&s=".$_GET['s']."&u=".$_GET['u'].$urlR);
 }
}else{
 if($flagSuccess==true){
	showAlertSuccessError(true,"../".$urlList2);	
 }else{
	showAlertSuccessError(false,"../".$urlList2);
 }
}

?>
