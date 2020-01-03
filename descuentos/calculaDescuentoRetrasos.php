<?php

require_once 'conexion.php';
require_once 'functions.php';
require_once 'configModule.php';

$dbh = new Conexion();

$codDescuento=$codigo_descuento;
$codMes=$codigo_mes;
$codGestionActiva=$_SESSION['globalGestion'];
$codEstado="1";

// Preparamos
$stmt = $dbh->prepare("SELECT rp.cod_personal, rp.minutos_retraso from retrasos_personal rp where cod_estadoreferencial=1 and cod_gestion=$codGestionActiva and cod_mes=$codMes
and rp.cod_personal NOT IN (
SELECT dpm.cod_personal FROM descuentos_personal_mes dpm WHERE dpm.cod_descuento=$codDescuento and dpm.cod_gestion=$codGestionActiva and dpm.cod_mes=$codMes and dpm.cod_estadoreferencial=1)");
// Ejecutamos
$stmt->execute();
// bindColumn
$stmt->bindColumn('cod_personal', $codPersonal);
$stmt->bindColumn('minutos_retraso', $minutos_retraso);


while ($row = $stmt->fetch(PDO::FETCH_BOUND)) {
    $codPersonalX= $codPersonal;
    $minutos_retrasoX=$minutos_retraso;
    $descuentoRetraso=calculaMontoDescuentoRetraso($minutos_retrasoX, $codPersonalX);

    if($descuentoRetraso!=null){

    $sql = $dbh->prepare("INSERT INTO descuentos_personal_mes (cod_descuento,cod_personal,cod_gestion,cod_mes,monto,cod_estadoreferencial)
    VALUES ($codDescuento,$codPersonalX,$codGestionActiva,$codMes,$descuentoRetraso,$codEstado) ");
    $flagSuccess=$sql->execute();
    }else{
      $flagSuccess=true;
    }
      
  }

  showAlertSuccessError($flagSuccess,$urlListMesPersona."&cod_descuento=".$codDescuento."&cod_mes=".$codMes);

?>