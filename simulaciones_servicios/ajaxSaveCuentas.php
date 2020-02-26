<?php
session_start();
require_once '../conexion.php';
require_once '../functions.php';
require_once '../functionsGeneral.php';
require_once 'configModule.php';

$dbh = new Conexion();

$codigo=$_GET["codigo"];
$monto=$_GET["monto"];
$ibnorca=$_GET["ibnorca"];
$simulacion=$_GET["simulacion"];
$simulaciones=$_GET["simulaciones"];
$plantilla=$_GET["plantilla"];
$partida=$_GET["partida"];
$cuenta=$_GET["cuenta"];
$habilitado=$_GET["habilitado"];
$cantidad=$_GET["cantidad"];


$cantidad=obtenerCantidadTotalSimulacionesServiciosDetalleAuditor($simulaciones,$codigo);
$monto=obtenerMontoTotalSimulacionesServiciosDetalleAuditor($simulaciones,$codigo);
$montoEditado=$monto/$cantidad;


$sqlUpdateDetalle="UPDATE simulaciones_serviciodetalle SET  monto_unitario='$monto',monto_total='$monto',habilitado=$habilitado,cantidad=$cantidad where codigo=$codigo";
$stmtUpdateDetalle = $dbh->prepare($sqlUpdateDetalle);
$stmtUpdateDetalle->execute();
$montoTotal=0;
$detallesMontos=obtenerMontosCuentasDetalleSimulacionServicioPartidaHabilitado($simulaciones,$partida);
while ($row = $detallesMontos->fetch(PDO::FETCH_ASSOC)) {
	if($row['cod_cuenta']==$cuenta){
    if($row['habilitado']==0){
      $montoTotal+=0;
    }else{
		$montoTotal+=$row['monto'];    
    }          
	}
}
$dbh2 = new Conexion();
     if($ibnorca==1){
       $sqlUpdate="UPDATE cuentas_simulacion SET  monto_local='$montoTotal' where codigo=$simulacion and cod_plancuenta=$cuenta"; 
      }else{
       $sqlUpdate="UPDATE cuentas_simulacion SET  monto_externo='$montoTotal' where codigo=$simulacion and cod_plancuenta=$cuenta";
      }
      $stmtUpdate = $dbh2->prepare($sqlUpdate);
      $flagSuccess=$stmtUpdate->execute();

?>