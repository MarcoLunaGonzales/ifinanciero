
<div class="card-body">
  <h6 class="card-title">Periodo: <?=$periodoTitle?></h6>
  <h6 class="card-title">Areas: <?=$areaAbrev;?></h6>
  <h6 class="card-title">Oficinas:<?=$unidadAbrev?></h6>
  <div class="table-responsive">
    <?php

    $html='<table class="table table-bordered table-condensed" id="libro_mayor_rep" width="80%" align="center">'.
            '<thead >'.
            '<tr class="text-center" style="background:#40A3A8;color:#ffffff;">'.
            '<th width="10%">Areas</th>'.
            '<th width="20%">Codigo</th>'.
              '<th >Servicio</th>'.
              '<th width="10%">Importe Neto</th>'.
            '</tr>'.
           '</thead>'.
           '<tbody>';

 

    $listaDetalleUnidades=obtenerListaVentasA_servicios($unidadCostoArray,$areasArray,0,$desde,$hasta);
    $totalImporte=0;
    while ($rowComp = $listaDetalleUnidades->fetch(PDO::FETCH_ASSOC)) {
        $codigo_alterno=$rowComp['Codigo'];
        $IdtipoX=$rowComp['IdTipo'];
        $codAreaX=$rowComp['cod_area'];
        $nombreAreaX=$rowComp['area'];
        // $codAreaX="";
        // $nombreAreaX="";
        $descripcion_n2=$rowComp['descripcion_n2'];
        $importe_realX=$rowComp['importe_real'];
        $totalImporte+=$importe_realX;
        $html.='<tr>'.
                      '<td class="text-left font-weight-bold">'.$nombreAreaX.'</td>'.
                      '<td class="text-left font-weight-bold">'.$codigo_alterno.'</td>'.
                      '<td class="text-left font-weight-bold">'.mb_strtoupper($descripcion_n2).'</td>'.
                      '<td class="text-right font-weight-bold">'.formatNumberDec($importe_realX).' </td>'.     
                  '</tr>';

        $longitudUnidades = count($unidadCosto);
        for($i=0; $i<$longitudUnidades; $i++){
          $unidadDetAbrevY=abrevUnidad($unidadCosto[$i]);
          $listaDetalleUnidades4=obtenerListaVentasA_servicios($unidadCosto[$i],$areasArray,$IdtipoX,$desde,$hasta);
          while ($rowCompUnidades = $listaDetalleUnidades4->fetch(PDO::FETCH_ASSOC)) {
            $importe_realY=$rowCompUnidades['importe_real'];
            if($importe_realY>0){
              $html.='<tr">'.
                    '<td class="text-center">-</td>'.  
                    '<td class="text-center">-</td>'.  
                    '<td class="text-center">'.$unidadDetAbrevY.'</td>'.  
                    '<td class="text-right font-weight-bold small">'.formatNumberDec($importe_realY).'</td>'.      
                '</tr>';              
            }        
          }
        }
    } 
    //para los cursos
    $listaDetalle_cursos=obtenerListaVentas_cursos($unidadCostoArray,0,$areasArray,$desde,$hasta);
    // $totalImporte=0;
    while ($rowComp = $listaDetalle_cursos->fetch(PDO::FETCH_ASSOC)) {        
        $codAreaX=$rowComp['cod_area'];
        $nombreAreaX=$rowComp['area'];
        $IdtipoX=$rowComp['IdCurso'];
        $codigo_alterno=obtenerCodigoExternoCurso($IdtipoX);
        $descripcion_n2=obtenerNombreCurso($IdtipoX);
        $importe_realX=$rowComp['importe_real'];
        // $tipo_cursoX=$rowComp['tipo_curso'];
        $string_curso="Curso ( ";        

        $totalImporte+=$importe_realX;
        $html.='<tr>'.
                      '<td class="text-left font-weight-bold">'.$nombreAreaX.'</td>'.
                      '<td class="text-left font-weight-bold">'.$string_curso.$codigo_alterno.')</td>'.
                      '<td class="text-left font-weight-bold">'.mb_strtoupper($descripcion_n2).'</td>'.
                      '<td class="text-right font-weight-bold">'.formatNumberDec($importe_realX).' </td>'.     
                  '</tr>';

        $longitudUnidades = count($unidadCosto);
        for($i=0; $i<$longitudUnidades; $i++){
          $unidadDetAbrevYX=abrevUnidad($unidadCosto[$i]);
          $listaDetalleCursos_4=obtenerListaVentas_cursos($unidadCosto[$i],$IdtipoX,$areasArray,$desde,$hasta);
          while ($rowCompUnidades = $listaDetalleCursos_4->fetch(PDO::FETCH_ASSOC)) {
            $importe_realY=$rowCompUnidades['importe_real'];
            if($importe_realY>0){
              $html.='<tr">'.
                    '<td class="text-center">-</td>'.  
                    '<td class="text-center">-</td>'.  
                    '<td class="text-center">'.$unidadDetAbrevYX.'</td>'.  
                    '<td class="text-right font-weight-bold small">'.formatNumberDec($importe_realY).'</td>'.      
                '</tr>';              
            }        
          }
        }
    }
    // //para los cursos grupales
    // $listaDetalle_cursos_grupal=obtenerListaVentas_cursos_grupal($unidadCostoArray,0,$desde,$hasta);
    // // $totalImporte=0;
    // while ($rowComp = $listaDetalle_cursos_grupal->fetch(PDO::FETCH_ASSOC)) {        
        
    //     $IdtipoX=$rowComp['cod_curso'];
    //     $codigo_alterno=obtenerCodigoExternoCurso($IdtipoX);
    //     $descripcion_n2=obtenerNombreCurso($IdtipoX);
    //     $importe_realX=$rowComp['importe_real'];
    //     // $tipo_cursoX=$rowComp['tipo_curso'];        
    //      $string_curso="Curso Grupal ( "; 

    //     $totalImporte+=$importe_realX;
    //     $html.='<tr>'.
    //                   '<td class="text-left font-weight-bold">'.$string_curso.$codigo_alterno.')</td>'.
    //                   '<td class="text-left font-weight-bold">'.mb_strtoupper($descripcion_n2).'</td>'.
    //                   '<td class="text-right font-weight-bold">'.formatNumberDec($importe_realX).' </td>'.     
    //               '</tr>';

    //     $longitudUnidades = count($unidadCosto);
    //     for($i=0; $i<$longitudUnidades; $i++){
    //       $unidadDetAbrevYX=abrevUnidad($unidadCosto[$i]);
    //       $listaDetalleCursos_4=obtenerListaVentas_cursos_grupal($unidadCosto[$i],$IdtipoX,$desde,$hasta);
    //       while ($rowCompUnidades = $listaDetalleCursos_4->fetch(PDO::FETCH_ASSOC)) {
    //         $importe_realY=$rowCompUnidades['importe_real'];
    //         if($importe_realY>0){
    //           $html.='<tr">'.
    //                 '<td class="text-center">-</td>'.  
    //                 '<td class="text-center">'.$unidadDetAbrevYX.'</td>'.  
    //                 '<td class="text-right font-weight-bold small">'.formatNumberDec($importe_realY).'</td>'.      
    //             '</tr>';              
    //         }        
    //       }
    //     }
    // } 

    $html.='<tr class="bg-secondary text-white">'.
                '<td class="text-center">-</td>'.  
                '<td class="text-center">-</td>'.  
                '<td colspan="1" class="text-center">Importe Total</td>'.  
                '<td class="text-right font-weight-bold small">'.formatNumberDec($totalImporte).'</td>'.      
            '</tr>';

    $html.=    '</tbody></table>';

    echo $html;
    ?>
  </div>
</div>
              