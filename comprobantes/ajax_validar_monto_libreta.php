<?php
require_once '../conexion.php';
require_once '../functions.php';
require_once '../styles.php';

$dbh = new Conexion();
$codigoDetalle=$_GET['codigo'];
$stmt = $dbh->prepare("SELECT obtener_saldo_libreta_bancaria_detalle_oficial($codigoDetalle) as saldo");
$stmt->execute();
$saldo=0;
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $saldo=$row['saldo']; 
}
$existeFactura=0;
$sqlFacturaLibreta="select f.fecha_factura,f.nro_factura,f.nit,f.razon_social,f.observaciones,f.importe from facturas_venta f join 
libretas_bancariasdetalle_facturas lf on lf.cod_facturaventa=f.codigo 
where lf.cod_libretabancariadetalle=".$codigoDetalle." and f.cod_estadofactura<>2";
$stmtFacLibreta = $dbh->prepare($sqlFacturaLibreta);
$stmtFacLibreta->execute();
 while ($rowFacLib = $stmtFacLibreta->fetch(PDO::FETCH_ASSOC)) {
     $existeFactura++;           
  }
$saldoFactura=obtenerMontoLibretasBancariasDetalle($codigoDetalle);
if($existeFactura>0){
    //calcular Saldo
    $saldoFactura=number_format($saldo,2,".","");
}

echo $saldoFactura;
?>
        