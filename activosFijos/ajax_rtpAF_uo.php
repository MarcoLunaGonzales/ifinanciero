<?php
require_once '../conexion.php';
require_once 'configModule.php';

//header('Content-Type: application/json');

$codigo = $_GET["codigo"];
//ini_set("display_errors", "1");
$db = new Conexion();

$sqlUO="SELECT cod_unidad,cod_area,(select a.nombre from areas a where a.codigo=cod_area) as nombre_area,(select a.abreviatura from areas a where a.codigo=cod_area) as abrev_area
FROM areas_organizacion
where cod_estadoreferencial=1 and cod_unidad in ($codigo) GROUP BY cod_area order by nombre_area";
$stmt = $db->prepare($sqlUO);
$stmt->execute();
?>
<select class="selectpicker form-control" title="Seleccione una opcion" name="areas[]" id="areas" data-style="select-with-transition" data-size="5" data-actions-box="true" multiple required data-show-subtext="true" data-live-search="true">	
    <?php 
    	while ($row = $stmt->fetch()){ 
	?>
      	 <option value="<?=$row["cod_area"];?>" data-subtext="(<?=$row['cod_area']?>)"><?=$row["abrev_area"];?> - <?=$row["nombre_area"];?></option>
     <?php 
 		} 
 	?>
</select>
