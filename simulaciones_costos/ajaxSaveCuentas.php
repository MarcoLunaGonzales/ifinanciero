<?php
require_once '../conexion.php';
require_once '../functions.php';
require_once '../functionsGeneral.php';
require_once 'configModule.php';

$dbh = new Conexion();

$codigo=$_GET["codigo"];
$monto=$_GET["monto"];
$ibnorca=$_GET["ibnorca"];
$simulacion=$_GET["simulacion"];
$plantilla=$_GET["plantilla"];
$partida=$_GET["partida"];
$cuenta=$_GET["cuenta"];

session_start();
$sqlUpdateDetalle="UPDATE plantillas_tcpdetalle SET  monto_unitario='$monto',monto_total='$monto' where codigo=$codigo";
$stmtUpdateDetalle = $dbh->prepare($sqlUpdateDetalle);
$stmtUpdateDetalle->execute();

$detallesMontos=obtenerMontosCuentasDetallePlantillaCostosPartida($plantilla,$partida);
while ($row = $detallesMontos->fetch(PDO::FETCH_ASSOC)) {
	if($row['cod_cuenta']==$cuenta){
		$montoTotal=$row['monto'];
      if($ibnorca==1){
       $sqlUpdate="UPDATE cuentas_simulacion SET  monto_local='$montoTotal' where codigo=$simulacion";	
      }else{
       $sqlUpdate="UPDATE cuentas_simulacion SET  monto_externo='$montoTotal' where codigo=$simulacion";
      }
      $stmtUpdate = $dbh->prepare($sqlUpdate);
      $flagSuccess=$stmtUpdate->execute();
	}
}
/*
if($ibnorca==1){
  $sqlUpdate="UPDATE cuentas_simulacion SET  monto_local='$monto' where codigo=$codigo";	
}else{
  $sqlUpdate="UPDATE cuentas_simulacion SET  monto_externo='$monto' where codigo=$codigo";
}
$stmtUpdate = $dbh->prepare($sqlUpdate);
$flagSuccess=$stmtUpdate->execute();*/

?>