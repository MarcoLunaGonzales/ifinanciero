<?php
require_once '../conexion.php';
require_once 'configModule.php';

$nit = $_GET["nit"];
$db = new Conexion();
$nro_factura=null;
$sqlUO="SELECT nro_factura from facturas_compra where  nit = '$nit' order by codigo desc limit 1";
$stmt = $db->prepare($sqlUO);
$stmt->execute();
while ($row = $stmt->fetch()){
	$nro_factura=$row['nro_factura'];
}

$valorNoValido="Valor no Válido.";
?>

<input class="form-control" type="number" name="nro_fac" id="nro_fac" required="true" value="<?=$nro_factura?>" />
<div class="invalid-feedback"><?=$valorNoValido;?></div>
