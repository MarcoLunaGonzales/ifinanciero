<?php
require_once '../layouts/bodylogin.php';
require_once '../conexion.php';
require_once '../functions.php';
require_once '../functionsGeneral.php';
require_once 'configModule.php';

$dbh = new Conexion();

$cantidadFilas=$_POST["cantidad_filas"];
session_start();

$globalUser=$_SESSION["globalUser"];
$globalGestion=$_SESSION["globalGestion"];
$globalUnidad=$_SESSION["globalUnidad"];
$globalArea=$_SESSION["globalArea"];
$globalAdmin=$_SESSION["globalAdmin"];

$fechaHoraActual=date("Y-m-d H:i:s");

$codPlantillaCosto=$_POST["cod_plantilla"];

$nombrePlan=$_POST['nombre'];
$abrevPlan=$_POST['abreviatura'];
$dias=$_POST['dias_auditoria'];

$sqlUpdate="UPDATE plantillas_servicios SET  nombre='$nombrePlan',abreviatura='$abrevPlan',dias_auditoria='$dias' where codigo=$codPlantillaCosto";

$stmtUpdate = $dbh->prepare($sqlUpdate);
$flagSuccess=$stmtUpdate->execute();

$sqlDelete="DELETE FROM plantillas_servicios_detalle where cod_plantillatcp=$codPlantillaCosto";
$stmtDelete = $dbh->prepare($sqlDelete);
$stmtDelete->execute();

//guardar las ediciones
for ($i=1;$i<=$cantidadFilas;$i++){
	$tipo_costo=$_POST["tipo_costo".$i];

	//$partida=$_POST['codigo_partidadetalle'.$i];
	//$cuenta=$_POST['codigo_cuentadetalle'.$i];
	$partida=$_POST['partida_presupuestaria'.$i];
	$cuenta=$_POST['cuenta_plantilladetalle'.$i];
	$glosa=$_POST['detalle_plantilla'.$i];
	$monto_unitario=$_POST['monto_detalleplantilla'.$i];
	$cantidad=$_POST['cantidad_detalleplantilla'.$i];
	$monto_total=$_POST['monto_total_detalleplantilla'.$i];
	$unidad=$_POST['unidad_detalleplantilla'.$i];
	if($tipo_costo==1){
		$unidad="";
	}
	if($tipo_costo!=0 || $tipo_costo!=""){  
	   $sqlInsert="INSERT INTO plantillas_servicios_detalle (cod_plantillatcp,cod_partidapresupuestaria,cod_cuenta,cod_tipo,glosa,monto_unitario,cantidad,monto_total,unidad,cod_estadoreferencial,habilitado)
	   VALUES('$codPlantillaCosto','$partida','$cuenta','$tipo_costo','$glosa','$monto_unitario','$cantidad','$monto_total','$unidad',1,1)";
       $stmtInsert = $dbh->prepare($sqlInsert);
       $stmtInsert->execute();
	}
} 

if($flagSuccess==true){
	showAlertSuccessError(true,"../".$urlList);	
}else{
	showAlertSuccessError(false,"../".$urlList);
}


?>
