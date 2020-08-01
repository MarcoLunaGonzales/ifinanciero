
<div class="card-body">
  <h6 class="card-title">Periodo: <?=$periodoTitle?></h6>
  <!-- <h6 class="card-title">Areas: <?=$areaAbrev;?></h6> -->
  <h6 class="card-title">Oficinas:<?=$unidadAbrev?></h6>
  <div class="table-responsive">
    <?php

    $html='<table class="table table-bordered table-condensed" id="libro_mayor_rep" width="80%" align="center">'.
            '<thead >'.
            '<tr class="text-center" style="background:#40A3A8;color:#ffffff;">'.
            '<th width="20%">Codigo/th>'.
              '<th width="20%">Area</th>'.
              '<th width="10%">Importe Neto</th>'.
            '</tr>'.
           '</thead>'.
           '<tbody>';

    // $valorIVA=100-(obtenerValorConfiguracion(1));  
    // $listaDetalle=obtenerListaVentasA_servicios($unidadCostoArray,$desde,$hasta);
    // $codigos_sf="";
    // while ($rowComp = $listaDetalle->fetch(PDO::FETCH_ASSOC)) {
    //   $codigos_sf.=$rowComp['codigo'].",";
    // }
    // $codigos_sf=trim($codigos_sf,",");

    $listaDetalleUnidades=obtenerListaVentasA_servicios($unidadCostoArray,$serviciosArray,$desde,$hasta);
    $totalImporte=0;
    while ($rowComp = $listaDetalleUnidades->fetch(PDO::FETCH_ASSOC)) {
        $codigo_alterno=$rowComp['Codigo'];
        $IdtipoX=$rowComp['IdTipo'];
        $descripcion_n2=$rowComp['descripcion_n2'];
        $importe_realX=$rowComp['importe_real'];
        $totalImporte+=$importe_realX;
        $html.='<tr>'.
                      '<td class="text-left font-weight-bold">'.$codigo_alterno.'</td>'.
                      '<td class="text-left font-weight-bold">'.$descripcion_n2.'</td>'.
                      '<td class="text-right font-weight-bold">'.formatNumberDec($importe_realX).' </td>'.     
                  '</tr>';

        $longitudUnidades = count($unidadCosto);
        for($i=0; $i<$longitudUnidades; $i++){
          $unidadDetAbrevY=abrevUnidad($unidadCosto[$i]);

          // $listaDetalle=obtenerListaVentasA_servicios($unidadCostoArray,$desde,$hasta);
          // $codigos_sf="";
          // while ($rowComp = $listaDetalle->fetch(PDO::FETCH_ASSOC)) {
          //   $codigos_sf.=$rowComp['codigo'].",";
          // }
          // $codigos_sf=trim($codigos_sf,",");
          $listaDetalleUnidades4=obtenerListaVentasA_servicios($unidadCosto[$i],$IdtipoX,$desde,$hasta);
          while ($rowCompUnidades = $listaDetalleUnidades4->fetch(PDO::FETCH_ASSOC)) {
            $importe_realY=$rowCompUnidades['importe_real'];
            if($importe_realY>0){
              $html.='<tr">'.
                    '<td class="text-center">-</td>'.  
                    '<td class="text-center">'.$unidadDetAbrevY.'</td>'.  
                    '<td class="text-right font-weight-bold small">'.formatNumberDec($importe_realY).'</td>'.      
                '</tr>';              
            }        
          }
        }
    }    
        $html.='<tr class="bg-secondary text-white">'.
                    '<td class="text-center">-</td>'.  
                    '<td colspan="1" class="text-center">Importe Total</td>'.  
                    '<td class="text-right font-weight-bold small">'.formatNumberDec($totalImporte).'</td>'.      
                '</tr>';

    $html.=    '</tbody></table>';

    echo $html;
    ?>
  </div>
</div>
              