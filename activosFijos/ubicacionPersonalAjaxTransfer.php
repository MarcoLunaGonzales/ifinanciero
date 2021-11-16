<?php
require_once '../conexion.php';
require_once 'configModule.php';

$codigo_UO=$_GET["codigo_UO"];
$db = new Conexion();


$stmt = $db->prepare("SELECT p.codigo,(CONCAT_WS(' ',p.paterno,p.materno,p.primer_nombre))as nombre from personal p 
where p.cod_estadopersonal=1 order by nombre");
$stmt->bindParam(':codigo_UO', $codigo_UO);
$stmt->execute();

?>

<select id="cod_responsables_responsable" name="cod_responsables_responsable" class="selectpicker form-control form-control-sm" 
data-style="btn btn-primary" data-size="5" data-show-subtext="true" data-live-search="true" required="true">
    <?php 
        while ($row = $stmt->fetch()){ 
       ?>
       <option value="<?=$row["codigo"];?>"><?=$row["nombre"];?></option>
        <?php 
        } ?>
</select>