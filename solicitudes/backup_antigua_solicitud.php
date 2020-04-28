<?php
require_once '../conexion.php';
require_once '../functions.php';
require_once '../assets/libraries/CifrasEnLetras.php';

session_start();
if(!isset($_GET['sol'])){
    header("location:listSolicitudRecursos.php");
}else{
    $codigo=$_GET['sol'];
    $moneda=1;
    $abrevMon=abrevMoneda($moneda);
    $nombreMonedaG=nameMoneda($moneda);
}

$dbh = new Conexion();
$sqlX="SET NAMES 'utf8'";
$stmtX = $dbh->prepare($sqlX);
$stmtX->execute();
// Preparamos
$stmt = $dbh->prepare("SELECT p.*,e.nombre as estado_solicitud, u.abreviatura as unidad,u.nombre as nombre_unidad,a.abreviatura as area 
        from solicitud_recursos p,unidades_organizacionales u, areas a,estados_solicitudrecursos e
  where p.cod_unidadorganizacional=u.codigo and p.cod_area=a.codigo and e.codigo=p.cod_estadosolicitudrecurso and p.codigo='$codigo' order by codigo");
// Ejecutamos
$stmt->execute();
// bindColumn
            $stmt->bindColumn('codigo', $codigoX);
            $stmt->bindColumn('cod_personal', $codPersonalX);
            $stmt->bindColumn('fecha', $fechaX);
            $stmt->bindColumn('cod_unidadorganizacional', $codUnidadX);
            $stmt->bindColumn('cod_area', $codAreaX);
            $stmt->bindColumn('area', $areaX);
            $stmt->bindColumn('unidad', $unidadX);
            $stmt->bindColumn('nombre_unidad', $unidadNombreX);
            $stmt->bindColumn('estado_solicitud', $estadoX);
            $stmt->bindColumn('cod_estadosolicitudrecurso', $codEstadoX);
            $stmt->bindColumn('numero', $numeroX);
            $stmt->bindColumn('cod_simulacion', $codSimulacionX);
            $stmt->bindColumn('cod_proveedor', $codProveedorX);

while ($rowDetalle = $stmt->fetch(PDO::FETCH_BOUND)) {
    $fechaC=$fechaX;
    $unidadC=$unidadNombreX;
    $codUC=$codUnidadX;
    $monedaC="Bs";
    $codMC=$moneda;
    $numeroC=$numeroX;
    $solicitante=namePersonal($codPersonalX);
}
//INICIAR valores de las sumas
$tDebeDol=0;$tHaberDol=0;$tDebeBol=0;$tHaberBol=0;

// Llamamos a la funcion para obtener el detalle de la solicitud

$data = obtenerSolicitudRecursosDetalle($codigo);
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
            '<div id="header_titulo_texto">Solicitud de Recursos</div>'.
         '<div id="header_titulo_texto_inf">UNIDAD DE '.$unidadC.'</div>'.
         '<table class="table pt-2">'.
            '<tr class="bold table-title">'.
              '<td width="22%">Fecha: '.strftime('%d/%m/%Y',strtotime($fechaC)).'</td>'.
              '<td width="33%">Solicitante: '.$solicitante.'</td>'.
              '<td width="45%" class="text-right">'.strtoupper(abrevMes(strftime('%m',strtotime($fechaC)))).' N&uacute;mero: '.generarNumeroCeros(6,$numeroC).'</td>'.
            '</tr>'.
         '</table>'.
         '</header>';
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
              '<td>#</td>'.
              '<td>Detalle</td>'.
              '<td>Numero</td>'.
              '<td>Cuenta</td>'.
              '<td>Monto</td>'.
              '<td>Proveedor</td>';
              if($moneda!=1){
               $html.='<td>Debe</td>'.
              '<td>Haber</td>'; 
              }    
            $html.='</tr>'.
           '</thead>'.
           '<tbody>';
            $index=1;$totalImporte=0;$totalImportePres=0;
            while ($row = $data->fetch(PDO::FETCH_ASSOC)) {
                  $codCuentaX=$row['cod_plancuenta'];
                  $detalleX=$row["detalle"];
                  $importeX=$row["importe_presupuesto"];
                  $importeSolX=$row["importe"];
                  $proveedorX=nameProveedor($row["cod_proveedor"]);
                  $retencionX=$row["cod_confretencion"];
                  $totalImportePres+=$importeX;
                  $totalImporte+=$importeSolX;
                  if($retencionX!=0){
                    $tituloImporte="<strong>".nameRetencion($retencionX)."</strong>";
                  }else{
                    $tituloImporte="Ninguno"; 
                  }
                 $numeroCuentaX=trim($row['numero']);
                 $nombreCuentaX=trim($row['nombre']);
             $html.='<tr>'.
                      '<td>'.$index.'</td>'.
                      '<td>'.$detalleX.'</td>'.
                      '<td>'.$numeroCuentaX.'</td>'.
                      '<td>'.$nombreCuentaX.'</td>';
                      //'<td>'.$tituloImporte.'</td>';
                       $html.='<td class="text-right">'.number_format($importeSolX, 2, '.', ',').'</td>'.
                      '<td>'.$proveedorX.'</td>';
                      if($moneda!=1){
                       $html.='<td class="text-right">'.number_format($row['debe']/$tc, 2, '.', ',').'</td>'.
                      '<td class="text-right">'.number_format($row['haber']/$tc, 2, '.', ',').'</td>';
                      }
                                                  
                    $html.='</tr>';
                    $index++;
              }

      $entero=floor($totalImporte);
      $decimal=$totalImporte-$entero;
      $centavos=round($decimal*100);
      if($centavos<10){
        $centavos="0".$centavos;
      }
      $html.='<tr class="bold table-title">'.
                  '<td colspan="4" class="text-center">Sumas:</td>'.
                  '<td class="text-right">'.number_format($totalImporte, 2, '.', ',').'</td><td></td>';
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
descargarPDF("IBNORCA - Solicitud Recursos ".$unidadC." (".$numeroC.")",$html);
?>
