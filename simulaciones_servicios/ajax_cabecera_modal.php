<?php
require_once '../conexion.php';
require_once 'configModule.php';

//header('Content-Type: application/json');

$uo = $_GET["uo"];
$area = $_GET["area"];
$nombre_simulacion = $_GET["nombre_simulacion"];
$area_simulacion = $_GET["area_simulacion"];

$fecha_registro = $_GET["fecha_registro"];
$fecha_facturar = $_GET["fecha_facturar"];
$nit = $_GET["nit"];
$razon_social = $_GET["razon_social"];
?>
<label class="col-sm-2 col-form-label" style="color:#000000; ">Oficina :</label>
<div class="col-sm-2">
  <div class="form-group">
  	<input type="text" class="form-control" readonly="true" value="<?=$uo?>" style="background-color:#E3CEF6;text-align: left" >
  </div>
</div>  
<label class="col-sm-1 col-form-label" style="color:#000000; ">Area :</label>
<div class="col-sm-1">
  <div class="form-group">
  	<input type="text" class="form-control" readonly="true" value="<?=$area?>" style="background-color:#E3CEF6;text-align: left">
  </div>
</div>  
<label class="col-sm-1 col-form-label" style="color:#000000; ">Nombre Propuesta :</label>
<div class="col-sm-2">
  <div class="form-group">
  	<input type="text" class="form-control" readonly="true" value="<?=$nombre_simulacion?> - <?=$area_simulacion?>" style="background-color:#E3CEF6;text-align: left" >
  </div>
</div>  
<label class="col-sm-1 col-form-label" style="color:#000000; ">F.Registro:</label>
<div class="col-sm-2">
  <div class="form-group">
  	<input type="text" class="form-control" readonly="true" value="<?=$fecha_registro?>" style="background-color:#E3CEF6;text-align: left" >
  </div>
</div> 
<label class="col-sm-2 col-form-label" style="color:#000000; ">F.Facturar</label>
<div class="col-sm-2">
  <div class="form-group">
  	<input type="text" class="form-control" readonly="true" value="<?=$fecha_facturar?>" style="background-color:#E3CEF6;text-align: left" >
  </div>
</div> 
<label class="col-sm-1 col-form-label" style="color:#000000; ">Nit</label>
<div class="col-sm-2">
  <div class="form-group">
  	<input type="text" class="form-control" readonly="true" value="<?=$nit?>" style="background-color:#E3CEF6;text-align: left" >
  </div>
</div> 
<label class="col-sm-1 col-form-label" style="color:#000000; ">Razón Social</label>
<div class="col-sm-3">
  <div class="form-group">
  	<input type="text" class="form-control" readonly="true" value="<?=$razon_social?>" style="background-color:#E3CEF6;text-align: left" >
  </div>
</div> 
