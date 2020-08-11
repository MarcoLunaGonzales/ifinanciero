<?php
require_once 'conexion.php';
require_once 'styles.php';
require_once 'configModule.php';

$dbh = new Conexion();

//RECIBIMOS LAS VARIABLES
$codigo=$codigo;
$cod_tcc=$cod_tcc;
$cod_a=$cod_a;
// echo "cod:".$cod_a."codigo:".$codigo;
if($cod_a==2){//borrado
	// Prepare
	$stmt = $dbh->prepare("UPDATE caja_chica set cod_estadoreferencial=2,cod_estado=2 where codigo=$codigo");
	$flagSuccess=$stmt->execute();
}elseif($cod_a==1){//cerrado
	$fecha_cierre=date('Y-m-d');
	$stmt = $dbh->prepare("UPDATE caja_chica set cod_estado=2,fecha_cierre='$fecha_cierre'  where codigo=$codigo");
	$flagSuccess=$stmt->execute();

	// $sql_rendicion="SELECT SUM(monto) monto_total from caja_chicadetalle where cod_cajachica=$codigo and cod_estadoreferencial=1";
    $sql_rendicion="SELECT SUM(c.monto)-IFNULL((select SUM(r.monto) from caja_chicareembolsos r where r.cod_cajachica=$codigo and r.cod_estadoreferencial=1),0) as monto_total from caja_chicadetalle c where c.cod_cajachica=$codigo and c.cod_estadoreferencial=1";
    $stmtSaldo = $dbh->prepare($sql_rendicion);
    $stmtSaldo->execute();
    $resultSaldo=$stmtSaldo->fetch();
    if($resultSaldo['monto_total']!=null || $resultSaldo['monto_total']!='')
      $monto_total=$resultSaldo['monto_total'];
    else $monto_total=0; 

    $stmtReembolso = $dbh->prepare("UPDATE caja_chica set monto_reembolso=$monto_total where codigo=$codigo");
    $stmtReembolso->execute();
    
}elseif($cod_a==3){//abrir
    $stmt = $dbh->prepare("UPDATE caja_chica set cod_estado=1  where codigo=$codigo");
    $flagSuccess=$stmt->execute();
}

showAlertSuccessError($flagSuccess,$urlListCajaChica."&codigo=".$cod_tcc);

?>