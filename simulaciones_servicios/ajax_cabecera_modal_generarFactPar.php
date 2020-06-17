<?php
require_once '../conexion.php';
require_once '../functions.php';
require_once 'configModule.php';

//header('Content-Type: application/json');

$cod_solicitud = $_GET["cod_solicitud"];
// if(isset($_GET["cod_libreta"]))
//   $cod_libreta=$_GET["cod_solicitud"];
// else $cod_libreta=0;
//ini_set("display_errors", "1");
$db = new Conexion();
$stmt = $db->prepare("SELECT cod_unidadorganizacional,cod_area,cod_cliente,razon_social,nit,observaciones,nro_correlativo from solicitudes_facturacion where codigo=$cod_solicitud");
$stmt->execute();
$result = $stmt->fetch();
$cod_unidadorganizacional = $result['cod_unidadorganizacional'];
$cod_area = $result['cod_area'];
$cod_cliente = $result['cod_cliente'];
$razon_social = $result['razon_social'];
$nit = $result['nit'];
$observaciones = $result['observaciones'];
$nro_correlativo = $result['nro_correlativo'];
$nombre_uo=abrevUnidad($cod_unidadorganizacional);
$nombre_area=abrevArea($cod_area);
$nombre_cliente=nameCliente($cod_cliente);
?>
<label class="col-sm-2 col-form-label" style="color:#000000;text-align: right; ">Oficina :</label>
<div class="col-sm-1">
  <div class="form-group">
  	<input type="text" class="form-control" readonly="true" value="<?=$nombre_uo?>" style="background-color:#E3CEF6;text-align: left" >
  </div>
</div>  
<label class="col-xs-1 col-form-label" style="color:#000000; ">Area :</label>
<div class="col-sm-1">
  <div class="form-group">
  	<input type="text" class="form-control" readonly="true" value="<?=$nombre_area?>" style="background-color:#E3CEF6;text-align: left">
  </div>
</div>  
<label class="col-xs-1 col-form-label" style="color:#000000;">Cliente :</label>
<div class="col-sm-4">
  <div class="form-group">
  	<input type="text" class="form-control" readonly="true" value="<?=$nombre_cliente?>" style="background-color:#E3CEF6;text-align: left" >
  </div>
</div>  
<label class="col-xs-1 col-form-label" style="color:#000000; ">Nit :</label>
<div class="col-sm-2">
  <div class="form-group">
  	<input type="text" class="form-control" readonly="true" value="<?=$nit?>" style="background-color:#E3CEF6;text-align: left" >
  </div>
</div> 
<label class="col-xs-1 col-form-label" style="color:#000000;text-align: right; ">Razón social</label>
<div class="col-sm-5">
  <div class="form-group">
  	<input type="text" class="form-control" readonly="true" value="<?=$razon_social?>" style="background-color:#E3CEF6;text-align: left" >
  </div>
</div> 

<label class="col-xs-1 col-form-label" style="color:#000000; ">Observaciones</label>
<div class="col-sm-5">
  <div class="form-group">
  	<input type="text" class="form-control" readonly="true" value="<?=$observaciones?>" style="background-color:#E3CEF6;text-align: left" >
  </div>
</div> 
