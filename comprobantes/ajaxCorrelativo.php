<?php
session_start();
require_once '../conexion.php';
require_once '../functions.php';
$dbh = new Conexion();
$sqlX="SET NAMES 'utf8'";
$stmtX = $dbh->prepare($sqlX);
$stmtX->execute();

$tipoComprobante=$_GET["tipo_comprobante"];
$anio=$_SESSION["globalNombreGestion"];
$mes=date('m');
$codGestion=$_SESSION["globalGestion"];
$codMes=$_SESSION["globalMes"];

$unidad=$_SESSION['globalUnidad'];

$nroCorrelativo=numeroCorrelativoComprobante($codGestion,$unidad,$tipoComprobante,$codMes);
?>

<input class="form-control" type="number" name="nro_correlativo" id="nro_correlativo" min="1" required="true" value="<?=$nroCorrelativo;?>"  readonly="true" style="background-color:#E3CEF6;text-align: left"/>
