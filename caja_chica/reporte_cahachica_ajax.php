<?php
require_once '../conexion.php';
require_once 'configModule.php';
require_once '../functions.php';
$dbh = new Conexion();
$codigo=$_GET['codigo'];
?>
<select class="selectpicker form-control form-control-sm" name="caja_chica" id="caja_chica" data-style="btn btn-info" data-live-search="true" required="true">  
  <?php 
  $query="select codigo,observaciones from caja_chica where cod_tipocajachica=$codigo and cod_estadoreferencial=1";
  $stmt = $dbh->prepare($query);
  $stmt->execute();
  while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $codigoCaja=$row['codigo'];    
    ?><option value="<?=$codigoCaja?>" class="text-right"><?=$row['observaciones']?></option>
   <?php 
   } ?> 
</select>



