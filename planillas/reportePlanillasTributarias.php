<?php
require_once '../conexion.php';
require_once '../functions.php';

require_once '../functionsGeneral.php';



session_start();
setlocale(LC_TIME, "Spanish");
//INICIAR valores de las sumas
$total_1=0;$total_2=0;$total_3=0;$total_4=0;$total_5=0;$total_6=0;$total_7=0;$total_8=0;$total_9=0;
  $total_10=0;$total_11=0;$total_12=0;$total_13=0;$total_14=0;$total_15=0;

//datos para el titulo del reporte
$mesActual=strtoupper(nameMes(date("m")));
$anioActual=date("Y");
$nombreUnidad="Reporte IBNORCA";


$fechaActual=date("Y-m-d");

  $dbh = new Conexion();
  $codPlanilla=$_GET['codigo_trib'];
  $cod_gestion = $_GET["cod_gestion"];//
  $cod_mes = $_GET["cod_mes"];//
  $nombre_gestion=nameGestion($cod_gestion);  
  $sql = "SELECT pt.*,p.primer_nombre,p.paterno,p.materno,
    (Select i.abreviatura from tipos_identificacion_personal i where i.codigo=p.cod_tipo_identificacion) as tipo_identificacion,p.tipo_identificacion_otro,
    p.identificacion, p.codigo_dependiente
    from planillas_tributarias_personal_mes_2 pt
    join personal p on p.codigo=pt.cod_personal
    where pt.cod_planillatributaria=$codPlanilla";

  $stmtPersonal = $dbh->prepare($sql);
  $stmtPersonal->execute(); 

// echo "mes:".$_GET['cod_mes']."-";
// echo "cod_gestion:".$_GET['cod_gestion']."-";
// echo "codigo_trib:".$_GET['codigo_trib']."-";
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
            '<div id="header_titulo_texto">PLANILLA TRIBUTARIA</div>'.
            
            '<div id="info_izq">
              <h4 >
              <b>NIT:</b>'.obtenerValorConfiguracionEmpresa(4).'<br>
              Mes de declaración: '.$cod_mes.'<br>
              Año de declaración: '.$nombre_gestion.'<br>
              SMN Actual: '.obtenerValorConfiguracionEmpresa(5).'<br>
                                
              </h4> 
            </div>'.
            
         
         '</header><br><br>';
         $html.='<table class="table">'.
            '<thead>'.
            '<tr class="bold table-title text-center">'.
              '<td><small>Año</td> 
              <td><small>Perio.</small></td>                   
              <td><small>Cod. Dependiente RC-IVA</small></td>
              <td><small>Nombres</small></td>
              <td><small>Primer Ape.</small></td>                    
              <td><small>Segundo Ape.</small></td>                    
              <td><small># Doc Identidad</small></td>
              <td><small>Tipo Doc.</small></td>
              <td><small>Nov.</small></td>
              <td><small>Monto De Ingreso Neto</small></td>                                        
              <td><small>Minimo No Imponible</small></td>
              <td><small>Importe Sujeto a Impuesto</small></td>
              <td><small>Impuesto RC-IVA</small></td>                            
              <td><small>(13%)Sal. Mínimo</small></td>
              <td><small>Impuesto Neto RC-IVA</small></td>
              <td><small>F-110</small></td>
              <td><small>Saldo Físico</small></td>     
              <td><small>Saldo Del Dep</small></td>                    
              <td><small>Saldo Del Dep Anterior</small></td>
              <td><small>Mant Saldo Del Dep Anterior</small></td>
              <td><small>Saldo Ant Actu</small></td>
              <td><small>Saldo Utilizado</small></td>
              <td><small>RC-IVA retenido</small></td>
              <td><small>Saldo C.Fiscal Sig.</small></td>';
              /*'<td>Saldo Sig Mes</td>';*/ 

            $html.='</tr>'.
           '</thead>'.
           '<tbody>';
            $index=1;
            $codArea=0;
            $data = obtenerPlanillaTributariaReporte($codPlanilla);
            while ($row = $stmtPersonal->fetch()) 
              {
                $total_1+=$row['monto_ingreso_neto'];
                $total_2+=$row['minimo_no_imponble'];
                $total_3+=$row['importe_sujeto_impuesto_i'];
                $total_4+=$row['impuesto_rc_iva'];
                $total_5+=$row['minimo_13'];
                $total_6+=$row['impuesto_neto_rc_iva'];
                $total_7+=$row['formulario_110_13'];
                $total_8+=$row['saldo_favor_fisico'];
                $total_9+=$row['saldo_favor_dependiente'];
                $total_10+=$row['saldo_mes_anterior'];
                $total_11+=$row['mantenimiento_saldo_mes_anterior'];
                $total_12+=$row['saldo_anterior_actualizado'];
                $total_13+=$row['saldo_utilizado'];
                $total_14+=$row['impuesto_rc_iva_retenido'];
                $total_15+=$row['saldo_credito_fiscal_mes_siguiente'];

             $html.='<tr>'.
                      
                      '<td class="text-center small">'.$nombre_gestion.'</td>'.
                            '<td class="text-left small">'.$cod_mes.'</td>'.
                            '<td class="text-left small">'.$row['codigo_dependiente'].'</td>'.
                            '<td class="text-left small">'.$row['primer_nombre'].'</td>'.
                            '<td class="text-left small">'.$row['paterno'].'</td>'.
                            '<td class="text-left small">'.$row['materno'].'</td>'.
                            '<td class="text-center small">'.$row['identificacion'].'</td>'.
                            '<td class="text-center small">'.$row['tipo_identificacion'].$row['tipo_identificacion_otro'].'</td>'.
                            '<td class="text-center small">V</td>'.
                            '<td class="text-center small">'.formatNumberDec($row['monto_ingreso_neto']).'</td>'.
                            '<td class="text-center small">'.formatNumberDec($row['minimo_no_imponble']).'</td>'.
                            '<td class="small">'.formatNumberDec($row['importe_sujeto_impuesto_i']).'</td>'.
                            '<td class="small">'.formatNumberDec($row['impuesto_rc_iva']).'</td>'.
                            '<td class="small">'.formatNumberDec($row['minimo_13']).'</td>'. 
                            '<td class="small">'.formatNumberDec($row['impuesto_neto_rc_iva']).'</td>'.
                            '<td class="small">'.formatNumberDec($row['formulario_110_13']).'</td>'.
                            '<td class="small">'.formatNumberDec($row['saldo_favor_fisico']).'</td>'.
                            '<td class="text-center small">'.formatNumberDec($row['saldo_favor_dependiente']).'</td>'.
                            '<td class="text-center small">'.formatNumberDec($row['saldo_mes_anterior']).'</td>'.
                            '<td class="text-center small">'.formatNumberDec($row['mantenimiento_saldo_mes_anterior']).'</td>'.
                            '<td class="text-center small">'.formatNumberDec($row['saldo_anterior_actualizado']).'</td>'.
                            '<td class="text-center small">'.formatNumberDec($row['saldo_utilizado']).'</td>'.
                            '<td class="text-center small">'.formatNumberDec($row['impuesto_rc_iva_retenido']).'</td>'.
                            '<td class="text-center small">'.formatNumberDec($row['saldo_credito_fiscal_mes_siguiente']).'</td>';

                    $html.='</tr>';

                 
              }
      $html.='<tr ">'.
                  
                  '<th colspan="9" class="text-center small">Total</th>'.
                  '<th class="text-center small">'.formatNumberDec($total_1).'</th>'.                      
                  '<th class="text-center small">'.formatNumberDec($total_2).'</th>'.
                  '<th class="text-center small">'.formatNumberDec($total_3).'</th>'.                            
                  '<th class="text-center small">'.formatNumberDec($total_4).'</th>'.
                  '<th class="text-center small">'.formatNumberDec($total_5).'</th>'.
                  '<th class="text-center small">'.formatNumberDec($total_6).'</th>'.
                  '<th class="text-center small">'.formatNumberDec($total_7).'</th>'.
                  '<th class="text-center small">'.formatNumberDec($total_8).'</th>'.                      
                  '<th class="text-center small">'.formatNumberDec($total_9).'</th>'.
                  '<th class="text-center small">'.formatNumberDec($total_10).'</th>'.                            
                  '<th class="text-center small">'.formatNumberDec($total_11).'</th>'.
                  '<th class="text-center small">'.formatNumberDec($total_12).'</th>'.
                  '<th class="text-center small">'.formatNumberDec($total_13).'</th>'.
                  '<th class="text-center small">'.formatNumberDec($total_14).'</th>'.
                  '<th class="text-center small">'.formatNumberDec($total_15).'</th>'. 
              '</tr>';

      $html.='</tbody>';
$html.=    '</table>';        
$html.='</body>'.
      '</html>';           
descargarPDFHorizontal("Reporte Planilla Tributaria",$html);
?>
