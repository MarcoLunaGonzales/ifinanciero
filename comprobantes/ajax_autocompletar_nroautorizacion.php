<?php
require_once '../conexion.php';
require_once 'configModule.php';

$nit = $_GET["nit"];
$db = new Conexion();
$nro_autorizacion=null;
$sqlUO="SELECT nro_autorizacion from facturas_compra where  nit = '$nit' order by codigo desc limit 1";
$stmt = $db->prepare($sqlUO);
$stmt->execute();
while ($row = $stmt->fetch()){
	$nro_autorizacion=$row['nro_autorizacion'];
}

$valorNoValido="Valor no Válido.";
?>
<input class="form-control" type="text" name="aut_fac" id="aut_fac" required="true" value="<?=$nro_autorizacion?>"/>
<div class="invalid-feedback"><?=$valorNoValido;?></div>