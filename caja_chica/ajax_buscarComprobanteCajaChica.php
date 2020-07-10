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
$unidad=$_GET['unidad'];

$query="SELECT numero,MONTH(fecha) as mes,DATE_FORMAT(fecha,'%d/%m/%Y')as fecha_x,glosa from comprobantes where cod_tipocomprobante=$tipo_comprobante and numero=$nro_comprobante and MONTH(fecha)=$mes_comprobante and cod_unidadorganizacional=$unidad";
// echo $query;
$stmt = $dbh->prepare($query);
$stmt->execute();
$glosa="no encontrado";
$numero='';
$mes='';
$fecha='';
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
  $glosa=$row['glosa'];
  $numero=$row['numero'];
  $mes=$row['mes'];
  $fecha=$row['fecha_x'];
}

if($glosa!="no encontrado"){
	$String_detalle='Nro: '.$numero." - Mes: ".$mes." - Fecha: ".$fecha." - Glosa: ".$glosa;
}else{
	$String_detalle="no encontrado";
}
?>
<label class="col-sm-2 text-right col-form-label" style="color:#424242">Detalles del Comprobante</label>
<div class="col-sm-9">
  <div class="form-group">    
    <textarea name="detalle_comprobante" id="detalle_comprobante" class="form-control" readonly><?=$String_detalle?></textarea><br>
    <?php if($glosa!="no encontrado"){?>    
    	<center><span style="color:#FF0000;background-color:#ffffff;"><small>Se Sobreescribirá la Glosa</small></span></center>
    <?php } ?>
  </div>
</div>