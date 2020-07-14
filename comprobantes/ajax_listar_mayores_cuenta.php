<?php
session_start();
require_once '../conexion.php';
require_once '../functionsGeneral.php';
require_once '../functions.php';
require_once '../styles.php';
require_once '../layouts/librerias.php';
$dbh = new Conexion();
// Preparamos
$globalNombreGestion=$_SESSION["globalNombreGestion"];
$globalUser=$_SESSION["globalUser"];
$globalGestion=$_SESSION["globalGestion"];
$globalUnidad=$_SESSION["globalUnidad"];
$globalNombreUnidad=$_SESSION['globalNombreUnidad'];
$globalArea=$_SESSION["globalArea"];
$globalAdmin=$_SESSION["globalAdmin"];

$fechaActual=date("Y-m-d");
$desdeInicioAnio="";
if($_POST["fecha_desde"]==""){
  $y=$globalNombreGestion;
  $desde=$y."-01-01";
  $hasta=$y."-12-31";
  $desdeInicioAnio=$y."-01-01";
}else{
  $porcionesFechaDesde = explode("-", $_POST["fecha_desde"]);
  $porcionesFechaHasta = explode("-", $_POST["fecha_hasta"]);

  $desdeInicioAnio=$porcionesFechaDesde[0]."-01-01";
  $desde=$porcionesFechaDesde[0]."-".$porcionesFechaDesde[1]."-".$porcionesFechaDesde[2];
  $hasta=$porcionesFechaHasta[0]."-".$porcionesFechaHasta[1]."-".$porcionesFechaHasta[2];
  //$desde=strftime('%Y-%m-%d',strtotime($_POST["fecha_desde"]));
  //$hasta=strftime('%Y-%m-%d',strtotime($_POST["fecha_hasta"]));
}

$moneda=$_POST["moneda"];

$codcuenta=$_POST["cuenta"];
$codcuentaMayor=$_POST["cuenta"];
$nombreMoneda=nameMoneda($moneda);
$unidadCosto=$_POST['unidad_costo'];
$areaCosto=$_POST['area_costo'];
$unidad=$_POST['unidad'];

//echo "VARIABLES: ".$unidadCosto." ".$areaCosto." ".$unidad;


$gestion= $_POST["gestion"];
$entidad = $_POST["entidad"];

//PONEMOS LAS VARIABLES PARA CUANDO LLAMEMOS AL REPORTE DESDE LOS MAYORES
if($gestion==null){
  $gestion=$globalGestion;
  $unidadCosto=explode(",",obtenerUnidadesReport(0));
  $unidad=explode(",",obtenerUnidadesReport(0));
  $areaCosto=explode(",",obtenerAreasReport(0));
}
$NombreGestion = nameGestion($gestion);
$unidadCostoArray=implode(",", $unidadCosto);
$areaCostoArray=implode(",", $areaCosto);
$unidadArray=implode(",", $unidad);
if(isset($_POST['glosa_len'])){
 $glosaLen=1; 
}else{
  $glosaLen=0;
}
if(isset($_POST['cuentas_auxiliares'])){
 $cuentas_auxiliares=1; 
}else{
  $cuentas_auxiliares=0;
}

if(isset($_POST['cuenta_especifica'])){
  $codcuenta=[];
  $codcuenta[0]=$_POST['cuenta_especifica']."@normal";
}

$unidadGeneral="";$unidadAbrev="";$areaAbrev="";

$unidadGeneral=abrevUnidad($unidadArray);
$unidadAbrev=abrevUnidad($unidadCostoArray);
$areaAbrev=abrevArea($areaCostoArray);

$nombreCuentaTitle="";
for ($jj=0; $jj < cantidadF($codcuenta); $jj++) { 
    $porciones1 = explode("@", $codcuenta[$jj]);
    $cuenta=$porciones1[0];
    if($porciones1[1]=="aux"){
      $nombreCuentaTitle.=trim(nameCuentaAux($cuenta)).", ";
    }else{
      $nombreCuentaTitle.="[".trim(obtieneNumeroCuenta($cuenta))."] ".trim(nameCuenta($cuenta)).", ";
    }
}
$periodoTitle=" Del ".strftime('%d/%m/%Y',strtotime($desde))." al ".strftime('%d/%m/%Y',strtotime($hasta));

     if(strlen($nombreCuentaTitle)>190){
        $nombreCuentaTitle=substr($nombreCuentaTitle,0,190)."...";
      }
?>
<style>
  tfoot input {
    width: 100%;
    padding: 3px;
  }
</style>

<script> periodo_mayor='<?=$periodoTitle?>';
          cuenta_mayor='<?=trim($nombreCuentaTitle)?>';
          unidad_mayor='<?=$unidadGeneral?>';
 </script>
     <?php
    $html='<table class="table table-bordered table-condensed" id="mayores_cuenta_reporte_modal">'.
            '<thead >'.
            '<tr class="text-center" style="background:#21618C; color:#fff;">'.
              '<th>Oficina Origen</th>'.
              '<th width="5%">Cbte</th>'.
              '<th width="7%">Fecha</th>'.
              '<th width="5%">Centro de Costos</th>'.
              '<th width="60%">Concepto</th>'.
              '<th width="3%">t/c</th>'.
              '<th width="5%">Debe</th>'.
              '<th width="5%">Haber</th>'.
              '<th width="5%">Saldos</th>'.
              '<th>*</th>'.
            '</tr>'.
           '</thead>'.
           '<tbody>';

    for ($xx=0; $xx < cantidadF($codcuenta); $xx++) { 
      $porciones = explode("@", $codcuenta[$xx]);
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
      //echo "desde: ".$desde." desdeInicioAnio: ".$desdeInicioAnio;
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
                      '<td style="display: none;"></td>'.
                      '<td style="display: none;"></td>'.
                      '<td style="display: none;"></td>'.
                      '<td colspan="4" class="text-left font-weight-bold">Nombre de la Cuenta: '.$nombreCuenta.' </td>'.
                      '<td colspan="2" class="text-right font-weight-bold">Sumas y Saldos Iniciales:</td>'.                  
                      '<td style="display: none;"></td>'.
                      '<td class="text-right font-weight-bold">'.formatNumberDec($debeAnterior/$tc).'</td>'.      
                      '<td class="text-right font-weight-bold">'.formatNumberDec($haberAnterior/$tc).'</td>'.      
                      '<td class="text-right font-weight-bold">'.$saldoAnteriorFormato.'</td>'.
                      '<td class="text-right font-weight-bold small"></td>'.       
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
        $nombreUnidad=abrevUnidad_solo($rowComp['unidad']);
        $codComprobanteX=$rowComp['codigo_comprobante'];
        $nombreComprobanteX=nombreComprobante($codComprobanteX);
        //INICIAR valores de las sumas
        if($glosaLen==0){      
          if(strlen($glosaX)>15){
            $glosaX=substr($glosaX,0,15)."...";
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
                '<td class="font-weight-bold small"><input type="hidden" id="fila_habilitada_mayor'.$index.'" value="'.$codigoX.'">'.$nombreUnidad.'</td>'.
                '<td class="font-weight-bold small">'.$nombreComprobanteX.'</td>'.
                '<td class="font-weight-bold small">'.strftime('%d/%m/%Y',strtotime($fechaX)).'</td>'.
                '<td class="font-weight-bold small">'.$unidadX.'-'.$areaX.'</td>'.
                '<td class="text-left small">['.$cuenta_auxiliarX."] - ".$glosaX.'</td>'.
                '<td class="font-weight-bold small">'.$tc.'</td>';
                
                 $html.='<td class="text-right font-weight-bold small"><input type="hidden" id="debe_mayor_ajax'.$index.'" value="'.$debeX/$tc.'">'.formatNumberDec($debeX/$tc).'</td>'.
                '<td class="text-right font-weight-bold small"><input type="hidden" id="haber_mayor_ajax'.$index.'" value="'.$haberX/$tc.'">'.formatNumberDec($haberX/$tc).'</td>'.
                '<td class="text-right font-weight-bold small">'.$saldoXFormato.'</td>'.        
                '<td class="text-right font-weight-bold small"><a href="#" id="boton_habilitado_mayor'.$index.'" style="padding: 0;font-size:10px;width:25px;height:25px;" onclick="listar_comprobanteDetalleMayor('.$codigoX.','.$index.')" class="btn btn-fab btn-success btn-sm list-de-com" title="Seleccionar Item"><i class="material-icons">done</i></a></td>'; 
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
                    '<td colspan="6" class="text-center">Sumas del periodo:</td>'.
                    '<td style="display: none;"></td>'.
                    '<td style="display: none;"></td>'.
                    '<td style="display: none;"></td>'.
                    '<td style="display: none;"></td>'.
                    '<td style="display: none;"></td>'.
                    '<td class="text-right font-weight-bold small">'.formatNumberDec($tDebeTc).'</td>'. 
                    '<td class="text-right font-weight-bold small">'.formatNumberDec($tHaberTc).'</td>'.
                    '<td class="text-right font-weight-bold small">'.$saldoYFormato.'</td>'.
                    '<td class="text-right font-weight-bold small"></td>'.       
                '</tr>';
        $html.='<tr class="bg-secondary text-white">'.
                    '<td colspan="6" class="text-center">Sumas y saldos finales:</td>'.
                    '<td style="display: none;"></td>'.
                    '<td style="display: none;"></td>'.
                    '<td style="display: none;"></td>'.
                    '<td style="display: none;"></td>'.
                    '<td style="display: none;"></td>'.
                    '<td class="text-right font-weight-bold">'.formatNumberDec($totalDebeSaldoFinal/$tc).'</td>'. 
                    '<td class="text-right font-weight-bold">'.formatNumberDec($totalHaberSaldoFinal/$tc).'</td>'.
                    '<td class="text-right font-weight-bold">'.$saldoFinalFormato.'</td>'.  
                    '<td class="text-right font-weight-bold small"></td>'.      
                '</tr>'; 
      }
    }//fin del for de cuentas

    $html.=    '</tbody>'.
    '<tfoot>'.
      '<tr style="background:#21618C; color:#fff;">'.
        '<th class="text-center">OF</td>'.
        '<th class="small"><small>Cbte</small></th>'.      
        '<th class="small"><small>Fecha</small></th>'.      
        '<th class="small"><small>C.C.</small></th>'.
        '<th class="small"><small>Concepto</small></th>'.
        '<th class="small"><small>t/c</small></th>'.
        '<th class="small"><small>DEBE</small></th>'.
        '<th class="small"><small>HABER.</small></th>'.      
        '<th class="small"><small>SALDOS</small></th>'.
        '<td class="small"><small>*</small></td>'.
      '</tr>'.
    '</tfoot></table>';

    echo $html;
    ?>
   <input type="hidden" id="cantidad_mayor_modal" value="<?=($index-1)?>">
    <?php
    ?>