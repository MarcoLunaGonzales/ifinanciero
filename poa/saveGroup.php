<?php

require_once '../layouts/bodylogin.php';
require_once '../conexion.php';
require_once '../functions.php';

//print_r($_POST);

$dbh = new Conexion();

//SACAMOS LA CONFIGURACION PARA REDIRECCIONAR EL PON
$stmt = $dbh->prepare("SELECT valor_configuracion FROM configuraciones where id_configuracion=6");
$stmt->execute();
$codigoIndicadorPON=0;
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
  $codigoIndicadorPON=$row['valor_configuracion'];
}

$codigoIndicador=$_POST["cod_indicador"];
$cantidadFilas=$_POST["cantidad_filas"];

$codigoUnidad=$_POST["codigoUnidad"];
$codigoArea=$_POST["codigoArea"];


$table="actividades_poa";
$urlRedirect="../index.php?opcion=listActividadesPOA&codigo=$codigoIndicador&codigoPON=$codigoIndicadorPON&area=0&unidad=0";

session_start();

$orden="1";
$codEstado="1";
$globalUser=$_SESSION["globalUser"];
$globalGestion=$_SESSION["globalGestion"];
$globalUnidad=$_SESSION["globalUnidad"];
$globalArea=$_SESSION["globalArea"];
$globalAdmin=$_SESSION["globalAdmin"];

$fechaHoraActual=date("Y-m-d H:i:s");

for ($i=1;$i<=$cantidadFilas;$i++){ 	    	
	// Prepare
	$nombre=$_POST["actividad".$i];
	//echo $i." area: ".$area." <br>";

	if($nombre!=0 || $nombre!=""){
		$codigo=$_POST["codigo".$i];
		$nombre=$_POST["actividad".$i];
		$normaPriorizada=$_POST["norma_priorizada".$i];
		$norma=$_POST["norma".$i];
		$productoEsperado=$_POST["producto_esperado".$i];
		$tipoSeguimiento=$_POST["tipo_seguimiento".$i];
		$tipoResultado="1";//valor numerico por defecto
		$datoClasificador=$_POST["clasificador".$i];
		$observaciones=$_POST["observaciones".$i];
		$hito=$_POST["hito".$i];
		$claveIndicador=$_POST["clave_indicador".$i];


		$codigoPOA=0;
		if($codigo==0){
			$stmtCod = $dbh->prepare("SELECT IFNULL(max(a.codigo)+1,1)as codigo from actividades_poa a");
			$stmtCod->execute();
			while ($rowCod = $stmtCod->fetch(PDO::FETCH_ASSOC)) {
			  $codigoPOA=$rowCod['codigo'];
			}			
		}else{
			$codigoPOA=$codigo;
		}

		//BORRAMOS LA TABLA
		$sqlDelete="";
		$sqlDelete="DELETE from $table where codigo='$codigoPOA'";
		$stmtDel = $dbh->prepare($sqlDelete);
		$flagSuccess=$stmtDel->execute();

		$ordenPOA=obtieneOrdenPOA($codigoIndicador,$codigoUnidad,$codigoArea);
		//echo $ordenPOA."<br>";
		//SACAMOS EL ESTADO DEL POA PARA LA GESTION
		$actividadExtra=0;
		$codEstadoPOAGestion=estadoPOAGestion($globalGestion);
		if($codEstadoPOAGestion==3){
			$actividadExtra=1;
		}
		$stmt = $dbh->prepare("INSERT INTO $table (codigo, orden, nombre, cod_gestion, cod_normapriorizada, cod_norma, cod_tiposeguimiento, producto_esperado, cod_indicador, cod_unidadorganizacional, cod_area, cod_estado, created_at, created_by, cod_tiporesultado, cod_datoclasificador, actividad_extra, observaciones, cod_hito, clave_indicador) VALUES (:codigo, :orden, :nombre, :cod_gestion, :cod_normapriorizada, :cod_norma, :cod_tiposeguimiento, :producto_esperado, :cod_indicador, :cod_unidadorganizacional, :cod_area, :cod_estado, :created_at, :created_by, :cod_tiporesultado, :cod_datoclasificador, :actividad_extra,:observaciones,:cod_hito,:clave_indicador)");
		// Bind
		$stmt->bindParam(':codigo', $codigoPOA);
		$stmt->bindParam(':orden', $ordenPOA);
		$stmt->bindParam(':nombre', $nombre);
		$stmt->bindParam(':cod_gestion', $globalGestion);
		$stmt->bindParam(':cod_normapriorizada', $normaPriorizada);
		$stmt->bindParam(':cod_norma', $norma);
		$stmt->bindParam(':cod_tiposeguimiento', $tipoSeguimiento);
		$stmt->bindParam(':producto_esperado', $productoEsperado);
		$stmt->bindParam(':cod_indicador', $codigoIndicador);
		$stmt->bindParam(':cod_unidadorganizacional', $codigoUnidad);
		$stmt->bindParam(':cod_area', $codigoArea);
		$stmt->bindParam(':cod_estado', $codEstado);
		$stmt->bindParam(':created_at', $fechaHoraActual);
		$stmt->bindParam(':created_by', $globalUser);
		$stmt->bindParam(':cod_tiporesultado', $tipoResultado);
		$stmt->bindParam(':cod_datoclasificador', $datoClasificador);
		$stmt->bindParam(':actividad_extra', $actividadExtra);
		$stmt->bindParam(':observaciones', $observaciones);
		$stmt->bindParam(':cod_hito', $hito);
		$stmt->bindParam(':clave_indicador', $claveIndicador);
		
		$flagSuccess=$stmt->execute();	
	}
} 


if($flagSuccess==true){
	showAlertSuccessError(true,$urlRedirect);	
}else{
	showAlertSuccessError(false,$urlRedirect);
}


?>
