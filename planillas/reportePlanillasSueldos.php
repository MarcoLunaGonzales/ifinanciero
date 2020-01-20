<?php
require_once '../conexion.php';
require_once '../functions.php';

session_start();
setlocale(LC_TIME, "Spanish");
//INICIAR valores de las sumas
$total1=0;$total2=0;$total3=0;$total4=0;$total5=0;$total6=0;
$total_bonos_otros=0;$total_afp1=0; $total_afp2=0;
$total_aporteSolidario=0;$total_rc_iva=0;$total_otros_descuentos=0;
$total_anticipos=0;

//datos para el titulo del reporte
$mesActual=strtoupper(nameMes(date("m")));
$anioActual=date("Y");
// $nombreUnidad="Reporte IBNORCA";


$fechaActual=date("Y-m-d");
$cod_mes=$_GET['cod_mes'];
$cod_gestion=$_GET['cod_gestion'];
$codPlanilla=$_GET['codigo_planilla'];
$cod_uo = $_GET["codigo_uo"];//
//nombre de unidad
$dbh = new Conexion();
$mes=strtoupper(nameMes($cod_mes));
$gestion=nameGestion($cod_gestion);
$nombreUnidad= nameUnidad($cod_uo);
$stmtArea = $dbh->prepare("SELECT cod_area,(SELECT a.abreviatura from areas a where a.codigo=cod_area) as nombre_area
  from personal_area_distribucion
  where cod_estadoreferencial=1 and cod_uo=$cod_uo
  GROUP BY cod_area order by nombre_area");
$stmtArea->execute();
$stmtArea->bindColumn('cod_area', $cod_area_x);
$stmtArea->bindColumn('nombre_area', $nombre_area_x);

$dias_trabajados_asistencia=30;//ver datos

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
            '<div id="header_titulo_texto">PLANILLA DE SUELDOS PERIODO '.$mes.' '.$gestion.'</div>'.
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
              '<td>Bono de Antig</td>'.
              '<td>Otros Bonos</td>'.
              '<td>Total ganado</td>'.
              '<td>Futuro de Bolivia</td>'.
              '<td>Previsión BBVA</td>'.
              '<td>Aporte Solidario > a 13000</td>'.
              '<td>RC-IVA</td>'.              
              '<td>Otros Desc</td>'.
              '<td>Antic.</td>'.
              '<td>Total Desc</td>'.
              '<td>Liquido Pagable</td>'; 

            $html.='</tr>'.
           '</thead>'.
           '<tbody>';
            $index=1;
            $codArea=0;
            while ($rowArea = $stmtArea->fetch(PDO::FETCH_BOUND)) 
            {
            $data = obtenerPlanillaSueldosRevision($codPlanilla,$cod_area_x,$cod_uo);
            while ($row = $data->fetch(PDO::FETCH_ASSOC)) {
              $monto_bonos_otros=obtenerPlanillaSueldoRevisionBonos($row['codigo'],$cod_gestion,$cod_mes,$dias_trabajados_asistencia,$row['dias_trabajados']);
              $cod_personal_cargo=$row['codigo'];
              $porcentaje=$row['porcentaje'];
              $sql = "SELECT * from planillas_personal_mes_patronal 
              where cod_planilla=$codPlanilla and cod_personal_cargo=$cod_personal_cargo";
              $stmtPersonalPatronal = $dbh->prepare($sql);
              $stmtPersonalPatronal->execute();
              $resultPatronal=$stmtPersonalPatronal->fetch();
              $a_solidario_13000=$resultPatronal['a_solidario_13000'];
              $a_solidario_25000=$resultPatronal['a_solidario_25000'];
              $rc_iva=$resultPatronal['rc_iva'];
              $atrasos=$resultPatronal['atrasos'];
              $anticipo=$resultPatronal['anticipo'];
              
              $sqlTotalOtroDescuentos = "SELECT SUM(monto) as suma_descuentos
                      from descuentos_personal_mes 
                      where  cod_personal=$cod_personal_cargo and cod_gestion=$cod_gestion and cod_mes=$cod_mes and cod_estadoreferencial=1";
              $stmtDescuentosOtros = $dbh->prepare($sqlTotalOtroDescuentos);
              $stmtDescuentosOtros->execute();
              $resultDescuentosOtros=$stmtDescuentosOtros->fetch();
              $sumaDescuentos_otros=$resultDescuentosOtros['suma_descuentos'];
              if($codArea!=$row['cod_area']){
               $html.='<tr>'.
                      '<td colspan="21">'.$row['area'].'</td>';                                  
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
                      '<td class="text-right">'.number_format($row['haber_basico']*$porcentaje/100, 2, '.', ',').'</td>'.
                      '<td class="text-right">'.$row['dias_trabajados'].'</td>'.
                      '<td class="text-right">'.number_format($row['bono_antiguedad']*$porcentaje/100, 2, '.', ',').'</td>'.
                      '<td class="text-right">'.number_format($monto_bonos_otros*$porcentaje/100, 2, '.', ',').'</td>'.
                      '<td class="text-right">'.number_format($row['total_ganado']*$porcentaje/100, 2, '.', ',').'</td>'.
                      '<td class="text-right">'.number_format($row['afp_1']*$porcentaje/100, 2, '.', ',').'</td>'.
                      '<td class="text-right">'.number_format($row['afp_2']*$porcentaje/100, 2, '.', ',').'</td>'.
                      '<td class="text-right">'.number_format(($a_solidario_13000+$a_solidario_25000)*$porcentaje/100, 2, '.', ',').'</td>'.
                      '<td class="text-right">'.number_format($rc_iva*$porcentaje/100, 2, '.', ',').'</td>'.                      
                      '<td class="text-right">'.number_format($sumaDescuentos_otros*$porcentaje/100, 2, '.', ',').'</td>'.
                      '<td class="text-right">'.number_format($anticipo*$porcentaje/100, 2, '.', ',').'</td>'.
                      '<td class="text-right">'.number_format($row['monto_descuentos']*$porcentaje/100, 2, '.', ',').'</td>'.
                      '<td class="text-right">'.number_format($row['liquido_pagable']*$porcentaje/100, 2, '.', ',').'</td>';                    
                    $html.='</tr>';
                  //suma de totales
                  $total1+=$row['haber_basico']*$porcentaje/100;                  
                  $total3+=$row['bono_antiguedad']*$porcentaje/100; 
                  $total_bonos_otros +=($monto_bonos_otros)*$porcentaje/100;
                  $total4+=$row['total_ganado']*$porcentaje/100;
                  $total_afp1+=$row['afp_1']*$porcentaje/100; 
                  $total_afp2+=$row['afp_2']*$porcentaje/100; 
                  $total_aporteSolidario+=($a_solidario_13000+$a_solidario_25000+$a_solidario_35000)*$porcentaje/100; 
                  $total_rc_iva+=$rc_iva*$porcentaje/100;              
                  $total_otros_descuentos+=($sumaDescuentos_otros+$atrasos)*$porcentaje/100;
                  $total_anticipos+=$anticipo*$porcentaje/100;
                  $total5+=$row['monto_descuentos']*$porcentaje/100; 
                  $total6+=$row['liquido_pagable']*$porcentaje/100; 
              }
            }
      $html.='<tr class="bold table-title">'.
                  '<td colspan="8" class="text-center">Sumas:</td>'.
                  '<td class="text-right">'.number_format($total1, 2, '.', ',').'</td>'.
                  '<td></td>'.
                  '<td class="text-right">'.number_format($total3, 2, '.', ',').'</td>'.
                  '<td class="text-right">'.number_format($total_bonos_otros, 2, '.', ',').'</td>'.
                  '<td class="text-right">'.number_format($total4, 2, '.', ',').'</td>'.
                  '<td class="text-right">'.number_format($total_afp1, 2, '.', ',').'</td>'.
                  '<td class="text-right">'.number_format($total_afp2, 2, '.', ',').'</td>'.
                  '<td class="text-right">'.number_format($total_aporteSolidario, 2, '.', ',').'</td>'.
                  '<td class="text-right">'.number_format($total_rc_iva, 2, '.', ',').'</td>'.
                  '<td class="text-right">'.number_format($total_otros_descuentos, 2, '.', ',').'</td>'.
                  '<td class="text-right">'.number_format($total_anticipos, 2, '.', ',').'</td>'.
                  '<td class="text-right">'.number_format($total5, 2, '.', ',').'</td>'.
                  '<td class="text-right">'.number_format($total6, 2, '.', ',').'</td>';                  
      $html.='</tr>';
              '</tbody>';
$html.=    '</table>';        
$html.='</body>'.
      '</html>';           
descargarPDFHorizontal("prueba Reporte Planilla",$html);
?>
