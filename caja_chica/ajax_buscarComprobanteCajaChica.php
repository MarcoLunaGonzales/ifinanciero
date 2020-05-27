<?php
require_once '../conexion.php';
require_once 'configModule.php';
require_once '../functions.php';
//header('Content-Type: application/json');
//ini_set("display_errors", "1");
$dbh = new Conexion();

$mes_comprobante=$_GET['mes'];
$nro_comprobante=$_GET['nro'];
$tipo_comprobante=$_GET['tipo'];

$query="SELECT glosa from comprobantes where cod_tipocomprobante=$tipo_comprobante and numero=$nro_comprobante and MONTH(fecha)=$mes_comprobante";
// echo $query;
$stmt = $dbh->prepare($query);
$stmt->execute();
$glosa="no encontrado";
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
  $glosa=$row['glosa'];
}
if($glosa=='no encontrado'){
  // echo 0;
}else{
  // echo 1;
}
// echo "---".$glosa;
?>
<label class="col-sm-4 text-right col-form-label" style="color:#424242">Glosa Comprobante</label>
<div class="col-sm-6">
  <div class="form-group">
    <input type="text"name="detalle_comprobante" id="detalle_comprobante" class="form-control" value="<?=$glosa?>" readonly>
  </div>
</div>