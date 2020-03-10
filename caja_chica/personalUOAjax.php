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

$sqlUO="SELECT codigo,nombre from unidades_organizacionales where cod_estado=1";
$stmt = $db->prepare($sqlUO);
$stmt->execute();
?>
<select name="cod_uo" id="cod_uo" class="selectpicker form-control form-control-sm" data-style="btn btn-primary" data-show-subtext="true" data-live-search="true" onChange="ajaxAreaUOCAJACHICA(this);">
    <?php 
    	while ($row = $stmt->fetch()){ 
	?>
      	 <option <?=($cod_uo==$row["codigo"])?"selected":"";?> data-subtext="<?=$row["codigo"];?>" value="<?=$row["codigo"];?>"><?=$row["nombre"];?></option>
     <?php 
 		} 
 	?>
 </select>



