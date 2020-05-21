<?php
require_once '../conexion.php';
require_once '../functions.php';
require_once '../functionsGeneral.php';
require_once 'configModule.php';

$dbh = new Conexion();

$codigo=$_GET["codigo"];
session_start();

$codPlantillaCosto=$_GET["codigo"];
$codSimulacion=$_GET["cod_sim"];
$ut_i=$_GET['ut_i'];
$ut_f=$_GET['ut_f'];
$al_i=$_GET['al_i'];
$al_f=$_GET['al_f'];
$precio_p=$_GET['precio_p'];
$precio_pedit=$_GET['precio_pedit'];

$sqlUpdate="UPDATE precios_simulacioncosto SET  venta_local='$precio_pedit' where codigo=$precio_p";
$stmtUpdate = $dbh->prepare($sqlUpdate);
$flagSuccess=$stmtUpdate->execute();

$sqlUpdatePlantilla="UPDATE simulaciones_costos SET  cod_precioplantilla='$precio_p',utilidad_minimalocal='$ut_i',cantidad_alumnoslocal='$al_i' where codigo=$codSimulacion";
$stmtUpdatePlantilla = $dbh->prepare($sqlUpdatePlantilla);
$stmtUpdatePlantilla->execute();


$sqlDetalles="SELECT * FROM simulaciones_detalle where cod_simulacioncosto=$codSimulacion and editado_alumno!=0";
$stmtDetalles = $dbh->prepare($sqlDetalles);
$stmtDetalles->execute();

 while ($rowDet = $stmtDetalles->fetch(PDO::FETCH_ASSOC)) {
    $codDet=$rowDet['codigo'];
    $partida=$rowDet['cod_partidapresupuestaria'];
    $montoDet=$rowDet['editado_alumno']*$al_i;
    $cuenta=$rowDet['cod_cuenta'];
    $dbhDet = new Conexion();
    $sqlUpdateDetalle="UPDATE simulaciones_detalle SET  monto_unitario='$montoDet',monto_total='$montoDet' where codigo=$codDet";
    $stmtUpdateDetalle = $dbhDet->prepare($sqlUpdateDetalle);
    $stmtUpdateDetalle->execute();

//insertar en cuentas_simulacion
    $dbhC = new Conexion();
$sqlCuentas="SELECT * FROM cuentas_simulacion where cod_simulacioncostos=$codSimulacion";
$stmtCuentas = $dbhC->prepare($sqlCuentas);
$stmtCuentas->execute();
 while ($rowCuentas = $stmtCuentas->fetch(PDO::FETCH_ASSOC)) {
    $simulacion=$rowCuentas['codigo'];  
    $montoTotal=0;
   $detallesMontos=obtenerMontosCuentasDetalleSimulacionCostosPartidaHabilitado($codSimulacion,$partida);
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
    $sqlUpdate="UPDATE cuentas_simulacion SET  monto_local='$montoTotal' where codigo=$simulacion and cod_plancuenta=$cuenta";  
      $stmtUpdate = $dbh2->prepare($sqlUpdate);
      $stmtUpdate->execute();
 }
}


$precios=obtenerPreciosPorCodigo($precio_p);
echo $precios[0]."$$$".$precios[1];
?>
