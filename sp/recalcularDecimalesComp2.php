<script>
function nuevoAjax()
{ var xmlhttp=false;
  try {
      xmlhttp = new ActiveXObject('Msxml2.XMLHTTP');
  } catch (e) {
  try {
    xmlhttp = new ActiveXObject('Microsoft.XMLHTTP');
  } catch (E) {
    xmlhttp = false;
  }
  }
  if (!xmlhttp && typeof XMLHttpRequest!='undefined') {
  xmlhttp = new XMLHttpRequest();
  }
  return xmlhttp;
}

function corregirComprobante(cod_comprobante){
  var contenedor;
  var codigo_comprobante=cod_comprobante;
  contenedor = document.getElementById('div'+cod_comprobante);
  ajax=nuevoAjax();
  ajax.open('GET', 'corrigeComprobanteDetAjax.php?cod_comprobante='+cod_comprobante,true);
  ajax.onreadystatechange=function() {
    if (ajax.readyState==4) {
      contenedor.innerHTML = ajax.responseText
    }
  }
  ajax.send(null)
}
</script>

<?php
require_once '../conexion.php';
require_once '../functions.php';

$dbh = new Conexion();
$sql="SELECT cd.cod_comprobante,sum(cd.debe)as debe,sum(cd.haber)as haber, abs(sum(cd.debe-cd.haber))as dif, c.fecha, count(*)as contador FROM comprobantes c,comprobantes_detalle cd WHERE c.codigo=cd.cod_comprobante AND c.cod_gestion=2023 and c.fecha BETWEEN '2023-01-01 00:00:00' and '2023-12-31 23:59:59' AND c.cod_estadocomprobante NOT IN (2) GROUP BY cd.cod_comprobante HAVING ABS(sum(cd.debe)-sum(cd.haber))> 0.001 and contador<=5 order by dif desc";

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

  $sqlDetalle="select concat(p.numero,'-',p.nombre)as cuenta, cd.debe, cd.haber, SUBSTRING(cd.glosa, 1, 30)as glosa from comprobantes_detalle cd, plan_cuentas p where p.codigo=cd.cod_cuenta and cd.cod_comprobante=$codCabecera;";
  $stmtDet = $dbh->prepare($sqlDetalle);
  $stmtDet->execute();
  $stmtDet->bindColumn('cuenta', $cuenta);
  $stmtDet->bindColumn('debe', $montoDebeDetalle);
  $stmtDet->bindColumn('haber', $montoHaberDetalle);
  $stmtDet->bindColumn('glosa', $glosaDetalle);

  while ($rowDetalle = $stmtDet->fetch(PDO::FETCH_BOUND)) {
    echo "<tr><td>$cuenta</td><td>$montoDebeDetalle</td><td>$montoHaberDetalle</td><td>-</td><td>$glosaDetalle</td></tr>";
  }

  echo "</table><br><br>";
}

?>