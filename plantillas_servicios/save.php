<?php
session_start();
require_once '../layouts/bodylogin2.php';
require_once '../conexion.php';
require_once '../functions.php';
require_once '../functionsGeneral.php';
require_once 'configModule.php';

$dbh = new Conexion();

$globalUser=$_SESSION["globalUser"];

$fechaHoraActual=date("Y-m-d H:i:s");

if(isset($_POST['nombre'])){
$nombre=$_POST['nombre'];
$abrev=$_POST['abreviatura'];
$cliente="";
$productos="";
$norma="";
$dias=$_POST['dias'];
$utmin=$_POST['utilidad_minima'];
$unidad=$_POST['unidad'];
$area=$_POST['area'];

if($area==39){
  $cantidadAuditorias=obtenerValorConfiguracion(17);
  $ingresoPresupuestado=obtenerValorConfiguracion(21);
}else{
  $cantidadAuditorias=obtenerValorConfiguracion(18);
  $ingresoPresupuestado=obtenerValorConfiguracion(22);
}

  $codPlanServ=obtenerCodigoPlanServ();
  $dbh = new Conexion();
  $sqlInsert="INSERT INTO plantillas_servicios (codigo, nombre, abreviatura, cod_unidadorganizacional, cod_area,cod_cliente,productos,norma,cod_personal_registro,fecha_registro,dias_auditoria,cod_estadoplantilla,cod_estadoreferencial,utilidad_minima,cantidad_auditorias,ingreso_presupuestado) 
  VALUES ('".$codPlanServ."','".$nombre."','".$abrev."', '".$unidad."', '".$area."','".$cliente."','".$productos."','".$norma."','".$globalUser."','".$fechaHoraActual."','".$dias."',1,1,'".$utmin."','".$cantidadAuditorias."','".$ingresoPresupuestado."')";
  $stmtInsert = $dbh->prepare($sqlInsert);
  $flagSuccess=$stmtInsert->execute();


  if($flagSuccess==true){
	  showAlertSuccessError(true,"../".$urlRegister."?cod=".$codPlanServ);	
  }else{
	  showAlertSuccessError(false,"../".$urlList);
  }
	
}

?>
