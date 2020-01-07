<?php
require_once '../conexion.php';
require_once '../functions.php';

session_start();
setlocale(LC_TIME, "Spanish");
//INICIAR valores de las sumas
$total1=0;$total2=0;$total3=0;$total4=0;$total5=0;$total6=0;$total7=0;$total8=0;$total9=0;$total10=0;$total11=0;$total12=0;$total13=0;$total14=0;$total15=0;

//datos para el titulo del reporte
$mesActual=strtoupper(nameMes(date("m")));
$anioActual=date("Y");
$nombreUnidad="Reporte IBNORCA";


$fechaActual=date("Y-m-d");

$mes=strtoupper(nameMes($_GET['cod_mes']));
$gestion=nameGestion($_GET['cod_gestion']);
$codPlanilla=$_GET['codigo_trib'];
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
            '<div id="header_titulo_texto">PLANILLA TRIBUTARIA PERIODO '.$mes.' '.$gestion.'</div>'.
         '<div id="header_titulo_texto_inf">'.$nombreUnidad.'</div>'.
         '</header><br><br>';
         $html.='<table class="table">'.
            '<thead>'.
            '<tr class="bold table-title text-center">'.
              '<td>N.</td>'.
              '<td>Nombres</td>'.
              '<td>Apellidos</td>'.
              /*'<td>C.I.</td>'.
              '<td>Cargo</td>'.*/

              '<td>Total Ganado</td>'.
              '<td>Min No Imp</td>'.
              '<td>Sueldo Grav</td>'.
              '<td>13% Sueldo Grav</td>'.
              '<td>13% Form 110</td>'.
              '<td>13% Min No Imp</td>'.
              '<td>Fisco</td>'.
              '<td>Depend</td>'.
              '<td>Saldo Mes Ant</td>'.
              '<td>Saldo Mes Ant Act</td>'.
              '<td>Total Saldo</td>'.
              '<td>Total Saldo Favor</td>'.
              '<td>Saldo Utilizado</td>'.
              '<td>Importe Retenido</td>';
              /*'<td>Saldo Sig Mes</td>';*/ 

            $html.='</tr>'.
           '</thead>'.
           '<tbody>';
            $index=1;
            $codArea=0;
            $data = obtenerPlanillaTributariaReporte($codPlanilla);
            while ($row = $data->fetch(PDO::FETCH_ASSOC)) {
              if($codArea!=$row['cod_area']){
               $html.='<tr>'.
                      '<td colspan="17">'.$row['area'].'</td>';                                  
                    $html.='</tr>'; 
               $codArea=$row['cod_area'];      
              }
             $html.='<tr>'.
                      '<td>'.$row['cod_personal'].'</td>'.
                      '<td>'.$row['nombres'].'</td>'.
                      '<td>'.$row['apellidos'].'</td>'.
                      /*'<td>'.$row['ci'].'</td>'.
                      '<td>'.$row['cargo'].'</td>'. */ 
                      
                      '<td class="text-right">'.number_format($row['total_ganado'], 2, '.', ',').'</td>'.
                      '<td class="text-right">'.number_format($row['minimo_no_imponible'], 2, '.', ',').'</td>'.
                      '<td class="text-right">'.number_format($row['sueldo_gravado'], 2, '.', ',').'</td>'.
                      '<td class="text-right">'.number_format($row['porcentaje_sueldogravado'], 2, '.', ',').'</td>'.
                      '<td class="text-right">'.number_format($row['porcentaje_formulario110'], 2, '.', ',').'</td>'.
                      '<td class="text-right">'.number_format($row['porcentaje_minimonoimponible'], 2, '.', ',').'</td>'.
                      '<td class="text-right">'.number_format($row['fisco'], 2, '.', ',').'</td>'.
                      '<td class="text-right">'.number_format($row['dependiente'], 2, '.', ',').'</td>'.
                      '<td class="text-right">'.number_format($row['saldo_mes_anterior'], 2, '.', ',').'</td>'.
                      '<td class="text-right">'.number_format($row['saldo_mes_anterior_actualizado'], 2, '.', ',').'</td>'.
                      '<td class="text-right">'.number_format($row['total_saldo'], 2, '.', ',').'</td>'.
                      '<td class="text-right">'.number_format($row['total_saldo_favordependiente'], 2, '.', ',').'</td>'.
                      '<td class="text-right">'.number_format($row['saldo_utilizado'], 2, '.', ',').'</td>'.
                      '<td class="text-right">'.number_format($row['importe_retenido'], 2, '.', ',').'</td>';
                      /*'<td class="text-right">No hay dato</td>'; */                                 
                    $html.='</tr>';

                  //suma de totales
                  $total1+=$row['total_ganado'];
                  $total2+=$row['minimo_no_imponible'];   
                  $total3+=$row['sueldo_gravado']; 
                  $total4+=$row['porcentaje_sueldogravado']; 
                  $total5+=$row['porcentaje_formulario110']; 
                  $total6+=$row['porcentaje_minimonoimponible'];
                  $total7+=$row['fisco'];
                  $total8+=$row['dependiente'];
                  $total9+=$row['saldo_mes_anterior'];
                  $total10+=$row['saldo_mes_anterior_actualizado'];
                  $total11+=$row['total_saldo'];
                  $total12+=$row['total_saldo_favordependiente']; 
                  $total13+=$row['saldo_utilizado'];
                  $total14+=$row['importe_retenido'];
                  $total15+=0;
              }
      $html.='<tr class="bold table-title">'.
                  '<td colspan="3" class="text-center">Sumas:</td>'.
                  '<td class="text-right">'.number_format($total1, 2, '.', ',').'</td>'.
                  '<td class="text-right">'.number_format($total2, 2, '.', ',').'</td>'.
                  '<td class="text-right">'.number_format($total3, 2, '.', ',').'</td>'.
                  '<td class="text-right">'.number_format($total4, 2, '.', ',').'</td>'.
                  '<td class="text-right">'.number_format($total5, 2, '.', ',').'</td>'.
                  '<td class="text-right">'.number_format($total6, 2, '.', ',').'</td>'.
                  '<td class="text-right">'.number_format($total7, 2, '.', ',').'</td>'.
                  '<td class="text-right">'.number_format($total8, 2, '.', ',').'</td>'.
                  '<td class="text-right">'.number_format($total9, 2, '.', ',').'</td>'.
                  '<td class="text-right">'.number_format($total10, 2, '.', ',').'</td>'.
                  '<td class="text-right">'.number_format($total11, 2, '.', ',').'</td>'.
                  '<td class="text-right">'.number_format($total12, 2, '.', ',').'</td>'.
                  '<td class="text-right">'.number_format($total13, 2, '.', ',').'</td>'.
                  '<td class="text-right">'.number_format($total14, 2, '.', ',').'</td>';
                  /*'<td class="text-right">'.number_format($total15, 2, '.', ',').'</td>'; */                 
      $html.='</tr>';
              '</tbody>';
$html.=    '</table>';        
$html.='</body>'.
      '</html>';           
descargarPDFHorizontal("Reporte Planilla Tributaria",$html);
?>
