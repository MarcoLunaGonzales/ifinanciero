<?php
require_once '../conexion.php';
require_once '../functions.php';
require_once '../rrhh/configModule.php';
require_once '../functionsGeneral.php';

$result_x=0;

$dbh = new Conexion();
$dbhI = new Conexion();
$dbhIPD = new Conexion();
$dbhU = new Conexion();

//RECIBIMOS LAS VARIABLES
$cod_planilla=$_POST['cod_planilla'];
$cod_estadoplanilla=$_POST['sw'];
$sw=$_POST['sw'];

$stmtDatosPlanilla = $dbh->prepare("SELECT cod_gestion,cod_mes,cod_uo from planillas where codigo=$cod_planilla");
$stmtDatosPlanilla->execute();
$resultDatosPlanilla =  $stmtDatosPlanilla->fetch();
$cod_gestion_x = $resultDatosPlanilla['cod_gestion'];
$cod_mes_x = $resultDatosPlanilla['cod_mes'];
$cod_uo_x = $resultDatosPlanilla['cod_uo'];

if($sw==2){//procesar planilla	
	//actualizamos estado
	$stmtU = $dbh->prepare("UPDATE planillas 
	set cod_estadoplanilla=:cod_estadoplanilla
	where codigo=:cod_planilla");
	$stmtU->bindParam(':cod_planilla', $cod_planilla);
	$stmtU->bindParam(':cod_estadoplanilla', $cod_estadoplanilla);
	$flagSuccess=$stmtU->execute();

	//=========================creando la planilla previa con valores ininciales
	$dias_trabajados = 30; //por defecto
	$horas_pagadas = 0; //buscar datos
	$minimo_salarial=0;
	$valor_conf_x65_90=0;
	$valor_conf_x90_120=0;
	$valor_conf_x120_150=0;
	$valor_conf_x150=0;
	
	$bono_antiguedad=0;
	$otros_b=0;
	$total_bonos=0;
	$total_ganado=0;

	$haber_basico=0;//del personal
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
	
	$stmtConfiguracion = $dbh->prepare("SELECT * from configuraciones_planillas where codigo in (1)");
	$stmtConfiguracion->execute();
	$stmtConfiguracion->bindColumn('id_configuracion', $codigo_configuracion);
	$stmtConfiguracion->bindColumn('valor_configuracion',$valor_configuracion);

  //capturando valores de configuracion
	while ($rowC = $stmtConfiguracion->fetch()) 
	{
		switch ($codigo_configuracion) {
		  case 1:
		    $minimo_salarial=$valor_configuracion;
		    break;		  
		  
		  default:
		    
		    break;
		}
	}
	// fin de valores de configruacion

	//============select del personal
	$sql = "SELECT codigo,haber_basico,cod_grado_academico,
	(Select pga.porcentaje from personal_grado_academico pga where pga.codigo=cod_grado_academico) as p_grado_academico,  
	cod_tipoafp,ing_contr
	from personal where cod_estadoreferencial=1 and cod_unidadorganizacional=$cod_uo_x";
	$stmtPersonal = $dbh->prepare($sql);
	$stmtPersonal->execute();
	$stmtPersonal->bindColumn('codigo', $codigo_personal);
	$stmtPersonal->bindColumn('haber_basico', $haber_basico);
	$stmtPersonal->bindColumn('cod_grado_academico', $cod_gradoacademico);  
	$stmtPersonal->bindColumn('p_grado_academico', $p_grado_academico);  
	$stmtPersonal->bindColumn('cod_tipoafp', $cod_tipoafp);
	$stmtPersonal->bindColumn('ing_contr', $ing_contr);
	while ($rowC = $stmtPersonal->fetch()) 
	{
		//porcentaje del personal en el area *$porcentaje/100
		$haber_basico_x=$haber_basico*$porcentaje/100;
		$minimo_salarial_x=$minimo_salarial*$porcentaje/100;

		$otros_b = obtenerTotalBonos($codigo_personal);
		$otros_b_x=$otros_b*$porcentaje/100;

		//calculado otros bonos		
		if($p_grado_academico==0)$bono_academico = 0;
		else $bono_academico = $p_grado_academico/100*$minimo_salarial_x;
		$bono_antiguedad= obtenerBonoAntiguedad($minimo_salarial_x,$ing_contr);//ok			
		$total_bonos=$bono_antiguedad+$otros_b_x;	
		$total_ganado = ($haber_basico_x/30*$dias_trabajados)+$total_bonos;	
		//calculamos descuentos
		if($cod_tipoafp==1){
		  $afp_futuro =obtenerAporteAFP($total_ganado);
		  $afp_prevision=0;
		}elseif($cod_tipoafp==2){
		  $afp_prevision = obtenerAporteAFP($total_ganado);
		  $afp_futuro=0;
		}else{
		  $afp_prevision = 0;
		  $afp_futuro=0;
		}
		//aportes volvuntarios
		$aporte_solidario_13000 = obtenerAporteSolidario13000($total_ganado);
		$aporte_solidario_25000 = obtenerAporteSolidario25000($total_ganado);
		$aporte_solidario_35000 = obtenerAporteSolidario35000($total_ganado);

		$RC_IVA = obtenerRC_IVA($total_ganado,$afp_futuro,$afp_prevision,$aporte_solidario_13000,$aporte_solidario_25000,$aporte_solidario_35000);

		$atrasos = obtenerAtrasoPersonal($codigo_personal,$haber_basico_x);
		$anticipo = obtenerAnticipo($codigo_personal);
		$dotaciones = obtenerDotaciones($codigo_personal,$cod_gestion_x,$cod_mes_x);
		$otros_descuentos=obtenerOtrosDescuentos($codigo_personal);
		$total_descuentos = $afp_futuro+$afp_prevision+$aporte_solidario_13000+$aporte_solidario_25000+$aporte_solidario_35000+$RC_IVA+$atrasos+$anticipo+$dotaciones+$otros_descuentos;
		
		$liquido_pagable=$total_ganado-$total_descuentos;

		$cod_config_planilla_seguro_medico=16;
		$cod_config_planilla_riesgo_prof=17;
		$cod_config_planilla_provivienda=18;
		$cod_config_planilla_solidario=19;		
		
		$seguro_de_salud=obtener_aporte_patronal_general($cod_config_planilla_seguro_medico,$total_ganado);
		$riesgo_profesional=obtener_aporte_patronal_general($cod_config_planilla_riesgo_prof,$total_ganado);
		$provivienda=obtener_aporte_patronal_general($cod_config_planilla_provivienda,$total_ganado);
		$a_patronal_sol=obtener_aporte_patronal_general($cod_config_planilla_solidario,$total_ganado);
		
		$total_a_patronal=$seguro_de_salud+$riesgo_profesional+$provivienda+$a_patronal_sol;

		//==== insert de panillas de  personal mes
		$sqlInsertPlanillas="INSERT into planillas_personal_mes(cod_planilla,cod_personalcargo,cod_gradoacademico,dias_trabajados,horas_pagadas,
		  haber_basico,bono_academico,bono_antiguedad,monto_bonos,total_ganado,monto_descuentos,afp_1,afp_2,dotaciones,
		  liquido_pagable,cod_estadoreferencial,created_by,modified_by)
		 values(:cod_planilla,:codigo_personal,:cod_gradoacademico,:dias_trabajados,:horas_pagadas,:haber_basico,:bono_academico,
		 	:bono_antiguedad,:monto_bonos,:total_ganado,:monto_descuentos,:afp_1,:afp_2,:dotaciones,
		  :liquido_pagable,:cod_estadoreferencial,:created_by,:modified_by)";
		$stmtInsertPlanillas = $dbhI->prepare($sqlInsertPlanillas);
		$stmtInsertPlanillas->bindParam(':cod_planilla', $cod_planilla);
		$stmtInsertPlanillas->bindParam(':codigo_personal',$codigo_personal);
		$stmtInsertPlanillas->bindParam(':cod_gradoacademico',$cod_gradoacademico);
		$stmtInsertPlanillas->bindParam(':dias_trabajados',$dias_trabajados);
		$stmtInsertPlanillas->bindParam(':horas_pagadas',$horas_pagadas);
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
		$flagSuccessIP=$stmtInsertPlanillas->execute();    
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
}elseif($sw==1)
{//reporcesar planilla
	//=========================creando la planilla previa
	$flagSuccessIPMD=0;
	$dias_trabajados = 30; //por defecto
	$horas_pagadas = 0; //buscar datos
	$minimo_salarial=0;
	$valor_conf_x65_90=0;
	$valor_conf_x90_120=0;
	$valor_conf_x120_150=0;
	$valor_conf_x150=0;
	$total_bonos=0;
	//$monto_bonos=0;
	$total_ganado=0;

	$haber_basico=0;//del personal
	$horas_extra = 0; //buscar datos
	$comisiones=0;//buscar datos
	$aporte_solidario_13000 = 0;
	$aporte_solidario_25000 = 0;
	$aporte_solidario_35000 = 0;
	$RC_IVA = 0;

	$atrasos = 0;
	$anticipo = 0;

	//$monto_descuentos=0;//

	//$otros_descuentos=0;
	$dotaciones=0;

	$total_descuentos=0;
	$liquido_pagable=0;
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

	$stmtConfiguracion = $dbh->prepare("SELECT * from configuraciones_planillas where id_configuracion in (1)");
	$stmtConfiguracion->execute();
	$stmtConfiguracion->bindColumn('id_configuracion', $codigo_configuracion);
	$stmtConfiguracion->bindColumn('valor_configuracion',$valor_configuracion);

  //capturando valores de configuracion
	while ($rowC = $stmtConfiguracion->fetch()) 
	{
		switch ($codigo_configuracion) {
		  case 1:
		    $minimo_salarial=$valor_configuracion;
		    break;		  
		  
		  default:
		    
		    break;
		}
	}
	// fin de valores de configruacion

	//============select del personal
	$sql = "SELECT pad.cod_personal,pad.porcentaje,p.haber_basico,p.cod_grado_academico,p.cod_tipoafp,p.ing_contr,
	(Select pga.porcentaje from personal_grado_academico pga where pga.codigo=p.cod_grado_academico) as p_grado_academico
	from personal_area_distribucion pad, personal p
	where pad.cod_estadoreferencial=1 and pad.cod_personal=p.codigo and cod_uo=$cod_uo_x";
		
	$stmtPersonal = $dbh->prepare($sql);
	$stmtPersonal->execute();
	$stmtPersonal->bindColumn('cod_personal', $codigo_personal);
	$stmtPersonal->bindColumn('haber_basico', $haber_basico);
	$stmtPersonal->bindColumn('cod_grado_academico', $cod_gradoacademico);  
	$stmtPersonal->bindColumn('p_grado_academico', $p_grado_academico);  
	$stmtPersonal->bindColumn('cod_tipoafp', $cod_tipoafp);
	$stmtPersonal->bindColumn('ing_contr', $ing_contr);
	$stmtPersonal->bindColumn('porcentaje', $porcentaje);
	while ($rowC = $stmtPersonal->fetch()) 
	{
		//porcentaje del personal en el area *$porcentaje/100
		$haber_basico_x=$haber_basico*$porcentaje/100;
		$minimo_salarial_x=$minimo_salarial*$porcentaje/100;

		$otros_b = obtenerTotalBonos($codigo_personal);
		$otros_b_x=$otros_b*$porcentaje/100;

		//calculado otros bonos		
		if($p_grado_academico==0)$bono_academico = 0;
		else $bono_academico = $p_grado_academico/100*$minimo_salarial_x;
		$bono_antiguedad= obtenerBonoAntiguedad($minimo_salarial_x,$ing_contr);//ok	

		//$otros_b = 0 ;//buscar datos
		//$total_bonos=$bono_academico+$bono_antiguedad+$otros_b;	
		$total_bonos=$bono_antiguedad+$otros_b_x;	

		$total_ganado = ($haber_basico_x/30*$dias_trabajados)+$total_bonos;	
		//calculamos descuentos
		if($cod_tipoafp==1){
		  $afp_futuro =obtenerAporteAFP($total_ganado);
		  $afp_prevision=0;
		}elseif($cod_tipoafp==2){
		  $afp_prevision = obtenerAporteAFP($total_ganado);
		  $afp_futuro=0;
		}else{
		  $afp_prevision = 0;
		  $afp_futuro=0;
		}
		//aportes volvuntarios
		$aporte_solidario_13000 = obtenerAporteSolidario13000($total_ganado);
		$aporte_solidario_25000 = obtenerAporteSolidario25000($total_ganado);
		$aporte_solidario_35000 = obtenerAporteSolidario35000($total_ganado);

		$RC_IVA = obtenerRC_IVA($total_ganado,$afp_futuro,$afp_prevision,$aporte_solidario_13000,$aporte_solidario_25000,$aporte_solidario_35000);

		$atrasos = obtenerAtrasoPersonal($codigo_personal,$haber_basico_x);
		$anticipo = obtenerAnticipo($codigo_personal);
		$dotaciones = obtenerDotaciones($codigo_personal,$cod_gestion_x,$cod_mes_x);

		// echo "personal: ".$codigo_personal."<br>";
		// echo "dotaciones : ".$dotaciones."<br>";

		$otros_descuentos=obtenerOtrosDescuentos($codigo_personal);
		$total_descuentos = $afp_futuro+$afp_prevision+$aporte_solidario_13000+$aporte_solidario_25000+$aporte_solidario_35000+$RC_IVA+$atrasos+$anticipo+$dotaciones+$otros_descuentos;
		
		$liquido_pagable=$total_ganado-$total_descuentos;

		$cod_config_planilla_seguro_medico=16;
		$cod_config_planilla_riesgo_prof=17;
		$cod_config_planilla_provivienda=18;
		$cod_config_planilla_solidario=19;		
		
		$seguro_de_salud=obtener_aporte_patronal_general($cod_config_planilla_seguro_medico,$total_ganado);
		$riesgo_profesional=obtener_aporte_patronal_general($cod_config_planilla_riesgo_prof,$total_ganado);
		$provivienda=obtener_aporte_patronal_general($cod_config_planilla_provivienda,$total_ganado);
		$a_patronal_sol=obtener_aporte_patronal_general($cod_config_planilla_solidario,$total_ganado);
		
		$total_a_patronal=$seguro_de_salud+$riesgo_profesional+$provivienda+$a_patronal_sol;

		// echo "codigo".$codigo_personal.", total ganado ". $total_ganado."<br>";
		// echo "liquido_pagable ".$liquido_pagable;


		$sqlpersonalVerificacion = "SELECT cod_personalcargo from planillas_personal_mes where cod_planilla=$cod_planilla and cod_personalcargo=$codigo_personal";
		$stmtPersonalVerificacion = $dbh->prepare($sqlpersonalVerificacion);
		$stmtPersonalVerificacion->execute();
		$resultPersonalVerificacion=$stmtPersonalVerificacion->fetch();
		$cod_personalVerificacion=$resultPersonalVerificacion['cod_personalcargo'];

		if($cod_personalVerificacion==null){
			$sqlInsertPlanillas="INSERT into planillas_personal_mes(cod_planilla,cod_personalcargo,cod_gradoacademico,dias_trabajados,horas_pagadas,
			  haber_basico,bono_academico,bono_antiguedad,monto_bonos,total_ganado,monto_descuentos,afp_1,afp_2,dotaciones,
			  liquido_pagable,cod_estadoreferencial,created_by,modified_by)
			 values(:cod_planilla,:codigo_personal,:cod_gradoacademico,:dias_trabajados,:horas_pagadas,:haber_basico,:bono_academico,
			 	:bono_antiguedad,:monto_bonos,:total_ganado,:monto_descuentos,:afp_1,:afp_2,:dotaciones,
			  :liquido_pagable,:cod_estadoreferencial,:created_by,:modified_by)";
			$stmtInsertPlanillas = $dbhI->prepare($sqlInsertPlanillas);
			$stmtInsertPlanillas->bindParam(':cod_planilla', $cod_planilla);
			$stmtInsertPlanillas->bindParam(':codigo_personal',$codigo_personal);
			$stmtInsertPlanillas->bindParam(':cod_gradoacademico',$cod_gradoacademico);
			$stmtInsertPlanillas->bindParam(':dias_trabajados',$dias_trabajados);
			$stmtInsertPlanillas->bindParam(':horas_pagadas',$horas_pagadas);
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
			$flagSuccessIP=$stmtInsertPlanillas->execute();

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

		}else{
			//==== update de panillas si personal existe
			$sqlInsertPlanillas="UPDATE planillas_personal_mes set cod_gradoacademico=:cod_grado_academico,dias_trabajados=:dias_trabajados,
			horas_pagadas=:horas_pagadas,haber_basico=:haber_basico,bono_academico=:bono_academico,bono_antiguedad=:bono_antiguedad,
			monto_bonos=:monto_bonos,total_ganado=:total_ganado,monto_descuentos=:monto_descuentos,
			afp_1=:afp_1,afp_2=:afp_2,dotaciones=:dotaciones,liquido_pagable=:liquido_pagable
			where cod_planilla=:cod_planilla and cod_personalcargo=:cod_personal_cargo";
			$stmtInsertPlanillas = $dbhI->prepare($sqlInsertPlanillas);
			$stmtInsertPlanillas->bindParam(':cod_planilla', $cod_planilla);
			$stmtInsertPlanillas->bindParam(':cod_personal_cargo',$codigo_personal);
			$stmtInsertPlanillas->bindParam(':cod_grado_academico',$cod_gradoacademico);
			$stmtInsertPlanillas->bindParam(':dias_trabajados',$dias_trabajados);
			$stmtInsertPlanillas->bindParam(':horas_pagadas',$horas_pagadas);
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
			$flagSuccessIP=$stmtInsertPlanillas->execute();

			//==== update de panillas de  personal mes de aporte patronal 
			$sqlInsertPlanillaDetalleU="UPDATE planillas_personal_mes_patronal set a_solidario_13000=:a_solidario_13000,a_solidario_25000=:a_solidario_25000,a_solidario_35000=:a_solidario_35000,rc_iva=:rc_iva,
			atrasos=:atrasos,anticipo=:anticipo,seguro_de_salud=:seguro_de_salud,riesgo_profesional=:riesgo_profesional,
			provivienda=:provivienda,a_patronal_sol=:a_patronal_sol,total_a_patronal=:total_a_patronal,dotaciones=:dotaciones
			where cod_planilla=:cod_planilla and cod_personal_cargo=:cod_personal_cargo";

			$stmtInsertPlanillaDetalleU = $dbhIPD->prepare($sqlInsertPlanillaDetalleU);
			$stmtInsertPlanillaDetalleU->bindParam(':cod_planilla',$cod_planilla);
			$stmtInsertPlanillaDetalleU->bindParam(':cod_personal_cargo',$codigo_personal);		
			$stmtInsertPlanillaDetalleU->bindParam(':a_solidario_13000',$aporte_solidario_13000);
			$stmtInsertPlanillaDetalleU->bindParam(':a_solidario_25000',$aporte_solidario_25000);
			$stmtInsertPlanillaDetalleU->bindParam(':a_solidario_35000',$aporte_solidario_35000);
			$stmtInsertPlanillaDetalleU->bindParam(':rc_iva',$RC_IVA);
			$stmtInsertPlanillaDetalleU->bindParam(':atrasos',$atrasos);
			$stmtInsertPlanillaDetalleU->bindParam(':anticipo',$anticipo);
			$stmtInsertPlanillaDetalleU->bindParam(':seguro_de_salud',$seguro_de_salud);
			$stmtInsertPlanillaDetalleU->bindParam(':riesgo_profesional',$riesgo_profesional);
			$stmtInsertPlanillaDetalleU->bindParam(':provivienda',$provivienda);
			$stmtInsertPlanillaDetalleU->bindParam(':a_patronal_sol',$a_patronal_sol);
			$stmtInsertPlanillaDetalleU->bindParam(':total_a_patronal',$total_a_patronal);
			$stmtInsertPlanillaDetalleU->bindParam(':dotaciones',$dotaciones);
			$flagSuccessIPMD=$stmtInsertPlanillaDetalleU->execute();
			// $sqlInsertPlanillaDetalle="INSERT into planillas_personal_mes_patronal(cod_planilla,cod_personal_cargo,a_solidario_13000,a_solidario_25000,a_solidario_35000,rc_iva,atrasos,anticipo,
			// seguro_de_salud,riesgo_profesional,provivienda,a_patronal_sol,total_a_patronal,dotaciones)
			// values(:cod_planilla,:cod_personal_cargo,:a_solidario_13000,:a_solidario_25000,:a_solidario_35000,:rc_iva,:atrasos,:anticipo,
			// 	:seguro_de_salud,:riesgo_profesional,:provivienda,:a_patronal_sol,:total_a_patronal,:dotaciones)";
			// $stmtInsertPlanillaDetalle = $dbhIPD->prepare($sqlInsertPlanillaDetalle);
			// $stmtInsertPlanillaDetalle->bindParam(':cod_planilla', $cod_planilla);
			// $stmtInsertPlanillaDetalle->bindParam(':cod_personal_cargo',$codigo_personal);
			// $stmtInsertPlanillaDetalle->bindParam(':a_solidario_13000',$aporte_solidario_13000);
			// $stmtInsertPlanillaDetalle->bindParam(':a_solidario_25000',$aporte_solidario_25000);
			// $stmtInsertPlanillaDetalle->bindParam(':a_solidario_35000',$aporte_solidario_35000);
			// $stmtInsertPlanillaDetalle->bindParam(':rc_iva',$RC_IVA);
			// $stmtInsertPlanillaDetalle->bindParam(':atrasos',$atrasos);
			// $stmtInsertPlanillaDetalle->bindParam(':anticipo',$anticipo);
			// $stmtInsertPlanillaDetalle->bindParam(':seguro_de_salud',$seguro_de_salud);
			// $stmtInsertPlanillaDetalle->bindParam(':riesgo_profesional',$riesgo_profesional);
			// $stmtInsertPlanillaDetalle->bindParam(':provivienda',$provivienda);
			// $stmtInsertPlanillaDetalle->bindParam(':a_patronal_sol',$a_patronal_sol);
			// $stmtInsertPlanillaDetalle->bindParam(':total_a_patronal',$total_a_patronal);
			// $stmtInsertPlanillaDetalle->bindParam(':dotaciones',$dotaciones);
			// $flagSuccessIPMD=$stmtInsertPlanillaDetalle->execute();
		}
		// echo "cod_planilla: ".$cod_planilla."<br>";
		// echo "codigo_personal: ".$codigo_personal."<br>";
		// echo "aporte_solidario_13000: ".$aporte_solidario_13000."<br>";
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
	}
}


if($flagSuccessIP){
	$result_x = 1;
}
echo $result_x;
$dbh = null;
$dbhI = null;
$dbhIPD = null;
$dbhU = null;
?>