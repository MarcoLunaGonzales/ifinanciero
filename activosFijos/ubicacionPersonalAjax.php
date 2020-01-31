<?php
require_once '../conexion.php';
require_once 'configModule.php';

$codigo_UO=$_GET["codigo_UO"];
$db = new Conexion();

$stmt0 = $db->prepare("SELECT valor_configuracion from configuraciones where id_configuracion=15 ");
$stmt0->execute();
$result0=$stmt0->fetch();
$codigo_dn=$result0['valor_configuracion'];
$stmt1 = $db->prepare("SELECT valor_configuracion from configuraciones where id_configuracion=16 ");
$stmt1->execute();
$result1=$stmt1->fetch();
$codigo_sis=$result1['valor_configuracion'];


//personal SN controla SIS
if($codigo_UO==$codigo_sis) $codigo_UO=$codigo_dn;

$stmt = $db->prepare("SELECT p.codigo, p.paterno,p.materno,p.primer_nombre
from personal p, ubicaciones u, unidades_organizacionales uo 
where u.cod_unidades_organizacionales=uo.codigo and uo.codigo=p.cod_unidadorganizacional and uo.codigo=:codigo_UO order by 2");
$stmt->bindParam(':codigo_UO', $codigo_UO);
$stmt->execute();

?>

<select id="cod_responsables_responsable" name="cod_responsables_responsable" class="form-control" 
data-style="btn btn-info" data-size="5">
    <?php 
        while ($row = $stmt->fetch()){ 
       ?>
       <option value="<?=$row["codigo"];?>"><?=$row["paterno"].' '.$row["materno"].' '.$row["primer_nombre"];?></option>
        <?php 
        } ?>
</select>