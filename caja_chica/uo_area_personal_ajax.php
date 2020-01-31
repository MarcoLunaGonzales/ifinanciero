<?php
require_once '../conexion.php';
require_once 'configModule.php';

//header('Content-Type: application/json');

$codigo_UO = $_GET["codigo_UO"];
//ini_set("display_errors", "1");
$db = new Conexion();

$sqlUO="SELECT cod_unidad,cod_area,(select a.nombre from areas a where a.codigo=cod_area) as nombre_area
FROM areas_organizacion
where cod_estadoreferencial=1 and cod_unidad=:cod_UO order by nombre_area";
$stmt = $db->prepare($sqlUO);
$stmt->bindParam(':cod_UO', $codigo_UO);
$stmt->execute();
?>
<select name="cod_area" id="cod_area" class="selectpicker" data-style="btn btn-primary" onchange="ajaxAreaCargos(<?=$codigo_UO;?>,this);" >
    <?php 
    	while ($row = $stmt->fetch()){ 
	?>
      	 <option value="<?=$row["cod_area"];?>"><?=$row["nombre_area"];?></option>
     <?php 
 		} 
 	?>
 </select>
