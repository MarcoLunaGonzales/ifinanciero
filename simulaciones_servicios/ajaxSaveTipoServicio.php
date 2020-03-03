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
$obs="";
$cant=$_GET['cantidad'];
$monto=$_GET['monto'];
$unidad=$_GET['unidad'];
$anio=$_GET['anio'];

$sql1="SELECT * from simulaciones_servicios_tiposervicio where cod_simulacionservicio=$cod_sim and cod_claservicio=$cod_cla";
$stmt1 = $dbh->prepare($sql1);
$stmt1->execute();

$existe=0;
 while ($rowServ = $stmt1->fetch(PDO::FETCH_ASSOC)) {
    $existe=1;
}

if($existe==0){
    $sql="INSERT INTO simulaciones_servicios_tiposervicio (cod_simulacionservicio, cod_claservicio,observaciones,cantidad,monto,cantidad_editado,cod_estadoreferencial,cod_tipounidad,cod_anio) 
       VALUES ('".$cod_sim."','".$cod_cla."','".$obs."','".$cant."','".$monto."','".$cant."', 1,'".$unidad."','".$anio."')";
    $stmt = $dbh->prepare($sql);
    $stmt->execute();
   echo "0"; 
}else{
  echo "1"; 
}