<?php
require_once '../conexion.php';
require_once 'configModule.php';

$nit = $_GET["nit"];
$db = new Conexion();
$razon_social="-";
$sqRS="SELECT razon_social from facturas_compra where  nit='$nit' order by codigo desc limit 1";
$stmtRS = $db->prepare($sqRS);
$stmtRS->execute();
while ($row = $stmtRS->fetch()){
	$razon_social=$row['razon_social'];
}
?>
<input class="form-control" type="text" name="razon_fac" id="razon_fac" required="true" value="<?=$razon_social?>" />
