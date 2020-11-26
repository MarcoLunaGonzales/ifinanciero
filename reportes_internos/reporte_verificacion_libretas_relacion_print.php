<?php
session_start();
set_time_limit(0);
require_once '../layouts/bodylogin2.php';
require_once '../conexion.php';
require_once '../styles.php';
require_once '../functionsGeneral.php';
require_once '../functions.php';
require_once '../functionsReportes.php';


$dbh = new Conexion();

if($_POST["fecha_desde"]==""){
  $y=$globalNombreGestion;
  $desde=$y."-01-01";
  $hasta=$y."-12-31";
  $desdeInicioAnio=$y."-01-01";
}else{
  $porcionesFechaDesde = explode("-", $_POST["fecha_desde"]);
  $porcionesFechaHasta = explode("-", $_POST["fecha_hasta"]);

  $desdeInicioAnio=$porcionesFechaDesde[0]."-01-01";
  $desde=$porcionesFechaDesde[0]."-".$porcionesFechaDesde[1]."-".$porcionesFechaDesde[2];
  $hasta=$porcionesFechaHasta[0]."-".$porcionesFechaHasta[1]."-".$porcionesFechaHasta[2];
}

// $nombreCuentaTitle="";

 $periodoTitle=" Del ".strftime('%d/%m/%Y',strtotime($desde))." al ".strftime('%d/%m/%Y',strtotime($hasta));

$codigoLibreta=$_POST["libretas"];
$nombreLibretasBancaria=nameLibretas($codigoLibreta);
$cuentaPasiva=contraCuentaLibreta($codigoLibreta);
$cuentaLibreta=cuentaLibreta($codigoLibreta);
$dbh = new Conexion();
$sql="SELECT f.fecha_factura,f.cod_comprobante as cod_comprobante_factura,ld.* 
from libretas_bancariasdetalle_facturas lf 
join facturas_venta f on f.codigo=lf.cod_facturaventa 
join libretas_bancariasdetalle ld on ld.codigo=lf.cod_libretabancariadetalle
where ld.cod_libretabancaria=$codigoLibreta 
and ld.fecha_hora BETWEEN '$desde 00:00:00' and '$hasta 23:59:59' 
and (concat(year(f.fecha_factura),'-',month(f.fecha_factura),'-','01')!=concat(year(ld.fecha_hora),'-',month(ld.fecha_hora),'-','01'))
and f.cod_estadofactura<>2;";
// echo $sql;
$stmt = $dbh->prepare($sql);
$stmt->execute();

$sql2="SELECT f.fecha_factura,f.cod_comprobante as cod_comprobante_factura,ld.* 
from libretas_bancariasdetalle_facturas lf 
join facturas_venta f on f.codigo=lf.cod_facturaventa 
join libretas_bancariasdetalle ld on ld.codigo=lf.cod_libretabancariadetalle
where ld.cod_libretabancaria=$codigoLibreta 
and ld.fecha_hora BETWEEN '$desde 00:00:00' and '$hasta 23:59:59' 
and (concat(year(f.fecha_factura),'-',month(f.fecha_factura),'-','01')==concat(year(ld.fecha_hora),'-',month(ld.fecha_hora),'-','01'))
and f.cod_estadofactura<>2;";
// echo $sql;
$stmt2 = $dbh->prepare($sql2);
$stmt2->execute();
?>
<div class="content">
  <div class="container-fluid">
    <div class="row">
      <div class="col-md-12">
        <div class="card">
          <div class="card-header <?=$colorCard;?> card-header-icon">
            <div class="card-icon bg-blanco">
              <img class="" width="40" height="40" src="../assets/img/logoibnorca.png">
            </div>
             <h4 class="card-title text-center"><?=$nombreLibretasBancaria?></h4>
          </div>
          <div class="card-body">
            <h6 class="card-title">Periodo: <?=$periodoTitle?></h6>            
            <h6 class="card-title">Cuenta Libreta: <?=nameCuenta($cuentaLibreta)?></h6>            
            <h6 class="card-title">Cuenta Pasiva: <?=nameCuenta($cuentaPasiva)?></h6>            
            <div class="table-responsive">
            <table id="libreta_bancaria_reporte" class="table table-condensed small" style="width:100% !important;">
      <thead>
        <tr style="background:#21618C; color:#fff;">
          <td>Cuenta</td>
          <td>Fecha</td>
          <td>Hora</td>
          <td width="35%">Descripción</td>
          <td>Sucursal</td>
          <td>Monto</td>
          <td>Saldo</td>
          <td width="10%">Nro Doc / Nro Ref</td>
          <td class="bg-success">Fecha</td>
          <td class="bg-success">Numero</td>
          <td class="bg-success">NIT</td>
          <td class="bg-success">Razon Social</td>
          <td width="10%" class="bg-success">Detalle</td>
          <td class="bg-success">Monto</td>    
        </tr>
      </thead>
      <body>
        <?php
              $totalMontoFac=0;
        while ($rowComp = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $codigo=$rowComp['codigo']; 
            $fecha=$rowComp['fecha_hora'];
            $descripcion=$rowComp['descripcion']; 
            $informacion_complementaria=$rowComp['informacion_complementaria'];
            $agencia=$rowComp['agencia'];
            $codComprobante=$rowComp['cod_comprobante'];
            $codComprobanteFactura=$rowComp['cod_comprobante_factura'];
            $codComprobanteDetalle=$rowComp['cod_comprobantedetalle'];
            $nro_documento=$rowComp['nro_documento']; 
            $monto=$rowComp['monto'];
            $saldo=obtenerSaldoLibretaBancariaDetalleFiltro($codigo,"",$monto);

            $sqlFacturas="SELECT lf.cod_facturaventa,(SELECT sum(f.importe) from facturas_venta f where f.codigo=lf.cod_facturaventa and f.cod_estadofactura!=2)as monto_fac From libretas_bancariasdetalle_facturas lf join facturas_venta f on f.codigo=lf.cod_facturaventa where lf.cod_libretabancariadetalle=$codigo and f.cod_estadofactura<>2 limit 1";
          $stmtFacturas = $dbh->prepare($sqlFacturas);
          // echo $sqlFacturas;
          $stmtFacturas->execute();        
          $resultFacturas=$stmtFacturas->fetch();
          $codFactura=$resultFacturas['cod_facturaventa'];
          $montoFac=$resultFacturas['monto_fac'];
          $entroLib=0;
          $entroLib=obtenerCantidadCuentaCodigoComprobante($codComprobanteFactura,$cuentaLibreta);
          if($entroLib>0){
            ?>
            <tr class="">
              <td class="text-center font-weight-bold"><?=nameCuenta($entroLib)?></td>
              <td class="text-center font-weight-bold"><?=strftime('%d/%m/%Y',strtotime($fecha))?></td>
              <td class="text-center"><?=strftime('%H:%M:%S',strtotime($fecha))?></td>
              <td class="text-left">
                <?=$descripcion?> info: <?=$informacion_complementaria?>
              </td>      
              <td class="text-left"><?=$agencia?></td>
              <td class="text-right"><?=number_format($monto,2,".",",")?></td>
              <td class="text-right"><?=number_format($saldo,2,".",",")?></td>
              <td class="text-right"><?=$nro_documento?></td>
              <?php
              
                $cadena_facturas=obtnerCadenaFacturas($codigo);
                $sqlDetalleX="SELECT * FROM facturas_venta f where f.codigo in ($cadena_facturas) and f.cod_estadofactura!=2 order by f.codigo desc";                                   
                $stmtDetalleX = $dbh->prepare($sqlDetalleX);
                $stmtDetalleX->execute();
                $stmtDetalleX->bindColumn('fecha_factura', $fechaDetalle);
                $stmtDetalleX->bindColumn('nro_factura', $nroDetalle);
                $stmtDetalleX->bindColumn('nit', $nitDetalle);
                $stmtDetalleX->bindColumn('razon_social', $rsDetalle);
                $stmtDetalleX->bindColumn('observaciones', $obsDetalle);
                $stmtDetalleX->bindColumn('importe', $impDetalle);
                $facturaFecha=[];
                $facturaNumero=[];
                $facturaNit=[];
                $facturaRazonSocial=[];
                $facturaDetalle=[];
                $facturaMonto=[];
                $filaFac=0;  
                while ($rowDetalleX = $stmtDetalleX->fetch(PDO::FETCH_BOUND)) {
                  $totalMontoFac+=$impDetalle;
                  $facturaFecha[$filaFac]=strftime('%d/%m/%Y',strtotime($fechaDetalle));
                  $facturaNumero[$filaFac]=$nroDetalle;
                  $facturaNit[$filaFac]=$nitDetalle;
                  $facturaRazonSocial[$filaFac]=$rsDetalle;
                  $facturaDetalle[$filaFac]=$obsDetalle;
                  $facturaMonto[$filaFac]=number_format($impDetalle,2,".",",");
                  $filaFac++;
                }?>
                <td class="text-right font-weight-bold" style="vertical-align: top;"><?=implode("<div style='border-bottom:1px solid #26BD3D;'></div>", $facturaFecha)?></td>
                <td class="text-right font-weight-bold" style="vertical-align: top;"><?=implode("<div style='border-bottom:1px solid #26BD3D;'></div>", $facturaNumero)?></td>
                <td class="text-right font-weight-bold" style="vertical-align: top;"><?=implode("<div style='border-bottom:1px solid #26BD3D;'></div>", $facturaNit)?></td>
                <td class="text-right font-weight-bold" style="vertical-align: top;"><?=implode("<div style='border-bottom:1px solid #26BD3D;'></div>", $facturaRazonSocial)?></td>
                <td class="text-right font-weight-bold" style="vertical-align: top;"><?=implode("<div style='border-bottom:1px solid #26BD3D;'></div>", $facturaDetalle)?></td>
                <td class="text-right font-weight-bold" style="vertical-align: top;"><?=implode("<div style='border-bottom:1px solid #26BD3D;'></div>", $facturaMonto)?></td> 
                <?php
              ?></tr>
              <?php
                 }         
                }                          
            ?>
             <?php
              $totalMontoFac=0;
        while ($rowComp = $stmt2->fetch(PDO::FETCH_ASSOC)) {
            $codigo=$rowComp['codigo']; 
            $fecha=$rowComp['fecha_hora'];
            $descripcion=$rowComp['descripcion']; 
            $informacion_complementaria=$rowComp['informacion_complementaria'];
            $agencia=$rowComp['agencia'];
            $codComprobante=$rowComp['cod_comprobante'];
            $codComprobanteFactura=$rowComp['cod_comprobante_factura'];
            $codComprobanteDetalle=$rowComp['cod_comprobantedetalle'];
            $nro_documento=$rowComp['nro_documento']; 
            $monto=$rowComp['monto'];
            $saldo=obtenerSaldoLibretaBancariaDetalleFiltro($codigo,"",$monto);

            $sqlFacturas="SELECT lf.cod_facturaventa,(SELECT sum(f.importe) from facturas_venta f where f.codigo=lf.cod_facturaventa and f.cod_estadofactura!=2)as monto_fac From libretas_bancariasdetalle_facturas lf join facturas_venta f on f.codigo=lf.cod_facturaventa where lf.cod_libretabancariadetalle=$codigo and f.cod_estadofactura<>2 limit 1";
          $stmtFacturas = $dbh->prepare($sqlFacturas);
          // echo $sqlFacturas;
          $stmtFacturas->execute();        
          $resultFacturas=$stmtFacturas->fetch();
          $codFactura=$resultFacturas['cod_facturaventa'];
          $montoFac=$resultFacturas['monto_fac'];
          $entroLib=0;
          $entroLib=obtenerCantidadCuentaCodigoComprobante($codComprobanteFactura,$cuentaPasiva);
          if($entroLib>0){
            ?>
            <tr class="" style="background:#82E0AA;">
              <td class="text-center font-weight-bold"><?=nameCuenta($entroLib)?></td>
              <td class="text-center font-weight-bold"><?=strftime('%d/%m/%Y',strtotime($fecha))?></td>
              <td class="text-center"><?=strftime('%H:%M:%S',strtotime($fecha))?></td>
              <td class="text-left">
                <?=$descripcion?> info: <?=$informacion_complementaria?>
              </td>      
              <td class="text-left"><?=$agencia?></td>
              <td class="text-right"><?=number_format($monto,2,".",",")?></td>
              <td class="text-right"><?=number_format($saldo,2,".",",")?></td>
              <td class="text-right"><?=$nro_documento?></td>
              <?php
              
                $cadena_facturas=obtnerCadenaFacturas($codigo);
                $sqlDetalleX="SELECT * FROM facturas_venta f where f.codigo in ($cadena_facturas) and f.cod_estadofactura!=2 order by f.codigo desc";                                   
                $stmtDetalleX = $dbh->prepare($sqlDetalleX);
                $stmtDetalleX->execute();
                $stmtDetalleX->bindColumn('fecha_factura', $fechaDetalle);
                $stmtDetalleX->bindColumn('nro_factura', $nroDetalle);
                $stmtDetalleX->bindColumn('nit', $nitDetalle);
                $stmtDetalleX->bindColumn('razon_social', $rsDetalle);
                $stmtDetalleX->bindColumn('observaciones', $obsDetalle);
                $stmtDetalleX->bindColumn('importe', $impDetalle);
                $facturaFecha=[];
                $facturaNumero=[];
                $facturaNit=[];
                $facturaRazonSocial=[];
                $facturaDetalle=[];
                $facturaMonto=[];
                $filaFac=0;  
                while ($rowDetalleX = $stmtDetalleX->fetch(PDO::FETCH_BOUND)) {
                  $totalMontoFac+=$impDetalle;
                  $facturaFecha[$filaFac]=strftime('%d/%m/%Y',strtotime($fechaDetalle));
                  $facturaNumero[$filaFac]=$nroDetalle;
                  $facturaNit[$filaFac]=$nitDetalle;
                  $facturaRazonSocial[$filaFac]=$rsDetalle;
                  $facturaDetalle[$filaFac]=$obsDetalle;
                  $facturaMonto[$filaFac]=number_format($impDetalle,2,".",",");
                  $filaFac++;
                }?>
                <td class="text-right font-weight-bold" style="vertical-align: top;"><?=implode("<div style='border-bottom:1px solid #26BD3D;'></div>", $facturaFecha)?></td>
                <td class="text-right font-weight-bold" style="vertical-align: top;"><?=implode("<div style='border-bottom:1px solid #26BD3D;'></div>", $facturaNumero)?></td>
                <td class="text-right font-weight-bold" style="vertical-align: top;"><?=implode("<div style='border-bottom:1px solid #26BD3D;'></div>", $facturaNit)?></td>
                <td class="text-right font-weight-bold" style="vertical-align: top;"><?=implode("<div style='border-bottom:1px solid #26BD3D;'></div>", $facturaRazonSocial)?></td>
                <td class="text-right font-weight-bold" style="vertical-align: top;"><?=implode("<div style='border-bottom:1px solid #26BD3D;'></div>", $facturaDetalle)?></td>
                <td class="text-right font-weight-bold" style="vertical-align: top;"><?=implode("<div style='border-bottom:1px solid #26BD3D;'></div>", $facturaMonto)?></td> 
                <?php
              ?></tr>
              <?php
                 }         
                }                          
            ?>
            </div>
          </div>
        </div>
      </div>
    </div>  
  </div>
</div>
