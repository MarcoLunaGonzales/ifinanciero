<?php
require_once '../conexion.php';
require_once 'configModule.php';

//header('Content-Type: application/json');

$codigo_UO = $_GET["codigo_UO"];
//ini_set("display_errors", "1");
$db = new Conexion();

$sqlUO="SELECT cod_unidad,cod_area,(select a.nombre from areas a where a.codigo=cod_area) as nombre_area,(select a.abreviatura from areas a where a.codigo=cod_area) as abrev_area
FROM areas_organizacion
where cod_estadoreferencial=1 and cod_unidad=:cod_UO order by nombre_area";
$stmt = $db->prepare($sqlUO);
$stmt->bindParam(':cod_UO', $codigo_UO);
$stmt->execute();
$cod_area_no=12;
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
