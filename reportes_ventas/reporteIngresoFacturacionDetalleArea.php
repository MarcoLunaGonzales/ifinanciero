
<div class="card-body">
  <h6 class="card-title">Periodo: <?=$periodoTitle?></h6>
  <h6 class="card-title">Areas: <?=$areaAbrev;?></h6>
  <h6 class="card-title">Oficinas:<?=$unidadAbrev?></h6>
  <h6 class="card-title">Formas Pago:<?=$formas_pago_titulo?></h6>  
  <div class="table-responsive">
    <?php

    $html='<table class="table table-bordered table-condensed" id="libro_mayor_rep" width="50%" align="center">'.
            '<thead >'.
            '<tr class="text-center" style="background:#40A3A8;color:#ffffff;">'.
              '<th width="5%">Area</th>'.
              '<th width="10%">Importe Neto</th>'.
            '</tr>'.
           '</thead>'.
           '<tbody>';

    $valorIVA=100-(obtenerValorConfiguracion(1));
    
    $listaDetalle=obtenerListaVentasArea($unidadCostoArray,$areaCostoArray,$desde,$hasta,$forma_pagoArray);
    $totalImporte=0;
    while ($rowComp = $listaDetalle->fetch(PDO::FETCH_ASSOC)) {
        $codAreaX=$rowComp['cod_area'];
        $areaX=$rowComp['area'];
        $importe_realX=$rowComp['importe_real'];
        $totalImporte+=$importe_realX;
        $html.='<tr>'.
                      '<td class="text-left font-weight-bold">'.$areaX.'</td>'.
                      '<td class="text-right font-weight-bold">'.formatNumberDec($importe_realX).' </td>'.     
                  '</tr>';

        $longitudUnidades = count($unidadCosto);
        for($i=0; $i<$longitudUnidades; $i++){
          $unidadDetAbrevY=abrevUnidad($unidadCosto[$i]);
          $listaDetalleUnidades=obtenerListaVentasArea($unidadCosto[$i],$codAreaX,$desde,$hasta,$forma_pagoArray);
          while ($rowCompUnidades = $listaDetalleUnidades->fetch(PDO::FETCH_ASSOC)) {
            $importe_realY=$rowCompUnidades['importe_real'];
            if($importe_realY>0){
              $html.='<tr">'.
                    '<td class="text-center">'.$unidadDetAbrevY.'</td>'.  
                    '<td class="text-right font-weight-bold small">'.formatNumberDec($importe_realY).'</td>'.      
                '</tr>';              
            }        
          }
        }
    }    
        $html.='<tr class="bg-secondary text-white">'.
                    '<td colspan="1" class="text-center">Importe Total</td>'.  
                    '<td class="text-right font-weight-bold small">'.formatNumberDec($totalImporte).'</td>'.      
                '</tr>';

    $html.=    '</tbody></table>';

    echo $html;
    ?>
  </div>
</div>
              