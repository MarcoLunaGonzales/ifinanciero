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

$sqlUpdate="UPDATE simulaciones_servicios SET  cod_estadosimulacion=$estado where codigo=$codigo";
$stmtUpdate = $dbh->prepare($sqlUpdate);
$flagSuccess=$stmtUpdate->execute();

if($estado!=1){
	//actualziar los estados del servidor ibnorca
	if($estado==4){
        //enviar propuestas para la actualizacion de ibnorca
        $fechaHoraActual=date("Y-m-d H:i:s");
        $idTipoObjeto=2707;
        $idObjeto=2716; //regristado
        $obs="Registro de propuesta";
        if(!isset($_GET['u'])){
         actualizarEstadosObjetosIbnorca($idTipoObjeto,$idObjeto,$globalUser,$codigo,$fechaHoraActual,$obs);           
        }else{
         $u=$_GET["u"];
         actualizarEstadosObjetosIbnorca($idTipoObjeto,$idObjeto,$u,$codigo,$fechaHoraActual,$obs);     
        }
	}
    if($estado==5){
        //enviar propuestas para la actualizacion de ibnorca
        $fechaHoraActual=date("Y-m-d H:i:s");
        $idTipoObjeto=2707;
        $idObjeto=2718; //regristado
        $obs="Adjudicada";
        if(!isset($_GET['u'])){
         actualizarEstadosObjetosIbnorca($idTipoObjeto,$idObjeto,$globalUser,$codigo,$fechaHoraActual,$obs);           
        }else{
         $u=$_GET["u"];
         actualizarEstadosObjetosIbnorca($idTipoObjeto,$idObjeto,$u,$codigo,$fechaHoraActual,$obs);     
        }
        //crear la solicitud
        $simulacion=obtenerDatosCompletosPorSimulacionServicios($codigo);
        while ($row = $simulacion->fetch(PDO::FETCH_ASSOC)) {
            $IdArea=$row['cod_area'];
            $IdOficina=$row['cod_unidadorganizacional'];
            $IdTipo=$row['id_tiposervicio'];
            $IdCliente=$row['cod_cliente'];
            $Descripcion=obtenerServiciosClaServicioTipoNombre($IdTipo)."  ".obtenerServiciosTipoObjetoNombre($codObjeto);
            $IdUsuarioRegistro=$row['cod_responsable'];
            $fecharegistro=date("Y-m-d");
            $idServicio=obtenerCodigoServicioIbnorca();
            // Prepare
            $stmt = $dbh->prepare("INSERT INTO ibnorca.servicios (idServicio,IdArea,IdOficina,IdTipo,IdCliente,Descripcion,IdUsuarioRegistro,fecharegistro,IdPropuesta) 
            VALUES ('$idServicio','$IdArea','$IdOficina','$IdTipo','$IdCliente','$Descripcion','$IdUsuarioRegistro','$fecharegistro','$codigo')");
            // Bind
            $stmt->execute();
            //enviar propuestas para la actualizacion de ibnorca
            $fechaHoraActual=date("Y-m-d H:i:s");
            $idTipoObjeto=195;
            $idObjeto=204; //regristado
            $obs="En ejecución";
            //id de perfil para cambio de estado en ibnorca
      
            if(isset($_GET['u'])){
              $id_perfil=$_GET['u'];
              actualizarEstadosObjetosIbnorca($idTipoObjeto,$idObjeto,$globalUser,$idServicio,$fechaHoraActual,$obs);
            }else{
              actualizarEstadosObjetosIbnorca($idTipoObjeto,$idObjeto,$id_perfil,$idServicio,$fechaHoraActual,$obs);
            }
       
            $stmt2 = $dbh->prepare("UPDATE simulaciones_servicios SET idServicio=$idServicio where codigo=$codigo");
            $flagSuccess2=$stmt2->execute();
        }
        //fin crear servicio
    }

	//fin de actulizar estados del servidor ibnorca
}else{
	//enviar propuestas para la actualizacion de ibnorca
    $fechaHoraActual=date("Y-m-d H:i:s");
    $idTipoObjeto=2707;
    $idObjeto=2715; //regristado
    $obs="Registro de propuesta";
    if(!isset($_GET['u'])){
     actualizarEstadosObjetosIbnorca($idTipoObjeto,$idObjeto,$globalUser,$codigo,$fechaHoraActual,$obs);
    }else{
     $u=$_GET["u"];
     actualizarEstadosObjetosIbnorca($idTipoObjeto,$idObjeto,$u,$codigo,$fechaHoraActual,$obs);
    }
}

if(isset($_GET['admin'])){
  $urlList2=$urlList;
  //aprobar mediante servicio web
}

if(isset($_GET['q'])){
 if($flagSuccess==true){
	showAlertSuccessError(true,"../".$urlList2."&q=".$_GET['q']."&s=".$_GET['s']."&u=".$_GET['u']);	
 }else{
	showAlertSuccessError(false,"../".$urlList2."&q=".$_GET['q']."&s=".$_GET['s']."&u=".$_GET['u']);
 }
}else{
 if($flagSuccess==true){
	showAlertSuccessError(true,"../".$urlList2);	
 }else{
	showAlertSuccessError(false,"../".$urlList2);
 }
}
?>
