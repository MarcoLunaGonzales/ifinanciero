<?php

require_once 'conexion.php';
require_once 'functions.php';
require_once 'configModule.php';

$dbh = new Conexion();

$codRefrigerio=$cod_refrigerio;
$codMes=$cod_mes;
$codGestionActiva=$_SESSION['globalGestion'];
$cod_estadoreferencial="1";
$montoRefrigerio=obtenerValorRefrigerio();
echo $montoRefrigerio;

$diasAsistidosVacio="0";

// Preparamos
$stmt = $dbh->prepare("SELECT rd.codigo, 
rd.cod_refrigerio, 
p.codigo as cod_personal,
(select dap.dias_asistencia from dias_asistencia_personal dap where dap.cod_personal=p.codigo and dap.cod_mes=$codMes and dap.cod_gestion=$codGestionActiva) as dias_asistidos,
rd.monto 
from personal p LEFT JOIN refrigerios_detalle rd 
ON rd.cod_personal=p.codigo and rd.cod_estadoreferencial=1 and rd.cod_refrigerio=$codRefrigerio");
// Ejecutamos
$stmt->execute();
// bindColumn
$stmt->bindColumn('codigo', $codigo);
$stmt->bindColumn('cod_refrigerio', $cod_refrigerio);
$stmt->bindColumn('cod_personal', $codPersona);
$stmt->bindColumn('dias_asistidos', $dias_asistidos);
$stmt->bindColumn('monto', $monto);


while ($row = $stmt->fetch(PDO::FETCH_BOUND)) {
    $codigoX=$codigo;
    $cod_refrigerioX=$cod_refrigerio;
    $codPersonaX=$codPersona;
    $dias_asistidosX=$dias_asistidos;

  if($dias_asistidosX!=0){
    if($codigoX==null){
      if($dias_asistidosX!=null){
    $sql = $dbh->prepare("INSERT INTO refrigerios_detalle (cod_refrigerio,cod_personal,dias_asistidos,monto,cod_estadoreferencial)
    VALUES ($codRefrigerio,$codPersonaX,$dias_asistidosX,$montoRefrigerio,$cod_estadoreferencial) ");

    $flagSuccess=$sql->execute();
      }
      else{
        $sql = $dbh->prepare("INSERT INTO refrigerios_detalle (cod_refrigerio,cod_personal,dias_asistidos,monto,cod_estadoreferencial)
    VALUES ($codRefrigerio,$codPersonaX,$diasAsistidosVacio,$montoRefrigerio,$cod_estadoreferencial) ");

    $flagSuccess=$sql->execute();
      }
    }
    else
    {
      if($dias_asistidosX!=null){
      $sql = $dbh->prepare("UPDATE refrigerios_detalle set monto=$montoRefrigerio, dias_asistidos=$dias_asistidosX
      where codigo=$codigoX");

    $flagSuccess=$sql->execute();
      }
      else{
        $sql = $dbh->prepare("UPDATE refrigerios_detalle set monto=$montoRefrigerio, dias_asistidos=$diasAsistidosVacio
      where codigo=$codigoX");

    $flagSuccess=$sql->execute();
      }
    }

    }//DIAS ASISTIDOS IF
  }
  $flagSuccess=true;
  showAlertSuccessError($flagSuccess,$urlDetalle."&cod_ref=".$codRefrigerio."&cod_mes=".$codMes);

?>