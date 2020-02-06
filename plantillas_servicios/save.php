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
$cliente=$_POST['cliente'];
$productos=$_POST['productos'];
$norma=$_POST['norma'];
$dias=$_POST['dias'];
$utmin=$_POST['utilidad_minima'];
//$pFecha = explode("/", $_POST['fecha_auditoria']);
//$fecha_auditoria=$pFecha[2]."-".$pFecha[1]."-".$pFecha[0];
$unidad=$_POST['unidad'];
$area=$_POST['area'];
//$servicio=$_POST['cod_servicio'];

  $codPlanServ=obtenerCodigoPlanServ();
  $dbh = new Conexion();
  $sqlInsert="INSERT INTO plantillas_servicios (codigo, nombre, abreviatura, cod_unidadorganizacional, cod_area,cod_cliente,productos,norma,cod_personal_registro,fecha_registro,dias_auditoria,cod_estadoplantilla,cod_estadoreferencial,utilidad_minima) 
  VALUES ('".$codPlanServ."','".$nombre."','".$abrev."', '".$unidad."', '".$area."','".$cliente."','".$productos."','".$norma."','".$globalUser."','".$fechaHoraActual."','".$dias."',1,1,'".$utmin."')";
  $stmtInsert = $dbh->prepare($sqlInsert);
  $flagSuccess=$stmtInsert->execute();

/*for ($i=0; $i < cantidadF($servicio); $i++) { 
    $sql="INSERT INTO plantillas_servicios_tiposervicio (cod_plantillaservicio, cod_claservicio,cod_estadoreferencial) 
       VALUES ('".$codPlanServ."','".$servicio[$i]."', 1)";
    $stmt = $dbh->prepare($sql);
    $stmt->execute();   
}*/

  if($flagSuccess==true){
	  showAlertSuccessError(true,"../".$urlRegister."?cod=".$codPlanServ);	
  }else{
	  showAlertSuccessError(false,"../".$urlList);
  }
	
}

?>
