<script> periodo_mayor='<?=$periodoTitle?>';
          cuenta_mayor='<?=trim($nombreCuentaTitle)?>';
          unidad_mayor='<?=$unidadGeneral?>';
 </script>

 <div class="content">
  <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
              <div class="card">
                <div class="card-header <?=$colorCard;?> card-header-icon">
                  <div class="card-icon bg-blanco">
                    <img class="" width="40" height="40" src="../assets/img/logoibnorca.png">
                  </div>
                  <div class="float-right col-sm-2"><h6 class="card-title">Exportar como:</h6></div>
                  <h4 class="card-title text-center">Reporte Libro Mayor</h4>
                  <h6 class="card-title">Periodo: <?=$periodoTitle?></h6>
                  <h6 class="card-title">Cuenta: <?=$nombreCuentaTitle;?></h6>
                  <h6 class="card-title">Oficina Origen Comprobante:<?=$unidadGeneral?></h6>
                  <div class="row">
                    <div class="col-sm-6"><h5 class="card-title"><b>Centro de Costo - Oficina: </b> <small><?=$unidadAbrev?></small></h6></div>
                    <div class="col-sm-6"><h5 class="card-title"><b>Centro de Costo - Area: </b> <small><?=$areaAbrev?></small></h6></div>
                  </div> 
                </div>
                <div class="card-body">
                  <div class="table-responsive">
     <?php
    $html='<table id="libro_mayor_rep" class="table table-bordered table-condensed" style="width:100%">'.
            '<thead >'.
            '<tr class="text-center">'.
              '<th colspan="5" class=""></th>'.
              // '<th colspan="3" class="">Bolivianos</th>'.
              '<th colspan="3" class="">'.$nombreMoneda.'</th>'.
            '</tr>'.
            '<tr class="text-center">'.
              //'<th>Entidad</th>'.
              '<th width="5%">Of/Area</th>'.
              '<th width="5%">Cbte</th>'.
              '<th width="7%">Fecha</th>'.
              '<th width="60%">Concepto</th>'.
              '<th width="3%">t/c</th>'.
              // '<th>Debe</th>'.
              // '<th>Haber</th>'.
              // '<th>Saldos</th>'.
              '<th width="5%">Debe</th>'.
              '<th width="5%">Haber</th>'.
              '<th width="5%">Saldos</th>'.
            '</tr>'.
           '</thead>'.
           '<tbody>';

for ($xx=0; $xx < cantidadF($codcuentaAux); $xx++) { 
  $porciones = explode("@", $codcuentaAux[$xx]);
  $cuenta=$porciones[0];
  if($porciones[1]=="aux"){
    $nombreCuenta=nameCuentaAux($cuenta);

    $query1="SELECT d.codigo as cod_det,d.cod_area,d.cod_unidadorganizacional,d.glosa,d.debe,d.haber, p.codigo,p.nro_cuenta,p.nombre,d.cod_cuentaauxiliar, u.abreviatura,a.abreviatura as areaAbrev, c.cod_unidadorganizacional as unidad,c.fecha, (c.codigo) as codigo_comprobante
    FROM cuentas_auxiliares p 
    join comprobantes_detalle d on p.codigo=d.cod_cuentaauxiliar 
    join areas a on d.cod_area=a.codigo 
    join unidades_organizacionales u on u.codigo=d.cod_unidadorganizacional 
    join comprobantes c on d.cod_comprobante=c.codigo
    where c.cod_gestion=$NombreGestion and p.codigo=$cuenta and c.cod_estadocomprobante<>2 and c.fecha BETWEEN '$desde 00:00:00' and '$hasta 23:59:59' and d.cod_unidadorganizacional in ($unidadCostoArray) and d.cod_area in ($areaCostoArray) and c.cod_unidadorganizacional in ($unidadArray) order by c.fecha";
  }else{
    $nombreCuenta=nameCuenta($cuenta);

    $query1="SELECT d.codigo as cod_det,d.cod_area,d.cod_unidadorganizacional,d.glosa,d.debe,d.haber,
      p.codigo,p.numero,p.nombre,d.cod_cuentaauxiliar,
      u.abreviatura,a.abreviatura as areaAbrev,
      c.cod_unidadorganizacional as unidad,c.fecha, (c.codigo) as codigo_comprobante
      FROM plan_cuentas p 
      join comprobantes_detalle d on p.codigo=d.cod_cuenta 
      join areas a on d.cod_area=a.codigo 
      join unidades_organizacionales u on u.codigo=d.cod_unidadorganizacional 
      join comprobantes c on d.cod_comprobante=c.codigo
      where c.cod_gestion=$NombreGestion and p.codigo=$cuenta and c.cod_estadocomprobante<>2 and c.fecha BETWEEN '$desde 00:00:00' and '$hasta 23:59:59' and d.cod_unidadorganizacional in ($unidadCostoArray) and d.cod_area in ($areaCostoArray) and c.cod_unidadorganizacional in ($unidadArray) order by c.fecha";
  }

  //echo $query1;

  $stmt = $dbh->prepare($query1);
  // Ejecutamos
  $stmt->execute();
  $stmtCount = $dbh->prepare($query1);
  $stmtCount->execute();
  $contador=0;
  while ($rowCount = $stmtCount->fetch(PDO::FETCH_ASSOC)) {
    $contador++;
  }

  //OBTENEMOS LOS SALDOS ANTERIORES
  $tc=obtenerValorTipoCambio($moneda,strftime('%Y-%m-%d',strtotime($desdeInicioAnio)));
  if($tc==0){$tc=1;}
  if($desde==$desdeInicioAnio){
    $saldoAnterior=0;
    $debeAnterior=0;
    $haberAnterior=0;
    $saldoAnteriorFormato="0";
  }else{
    $saldoAnteriorArray=montoCuentaRangoFechas($unidadArray, $unidadCostoArray, $areaCostoArray, $desdeInicioAnio, $desde, $cuenta, $NombreGestion);
    $saldoAnterior=floatval($saldoAnteriorArray[0])-floatval($saldoAnteriorArray[1]);
    $debeAnterior=$saldoAnteriorArray[0];
    $haberAnterior=$saldoAnteriorArray[1];

    $saldoAnterior=$saldoAnterior;
    $saldoAnteriorFormato=0;
    if($saldoAnterior<0){
      $saldoAnteriorTC=$saldoAnterior/$tc;
      $saldoAnteriorFormato="(".formatNumberDec(abs($saldoAnteriorTC)).")";
    }else{
      $saldoAnteriorTC=$saldoAnterior/$tc;
      $saldoAnteriorFormato=formatNumberDec($saldoAnteriorTC);
    }
  }
  //FIN SALDO ANTERIOR

  if($contador!=0){
    $html.='<tr class="bg-plomo">'.
                  '<td colspan="3" class="text-left font-weight-bold">Nombre de la Cuenta: '.$nombreCuenta.' </td>'.
                  '<td colspan="2" class="text-right font-weight-bold">Sumas y Saldos Iniciales:</td>'.
                  '<td class="text-right font-weight-bold">'.formatNumberDec($debeAnterior/$tc).'</td>'.      
                  '<td class="text-right font-weight-bold">'.formatNumberDec($haberAnterior/$tc).'</td>'.      
                  '<td class="text-right font-weight-bold">'.$saldoAnteriorFormato.'</td>'.      
              '</tr>';
  
  }

  $index=1; $tDebeTc=0;$tHaberTc=0;$tDebeBol=0;$tHaberBol=0;   
  $saldoX=$saldoAnterior; 
  while ($rowComp = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $fechaX=$rowComp['fecha'];
    $codigoX=$rowComp['cod_det'];
    $glosaX=$rowComp['glosa'];
    $unidadX=$rowComp['abreviatura'];
    $areaX=$rowComp['areaAbrev'];
    $debeX=$rowComp['debe'];
    $haberX=$rowComp['haber'];
    $codCuentaAuxiliar=$rowComp['cod_cuentaauxiliar'];
    $cuenta_auxiliarX=nameCuentaAuxiliar($codCuentaAuxiliar);
    $nombreUnidad=nameUnidad($rowComp['unidad']);
    $codComprobanteX=$rowComp['codigo_comprobante'];
    $nombreComprobanteX=nombreComprobante($codComprobanteX);
    //INICIAR valores de las sumas
    if($glosaLen==0){      
      if(strlen($glosaX)>obtenerValorConfiguracion(72)){
          $glosaX=substr($glosaX,0,obtenerValorConfiguracion(72))."...";
      }
    }
    $tc=obtenerValorTipoCambio($moneda,strftime('%Y-%m-%d',strtotime($fechaX)));
    if($tc==0){$tc=1;}

            $tDebeBol+=$debeX;$tHaberBol+=$haberX;
            $tDebeTc+=$debeX/$tc;$tHaberTc+=$haberX/$tc; 
            $saldoX+=$debeX-$haberX; 

            if($saldoX<0){
              $saldoXFormato="(".formatNumberDec(abs($saldoX/$tc)).")";
            }else{
              $saldoXFormato=formatNumberDec($saldoX/$tc);
            }
            
             $html.='<tr>'.
                      //'<td class="font-weight-bold">'.$nombreUnidad.'</td>'.
                      '<td class="font-weight-bold small">'.$unidadX.'-'.$areaX.'</td>'.
                      '<td class="font-weight-bold small">'.$nombreComprobanteX.'</td>'.
                      '<td class="font-weight-bold small">'.strftime('%d/%m/%Y',strtotime($fechaX)).'</td>'.
                      '<td class="text-left small">['.$cuenta_auxiliarX."] - ".$glosaX.'</td>'.
                      '<td class="font-weight-bold small">'.$tc.'</td>';
                      
                       $html.='<td class="text-right font-weight-bold small">'.formatNumberDec($debeX/$tc).'</td>'.
                      '<td class="text-right font-weight-bold small">'.formatNumberDec($haberX/$tc).'</td>'.
                      '<td class="text-right font-weight-bold small">'.$saldoXFormato.'</td>';        
                      
                    $html.='</tr>';
      $entero=floor($tDebeBol);
      $decimal=$tDebeBol-$entero;
      $centavos=floor($decimal*100);
      if($centavos<10){
        $centavos="0".$decimal;
      }
      $index++; 
    }/* Fin del primer while*/
    if($contador!=0){
      $saldoY=$tDebeTc-$tHaberTc;

      $saldoYFormato="";
      if($saldoY<0){
        $saldoYFormato="(".formatNumberDec(abs($saldoY)).")";
      }else{
        $saldoYFormato=formatNumberDec($saldoY);
      }

      $totalDebeSaldoFinal=$debeAnterior+$tDebeBol;
      $totalHaberSaldoFinal=$haberAnterior+$tHaberBol;
      $saldoFinal=$totalDebeSaldoFinal-$totalHaberSaldoFinal;

      $saldoFinalFormato="";
      if($saldoFinal<0){
        $saldoFinalFormato="(".formatNumberDec(abs($saldoFinal/$tc)).")";
      }else{
        $saldoFinalFormato=formatNumberDec($saldoFinal/$tc);
      }

      $html.='<tr class="bg-secondary text-white">'.
                  '<td colspan="5" class="text-center">Sumas del periodo: '.$nombreCuenta.'</td>'.
                  '<td style="display: none;"></td>'.
                  '<td style="display: none;"></td>'.
                  '<td style="display: none;"></td>'.
                  '<td style="display: none;"></td>'.
                  '<td class="text-right font-weight-bold small">'.formatNumberDec($tDebeTc).'</td>'. 
                  '<td class="text-right font-weight-bold small">'.formatNumberDec($tHaberTc).'</td>'.
                  '<td class="text-right font-weight-bold small">'.$saldoYFormato.'</td>'.       
              '</tr>';
      $html.='<tr class="bg-secondary text-white">'.
                  '<td colspan="5" class="text-center">Sumas y saldos finales: '.$nombreCuenta.'</td>'.
                  '<td style="display: none;"></td>'.
                  '<td style="display: none;"></td>'.
                  '<td style="display: none;"></td>'.
                  '<td style="display: none;"></td>'.
                  '<td class="text-right font-weight-bold">'.formatNumberDec($totalDebeSaldoFinal/$tc).'</td>'. 
                  '<td class="text-right font-weight-bold">'.formatNumberDec($totalHaberSaldoFinal/$tc).'</td>'.
                  '<td class="text-right font-weight-bold">'.$saldoFinalFormato.'</td>'.       
              '</tr>'; 
    }
}//fin del for de cuentas

$html.=    '</tbody></table>';

echo $html;
?>
                   </div>
                </div>
              </div>
            </div>
          </div>  
        </div>
    </div>
