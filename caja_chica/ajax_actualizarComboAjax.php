<?php
require_once '../conexion.php';
require_once 'configModule.php';
require_once '../functions.php';
//header('Content-Type: application/json');
//ini_set("display_errors", "1");
$dbh = new Conexion();

?>
<select class="selectpicker form-control form-control-sm" name="proveedores" id="proveedores" data-style="btn btn-info" data-show-subtext="true" data-live-search="true" title="Seleccione Proveedor">
  <option value=""></option>
  <?php 
  $query="SELECT * FROM af_proveedores order by nombre";
  $stmt = $dbh->prepare($query);
  $stmt->execute();
  while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $codigoProv=$row['codigo'];    
    ?><option value="<?=$codigoProv?>" class="text-right"><?=$row['nombre']?></option>
   <?php 
   } ?> 
</select>



