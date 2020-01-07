<?php
require_once '../conexion.php';
require_once '../functions.php';

session_start();
setlocale(LC_TIME, "Spanish");
//INICIAR valores de las sumas
$total1=0;$total2=0;$total3=0;$total4=0;$total5=0;$total6=0;

//datos para el titulo del reporte
$mesActual=strtoupper(nameMes(date("m")));
$anioActual=date("Y");
$nombreUnidad="Regional La Paz";


$fechaActual=date("Y-m-d");

//html del reporte
$html = '';
$html.='<html>'.
         '<head>'.
             '<!-- CSS Files -->'.
             '<link rel="icon" type="image/png" href="../assets/img/favicon.png">'.
             '<link href="../assets/libraries/plantillaPDF.css" rel="stylesheet" />'.
           '</head>';
$html.='<body>'.
        '<script type="text/php">'.
      'if ( isset($pdf) ) {'. 
        '$font = Font_Metrics::get_font("helvetica", "normal");'.
        '$size = 9;'.
        '$y = $pdf->get_height() - 24;'.
        '$x = $pdf->get_width() - 15 - Font_Metrics::get_text_width("1/1", $font, $size);'.
        '$pdf->page_text($x, $y, "{PAGE_NUM}/{PAGE_COUNT}", $font, $size);'.
      '}'.
    '</script>';
$html.=  '<header class="header">'.            
            '<img class="imagen-logo-izq" src="../assets/img/ibnorca2.jpg">'.
            '<div id="header_titulo_texto">PLANILLA DE SUELDOS PERIODO '.$mesActual.' '.$anioActual.'</div>'.
         '<div id="header_titulo_texto_inf">'.$nombreUnidad.'</div>'.
         '</header><br><br>';
         $html.='<table class="table">'.
            '<thead>'.
            '<tr class="bold table-title text-center">'.
              '<td>N.</td>'.
              '<td>N. Reg</td>'.
              '<td>COD</td>'.
              '<td>Nombres</td>'.
              '<td>Apellidos</td>'.
              '<td>C.I.</td>'.
              '<td>Ing Planilla</td>'.
              '<td>Cargo</td>'.
              '<td>Haber Basico</td>'.
              '<td>Días Trab</td>'.
              '<td>Bono Academico</td>'.
              '<td>Bono de Antig</td>'.
              '<td>Total ganado</td>'.
              '<td>Descuentos</td>'.
              '<td>Liquido Pagable</td>'; 

            $html.='</tr>'.
           '</thead>'.
           '<tbody>';
            $index=1;
            $codArea=0;
            $data = obtenerPlanillaSueldosRevision();
            while ($row = $data->fetch(PDO::FETCH_ASSOC)) {
              if($codArea!=$row['cod_area']){
               $html.='<tr>'.
                      '<td colspan="15">'.$row['area'].'</td>';                                  
                    $html.='</tr>'; 
               $codArea=$row['cod_area'];      
              }
             $html.='<tr>'.
                      '<td>'.$row['codigo'].'</td>'.
                      '<td>'.$row['codigo'].'</td>'.
                      '<td></td>'.
                      '<td>'.$row['nombres'].'</td>'.
                      '<td>'.$row['apellidos'].'</td>'.
                      '<td>'.$row['ci'].'</td>'.
                      '<td>'.strftime('%d/%m/%Y',strtotime($row['ing_planilla'])).'</td>'.
                      '<td>'.$row['cargo'].'</td>'.
                      '<td class="text-right">'.number_format($row['haber_basico'], 2, '.', ',').'</td>'.
                      '<td class="text-right">'.$row['dias_trabajados'].'</td>'.
                      '<td class="text-right">'.number_format($row['bono_academico'], 2, '.', ',').'</td>'.
                      '<td class="text-right">'.number_format($row['bono_antiguedad'], 2, '.', ',').'</td>'.
                      '<td class="text-right">'.number_format($row['total_ganado'], 2, '.', ',').'</td>'.
                      '<td class="text-right">'.number_format($row['monto_descuentos'], 2, '.', ',').'</td>'.
                      '<td class="text-right">'.number_format($row['liquido_pagable'], 2, '.', ',').'</td>';                                  
                    $html.='</tr>';

                  //suma de totales
                  $total1+=$row['haber_basico'];
                  $total2+=$row['bono_academico'];   
                  $total3+=$row['bono_antiguedad']; 
                  $total4+=$row['total_ganado']; 
                  $total5+=$row['monto_descuentos']; 
                  $total6+=$row['liquido_pagable']; 
              }
      $html.='<tr class="bold table-title">'.
                  '<td colspan="8" class="text-center">Sumas:</td>'.
                  '<td class="text-right">'.number_format($total1, 2, '.', ',').'</td>'.
                  '<td></td>'.
                  '<td class="text-right">'.number_format($total2, 2, '.', ',').'</td>'.
                  '<td class="text-right">'.number_format($total3, 2, '.', ',').'</td>'.
                  '<td class="text-right">'.number_format($total4, 2, '.', ',').'</td>'.
                  '<td class="text-right">'.number_format($total5, 2, '.', ',').'</td>'.
                  '<td class="text-right">'.number_format($total6, 2, '.', ',').'</td>';                  
      $html.='</tr>';
              '</tbody>';
$html.=    '</table>';        
$html.='</body>'.
      '</html>';           
descargarPDFHorizontal("prueba Reporte Planilla",$html);
?>
