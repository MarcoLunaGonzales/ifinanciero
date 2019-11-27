<?php
session_start();
require_once '../conexion.php';
require_once '../styles.php';
require_once '../functionsGeneral.php';
require_once '../functions.php';
require_once '../assets/libraries/CifrasEnLetras.php';
require_once '../layouts/bodylogin2.php';

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

$unidad=$_POST["unidad"];
$nombreUnidad=nameUnidad($unidad);
$tipo=$_POST["tipo_comprobante"];
$desde=strftime('%Y-%m-%d',strtotime($_POST["fecha_desde"]));
$hasta=strftime('%Y-%m-%d',strtotime($_POST["fecha_hasta"]));
$moneda=$_POST["moneda"];
$nombreMoneda=nameMoneda($moneda);
$tcA=obtenerValorTipoCambio($moneda,$fechaActual);

$query1="SELECT (select u.nombre from unidades_organizacionales u where u.codigo=c.cod_unidadorganizacional)unidad,
(select u.abreviatura from unidades_organizacionales u where u.codigo=c.cod_unidadorganizacional)cod_unidad,c.cod_gestion, 
(select m.nombre from monedas m where m.codigo=c.cod_moneda)moneda, (select m.codigo from monedas m where m.codigo=c.cod_moneda)cod_moneda,
(select t.nombre from tipos_comprobante t where t.codigo=c.cod_tipocomprobante)tipo_comprobante, c.fecha, c.numero,c.codigo, c.glosa
from comprobantes c where c.cod_unidadorganizacional=$unidad and c.fecha BETWEEN '$desde' and '$hasta' and(";
for ($i=0; $i < cantidadF($tipo); $i++) { 
  
  if($i==(cantidadF($tipo)-1)){
  $query1.=" c.cod_tipocomprobante=".$tipo[$i].")";
  }else{
  $query1.=" c.cod_tipocomprobante=".$tipo[$i]." or";
  }   
}
$stmt = $dbh->prepare($query1);
// Ejecutamos
$stmt->execute();
 ?><div class="content">
  <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
              <div class="card">
                <div class="card-header <?=$colorCard;?> card-header-icon">
                  <div class="card-icon bg-blanco">
                    <img class="" width="40" height="40" src="../assets/img/logoibnorca.png">
                  </div>
                  <div class="float-right col-sm-2"><h6 class="card-title">Exportar como:</h6></div>
                  <h4 class="card-title text-center">Reporte Libro Diario</h4>
                  <h6 class="card-title">Gestion: <?=strftime('%Y',strtotime($hasta));?></h6>
                  <h6 class="card-title">Unidad: <?=$nombreUnidad;?></h6> 

                </div>
                <div class="card-body">
                  <div class="table-responsive">
     <?php
    $html='<table id="libro_diario_rep" class="table table-bordered table-condensed" style="width:100%">'.
            '<thead class="bg-principal text-white">'.
            '<tr class="text-center">'.
              '<th colspan="2" class=""></th>'.
              '<th colspan="2" class="">Bolivianos</th>'.
              '<th colspan="2" class="">'.$nombreMoneda.'</th>'.
            '</tr>'.
            '<tr class="text-center">'.
              '<th>Cuenta</th>'.
              '<th>Nombre de la cuenta / Descripción</th>'.
              '<th>Debe</th>'.
              '<th>Haber</th>'.
              '<th>Debe</th>'.
              '<th>Haber</th>'.
            '</tr>'.
           '</thead>'.
           '<tbody>'; 
$index=1; $totalSumasDebe=0;$totalSumasHaber=0; $totalSumasDebeTc=0;$totalSumasHaberTc=0;      
while ($rowComp = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $fechaX=$rowComp['fecha'];
    $glosaX=$rowComp['glosa'];
    $unidadX=$rowComp['unidad'];
    $coduX=$rowComp['cod_unidad'];
    $monedaX=$rowComp['moneda'];
    $codmX=$rowComp['cod_moneda'];
    $tipoX=$rowComp['tipo_comprobante'];
    $numeroX=$rowComp['numero'];
    $codigoX=$rowComp['codigo'];
    //INICIAR valores de las sumas
    $tDebeTc=0;$tHaberTc=0;$tDebeBol=0;$tHaberBol=0;
   // Llamamos a la funcion para obtener el reporte de comprobantes
    $data = obtenerComprobantesDet($codigoX);
    $tc=obtenerValorTipoCambio($moneda,strftime('%Y-%m-%d',strtotime($fechaX)));
    if($tc==0){$tc=1;}
    $html.='<tr class="bg-plomo">'.
            '<td class="">N. correlativo:<b>'.$index.'</b></td>'.
            '<td class="">Fecha:<b>'.strftime('%d/%m/%Y',strtotime($fechaX)).'</b></td>'.
            '<td class="" colspan="3">t/c:<b>'.$tc.'</b></td>'.
            '<td style="display: none;"></td>'.
            '<td style="display: none;"></td>'.
            '<td class=" td-border-r">Numero:<b>'.generarNumeroCeros(6,$numeroX).'</b></td>'.
            '</tr>';
    $html.='<tr class="bg-plomo">'.
            '<td colspan="6"class="text-left">CONCEPTO:  '.$glosaX.'</td>'.
            '<td style="display: none;"></td>'.
            '<td style="display: none;"></td>'.
            '<td style="display: none;"></td>'.
            '<td style="display: none;"></td>'.
            '<td style="display: none;"></td>'.
            '</tr>';   
            while ($row = $data->fetch(PDO::FETCH_ASSOC)) {
             $html.='<tr>'.
                      '<td class="font-weight-bold">'.$row['numero'].'<br>'.$row['unidadAbrev'].'/'.$row['abreviatura'].'</td>'.
                      '<td><b>'.$row['nombre'].'<br>'.$row['glosa'].'</b></td>';
                      $tDebeBol+=$row['debe'];$tHaberBol+=$row['haber'];
                      $tDebeTc+=$row['debe']/$tc;$tHaberTc+=$row['haber']/$tc;
                       $html.='<td class="text-right font-weight-bold">'.number_format($row['debe'], 2, '.', ',').'</td>'.
                      '<td class="text-right font-weight-bold">'.number_format($row['haber'], 2, '.', ',').'</td>'.
                      '<td class="text-right font-weight-bold">'.number_format($row['debe']/$tc, 2, '.', ',').'</td>'.
                      '<td class="text-right font-weight-bold">'.number_format($row['haber']/$tc, 2, '.', ',').'</td>';        
                      
                    $html.='</tr>';
              }
      $entero=floor($tDebeBol);
      $decimal=$tDebeBol-$entero;
      $centavos=floor($decimal*100);
      if($centavos<10){
        $centavos="0".$decimal;
      }
      $html.='<tr class="">'.
                  '<td colspan="2" class="text-center">Sumas:</td>'.
                  '<td style="display: none;"></td>'.
                  '<td class="text-right font-weight-bold">'.number_format($tDebeBol, 2, '.', ',').'</td>'.
                  '<td class="text-right font-weight-bold">'.number_format($tHaberBol, 2, '.', ',').'</td>'. 
                  '<td class="text-right font-weight-bold">'.number_format($tDebeTc, 2, '.', ',').'</td>'. 
                  '<td class="text-right font-weight-bold">'.number_format($tHaberTc, 2, '.', ',').'</td>'.       
              '</tr>';

    $totalSumasHaber=$totalSumasHaber+$tHaberBol;
    $totalSumasDebe=$totalSumasDebe+$tDebeBol;
    $totalSumasDebeTc=$totalSumasDebeTc+$tDebeTc;
    $totalSumasHaberTc=$totalSumasHaberTc+$tHaberTc;
$index++;          
}/* Fin del primer while*/
$html.='<tr class="bg-secondary text-white">'.
                  '<td colspan="2" class="text-center font-weight-bold">Sumas Totales:</td>'.
                  '<td style="display: none;"></td>'.
                  '<td class="text-right font-weight-bold">'.number_format($totalSumasDebe, 2, '.', ',').'</td>'.
                  '<td class="text-right font-weight-bold">'.number_format($totalSumasHaber, 2, '.', ',').'</td>'. 
                  '<td class="text-right font-weight-bold">'.number_format($totalSumasDebeTc, 2, '.', ',').'</td>'. 
                  '<td class="text-right font-weight-bold">'.number_format($totalSumasHaberTc, 2, '.', ',').'</td>'.       
              '</tr>';
$html.=    '</tbody></table>';
/*$html.='<p class=">Son: '.ucfirst(CifrasEnLetras::convertirNumeroEnLetras($entero)).'      '.$centavos.'/100 Bolivianos</p>';*/
echo $html;
?>
                   </div>
                </div>
              </div>
            </div>
          </div>  
        </div>
    </div>
