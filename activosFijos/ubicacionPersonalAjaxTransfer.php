<?php
require_once '../conexion.php';
require_once 'configModule.php';

$codigo_UO=$_GET["codigo_UO"];
$db = new Conexion();


$stmt = $db->prepare("SELECT p.codigo,(CONCAT_WS(' ',p.paterno,p.materno,p.primer_nombre))as nombre from personal p, unidades_organizacionales uo 
where uo.codigo=p.cod_unidadorganizacional and uo.codigo=:codigo_UO order by nombre");
$stmt->bindParam(':codigo_UO', $codigo_UO);
$stmt->execute();

?>

<select id="cod_responsables_responsable" name="cod_responsables_responsable" class="selectpicker form-control" 
data-style="btn btn-primary" data-size="5">
    <?php 
        while ($row = $stmt->fetch()){ 
       ?>
       <option value="<?=$row["codigo"];?>"><?=$row["nombre"];?></option>
        <?php 
        } ?>
</select>