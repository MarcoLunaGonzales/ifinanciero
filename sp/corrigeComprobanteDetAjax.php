<?php

require_once '../conexion.php';

$dbh = new Conexion();
$codComprobante=$_GET["cod_comprobante"];

$sqlGasto="SELECT p.codigo, p.numero, sum(cd.debe)as monto from comprobantes_detalle cd, plan_cuentas p
where p.codigo=cd.cod_cuenta and cd.cod_comprobante='$codComprobante' and (p.numero like '5%' or p.codigo=76) ";
$stmt = $dbh->prepare($sqlGasto);
$stmt->execute();
$codCuentaGasto=0;
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $codCuentaGasto=$row['codigo'];
    $numeroCuentaGasto=$row['numero'];
    $montoGasto=$row['monto'];
}


$sqlIVA="SELECT p.codigo, p.numero, sum(cd.debe)as monto from comprobantes_detalle cd, plan_cuentas p
where p.codigo=cd.cod_cuenta and cd.cod_comprobante='$codComprobante' and p.codigo in (63,64) ";
$stmt = $dbh->prepare($sqlIVA);
$stmt->execute();
$codCuentaIVA=0;
$montoIVA=0;
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $codCuentaIVA=$row['codigo'];
    $numeroCuentaIVA=$row['numero'];
    $montoIVA=$row['monto'];
}

$sqlPasivo="SELECT p.codigo, p.numero, sum(cd.haber)as monto from comprobantes_detalle cd, plan_cuentas p
where p.codigo=cd.cod_cuenta and cd.cod_comprobante='$codComprobante' and 
p.numero like '2%'";
$stmt = $dbh->prepare($sqlPasivo);
$stmt->execute();
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $codCuentaPasivo=$row['codigo'];
    $numeroCuentaPasivo=$row['numero'];
    $montoPasivo=$row['monto'];
}

$montoActualizar=$montoPasivo*0.87;

$totalDebe=$montoGasto+$montoIVA;
$totalHaber=$montoPasivo;

if( abs($totalDebe-$totalHaber)>0.001 && $montoIVA>0){
	$sqlUpd="UPDATE comprobantes_detalle set debe='$montoActualizar' where cod_comprobante='$codComprobante' 
	and cod_cuenta='$codCuentaGasto' ";
	$stmtUpd = $dbh->prepare($sqlUpd);
	$stmtUpd->execute();
	echo "**** MONTO ACTUALIZADO CORRECTAMENTE*** ";
}else{
	echo "Sin update.";
}

//echo $totalDebe."-".$totalHaber;




?>