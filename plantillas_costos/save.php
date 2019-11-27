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


for ($i=1;$i<=$cantidadFilas;$i++){ 	    	
	$tipo_costo=$_POST["tipo_costo".$i];

	if($tipo_costo!=0 || $tipo_costo!=""){
		$nombreGrupo=$_POST["nombre_grupo".$i];
		$abreviaturaGrupo=$_POST["abreviatura_grupo".$i];
        $codPlantillaGrupo=obtenerCodigoPlantillaGrupo();
        
		$sqlDetalle="INSERT INTO plantillas_gruposcosto (codigo,cod_tipocosto, nombre, abreviatura, cod_plantillacosto) VALUES ('$codPlantillaGrupo','$tipo_costo', '$nombreGrupo', '$abreviaturaGrupo','$codPlantillaCosto')";
		$stmtDetalle = $dbh->prepare($sqlDetalle);
		$flagSuccessDetalle=$stmtDetalle->execute();	

        $nF=cantidadF($detalles[$i-1]);

         for($j=0;$j<$nF;$j++){
         	  $codigo_cuenta=$detalles[$i-1][$j]->codigo_cuenta;
         	  $descripcion_detalle=$detalles[$i-1][$j]->descripcion;	  
         	  $nombre_cuenta=$detalles[$i-1][$j]->cuenta;
         	  $numero_cuenta=$detalles[$i-1][$j]->nro_cuenta;
         	  $tipo_detalle=$detalles[$i-1][$j]->tipo;
         	  $monto_i_detalle=$detalles[$i-1][$j]->monto_i;
         	  $monto_fi_detalle=$detalles[$i-1][$j]->monto_fi;
              $codPlantillaGrupoDetalle=obtenerCodigoPlantillaGrupoDetalle();
		      $sqlDetalle2="INSERT INTO plantillas_grupocostodetalle (codigo,cod_plantillagrupocosto, cod_plancuenta, tipo_calculo, monto_local, monto_externo) VALUES ('$codPlantillaGrupoDetalle','$codPlantillaGrupo', '$codigo_cuenta','$tipo_detalle', '$monto_i_detalle', '$monto_fi_detalle')";
		      $stmtDetalle2 = $dbh->prepare($sqlDetalle2);
		      $flagSuccessDetalle2=$stmtDetalle2->execute();
         }
	}
} 

if($flagSuccessDetalle==true){
	showAlertSuccessError(true,"../".$urlList);	
}else{
	showAlertSuccessError(false,"../".$urlList);
}


?>
