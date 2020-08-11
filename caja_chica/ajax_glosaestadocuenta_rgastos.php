<?php
require_once '../conexion.php';
require_once 'configModule.php';

//header('Content-Type: application/json');

$cod_estado_cuenta = $_GET["cod_estado_cuenta"];
// echo $cod_estado_cuenta."a";
$dbh = new Conexion();
$query="SELECT cd.glosa from estados_cuenta e,comprobantes_detalle cd where e.cod_comprobantedetalle=cd.codigo and  e.codigo=$cod_estado_cuenta";
  $stmt = $dbh->prepare($query);
  $stmt->execute();
  while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $glosa_auxiliar=$row['glosa'];    
  } 
?>
<div class="row">     
	<label class="col-sm-2 col-form-label">Glosa Comprobante</label>
	<div class="col-sm-8">
	  <div class="form-group">
	    <textarea class="form-control" readonly><?=$glosa_auxiliar;?></textarea>
	  </div>
	</div>
</div>