<?php
require_once '../conexion.php';
require_once '../functions.php';
require_once '../functionsGeneral.php';
// session_start();
set_time_limit(0);

// $cod_mes=$_GET['cod_mes'];
$cod_gestion=$_GET['cod_gestion'];
$codPlanilla=$_GET['codigo_planilla'];
$tipo=$_GET['tipo'];

//nombre de unidad
$dbh = new Conexion();
// $mes=strtoupper(nombreMes($cod_mes));
$gestion=nameGestion($cod_gestion);


if($tipo==1){//CNS
  $sql_add=" and p.cod_cajasalud=1";
}elseif($tipo==2){//CPS
  $sql_add=" and p.cod_cajasalud=2";
}

  $mes=strtoupper(nombreMes($cod_mes));
  $gestion=nameGestion($cod_gestion);

$fecha_x=$gestion.'-05-01';
$fecha_cns=date("Y-m-t",strtotime($fecha_x."+ 1 month")); 
$datos_fecha=explode("-", $fecha_cns);
//sacamos el ultimo día habil
$dia_ultimo=$datos_fecha[2];
$dia_semana=date("N",strtotime($fecha_cns)); 
if ($dia_semana == 7) {
    $dia_ultimo=$dia_ultimo-2;
}
if ($dia_semana == 6) {
    $dia_ultimo=$dia_ultimo-1;
}



$html = '';
    $html.='<head>'.
    '<!-- CSS Files -->'.
    '<link rel="icon" type="image/png" href="../assets/img/favicon.png">'.
    '<link href="../assets/libraries/plantillaPDF_planillas.css" rel="stylesheet" />'.
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
            '<table width="100%">
             

              <tr>
              <td width="50%"><span style="font-size: 15px;">CORPORACION BOLIVIANA DE FARMACIAS S.A.</span><br>NIT:1022039027<br>Av.Landaeta Nro. 836<br>La Paz - Bolivia<br>N° Patronal C.N.S. 01 - 652 - 00289</td>
              <td><span style="font-size: 15px">PLANILLA CORRESPONDIENTE A LOS MESES DE ENERO A ABRIL  '.$gestion.'</span><br>EXPRESADO EN BOLIVIANOS<BR>S.S. LARGO PLAZO<BR>TELF.: 2 - 413051</td>
              </tr>

            </table>'.
         '</header>';
          $html.='<table class="table">'.
            '<thead>'.
            '<tr class="table-title bold text-center">'.
              '<td width="1%"><small><small><small>Nro</small></small></small></td>'.
              
              '<td width="3%"><small><small><small>CI</small></small></small></td>'.
              '<td width="1%"><small><small><small>Lugar de Emision</small></small></small></td>'.

              '<td><small><small><small>Paterno</small></small></small></td>'.
              '<td><small><small><small>Materno</small></small></small></td>'.
              '<td><small><small><small>Nombres</small></small></small></td>'.     
              '<td><small><small><small>Nacion<br>alidad</small></small></small></td>'.
              '<td><small><small><small>Fecha Nac.</small></small></small></td>'.
              '<td><small><small><small>Cargo</small></small></small></td>'.
              '<td><small><small><small>Fecha Ing R.A. INASES 129/2016</small></small></small></td>'.

              // '<td><small><small><small>Fecha de<br>Retiro</small></small></small></td>'.
              '<td><small><small><small>Haber Basico Inicial</small></small></small></td>'.
              '<td><small><small><small>Bono Antig Inicial</small></small></small></td>'.
              '<td><small><small><small>Haber Basico Nuevo</small></small></small></td>'.
              '<td><small><small><small>Bono Antig Nuevo</small></small></small></td>'.
              
              '<td><small><small><small>Retroa Enero</small></small></small></td>'.
              '<td><small><small><small>Bono Antig</small></small></small></td>'.
              '<td><small><small><small>Retroa Febrero</small></small></small></td>'.
              '<td><small><small><small>Bono Antig</small></small></small></td>'.
              '<td><small><small><small>Retroa Marzo</small></small></small></td>'.
              '<td><small><small><small>Bono Antig</small></small></small></td>'.
              '<td><small><small><small>Retroa Abril</small></small></small></td>'.
              '<td><small><small><small>Bono Antig</small></small></small></td>'.
              
              '<td><small><small><small>Tota Gana</small></small></small></td>'.
              '<td><small><small><small>Ap. Vejez 10%</small></small></small></td>'.
              '<td><small><small><small>Ries Prof 1.71%</small></small></small></td>'.
              '<td><small><small><small>Com AFP 0.5%</small></small></small></td>'.
              '<td><small><small><small>Ap.Sol 0.5%</small></small></small></td>'.
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
            $subtotal_retroactivo_febrero=0;
            $subtotal_antiguedad_febrero=0;
            $subtotal_retroactivo_marzo=0;
            $subtotal_antiguedad_marzo=0;
            $subtotal_retroactivo_abril=0;
            $subtotal_antiguedad_abril=0;
            $subtotal_total_ganado=0;
            $subtotal_ap_vejez=0;
            $subtotal_riesgo_prof=0;
            $subtotal_com_afp=0;
            $subtotal_aporte_sol=0;
            $subtotal_total_descuentos=0;
            $subtotal_liquido_pagable=0;

            $sql="SELECT p.codigo,prd.correlativo_planilla,a.nombre as area,p.identificacion as ci,p.paterno,p.materno,p.primer_nombre,p.fecha_nacimiento,prd.ing_planilla,prd.retiro_planilla,(select c.nombre from cargos c where c.codigo=p.cod_cargo) as cargo,(select pd.abreviatura from personal_departamentos pd where pd.codigo=p.cod_lugar_emision) as emision,prd.haber_basico_anterior,prd.haber_basico_nuevo,prd.bono_antiguedad_anterior,prd.bono_antiguedad_nuevo,prd.retroactivo_enero,prd.retroactivo_febrero,prd.retroactivo_marzo,prd.retroactivo_abril,prd.antiguedad_enero,prd.antiguedad_febrero,prd.antiguedad_marzo,prd.antiguedad_abril,prd.total_ganado,prd.ap_vejez,prd.riesgo_prof,prd.com_afp,prd.aporte_sol,prd.total_descuentos,prd.liquido_pagable
              from  personal p join planillas_retroactivos_detalle prd on p.codigo=prd.cod_personal join areas a on prd.cod_area=a.codigo
              where prd.cod_planilla=$codPlanilla $sql_add
              order by correlativo_planilla";
            // echo $sql;
            $stmt = $dbh->prepare($sql);
            $stmt->execute();
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
              
              $area=$row['area'];
              $retiro_planilla=$row['retiro_planilla'];
              if($retiro_planilla>"2022-01-01"){//fecha VALIDA
                $area="PERSONAL RETIRADO";
              }
              // if($retiro_planilla<>null || $retiro_planilla<>""){
                $retiro_planilla=strftime('%d/%m/%Y',strtotime($retiro_planilla));
              // }

              $html.='<tr>'.
                '<td class="text-center"><small><small><small><small>'.$index.'</small></small></small></small></td>'.
                
                '<td class="text-left"><small><small><small><small>'.$row['ci'].'</small></small></small></small></td>'.
                '<td class="text-left"><small><small><small><small>'.$row['emision'].'</small></small></small></small></td>'.
                '<td class="text-left"><small><small><small><small>'.$row['paterno'].'</small></small></small></small></td>'.
                '<td class="text-left"><small><small><small><small>'.$row['materno'].'</small></small></small></small></td>'.
                '<td class="text-left"><small><small><small><small>'.$row['primer_nombre'].'</small></small></small></small></td>'.
                '<td class="text-left"><small><small><small><small>BOLIVIANA</small></small></small></small></td>'.
                '<td class="text-left"><small><small><small><small>'.strftime("%d/%m/%Y",strtotime($row['fecha_nacimiento'])).'</small></small></small></small></td>'.
                '<td class="text-left"><small><small><small><small>'.$row['cargo'].'</small></small></small></small></td>'.
                '<td class="text-center"><small><small><small><small>'.strftime('%d/%m/%Y',strtotime($row['ing_planilla'])).'</small></small></small></small></td>'.
                // '<td class="text-left"><small><small><small><small>'.$retiro_planilla.'</small></small></small></small></td>'.
                
                '<td class="text-right"><small><small><small><small>'.formatNumberDec($row['haber_basico_anterior']).'</small></small></small></small></td>'.
                '<td class="text-right"><small><small><small><small>'.formatNumberDec($row['bono_antiguedad_anterior']).'</small></small></small></small></td>'.
                '<td class="text-right"><small><small><small><small>'.formatNumberDec($row['haber_basico_nuevo']).'</small></small></small></small></td>'.
                '<td class="text-right"><small><small><small><small>'.formatNumberDec($row['bono_antiguedad_nuevo']).'</small></small></small></small></td>'.
                '<td class="text-right"><small><small><small><small>'.formatNumberDec($row['retroactivo_enero']).'</small></small></small></small></td>'.
                '<td class="text-right"><small><small><small><small>'.formatNumberDec($row['antiguedad_enero']).'</small></small></small></small></td>'.
                '<td class="text-right"><small><small><small><small>'.formatNumberDec($row['retroactivo_febrero']).'</small></small></small></small></td>'.
                '<td class="text-right"><small><small><small><small>'.formatNumberDec($row['antiguedad_febrero']).'</small></small></small></small></td>'.
                '<td class="text-right"><small><small><small><small>'.formatNumberDec($row['retroactivo_marzo']).'</small></small></small></small></td>'.
                '<td class="text-right"><small><small><small><small>'.formatNumberDec($row['antiguedad_marzo']).'</small></small></small></small></td>'.
                '<td class="text-right"><small><small><small><small>'.formatNumberDec($row['retroactivo_abril']).'</small></small></small></small></td>'.
                '<td class="text-right"><small><small><small><small>'.formatNumberDec($row['antiguedad_abril']).'</small></small></small></small></td>'.
                '<td class="text-right"><small><small><small><small>'.formatNumberDec($row['total_ganado']).'</small></small></small></small></td>'.
                '<td class="text-right"><small><small><small><small>'.formatNumberDec($row['ap_vejez']).'</small></small></small></small></td>'.
                '<td class="text-right"><small><small><small><small>'.formatNumberDec($row['riesgo_prof']).'</small></small></small></small></td>'.
                '<td class="text-right"><small><small><small><small>'.formatNumberDec($row['com_afp']).'</small></small></small></small></td>'.
                '<td class="text-right"><small><small><small><small>'.formatNumberDec($row['aporte_sol']).'</small></small></small></small></td>'.
                '<td class="text-right"><small><small><small><small>'.formatNumberDec($row['total_descuentos']).'</small></small></small></small></td>'.
                '<td class="text-right"><small><small><small><small>'.formatNumberDec($row['liquido_pagable']).'</small></small></small></small></td>';
             
              //suma de totales
              $subtotal_haber_basico_anterior+=$row['haber_basico_anterior'];                  
              $subtotal_bono_antiguedad_anterior+=$row['bono_antiguedad_anterior']; 
              $subtotal_haber_basico_nuevo+=$row['haber_basico_nuevo'];
              $subtotal_bono_antiguedad_nuevo+=$row['bono_antiguedad_nuevo']; 
              $subtotal_retroactivo_enero+=$row['retroactivo_enero']; 
              $subtotal_antiguedad_enero+=$row['antiguedad_enero']; 
              $subtotal_retroactivo_febrero+=$row['retroactivo_febrero']; 
              $subtotal_antiguedad_febrero+=$row['antiguedad_febrero']; 
              $subtotal_retroactivo_marzo+=$row['retroactivo_marzo']; 
              $subtotal_antiguedad_marzo+=$row['antiguedad_marzo']; 
              $subtotal_retroactivo_abril+=$row['retroactivo_abril']; 
              $subtotal_antiguedad_abril+=$row['antiguedad_abril']; 
              $subtotal_total_ganado+=$row['total_ganado']; 
              $subtotal_ap_vejez+=$row['ap_vejez']; 
              $subtotal_riesgo_prof+=$row['riesgo_prof']; 
              $subtotal_com_afp+=$row['com_afp']; 
              $subtotal_aporte_sol+=$row['aporte_sol']; 
              $subtotal_total_descuentos+=$row['total_descuentos']; 
              $subtotal_liquido_pagable+=$row['liquido_pagable']; 
              $index++;
            }
      $html.='</tbody>';
      $html.='<tfoot><tr>'.
          '<td  colspan="10" class="text-right"><small><small><small><small><small><b>TOTAL</b></small></small></small></small></small></td>'.
          
          '<td  class="text-right"><small><small><small><small><small><b>'.formatNumberDec($subtotal_haber_basico_anterior).'</b></small></small></small></small></small></td>'.
          '<td  class="text-right"><small><small><small><small><small><b>'.formatNumberDec($subtotal_bono_antiguedad_anterior).'</b></small></small></small></small></small></td>'.
          '<td  class="text-right"><small><small><small><small><small><b>'.formatNumberDec($subtotal_haber_basico_nuevo).'</b></small></small></small></small></small></td>'.
          '<td  class="text-right"><small><small><small><small><small><b>'.formatNumberDec($subtotal_bono_antiguedad_nuevo).'</b></small></small></small></small></small></td>'.
          '<td  class="text-right"><small><small><small><small><small><b>'.formatNumberDec($subtotal_retroactivo_enero).'</b></small></small></small></small></small></td>'.
          '<td  class="text-right"><small><small><small><small><small><b>'.formatNumberDec($subtotal_antiguedad_enero).'</b></small></small></small></small></small></td>'.
          '<td  class="text-right"><small><small><small><small><small><b>'.formatNumberDec($subtotal_retroactivo_febrero).'</b></small></small></small></small></small></td>'.
          '<td  class="text-right"><small><small><small><small><small><b>'.formatNumberDec($subtotal_antiguedad_febrero).'</b></small></small></small></small></small></td>'.
          '<td  class="text-right"><small><small><small><small><small><b>'.formatNumberDec($subtotal_retroactivo_marzo).'</b></small></small></small></small></small></td>'.
          '<td  class="text-right"><small><small><small><small><small><b>'.formatNumberDec($subtotal_antiguedad_marzo).'</b></small></small></small></small></small></td>'.
          '<td  class="text-right"><small><small><small><small><small><b>'.formatNumberDec($subtotal_retroactivo_abril).'</b></small></small></small></small></small></td>'.
          '<td  class="text-right"><small><small><small><small><small><b>'.formatNumberDec($subtotal_antiguedad_abril).'</b></small></small></small></small></small></td>'.
          '<td  class="text-right"><small><small><small><small><small><b>'.formatNumberDec($subtotal_total_ganado).'</b></small></small></small></small></small></td>'.
          '<td  class="text-right"><small><small><small><small><small><b>'.formatNumberDec($subtotal_ap_vejez).'</b></small></small></small></small></small></td>'.
          '<td  class="text-right"><small><small><small><small><small><b>'.formatNumberDec($subtotal_riesgo_prof).'</b></small></small></small></small></small></td>'.              
          '<td  class="text-right"><small><small><small><small><small><b>'.formatNumberDec($subtotal_com_afp).'</b></small></small></small></small></small></td>'.
          '<td  class="text-right"><small><small><small><small><small><b>'.formatNumberDec($subtotal_aporte_sol).'</b></small></small></small></small></small></td>'.
          '<td  class="text-right"><small><small><small><small><small><b>'.formatNumberDec($subtotal_total_descuentos).'</b></small></small></small></small></small></td>'.
          '<td  class="text-right"><small><small><small><small><small><b>'.formatNumberDec($subtotal_liquido_pagable).'</b></small></small></small></small></small></td>'.
          '</tr>

      </tfoot>';
$html.='</table><br><br><br>';        

$html.='<table width="100%">
  <tr >
  <td width="25%"><center><p>______________________________<BR>'.obtenerValorConfiguracionPlanillas(25).'<BR>REPRESENTANTE LEGAL COBOFAR S.A.</p></center></td>
  <td><center><p>SELLO</p></center></td>
  <td width="25%"><center><p>LA PAZ, '.$dia_ultimo.' DE '.strtoupper(nombreMes($datos_fecha[1])).' DE '.$datos_fecha[0].'</p></center></td>
  </tr>
</table>';

$html.='</body>'.
      '</html>';           

$dbh=null;
$stmtBonos=null;
$stmtDescuento=null;
$stmtBonosOtrs=null;
$stmtDescuentos=null;

descargarPDFHorizontal("PLANILLA RETROACTIVO ".$gestion,$html);



?>
