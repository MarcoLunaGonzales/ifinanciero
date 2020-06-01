<?php
require_once '../conexion.php';
// require_once 'configModule.php';

//header('Content-Type: application/json');

$cod_gestion = $_GET["cod_gestion"];
//ini_set("display_errors", "1");
$db = new Conexion();

$sql="SELECT c.cod_mes,(select m.nombre from meses m where m.codigo=c.cod_mes) as nombre_mes from meses_trabajo c where c.cod_gestion=$cod_gestion";
$stmtg = $db->prepare($sql);
$stmtg->execute();
?>
<select name="cod_mes_x" id="cod_mes_x" class="selectpicker form-control form-control-sm" data-style="btn btn-primary"  required data-live-search="true">
<?php
  
  while ($rowg = $stmtg->fetch(PDO::FETCH_ASSOC)) {    
    $cod_mes=$rowg['cod_mes'];    
    $nombre_mes=$rowg['nombre_mes'];    
  ?>
  <option value="<?=$cod_mes;?>"><?=$nombre_mes;?></option>
  <?php 
  }
?>
</select>
