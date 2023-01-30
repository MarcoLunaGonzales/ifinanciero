<?php
require_once '../conexion.php';
require_once '../functions.php';

$dbh = new Conexion();
$sql="SELECT c.codigo as cod_cabecera, cd.codigo as cod_detalle, cd.debe, cd.haber, cd.glosa from comprobantes c, comprobantes_detalle cd
where c.codigo=cd.cod_comprobante and c.cod_gestion=2022 and c.cod_estadocomprobante<>2";

$stmt = $dbh->prepare($sql);
$stmt->execute();
$stmt->bindColumn('cod_cabecera', $codCabecera);
$stmt->bindColumn('cod_detalle', $codDetalle);
$stmt->bindColumn('debe', $montoDebe);
$stmt->bindColumn('haber', $montoHaber);
$stmt->bindColumn('glosa', $glosa);

while ($rowDetalle = $stmt->fetch(PDO::FETCH_BOUND)) {
  $montoDebeRound=round($montoDebe,2);
  $montoHaberRound=round($montoHaber,2);

  echo "$codCabecera $codDetalle $montoDebe $montoHaber <br> $montoDebeRound $montoHaberRound<br>";

  /*$sqlUpd="update comprobantes_detalle set debe='$montoDebeRound', haber='$montoHaberRound' where codigo='$codDetalle' and cod_comprobante='$codCabecera'";
  $stmtUpdate = $dbh->prepare($sqlUpd);
  $stmtUpdate->execute();*/

}

?>