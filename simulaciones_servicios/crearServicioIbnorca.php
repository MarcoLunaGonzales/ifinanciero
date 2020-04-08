<?php

require_once 'conexion.php';
require_once 'functions.php';
require_once 'functionsGeneral.php';
require_once 'configModule.php';

$dbh = new Conexion();

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
    $Descripcion="Nuevo Servicio";
    $IdUsuarioRegistro=$row['cod_responsable'];
    $fecharegistro=date("Y-m-d");
    $idServicio=obtenerCodigoServicioIbnorca();
   // Prepare
    $stmt = $dbh->prepare("INSERT INTO ibnorca.servicios (idServicio,IdArea,IdOficina,IdTipo,IdCliente,Descripcion,IdUsuarioRegistro,fecharegistro) 
	VALUES ('$idServicio','$IdArea','$IdOficina','$IdTipo','$IdCliente','$Descripcion','$IdUsuarioRegistro','$fecharegistro')");
// Bind
    $flagSuccess=$stmt->execute();
   
    $stmt2 = $dbh->prepare("UPDATE simulaciones_servicios SET idServicio=$idServicio where codigo=$codigo");
    $flagSuccess2=$stmt2->execute();
}

if(isset($_GET['q'])){
  showAlertSuccessError($flagSuccess,$urlList."&q=".$_GET['q']);	
}else{
  showAlertSuccessError($flagSuccess,$urlList);
}


?>