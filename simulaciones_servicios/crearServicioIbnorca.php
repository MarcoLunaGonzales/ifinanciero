<?php

require_once 'conexion.php';
require_once 'functions.php';
require_once 'functionsGeneral.php';
require_once 'configModule.php';

$dbh = new Conexion();

$globalNombreGestion=$_SESSION["globalNombreGestion"];
$globalUser=$_SESSION["globalUser"];
$globalGestion=$_SESSION["globalGestion"];
$globalUnidad=$_SESSION["globalUnidad"];
$globalNombreUnidad=$_SESSION['globalNombreUnidad'];
$globalArea=$_SESSION["globalArea"];
$globalAdmin=$_SESSION["globalAdmin"];

//RECIBIMOS LAS VARIABLES
$codigo=$_GET['codigo'];

//datos 

$flagSuccess=false;

$simulacion=obtenerDatosCompletosPorSimulacionServicios($codigo);
while ($row = $simulacion->fetch(PDO::FETCH_ASSOC)) {
	$IdArea=$row['cod_area'];
    $IdOficina=$row['cod_unidadorganizacional'];
    $IdTipo=$row['id_tiposervicio'];
    $IdCliente=$row['cod_cliente'];
    $codObjeto=$row['cod_objetoservicio'];
    $Descripcion=obtenerServiciosClaServicioTipoNombre($IdTipo)."  ".obtenerServiciosTipoObjetoNombre($codObjeto);
    $IdUsuarioRegistro=$row['cod_responsable'];
    $fecharegistro=date("Y-m-d");
    $idServicio=obtenerCodigoServicioIbnorca();
   // Prepare
    $stmt = $dbh->prepare("INSERT INTO ibnorca.servicios (idServicio,IdArea,IdOficina,IdTipo,IdCliente,Descripcion,IdUsuarioRegistro,fecharegistro,IdPropuesta) 
	VALUES ('$idServicio','$IdArea','$IdOficina','$IdTipo','$IdCliente','$Descripcion','$IdUsuarioRegistro','$fecharegistro','$codigo')");
// Bind
    $flagSuccess=$stmt->execute();

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

if(isset($_GET['q'])){
  showAlertSuccessError($flagSuccess,$urlList."&q=".$_GET['q']."&s=".$_GET['s']."&u=".$_GET['u']);	
}else{
  showAlertSuccessError($flagSuccess,$urlList);
}


?>