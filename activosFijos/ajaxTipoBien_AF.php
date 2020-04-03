<?php
require_once '../conexion.php';
require_once 'configModule.php';

$codigoRubro=$_GET["codigo"];
$db = new Conexion();

$sqlCodAF="SELECT * from tiposbienes where cod_estado=1 and cod_depreciaciones=$codigoRubro order by 3 ";
$stmtCodAF = $db->prepare($sqlCodAF);
$stmtCodAF->execute();

?>

<select name="cod_tiposbienes" id="cod_tiposbienes" class="selectpicker form-control form-control-sm" data-style="btn btn-primary" required="true">
<?php while ($row = $stmtCodAF->fetch()){ ?>
	<option value="<?=$row["codigo"];?>"><?=$row["tipo_bien"];?></option>
<?php } ?>
</select>