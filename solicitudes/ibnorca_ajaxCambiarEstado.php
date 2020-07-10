<?php
//require_once '../layouts/bodylogin.php';
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

$iEstado=obtenerEstadoIfinancieroSolicitudes($estado);
$fechaHoraActual=date("Y-m-d H:i:s");


//////////////////////////////fin cambio estado//////////////////////////777

 if($estado=3){
  if(comprobarCuentasPasivasDeSolicitudRecursos($codigo)>0){
    //no crear el comprobante
    echo "####none";
  }else{
    $sqlUpdate="UPDATE solicitud_recursos SET  cod_estadosolicitudrecurso=$iEstado where codigo=$codigo";
    $stmtUpdate = $dbh->prepare($sqlUpdate);
    $flagSuccess=$stmtUpdate->execute();
    //enviar propuestas para la actualizacion de ibnorca
    $fechaHoraActual=date("Y-m-d H:i:s");
    $idTipoObjeto=2708;
    $idObjeto=$estado; //variable desde get
    $obs=$_GET['obs']; //$obs="Registro de propuesta";
    if(isset($_GET['u'])){
      $u=$_GET['u'];
      actualizarEstadosObjetosIbnorca($idTipoObjeto,$idObjeto,$u,$codigo,$fechaHoraActual,$obs);
    }else{
      actualizarEstadosObjetosIbnorca($idTipoObjeto,$idObjeto,$globalUser,$codigo,$fechaHoraActual,$obs);
    }
         echo "####ok";
    }       
 }else{
  $sqlUpdate="UPDATE solicitud_recursos SET  cod_estadosolicitudrecurso=$iEstado where codigo=$codigo";
    $stmtUpdate = $dbh->prepare($sqlUpdate);
    $flagSuccess=$stmtUpdate->execute();
    //enviar propuestas para la actualizacion de ibnorca
    $fechaHoraActual=date("Y-m-d H:i:s");
    $idTipoObjeto=2708;
    $idObjeto=$estado; //variable desde get
    $obs=$_GET['obs']; //$obs="Registro de propuesta";
    if(isset($_GET['u'])){
      $u=$_GET['u'];
      actualizarEstadosObjetosIbnorca($idTipoObjeto,$idObjeto,$u,$codigo,$fechaHoraActual,$obs);
    }else{
      actualizarEstadosObjetosIbnorca($idTipoObjeto,$idObjeto,$globalUser,$codigo,$fechaHoraActual,$obs);
    }
  echo "####ok";
 }

  

?>
