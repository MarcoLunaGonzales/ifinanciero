<?php
session_start();
require_once '../layouts/bodylogin.php';
require_once '../conexion.php';
require_once '../functions.php';
require_once '../functionsGeneral.php';
require_once '../simulaciones_servicios/configModule.php';

$dbh = new Conexion();

$codigo=$_GET["cod"];
$estado=$_GET["estado"];


$globalUser=$_SESSION["globalUser"];
$globalGestion=$_SESSION["globalGestion"];
$globalUnidad=$_SESSION["globalUnidad"];
$globalArea=$_SESSION["globalArea"];
$globalAdmin=$_SESSION["globalAdmin"];

date_default_timezone_set('America/La_Paz');
$fechaHoraActual=date("Y-m-d H:i:s");

//SI LA SOLICITUD YA SE ENCUENTRA FACTURADA
$stmtSolicitud = $dbh->prepare("SELECT cod_estadosolicitudfacturacion from solicitudes_facturacion where codigo=$codigo");
$stmtSolicitud->execute();
$estadoAnteriorX=0;
while ($rowSolicitud = $stmtSolicitud->fetch(PDO::FETCH_ASSOC)) {
   $estadoAnteriorX=$rowSolicitud['cod_estadosolicitudfacturacion'];
}

if($estadoAnteriorX!=5){
  $sqlUpdate="UPDATE solicitudes_facturacion SET  cod_estadosolicitudfacturacion=$estado where codigo=$codigo";
  $stmtUpdate = $dbh->prepare($sqlUpdate);
  $flagSuccess=$stmtUpdate->execute();
}else{
  $flagSuccess=false;
}

if($estado!=1){
	//actualziar los estados del servidor ibnorca
	if($estado==4){
    //enviar propuestas para la actualizacion de ibnorca  
    $fechaHoraActual=date("Y-m-d H:i:s");
    $idTipoObjeto=2709;
    $idObjeto=2727; //regristado
    $obs="Solicitud Facturación En Aprobación ";
    if(isset($_GET['u'])){
       $u=$_GET['u'];
       actualizarEstadosObjetosIbnorca($idTipoObjeto,$idObjeto,$u,$codigo,$fechaHoraActual,$obs);    
    }else{
       actualizarEstadosObjetosIbnorca($idTipoObjeto,$idObjeto,$globalUser,$codigo,$fechaHoraActual,$obs);    
    }
	}else{
    if($estado==6){
      //enviar propuestas para la actualizacion de ibnorca
      $fechaHoraActual=date("Y-m-d H:i:s");
      $idTipoObjeto=2709;
      $idObjeto=2823; //en revison
      $obs="Solicitud En Revisión";
      if(isset($_GET['u'])){
        $u=$_GET['u'];
        actualizarEstadosObjetosIbnorca($idTipoObjeto,$idObjeto,$u,$codigo,$fechaHoraActual,$obs);    
      }else{
        actualizarEstadosObjetosIbnorca($idTipoObjeto,$idObjeto,$globalUser,$codigo,$fechaHoraActual,$obs);    
      }   
    }elseif($estado==3){
      //enviar propuestas para la actualizacion de ibnorca
      $fechaHoraActual=date("Y-m-d H:i:s");
      $idTipoObjeto=2709;
      $idObjeto=2728; //aprobado
      $obs="Solicitud Aprobada desde Contabilidad"; //$obs="Registro de propuesta";
      if(isset($_GET['u'])){
        $u=$_GET['u'];
        actualizarEstadosObjetosIbnorca($idTipoObjeto,$idObjeto,$u,$codigo,$fechaHoraActual,$obs);
      }else{
        actualizarEstadosObjetosIbnorca($idTipoObjeto,$idObjeto,$globalUser,$codigo,$fechaHoraActual,$obs);
      }
    }
  }
	//fin de actulizar estados del servidor ibnorca
}else{
	//enviar propuestas para la actualizacion de ibnorca
  $fechaHoraActual=date("Y-m-d H:i:s");
  $idTipoObjeto=2709;
  $idObjeto=2726; //regristado
  $obs="Registro de Solicitud Facturación";
  if(isset($_GET['u'])){
    $u=$_GET['u'];
    actualizarEstadosObjetosIbnorca($idTipoObjeto,$idObjeto,$u,$codigo,$fechaHoraActual,$obs);
  }else{
     actualizarEstadosObjetosIbnorca($idTipoObjeto,$idObjeto,$globalUser,$codigo,$fechaHoraActual,$obs);
  }
}
if(isset($_GET['admin'])){
  if($_GET['admin']==10){
    $urlList2Sol=$urlListSol_conta;
  }elseif($_GET['admin']==20){
    $urlList2Sol=$urlList2Sol;
  }else{
    $urlList2Sol=$urlListSol;    
  }
  if(isset($_GET['sim'])){
    $urlList2Sol=$urlListSolSim."&cod=".$_GET['sim'];
  }  
  
}
if(isset($_GET['q'])){
	$q=$_GET['q'];
  $s=$_GET['s'];
  $u=$_GET['u'];
  $v=$_GET['v'];
  if($_GET['admin']==20){
    $string_itranet="&q=".$q."&s=".$s."&u=".$u."&r=".$v;
  }else{
    $string_itranet="&q=".$q."&s=".$s."&u=".$u."&v=".$v;
  }
  if($flagSuccess==true){
   showAlertSuccessError(true,"../".$urlList2Sol.$string_itranet);
  }else{
   showAlertSuccessError(false,"../".$urlList2Sol.$string_itranet);
  }
  
}else{
  if($estado==3){//enviado desde conta
    if($flagSuccess==true){
      showAlertSuccessError(true,"../".$urlListSol_conta); 
    }else{  
      showAlertSuccessError(false,"../".$urlListSol_conta);
    }
  }else{
    if($flagSuccess==true){
      showAlertSuccessError(true,"../".$urlList2Sol); 
    }else{  
      showAlertSuccessError(false,"../".$urlList2Sol);
    }
  }
}

?>