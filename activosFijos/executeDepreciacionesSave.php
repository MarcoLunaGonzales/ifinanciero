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

$fecha_actual_x=date($gestion."-".$mes."-01");//
$fecha_actual_x=date('Y-m-d',strtotime($fecha_actual_x.'+1 month'));
$fecha_actual=date('Y-m-d',strtotime($fecha_actual_x.'-1 day'));//
//verificamos si esa fecha no se registro aun
set_time_limit(3000);
$sql="SELECT count(codigo)as contador from mesdepreciaciones where gestion=$gestion and mes=$mes";
$stmt = $dbh->prepare($sql);
$stmt->execute();
$result=$stmt->fetch();
$codigo_aux=$result['contador'];
 // $codigo_aux=0;//codigo temporal**********
if($codigo_aux==0){
	$sqlValidacionUFV="SELECT mes,gestion FROM mesdepreciaciones order by codigo desc limit 0,1";	
	$stmtValidacionUfv= $dbh->prepare($sqlValidacionUFV);
	$stmtValidacionUfv->execute();
	$resultValidacionUFV=$stmtValidacionUfv->fetch();
	$gestionValidacionUFV=$resultValidacionUFV['gestion'];
	$mesValidacionUFV=$resultValidacionUFV['mes'];
	$fechaInicioDepreciacion=$gestionValidacionUFV."-".$mesValidacionUFV."-01";	
	$fechaFinComparacion=$gestion."-".$mes."-01";
	$fechaInicioDepreciacion=date('Y-m-d',strtotime($fechaInicioDepreciacion.'+1 month'));
	$fechaInicioDepreciacion=date('Y-m-d',strtotime($fechaInicioDepreciacion.'-1 day'));//	
	$numeroMesesDepreciacion=diferenciaMeses($fechaInicioDepreciacion,$fechaFinComparacion);
	$fechaFinalDepreciacion=date('Y-m-d',strtotime($fechaFinComparacion.'+1 month'));
	$fechaFinalDepreciacion=date('Y-m-d',strtotime($fechaFinalDepreciacion.'-1 day'));//necesitamos el 

	$ufvInicio=obtenerUFV($fechaInicioDepreciacion);
    $ufvFinal=obtenerUFV($fechaFinalDepreciacion);
    echo $fechaInicioDepreciacion."__".$fechaFinalDepreciacion."<br>";
    echo $ufvInicio."-".$ufvFinal."<br>";
    $valorUFVActualizacion=0;
    $respuestaDepreciacion=1;//verifica la ufv
    if($ufvInicio>0 && $ufvFinal>0){
    	//REALIZAR PROCESO DE DEPRECIACION INDIVIDUAL POR CADA ITEM
		//*****borrado temporalmente
		$sqlInsertCab="INSERT into mesdepreciaciones (mes, gestion, estado) values ('$mes', '$gestion', '1')";
		$stmtInsertCab = $dbh->prepare($sqlInsertCab);
		$stmtInsertCab -> execute();
		$ultimoIdInsertado = $dbh->lastInsertId();
		$sqlActivos="SELECT a.codigo,a.cod_depreciaciones, a.valorinicial, ifnull(a.depreciacionacumulada,0)as depreciacionacumulada, a.cantidad_meses_depreciacion as vidautil,a.vidautilmeses_restante, a.fecha_iniciodepreciacion  from activosfijos a where a.tipo_af=1 and a.cod_estadoactivofijo=1 and cod_unidadorganizacional <> 3000 ";
		// 829,9,10,5,8,270 // 271,272,2692 // and cod_unidadorganizacional in (10) and a.codigo in (2471,2472,2473)
		//echo $sqlActivos;
		$stmtActivos = $dbh->prepare($sqlActivos);
		$stmtActivos -> execute();	
		while ($resultActivos = $stmtActivos->fetch(PDO::FETCH_ASSOC)) {
			$codActivo=$resultActivos["codigo"];
			$cod_depreciaciones=$resultActivos["cod_depreciaciones"];
			$valorInicial=$resultActivos["valorinicial"];
			$depreciacionAcum=$resultActivos["depreciacionacumulada"];
			$vidautil=$resultActivos["vidautil"];
			$vidautilmeses_restante_af=$resultActivos["vidautilmeses_restante"];
			$fechaIniDepreciacionBD=$resultActivos["fecha_iniciodepreciacion"];
			//echo "ACTIVO FIJO: ".$codActivo." ".$valorInicial." ".$depreciacionAcum." ".$vidautil." ".$fechaIniDepreciacionBD."";
			//VALIDAMOS SI EL ACTIVO YA FUE DEPRECIADO
			$sqlValidacion="SELECT count(*) as contador from mesdepreciaciones d, mesdepreciaciones_detalle dd where d.codigo=dd.cod_mesdepreciaciones and dd.cod_activosfijos=$codActivo order by dd.codigo desc limit 0,1;";
			//echo $sqlValidacion;
			$stmtValidacion= $dbh->prepare($sqlValidacion);
			$stmtValidacion->execute();
			$resultValidacion=$stmtValidacion->fetch();
			$contadorValidacion=$resultValidacion['contador'];		
			$sw_nuevo=0;//identificador de ACtivo nuevo //1 nuevo 0 no;
			if($contadorValidacion==0){//verifica si ya tuvo alguna depreciacion anterior
				//DEPRECIAMOS DESDE LA FECHA DE INICIO DE DEPRECIACION //para nuevos desde la fecha que se introdujo
				list($yearIni, $mesIni, $dayIni) = explode('-', $fechaIniDepreciacionBD);
				$sw_nuevo=verificar_si_nuevo($codActivo);//1 si 0 no;
				$fechaIniComparacion=$yearIni."-".$mesIni."-01";//enero
				$fechaFinComparacion=$gestion."-".$mes."-01";
				//SACAMOS EL INICIO DE LA DEPRECIACION Y EL ULTIMO DIA DEL MES SELECCIONADO EN EL FILTRO
				if($sw_nuevo==1){				
					$fechaInicioDepreciacion=$fechaIniDepreciacionBD;//se deprecia desde que se insertó el activo
					$numeroMesesDepreciacion=diferenciaMeses($fechaIniComparacion,$fechaFinComparacion);
				}else{
					$fechaInicioDepreciacion=date('Y-m-d',strtotime($fechaIniDepreciacionBD.'-1 day'));//
					$numeroMesesDepreciacion=diferenciaMeses($fechaIniDepreciacionBD,$fechaFinComparacion);	
				}
				$fechaFinalDepreciacion=date('Y-m-d',strtotime($fechaFinComparacion.'+1 month'));//para el dia final a la fecha
				$fechaFinalDepreciacion=date('Y-m-d',strtotime($fechaFinalDepreciacion.'-1 day')); //
				if($vidautilmeses_restante_af<($numeroMesesDepreciacion+1)){//saca  hasta la fecha que llega
					$vidautilmeses_restante_af=$vidautilmeses_restante_af+1;//aumenamos 1 ya que descontaremos un dia
					$fechaInicioDepreciacion_x=date('Y-m-01',strtotime($fechaInicioDepreciacion));
					$fechaIniComparacion_x = strtotime($fechaInicioDepreciacion_x);
	  				$fechaFinalDepreciacion = date("Y-m-01", strtotime("+$vidautilmeses_restante_af month", $fechaIniComparacion_x));
	  				$fechaFinalDepreciacion=date('Y-m-d',strtotime($fechaFinalDepreciacion.'-1 day'));	//31 de enero
	  				$vidautilmeses_restante_af=$vidautilmeses_restante_af-1;//ponemos en estado normal
	  				// echo $fechaFinalDepreciacion."<br>";
				}
				if($codActivo==1795){//solo para apertura de la depreciacion del sistema, activo con datos incorrectos.
		            $sw_nuevo=$codActivo;
		        }
				//echo "fechas depre: ".$fechaInicioDepreciacion." ".$fechaFinalDepreciacion;			
				$respuestaDepreciacion=correrDepreciacion($codActivo,$fechaInicioDepreciacion,$fechaFinalDepreciacion,$valorInicial,$depreciacionAcum,$numeroMesesDepreciacion,$vidautil,$ultimoIdInsertado,$vidautilmeses_restante_af,$cod_depreciaciones,$fecha_actual,$sw_nuevo);
				if($respuestaDepreciacion==2){break;}
			}else{			
				$sqlValidacion2="SELECT d.gestion, d.mes, dd.d4_valoractualizado, dd.d9_depreciacionacumuladaactual,dd.d11_vidarestante from mesdepreciaciones d, mesdepreciaciones_detalle dd where d.codigo=dd.cod_mesdepreciaciones and dd.cod_activosfijos=$codActivo order by dd.codigo desc limit 0,1;";
				//echo $sqlValidacion2;
				$stmtValidacion2= $dbh->prepare($sqlValidacion2);
				$stmtValidacion2->execute();
				$resultValidacion2=$stmtValidacion2->fetch();			
				$gestionDepreciacion=$resultValidacion2['gestion'];
				$mesDepreciacion=$resultValidacion2['mes'];
				$valorInicialDepreciado=$resultValidacion2['d4_valoractualizado'];
				$depreciacionAcumDepreciado=$resultValidacion2['d9_depreciacionacumuladaactual'];
				$vidarestante=$resultValidacion2['d11_vidarestante'];

				$fechaInicioDepreciacion=$gestionDepreciacion."-".$mesDepreciacion."-01";
				// echo $fechaInicioDepreciacion;
				$fechaFinComparacion=$gestion."-".$mes."-01";
				//DEPRECIAMOS DESDE LA ULTIMA DEPRECIACION	
				$fechaInicioDepreciacion=date('Y-m-d',strtotime($fechaInicioDepreciacion.'+1 month'));
				$fechaInicioDepreciacion=date('Y-m-d',strtotime($fechaInicioDepreciacion.'-1 day'));//
				//echo $fechaInicioDepreciacion."##".$fechaFinComparacion;
				$numeroMesesDepreciacion=diferenciaMeses($fechaInicioDepreciacion,$fechaFinComparacion);
				$fechaFinalDepreciacion=date('Y-m-d',strtotime($fechaFinComparacion.'+1 month'));
				$fechaFinalDepreciacion=date('Y-m-d',strtotime($fechaFinalDepreciacion.'-1 day'));//necesitamos el ultimo dìa de la fecha

				$vidautilmeses_restante_af=$vidarestante;
				if($vidautilmeses_restante_af<($numeroMesesDepreciacion+1)){//
					$vidautilmeses_restante_af=$vidautilmeses_restante_af+1;//aumenamos 1 ya que descontaremos un dia
					$fechaInicioDepreciacion_x=date('Y-m-01',strtotime($fechaInicioDepreciacion));
					$fechaIniComparacion_x = strtotime($fechaInicioDepreciacion_x);
	  				$fechaFinalDepreciacion = date("Y-m-d", strtotime("+$vidautilmeses_restante_af month", $fechaIniComparacion_x));
	  				$fechaFinalDepreciacion=date('Y-m-d',strtotime($fechaFinalDepreciacion.'-1 day'));	
	  				$vidautilmeses_restante_af=$vidautilmeses_restante_af-1;//ponemos en estado normal
				}
				// echo "   *** FECHAS COMPARACION: ".$fechaInicioDepreciacion." ".$fechaFinalDepreciacion;
				// echo "    *** NRO MESES: ".$numeroMesesDepreciacion."<br>";
				$respuestaDepreciacion=correrDepreciacion($codActivo,$fechaInicioDepreciacion,$fechaFinalDepreciacion,$valorInicialDepreciado,$depreciacionAcumDepreciado,$numeroMesesDepreciacion,$vidautil,$ultimoIdInsertado,$vidautilmeses_restante_af,$cod_depreciaciones,$fecha_actual,$sw_nuevo);
				if($respuestaDepreciacion==2){break;}
			}
		}
		if($respuestaDepreciacion==1){
			$flagSuccess=true;
			showAlertSuccessErrorDepreciaciones($flagSuccess,$urlList7);
		}else{
			//eliminar el proceso ya que hubo error de UFV.
			$sqlultim="DELETE from mesdepreciaciones_detalle where cod_mesdepreciaciones=$ultimoIdInsertado;DELETE from mesdepreciaciones where codigo=$ultimoIdInsertado;";			
			$stmtUltim = $dbh->prepare($sqlultim);
			$stmtUltim -> execute();
			$flagSuccess=false;
			showAlertSuccessErrorDepreciaciones4($flagSuccess,$urlList7);
		}
    }else{
    	$flagSuccess=false;
		showAlertSuccessErrorDepreciaciones3($flagSuccess,$urlList7);
    }

}else{
	$flagSuccess=false;
	showAlertSuccessErrorDepreciaciones($flagSuccess,$urlRegistrar7);
}
	

?>