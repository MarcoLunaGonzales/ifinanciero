<?php
require_once '../conexion.php';
require_once '../functions.php';
require_once '../assets/libraries/CifrasEnLetras.php';

session_start();
if(!isset($_GET['comp'])){
    header("location:list.php");
}else{
    $codigo=$_GET['comp'];
    $moneda=$_GET['mon'];
    $abrevMon=abrevMoneda($moneda);
    $nombreMonedaG=nameMoneda($moneda);
}

$dbh = new Conexion();
$sqlX="SET NAMES 'utf8'";
$stmtX = $dbh->prepare($sqlX);
$stmtX->execute();
// Preparamos
$stmt = $dbh->prepare("SELECT (select u.nombre from unidades_organizacionales u where u.codigo=c.cod_unidadorganizacional)unidad,
(select u.abreviatura from unidades_organizacionales u where u.codigo=c.cod_unidadorganizacional)cod_unidad,c.cod_gestion, 
(select m.nombre from monedas m where m.codigo=c.cod_moneda)moneda, (select m.codigo from monedas m where m.codigo=c.cod_moneda)cod_moneda,
(select t.nombre from tipos_comprobante t where t.codigo=c.cod_tipocomprobante)tipo_comprobante, c.fecha, c.numero,c.codigo, c.glosa
from comprobantes c where c.codigo=$codigo;");
// Ejecutamos
$stmt->execute();
// bindColumn
$stmt->bindColumn('unidad', $nombreUnidad);
$stmt->bindColumn('cod_unidad', $codigoUnidad);
$stmt->bindColumn('cod_gestion', $nombreGestion);
$stmt->bindColumn('moneda', $nombreMoneda);
$stmt->bindColumn('cod_moneda', $codigoMoneda);
$stmt->bindColumn('tipo_comprobante', $nombreTipoComprobante);
$stmt->bindColumn('fecha', $fechaComprobante);
$stmt->bindColumn('numero', $nroCorrelativo);
$stmt->bindColumn('codigo', $codigo);
$stmt->bindColumn('glosa', $glosaComprobante);

while ($rowDetalle = $stmt->fetch(PDO::FETCH_BOUND)) {
    $fechaC=$fechaComprobante;
    $glosaC=$glosaComprobante;
    $unidadC=$nombreUnidad;
    $codUC=$codigoUnidad;
    $monedaC=$nombreMoneda;
    $codMC=$codigoMoneda;
    $tipoC=$nombreTipoComprobante;
    $numeroC=$nroCorrelativo;
}
//INICIAR valores de las sumas
$tDebeDol=0;$tHaberDol=0;$tDebeBol=0;$tHaberBol=0;

// Llamamos a la funcion para obtener el reporte de comprobantes
$data = obtenerComprobantesDetImp($codigo);
$tc=obtenerValorTipoCambio($moneda,strftime('%Y-%m-%d',strtotime($fechaC)));
if($tc==0){$tc=1;}
$fechaActual=date("Y-m-d");
header('Content-type: text/html; charset=ISO-8859-1');
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
            '<div id="header_titulo_texto">Comprobante de Contabilidad</div>'.
         '<div id="header_titulo_texto_inf">UNIDAD DE '.$unidadC.'</div>'.
         '<table class="table pt-2">'.
            '<tr class="bold table-title">'.
              '<td width="22%">Fecha: '.strftime('%d/%m/%Y',strtotime($fechaC)).'</td>'.
              '<td width="33%">t/c: '.$abrevMon.': '.$tc.'</td>'.
              '<td width="45%" class="text-right">'.$tipoC.' '.strtoupper(abrevMes(strftime('%m',strtotime($fechaC)))).' N&uacute;mero: '.generarNumeroCeros(6,$numeroC).'</td>'.
            '</tr>'.
            '<tr>'.
            '<td colspan="3">CONCEPTO: '.$glosaC.'</td>'.
            '</tr>'.
         '</table>'.
         '</header><br><br>';
         $html.='
         <footer class="footer"><table class="table">'.
             '<tr class="text-center" valign="top">'.
               '<td width="25%" class="text-center"></td>'.
               '<td width="25%" class="text-center"></td>'.
               '<td width="25%" class="text-center"></td>'.
               '<td width="25%" class="text-left"><p>Firma/Sello  ___________________</p>Nombre:</td>'.
             '</tr>'.
             '<tr class="text-center" valign="top">'.
               '<td width="25%" class="text-center">ELABORADO POR</td>'.
               '<td width="25%" class="text-center">REVISADO POR<br>_________________</td>'.
               '<td width="25%" class="text-center">APROBADO POR<br>__________________.</td>'.
               '<td width="25%" class="text-left">C.I. Nº</td>'.
             '</tr>'.
           '</table></footer>'.
         '<table class="table">'.
            '<thead>'.
            '<tr class="bold table-title text-center">'.
              '<td colspan="2" class="td-border-none"></td>'.
              '<td colspan="2" class="td-border-none">Bolivianos</td>';
              if($moneda!=1){
               $html.='<td colspan="2" class="td-border-none">'.$nombreMonedaG.'</td>'; 
              }      
            $html.='</tr>'.
            '<tr class="bold table-title text-center">'.
              '<td>Cuenta</td>'.
              '<td>Nombre de la cuenta / Descripci&oacute;n</td>'.
              '<td>Debe</td>'.
              '<td>Haber</td>';
              if($moneda!=1){
               $html.='<td>Debe</td>'.
              '<td>Haber</td>'; 
              }    
            $html.='</tr>'.
           '</thead>'.
           '<tbody>';
            $index=1;
            while ($row = $data->fetch(PDO::FETCH_ASSOC)) {
              print_r($row['nombre']);
             $html.='<tr>'.
                      '<td>'.$row['numero'].'<br>'.$row['unidadAbrev'].'<br>'.$row['abreviatura'].'</td>'.
                      '<td>'.$row['nombre'].'<br>'.$row['glosa'].'</td>';
                      $tDebeBol+=$row['debe'];$tHaberBol+=$row['haber'];
                      $tDebeDol+=$row['debe']/$tc;$tHaberDol+=$row['haber']/$tc;
                       $html.='<td class="text-right">'.number_format($row['debe'], 2, '.', ',').'</td>'.
                      '<td class="text-right">'.number_format($row['haber'], 2, '.', ',').'</td>';
                      if($moneda!=1){
                       $html.='<td class="text-right">'.number_format($row['debe']/$tc, 2, '.', ',').'</td>'.
                      '<td class="text-right">'.number_format($row['haber']/$tc, 2, '.', ',').'</td>';
                      }
                                                  
                    $html.='</tr>';
              }

      $entero=floor($tDebeBol);
      $decimal=$tDebeBol-$entero;
      $centavos=round($decimal*100);
      if($centavos<10){
        $centavos="0".$centavos;
      }
      $html.='<tr class="bold table-title">'.
                  '<td colspan="2" class="text-center">Sumas:</td>'.
                  '<td class="text-right">'.number_format($tDebeBol, 2, '.', ',').'</td>'.
                  '<td class="text-right">'.number_format($tHaberBol, 2, '.', ',').'</td>';
                   if($moneda!=1){
                       $html.='<td class="text-right">'.number_format($tDebeDol, 2, '.', ',').'</td>'. 
                      '<td class="text-right">'.number_format($tHaberDol, 2, '.', ',').'</td>';
                      }
                         
              $html.='</tr>'.
              '</tbody>';
$html.=    '</table>';
$html.='<p class="bold table-title">Son: '.ucfirst(CifrasEnLetras::convertirNumeroEnLetras($entero)).'      '.$centavos.'/100 Bolivianos</p>';         
$html.='</body>'.
      '</html>';

//$html = mb_convert_encoding($html,'UTF-8', 'ISO-8859-1');

 //echo $html;           
descargarPDF("IBNORCA - ".$unidadC." (".$tipoC.", ".$numeroC.")",$html);
?>
