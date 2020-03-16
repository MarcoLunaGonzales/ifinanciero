<?php
require_once '../conexion.php';
require_once 'configModule.php';

//header('Content-Type: application/json');

$codigo_comprobante = $_GET["codigo_comprobante"];
//ini_set("display_errors", "1");
$db = new Conexion();

$sql="SELECT cod_unidadorganizacional,cod_area from comprobantes_detalle where codigo=$codigo_comprobante";
$stmtComprobant = $db->prepare($sql);
$stmtComprobant->execute();
$resultCompro=$stmtComprobant->fetch();
$cod_uo=$resultCompro['cod_unidadorganizacional'];
$cod_area=$resultCompro['cod_area'];

$sqlUO="SELECT codigo,nombre,abreviatura from unidades_organizacionales where cod_estado=1";
$stmtUO = $db->prepare($sqlUO);
$stmtUO->execute();
?>
<select name="cod_uo" id="cod_uo" class="selectpicker form-control form-control-sm" data-style="btn btn-primary" data-show-subtext="true" data-live-search="true" onChange="ajaxAreaUOCAJACHICA(this);">
    <?php 
    	while ($row = $stmtUO->fetch()){ 
	?>
      	 <option <?=($cod_uo==$row["codigo"])?"selected":"";?> data-subtext="<?=$row["codigo"];?>" value="<?=$row["codigo"];?>"><?=$row["nombre"];?>(<?=$row["abreviatura"];?>)</option>
     <?php 
 		} 
 	?>
 </select>



