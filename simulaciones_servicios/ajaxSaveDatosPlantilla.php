<?php
require_once '../conexion.php';
require_once '../functions.php';
require_once '../functionsGeneral.php';
require_once 'configModule.php';

$dbh = new Conexion();

$codigo=$_GET["codigo"];
session_start();

$codPlantillaCosto=$_GET["plantilla"];
$codSimulacion=$_GET["simulacion"];
$ut_i=$_GET['utilidad'];
$dia=$_GET['dia'];


$monto=$_GET['monto'];
$cantidad=$_GET['cantidad'];
$habilitado=$_GET['habilitado'];

$sqlUpdatePlantilla="UPDATE simulaciones_servicios SET  utilidad_minima='$ut_i',dias_auditoria='$dia' where codigo=$codSimulacion";
$stmtUpdatePlantilla = $dbh->prepare($sqlUpdatePlantilla);
$stmtUpdatePlantilla->execute();


$sqlDetalles="UPDATE simulaciones_servicios_auditores SET cantidad_editado=$cantidad,monto=$monto,habilitado=$habilitado where codigo=$codigo";
$stmtDetalles = $dbh->prepare($sqlDetalles);
$stmtDetalles->execute();

$sqlDet="SELECT * FROM simulaciones_serviciodetalle where cod_simulacionservicio=$codSimulacion";
$stmtDet = $dbh->prepare($sqlDet);
$stmtDet->execute();

 while ($rowPre = $stmtDet->fetch(PDO::FETCH_ASSOC)) {
 	$cantidadDet=$rowPre['cantidad'];
 	$codigoDet=$rowPre['codigo'];
 	$montoDet=$rowPre['editado_personal'];
 	$partidaDet=$rowPre['cod_partidapresupuestaria'];
 	$cuenta=$rowPre['cod_cuenta'];
 	if($cantidadDet>$cantidad){
 		$montoTotalDet=$montoDet*$cantidad;
 		$sqlDetalles="UPDATE simulaciones_serviciodetalle SET cantidad=$cantidad,monto_total=$montoTotalDet,monto_unitario=$montoTotalDet where codigo=$codigoDet";
        $stmtDetalles = $dbh->prepare($sqlDetalles);
        $stmtDetalles->execute();
        $dbhC = new Conexion();
        $sqlCuentas="SELECT * FROM cuentas_simulacion where cod_simulacionservicios=$codSimulacion";
        $stmtCuentas = $dbhC->prepare($sqlCuentas);
        $stmtCuentas->execute();
       while ($rowCuentas = $stmtCuentas->fetch(PDO::FETCH_ASSOC)) {
          $simulacion=$rowCuentas['codigo'];  
          $montoTotal=0;
           $detallesMontos=obtenerMontosCuentasDetalleSimulacionServicioPartidaHabilitado($codSimulacion,$partidaDet);
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
}

echo "OK";
?>
