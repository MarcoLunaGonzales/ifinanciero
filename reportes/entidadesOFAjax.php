<?php
require_once '../conexion.php';
// require_once 'configModule.php';

//header('Content-Type: application/json');

$codigo_entidad = $_GET["codigo_entidad"];
//ini_set("display_errors", "1");
$db = new Conexion();

$sqlUO="SELECT uo.codigo, uo.nombre,uo.abreviatura from entidades_uo e, unidades_organizacionales uo where e.cod_uo=uo.codigo and e.cod_entidad in ($codigo_entidad)";
$stmt = $db->prepare($sqlUO);
$stmt->execute();
?>
	<select class="selectpicker form-control form-control-sm" name="unidad[]" id="unidad" data-style="select-with-transition" multiple data-actions-box="true" required data-live-search="true" data-container="body"">
	    <?php 
	    	while ($row = $stmt->fetch()){ 
		?>
	      	 <option value="<?=$row["codigo"];?>" data-subtext="<?=$row["nombre"];?>" selected><?=$row["abreviatura"];?></option>
	     <?php 
	 		} 
	 	?>
	</select>

