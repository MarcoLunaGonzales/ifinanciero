<?php
require_once '../conexion.php';
require_once '../functions.php';
require_once '../functionsGeneral.php';
require_once 'configModule.php';

$dbh = new Conexion();

$codigo=$_GET["codigo"];
//$aprobado=$_GET["aprobado"];
$aprobado=4;
session_start();

$globalUser=$_SESSION["globalUser"];
$globalGestion=$_SESSION["globalGestion"];
$globalUnidad=$_SESSION["globalUnidad"];
$globalArea=$_SESSION["globalArea"];
$globalAdmin=$_SESSION["globalAdmin"];

$fechaHoraActual=date("Y-m-d H:i:s");

$sqlUpdate="UPDATE simulaciones_servicios SET  cod_estadosimulacion=$aprobado where codigo=$codigo";
$stmtUpdate = $dbh->prepare($sqlUpdate);
$flagSuccess=$stmtUpdate->execute();


//enviar propuestas para la actualizacion de ibnorca

$id_perfil=$_GET["id_perfil"];
$fechaHoraActual=date("Y-m-d H:i:s");
$idTipoObjeto=2707;
$idObjeto=2716; //regristado
$obs="Propuesta en Aprobacion";
if($id_perfil==0){
    actualizarEstadosObjetosIbnorca($idTipoObjeto,$idObjeto,$globalUser,$codigo,$fechaHoraActual,$obs);
  }else{
    actualizarEstadosObjetosIbnorca($idTipoObjeto,$idObjeto,$id_perfil,$codigo,$fechaHoraActual,$obs);
  }

?>
