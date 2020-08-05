
<div class="card-body">
  <h6 class="card-title">Periodo: <?=$periodoTitle?></h6>
  <h6 class="card-title">Areas: <?=$areaAbrev;?></h6>
  <h6 class="card-title">Oficinas:<?=$unidadAbrev?></h6>
  <!-- <h6 class="card-title">Formas Pago:<?=$formas_pago_titulo?></h6>   -->
  <div class="table-responsive">
    <?php
    $txtEstiloPersonal="";
    if($filtroPersonal==0){
      $txtEstiloPersonal="display: none;";
    }

    $html='<table class="table table-bordered table-condensed" id="libro_mayor_rep">'.
            '<thead >'.
            '<tr class="text-center" style="background:#40A3A8;color:#ffffff;">'.
              '<th width="5%">Oficina</th>'.
              '<th width="5%">Area</th>'.
              '<th width="10%">Fecha Factura</th>'.
              '<th width="10%"># Factura</th>'.
              '<th width="15%">NIT</th>'.
              '<th width="10%">Razon Social</th>'.
              '<th width="10%" style="'.$txtEstiloPersonal.'">Personal</th>'.
              '<th width="10%">Origen</th>'.
              '<th width="10%">Distribución Area</th>'.
              '<th width="10%">Importe Neto</th>'.
            '</tr>'.
           '</thead>'.
           '<tbody>';
           // $forma_pagoArray="";
    $valorIVA=100-(obtenerValorConfiguracion(1));
    
    $listaDetalle=obtenerListaVentasResumido($unidadCostoArray,$areaCostoArray,$solo_tienda,$desde,$hasta,$solo_credito);
    $totalImporte=0;
    while ($rowComp = $listaDetalle->fetch(PDO::FETCH_ASSOC)) {
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
        $porcentajeArea=$rowComp['porcentaje'];

        $personalFacturador=$rowComp['facturador'];
        $personalSolicitante=$rowComp['solicitante'];

        $importe_realX=$importe_realX*($porcentajeArea/100);
        //APLICAMOS EL IVA
        $importe_realX=$importe_realX*($valorIVA/100);

        $totalImporte+=$importe_realX;

        $txtPorcentaje="";
        if($porcentajeArea==100){
          $txtPorcentaje="text-right text-success small";
        }else{
          $txtPorcentaje="text-right text-danger font-weight-bold";
        }

        $personalReporte="";
        if($filtroPersonal==1){
          $personalReporte=$personalFacturador;
        }elseif($filtroPersonal==2){
          $personalReporte=$personalSolicitante;
        }
        
        $html.='<tr>'.
                      '<td class="text-left font-weight-bold">'.$unidadX.' </td>'.
                      '<td class="text-left font-weight-bold">'.$areaX.'</td>'.
                      '<td class="text-right">'.strftime('%d/%m/%Y',strtotime($fecha_fac)).' </td>'.
                      '<td class="text-right">'.$nroFactura.' </td>'.
                      '<td class="text-right">'.$nitX.' </td>'.
                      '<td class="text-left">'.$razon_socialX.'</td>'.
                      '<td class="text-left" style="'.$txtEstiloPersonal.'">'.$personalReporte.'</td>'.
                      '<td class="text-left">'.$origenFacturaX.'</td>'.
                      '<td class="'.$txtPorcentaje.'">'.$porcentajeArea.'%</td>'.
                      '<td class="text-right font-weight-bold">'.formatNumberDec($importe_realX).' </td>'.     
                  '</tr>';
    }
        $html.='<tr class="bg-secondary text-white">'.
                    '<td colspan="8" class="text-center">Importe Total</td>'.
                    '<td class="text-right font-weight-bold small" style="'.$txtEstiloPersonal.'"></td>'.      
                    '<td class="text-right font-weight-bold small">'.formatNumberDec($totalImporte).'</td>'.      
                '</tr>';

    $html.=    '</tbody></table>';

    echo $html;
    ?>
  </div>
</div>
              