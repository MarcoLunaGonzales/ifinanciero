<?php
require_once '../conexion.php';
require_once 'configModule.php';
require_once '../functions.php';
//header('Content-Type: application/json');
//ini_set("display_errors", "1");
$dbh = new Conexion();
// $cod_cliente=$_GET['cod_cliente'];

?>
<select class="selectpicker form-control form-control-sm" name="persona_contacto" id="persona_contacto" data-style="btn btn-info" data-show-subtext="true" data-live-search="true" title="Seleccione Contacto">
  <option value=""></option>
  <?php 
  $query="SELECT * FROM clientes_contactos  order by nombre";
  $stmt = $dbh->prepare($query);
  $stmt->execute();
  while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $codigo=$row['codigo'];    
    ?><option value="<?=$codigo?>" class="text-right"><?=$row['nombre']?></option>
   <?php 
   } ?> 
</select>


