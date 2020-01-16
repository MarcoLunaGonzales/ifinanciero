<?php
require_once '../conexion.php';
require_once '../rrhh/configModule.php';

//header('Content-Type: application/json');

$codigo_UO = $_GET["codigo_UO"];
//ini_set("display_errors", "1");
$db = new Conexion();

$sqlUO="SELECT codigo,paterno,materno,primer_nombre
from personal
where cod_estadoreferencial=1 and cod_unidadorganizacional=:cod_UO ORDER BY paterno
";
$stmt = $db->prepare($sqlUO);
$stmt->bindParam(':cod_UO', $codigo_UO);
$stmt->execute();
?>
<select name="cod_personal" id="cod_personal" class="selectpicker form-control" data-style="btn btn-primary" >
	<option ></option>
    <?php 
    	while ($row = $stmt->fetch()){ 
	?>
      	 <option value="<?=$row["codigo"];?>"><?=$row["paterno"];?> <?=$row["materno"];?> <?=$row["primer_nombre"];?></option>
     <?php 
 		} 
 	?>
 </select>
