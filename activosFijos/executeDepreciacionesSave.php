<?php

require_once 'layouts/bodylogin.php';
require_once 'conexion.php';
require_once 'functions.php';
require_once 'configModule.php';
require_once 'functionsDepreciacion.php';

$dbh = new Conexion();


//$cod_empresa=$_POST["cod_empresa"];
$mes=$_POST["mes"];
$gestion=$_POST["gestion"];
//verificamos si esa fecha no se registro aun


$sql="SELECT count(codigo)as contador from mesdepreciaciones where gestion=$gestion and mes=$mes";
$stmt = $dbh->prepare($sql);
$stmt->execute();
$result=$stmt->fetch();
$codigo_aux=$result['contador'];
if($codigo_aux==0){ // REALIZAR PROCESO DE DEPRECIACION INDIVIDUAL POR CADA ITEM

	//INSERTAMOS LA CABECERA DEL EJERCICIO
	$sqlInsertCab="INSERT into mesdepreciaciones (mes, gestion, estado) values ('$mes', '$gestion', '1')";
	$stmtInsertCab = $dbh->prepare($sqlInsertCab);
	$stmtInsertCab -> execute();

	$ultimoIdInsertado = $dbh->lastInsertId();


	$sqlActivos="SELECT a.codigo, a.valorinicial, ifnull(a.depreciacionacumulada,0)as depreciacionacumulada, 
	(select d.vida_util from depreciaciones d where d.codigo=a.cod_depreciaciones)vidautil, a.fecha_iniciodepreciacion  from activosfijos a where a.tipo_af=1;";
	//echo $sqlActivos;
	$stmtActivos = $dbh->prepare($sqlActivos);
	$stmtActivos->execute();
	$banderaUFVError=0;
	while ($resultActivos = $stmtActivos->fetch(PDO::FETCH_ASSOC)) {
		$codActivo=$resultActivos["codigo"];
		$valorInicial=$resultActivos["valorinicial"];
		$depreciacionAcum=$resultActivos["depreciacionacumulada"];
		$vidautil=$resultActivos["vidautil"];
		$fechaIniDepreciacionBD=$resultActivos["fecha_iniciodepreciacion"];

		echo "ACTIVO FIJO: ".$codActivo." ".$valorInicial." ".$depreciacionAcum." ".$vidautil." ".$fechaIniDepreciacionBD."";

		//VALIDAMOS SI EL ACTIVO YA FUE DEPRECIADO
		$sqlValidacion="SELECT count(*) as contador, d.gestion, d.mes, dd.d4_valoractualizado, dd.d9_depreciacionacumuladaactual from mesdepreciaciones d, mesdepreciaciones_detalle dd where 
		d.codigo=dd.cod_mesdepreciaciones and dd.cod_activosfijos=$codActivo order by dd.codigo desc limit 0,1;";
		//echo $sqlValidacion;
		$stmtValidacion= $dbh->prepare($sqlValidacion);
		$stmtValidacion->execute();
		$resultValidacion=$stmtValidacion->fetch();
		$contadorValidacion=$resultValidacion['contador'];
		$gestionDepreciacion=$resultValidacion['gestion'];
		$mesDepreciacion=$resultValidacion['mes'];
		$valorInicialDepreciado=$resultValidacion['d4_valoractualizado'];
		$depreciacionAcumDepreciado=$resultValidacion['d9_depreciacionacumuladaactual'];

		if($contadorValidacion==0){
			//DEPRECIAMOS DESDE LA FECHA DE INICIO DE DEPRECIACION
			list($yearIni, $mesIni, $dayIni) = explode('-', $fechaIniDepreciacionBD);
			$fechaIniComparacion=$yearIni."-".$mesIni."-01";
			$fechaFinComparacion=$gestion."-".$mes."-01";

			$numeroMesesDepreciacion=diferenciaMeses($fechaIniComparacion,$fechaFinComparacion);
			
			echo "    *** NRO MESES: ".$numeroMesesDepreciacion."<br>";

			//SACAMOS EL INICIO DE LA DEPRECIACION Y EL ULTIMO DIA DEL MES SELECCIONADO EN EL FILTRO
			$fechaInicioDepreciacion=date('Y-m-d',strtotime($fechaIniDepreciacionBD.'-1 day'));
			$fechaFinalDepreciacion=date('Y-m-d',strtotime($fechaFinComparacion.'+1 month'));
			$fechaFinalDepreciacion=date('Y-m-d',strtotime($fechaFinalDepreciacion.'-1 day'));

			//echo "fechas depre: ".$fechaInicioDepreciacion." ".$fechaFinalDepreciacion;

			$respuestaDepreciacion=correrDepreciacion($codActivo,$fechaInicioDepreciacion,$fechaFinalDepreciacion,$valorInicial,$depreciacionAcum,$numeroMesesDepreciacion,$vidautil,$ultimoIdInsertado);

			

		}else{
			//DEPRECIAMOS DESDE LA ULTIMA DEPRECIACION
			$fechaIniComparacion=$gestionDepreciacion."-".$mesDepreciacion."-01";
			$fechaIniComparacion=date('Y-m-d',strtotime($fechaIniComparacion.'+1 month'));
			$fechaFinComparacion=$gestion."-".$mes."-01";

			echo "   *** FECHAS COMPARACION: ".$fechaIniComparacion." ".$fechaFinComparacion;
			$numeroMesesDepreciacion=diferenciaMeses($fechaIniComparacion,$fechaFinComparacion);

			echo "    *** NRO MESES: ".$numeroMesesDepreciacion."<br>";

			$fechaInicioDepreciacion=$gestionDepreciacion."-".$mesDepreciacion."-01";
			$fechaInicioDepreciacion=date('Y-m-d',strtotime($fechaInicioDepreciacion.'+1 month'));
			$fechaFinalDepreciacion=date('Y-m-d',strtotime($fechaFinComparacion.'+1 month'));
			$fechaFinalDepreciacion=date('Y-m-d',strtotime($fechaFinalDepreciacion.'-1 day'));

			//echo "fechas depre: ".$fechaInicioDepreciacion." ".$fechaFinalDepreciacion;

			$respuestaDepreciacion=correrDepreciacion($codActivo,$fechaInicioDepreciacion,$fechaFinalDepreciacion,$valorInicialDepreciado,$depreciacionAcumDepreciado,$numeroMesesDepreciacion,$vidautil,$ultimoIdInsertado);
		}


	}
	if($banderaUFVError==1){
		echo "DATOS DE UFV INCOMPLETOS.";
	}

}else{
	$flagSuccess=false;
	showAlertSuccessErrorDepreciaciones($flagSuccess,$urlRegistrar7);
}
	

?>
