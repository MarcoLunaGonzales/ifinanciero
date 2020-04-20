<?php
session_start();
require_once '../conexion.php';
require_once '../functions.php';
require_once '../functionsGeneral.php';
require_once 'configModule.php';

$dbh = new Conexion();


$simulaciones=$_GET["simulaciones"];
$codDet=$_GET["cod_detalle"];
$tipo=$_GET["cod_tipoau"];
$monto=$_GET["monto"];
$montoe=$_GET["montoe"];
$extlocal=$_GET["extlocal"];
$dias=$_GET["dias"];
$cantidad=$_GET["cantidad"];
$anio=$_GET["anio"];
$habilitadoP=$_GET["habilitadoP"];
//HONORARIOS AUDITORES
$dias_honorarios=$_GET["dias_honorarios"];
$monto_honorarios=$_GET["monto_honorarios"];

if($habilitadoP!=0){
  if(verificarTipoAuditorMontosVariables($simulaciones,$tipo,$codDet,$anio)==0){
	$sqlUpdateDetalle="INSERT INTO simulaciones_ssd_ssa (monto,monto_externo,dias,cantidad,cod_simulacionservicio,cod_simulacionserviciodetalle,cod_simulacionservicioauditor,cod_anio) 
	VALUES('$monto','$montoe','$dias',$cantidad,$simulaciones,'$codDet','$tipo',$anio)";
    $stmtUpdateDetalle = $dbh->prepare($sqlUpdateDetalle);
    $stmtUpdateDetalle->execute();
  }else{
    $sqlUpdateDetalle="UPDATE simulaciones_ssd_ssa SET  monto='$monto',monto_externo='$montoe',dias='$dias',cantidad=$cantidad where cod_simulacionservicio=$simulaciones and cod_simulacionserviciodetalle='$codDet' and cod_simulacionservicioauditor='$tipo' and cod_anio=$anio";
    $stmtUpdateDetalle = $dbh->prepare($sqlUpdateDetalle);
    $stmtUpdateDetalle->execute();	
  }	
}

$sqlDetalles="UPDATE simulaciones_servicios_auditores SET monto=$monto_honorarios,dias=$dias_honorarios,habilitado=$habilitadoP where codigo=$tipo";
$stmtDetalles = $dbh->prepare($sqlDetalles);
$stmtDetalles->execute();


if(isset($_GET["otroanio"])){
	$anios=json_decode($_GET["otroanio"]);
	for ($i=0; $i < count($anios) ; $i++) {
	$aniose=$anios[$i]; 
	//obtenemos el codigo del auditor similar para el anio copiado
	$listaAuditor=listarSimulacionServicioAuditorParaCopiar($simulaciones,$tipo,$aniose,obtenerGlosaSimulacionServicioDetalle($codDet));
	 while ($row = $listaAuditor->fetch(PDO::FETCH_ASSOC)) {
	   $codDet2=$row['codigo'];
       $tipo2=$row['cod_simulacionservicioauditor']; //obtenemos el codigo de simulaciones_servicios_auditores
	   if(verificarTipoAuditorMontosVariables($simulaciones,$tipo2,$codDet2,$aniose)==0){
	    $sqlUpdateDetalle="INSERT INTO simulaciones_ssd_ssa (monto,monto_externo,dias,cantidad,cod_simulacionservicio,cod_simulacionserviciodetalle,cod_simulacionservicioauditor,cod_anio) 
	    VALUES('$monto','$montoe','$dias',$cantidad,$simulaciones,'$codDet2','$tipo2',$aniose)";
        $stmtUpdateDetalle = $dbh->prepare($sqlUpdateDetalle);
        $stmtUpdateDetalle->execute();
       }else{
        $sqlUpdateDetalle="UPDATE simulaciones_ssd_ssa SET  monto='$monto',monto_externo='$montoe',dias='$dias',cantidad=$cantidad where cod_simulacionservicio=$simulaciones and cod_simulacionserviciodetalle='$codDet2' and cod_simulacionservicioauditor='$tipo2' and cod_anio=$aniose";
        $stmtUpdateDetalle = $dbh->prepare($sqlUpdateDetalle);
        $stmtUpdateDetalle->execute();	
       }	
	 }
	}
}

?>