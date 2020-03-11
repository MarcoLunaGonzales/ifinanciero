<?php
require_once '../conexion.php';
require_once 'configModule.php';

//header('Content-Type: application/json');

$codigo_personal = $_GET["codigo_personal"];
//ini_set("display_errors", "1");
$db = new Conexion();

$sql="SELECT cod_unidadorganizacional,cod_area from personal where codigo=$codigo_personal";
$stmtPersonal = $db->prepare($sql);
$stmtPersonal->execute();
$result=$stmtPersonal->fetch();
$cod_uo=$result['cod_unidadorganizacional'];
$cod_area=$result['cod_area'];

$sqlUO="
SELECT cod_area,(select a.nombre from areas a where a.codigo=cod_area )as nombre_areas from areas_organizacion where cod_estadoreferencial=1 and cod_unidad=$cod_uo";
$stmt = $db->prepare($sqlUO);
$stmt->execute();
?>
<select name="cod_area" id="cod_area" class="selectpicker form-control form-control-sm" data-style="btn btn-primary" data-show-subtext="true" data-live-search="true">
    <?php 
    	while ($row = $stmt->fetch()){ 
	?>
      	 <option <?=($cod_area==$row["cod_area"])?"selected":"";?> data-subtext="<?=$row["cod_area"];?>" value="<?=$row["cod_area"];?>"><?=$row["nombre_areas"];?></option>
     <?php 
 		} 
 	?>
 </select>

