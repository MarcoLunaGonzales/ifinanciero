<?php
require_once '../conexion.php';
require_once '../functions.php';
require_once 'configModule.php';
$dbh = new Conexion();
$codigoLote=$_GET["cod"];
if($_GET['a']==1){
  require_once '../layouts/bodylogin.php';
  require_once '../functionsGeneral.php';
  $sqlUpdate="UPDATE pagos_proveedores SET  cod_ebisa=1 where cod_pagolote=$codigoLote;
  UPDATE pagos_lotes SET  cod_ebisalote=1 where codigo=$codigoLote;";
  $stmtUpdate = $dbh->prepare($sqlUpdate);
  $flagSuccess=$stmtUpdate->execute();
  if($flagSuccess==true){
    showAlertSuccessError(true,"../".$urlListPagoLotes); 
  }else{
    showAlertSuccessError(false,"../".$urlListPagoLotes);
  }
}else{ ?>
<meta charset="utf-8">
<?php
header("Pragma: public");
header("Expires: 0");
$filename = "reporte_movimientos.xls";
header("Content-type: application/x-msdownload");
header("Content-Disposition: attachment; filename=$filename");
header("Pragma: no-cache");
header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
   
?>

<table>
  <tr><td>REGIONAL</td><td>CODIGO</td><td>CUENTA</td><td>AUXILIAR</td><td>DEBE</td><td>HABER</td><td>GLOSA</td><td>TIPO PAGO</td><td>OBSERVACIONES</td></tr>
<?php
  $sqlLote="SELECT pp.codigo,ppd.cod_proveedor,(select tp.nombre from tipos_pagoproveedor tp where tp.codigo=ppd.cod_tipopagoproveedor)as tipopagoproveedor,ppd.monto,ppd.observaciones,DATE_FORMAT(ppd.fecha,'%d/%m/%Y') as fecha,cd.cod_cuenta,cd.cod_unidadorganizacional
 from pagos_proveedores pp join pagos_proveedoresdetalle ppd on ppd.cod_pagoproveedor=pp.codigo join comprobantes_detalle cd on cd.codigo=ppd.cod_solicitudrecursosdetalle
   where pp.cod_pagolote=$codigoLote";
  $stmtLote = $dbh->prepare($sqlLote);
  $stmtLote->execute();
  while ($rowLote = $stmtLote->fetch(PDO::FETCH_ASSOC)) {
    $codigo=$rowLote['codigo'];
    $cod_cuenta=$rowLote['cod_cuenta'];
    $cod_unidadorganizacional=$rowLote['cod_unidadorganizacional'];
    $cod_proveedor=$rowLote['cod_proveedor'];
    $tipopagoproveedor=$rowLote['tipopagoproveedor'];
    $monto=$rowLote['monto'];
    $observaciones=$rowLote['observaciones'];
    ?>
      <tr><td><?=nameUnidad($cod_unidadorganizacional)?></td><td></td><td><?=obtieneNumeroCuenta($cod_cuenta)?></td><td></td><td><?=$monto?></td><td></td><td><?=$observaciones?></td><td><?=$tipopagoproveedor?></td><td></td></tr>
    <?php
   
  }?>
</table>
<?php

}

?>