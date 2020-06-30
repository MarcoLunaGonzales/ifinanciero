<?php
require_once '../conexion.php';
require_once 'configModule.php';

$nit = $_GET["nit"];
$importe = $_GET["importe"];

$db = new Conexion();
$sql="SELECT nro_factura, razon_social, nro_autorizacion from facturas_compra where  nit = '$nit' order by codigo desc limit 1";
$stmt = $db->prepare($sql);
$stmt->execute();
$nroFacturaX=0;
$razonSocialX="";
$nroAutorizacionX=0;
while ($row = $stmt->fetch()){
	$nroFacturaX=$row['nro_factura'];
	$razonSocialX=$row['razon_social'];
	$nroAutorizacionX=$row['nro_autorizacion'];
}
echo $nroFacturaX."@".$razonSocialX."@".$nroAutorizacionX;
?>
