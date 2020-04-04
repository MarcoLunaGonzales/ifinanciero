<?php
require_once '../conexion.php';
require_once '../rrhh/configModule.php';

//header('Content-Type: application/json');

$cod_uo = $_GET["cod_uo"];
$dbh = new Conexion();
$query_uoE = "SELECT * from unidades_organizacionales where cod_estado=1 order by 2";
$statementUOE = $dbh->query($query_uoE);

?>
<select name="cod_uoE" id="cod_uoE" data-style="btn btn-primary" onChange="ajaxPersonal_area_distribucionE(this);" class="selectpicker form-control form-control-sm" required data-show-subtext="true" data-live-search="true">
<?php while ($rowUOE = $statementUOE->fetch()){ ?>
  <option <?=($cod_uo==$rowUOE["codigo"])?"selected":"";?> value="<?=$rowUOE["codigo"];?>" data-subtext="<?=$rowUOE["codigo"];?>"><?=$rowUOE["abreviatura"];?> - <?=$rowUOE["nombre"];?></option>
<?php } ?>
</select>