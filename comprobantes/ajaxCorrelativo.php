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

$unidad=$_SESSION['globalUnidad'];

$sql1="SELECT m.*,g.nombre from meses_trabajo m join gestiones g on m.cod_gestion=g.codigo where cod_gestion='$codGestion' and cod_estadomesestrabajo=3";
$stmt1 = $dbh->prepare($sql1);
$stmt1->execute();

while ($row1= $stmt1->fetch(PDO::FETCH_ASSOC)) {
	$mesActivo=$row1['cod_mes'];
}

$fechaInicio=$anio."-".$mesActivo."-01";

$fechaFin=date('Y-m-d',strtotime($fechaInicio.'+1 month'));
$fechaFin=date('Y-m-d',strtotime($fechaFin.'-1 day'));


$sql="SELECT IFNULL(max(c.numero)+1,1)as codigo from comprobantes c where c.cod_tipocomprobante='$tipoComprobante' and c.cod_unidadorganizacional=$unidad and c.fecha between '$fechaInicio 00:00:00' and '$fechaFin 23:59:59'";
//echo $sql;
$stmt = $dbh->prepare($sql);
$stmt->execute();
$nroCorrelativo=0;
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
  $nroCorrelativo=$row['codigo'];
}
?>

<input class="form-control" type="number" name="nro_correlativo" id="nro_correlativo" min="1" required="true" value="<?=$nroCorrelativo;?>"  readonly="true"/>
