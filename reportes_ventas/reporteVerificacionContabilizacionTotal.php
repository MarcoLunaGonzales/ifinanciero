<?php
session_start();
set_time_limit(0);
require_once '../layouts/bodylogin2.php';
require_once '../conexion.php';
require_once '../styles.php';
require_once '../functionsGeneral.php';
require_once '../functions.php';
require_once '../functionsReportes.php';
require_once '../assets/libraries/CifrasEnLetras.php';

$dbh = new Conexion();

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
                   <h4 class="card-title text-center">Reporte de Verificacion de Contabilizacion por Totales</h4>
                </div>


<div class="card-body">
  <div class="table-responsive">
    <?php
    $html='<table class="table table-bordered table-condensed" id="libro_mayor_rep">'.
            '<thead >'.
            '<tr class="text-center" style="background:#40A3A8;color:#ffffff;">'.
              '<th width="5%">Oficina</th>'.
              '<th width="5%">Area</th>'.
              '<th width="10%">Fecha Factura</th>'.
              '<th width="10%"># Factura</th>'.
              '<th width="15%">NIT</th>'.
              '<th width="10%">Razon Social</th>'.
              '<th width="10%">Importe Neto</th>'.
              '<th width="10%">DebeComprobante</th>'.
              '<th width="10%">HaberComprobante</th>'.
            '</tr>'.
           '</thead>'.
           '<tbody>';

    $valorIVA=100-(obtenerValorConfiguracion(1));
    
    $sql="SELECT f.codigo, f.cod_solicitudfacturacion, 
    (SELECT uo.abreviatura from unidades_organizacionales uo where uo.codigo=f.cod_unidadorganizacional)uo, 
    (SELECT a.abreviatura from areas a where a.codigo=f.cod_area) area, 
    f.fecha_factura, f.razon_social, f.nit, f.cod_personal, 
    (SELECT SUM((cantidad*precio)-descuento_bob) as importe from facturas_ventadetalle where cod_facturaventa=f.codigo )as importe_real, f.nro_factura, f.cod_comprobante
    FROM facturas_venta f
    WHERE f.fecha_factura BETWEEN '2020-07-01 00:00:00' and '2020-12-31 23:59:59' and f.cod_estadofactura<>2 order by fecha_factura, nro_factura";
    //echo $sql;
    $stmt = $dbh->prepare($sql);
    $stmt->execute();
    $totalImporte=0;
    $totalDebe=0;
    $totalHaber=0;
    while ($rowComp = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $codigoX=$rowComp['codigo'];
        
        $unidadX=$rowComp['uo'];
        $areaX=$rowComp['area'];
        $razon_socialX=$rowComp['razon_social'];
        $razon_socialX=mb_strtoupper($razon_socialX);
        $codSolicitudFacturacion=$rowComp['cod_solicitudfacturacion'];
        
        $origenFacturaX="";
        if($codSolicitudFacturacion==-100){
          $origenFacturaX.='<label class="text-primary">Tienda</label>';
        }

        $nitX=$rowComp['nit'];
        $importe_realX=$rowComp['importe_real'];
        $fecha_fac=$rowComp['fecha_factura'];
        $nroFactura=$rowComp['nro_factura'];

        $codArea=$rowComp['cod_area'];
        $cuentaArea=obtenerCodCuentaArea($codArea);
        $codComprobanteX=$rowComp['cod_comprobante'];

        $sqlComp="SELECT sum(cd.debe)as debe, sum(cd.haber)as haber from comprobantes c, comprobantes_detalle cd where c.codigo=cd.cod_comprobante and c.codigo='$codComprobanteX'";
        $stmtComp = $dbh->prepare($sqlComp);
        $stmtComp->execute();
        $montoComprobanteX=0;
        while ($rowComprobante = $stmtComp->fetch(PDO::FETCH_ASSOC)) {
          $debeX=$rowComprobante['debe'];
          $haberX=$rowComprobante['haber'];
        }

        //APLICAMOS EL IVA
        $importe_realX=$importe_realX+($importe_realX*(0.03));

        $totalImporte+=$importe_realX;
        $txtDebe="";
        $txtHaber="";

        if(abs($importe_realX-$debeX)>0.01){
          $txtDebe="style='border: red 5px solid;'";
        }
        if(abs($importe_realX-$haberX)>0.01){
          $txtHaber="style='border: red 5px solid;'";
        }
        $html.='<tr>'.
                      '<td class="text-left font-weight-bold">'.$unidadX.' </td>'.
                      '<td class="text-left font-weight-bold">'.$areaX.'</td>'.
                      '<td class="text-right">'.strftime('%d/%m/%Y',strtotime($fecha_fac)).' </td>'.
                      '<td class="text-right">'.$nroFactura.' </td>'.
                      '<td class="text-right">'.$nitX.' </td>'.
                      '<td class="text-left">'.$razon_socialX.'</td>'.
                      '<td class="text-right font-weight-bold">'.formatNumberDec($importe_realX).' </td>'.     
                      '<td class="text-right font-weight-bold" '.$txtDebe.'>'.formatNumberDec($debeX).' </td>'.     
                      '<td class="text-right font-weight-bold" '.$txtHaber.'>'.formatNumberDec($haberX).' </td>'.     
                  '</tr>';
    }
        $html.='<tr class="bg-secondary text-white">'.
                    '<td colspan="6" class="text-center">Importe Total</td>'.
                    '<td class="text-right font-weight-bold small">'.formatNumberDec($totalImporte).'</td>'.      
                '</tr>';

    $html.=    '</tbody></table>';

    echo $html;
    ?>
  </div>
</div>
              


              </div>
            </div>
          </div>  
        </div>
    </div>
