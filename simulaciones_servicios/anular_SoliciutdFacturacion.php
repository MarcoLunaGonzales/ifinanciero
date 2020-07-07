<?php
require_once '../layouts/bodylogin.php';
require_once '../conexion.php';
require_once '../functions.php';
require_once '../functionsGeneral.php';
require_once 'configModule.php';

$dbh = new Conexion();

//RECIBIMOS LAS VARIABLES
$codigo=$_GET['codigo'];
session_start();
$globalUser=$_SESSION["globalUser"];

if(isset($_GET['q'])){
  $q=$_GET['q'];
  $v=$_GET['v'];
  $s=$_GET['s'];
  $u=$_GET['u'];  
}
$sql="UPDATE solicitudes_facturacion set cod_estadosolicitudfacturacion='2' where codigo=$codigo";
$stmt = $dbh->prepare($sql);
$flagSuccess=$stmt->execute();
if($flagSuccess){
  //enviar propuestas para la actualizacion de ibnorca
  $fechaHoraActual=date("Y-m-d H:i:s");
  $idTipoObjeto=2709;
  $idObjeto= 2730; //anulado
  $obs="Solicitud Facturación Anulada";
  if(isset($_GET['q'])){
      $u=$_GET['q'];
      actualizarEstadosObjetosIbnorca($idTipoObjeto,$idObjeto,$u,$codigo,$fechaHoraActual,$obs);
  }else{
     actualizarEstadosObjetosIbnorca($idTipoObjeto,$idObjeto,$globalUser,$codigo,$fechaHoraActual,$obs);
  }

	$tipoSolicitud=obtenerTipoSolicitud($codigo);
	if($tipoSolicitud==5){//normas borrar el id de norma
	   	$stmtFact = $dbh->prepare("SELECT cod_curso from solicitudes_facturaciondetalle where cod_solicitudfacturacion=$codigo");
        $stmtFact->execute();        
        while ($row_fac = $stmtFact->fetch()){        	
			$codigo_norma=$row_fac['cod_curso'];
			$stmtIbnorca = $dbh->prepare("UPDATE ibnorca.ventanormas set idSolicitudfactura='' where IdVentaNormas=$codigo_norma");
        	$flagSuccess=$stmtIbnorca->execute();
        }
	}
}
if(isset($_GET['q'])){
  showAlertSuccessError($flagSuccess,"../".$urlListSolicitudFacturas."&q=".$q."&u=".$u."&v=".$v."&s=".$s);
}else{
  showAlertSuccessError($flagSuccess,"../".$urlListSolicitudFacturas);	
}

?>