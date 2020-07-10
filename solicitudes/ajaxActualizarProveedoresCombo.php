<?php
require_once '../conexion.php';
require_once 'configModule.php';
require_once '../functions.php';
$dbh = new Conexion();

?>
 <option disabled selected value="">Proveedores</option>
  <?php 
  $query="SELECT * FROM af_proveedores order by nombre";
  $stmt = $dbh->prepare($query);
  $stmt->execute();
  while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $codigoProv=$row['codigo'];    
    ?><option value="<?=$codigoProv?>" class="text-right"><?=$row['nombre']?></option>
   <?php 
   } ?> 

