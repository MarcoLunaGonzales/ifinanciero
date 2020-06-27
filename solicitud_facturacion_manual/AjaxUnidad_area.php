<?php
require_once '../conexion.php';
require_once 'configModule.php';

//header('Content-Type: application/json');

$codigo_UO = $_GET["codigo_UO"];
$aux = $_GET["aux"];
if($aux==1){
	$cod_area_no=12;
}elseif($aux==2){
	$cod_area_no=13;
}

//ini_set("display_errors", "1");
$db = new Conexion();

$sqlUO="SELECT uo.cod_unidad,uo.cod_area,a.nombre as nombre_area,a.abreviatura as abrev_area
FROM areas_organizacion uo,areas a
where uo.cod_estadoreferencial=1 and uo.cod_area=a.codigo and a.areas_ingreso=1 and uo.cod_unidad=$codigo_UO order by nombre_area";
$stmt = $db->prepare($sqlUO);
$stmt->execute();
?>
<select name="cod_area" id="cod_area" class="selectpicker form-control form-control-sm" data-style="btn btn-primary"  data-show-subtext="true" data-live-search="true" required="true">	
    <?php 
    	while ($row = $stmt->fetch()){ 
	?>
      	 <option <?=($cod_area_no==$row["cod_area"])?"selected":"";?> value="<?=$row["cod_area"];?>" data-subtext="(<?=$row['cod_area']?>)"><?=$row["abrev_area"];?> - <?=$row["nombre_area"];?></option>
     <?php 
 		} 
 	?>
 </select>
