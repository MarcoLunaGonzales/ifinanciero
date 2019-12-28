<?php

require_once '../conexion.php';
require_once '../functions.php';
require_once '../styles.php';

$dbh = new Conexion();

$sqlX="SET NAMES 'utf8'";
$stmtX = $dbh->prepare($sqlX);
$stmtX->execute();

session_start();
$globalAdmin=$_SESSION["globalAdmin"];
$globalGestion=$_SESSION["globalGestion"];
$globalUnidad=$_SESSION["globalUnidad"];
$globalArea=$_SESSION["globalArea"];

$fechaActual=date("d/m/Y");
//$codCuenta=$_GET['cod_cuenta'];
$codDet=$_GET['cod_det'];
$stmt = $dbh->prepare("SELECT d.glosa FROM estados_cuenta e,comprobantes_detalle d where e.cod_comprobantedetalle=d.codigo and e.cod_comprobantedetalle=$codDet");
$stmt->execute();
$glosaX="";
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $glosaX=$row['glosa']; 
    echo $glosaX."";
}
?>
        