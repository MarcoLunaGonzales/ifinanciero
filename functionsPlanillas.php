<?php
require_once 'conexion.php';

function totalGanadoDN($gestion, $mes, $unidad){
	$dbh = new Conexion();
	$sql="SELECT sum(pm.total_ganado*(pad.porcentaje/100))as monto from planillas p, planillas_personal_mes pm, personal_area_distribucion pad where p.codigo=pm.cod_planilla and p.cod_gestion='$gestion' and p.cod_mes='$mes' and pm.cod_personalcargo=pad.cod_personal and pad.cod_uo='$unidad'";
   	$stmt = $dbh->prepare($sql);
   	$stmt->execute();
   	$monto=0;
   	while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    	$monto=$row['monto'];
   	}
   	return($monto);
}

function totalLiquidoPagable($gestion, $mes, $unidad){
	$dbh = new Conexion();
	$sql="SELECT sum(pm.liquido_pagable*(pad.porcentaje/100))as monto from planillas p, planillas_personal_mes pm, personal_area_distribucion pad where p.codigo=pm.cod_planilla and p.cod_gestion='$gestion' and p.cod_mes='$mes' and pm.cod_personalcargo=pad.cod_personal and pad.cod_uo='$unidad'";
   	$stmt = $dbh->prepare($sql);
   	$stmt->execute();
   	$monto=0;
   	while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    	$monto=$row['monto'];
   	}
   	return($monto);
}


function totalPersonalProyectos($proyecto){
	$dbh = new Conexion();
	$sql="SELECT sum(p.monto_subsidio)as monto from personal_proyectosfinanciacionexterna p where p.cod_estado_referencial=1 and p.cod_proyecto='$proyecto'";
   	$stmt = $dbh->prepare($sql);
   	$stmt->execute();
   	$monto=0;
   	while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    	$monto=$row['monto'];
   	}
   	return($monto);
}


function cuentaCorrienteUO($unidad){
	$nroCuenta="";
	if($unidad==10){
		$nroCuenta="56";
	}
	if($unidad==5){
		$nroCuenta="55";
	}
	if($unidad==8){
		$nroCuenta="60";
	}
	if($unidad==9){
		$nroCuenta="57";
	}
	if($unidad==270){
		$nroCuenta="58";
	}
	if($unidad==271){
		$nroCuenta="59";
	}
	return($nroCuenta);
}

function obtenerTotalCPS($gestion, $mes, $unidad){
	$dbh = new Conexion();
	$sql="SELECT sum(pm.seguro_de_salud*(pad.porcentaje/100))as monto from planillas p, planillas_personal_mes_patronal pm, personal_area_distribucion pad where p.codigo=pm.cod_planilla and p.cod_gestion='$gestion' and p.cod_mes='$mes' and pm.cod_personal_cargo=pad.cod_personal and pad.cod_uo='$unidad'";

   	$stmt = $dbh->prepare($sql);
   	$stmt->execute();
   	$monto=0;
   	while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    	$monto=$row['monto'];
   	}
   	return($monto);
}
function obtenerTotalAFP_prev1($gestion, $mes, $unidad){
	$dbh = new Conexion();
	$sql="SELECT sum((pm.riesgo_profesional*(pad.porcentaje/100))+(pm.a_patronal_sol*(pad.porcentaje/100)))as monto from planillas p, planillas_personal_mes_patronal pm, personal_area_distribucion pad,personal per where p.codigo=pm.cod_planilla and p.cod_gestion='$gestion' and p.cod_mes='$mes' and pm.cod_personal_cargo=pad.cod_personal and pad.cod_uo='$unidad' and pm.cod_personal_cargo=per.codigo and per.cod_tipoafp=2";

   	$stmt = $dbh->prepare($sql);
   	$stmt->execute();
   	$monto1=0;
   	while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    	$monto1=$row['monto'];
   	}

   	$sql2="SELECT sum((pm.afp_2*(pad.porcentaje/100)))as monto from planillas p, planillas_personal_mes pm, personal_area_distribucion pad,personal per where p.codigo=pm.cod_planilla and p.cod_gestion='$gestion' and p.cod_mes='$mes' and pm.cod_personalcargo=pad.cod_personal and pad.cod_uo='$unidad' and pm.cod_personalcargo=per.codigo and per.cod_tipoafp=2";

   	$stmt2 = $dbh->prepare($sql2);
   	$stmt2->execute();
   	$monto2=0;
   	while ($row = $stmt2->fetch(PDO::FETCH_ASSOC)) {
    	$monto2=$row['monto'];
   	}

   	$montoTotal=$monto1+$monto2;

   	return($montoTotal);
}
function obtenerTotalAFP_prev2($gestion, $mes, $unidad){
	$dbh = new Conexion();
	$sql="SELECT sum((pm.a_solidario_13000*(pad.porcentaje/100))+(pm.a_solidario_25000*(pad.porcentaje/100))+(pm.a_solidario_35000*(pad.porcentaje/100)))as monto from planillas p, planillas_personal_mes_patronal pm, personal_area_distribucion pad where p.codigo=pm.cod_planilla and p.cod_gestion='$gestion' and p.cod_mes='$mes' and pm.cod_personal_cargo=pad.cod_personal and pad.cod_uo='$unidad' ";

   	$stmt = $dbh->prepare($sql);
   	$stmt->execute();
   	$monto=0;
   	while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    	$monto=$row['monto'];
   	}
   	return($monto);
}
function obtenerTotalAFP_prev3($gestion, $mes, $unidad){
	$dbh = new Conexion();
	$sql="SELECT sum((pm.riesgo_profesional*(pad.porcentaje/100))+(pm.a_patronal_sol*(pad.porcentaje/100)))as monto from planillas p, planillas_personal_mes_patronal pm, personal_area_distribucion pad,personal per where p.codigo=pm.cod_planilla and p.cod_gestion='$gestion' and p.cod_mes='$mes' and pm.cod_personal_cargo=pad.cod_personal and pad.cod_uo='$unidad' and pm.cod_personal_cargo=per.codigo and per.cod_tipoafp=1";

   	$stmt = $dbh->prepare($sql);
   	$stmt->execute();
   	$monto1=0;
   	while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    	$monto1=$row['monto'];
   	}

   	$sql2="SELECT sum((pm.afp_1*(pad.porcentaje/100)))as monto from planillas p, planillas_personal_mes pm, personal_area_distribucion pad,personal per where p.codigo=pm.cod_planilla and p.cod_gestion='$gestion' and p.cod_mes='$mes' and pm.cod_personalcargo=pad.cod_personal and pad.cod_uo='$unidad' and pm.cod_personalcargo=per.codigo and per.cod_tipoafp=1";

   	$stmt2 = $dbh->prepare($sql2);
   	$stmt2->execute();
   	$monto2=0;
   	while ($row = $stmt2->fetch(PDO::FETCH_ASSOC)) {
    	$monto2=$row['monto'];
   	}
   	$montoTotal=$monto1+$monto2;
   	return($montoTotal);
}
function obtenerTotalprovivienda($gestion, $mes, $unidad){
	$dbh = new Conexion();
	$sql="SELECT sum((pm.provivienda*(pad.porcentaje/100)))as monto from planillas p, planillas_personal_mes_patronal pm, personal_area_distribucion pad,personal per where p.codigo=pm.cod_planilla and p.cod_gestion='$gestion' and p.cod_mes='$mes' and pm.cod_personal_cargo=pad.cod_personal and pad.cod_uo='$unidad' and pm.cod_personal_cargo=per.codigo and per.cod_tipoafp=2";

   	$stmt = $dbh->prepare($sql);
   	$stmt->execute();
   	$monto1=0;
   	while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    	$monto1=$row['monto'];
   	}
   	return($monto1);
}
function obtenerTotalprovivienda2($gestion, $mes, $unidad){
	$dbh = new Conexion();
	$sql="SELECT sum((pm.provivienda*(pad.porcentaje/100)))as monto from planillas p, planillas_personal_mes_patronal pm, personal_area_distribucion pad,personal per where p.codigo=pm.cod_planilla and p.cod_gestion='$gestion' and p.cod_mes='$mes' and pm.cod_personal_cargo=pad.cod_personal and pad.cod_uo='$unidad' and pm.cod_personal_cargo=per.codigo and per.cod_tipoafp=1";
   	$stmt = $dbh->prepare($sql);
   	$stmt->execute();
   	$monto1=0;
   	while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    	$monto1=$row['monto'];
   	}
   	return($monto1);
}
function obtenerTotalOtrosdescuentos($gestion, $mes, $unidad){
	$dbh = new Conexion();
	$sql="SELECT sum((pm.monto_descuentos*(pad.porcentaje/100))-(pm.afp_1*(pad.porcentaje/100))-(pm.afp_2*(pad.porcentaje/100)))as monto from planillas p, planillas_personal_mes pm, personal_area_distribucion pad,personal per where p.codigo=pm.cod_planilla and p.cod_gestion='$gestion' and p.cod_mes='$mes' and pm.cod_personalcargo=pad.cod_personal and pad.cod_uo='$unidad' and pm.cod_personalcargo=per.codigo";
   	$stmt = $dbh->prepare($sql);
   	$stmt->execute();
   	$monto1=0;$monto2=0;
   	while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    	$monto1=$row['monto'];
   	}
   	$sql2="SELECT sum((pm.a_solidario_13000*(pad.porcentaje/100))+(pm.a_solidario_25000*(pad.porcentaje/100))+(pm.a_solidario_35000*(pad.porcentaje/100))+(pm.rc_iva*(pad.porcentaje/100))+(pm.atrasos*(pad.porcentaje/100))+(pm.anticipo*(pad.porcentaje/100))+(pm.dotaciones*(pad.porcentaje/100)))as monto from planillas p, planillas_personal_mes_patronal pm, personal_area_distribucion pad,personal per where p.codigo=pm.cod_planilla and p.cod_gestion='$gestion' and p.cod_mes='$mes' and pm.cod_personal_cargo=pad.cod_personal and pad.cod_uo='$unidad' and pm.cod_personal_cargo=per.codigo";
   	$stmt2 = $dbh->prepare($sql2);
   	$stmt2->execute();
   	
   	while ($row = $stmt2->fetch(PDO::FETCH_ASSOC)) {
    	$monto2=$row['monto'];
   	}
   	$montoTotal=$monto1-$monto2;
   	return($montoTotal);
}


?>