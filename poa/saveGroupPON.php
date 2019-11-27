<?php

require_once '../layouts/bodylogin.php';
require_once '../conexion.php';
require_once '../functions.php';

//print_r($_POST);

$dbh = new Conexion();

//SACAMOS LA CONFIGURACION PARA REDIRECCIONAR EL PON
$codigoIndicadorPON=obtenerCodigoPON();

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
	$modogeneracion=$_POST["modogeneracion".$i];
	//echo $i." area: ".$area." <br>";

	if($modogeneracion!=0 || $modogeneracion!=""){
		$codigo=$_POST["codigo".$i];
		$nombre=$_POST["actividad".$i];
		$comite=$_POST["comite".$i];
		$norma=$_POST["norma".$i];
		$modogeneracion=$_POST["modogeneracion".$i];
		$estadopon=0;
		$personal=$_POST["personal".$i];
		$tipoResultado="1";//valor numerico por defecto
		$tipoSolicitante=$_POST["tipo_solicitante".$i];
		$solicitante=$_POST["solicitante".$i];


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

		$sql="INSERT INTO $table (codigo, orden, nombre, cod_gestion, cod_norma, cod_indicador, cod_unidadorganizacional, cod_area, cod_estado, created_at, created_by, cod_comite, cod_estadopon, cod_modogeneracionpon, cod_personal, actividad_extra, cod_tiposolicitante, solicitante) VALUES (:codigo, :orden, :nombre, :cod_gestion, :cod_norma, :cod_indicador, :cod_unidadorganizacional, :cod_area, :cod_estado, :created_at, :created_by, :cod_comite, :cod_estadopon, :cod_modogeneracionpon, :cod_personal, :actividad_extra, :tipo_solicitante, :solicitante)";
		$stmt = $dbh->prepare($sql);
		
		$values = array(':codigo' => $codigoPOA,
		':orden' => $ordenPOA,
        ':nombre' => $nombre,
        ':cod_gestion' => $globalGestion,
        ':cod_norma' => $norma,
        ':cod_indicador'=> $codigoIndicador,
        ':cod_unidadorganizacional'=> $codigoUnidad,
        ':cod_area' => $codigoArea,
        ':cod_estado' => $codEstado,
        ':created_at' => $fechaHoraActual,
        ':created_by'=> $globalUser,
        ':cod_comite'=> $comite,
        ':cod_estadopon'=> $estadopon,
        ':cod_modogeneracionpon'=> $modogeneracion,
        ':cod_personal'=> $personal,
        ':actividad_extra'=>$actividadExtra,
        ':tipo_solicitante'=>$tipoSolicitante,
        ':solicitante'=>$solicitante
    	);

    	$exQuery=str_replace(array_keys($values), array_values($values), $sql);
    	//echo $exQuery;
		$flagSuccess=$stmt->execute($values);	

		/*$stmt->bindParam(':orden', $i);
		$stmt->bindParam(':nombre', $nombre);
		$stmt->bindParam(':cod_gestion', $globalGestion);
		$stmt->bindParam(':cod_norma', $norma);
		$stmt->bindParam(':cod_indicador', $codigoIndicador);
		$stmt->bindParam(':cod_unidadorganizacional', $codigoUnidad);
		$stmt->bindParam(':cod_area', $codigoArea);
		$stmt->bindParam(':cod_estado', $codEstado);
		$stmt->bindParam(':created_at', $fechaHoraActual);
		$stmt->bindParam(':created_by', $globalUser);
		$stmt->bindParam(':cod_comite', $comite);
		$stmt->bindParam(':cod_estadopon', $estadopon);
		$stmt->bindParam(':cod_modogeneracionpon', $modogeneracion);
		$stmt->bindParam(':cod_personal', $personal);*/

	}
} 

if($flagSuccess==true){
	showAlertSuccessError(true,$urlRedirect);	
}else{
	showAlertSuccessError(false,$urlRedirect);
}


?>
