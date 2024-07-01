<?php
require_once '../conexion.php';
require_once '../functions.php';
require_once '../functionsGeneral.php';
require_once '../functionsPlanillas.php';


// session_start();
set_time_limit(0);

// $cod_mes=$_GET['cod_mes'];
$cod_gestion=$_GET['cod_gestion'];
$codPlanilla=$_GET['codigo_planilla'];
$tipo=$_GET['tipo'];
$cod_mes = $_GET['mes'];
if($cod_mes==1){
  $texto_mes = 'ENERO';
}else if($cod_mes==2){
  $texto_mes = 'FEBRERO';
}else if($cod_mes==3){
  $texto_mes = 'MARZO';
}else if($cod_mes==4){
  $texto_mes = 'ABRIL';
}

//nombre de unidad
$dbh = new Conexion();
// $mes=strtoupper(nombreMes($cod_mes));
$gestion=nameGestion($cod_gestion);

if($tipo==2){ //excel?>
  <meta charset="utf-8">
  <?php
    header("Pragma: public");
    header("Expires: 0");
    $filename = "Planilla Retroactivo ".$gestion.".xls";
    header("Content-type: application/x-msdownload");
    header("Content-Disposition: attachment; filename=$filename");
    header("Pragma: no-cache");
    header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
}
$html = '';
  if($tipo==1){//pdf
    $html.='<head>'.
    '<!-- CSS Files -->'.
    '<link rel="icon" type="image/png" href="../assets/img/favicon.png">'.
    '<link href="../assets/libraries/plantillaPDF_planillas.css" rel="stylesheet" />'.
    '</head>';
  }
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
            '<table width="100%">
              <tr>
              <td width="25%"><p>INSTITUTO BOLIVIANO DE NORMALIZACIÓN Y CALIDAD
                <br>Calle 7 N° 545 Esq. 14 Septiembre, Zona Obrajes
                <br>La Paz - Bolivia</p></td>
              <td><center><span style="font-size: 13px"><b>PLANILLA DE RETROACTIVOS</b></span><BR>CORRESPONDIENTES AL MES DE '.$texto_mes.' '.$gestion.'<br><b>(Expresado en Bolivianos)</b></center></td>
              <td width="25%"><center>NIT:1020745020<br>N° PATRONAL: 925-1-761</center></td>
              </tr>
            </table>'.


         '</header>';
          $html.='<table class="table">'.
            '<thead>'.
            '<tr class="table-title bold text-center">'.
              '<td width="1%"><small><small><small>Nro</small></small></small></td>'.
              '<td width="1%"><small><small><small>Area/Sucursal</small></small></small></td>'.
              '<td width="3%"><small><small><small>CI</small></small></small></td>'.
              '<td width="1%"><small><small><small>Ex.</small></small></small></td>'.
              '<td><small><small><small>Paterno</small></small></small></td>'.
              '<td><small><small><small>Materno</small></small></small></td>'.
              '<td><small><small><small>Nombres</small></small></small></td>'.              
              '<td><small><small><small>Cargo</small></small></small></td>'.
              '<td><small><small><small>Fecha de<br>Ingreso</small></small></small></td>'.
              '<td><small><small><small>Fecha de<br>Retiro</small></small></small></td>'.
              '<td><small><small><small>Haber Basico Inicial</small></small></small></td>'.
              '<td><small><small><small>Bono Antig Inicial</small></small></small></td>'.
              '<td><small><small><small>Haber Basico Nuevo</small></small></small></td>'.
              '<td><small><small><small>Bono Antig Nuevo</small></small></small></td>';
              
              if($cod_mes == 1){
                $html .= '<td><small><small><small>Retroa Enero</small></small></small></td>';
              }
              if($cod_mes == 2){
                $html .= '<td><small><small><small>Retroa Febrero</small></small></small></td>';
              }
              if($cod_mes == 3){
                $html .= '<td><small><small><small>Retroa Marzo</small></small></small></td>';
              }
              if($cod_mes == 4){
                $html .= '<td><small><small><small>Retroa Abril</small></small></small></td>';
              }
              $html .= '<td><small><small><small>Bono Antig</small></small></small></td>'.
                        '<td><small><small><small>Bono Otros</small></small></small></td>';
              
              $html .= '<td><small><small><small>Tota Gana</small></small></small></td>'.
              '<td><small><small><small>Ap. Vejez 10%</small></small></small></td>'.
              '<td><small><small><small>Ries Prof 1.71%</small></small></small></td>'.
              '<td><small><small><small>Com AFP 0.5%</small></small></small></td>'.
              '<td><small><small><small>Ap.Sol 0.5%</small></small></small></td>'.
              '<td><small><small><small>Ap.Sol 13 25 35</small></small></small></td>'.
              '<td><small><small><small>Total Desc</small></small></small></td>'.
              '<td ><small><small><small>Liq Pag</small></small></small></td>';
            $html.='</tr>'.
           '</thead>'.
           '<tbody>';
            $index=1;            
            // $data = obtenerPlanillaSueldosRevision($codPlanilla);

            //INICIAR valores de las sumas
            $subtotal_haber_basico_anterior=0;
            $subtotal_bono_antiguedad_anterior=0;
            $subtotal_haber_basico_nuevo=0;
            $subtotal_bono_antiguedad_nuevo=0;
            $subtotal_retroactivo_enero=0;
            $subtotal_antiguedad_enero=0;
            $subtotal_bonoacademico_enero=0;
            $subtotal_retroactivo_febrero=0;
            $subtotal_antiguedad_febrero=0;
            $subtotal_bonoacademico_febrero=0;
            $subtotal_retroactivo_marzo=0;
            $subtotal_antiguedad_marzo=0;
            $subtotal_bonoacademico_marzo=0;
            $subtotal_retroactivo_abril=0;
            $subtotal_antiguedad_abril=0;
            $subtotal_bonoacademico_abril=0;
            $subtotal_total_ganado=0;
            $subtotal_ap_vejez=0;
            $subtotal_riesgo_prof=0;
            $subtotal_com_afp=0;
            $subtotal_aporte_sol=0;
            $subtotal_total_descuentos=0;
            $subtotal_liquido_pagable=0;
            $subtotal_aporte_sol_13_25_35=0;

            $sql="SELECT p.codigo,prd.correlativo_planilla,a.nombre as area,p.identificacion as ci,p.paterno,p.materno,p.primer_nombre,prd.ing_planilla,
              prd.retiro_planilla,(select c.nombre from cargos c where c.codigo=p.cod_cargo) as cargo,
              (select pd.abreviatura from personal_departamentos pd where pd.codigo=p.cod_lugar_emision) as emision,prd.haber_basico_anterior,prd.haber_basico_nuevo,
              prd.bono_antiguedad_anterior,prd.bono_antiguedad_nuevo,prd.retroactivo_enero,prd.retroactivo_febrero,prd.retroactivo_marzo,prd.retroactivo_abril,
              prd.antiguedad_enero,prd.antiguedad_febrero,prd.antiguedad_marzo,prd.antiguedad_abril,prd.total_ganado,prd.ap_vejez,
              prd.riesgo_prof,prd.com_afp,prd.aporte_sol,prd.total_descuentos,prd.liquido_pagable,prd.bonoacademico_enero,prd.bonoacademico_febrero,
              prd.bonoacademico_marzo,prd.bonoacademico_abril,prd.a_solidario_13_25_35_enero,prd.a_solidario_13_25_35_febrero,prd.a_solidario_13_25_35_marzo,
              prd.a_solidario_13_25_35_abril
              from  personal p join planillas_retroactivos_detalle prd on p.codigo=prd.cod_personal join areas a on prd.cod_area=a.codigo
              where prd.cod_planilla=$codPlanilla
              order by correlativo_planilla";
            // echo $sql;
            $stmt = $dbh->prepare($sql);
            $stmt->execute();
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
              $codigoPersonal=$row['codigo'];
              $area=$row['area'];
              $retiro_planilla=$row['retiro_planilla'];
              if($retiro_planilla>"2022-01-01"){//fecha VALIDA
                $area="PERSONAL RETIRADO";
              }
              // if($retiro_planilla<>null || $retiro_planilla<>""){
                $retiro_planilla= (empty($retiro_planilla) || '0000-00-00' == $retiro_planilla) ? '-' : strftime('%d/%m/%Y',strtotime($retiro_planilla));
              // }

              $html.='<tr>'.
                '<td class="text-center"><small><small><small><small>'.$row['correlativo_planilla'].'</small></small></small></small></td>'.
                '<td class="text-left"><small><small><small><small>'.$area.'</small></small></small></small></td>'.
                '<td class="text-left"><small><small><small><small>'.$row['ci'].'</small></small></small></small></td>'.
                '<td class="text-left"><small><small><small><small>'.$row['emision'].'</small></small></small></small></td>'.
                '<td class="text-left"><small><small><small><small>'.$row['paterno'].'</small></small></small></small></td>'.
                '<td class="text-left"><small><small><small><small>'.$row['materno'].'</small></small></small></small></td>'.
                '<td class="text-left"><small><small><small><small>'.$row['primer_nombre'].'</small></small></small></small></td>'.
                '<td class="text-left"><small><small><small><small>'.$row['cargo'].'</small></small></small></small></td>'.
                '<td class="text-left"><small><small><small><small>'.strftime('%d/%m/%Y',strtotime($row['ing_planilla'])).'</small></small></small></small></td>'.
                '<td class="text-left"><small><small><small><small>'.$retiro_planilla.'</small></small></small></small></td>'.
                
                '<td class="text-right"><small><small><small><small>'.formatNumberDec($row['haber_basico_anterior']).'</small></small></small></small></td>'.
                '<td class="text-right"><small><small><small><small>'.formatNumberDec($row['bono_antiguedad_anterior']).'</small></small></small></small></td>'.
                '<td class="text-right"><small><small><small><small>'.formatNumberDec($row['haber_basico_nuevo']).'</small></small></small></small></td>'.
                '<td class="text-right"><small><small><small><small>'.formatNumberDec($row['bono_antiguedad_nuevo']).'</small></small></small></small></td>';

                $a_solidario_13_25_35 = 0;

                if($cod_mes == 1){
                  $totalGanadoMes=$row['retroactivo_enero'] + $row['antiguedad_enero'] + $row['bonoacademico_enero'];
                  $a_solidario_13_25_35 = $row['a_solidario_13_25_35_enero'];

                  $html .= '<td class="text-right"><small><small><small><small>'.formatNumberDec($row['retroactivo_enero']).'</small></small></small></small></td>'.
                  '<td class="text-right"><small><small><small><small>'.formatNumberDec($row['antiguedad_enero']).'</small></small></small></small></td>'.
                  '<td class="text-right"><small><small><small><small>'.formatNumberDec($row['bonoacademico_enero']).'</small></small></small></small></td>';
                }
                if($cod_mes == 2){                  
                  $totalGanadoMes=$row['retroactivo_febrero'] + $row['antiguedad_febrero'] + $row['bonoacademico_febrero'];
                  $a_solidario_13_25_35 = $row['a_solidario_13_25_35_febrero'];
                  
                  $html .= '<td class="text-right"><small><small><small><small>'.formatNumberDec($row['retroactivo_febrero']).'</small></small></small></small></td>'.
                  '<td class="text-right"><small><small><small><small>'.formatNumberDec($row['antiguedad_febrero']).'</small></small></small></small></td>'.
                  '<td class="text-right"><small><small><small><small>'.formatNumberDec($row['bonoacademico_febrero']).'</small></small></small></small></td>';
                }
                if($cod_mes == 3){                  
                  $totalGanadoMes=$row['retroactivo_marzo'] + $row['antiguedad_marzo'] + $row['bonoacademico_marzo'];
                  $a_solidario_13_25_35 = $row['a_solidario_13_25_35_marzo'];
                  
                  $html .= '<td class="text-right"><small><small><small><small>'.formatNumberDec($row['retroactivo_marzo']).'</small></small></small></small></td>'.
                  '<td class="text-right"><small><small><small><small>'.formatNumberDec($row['antiguedad_marzo']).'</small></small></small></small></td>'.
                  '<td class="text-right"><small><small><small><small>'.formatNumberDec($row['bonoacademico_marzo']).'</small></small></small></small></td>';
                }
                if($cod_mes == 4){                  
                  $totalGanadoMes=$row['retroactivo_abril'] + $row['antiguedad_abril'] + $row['bonoacademico_abril'];
                  $a_solidario_13_25_35 = $row['a_solidario_13_25_35_abril'];
                  
                  $html .= '<td class="text-right"><small><small><small><small>'.formatNumberDec($row['retroactivo_abril']).'</small></small></small></small></td>'.
                  '<td class="text-right"><small><small><small><small>'.formatNumberDec($row['antiguedad_abril']).'</small></small></small></small></td>'.
                  '<td class="text-right"><small><small><small><small>'.formatNumberDec($row['bonoacademico_abril']).'</small></small></small></small></td>';
                }
                $aporteVejez=$totalGanadoMes*10/100;//10%
                //CASO JOSE DURAN 
                if($codigoPersonal==84){
                  $aporteVejez=0;//10%
                }
                $riesgo_prof=$totalGanadoMes*1.71/100;//1.7%
                $com_afp=$totalGanadoMes*0.5/100;//0.5%
                $aporte_sol=$totalGanadoMes*0.5/100;//0.5%

                $totalDescuentosMes=$aporteVejez + $riesgo_prof + $com_afp + $aporte_sol + $a_solidario_13_25_35;

                $liquidoPagableMes = $totalGanadoMes - $totalDescuentosMes;

                $html .= '<td class="text-right"><small><small><small><small>'.formatNumberDec($totalGanadoMes).'</small></small></small></small></td>'.
                '<td class="text-right"><small><small><small><small>'.formatNumberDec($aporteVejez).'</small></small></small></small></td>'.
                '<td class="text-right"><small><small><small><small>'.formatNumberDec($riesgo_prof).'</small></small></small></small></td>'.
                '<td class="text-right"><small><small><small><small>'.formatNumberDec($com_afp).'</small></small></small></small></td>'.
                '<td class="text-right"><small><small><small><small>'.formatNumberDec($aporte_sol).'</small></small></small></small></td>'.
                '<td class="text-right"><small><small><small><small>'.formatNumberDec($a_solidario_13_25_35).'</small></small></small></small></td>'.
                '<td class="text-right"><small><small><small><small>'.formatNumberDec($totalDescuentosMes).'</small></small></small></small></td>'.
                '<td class="text-right"><small><small><small><small>'.formatNumberDec($liquidoPagableMes).'</small></small></small></small></td>';
             
              //suma de totales
              $subtotal_haber_basico_anterior+=$row['haber_basico_anterior'];                  
              $subtotal_bono_antiguedad_anterior+=$row['bono_antiguedad_anterior']; 
              $subtotal_haber_basico_nuevo+=$row['haber_basico_nuevo'];
              $subtotal_bono_antiguedad_nuevo+=$row['bono_antiguedad_nuevo']; 
              $subtotal_retroactivo_enero+=$row['retroactivo_enero']; 
              $subtotal_antiguedad_enero+=$row['antiguedad_enero']; 

              $subtotal_bonoacademico_enero+=$row['bonoacademico_enero']; 

              $subtotal_retroactivo_febrero+=$row['retroactivo_febrero']; 
              $subtotal_antiguedad_febrero+=$row['antiguedad_febrero']; 
              $subtotal_bonoacademico_febrero+=$row['bonoacademico_febrero']; 
              $subtotal_retroactivo_marzo+=$row['retroactivo_marzo']; 
              $subtotal_antiguedad_marzo+=$row['antiguedad_marzo']; 
              $subtotal_bonoacademico_marzo+=$row['bonoacademico_marzo']; 
              $subtotal_retroactivo_abril+=$row['retroactivo_abril']; 
              $subtotal_antiguedad_abril+=$row['antiguedad_abril']; 
              $subtotal_bonoacademico_abril+=$row['bonoacademico_abril']; 
              $subtotal_total_ganado+=$row['total_ganado'];
              
              
              $subtotal_ap_vejez+=$aporteVejez; 
              $subtotal_riesgo_prof+=$riesgo_prof; 
              $subtotal_com_afp+=$com_afp; 
              $subtotal_aporte_sol+=$aporte_sol; 
              $subtotal_aporte_sol_13_25_35+=$a_solidario_13_25_35;

              $subtotal_total_descuentos+=$totalDescuentosMes; 
              $subtotal_liquido_pagable+=$liquidoPagableMes;  
              $index++;
            }
      $html.='</tbody>';
      $html.='<tfoot><tr>'.
          '<td  colspan="10" class="text-right"><small><small><small><small><small><b>TOTAL</b></small></small></small></small></small></td>'.
          
          '<td  class="text-right"><small><small><small><small><small><b>'.formatNumberDec($subtotal_haber_basico_anterior).'</b></small></small></small></small></small></td>'.
          '<td  class="text-right"><small><small><small><small><small><b>'.formatNumberDec($subtotal_bono_antiguedad_anterior).'</b></small></small></small></small></small></td>'.
          '<td  class="text-right"><small><small><small><small><small><b>'.formatNumberDec($subtotal_haber_basico_nuevo).'</b></small></small></small></small></small></td>'.
          '<td  class="text-right"><small><small><small><small><small><b>'.formatNumberDec($subtotal_bono_antiguedad_nuevo).'</b></small></small></small></small></small></td>';
          if($cod_mes == 1){
            $html .= '<td  class="text-right"><small><small><small><small><small><b>'.formatNumberDec($subtotal_retroactivo_enero).'</b></small></small></small></small></small></td>'.
            '<td  class="text-right"><small><small><small><small><small><b>'.formatNumberDec($subtotal_antiguedad_enero).'</b></small></small></small></small></small></td>'.
            '<td  class="text-right"><small><small><small><small><small><b>'.formatNumberDec($subtotal_bonoacademico_enero).'</b></small></small></small></small></small></td>';
          }
          if($cod_mes == 2){
            $html .= '<td  class="text-right"><small><small><small><small><small><b>'.formatNumberDec($subtotal_retroactivo_febrero).'</b></small></small></small></small></small></td>'.
            '<td  class="text-right"><small><small><small><small><small><b>'.formatNumberDec($subtotal_antiguedad_febrero).'</b></small></small></small></small></small></td>'.
            '<td  class="text-right"><small><small><small><small><small><b>'.formatNumberDec($subtotal_bonoacademico_febrero).'</b></small></small></small></small></small></td>';
          }
          if($cod_mes == 3){
            $html .= '<td  class="text-right"><small><small><small><small><small><b>'.formatNumberDec($subtotal_retroactivo_marzo).'</b></small></small></small></small></small></td>'.
            '<td  class="text-right"><small><small><small><small><small><b>'.formatNumberDec($subtotal_antiguedad_marzo).'</b></small></small></small></small></small></td>'.
            '<td  class="text-right"><small><small><small><small><small><b>'.formatNumberDec($subtotal_bonoacademico_marzo).'</b></small></small></small></small></small></td>';
          }
          if($cod_mes == 4){
            $html .= '<td  class="text-right"><small><small><small><small><small><b>'.formatNumberDec($subtotal_retroactivo_abril).'</b></small></small></small></small></small></td>'.
            '<td  class="text-right"><small><small><small><small><small><b>'.formatNumberDec($subtotal_antiguedad_abril).'</b></small></small></small></small></small></td>'.
            '<td  class="text-right"><small><small><small><small><small><b>'.formatNumberDec($subtotal_bonoacademico_abril).'</b></small></small></small></small></small></td>';
          }
          $html .= '<td  class="text-right"><small><small><small><small><small><b>'.formatNumberDec($subtotal_total_ganado).'</b></small></small></small></small></small></td>'.
          '<td  class="text-right"><small><small><small><small><small><b>'.formatNumberDec($subtotal_ap_vejez).'</b></small></small></small></small></small></td>'.
          '<td  class="text-right"><small><small><small><small><small><b>'.formatNumberDec($subtotal_riesgo_prof).'</b></small></small></small></small></small></td>'.              
          '<td  class="text-right"><small><small><small><small><small><b>'.formatNumberDec($subtotal_com_afp).'</b></small></small></small></small></small></td>'.
          '<td  class="text-right"><small><small><small><small><small><b>'.formatNumberDec($subtotal_aporte_sol).'</b></small></small></small></small></small></td>'.
          '<td  class="text-right"><small><small><small><small><small><b>'.formatNumberDec($subtotal_aporte_sol_13_25_35).'</b></small></small></small></small></small></td>'.
          '<td  class="text-right"><small><small><small><small><small><b>'.formatNumberDec($subtotal_total_descuentos).'</b></small></small></small></small></small></td>'.
          '<td  class="text-right"><small><small><small><small><small><b>'.formatNumberDec($subtotal_liquido_pagable).'</b></small></small></small></small></small></td>'.
          
          '</tr>

      </tfoot>';
$html.='</table><br><br><br>';        

$html.='<table width="100%">
  <tr >
  <td width="25%"><center><p>______________________________<br>'.obtenerValorConfiguracionPlanillas(26).'<BR>JEFE DE RECURSOS HUMANOS </p></center></td>
  <td><center><p>______________________________<BR>'.obtenerValorConfiguracionPlanillas(24).'<BR>GERENTE GENERAL </p></center></td>
  <td width="25%"><center><p>______________________________<BR>'.obtenerValorConfiguracionPlanillas(25).'<BR>REPRESENTANTE LEGAL </p></center></td>
  </tr>
</table>';

$html.='</body>'.
      '</html>';           

$dbh=null;
$stmtBonos=null;
$stmtDescuento=null;
$stmtBonosOtrs=null;
$stmtDescuentos=null;

 // echo $html;

if($tipo==2){
  echo $html;
}else{
  // descargarPDFHorizontal("PLANILLA RETROACTIVO ".$gestion,$html);
  echo $html;
}


?>
