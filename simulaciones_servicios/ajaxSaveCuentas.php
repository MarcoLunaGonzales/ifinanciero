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
$anio=$_GET["anio"];

$cantidad=obtenerCantidadTotalSimulacionesServiciosDetalleAuditorPeriodo($simulaciones,$codigo,$anio);
$monto=obtenerMontoTotalSimulacionesServiciosDetalleAuditorPeriodo($simulaciones,$codigo,$anio);
$montoEditado=$monto/$cantidad;


$sqlUpdateDetalle="UPDATE simulaciones_serviciodetalle SET  monto_unitario='$monto',monto_total='$monto',habilitado=$habilitado,cantidad=$cantidad where codigo=$codigo and cod_anio=$anio";
$stmtUpdateDetalle = $dbh->prepare($sqlUpdateDetalle);
$stmtUpdateDetalle->execute();
$montoTotal=0;
$detallesMontos=obtenerMontosCuentasDetalleSimulacionServicioPartidaHabilitadoPeriodo($simulaciones,$partida,$anio);
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
       $sqlUpdate="UPDATE cuentas_simulacion SET  monto_local='$montoTotal' where codigo=$simulacion and cod_plancuenta=$cuenta and cod_anio=$anio"; 
      }else{
       $sqlUpdate="UPDATE cuentas_simulacion SET  monto_externo='$montoTotal' where codigo=$simulacion and cod_plancuenta=$cuenta and cod_anio=$anio";
      }
      $stmtUpdate = $dbh2->prepare($sqlUpdate);
      $flagSuccess=$stmtUpdate->execute();

/*if(isset($_GET["otroanio"])){
  $anios=json_decode($_GET["otroanio"]);
  for ($i=0; $i < count($anios) ; $i++) {
  $aniose=$anios[$i]; 
    $codigo2=obtenerCodigoSimulacionServicioDetalleGlosa($simulaciones,$codigo,$aniose);
    $cantidad=obtenerCantidadTotalSimulacionesServiciosDetalleAuditorPeriodo($simulaciones,$codigo,$anio);
$monto=obtenerMontoTotalSimulacionesServiciosDetalleAuditorPeriodo($simulaciones,$codigo,$anio);
$montoEditado=$monto/$cantidad;


$sqlUpdateDetalle="UPDATE simulaciones_serviciodetalle SET  monto_unitario='$monto',monto_total='$monto',habilitado=$habilitado,cantidad=$cantidad where codigo=$codigo2 and cod_anio=$aniose";
$stmtUpdateDetalle = $dbh->prepare($sqlUpdateDetalle);
$stmtUpdateDetalle->execute();
echo $sqlUpdateDetalle;
$montoTotal=0;
$detallesMontos=obtenerMontosCuentasDetalleSimulacionServicioPartidaHabilitadoPeriodo($simulaciones,$partida,$aniose);
while ($row = $detallesMontos->fetch(PDO::FETCH_ASSOC)) {
  if($row['cod_cuenta']==$cuenta){
    if($row['habilitado']==0){
      $montoTotal+=0;
    }else{
    $montoTotal+=$row['monto'];    
    }          
  }
}
$simulacionCopy=obtenerCodigoCuentasSimulacionServicio($simulaciones,$aniose);
$dbh2 = new Conexion();
     if($ibnorca==1){
       $sqlUpdate="UPDATE cuentas_simulacion SET  monto_local='$montoTotal' where codigo=$simulacionCopy and cod_plancuenta=$cuenta and cod_anio=$aniose"; 
      }else{
       $sqlUpdate="UPDATE cuentas_simulacion SET  monto_externo='$montoTotal' where codigo=$simulacionCopy and cod_plancuenta=$cuenta and cod_anio=$aniose";
      }
      $stmtUpdate = $dbh2->prepare($sqlUpdate);
      $flagSuccess=$stmtUpdate->execute();

  }
}*/
?>