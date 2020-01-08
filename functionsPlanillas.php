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

?>