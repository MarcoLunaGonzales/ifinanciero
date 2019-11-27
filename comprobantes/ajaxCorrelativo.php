<?php
require_once '../conexion.php';
require_once '../functions.php';
$dbh = new Conexion();
$sqlX="SET NAMES 'utf8'";
$stmtX = $dbh->prepare($sqlX);
$stmtX->execute();

$tipoComprobante=$_GET["tipo_comprobante"];

$sql="SELECT IFNULL(max(c.codigo)+1,1)as codigo from comprobantes c where c.cod_tipocomprobante='$tipoComprobante'";
$stmt = $dbh->prepare($sql);
$stmt->execute();
$nroCorrelativo=0;
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
  $nroCorrelativo=$row['codigo'];
}
?>

<input class="form-control" type="number" name="nro_correlativo" id="nro_correlativo" min="1" required="true" value="<?=$nroCorrelativo;?>"  readonly="true"/>
