<?php 
require_once '../conexion.php';
require_once '../styles.php';

$dbh = new Conexion();

$cod_retencion=6;//retencion sin gasto
?>
<select name="tipo_retencion" id="tipo_retencion" class="selectpicker form-control form-control-sm" data-style="btn btn-info" required>
  <option value="" disabled selected="selected">-Retenciones-</option>
    <?php                                     
    $stmtTipoRet = $dbh->query("SELECT * from configuracion_retenciones where cod_estadoreferencial=1 order by 2");
    while ($row = $stmtTipoRet->fetch()){ ?>
        <option <?=($cod_retencion==$row["codigo"])?"selected":"disabled";?> value="<?=$row["codigo"];?>"><?=$row["nombre"];?></option>
    <?php } ?>
</select>  