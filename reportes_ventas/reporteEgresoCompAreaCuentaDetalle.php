
<div class="card-body">
  <h6 class="card-title">Periodo: <?=$periodoTitle?></h6>
  <h6 class="card-title">Areas: <?=$areaAbrev;?></h6>
  <h6 class="card-title">Oficinas:<?=$unidadAbrev?></h6>
  <!--<h6 class="card-title">Cuentas:<?=$cuentaNombre?></h6>-->
  <div class="table-responsive">
    <?php

    $html='<table class="table table-bordered table-condensed" id="libro_mayor_rep" width="80%" align="center">'.
            '<thead >'.
            '<tr class="text-center" style="background:#40A3A8;color:#ffffff;">'.
            '<th width="10%">Areas</th>'.
            '<th width="20%">Numero</th>'.
              '<th >Cuenta</th>'.
              '<th width="10%">Monto Neto</th>'.
            '</tr>'.
           '</thead>'.
           '<tbody>';

    $totalImporte=0;$indexCuenta=0;
    foreach ($areas as $key => $areasX) {
      $listaDetalleUnidades=obtenerListaCuentasEgreso($unidadCostoArray,$areasX,$cuentasArray,$desde,$hasta);      
    while ($rowComp = $listaDetalleUnidades->fetch(PDO::FETCH_ASSOC)) {
        //$codigo_alterno=$rowComp['Codigo'];
        $cuentaX=$rowComp['cuenta'];
        $codigo_cuenta=$rowComp['cod_cuenta'];
        $codAreaX=$rowComp['cod_area'];
        $nombreAreaX=$rowComp['area'];
        $numeroX=$rowComp['numero_cuenta'];
        $importe_realX=0;
        $listaDetalleUnidadesAreas=obtenerListaCuentasEgreso($unidadCostoArray,$codAreaX,$codigo_cuenta,$desde,$hasta);
        $rowCompAreas = $listaDetalleUnidadesAreas->fetch();    
        $importe_realX=abs($rowCompAreas['monto_real']);  
        
        $totalImporte+=$importe_realX;
        $funcionOnclick='filasPresupuesto('.$indexCuenta.')';
        $html.='<tr onclick="'.$funcionOnclick.'">'.
                      '<td class="text-left font-weight-bold">'.$nombreAreaX.'</td>'.
                      '<td class="text-left font-weight-bold">'.$numeroX.'</td>'.
                      '<td class="text-left font-weight-bold">'.mb_strtoupper($cuentaX).'</td>'.
                      '<td class="text-right font-weight-bold">'.formatNumberDec($importe_realX).' </td>'.     
                  '</tr>';

        $longitudUnidades = count($unidadCosto);
        for($i=0; $i<$longitudUnidades; $i++){
          $unidadDetAbrevY=abrevUnidad($unidadCosto[$i]);
          $listaDetalleUnidades4=obtenerListaCuentasEgreso($unidadCosto[$i],$codAreaX,$codigo_cuenta,$desde,$hasta);
          $rowCompUnidades = $listaDetalleUnidades4->fetch();    
          $importe_realY=abs($rowCompUnidades['monto_real']);
          if($importe_realY>0){
              $html.='<tr class="cuenta'.$indexCuenta.'" style="display:none">'.
                    '<td class="text-center">-</td>'.  
                    '<td class="text-center">-</td>'.  
                    '<td class="text-center">'.$unidadDetAbrevY.'</td>'.  
                    '<td class="text-right font-weight-bold small">'.formatNumberDec($importe_realY).'</td>'.      
                '</tr>';              
          } 
        }
        $indexCuenta++;
    } 
  }//FIN



    $html.='<tr class="bg-secondary text-white">'.
                '<td class="text-center">-</td>'.  
                '<td class="text-center">-</td>'.  
                '<td colspan="1" class="text-center">Monto Total</td>'.  
                '<td class="text-right font-weight-bold small">'.formatNumberDec($totalImporte).'</td>'.      
            '</tr>';

    $html.=    '</tbody></table>';

    echo $html;
    ?>
  </div>
</div>
              