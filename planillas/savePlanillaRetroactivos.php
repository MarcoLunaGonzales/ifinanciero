<?php
require_once '../conexion.php';
require_once '../functions.php';
require_once '../rrhh/configModule.php';
require_once '../functionsGeneral.php';

$result_x=0;
$dbh = new Conexion();

 error_reporting(E_ALL);
 ini_set('display_errors', '1');

$flagSuccessIP="";
// session_start();
// $globalUser=$_SESSION["globalAdmin"];
//RECIBIMOS LAS VARIABLES
$cod_planilla=$_POST['cod_planilla'];
$cod_estadoplanilla=$_POST['sw'];
$sw=$_POST['sw'];

$sql="SELECT (select g.nombre from gestiones g where g.codigo=p.cod_gestion)as gestion,p.cod_gestion 
	from planillas_retroactivos p
where p.codigo=$cod_planilla";
// echo $sql;
$stmtDatosPlanilla = $dbh->prepare($sql);
$stmtDatosPlanilla->execute();
$resultDatosPlanilla =  $stmtDatosPlanilla->fetch();
$gestion_x = $resultDatosPlanilla['gestion'];
$cod_gestion_x = $resultDatosPlanilla['cod_gestion'];

$fecha_planilla_1_aux=$gestion_x."-01-01";//primer dia de enero
$fecha_planilla_2_aux=$gestion_x."-02-01";
$fecha_planilla_3_aux=$gestion_x."-03-01";
$fecha_planilla_4_aux=$gestion_x."-04-01";

$fecha_planilla_1=date("Y-m-t", strtotime($fecha_planilla_1_aux));//ultimo dia de enero
$fecha_planilla_2=date("Y-m-t", strtotime($fecha_planilla_2_aux));
$fecha_planilla_3=date("Y-m-t", strtotime($fecha_planilla_3_aux));
$fecha_planilla_4=date("Y-m-t", strtotime($fecha_planilla_4_aux));

//rango de fecha para personal retirado
$fecha_inicio=$fecha_planilla_1_aux;
$fecha_final=$fecha_planilla_4;


$mes1 = 1; //enero
$mes2 = 2; //febrero
$mes3 = 3; //marzo
$mes4 = 4; //abril
$minimo_salarial=obtenerValorConfiguracionPlanillas(1);
$minimo_salarial_anterior=obtenerValorConfiguracionPlanillas(31);
$dias_del_mes=30;
if($sw==2 || $sw==1){//procesar o reprocesar planilla
	$cod_planilla_1=obtener_id_planilla($cod_gestion_x,$mes1);
	$cod_planilla_2=obtener_id_planilla($cod_gestion_x,$mes2);
	$cod_planilla_3=obtener_id_planilla($cod_gestion_x,$mes3);
	$cod_planilla_4=obtener_id_planilla($cod_gestion_x,$mes4);

	if($sw==2){//estado procesado
		//actualizamos estado
		$stmtU = $dbh->prepare("UPDATE planillas_retroactivos 
		set cod_estadoplanilla=2
		where codigo=$cod_planilla");
		$flagSuccess=$stmtU->execute();
	}
	$stmtDelete = $dbh->prepare("DELETE FROM planillas_retroactivos_detalle where cod_planilla=$cod_planilla");
	$stmtDelete->execute();	
	
	//============select del personal
	$sql="SELECT 1 as orden,p.identificacion,p.codigo,p.haber_basico,p.haber_basico_anterior,p.cod_tipoafp,p.ing_contr as ing_planilla,p.cuenta_bancaria,p.cod_area,p.cod_unidadorganizacional,a.nombre as area,p.paterno,'' as retiro_planilla
	from personal p join areas a on p.cod_area=a.codigo
	where p.cod_estadoreferencial=1 and p.cod_estadopersonal=1 and p.ing_contr <= '$fecha_final'
	UNION
 	select 2 as orden,p.identificacion,p.codigo,p.haber_basico,p.haber_basico_anterior,p.cod_tipoafp,p.ing_contr as ing_planilla,p.cuenta_bancaria,p.cod_area,p.cod_unidadorganizacional,'PERSONAL RETIRADO' as area,p.paterno,pr.fecha_retiro as retiro_planilla
	from personal p join personal_retiros pr on p.codigo=pr.cod_personal 
	join areas a on p.cod_area=a.codigo
	where pr.fecha_retiro BETWEEN '$fecha_inicio' and '$fecha_final' 
	order by orden,retiro_planilla,cod_unidadorganizacional,area,paterno";

	// echo $sql;

	$stmtPersonal = $dbh->prepare($sql);
	$stmtPersonal->execute();
	$stmtPersonal->bindColumn('identificacion', $identificacion);
	$stmtPersonal->bindColumn('codigo', $cod_personal);
	$stmtPersonal->bindColumn('haber_basico', $haber_basico_nuevo);
	$stmtPersonal->bindColumn('haber_basico_anterior', $haber_basico_anterior);
	$stmtPersonal->bindColumn('cod_tipoafp', $cod_tipoafp);
	$stmtPersonal->bindColumn('ing_planilla', $ing_planilla);
	$stmtPersonal->bindColumn('retiro_planilla', $retiro_planilla);
	//$stmtPersonal->bindColumn('cuenta_habilitada', $cuenta_habilitada);
	$stmtPersonal->bindColumn('cuenta_bancaria', $cuenta_bancaria);
	$stmtPersonal->bindColumn('cod_area', $cod_area);
	$stmtPersonal->bindColumn('cod_unidadorganizacional', $cod_unidadorganizacional);
	// $stmtPersonal->bindColumn('turno', $turno);
	$index=1;
	while ($rowC = $stmtPersonal->fetch()) 
	{

		if($cuenta_bancaria>0){
		 	$cuenta_habilitada=1;
		}else{
		 	$cuenta_habilitada=0;
		}

		// $ing_planilla=$ing_planilla;
		if($haber_basico_anterior==null || $haber_basico_anterior==""){
			$haber_basico_anterior=$haber_basico_nuevo;
		}

		$datos_planilla1=explode("@@@", obtenerdatos_planilla($cod_personal,$cod_planilla_1));
		$datos_planilla2=explode("@@@", obtenerdatos_planilla($cod_personal,$cod_planilla_2));
		$datos_planilla3=explode("@@@", obtenerdatos_planilla($cod_personal,$cod_planilla_3));
		$datos_planilla4=explode("@@@", obtenerdatos_planilla($cod_personal,$cod_planilla_4));
		
		// var_dump($datos_planilla1);
		// echo "<br><br>";
		// var_dump($datos_planilla2);
		// echo "<br><br>";
		// var_dump($datos_planilla3);
		// echo "<br><br>";
		// var_dump($datos_planilla4);
		// echo "<br><br>";


		$haber_basico1=$datos_planilla1[0];
		$haber_basico2=$datos_planilla2[0];
		$haber_basico3=$datos_planilla3[0];
		$haber_basico4=$datos_planilla4[0];

		$bono_antiguedad1=$datos_planilla1[1];
		$bono_antiguedad2=$datos_planilla2[1];
		$bono_antiguedad3=$datos_planilla3[1];
		$bono_antiguedad4=$datos_planilla4[1];

		$dias_trabajados1=$datos_planilla1[4];
		$dias_trabajados2=$datos_planilla2[4];
		$dias_trabajados3=$datos_planilla3[4];
		$dias_trabajados4=$datos_planilla4[4];

		//* ini bono academ
		$por_bonoacademico1=$datos_planilla1[5];
		$por_bonoacademico2=$datos_planilla2[5];
		$por_bonoacademico3=$datos_planilla3[5];
		$por_bonoacademico4=$datos_planilla4[5];

		$bonoacademico1_ant=0;
		$bonoacademico2_ant=0;
		$bonoacademico3_ant=0;
		$bonoacademico4_ant=0;

		$bonoacademico1_nuevo=0;
		$bonoacademico2_nuevo=0;
		$bonoacademico3_nuevo=0;
		$bonoacademico4_nuevo=0;

		if($por_bonoacademico1>0){
			$bonoacademico1_ant=$por_bonoacademico1*$minimo_salarial_anterior/100;
			$bonoacademico1_nuevo=$por_bonoacademico1*$minimo_salarial/100;
			//echo "************ $identificacion $bonoacademico1_ant $bonoacademico1_nuevo <br>";
		}
		if($por_bonoacademico2>0){
			$bonoacademico2_ant=$por_bonoacademico2*$minimo_salarial_anterior/100;
			$bonoacademico2_nuevo=$por_bonoacademico2*$minimo_salarial/100;
			//echo "$bonoacademico2_ant $bonoacademico2_nuevo <br>";
		}
		if($por_bonoacademico3>0){
			$bonoacademico3_ant=$por_bonoacademico3*$minimo_salarial_anterior/100;
			$bonoacademico3_nuevo=$por_bonoacademico3*$minimo_salarial/100;
			//echo "$bonoacademico3_ant $bonoacademico3_nuevo <br>";
		}
		if($por_bonoacademico4>0){
			$bonoacademico4_ant=$por_bonoacademico4*$minimo_salarial_anterior/100;
			$bonoacademico4_nuevo=$por_bonoacademico4*$minimo_salarial/100;		
			//echo "$bonoacademico4_ant $bonoacademico4_nuevo <br>";
		}		
		//* fin bono acade

		$totalGanadoMes1_ant=$haber_basico1+$bono_antiguedad1+$bonoacademico1_ant;
		$totalGanadoMes2_ant=$haber_basico2+$bono_antiguedad2+$bonoacademico2_ant;
		$totalGanadoMes3_ant=$haber_basico3+$bono_antiguedad3+$bonoacademico3_ant;
		$totalGanadoMes4_ant=$haber_basico4+$bono_antiguedad4+$bonoacademico4_ant;


		$retroactivo_enero=0;
		$retroactivo_febrero=0;
		$retroactivo_marzo=0;
		$retroactivo_abril=0;
		$antiguedad_enero=0;
		$antiguedad_febrero=0;
		$antiguedad_marzo=0;
		$antiguedad_abril=0;

		$bonoacademico_enero=0;//* bono academ
		$bonoacademico_febrero=0;//* bono academ
		$bonoacademico_marzo=0;//* bono academ
		$bonoacademico_abril=0;//* bono academ

		$bono_antiguedad_nuevo_enero=obtenerBonoAntiguedad($minimo_salarial,$ing_planilla,$fecha_planilla_1);//ok
		$bono_antiguedad_nuevo_febrero=obtenerBonoAntiguedad($minimo_salarial,$ing_planilla,$fecha_planilla_2);//ok
		$bono_antiguedad_nuevo_marzo=obtenerBonoAntiguedad($minimo_salarial,$ing_planilla,$fecha_planilla_3);//ok
		$bono_antiguedad_nuevo_abril=obtenerBonoAntiguedad($minimo_salarial,$ing_planilla,$fecha_planilla_4);//ok

		//echo "ene: ".$bono_antiguedad_nuevo_enero." feb: ".$bono_antiguedad_nuevo_febrero." mar: ".$bono_antiguedad_nuevo_marzo." abril: ".$bono_antiguedad_nuevo_abril;

		if($haber_basico1>0){
			$haber_basico_nuevo1=$haber_basico_nuevo*$dias_trabajados1/$dias_del_mes;
			$retroactivo_enero=$haber_basico_nuevo1-$haber_basico1;
			$antiguedad_enero=$bono_antiguedad_nuevo_enero-$bono_antiguedad1;
			$bonoacademico_enero=$bonoacademico1_nuevo-$bonoacademico1_ant;//* bono academ

			$totalGanadoMes1_nuevo=$haber_basico_nuevo1+$bono_antiguedad_nuevo_enero+$bonoacademico1_nuevo;
			
			$a_solidario_13_25_35_enero_nuevo = obtenerAporteSolidario13000($totalGanadoMes1_nuevo)+obtenerAporteSolidario25000($totalGanadoMes1_nuevo)+obtenerAporteSolidario35000($totalGanadoMes1_nuevo);

			$a_solidario_13_25_35_enero_ant = obtenerAporteSolidario13000($totalGanadoMes1_ant)+obtenerAporteSolidario25000($totalGanadoMes1_ant)+obtenerAporteSolidario35000($totalGanadoMes1_ant);

			$a_solidario_13_25_35_enero=$a_solidario_13_25_35_enero_nuevo-$a_solidario_13_25_35_enero_ant;

		}
		if($haber_basico2>0){
			$haber_basico_nuevo2=$haber_basico_nuevo*$dias_trabajados2/$dias_del_mes;
			$retroactivo_febrero=$haber_basico_nuevo2-$haber_basico2;
			$antiguedad_febrero=$bono_antiguedad_nuevo_febrero-$bono_antiguedad2;
			$bonoacademico_febrero=$bonoacademico2_nuevo-$bonoacademico2_ant;//* bono academ

			$totalGanadoMes2_nuevo=$haber_basico_nuevo2+$bono_antiguedad_nuevo_febrero+$bonoacademico2_nuevo;

			$a_solidario_13_25_35_febrero_nuevo = obtenerAporteSolidario13000($totalGanadoMes2_nuevo)+obtenerAporteSolidario25000($totalGanadoMes2_nuevo)+obtenerAporteSolidario35000($totalGanadoMes2_nuevo);

			$a_solidario_13_25_35_febrero_ant = obtenerAporteSolidario13000($totalGanadoMes2_ant)+obtenerAporteSolidario25000($totalGanadoMes2_ant)+obtenerAporteSolidario35000($totalGanadoMes2_ant);

			$a_solidario_13_25_35_febrero=$a_solidario_13_25_35_febrero_nuevo-$a_solidario_13_25_35_febrero_ant;
		}
		if($haber_basico3>0){
			$haber_basico_nuevo3=$haber_basico_nuevo*$dias_trabajados3/$dias_del_mes;
			$retroactivo_marzo=$haber_basico_nuevo3-$haber_basico3;
			$antiguedad_marzo=$bono_antiguedad_nuevo_marzo-$bono_antiguedad3;
			$bonoacademico_marzo=$bonoacademico3_nuevo-$bonoacademico3_ant;//* bono academ

			$totalGanadoMes3_nuevo=$haber_basico_nuevo3+$bono_antiguedad_nuevo_marzo+$bonoacademico3_nuevo;

			$a_solidario_13_25_35_marzo_nuevo = obtenerAporteSolidario13000($totalGanadoMes3_nuevo)+obtenerAporteSolidario25000($totalGanadoMes3_nuevo)+obtenerAporteSolidario35000($totalGanadoMes3_nuevo);

			$a_solidario_13_25_35_marzo_ant = obtenerAporteSolidario13000($totalGanadoMes3_ant)+obtenerAporteSolidario25000($totalGanadoMes3_ant)+obtenerAporteSolidario35000($totalGanadoMes3_ant);

			$a_solidario_13_25_35_marzo=$a_solidario_13_25_35_marzo_nuevo-$a_solidario_13_25_35_marzo_ant;
		}
		if($haber_basico4>0){
			$haber_basico_nuevo4=$haber_basico_nuevo*$dias_trabajados4/$dias_del_mes;
			$retroactivo_abril=$haber_basico_nuevo4-$haber_basico4;
			$antiguedad_abril=$bono_antiguedad_nuevo_abril-$bono_antiguedad4;
			$bonoacademico_abril=$bonoacademico4_nuevo-$bonoacademico4_ant;//* bono academ

			$totalGanadoMes4_nuevo=$haber_basico_nuevo4+$bono_antiguedad_nuevo_abril+$bonoacademico4_nuevo;

			$a_solidario_13_25_35_abril_nuevo = obtenerAporteSolidario13000($totalGanadoMes4_nuevo)+obtenerAporteSolidario25000($totalGanadoMes4_nuevo)+obtenerAporteSolidario35000($totalGanadoMes4_nuevo);
			
			$a_solidario_13_25_35_abril_ant = obtenerAporteSolidario13000($totalGanadoMes4_ant)+obtenerAporteSolidario25000($totalGanadoMes4_ant)+obtenerAporteSolidario35000($totalGanadoMes4_ant);

			$a_solidario_13_25_35_abril=$a_solidario_13_25_35_abril_nuevo-$a_solidario_13_25_35_abril_ant;
		}
		$bono_antiguedad_anterior=$bono_antiguedad4;
		$bono_antiguedad_nuevo=$bono_antiguedad_nuevo_abril;

		
		/************************************************************************************************/
		/********Revisamos si el personal esta en excepciones (no se le calculal el retroactivo)*********/
		/************************************************************************************************/
		$sqlVerificaExcepcion="SELECT  from planillas_retroactivos_excepciones p where 
			p.cod_planilla='$cod_planilla' and p.cod_personal='$cod_personal'";
		$stmtVerificacionExcepcion = $dbh->prepare($sqlVerificaExcepcion);
		$stmtVerificacionExcepcion->execute();
		$stmtVerificacionExcepcion->bindColumn('contador', $banderaVeriExcepcion);

		if($rowVerificacionExcepcion = $stmtVerificacionExcepcion->fetch()) {
			$banderaVeriExcepcion=$banderaVeriExcepcion;
		}
		/************************************************************************************************/
		/******** Fin Excepciones al calculo de incrementos *********/
		/************************************************************************************************/


		// $total_ganado=$retroactivo_enero+$retroactivo_febrero+$retroactivo_marzo+$retroactivo_abril+$antiguedad_enero+$antiguedad_febrero+$antiguedad_marzo+$antiguedad_abril;
		$total_ganado=$retroactivo_enero+$retroactivo_febrero+$retroactivo_marzo+$retroactivo_abril+$antiguedad_enero+$antiguedad_febrero+$antiguedad_marzo+$antiguedad_abril+$bonoacademico_enero+$bonoacademico_febrero+$bonoacademico_marzo+$bonoacademico_abril;//* bono academ
		
		$ap_vejez=$total_ganado*10/100;//10%
		//CASO JOSE DURAN 
		if($cod_personal==84){
			$ap_vejez=0;//10%
		}
		$riesgo_prof=$total_ganado*1.71/100;//1.7%
		$com_afp=$total_ganado*0.5/100;//0.5%
		$aporte_sol=$total_ganado*0.5/100;//0.5%

		$total_descuentos=$ap_vejez+$riesgo_prof+$com_afp+$aporte_sol+$a_solidario_13_25_35_enero+$a_solidario_13_25_35_febrero+$a_solidario_13_25_35_marzo+$a_solidario_13_25_35_abril;
		$liquido_pagable=$total_ganado-$total_descuentos;

		
		// if($por_bonoacademico1>0){
		// 	echo $cod_personal."*".$por_bonoacademico1."*Ant:".$bonoacademico1_ant."*nuevo: ".$bonoacademico1_nuevo."*TOTAL: ".$bonoacademico_enero."<br>";
		// }
		//==== insert de panillas de personal mes
		

		$sqlInsertPlanillas = "INSERT into planillas_retroactivos_detalle(cod_planilla,cod_personal,cod_area,haber_basico_anterior,haber_basico_nuevo,bono_antiguedad_anterior,bono_antiguedad_nuevo,retroactivo_enero,retroactivo_febrero,retroactivo_marzo,retroactivo_abril,antiguedad_enero,antiguedad_febrero,antiguedad_marzo,antiguedad_abril,total_ganado,ap_vejez,riesgo_prof,com_afp,aporte_sol,total_descuentos,liquido_pagable,correlativo_planilla,ing_planilla,retiro_planilla,dias_trabajados_enero,dias_trabajados_febrero,dias_trabajados_marzo,dias_trabajados_abril,cuenta_habilitada,bonoacademico_enero,bonoacademico_febrero,bonoacademico_marzo,bonoacademico_abril,a_solidario_13_25_35_enero,a_solidario_13_25_35_febrero,a_solidario_13_25_35_marzo,a_solidario_13_25_35_abril)
		values('$cod_planilla','$cod_personal','$cod_area','$haber_basico_anterior','$haber_basico_nuevo','$bono_antiguedad_anterior','$bono_antiguedad_nuevo','$retroactivo_enero','$retroactivo_febrero','$retroactivo_marzo','$retroactivo_abril','$antiguedad_enero','$antiguedad_febrero','$antiguedad_marzo','$antiguedad_abril','$total_ganado','$ap_vejez','$riesgo_prof','$com_afp','$aporte_sol','$total_descuentos','$liquido_pagable','$index','$ing_planilla','$retiro_planilla','$dias_trabajados1','$dias_trabajados2','$dias_trabajados3','$dias_trabajados4','$cuenta_habilitada','$bonoacademico_enero','$bonoacademico_febrero','$bonoacademico_marzo','$bonoacademico_abril','$a_solidario_13_25_35_enero','$a_solidario_13_25_35_febrero','$a_solidario_13_25_35_marzo','$a_solidario_13_25_35_abril')";
		$stmtInsertPlanillas = $dbh->prepare($sqlInsertPlanillas);
		$flagSuccessIP=$stmtInsertPlanillas->execute();
		$index++;

	}
}elseif($sw==3)
{//cerrar planilla	
	// Prepare
	$stmtU = $dbh->prepare("UPDATE planillas_retroactivos
	set cod_estadoplanilla=:cod_estadoplanilla
	where codigo=:cod_planilla");
	// Bind
	$stmtU->bindParam(':cod_planilla', $cod_planilla);
	$stmtU->bindParam(':cod_estadoplanilla', $cod_estadoplanilla);
	$flagSuccessIP=$stmtU->execute();
}
if($flagSuccessIP){
	$result_x = 1;
}

$dbh=null;
$stmtU=null;
$stmtInsertPlanillas=null;
$stmtDatosPlanilla=null;

echo $result_x;
?>