<style>
  #reporte_datos_busqueda_filter{
         display: none !important;
       }      
</style>
<div class="card-body">
  <h6 class="card-title">Periodo: <?=$periodoTitle?></h6>
  <h6 class="card-title">Areas: <?=$areaAbrev;?></h6>
  <h6 class="card-title">Oficinas:<?=$unidadAbrev?></h6>
  <!-- <h6 class="card-title">Formas Pago:<?=$formas_pago_titulo?></h6>   -->
  <div class="table-responsive">
    <?php
    $txtEstiloPersonal="";
    // if($filtroPersonal==0){
    //   $txtEstiloPersonal="display: none;";
    // }

    $html='<table class="table table-bordered table-condensed" id="reporte_datos_busqueda">'.
            '<thead >'.
            '<tr class="text-center" style="background:#40A3A8;color:#ffffff;">'.
              '<th width="5%">Oficina</th>'.
              '<th width="5%">Area</th>'.
              '<th width="5%">Tipo Pago</th>'.
              '<th width="10%">Fecha Factura</th>'.
              '<th width="10%"># Factura</th>'.
              '<th width="15%">NIT</th>'.
              '<th width="10%">Razon Social</th>'.
              '<th width="10%" style="'.$txtEstiloPersonal.'">Personal</th>'.
              '<th width="10%">Origen</th>'.
              '<th width="10%">Importe Bruto</th>'.
              '<th width="10%">Importe Neto</th>'.
            '</tr>'.
           '</thead>'.
           '<tbody>';
           // $forma_pagoArray="";
    $valorIVA=100-(obtenerValorConfiguracion(1));
    
    $listaDetalle=obtenerListaVentasResumidoAdministrativo($unidadCostoArray,$areaCostoArray,$filtroTiposPago,$desde,$hasta,$filtroPersonal);
    $totalImporte=0;$totalImporteBruto=0;
    while ($rowComp = $listaDetalle->fetch(PDO::FETCH_ASSOC)) {
        $codigoX=$rowComp['codigo'];
        
        $unidadX=$rowComp['uo'];
        $areaX=$rowComp['area'];
        $razon_socialX=$rowComp['razon_social'];
        $tipoPago=$rowComp['tipo_pago'];
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
        
        $importe_realXBruto=$importe_realX; 
        $totalImporteBruto+=$importe_realXBruto;
        //APLICAMOS EL IVA
        $importe_realX=$importe_realX*($valorIVA/100);

        $totalImporte+=$importe_realX;

        $txtPorcentaje="";
        $txtPorcentaje="text-right text-success small";  
        $personalReporte="";
        $personalReporte=namePersonalCompleto($rowComp['cod_personal']);
        $html.='<tr>'.
                      '<td class="text-left font-weight-bold">'.$unidadX.' </td>'.
                      '<td class="text-left font-weight-bold">'.$areaX.'</td>'.
                      '<td class="text-left font-weight-bold">'.$tipoPago.'</td>'.
                      '<td class="text-right">'.strftime('%d/%m/%Y',strtotime($fecha_fac)).' </td>'.
                      '<td class="text-right">'.$nroFactura.' </td>'.
                      '<td class="text-right">'.$nitX.' </td>'.
                      '<td class="text-left">'.$razon_socialX.'</td>'.
                      '<td class="text-left" style="'.$txtEstiloPersonal.'">'.$personalReporte.'</td>'.
                      '<td class="text-left">'.$origenFacturaX.'</td>'.
                      '<td class="text-right font-weight-bold">'.formatNumberDec($importe_realXBruto).' </td>'. 
                      '<td class="text-right font-weight-bold">'.formatNumberDec($importe_realX).' </td>'.     
                  '</tr>';
    }
        $html.='<tr class="bg-secondary text-white">'.
                    '<td colspan="9" class="text-center">Importe Total</td>'.
                    '<td class="d-none"></td>'.
                    '<td class="d-none"></td>'.
                    '<td class="d-none"></td>'.
                    '<td class="d-none"></td>'.
                    '<td class="d-none"></td>'.
                    '<td class="d-none"></td>'.
                    '<td class="d-none"></td>'.
                    '<td class="d-none"></td>'.
                    '<td class="text-right font-weight-bold small">'.formatNumberDec($totalImporteBruto).'</td>'.       
                    '<td class="text-right font-weight-bold small">'.formatNumberDec($totalImporte).'</td>'.      
                '</tr>';

    $html.=    '</tbody><tfoot>
      <tr style="background:#21618C; color:#fff;">
        <th class="small" width="5%"><small>Oficina</small></th>      
        <th class="small" width="5%"><small>Area</small></th>      
        <th class="small" width="5%"><small>Tipo Pago</small></th>
        <th class="small" width="5%"><small>Fecha</small></th>
        <th class="small" width="3%"><small><small># Factura</small></small></th>
        <th class="small" width="4%"><small>Nit</small></th>
        <th class="small" width="30%"><small>Razón Social</small></th>      
        <th class="small" width="7%"><small>Personal</small></th>
        <th class="small" width="7%"><small>Origen</small></th>
        <th class="small" width="5%"><small>Importe Bruto</small></th>
        <th class="small" width="5%"><small>Importe Neto</small></th>        
      </tr>
    </tfoot></table>';

    echo $html;
    ?>
  </div>
</div>
              