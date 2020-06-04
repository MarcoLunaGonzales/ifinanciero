<?php
require_once '../conexion.php';
require_once '../functions.php';
require_once 'configModule.php';

$codigo_UO=$_GET["codigo_UO"];
$db = new Conexion();

$stmt0 = $db->prepare("SELECT valor_configuracion from configuraciones where id_configuracion=15");
$stmt0->execute();
$result0=$stmt0->fetch();
$codigo_dn=$result0['valor_configuracion'];
$stmt1 = $db->prepare("SELECT valor_configuracion from configuraciones where id_configuracion=16 ");
$stmt1->execute();
$result1=$stmt1->fetch();
$codigo_sis=$result1['valor_configuracion'];


//personal SN controla SIS
if($codigo_UO==$codigo_sis) $codigo_UO=$codigo_dn;
// $sql="SELECT p.codigo, p.paterno,p.materno,p.primer_nombre
// from personal p, unidades_organizacionales uo 
// where uo.codigo=p.cod_unidadorganizacional and p.cod_estadoreferencial=1 and uo.codigo=$codigo_UO order by 2";
$sql="SELECT p.codigo, p.paterno,p.materno,p.primer_nombre,p.cod_unidadorganizacional
from personal p, unidades_organizacionales uo 
where uo.codigo=p.cod_unidadorganizacional  and p.cod_estadoreferencial=1 order by 2";
$stmt = $db->prepare($sql);
$stmt->execute();
?>

<select id="cod_personal" name="cod_personal" class="selectpicker form-control form-control-sm" 
data-style="btn btn-primary" data-size="5" data-show-subtext="true" data-live-search="true">
    <?php 
        while ($row = $stmt->fetch()){ 
        	$cod_uo=$row["cod_unidadorganizacional"];
        	$nombre_uo=nameUnidad($cod_uo);
       ?>
       <option value="<?=$row["codigo"];?>"><?=$row["paterno"].' '.$row["materno"].' '.$row["primer_nombre"];?> ( <?=$nombre_uo?> ) </option>
        <?php 
        } ?>
</select>