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

$codPlantillaCosto=$_POST["cod_plantilla"];
$nombrePlan=$_POST['nombre'];
$abrevPlan=$_POST['abreviatura'];
$utilidadIbrnocaPlan=$_POST['utilidad_ibnorca'];
$utilidadFueraPlan=$_POST['utilidad_fuera'];
$alumnosLocalPlan=$_POST['alumnos_ibnorca'];
$alumnosExternoPlan=$_POST['alumnos_fuera'];

$precioPresupuestadoTabla=$_POST['presupuestado_plan'];
$sqlUpdate="UPDATE plantillas_costo SET  nombre='$nombrePlan',abreviatura='$abrevPlan',utilidad_minimalocal='$utilidadIbrnocaPlan',utilidad_minimaexterno='$utilidadFueraPlan',cantidad_alumnoslocal='$alumnosLocalPlan',cantidad_alumnosexterno='$alumnosExternoPlan' where codigo=$codPlantillaCosto";
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
$cab[0]="cod_tipocosto";
$cab[1]="nombre";
$cab[2]="abreviatura";

//$codComprobanteDetalle=obtenerCodigoComprobanteDetalle();
$comDet=contarPlantillaCosto($codPlantillaCosto);
$comDet->bindColumn('total', $contador);
while ($row = $comDet->fetch(PDO::FETCH_BOUND)) {
 $cont1=$contador;
}

$stmt1 = obtenerPlantillaCosto($codPlantillaCosto);
editarPlantillaCosto($codPlantillaCosto,'cod_plantillacosto',$cont1,$cantidadFilas,$stmt1,'plantillas_gruposcosto',$cab,$data,$detalles); 

if($flagSuccess==true){
	showAlertSuccessError(true,"../".$urlList);	
}else{
	showAlertSuccessError(false,"../".$urlList);
}


?>
