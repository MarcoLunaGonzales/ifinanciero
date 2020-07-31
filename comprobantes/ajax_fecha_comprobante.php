<?php
require_once '../conexion.php';
require_once '../functions.php';
require_once 'configModule.php';

setlocale(LC_TIME, "Spanish");
session_start();

$tipoComprobante=$_GET["tipo_comprobante"];
$fecha=$_GET["fecha"];
$codigo=$_GET["codigo"];
$globalGestion=$_SESSION["globalGestion"];
$anio=nameGestion($globalGestion);

$globalUnidad=$_SESSION["globalUnidad"];
$globalMes=$_SESSION["globalMes"];


$db = new Conexion();
$sqlUO="SELECT max(fecha)as fecha from comprobantes where cod_tipocomprobante=$tipoComprobante and cod_unidadorganizacional='$globalUnidad' and YEAR(fecha)='$anio' and MONTH(fecha)='$globalMes' and cod_estadocomprobante<>2 and codigo!=$codigo";

$stmt = $db->prepare($sqlUO);
$stmt->execute();
$fechaDefault=$anio."-".$globalMes."-01";

$fechaDefault = date("Y-m-d",strtotime($fechaDefault));
// echo $fechaDefault;

$fechaComprobante=$anio."-".$globalMes."-01";
while ($row = $stmt->fetch()){
	$fechaComprobante=$row['fecha'];
	// echo "entro: ".$fechaComprobante;
}

if($fechaComprobante=="" || $fechaComprobante==null){
	// echo "if";
	$fechaMin=$fechaDefault;	
}else{
	$fechaMin = date("Y-m-d",strtotime($fechaComprobante."+ 0 days"));	
}

$fechaMax = date("Y-m-d",strtotime($fechaDefault."+ 1 month"));
$fechaMax = date("Y-m-d",strtotime($fechaMax."- 1 days"));

?>
<input class="form-control" type="date" name="fecha" min="<?=$fechaMin;?>" value="<?=$fechaMin;?>" max="<?=$fechaMax;?>" id="fecha" required/>
