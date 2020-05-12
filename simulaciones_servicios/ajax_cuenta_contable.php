<?php
require_once '../conexion.php';
require_once 'configModule.php';
require_once '../functions.php';
//header('Content-Type: application/json');
//ini_set("display_errors", "1");
$dbh = new Conexion();
$cod_cuenta=$_GET['cod_cuenta'];

//plan de cuentas
$query_cuentas = "SELECT codigo,numero,nombre from plan_cuentas where cod_estadoreferencial=1";
$statementCuentas = $dbh->query($query_cuentas);


?>
<select name="cod_cuenta" id="cod_cuenta" class="selectpicker form-control form-control-sm" data-style="btn btn-primary" required data-show-subtext="true" data-live-search="true">
  <option value=""></option>
  <?php while ($row = $statementCuentas->fetch()){ ?>
      <option <?=($cod_cuenta==$row["codigo"])?"selected":"";?> value="<?=$row["codigo"];?>"><?=$row["numero"];?> - <?=$row["nombre"];?></option>
  <?php } ?>
</select>


