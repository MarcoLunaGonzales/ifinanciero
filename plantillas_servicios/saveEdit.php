<?php

require_once '../layouts/bodylogin.php';
require_once '../conexion.php';
require_once '../functions.php';
require_once '../functionsGeneral.php';
require_once 'configModule.php';

$dbh = new Conexion();

$cantidadFilas=$_POST["cantidad_filas"];
$detalles= json_decode($_POST['detalles']);
session_start();

$globalUser=$_SESSION["globalUser"];
$globalGestion=$_SESSION["globalGestion"];
$globalUnidad=$_SESSION["globalUnidad"];
$globalArea=$_SESSION["globalArea"];
$globalAdmin=$_SESSION["globalAdmin"];

$fechaHoraActual=date("Y-m-d H:i:s");

$codPlantillaServicio=$_POST["cod_plantilla"];
$nombrePlan=$_POST['nombre'];
$abrevPlan=$_POST['abreviatura'];
//$utilidadIbrnocaPlan=$_POST['utilidad_ibnorca'];
//$utilidadFueraPlan=$_POST['utilidad_fuera'];
$cantidadPersonal=$_POST['alumnos_ibnorca'];
$utMinima=$_POST['utilidad_minima'];

$sqlUpdate="UPDATE plantillas_servicios SET  nombre='$nombrePlan',abreviatura='$abrevPlan',cantidad_personal='$cantidadPersonal',utilidad_minima='$utMinima' where codigo=$codPlantillaServicio";
echo $sqlUpdate;
$stmtUpdate = $dbh->prepare($sqlUpdate);
$flagSuccess=$stmtUpdate->execute();
//guardar las ediciones
for ($i=1;$i<=$cantidadFilas;$i++){

	$tipo_costo=$_POST["tipo_costo".$i];

	if($tipo_costo!=0 || $tipo_costo!=""){
	$nombreGrupo=$_POST["nombre_grupo".$i];
	$abreviaturaGrupo=$_POST["abreviatura_grupo".$i];
	$data[$i-1][0]=$tipo_costo;
    $data[$i-1][1]=$_POST["nombre_grupo".$i]; 
    $data[$i-1][2]=$_POST["abreviatura_grupo".$i]; 
    //$dataInsert 	
	}
} 
$cab[0]="cod_tiposervicio";
$cab[1]="nombre";
$cab[2]="abreviatura";

//$codComprobanteDetalle=obtenerCodigoComprobanteDetalle();
$comDet=contarPlantillaServicio($codPlantillaServicio);
$comDet->bindColumn('total', $contador);
while ($row = $comDet->fetch(PDO::FETCH_BOUND)) {
 $cont1=$contador;
}

$stmt1 = obtenerPlantillaServicio($codPlantillaServicio);
editarPlantillaServicio($codPlantillaServicio,'cod_plantillaservicio',$cont1,$cantidadFilas,$stmt1,'plantillas_gruposervicio',$cab,$data,$detalles); 

if($flagSuccess==true){
	showAlertSuccessError(true,"../".$urlList);	
}else{
	showAlertSuccessError(false,"../".$urlList);
}


?>
