<?php
require_once '../conexion.php';

require_once '../functions.php';
require_once '../rrhh/configModule.php';
require_once '../functionsGeneral.php';



$result_x=0;


// $dbhU = new Conexion();

$dbh = new Conexion();
$dbhI = new Conexion();
$dbhIPD = new Conexion();
$dbhU = new Conexion();

$dbhPADP = new Conexion();
set_time_limit(0);
session_start();

//RECIBIMOS LAS VARIABLES
$cod_planilla=$_POST['cod_planilla'];
$cod_estadoplanilla=$_POST['sw'];
$sw=$_POST['sw'];
// if($sw==2){
// 	$nombre_gestion_x=$_SESSION['globalNombreGestion'];
// 	$cod_mes_x=$_SESSION['globalMes'];
// 	$cod_gestion_x = $_SESSION['globalGestion'];
// }else{
	$stmtDatosPlanilla = $dbh->prepare("SELECT cod_gestion,cod_mes from planillas where codigo=$cod_planilla");
	$stmtDatosPlanilla->execute();
	$resultDatosPlanilla = $stmtDatosPlanilla->fetch();
	$cod_gestion_x = $resultDatosPlanilla['cod_gestion'];
	$cod_mes_x = $resultDatosPlanilla['cod_mes'];

	$sqlGestion = "SELECT nombre from gestiones where codigo=$cod_gestion_x";
	$stmtGestion = $dbh->prepare($sqlGestion);
	$stmtGestion->execute();
	$resultGestion=$stmtGestion->fetch();
	$nombre_gestion_x = $resultGestion['nombre'];
// }
$date1 = $nombre_gestion_x.'-'.$cod_mes_x; 
$d = date_create_from_format('Y-m',$date1); 
$ultimo_dia = date_format($d, 't');
$fecha_planilla=$nombre_gestion_x."-".$cod_mes_x."-".$ultimo_dia;

$date1 = $nombre_gestion_x.'-'.$cod_mes_x; 
$d = date_create_from_format('Y-m',$date1); 
$last_day = date_format($d, 't');
//echo "llega ".$cod_estadoasignacionaf;

if($sw==2 || $sw==1 || $sw==10){//procesar planilla	
	if($sw==2){
		//actualizamos estado
		$stmtU = $dbh->prepare("UPDATE planillas 
		set cod_estadoplanilla=:cod_estadoplanilla
		where codigo=:cod_planilla");
		$stmtU->bindParam(':cod_planilla', $cod_planilla);
		$stmtU->bindParam(':cod_estadoplanilla', $cod_estadoplanilla);
		$flagSuccess=$stmtU->execute();	
	}
	$stmtDelete = $dbh->prepare("DELETE  FROM planillas_personal_mes where cod_planilla=$cod_planilla");
	$stmtDelete->execute();
	$stmtDelete2 = $dbh->prepare("DELETE  FROM planillas_personal_mes_patronal where cod_planilla=$cod_planilla");
	$stmtDelete2->execute();

	
	//=========================creando la planilla previa con valores ininciales
	$dias_trabajados_por_defecto = 30; //por defecto
	// $dias_trabajados_asistencia = 30; //por asistencia

	$minimo_salarial=obtenerValorConfiguracionPlanillas(1);
	// $dias_trabajados_asistencia = obtenerValorConfiguracionPlanillas(22); //por defecto
	$dias_trabajados_mes = obtenerValorConfiguracionPlanillas(22); //por defecto
	// $dias_del_mes=30;

	$horas_pagadas = 0; //buscar datos
	// $minimo_salarial=0;
	$valor_conf_x65_90=0;
	$valor_conf_x90_120=0;
	$valor_conf_x120_150=0;
	$valor_conf_x150=0;
	$bono_antiguedad=0;
	$otros_b=0;
	$total_bonos=0;
	$total_ganado=0;
	// $haber_basico=0;//del personal
	// $horas_extra = 0; //buscar datos
	// $comisiones=0;//buscar datos
	$aporte_solidario_13000 = 0;
	$aporte_solidario_25000 = 0;
	$aporte_solidario_35000 = 0;
	$RC_IVA = 0;

	$atrasos = 0;
	$anticipo = 0;
	$dotaciones=0;
	$total_descuentos=0;
	$liquido_pagable=0;
	$cod_estadoreferencial=1;
	$created_by=1;
	$modified_by=1;

	$seguro_de_salud=0;
	$riesgo_profesional=0;
	$provivienda=0;
	$a_patronal_sol=0;
	$total_a_patronal=0;

	$flagSuccessIP=0;
	$flagSuccessIPMD=0;
	$procesado_reprocesado=1;//procesado

	$correlativo_planilla=1;
		
	// fin de valores de configruacion

	// Limpiando tabla personal_area_distribucion_planilla
	$stmtDelete = $dbh->prepare("DELETE  FROM personal_area_distribucion_planilla where cod_planilla='$cod_planilla'");
	$stmtDelete->execute();

	//============select del personal
	$sql = "SELECT cod_cargo, cod_unidadorganizacional as cod_unidad, cod_area, codigo,haber_basico,cod_grado_academico,
	(Select pga.porcentaje from personal_grado_academico pga where pga.codigo=cod_grado_academico) as p_grado_academico,cod_tipoafp,ing_contr,cuenta_bancaria
	from personal where cod_estadoreferencial=1 and cod_estadopersonal=1";
	$stmtPersonal = $dbh->prepare($sql);
	$stmtPersonal->execute();
	$stmtPersonal->bindColumn('cod_cargo', $cod_cargo);
	$stmtPersonal->bindColumn('cod_unidad', $cod_unidad);
	$stmtPersonal->bindColumn('cod_area', $cod_area);
	$stmtPersonal->bindColumn('codigo', $codigo_personal);
	$stmtPersonal->bindColumn('haber_basico', $haber_basico);
	$stmtPersonal->bindColumn('cod_grado_academico', $cod_gradoacademico);  
	$stmtPersonal->bindColumn('p_grado_academico', $p_grado_academico);  
	$stmtPersonal->bindColumn('cod_tipoafp', $cod_tipoafp);
	$stmtPersonal->bindColumn('ing_contr', $ing_contr);
	$stmtPersonal->bindColumn('cuenta_bancaria', $cuenta_bancaria);
	while ($rowC = $stmtPersonal->fetch()) 
	{
		if($cuenta_bancaria>0){
			$cuenta_habilitada=1;
		}else{
			$cuenta_habilitada=0;
		}

		$dias_trabajados_asistencia = obtenerAsistenciaPersonal($codigo_personal,$cod_gestion_x,$cod_mes_x,$dias_trabajados_mes); 

		$otros_b = obtenerTotalBonos($codigo_personal,$dias_trabajados_asistencia,$dias_trabajados_por_defecto,$cod_gestion_x,$cod_mes_x);//ee
		//calculado otros bonos	
		$bono_academico = 0;
		// if($p_grado_academico==0)
		// else $bono_academico = $p_grado_academico/100*$minimo_salarial;

		$bono_antiguedad= obtenerBonoAntiguedad($minimo_salarial,$ing_contr,$fecha_planilla);//ok	
		//echo $minimo_salarial."--".$ing_contr."--".$nombre_gestion_x;

		//$otros_b = 0 ;//buscar datos
		//$total_bonos=$bono_academico+$bono_antiguedad+$otros_b;	
		$total_bonos=$bono_antiguedad+$otros_b+$bono_academico;	
		
		$haber_basico_x=$haber_basico*$dias_trabajados_asistencia/$dias_trabajados_por_defecto;
		$total_ganado = $haber_basico_x+$total_bonos;	
		//calculamos descuentos
		if($cod_tipoafp==1){
		  	$afp_futuro =obtenerAporteAFP($total_ganado);
		  	$afp_prevision=0;
  			/*CASO ESPECIAL JOSE DURAN*/
  			if($codigo_personal==84){
  				$afp_futuro = $total_ganado*0.0271; 
  			}
			/*FIN CASO ESPECIAL JOSE DURAN*/
		}elseif($cod_tipoafp==2){
		  	$afp_prevision = obtenerAporteAFP($total_ganado);
		  	$afp_futuro=0;
  			/*CASO ESPECIAL JOSE DURAN*/
  			if($codigo_personal==84){
	  			$afp_prevision = $total_ganado*0.0271; 
  			}
			/*FIN CASO ESPECIAL JOSE DURAN*/
		}else{
		  	$afp_prevision = 0;
		  	$afp_futuro=0;
		}


		//aportes volvuntarios
		$aporte_solidario_13000 = obtenerAporteSolidario13000($total_ganado);
		$aporte_solidario_25000 = obtenerAporteSolidario25000($total_ganado);
		$aporte_solidario_35000 = obtenerAporteSolidario35000($total_ganado);

		// $RC_IVA = obtenerRC_IVA($total_ganado,$afp_futuro,$afp_prevision,$aporte_solidario_13000,$aporte_solidario_25000,$aporte_solidario_35000);
		$RC_IVA=obtenerRC_IVA_planilla($codigo_personal,$cod_gestion_x,$cod_mes_x);

		$atrasos = 0;//ee
		$anticipo = obtenerAnticipo($codigo_personal,$cod_gestion_x,$cod_mes_x);//ee
		$dotaciones = obtenerDotaciones($codigo_personal,$cod_gestion_x,$cod_mes_x);

		// echo "personal: ".$codigo_personal."<br>";
		// echo "dotaciones : ".$dotaciones."<br>";

		$otros_descuentos=obtenerOtrosDescuentos($codigo_personal,$cod_gestion_x,$cod_mes_x);//ee
		$total_descuentos = $afp_futuro+$afp_prevision+$aporte_solidario_13000+$aporte_solidario_25000+$aporte_solidario_35000+$RC_IVA+$atrasos+$anticipo+$dotaciones+$otros_descuentos;
		
		$liquido_pagable=$total_ganado-$total_descuentos;

		$cod_config_planilla_seguro_medico=16;//estatico
		$cod_config_planilla_riesgo_prof=17;//estatico
		$cod_config_planilla_provivienda=18;//estatico
		$cod_config_planilla_solidario=19;//estatico
		
		$seguro_de_salud=obtener_aporte_patronal_general($cod_config_planilla_seguro_medico,$total_ganado);
		$riesgo_profesional=obtener_aporte_patronal_general($cod_config_planilla_riesgo_prof,$total_ganado);
		$provivienda=obtener_aporte_patronal_general($cod_config_planilla_provivienda,$total_ganado);
		$a_patronal_sol=obtener_aporte_patronal_general($cod_config_planilla_solidario,$total_ganado);
		
		$total_a_patronal=$seguro_de_salud+$riesgo_profesional+$provivienda+$a_patronal_sol;

		//==== insert de panillas de  personal mes
		$sqlInsertPlanillas="INSERT into planillas_personal_mes(cod_planilla,cod_personalcargo,cod_gradoacademico,dias_trabajados,horas_pagadas,
		  haber_basico,bono_academico,bono_antiguedad,monto_bonos,total_ganado,monto_descuentos,afp_1,afp_2,dotaciones,
		  liquido_pagable,cod_estadoreferencial,created_by,modified_by,procesado_reprocesado,bonos_otros,descuentos_otros,cuenta_habilitada,haber_basico_pactado,correlativo_planilla, cod_cargo, cod_uo, cod_area)
		 values(:cod_planilla,:codigo_personal,:cod_gradoacademico,:dias_trabajados,:horas_pagadas,:haber_basico,:bono_academico,
		 	:bono_antiguedad,:monto_bonos,:total_ganado,:monto_descuentos,:afp_1,:afp_2,:dotaciones,
		  :liquido_pagable,:cod_estadoreferencial,:created_by,:modified_by,:procesado_reprocesado,:bonos_otros,:descuentos_otros,:cuenta_habilitada,:haber_basico_pactado,:correlativo_planilla, :cod_cargo, :cod_uo, :cod_area)";
		$stmtInsertPlanillas = $dbhI->prepare($sqlInsertPlanillas);
		$stmtInsertPlanillas->bindParam(':cod_planilla', $cod_planilla);
		$stmtInsertPlanillas->bindParam(':codigo_personal',$codigo_personal);
		$stmtInsertPlanillas->bindParam(':cod_gradoacademico',$cod_gradoacademico);
		$stmtInsertPlanillas->bindParam(':dias_trabajados',$dias_trabajados_asistencia);
		$stmtInsertPlanillas->bindParam(':horas_pagadas',$horas_pagadas);
		$stmtInsertPlanillas->bindParam(':haber_basico_pactado',$haber_basico);
		$stmtInsertPlanillas->bindParam(':haber_basico',$haber_basico_x);
		$stmtInsertPlanillas->bindParam(':bono_academico',$bono_academico);		
		$stmtInsertPlanillas->bindParam(':bono_antiguedad',$bono_antiguedad);
		$stmtInsertPlanillas->bindParam(':monto_bonos',$total_bonos);
		$stmtInsertPlanillas->bindParam(':total_ganado',$total_ganado);
		$stmtInsertPlanillas->bindParam(':monto_descuentos',$total_descuentos);
		$stmtInsertPlanillas->bindParam(':afp_1',$afp_futuro);  
		$stmtInsertPlanillas->bindParam(':afp_2',$afp_prevision);
		$stmtInsertPlanillas->bindParam(':dotaciones',$dotaciones);			
		$stmtInsertPlanillas->bindParam(':liquido_pagable',$liquido_pagable);
		$stmtInsertPlanillas->bindParam(':cod_estadoreferencial',$cod_estadoreferencial);
		$stmtInsertPlanillas->bindParam(':created_by',$created_by);
		$stmtInsertPlanillas->bindParam(':modified_by',$modified_by);
		$stmtInsertPlanillas->bindParam(':procesado_reprocesado',$procesado_reprocesado);

		$stmtInsertPlanillas->bindParam(':bonos_otros',$otros_b);
		$stmtInsertPlanillas->bindParam(':descuentos_otros',$otros_descuentos);
		$stmtInsertPlanillas->bindParam(':cuenta_habilitada',$cuenta_habilitada);
		$stmtInsertPlanillas->bindParam(':correlativo_planilla',$correlativo_planilla);
		
		// NUEVO CAMPOS AGREGADOS
		$stmtInsertPlanillas->bindParam(':cod_cargo',$cod_cargo);
		$stmtInsertPlanillas->bindParam(':cod_uo',$cod_unidad);
		$stmtInsertPlanillas->bindParam(':cod_area',$cod_area);
		
		$flagSuccessIP=$stmtInsertPlanillas->execute();

		// PERSONAL ÁREA DISTRIBUCIÓN
		$sqlPAD = "SELECT pad.cod_uo, pad.cod_area, pad.porcentaje, pad.monto
				FROM personal_area_distribucion pad
				WHERE pad.cod_estadoreferencial = 1 
				AND pad.cod_personal = '$codigo_personal'";
		$stmtPAD = $dbhPADP->prepare($sqlPAD);
		$stmtPAD->execute();
		while ($rowPAD = $stmtPAD->fetch(PDO::FETCH_ASSOC)) {
			$sqlReload="INSERT into personal_area_distribucion_planilla(
				cod_planilla,
				cod_personal,
				cod_uo,
				cod_area,
				porcentaje,
				monto,
				cod_estadoreferencial
			)
			values(
				:cod_planilla,
				:cod_personal,
				:cod_uo,
				:cod_area,
				:porcentaje,
				:monto,
				1)";
			$stmtInsertPAD = $dbhPADP->prepare($sqlReload);
			$stmtInsertPAD->bindParam(':cod_planilla', $cod_planilla);
			$stmtInsertPAD->bindParam(':cod_personal', $codigo_personal);
			$stmtInsertPAD->bindParam(':cod_uo', $rowPAD['cod_uo']);
			$stmtInsertPAD->bindParam(':cod_area', $rowPAD['cod_area']);
			$stmtInsertPAD->bindParam(':porcentaje', $rowPAD['porcentaje']);
			$stmtInsertPAD->bindParam(':monto', $rowPAD['monto']);
			$flagSuccessPAD=$stmtInsertPAD->execute();
		}
    

		// echo "codigo_planilla_actual: ".$cod_planilla."<br>";
		// echo "codigo_personal: ".$codigo_personal."<br>";
		// echo "afp_futuro: ".$afp_futuro."<br>";
		// echo "afp_prevision: ".$afp_prevision."<br>";
		// echo "aporte_solidario_25000: ".$aporte_solidario_13000."<br>";
		// echo "aporte_solidario_25000: ".$aporte_solidario_25000."<br>";
		// echo "aporte_solidario_35000: ".$aporte_solidario_35000."<br>";
		// echo "RC_IVA: ".$RC_IVA."<br>";
		// echo "atrasos: ".$atrasos."<br>";
		// echo "anticipo: ".$anticipo."<br>";	
		
		// echo "seguro_de_salud: ".$seguro_de_salud."<br>";
		// echo "riesgo_profesional: ".$riesgo_profesional."<br>";
		// echo "provivienda: ".$provivienda."<br>";
		// echo "a_patronal_sol: ".$a_patronal_sol."<br>";
		// echo "total_a_patronal: ".$total_a_patronal."<br>";
		// echo "==============<br>";	

		//==== insert de panillas de  personal mes de aporte patronal 
		$sqlInsertPlanillaDetalle="INSERT into planillas_personal_mes_patronal(cod_planilla,cod_personal_cargo,a_solidario_13000,a_solidario_25000,a_solidario_35000,rc_iva,atrasos,anticipo,
			seguro_de_salud,riesgo_profesional,provivienda,a_patronal_sol,total_a_patronal)
		values(:cod_planilla,:cod_personal_cargo,:a_solidario_13000,:a_solidario_25000,:a_solidario_35000,:rc_iva,:atrasos,:anticipo,
			:seguro_de_salud,:riesgo_profesional,:provivienda,:a_patronal_sol,:total_a_patronal)";
		$stmtInsertPlanillaDetalle = $dbhIPD->prepare($sqlInsertPlanillaDetalle);
		$stmtInsertPlanillaDetalle->bindParam(':cod_planilla', $cod_planilla);
		$stmtInsertPlanillaDetalle->bindParam(':cod_personal_cargo',$codigo_personal);
		$stmtInsertPlanillaDetalle->bindParam(':a_solidario_13000',$aporte_solidario_13000);
		$stmtInsertPlanillaDetalle->bindParam(':a_solidario_25000',$aporte_solidario_25000);
		$stmtInsertPlanillaDetalle->bindParam(':a_solidario_35000',$aporte_solidario_35000);
		$stmtInsertPlanillaDetalle->bindParam(':rc_iva',$RC_IVA);
		$stmtInsertPlanillaDetalle->bindParam(':atrasos',$atrasos);
		$stmtInsertPlanillaDetalle->bindParam(':anticipo',$anticipo);
		$stmtInsertPlanillaDetalle->bindParam(':seguro_de_salud',$seguro_de_salud);
		$stmtInsertPlanillaDetalle->bindParam(':riesgo_profesional',$riesgo_profesional);
		$stmtInsertPlanillaDetalle->bindParam(':provivienda',$provivienda);
		$stmtInsertPlanillaDetalle->bindParam(':a_patronal_sol',$a_patronal_sol);
		$stmtInsertPlanillaDetalle->bindParam(':total_a_patronal',$total_a_patronal);
		$flagSuccessIPMD=$stmtInsertPlanillaDetalle->execute();
		$correlativo_planilla++;
	}
	//===fin de planilla previa
	// if($flagSuccessIP)echo "Planilla Sueldos Personal CORRECTO"."<br>";
	// else echo "Planilla Sueldos Personal ERROR"."<br>";
	// if($flagSuccessIPMD)echo "Planilla Sueldos Detalle CORRECTO"."<br>";
	// else echo "Planilla Sueldos Detalle ERROR"."<br>";

}elseif($sw==3)
{//cerrar planilla	
	// Prepare
	$stmtU = $dbhU->prepare("UPDATE planillas 
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
echo $result_x;
$dbhU=null;

?>