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
if(isset($_GET["conta"])){
  $urlList2=$urlList4;
}

if($estado==10||$estado==11||$estado==12){
  if($estado==12){
    $urlList2=$urlList3;
  }else{
   $urlList2=$urlList4;  
  }
  
  $estado=$estado-10;
  $sqlUpdate="UPDATE solicitud_recursos SET  revisado_contabilidad=$estado where codigo=$codigo";
  $stmtUpdate = $dbh->prepare($sqlUpdate);
  $flagSuccess=$stmtUpdate->execute();
}else{

if(obtenerUnidadSolicitanteRecursos($codigo)==3000||obtenerAreaSolicitanteRecursos($codigo)==obtenerValorConfiguracion(65)||obtenerDetalleRecursosSIS($codigo)>0){ //&&obtenerAreaSolicitanteRecursos($codigo)==obtenerValorConfiguracion(65)
  if(isset($_GET["reg"])){
   if($estado==4&&$_GET['reg']!=2){
    $estado=7;
   }   
  }  
}

$sqlUpdate="UPDATE solicitud_recursos SET  cod_estadosolicitudrecurso=$estado where codigo=$codigo";
if($estado==8){
  //enviar propuestas para la actualizacion de ibnorca
         $fechaHoraActual=date("Y-m-d H:i:s");
         $idTipoObjeto=2708;
         $idObjeto=2725; //regristado
         $obs="Solicitud Contabilizada";
         if(isset($_GET['u'])){
          $u=$_GET['u'];
           actualizarEstadosObjetosIbnorca($idTipoObjeto,$idObjeto,$u,$codigo,$fechaHoraActual,$obs);    
          }else{
           actualizarEstadosObjetosIbnorca($idTipoObjeto,$idObjeto,$globalUser,$codigo,$fechaHoraActual,$obs);    
          }
        ///
}

if(isset($_GET['obs'])){
  $obs=$_GET['obs'];
  if(isset($_GET["ll"])){
    $sqlUpdate="UPDATE solicitud_recursos SET  cod_estadosolicitudrecurso=$estado,glosa_estado=CONCAT(glosa_estado,'####','$obs') where codigo=$codigo";  
  }else{
    $sqlUpdate="UPDATE solicitud_recursos SET  cod_estadosolicitudrecurso=$estado,glosa_estado='$obs' where codigo=$codigo";
  }  
}


$stmtUpdate = $dbh->prepare($sqlUpdate);
$flagSuccess=$stmtUpdate->execute();


if($estado!=1){
	//actualziar los estados del servidor ibnorca
	if($estado==4){
    //enviar propuestas para la actualizacion de ibnorca
    $fechaHoraActual=date("Y-m-d H:i:s");
    $idTipoObjeto=2708;
    $idObjeto=2722; //regristado
    $obs="En Aprobacion Solicitud";
    if(isset($_GET['q'])){
       $u=$_GET['q'];
       actualizarEstadosObjetosIbnorca($idTipoObjeto,$idObjeto,$u,$codigo,$fechaHoraActual,$obs);    
     }else{
       actualizarEstadosObjetosIbnorca($idTipoObjeto,$idObjeto,$globalUser,$codigo,$fechaHoraActual,$obs);    
     }
    
	}else{
  if($estado==6){
    //enviar propuestas para la actualizacion de ibnorca
    $fechaHoraActual=date("Y-m-d H:i:s");
    $idTipoObjeto=2708;
    $idObjeto=2822; //regristado
    $obs="En Pre Aprobacion Solicitud";
    if(isset($_GET['u'])){
       $u=$_GET['u'];
       actualizarEstadosObjetosIbnorca($idTipoObjeto,$idObjeto,$u,$codigo,$fechaHoraActual,$obs);    
     }else{
       actualizarEstadosObjetosIbnorca($idTipoObjeto,$idObjeto,$globalUser,$codigo,$fechaHoraActual,$obs);    
     }   
   }else{
    if($estado==7){
     //enviar propuestas para la actualizacion de ibnorca
     $fechaHoraActual=date("Y-m-d H:i:s");
     $idTipoObjeto=2708;
     $idObjeto=3107; //ESTADO PARA PROYECTO SIS
     $obs="Enviado a Gestión SIS";
     if(isset($_GET['u'])){
       $u=$_GET['u'];
       actualizarEstadosObjetosIbnorca($idTipoObjeto,$idObjeto,$u,$codigo,$fechaHoraActual,$obs);    
      }else{
       actualizarEstadosObjetosIbnorca($idTipoObjeto,$idObjeto,$globalUser,$codigo,$fechaHoraActual,$obs);    
      } 
    }else{
      if($estado==5){
             //enviar propuestas para la actualizacion de ibnorca
         $fechaHoraActual=date("Y-m-d H:i:s");
         $idTipoObjeto=2708;
         $idObjeto=2725; //regristado
         $obs="Solicitud Contabilizada";
         if(isset($_GET['u'])){
          $u=$_GET['u'];
           actualizarEstadosObjetosIbnorca($idTipoObjeto,$idObjeto,$u,$codigo,$fechaHoraActual,$obs);    
          }else{
           actualizarEstadosObjetosIbnorca($idTipoObjeto,$idObjeto,$globalUser,$codigo,$fechaHoraActual,$obs);    
          }
        ///
      }
    }
   }   
  }
	//fin de actulizar estados del servidor ibnorca
 }else{
	//enviar propuestas para la actualizacion de ibnorca
    $fechaHoraActual=date("Y-m-d H:i:s");
    $idTipoObjeto=2708;
    $idObjeto=2721; //regristado
    $obs="Registro de Solicitud";
    if(isset($_GET['u'])){
       $u=$_GET['u'];
       actualizarEstadosObjetosIbnorca($idTipoObjeto,$idObjeto,$u,$codigo,$fechaHoraActual,$obs);
     }else{
       actualizarEstadosObjetosIbnorca($idTipoObjeto,$idObjeto,$globalUser,$codigo,$fechaHoraActual,$obs);
     }
    
 }

} //else Estado Contabilidad

if(isset($_GET['q'])){
  $q=$_GET['q'];
  $s=$_GET['s'];
  $u=$_GET['u'];
  $v=$_GET['v'];

}

if(isset($_GET['admin'])){
  $urlList2=$urlList;
  $urlc="&q=".$q."&s=".$s."&u=".$u."&v=".$v;
  if(isset($_GET['reg'])){
    $urlList2=$urlList3;
    if($_GET['reg']==2){
     $urlList2=$urlList5;
    }
    
  }
}else{
  $urlc="&q=".$q."&s=".$s."&u=".$u;
  if(isset($_GET['r'])){
    $urlc=$urlc."&r=".$_GET['r'];
  }
}
if(isset($_GET["ladmin"])){
  $urlList2=$urlList2Auxiliar;
}

if(isset($_GET['q'])){
	$q=$_GET['q'];
  $s=$_GET['s'];
  $u=$_GET['u'];
  $v=$_GET['v'];
  if($flagSuccess==true){
	showAlertSuccessError(true,"../".$urlList2.$urlc);	
   }else{
	showAlertSuccessError(false,"../".$urlList2.$urlc);
   }
}else{
	if($flagSuccess==true){
	showAlertSuccessError(true,"../".$urlList2);	
   }else{
	showAlertSuccessError(false,"../".$urlList2);
   }
}

?>