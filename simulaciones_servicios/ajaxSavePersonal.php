<?php
session_start();
require_once '../conexion.php';
require_once '../functionsGeneral.php';
require_once '../functions.php';
require_once '../styles.php';

$dbh = new Conexion();

$sqlX="SET NAMES 'utf8'";
$stmtX = $dbh->prepare($sqlX);
$stmtX->execute();


$globalAdmin=$_SESSION["globalAdmin"];
$globalGestion=$_SESSION["globalGestion"];
$globalUnidad=$_SESSION["globalUnidad"];
$globalArea=$_SESSION["globalArea"];

$cod_sim=$_GET['cod_sim'];
$cod_cla=$_GET['cod_cla'];
$cant=$_GET['cantidad'];
$monto=$_GET['monto'];
$dias=$_GET['dias'];
$anio=$_GET['anio'];

$sql1="SELECT * from simulaciones_servicios_auditores where cod_simulacionservicio=$cod_sim and cod_tipoauditor=$cod_cla and cod_anio=$anio";
$stmt1 = $dbh->prepare($sql1);
$stmt1->execute();

$existe=0;$contador=0;
 while ($rowServ = $stmt1->fetch(PDO::FETCH_ASSOC)) {
    $existe=0; // valor 1 para habilitar que no se repitan los registros
    $contador++;
}

if($existe==0){
	$nombreTIPA=nameTipoAuditor($cod_cla)."(".($contador+1).")";
	$codSimulacionServicioAuditor=obtenerCodigoSimulacionServicioAuditor();
    $sql="INSERT INTO simulaciones_servicios_auditores (codigo,cod_simulacionservicio, cod_tipoauditor,cantidad,monto,cantidad_editado,cod_estadoreferencial,dias,cod_externolocal,cod_anio,descripcion) 
       VALUES ('".$codSimulacionServicioAuditor."','".$cod_sim."','".$cod_cla."','".$cant."','".$monto."','".$cant."', 1,'".$dias."',1,'".$anio."','".$nombreTIPA."')";
    $stmt = $dbh->prepare($sql);
    $stmt->execute();
   echo "0###".$codSimulacionServicioAuditor; 
}else{
  echo "1###NNN"; 
}