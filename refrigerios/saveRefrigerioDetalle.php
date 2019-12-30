<?php

require_once '../layouts/bodylogin.php';
require_once '../conexion.php';
require_once '../functionsGeneral.php';
require_once 'configModule.php';

$dbh = new Conexion();
$codPersona=$_POST["personal"];
$codRefrigerio=$_POST["codRefrigerio"];
$monto=$_POST["monto"];
$codEstado="1";

//Seleccionar el monto de refrigerio de configuraciones
$stmtb = $dbh->prepare("SELECT dap.dias_asistencia from dias_asistencia_personal dap where dap.cod_personal=$codPersona");
$stmtb->execute();
$stmtb->bindColumn('dias_asistencia', $dias_asistencia);

while ($row = $stmtb->fetch(PDO::FETCH_BOUND)) {
  $diasAsistenciaX = $dias_asistencia;
}


if($diasAsistenciaX!=null){

// Prepare
$stmt = $dbh->prepare("INSERT INTO refrigerios_detalle (cod_refrigerio, cod_personal,dias_asistidos,monto,cod_estadoreferencial) 
                        VALUES (:cod_refrigerio,:cod_personal,:dias_asistidos,:monto, :cod_estado)");
// Bind
$stmt->bindParam(':cod_refrigerio', $codRefrigerio);
$stmt->bindParam(':cod_estado', $codEstado);
$stmt->bindParam(':cod_personal',$codPersona);
$stmt->bindParam(':dias_asistidos',$diasAsistenciaX);
$stmt->bindParam(':monto',$monto);

$flagSuccess=$stmt->execute();

}else{
 // Prepare
$stmt = $dbh->prepare("INSERT INTO refrigerios_detalle (cod_refrigerio, cod_personal,dias_asistidos,monto,cod_estadoreferencial) 
VALUES (:cod_refrigerio,:cod_personal,0,:monto, :cod_estado)");
// Bind
$stmt->bindParam(':cod_refrigerio', $codRefrigerio);
$stmt->bindParam(':cod_estado', $codEstado);
$stmt->bindParam(':cod_personal',$codPersona);
$stmt->bindParam(':monto',$monto);

$flagSuccess=$stmt->execute();
}

showAlertSuccessError($flagSuccess,"../".$urlDetalle."&cod_ref=".$codRefrigerio);

?>
