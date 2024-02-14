<?php
require_once '../conexion.php';
require_once '../functions.php';


$codigoComprobanteX=$_GET['cod_comprobante'];


$dbh = new Conexion();
$sql="SELECT cd.cod_comprobante,sum(cd.debe)as debe,sum(cd.haber)as haber, abs(sum(cd.debe-cd.haber))as dif, c.fecha, count(*)as contador FROM comprobantes c,comprobantes_detalle cd WHERE c.codigo=cd.cod_comprobante AND c.cod_gestion=2023 and c.fecha BETWEEN '2023-01-01 00:00:00' and '2023-12-31 23:59:59' and c.codigo='$codigoComprobanteX'";

$stmt = $dbh->prepare($sql);
$stmt->execute();
$stmt->bindColumn('cod_comprobante', $codCabecera);
$stmt->bindColumn('debe', $montoDebe);
$stmt->bindColumn('haber', $montoHaber);
$stmt->bindColumn('dif', $diferencia);
$stmt->bindColumn('fecha', $fecha);

while ($row = $stmt->fetch(PDO::FETCH_BOUND)) {
  echo "<table border=1 width='70%'>";
  echo "<tr><th>$codCabecera</th><th>$montoDebe</th><th>$montoHaber</th><th>$diferencia</th><th>$fecha <a href='javascript:corregirComprobante($codCabecera);'>Corregir</a><div id='div$codCabecera'></div></th></tr>";

  $sqlDetalle="select cd.codigo, concat(p.numero,'-',p.nombre)as cuenta, cd.debe, cd.haber, SUBSTRING(cd.glosa, 1, 30)as glosa from comprobantes_detalle cd, plan_cuentas p where p.codigo=cd.cod_cuenta and cd.cod_comprobante=$codCabecera;";
  $stmtDet = $dbh->prepare($sqlDetalle);
  $stmtDet->execute();
  $stmtDet->bindColumn('codigo', $codigoDetalle);
  $stmtDet->bindColumn('cuenta', $cuenta);
  $stmtDet->bindColumn('debe', $montoDebeDetalle);
  $stmtDet->bindColumn('haber', $montoHaberDetalle);
  $stmtDet->bindColumn('glosa', $glosaDetalle);

  $montoMayor=0;
  $subTotalDebe=0;
  $subTotalHaber=0;
  while ($rowDetalle = $stmtDet->fetch(PDO::FETCH_BOUND)) {
    if($montoDebeDetalle>0){
      if( $montoDebeDetalle>$montoMayor && ($cuenta!=63 || $cuenta!=64) ){
        $montoMayor=$montoDebeDetalle;
        $cuentaActualizarDetalle=$cuenta;  
        $codigoComprobanteDetalle=$codigoDetalle;

        echo "<tr><td>($codigoDetalle) $cuenta</td><td>$montoDebeDetalle</td><td>$montoHaberDetalle</td><td>-</td><td>$glosaDetalle</td><td>-</td></tr>";
      }else{
        echo "<tr><td>($codigoDetalle) $cuenta</td><td>$montoDebeDetalle</td><td>$montoHaberDetalle</td><td>-</td><td>$glosaDetalle</td><td>-</td></tr>";
      }
      $subTotalDebe+=$montoDebeDetalle;
    }else{
      $subTotalHaber+=$montoHaberDetalle;
      $diferenciaSubtotal=$subTotalDebe-$subTotalHaber;
      echo "<tr><td>($codigoDetalle) $cuenta</td><td>$montoDebeDetalle</td><td>$montoHaberDetalle</td><td>-</td><td>$glosaDetalle</td><td>$cuentaActualizarDetalle $montoMayor $diferenciaSubtotal</td></tr>";
      if($diferenciaSubtotal!=0){
        $montoActualizarDebe=$montoMayor+($diferenciaSubtotal*(-1));
        echo "update comprobantes_detalle set debe='$montoActualizarDebe' where codigo='$codigoComprobanteDetalle';"."/*$cuentaActualizarDetalle*/"."<br>";
      }

      $montoMayor=0;
      $cuentaActualizarDetalle=0;
      $subTotalHaber=0;
      $subTotalDebe=0;
      $diferenciaSubtotal=0;
    }
  }

  echo "</table><br><br>";
}

?>