<?php
require_once '../conexion.php';
require_once '../functions.php';
require_once '../assets/controlcode/sin/ControlCode.php';

$dbh = new Conexion();

$controlCode = new ControlCode();


$sql="select f.codigo, f.nit, DATE_FORMAT(f.fecha_factura,'%Y%m%d')as fecha_factura, f.importe, f.nro_autorizacion, f.nro_factura from facturas_venta f where f.fecha_factura BETWEEN '2021-08-30 00:00:00' and '2021-08-30 23:59:59'";
$stmt = $dbh->prepare($sql);
$stmt->execute();
$nombreX=0;
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
  $codigoX=$row['codigo'];
  $nit=$row['nit'];
  $fechaFactura=$row['fecha_factura'];
  $importe=$row['importe'];
  $nroAutorizacion=$row['nro_autorizacion'];
  $nroFactura=$row['nro_factura'];
  $llave="_(GD3=4S*-3DQgDEVF(VpHMBIzCCTZdD3krf[rQpDC+vCUBWgwvgGLm[RY4w)2=A";

  $codigoControl = $controlCode->generate($nroAutorizacion,//Numero de autorizacion
                                           $nroFactura,//Numero de factura
                                           $nit,//Número de Identificación Tributaria o Carnet de Identidad
                                           $fechaFactura,//fecha de transaccion de la forma AAAAMMDD
                                           $importe,
                                           $llave);
  //echo "$codigoX $nit $fechaFactura $importe $nroAutorizacion $llave $codigoControl<br>";
  echo "update facturas_venta set codigo_control='$codigoControl' where codigo='$codigoX';<br>";
}

?>