<?php
require_once '../conexion.php';
require_once '../rrhh/configModule.php';

//header('Content-Type: application/json');

$codigo_uo = $_GET["codigo_uo"];
$codigo_area = $_GET["codigo_area"];
//ini_set("display_errors", "1");
$db = new Conexion();

$sqlUO="SELECT ca.cod_cargo,
(select c.nombre from cargos c where c.codigo=ca.cod_cargo) as nombre_cargo
from cargos_areasorganizacion ca,areas_organizacion ao
where ca.cod_estadoreferencial=1 and ca.cod_areaorganizacion=ao.codigo and ao.cod_unidad=:codigo_uo and ao.cod_area=:codigo_area";
$stmtCargos = $db->prepare($sqlUO);
$stmtCargos->bindParam(':codigo_uo', $codigo_uo);
$stmtCargos->bindParam(':codigo_area', $codigo_area);
$stmtCargos->execute();
// $stmtCargos->bindColumn('cod_cargo', $cod_cargo);
// $stmtCargos->bindColumn('nombre_cargo', $nombre_cargo);
// while ($row = $stmtCargos->fetch()) { 
// 	echo $cod_cargo."-".$nombre_cargo;
// }

?>

<select name="cod_cargo" id="cod_cargo" class="selectpicker form-control form-control-sm" data-style="btn btn-primary" required data-show-subtext="true" data-live-search="true">
	<option value=""></option>
    <?php 
    	while ($row = $stmtCargos->fetch()){ 
	?>
      	 <option value="<?=$row["cod_cargo"];?>"><?=$row["nombre_cargo"];?></option>
     <?php 
 		} 
 	?>
 </select>

