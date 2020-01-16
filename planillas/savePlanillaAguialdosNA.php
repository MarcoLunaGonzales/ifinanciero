<?php
require_once '../conexion.php';

require_once '../functions.php';
require_once '../rrhh/configModule.php';

require_once '../functionsGeneral.php';



$result_x=0;

$dbh = new Conexion();

//RECIBIMOS LAS VARIABLES
$cod_planilla=$_POST['cod_planilla'];
$cod_estadoplanilla=$_POST['sw'];
$sw=$_POST['sw'];
$cod_uo_x=$_POST['cod_uo'];

$stmtDatosPlanilla = $dbh->prepare("SELECT cod_gestion from planillas_aguinaldos where codigo=$cod_planilla");
$stmtDatosPlanilla->execute();
$resultDatosPlanilla =  $stmtDatosPlanilla->fetch();
$cod_gestion_x = $resultDatosPlanilla['cod_gestion'];

if($sw==3)
{//cerrar planilla	
	$created_by=1;
	$modified_by=1;
	$stmtU = $dbh->prepare("INSERT into planillas_aguinaldos_uo_cerrados(cod_planilla,cod_uo,created_by,modified_by) values(:cod_planilla,:cod_uo,:created_by,:modified_by)");
	// Bind
	$stmtU->bindParam(':cod_planilla', $cod_planilla);
	$stmtU->bindParam(':cod_uo', $cod_uo_x);
	$stmtU->bindParam(':created_by', $created_by);
	$stmtU->bindParam(':modified_by', $modified_by);
	$flagSuccessIP=$stmtU->execute();
}elseif($sw==1)
{//reporcesar planilla
	$created_by=1;
	$modified_by=1;
	$sqlUO="SELECT cpsd.cod_uo
		from configuraciones_planilla_sueldo cps,configuraciones_planilla_sueldo_detalle cpsd
		where cps.id_configuracion=cpsd.cod_conf_planilla_sueldo and cps.cod_uo=$cod_uo_x";
	$stmtUO=$dbh->prepare($sqlUO);
	$stmtUO->execute();
	$stmtUO->bindColumn('cod_uo',$cod_uo_2);
	while ($rowUO=$stmtUO->fetch()){
		//============select del personal
		$sql = "SELECT codigo,ing_contr from personal where cod_estadoreferencial=1 and cod_unidadorganizacional=$cod_uo_2";
		$stmtPersonal = $dbh->prepare($sql);
		$stmtPersonal->execute();
		$stmtPersonal->bindColumn('codigo', $codigo_personal);
		$stmtPersonal->bindColumn('ing_contr', $ing_contr);
		while ($rowC = $stmtPersonal->fetch()) 
		{
			$sw_Aguinaldo=Verificar_si_corresponde_Aguinaldo($ing_contr);
			if($sw_Aguinaldo=1){
				$meses_trabajados_del_anio=12;
				$dias_trabajados_del_anio=30;
				// $cod_planilla_1=obtner_id_planilla($cod_gestion_x,9);
				// $cod_planilla_2=obtner_id_planilla($cod_gestion_x,10);			
				$cod_planilla_3=obtner_id_planilla($cod_gestion_x,11);
				// echo "codigo: ".$codigo_personal."-planilla:".$cod_planilla_3."-";
				// $liquido_mes1=obtnerSueldomes($codigo_personal,$cod_planilla_1);
				// $liquido_mes2=obtnerSueldomes($codigo_personal,$cod_planilla_2);
				$liquido_mes1=obtnerSueldomes($codigo_personal,$cod_planilla_3);//cambiar
				$liquido_mes2=obtnerSueldomes($codigo_personal,$cod_planilla_3);//cambiar
				$liquido_mes3=obtnerSueldomes($codigo_personal,$cod_planilla_3);
				// echo "liquido_mes1:".$liquido_mes1."<br>";

				$promedio_sueldos=($liquido_mes1+$liquido_mes2+$liquido_mes3)/3;
				$dias_sueldo=$promedio_sueldos/360*$dias_trabajados_del_anio;
				$meses_sueldo=$promedio_sueldos/12*$meses_trabajados_del_anio;
				$total_pago_aguinaldo=$dias_sueldo+$meses_sueldo;
			}else{
				$meses_trabajados_del_anio=2;
				$dias_trabajados_del_anio=26;
				$liquido_mes1=0;
				$liquido_mes2=0;
				$liquido_mes3=0;
				$dias_sueldo=0;
				$meses_sueldo=0;
				$total_pago_aguinaldo=0;
			}
			$sqlpersonalVerificacion = "SELECT cod_personal from planillas_aguinaldos_detalle where cod_planilla=$cod_planilla and cod_personal=$codigo_personal";
			$stmtPersonalVerificacion = $dbh->prepare($sqlpersonalVerificacion);
			$stmtPersonalVerificacion->execute();
			$resultPersonalVerificacion=$stmtPersonalVerificacion->fetch();
			$cod_personalVerificacion=$resultPersonalVerificacion['cod_personal'];

			if($cod_personalVerificacion==null){//insert de personal
				$sqlInsertPlanillas="INSERT into planillas_aguinaldos_detalle(cod_planilla,cod_personal,sueldo_1,sueldo_2,sueldo_3,
				  meses_trabajados,dias_trabajados,total_aguinaldo,created_by,modified_by)
				 values(:cod_planilla,:codigo_personal,:sueldo1,:sueldo2,:sueldo3,:meses_trabajados,:dias_trabajados,
				 	:total_aguinaldo,:created_by,:modified_by)";
				$stmtInsertPlanillas = $dbh->prepare($sqlInsertPlanillas);
				$stmtInsertPlanillas->bindParam(':cod_planilla', $cod_planilla);
				$stmtInsertPlanillas->bindParam(':codigo_personal',$codigo_personal);
				$stmtInsertPlanillas->bindParam(':sueldo1',$liquido_mes1);
				$stmtInsertPlanillas->bindParam(':sueldo2',$liquido_mes2);
				$stmtInsertPlanillas->bindParam(':sueldo3',$liquido_mes3);
				$stmtInsertPlanillas->bindParam(':meses_trabajados',$meses_trabajados_del_anio);	
				$stmtInsertPlanillas->bindParam(':dias_trabajados',$dias_trabajados_del_anio);		
				$stmtInsertPlanillas->bindParam(':total_aguinaldo',$total_pago_aguinaldo);

				$stmtInsertPlanillas->bindParam(':created_by',$created_by);
				$stmtInsertPlanillas->bindParam(':modified_by',$modified_by);
				$flagSuccessIP=$stmtInsertPlanillas->execute();			
			}else{
				//==== update de panillas si personal existe
				$sqlInsertPlanillas="UPDATE planillas_aguinaldos_detalle set sueldo_1=:sueldo1,sueldo_2=:sueldo2,sueldo_3=:sueldo3,meses_trabajados=:meses_trabajados,dias_trabajados=:dias_trabajados,total_aguinaldo=:total_aguinaldo,modified_by=:modified_by
				where cod_planilla=:cod_planilla and cod_personal=:codigo_personal";
				$stmtInsertPlanillas = $dbh->prepare($sqlInsertPlanillas);
				$stmtInsertPlanillas->bindParam(':cod_planilla', $cod_planilla);
				$stmtInsertPlanillas->bindParam(':codigo_personal',$codigo_personal);
				$stmtInsertPlanillas->bindParam(':sueldo1',$liquido_mes1);
				$stmtInsertPlanillas->bindParam(':sueldo2',$liquido_mes2);
				$stmtInsertPlanillas->bindParam(':sueldo3',$liquido_mes3);
				$stmtInsertPlanillas->bindParam(':meses_trabajados',$meses_trabajados_del_anio);	
				$stmtInsertPlanillas->bindParam(':dias_trabajados',$dias_trabajados_del_anio);		
				$stmtInsertPlanillas->bindParam(':total_aguinaldo',$total_pago_aguinaldo);
				$stmtInsertPlanillas->bindParam(':modified_by',$modified_by);
				$flagSuccessIP=$stmtInsertPlanillas->execute();	
			}

		}	
	}
	
}


if($flagSuccessIP){
	$result_x = 1;
}
echo $result_x;
$dbhU=null;

?>