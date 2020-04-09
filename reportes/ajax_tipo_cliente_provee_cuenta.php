<?php
require_once '../conexion.php';
// require_once 'configModule.php';

//header('Content-Type: application/json');

$codigo = $_GET["codigo"];
//ini_set("display_errors", "1");
$db = new Conexion();

$sql="SELECT p.codigo,p.nombre,p.numero from configuracion_estadocuentas c,plan_cuentas p where c.cod_plancuenta=p.codigo and c.cod_tipoestadocuenta in ($codigo) order by nombre";
$stmtg = $db->prepare($sql);
$stmtg->execute();
?>
	<select name="cuenta[]" id="cuenta" class="selectpicker form-control"  data-style="select-with-transition" data-size="5"  data-actions-box="true" multiple required data-live-search="true">
<?php
  
  while ($rowg = $stmtg->fetch(PDO::FETCH_ASSOC)) {
    $codigog=$rowg['codigo'];
    $nombreg=$rowg['nombre'];
    $numerog=$rowg['numero'];
  ?>
  <option value="<?=$codigog;?>"><?=$numerog?> - <?=$nombreg;?></option>
  <?php 
  }
?>
</select>
