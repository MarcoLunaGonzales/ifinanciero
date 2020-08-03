
<div class="card-body">
  <h6 class="card-title">Periodo: <?=$periodoTitle?></h6>
  <!-- <h6 class="card-title">Areas: <?=$areaAbrev;?></h6> -->
  <!-- <h6 class="card-title">Oficinas:<?=$unidadAbrev?></h6> -->
  <div class="table-responsive">
    <?php

    $html='<table class="table table-bordered table-condensed" id="libro_mayor_rep" width="80%" align="center">'.
            '<thead >'.
            '<tr class="text-center" style="background:#40A3A8;color:#ffffff;">'.
            '<th width="20%">Cod Libreta</th>'.
              '<th width="20%">Descripción</th>'.
              '<th width="10%">monto <br>Libreta</th>'.
              '<th width="10%">Saldo <br>Libreta</th>'.
              '<th width="10%">Nro<br>Factura</th>'.
              '<th width="10%">monto Factura</th>'.
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
    $totalLibreta=0;
    $totalSaldo=0;
    $totalFactura=0;
    $listaDetalleLibretas=obtenerListaVentas_libretas($desde,$hasta);
    $totalImporte=0;
    while ($rowComp = $listaDetalleLibretas->fetch(PDO::FETCH_ASSOC)) {
        $cod_libretabancariadetalle=$rowComp['cod_libretabancariadetalle'];
        // $cod_facturaventa=$rowComp['cod_facturaventa'];
        $array_facturas=obtenerFacturasLibreta($cod_libretabancariadetalle);
        $monto_factura=sumatotaldetallefactura_libretas($cod_libretabancariadetalle);
        // echo $monto_factura."";
        if($monto_factura>0){
          // echo $datos_libreta;        
          $monto_libreta=obtenerDatosLibreta_factura($cod_libretabancariadetalle,"monto");
          $descripcion_libreta=obtenerDatosLibreta_factura($cod_libretabancariadetalle,"informacion_complementaria");
          $saldo_libreta=obtenerSaldoLibretaBancariaDetalle($cod_libretabancariadetalle);
          // $nro_factura=obtenerNroFactura($cod_facturaventa);
          // $monto_factura=sumatotaldetallefactura($cod_facturaventa);                
          $totalLibreta+=$monto_libreta;
          $totalSaldo+=$saldo_libreta;
          // $totalFactura+=$monto_factura;
          $html.='<tr>'.
                        '<td class="text-left font-weight-bold">'.$cod_libretabancariadetalle.'</td>'.
                        '<td class="text-left font-weight-bold">'.$descripcion_libreta.'</td>'.
                        '<td class="text-right font-weight-bold">'.formatNumberDec($monto_libreta).' </td>'.     
                        '<td class="text-right font-weight-bold">'.formatNumberDec($saldo_libreta).' </td>'.     
                        '<td class="text-left font-weight-bold">'.$array_facturas.'</td>'.
                        '<td class="text-right font-weight-bold">'.formatNumberDec($monto_factura).' </td>'.     
                    '</tr>';
        }
    }    
    $totalFactura=obtener_saldo_total_facturas($desde,$hasta);
        $html.='<tr class="bg-secondary text-white">'.
                    '<td class="text-left font-weight-bold">-</td>'.
                      '<td class="text-left font-weight-bold">Total Libreta</td>'.
                      '<td class="text-right font-weight-bold">'.formatNumberDec($totalLibreta).' </td>'.     
                      '<td class="text-right font-weight-bold">'.formatNumberDec($totalSaldo).' </td>'.     
                      '<td class="text-left font-weight-bold">Total Factura</td>'.
                      '<td class="text-right font-weight-bold">'.formatNumberDec($totalFactura).' </td>'.     
                '</tr>';

        $diferencia=$totalLibreta-$totalFactura;
        $html.='<tr class="bg-secondary text-white">'.
                    '<td class="text-left font-weight-bold">-</td>'.
                      '<td class="text-left font-weight-bold">Libreta-Factura</td>'.
                      '<td class="text-right font-weight-bold">'.formatNumberDec($diferencia).' </td>'.     
                      '<td class="text-right font-weight-bold"></td>'.     
                      '<td class="text-left font-weight-bold"></td>'.
                      '<td class="text-right font-weight-bold"></td>'.     
                '</tr>';

    $html.=    '</tbody></table>';

    echo $html;
    ?>
  </div>
</div>
              
