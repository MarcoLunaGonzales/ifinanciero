<?php
require_once '../layouts/bodylogin.php';
require_once '../conexion.php';
require_once '../functions.php';
require_once '../functionsGeneral.php';
require_once 'configModule.php';

$dbh = new Conexion();


session_start();

$globalUser=$_SESSION["globalUser"];
$globalGestion=$_SESSION["globalGestion"];
$globalUnidad=$_SESSION["globalUnidad"];
$globalArea=$_SESSION["globalArea"];
$globalAdmin=$_SESSION["globalAdmin"];

$codigo=$_GET["codigo"];
$estado=$_GET["estado"];

$iEstado=obtenerEstadoIfinancieroPlantillas($estado);
$fechaHoraActual=date("Y-m-d H:i:s");

$sqlUpdate="UPDATE plantillas_servicios SET  cod_estadoplantilla=$iEstado where codigo=$codigo";
$stmtUpdate = $dbh->prepare($sqlUpdate);
$flagSuccess=$stmtUpdate->execute();

$id_perfil=$_GET["id_perfil"];
//enviar propuestas para la actualizacion de ibnorca
    $fechaHoraActual=date("Y-m-d H:i:s");
    $idTipoObjeto=2706;
    $idObjeto=$estado; //variable desde get
    $obs=$_GET['obs']; //$obs="Registro de propuesta";
  if($id_perfil==0){
  	actualizarEstadosObjetosIbnorca($idTipoObjeto,$idObjeto,$globalUser,$codigo,$fechaHoraActual,$obs);
  }else{
  	actualizarEstadosObjetosIbnorca($idTipoObjeto,$idObjeto,$id_perfil,$codigo,$fechaHoraActual,$obs);
  }  
    

?>
