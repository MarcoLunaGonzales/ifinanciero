<?php
require_once '../conexion.php';
require_once 'configModule.php';

//header('Content-Type: application/json');

$codigo_comprobante = $_GET["codigo_comprobante"];
//ini_set("display_errors", "1");
$db = new Conexion();

$sql="SELECT cod_unidadorganizacional,cod_area from comprobantes_detalle where codigo=$codigo_comprobante";
$stmtComprobante = $db->prepare($sql);
$stmtComprobante->execute();
$resultComprobante=$stmtComprobante->fetch();
$cod_uo=$resultComprobante['cod_unidadorganizacional'];
$cod_area=$resultComprobante['cod_area'];

$sqlUO="SELECT codigo,nombre,abreviatura from areas where cod_estado=1";
$stmtArea = $db->prepare($sqlUO);
$stmtArea->execute();
?>
<select name="cod_area" id="cod_area" class="selectpicker form-control form-control-sm" data-style="btn btn-primary" data-show-subtext="true" data-live-search="true">
    <?php 
    	while ($row = $stmtArea->fetch()){ 
	?>
      	 <option <?=($cod_area==$row["codigo"])?"selected":"";?> data-subtext="<?=$row["codigo"];?>" value="<?=$row["codigo"];?>"><?=$row["nombre"];?>(<?=$row["abreviatura"];?>)</option>
     <?php 
 		} 
 	?>
 </select>



