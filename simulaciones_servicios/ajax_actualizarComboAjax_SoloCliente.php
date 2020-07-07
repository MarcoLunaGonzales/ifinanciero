<?php
require_once '../conexion.php';
require_once 'configModule.php';
require_once '../functions.php';
//header('Content-Type: application/json');
//ini_set("display_errors", "1");
$dbh = new Conexion();
?>
<select class="selectpicker form-control" data-size="4" data-live-search-placeholder="Buscar cliente..." data-live-search="true" name="cliente" id="cliente" data-style="btn btn-info"  required>
          
<!--<option disabled selected="selected" value="">Cliente</option>-->
<?php
  $stmt = $dbh->prepare("SELECT c.codigo, c.nombre FROM clientes c where c.cod_estadoreferencial=1 order by 2");
  $stmt->execute();
   while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
     $codigoX=$row['codigo'];
     $nombreX=$row['nombre'];
    ?>
  <option value="<?=$codigoX;?>"><?=$nombreX;?></option> 
  <?php
     }
    ?>
</select>


