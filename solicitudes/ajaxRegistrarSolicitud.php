<?php
session_start();
require_once '../conexion.php';
require_once '../styles.php';
require_once '../functionsGeneral.php';
require_once '../functions.php';
require_once 'configModule.php';

$dbh = new Conexion();

$sqlX="SET NAMES 'utf8'";
$stmtX = $dbh->prepare($sqlX);
$stmtX->execute();

$globalNombreGestion=$_SESSION["globalNombreGestion"];
$globalUser=$_SESSION["globalUser"];
$globalGestion=$_SESSION["globalGestion"];
$globalUnidad=$_SESSION["globalUnidad"];
$globalNombreUnidad=$_SESSION['globalNombreUnidad'];
$globalArea=$_SESSION["globalArea"];
$globalAdmin=$_SESSION["globalAdmin"];

if(isset($_GET['numero'])){
	$numero=$_GET['numero'];
  $codProv=$_GET['cod_prov'];
  if($codProv==0){
    if($_GET['cod_sim']==0){
      //datos para solicitud recursos manual manual
      $codSim=0;
      $codSimServ=0;
    }else{
      //datos para solicitud recursos SIMULACION (PROPUESTA)
     $simu=explode("$$$",$_GET['cod_sim']);
     if($simu[1]=="TCP"){
      //tcp o tcs
      $codSim=0;
      $codSimServ=$simu[0];
      $areaUnidad=obtenerUnidadAreaPorSimulacionServicio($codSimServ);
     }else{
      // sec
      $codSim=$simu[0];
      $codSimServ=0;
      $areaUnidad=obtenerUnidadAreaPorSimulacionCosto($codSim);
     }
     $globalArea=$areaUnidad[0];
     $globalUnidad=$areaUnidad[1];     
    }
  }else{
    //datos para solicitud recursos proveeedor
    $codSim=0;
    $codSimServ=0;
  }
  
  $codCont=0;//CODIGO DE CONTRATO
  $fecha= date("Y-m-d h:m:s");
  $codSolicitud=obtenerCodigoSolicitudRecursos();
  $dbh = new Conexion();
  if(isset($_GET['v'])){
       $v=$_GET['v'];
       $sqlInsert="INSERT INTO solicitud_recursos (codigo, cod_personal,cod_unidadorganizacional,cod_area,fecha,numero,cod_simulacion,cod_proveedor,cod_simulacionservicio,cod_contrato,idServicio) 
       VALUES ('".$codSolicitud."','".$globalUser."','".$globalUnidad."', '".$globalArea."', '".$fecha."','".$numero."','".$codSim."','".$codProv."','".$codSimServ."','".$codCont."','".$v."')";
  }else{
    $sqlInsert="INSERT INTO solicitud_recursos (codigo, cod_personal,cod_unidadorganizacional,cod_area,fecha,numero,cod_simulacion,cod_proveedor,cod_simulacionservicio,cod_contrato) 
    VALUES ('".$codSolicitud."','".$globalUser."','".$globalUnidad."', '".$globalArea."', '".$fecha."','".$numero."','".$codSim."','".$codProv."','".$codSimServ."','".$codCont."')";
  }
  
  
  $stmtInsert = $dbh->prepare($sqlInsert);
  $stmtInsert->execute();

  //enviar propuestas para la actualizacion de ibnorca
  $fechaHoraActual=date("Y-m-d H:i:s");
  $idTipoObjeto=2708;
  $idObjeto=2721; //regristado
  $obs="Registro de Solicitud";
  if(isset($_GET['u'])){
       $u=$_GET['u'];
       actualizarEstadosObjetosIbnorca($idTipoObjeto,$idObjeto,$u,$codSolicitud,$fechaHoraActual,$obs);
  }else{
       actualizarEstadosObjetosIbnorca($idTipoObjeto,$idObjeto,$globalUser,$codSolicitud,$fechaHoraActual,$obs);
  }
  

  if(isset($_GET['q'])){
    $q=$_GET['q'];
    $s=$_GET['s'];
    $u=$_GET['u'];
    $v=$_GET['v'];
    showAlertSuccessError(true,"../solicitudes/registerSolicitud.php?cod=".$codSolicitud."&q=".$q."&s=".$s."&u=".$u."&v=".$v);
  }else{
    showAlertSuccessError(true,"../solicitudes/registerSolicitud.php?cod=".$codSolicitud);
  }
  
  echo "Registro Satisfactorio";
}

?>
