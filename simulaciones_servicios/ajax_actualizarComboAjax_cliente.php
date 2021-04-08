<?php
require_once '../conexion.php';
require_once 'configModule.php';
require_once '../functions.php';
//header('Content-Type: application/json');
//ini_set("display_errors", "1");
$dbh = new Conexion();
$cod_cliente=$_GET['cod_cliente'];

?>
<select class="selectpicker form-control form-control-sm" name="persona_contacto" id="persona_contacto" data-style="btn btn-info" data-show-subtext="true" data-live-search="true" title="Seleccione Contacto">  
  <?php 
  $query="SELECT * FROM clientes_contactos where cod_cliente=$cod_cliente order by nombre";
  $stmt = $dbh->prepare($query);
  $stmt->execute();
  while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $codigo_contacto=$row['codigo'];    
    $nombre_conatacto=$row['nombre']." ".$row['paterno'];
    ?><option value="<?=$codigo_contacto?>" class="text-right"><?=$nombre_conatacto?></option>
   <?php 
   } ?> 
</select>


