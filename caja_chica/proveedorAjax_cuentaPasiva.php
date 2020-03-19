<?php
require_once '../conexion.php';
require_once 'configModule.php';

//header('Content-Type: application/json');

$codigo_proveedor = $_GET["codigo_proveedor"];
//ini_set("display_errors", "1");
$dbh = new Conexion();

// $sqlProve="SELECT codigo,nombre from af_proveedores where codigo=$codigo_proveedor";
// $stmtProveedor = $db->prepare($sqlProve);
// $stmtProveedor->execute();
// $resultProveedor=$stmtProveedor->fetch();
// $codigo=$resultProveedor['codigo'];
// $nombre=$resultProveedor['nombre'];

?>

<!--  <input class="form-control" type="text" value="<?=$nombre?>" readonly="true" />
 <input  name="proveedores" id="proveedores" type="hidden"  required="true" value="<?=$codigo?>" />-->

 <select class="selectpicker form-control form-control-sm" name="proveedores" id="proveedores" data-style="btn btn-info" data-show-subtext="true" data-live-search="true" title="Seleccione Proveedor">
  <option value=""></option>
  <?php 
  $query="SELECT * FROM af_proveedores order by nombre";
  $stmt = $dbh->prepare($query);
  $stmt->execute();
  while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $codigoProv=$row['codigo'];    
    ?><option <?=($codigo_proveedor==$codigoProv)?"selected":"";?> value="<?=$codigoProv?>" class="text-right"><?=$row['nombre']?></option>
   <?php 
   } ?> 
</select>