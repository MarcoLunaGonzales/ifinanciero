<?php
require_once '../conexion.php';
require_once 'configModule.php';

$codigo_UO=$_GET["codigo_UO"];
$db = new Conexion();


$stmt = $db->prepare("SELECT p.codigo, p.nombre from personal2 p, ubicaciones u, unidades_organizacionales uo 
where u.cod_unidades_organizacionales=uo.codigo and uo.codigo=p.cod_unidad and u.codigo=:codigo_UO order by 2");
$stmt->bindParam(':codigo_UO', $codigo_UO);
$stmt->execute();

?>

<select id="cod_responsables_responsable" name="cod_responsables_responsable" class="form-control" 
data-style="btn btn-info" data-size="5">
    <?php 
        while ($row = $stmt->fetch()){ 
       ?>
       <option value="<?=$row["codigo"];?>"><?=$row["nombre"];?></option>
        <?php 
        } ?>
</select>