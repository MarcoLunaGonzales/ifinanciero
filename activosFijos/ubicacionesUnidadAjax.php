<?php
require_once '../conexion.php';
require_once 'configModule.php';

//header('Content-Type: application/json');

$codigo_UO = $_GET["codigo_UO"];
//ini_set("display_errors", "1");
$db = new Conexion();

$sqlUO="SELECT uo.codigo, uo.abreviatura, uo.nombre from ubicaciones u, unidades_organizacionales uo 
where u.cod_unidades_organizacionales=uo.codigo and uo.cod_estado=1 and u.codigo=:cod_UO";

//echo $sqlUO;

$stmt = $db->prepare($sqlUO);
$stmt->bindParam(':cod_UO', $codigo_UO);
$stmt->execute();

?>

<select name="cod_unidadorganizacional" id="cod_unidadorganizacional" class="selectpicker" data-style="btn btn-primary" onChange="ajaxPersonalUbicacion();">
    <?php 
    	while ($row = $stmt->fetch()){ 
	?>
      	 <option value="<?=$row["codigo"];?>"><?=$row["nombre"];?></option>
     <?php 
 		} 
 	?>
 </select>
