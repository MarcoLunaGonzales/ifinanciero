<?php
require_once 'configModule.php';
$cod_tipopago=$_GET['cod_tipopago'];
$monto_total=$_GET['monto_total'];
?>
<label class="col-sm-6 col-form-label text-right" style="color:#000000; ">Monto Total de Solicitud de Facturación</label>
<div class="col-sm-4">
  <div class="form-group">
    <input type="hidden" name="monto_total_ingreso_tipopago" id="monto_total_ingreso_tipopago" value="<?=$monto_total?>" readonly="true">
    <input type="number" class="form-control"  value="<?=number_format($monto_total,2,".","");?>" readonly="true" style="background-color:#E3CEF6;text-align: left">
  </div>
</div>  

