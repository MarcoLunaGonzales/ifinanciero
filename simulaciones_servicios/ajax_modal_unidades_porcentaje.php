<?php
require_once 'configModule.php';
require_once '../functions.php';
$id=$_GET['id'];
$porcentaje_area=$_GET['porcentaje_area'];
$monto_total=$_GET['monto_total'];
$codigo_area=$_GET['codigo_area'];
$nombre_area=nameArea($codigo_area);
$monto_total=$porcentaje_area*$monto_total/100;//el monto total lo convertimos al monto del porcentaje
?>
<div class="row col-xs-3">
	<label class="col-sm-12 col-form-label text-center" style="color:#000000; ">Area: <?=$nombre_area?></label>
</div>
<div class="row">
	<label class="col-sm-3 col-form-label text-right" style="color:#000000; ">Monto de Distribución</label>
	<div class="col-sm-2">
	  <div class="form-group">
	    <input type="hidden" name="monto_total_ingreso_unidades" id="monto_total_ingreso_unidades" value="<?=$monto_total?>" readonly="true">
	    <input type="number" class="form-control"  value="<?=number_format($monto_total,2,".","");?>" readonly="true" style="background-color:#E3CEF6;text-align: left">
	  </div>
	</div> 
	<label class="col-sm-3 col-form-label text-right" style="color:#000000; ">Porcentaje de Distribución</label>
	<div class="col-sm-2">
	  <div class="form-group">
	    <!-- <input type="hidden" name="porcentaje_total_ingreso_Unidad" id="porcentaje_total_ingreso_Unidad" value="<?=$porcentaje_area?>" readonly="true"> -->
	    <input type="number" class="form-control"  value="<?=number_format($porcentaje_area,2,".","");?>" readonly="true" style="background-color:#E3CEF6;text-align: left">
	  </div>
	</div>  	
</div>

