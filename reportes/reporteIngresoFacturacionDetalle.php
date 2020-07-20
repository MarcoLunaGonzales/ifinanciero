
<div class="card-body">
  <h6 class="card-title">Periodo: <?=$periodoTitle?></h6>
  <h6 class="card-title">Areas: <?=$areaAbrev;?></h6>
  <h6 class="card-title">Oficinas:<?=$unidadAbrev?></h6>
  <div class="table-responsive">
     <?php
    $html='<table class="table table-bordered table-condensed" id="libro_mayor_rep">'.
            '<thead >'.
            '<tr class="text-center" style="background:#40A3A8;color:#ffffff;">'.
              '<th width="5%">Oficina</th>'.
              '<th width="5%">Area</th>'.
              '<th width="5%">Fecha Factura</th>'.
              '<th width="37%">Razon Social</th>'.
              '<th>Distribución Area</th>'.
              '<th width="15%">NIT</th>'.
              '<th width="15%">Importe Neto</th>'.
            '</tr>'.
           '</thead>'.
           '<tbody>';

    $listaDetalle=obtenerListaVentasResumido($unidadCostoArray,$areaCostoArray,$solo_tienda,$desde,$hasta);
    $totalImporte=0;
    while ($rowComp = $listaDetalle->fetch(PDO::FETCH_ASSOC)) {
        $codigoX=$rowComp['codigo'];
        
        $unidadX=$rowComp['unidad'];
        $areaX=$rowComp['area'];
        $razon_socialX=$rowComp['razon_social'];
        if($rowComp['cod_solicitudfacturacion']==-100){
          $razon_socialX.='<label class="text-info">(Tienda)</label>';
        }
        $nitX=$rowComp['nit'];
        $importe_realX=$rowComp['importe_real'];
        $fecha_fac=$rowComp['fecha_factura'];
        $totalImporte+=$importe_realX;
        $datosSolicitados=obtenerDatosDistribucionSolicitudFacturacion($rowComp['cod_solicitudfacturacion']);
        $porcentajeArea="";
        if(!($datosSolicitados[2]==null||$datosSolicitados[2]==""||$datosSolicitados[2]<0)){
          $porcentajeArea=$datosSolicitados[2]." %";
        }
        
        $html.='<tr>'.
                      '<td class="text-left font-weight-bold">'.$unidadX.' </td>'.
                      '<td class="text-left font-weight-bold">'.$areaX.' '.$porcentajeArea.'</td>'.
                      '<td class="text-right">'.strftime('%d/%m/%Y',strtotime($fecha_fac)).' </td>'.
                      '<td class="text-left">'.$razon_socialX.'</td>'.
                      '<td class="text-left text-success">'.$datosSolicitados[4].'</td>'.
                      '<td class="text-right">'.$nitX.' </td>'.
                      '<td class="text-right font-weight-bold">'.formatNumberDec($importe_realX).' </td>'.     
                  '</tr>';
    }
        $html.='<tr class="bg-secondary text-white">'.
                    '<td colspan="6" class="text-center">Importe Total</td>'.
                    '<td style="display: none;"></td>'.
                    '<td style="display: none;"></td>'.
                    '<td style="display: none;"></td>'.
                    '<td style="display: none;"></td>'.
                    '<td style="display: none;"></td>'.
                    '<td class="text-right font-weight-bold small">'.formatNumberDec($totalImporte).'</td>'.      
                '</tr>';

    $html.=    '</tbody></table>';

    echo $html;
    ?>
  </div>
</div>
              