
<div class="card-body">
  <h6 class="card-title">Periodo: <?=$periodoTitle?></h6>
  <h6 class="card-title">Areas: <?=$areaAbrev;?></h6>
  <h6 class="card-title">Oficinas:<?=$unidadAbrev?></h6>
  <!--<h6 class="card-title">Formas Pago:<?=$formas_pago_titulo?></h6> --> 
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
              '<th width="5%">Fecha</th>'.
              '<th width="5%"># Comprobante</th>'.
              '<th width="20%">Cuenta</th>'.
              '<th width="25%">Detalle</th>'.
              '<th width="10%" style="'.$txtEstiloPersonal.'">Personal</th>'.
              '<th width="10%">Cliente/Proveedor</th>'.
              '<th width="10%">Monto</th>'.
            '</tr>'.
           '</thead>'.
           '<tbody>';

    $valorIVA=100-(obtenerValorConfiguracion(1));
    
    $listaDetalle=obtenerListaGastosResumido($unidadCostoArray,$areaCostoArray,$desde,$hasta);
    //echo obtenerListaGastosResumidoECHO($unidadCostoArray,$areaCostoArray,$desde,$hasta);
    $totalMonto=0;
    while ($rowComp = $listaDetalle->fetch(PDO::FETCH_ASSOC)) {
        $codigoX=$rowComp['codigo'];
        
        $unidadX=$rowComp['unidad'];
        $areaX=$rowComp['area'];
        $glosaX=$rowComp['glosa'];
        $glosaX=mb_strtoupper($glosaX);
        $tamanioGlosa=obtenerValorConfiguracion(72); 
        if($glosaX>$tamanioGlosa){
          $glosaX=substr($glosaX, 0, $tamanioGlosa);
        }
        $codClienteProv=$rowComp['cliente_proveedor'];
        $nombreAuxiliar=$rowComp['nombre_cliente_proveedor'];
        $origenAux="";
        if($codClienteProv==2){
          $origenAux.='<label class="text-info">Cliente:</label>';
        }else{
          if($codClienteProv==1){
            $origenAux.='<label class="text-primary">Proveedor:</label>';
          }
        }

        $montoX=$rowComp['monto'];
        $fechaX=$rowComp['fecha'];
        $cuentaX=trim($rowComp['numero_cuenta'])." - ".trim($rowComp['cuenta']);
        $numeroCompX=nombreComprobante($rowComp['cod_comprobante']);

        $personalX=$rowComp['personal'];

        $totalMonto+=$montoX;
      
        
        $html.='<tr>'.
                      '<td class="text-left font-weight-bold">'.$unidadX.' </td>'.
                      '<td class="text-left font-weight-bold">'.$areaX.'</td>'.
                      '<td class="text-right">'.strftime('%d/%m/%Y',strtotime($fechaX)).' </td>'.
                      '<td class="text-right font-weight-bold">'.$numeroCompX.' </td>'.
                      '<td class="text-right"><small><small>'.$cuentaX.' </small></small></td>'.
                      '<td class="text-left"><small><small>'.$glosaX.'</small></small></td>'.
                      '<td class="text-left" style="'.$txtEstiloPersonal.'">'.$personalX.'</td>'.
                      '<td class="text-left">'.$origenAux.' '.$nombreAuxiliar.'</td>'.
                      '<td class="text-right font-weight-bold">'.formatNumberDec($montoX).' </td>'.     
                  '</tr>';
    }
        $html.='<tr class="bg-secondary text-white">'.
                    '<td colspan="7" class="text-center">Monto Total</td>'.
                    '<td class="text-right font-weight-bold small" style="'.$txtEstiloPersonal.'"></td>'.      
                    '<td class="text-right font-weight-bold small">'.formatNumberDec($totalMonto).'</td>'.      
                '</tr>';

    $html.=    '</tbody></table>';

    echo $html;
    ?>
  </div>
</div>
              