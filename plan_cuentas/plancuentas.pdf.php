<?php

require_once '../conexion.php';
require_once 'configModule.php';
require_once '../styles.php';
require_once '../functions.php';
require_once '../functionsGeneral.php';
// require_once '../layouts/bodylogin2.php';


$dbh = new Conexion();

// Preparamos
$stmt = $dbh->prepare("SELECT p.codigo, p.numero, p.nombre, p.cod_padre, p.nivel, 
  (select tc.nombre from tipos_cuenta tc where tc.codigo=p.cod_tipocuenta)cod_tipocuenta, p.cuenta_auxiliar FROM $table p where cod_estadoreferencial=1 order by p.numero");
// Ejecutamos
$stmt->execute();
// bindColumn
$stmt->bindColumn('codigo', $codigo);
$stmt->bindColumn('numero', $numero);
$stmt->bindColumn('nombre', $nombre);
$stmt->bindColumn('cod_padre', $codPadre);
$stmt->bindColumn('nivel', $nivel);
$stmt->bindColumn('cod_tipocuenta', $codTipoCuenta);
$stmt->bindColumn('cuenta_auxiliar', $cuentaAuxiliar);



$html = '';
$html.='<html>'.
         '<head>'.
             '<!-- CSS Files -->'.
             '<link rel="icon" type="image/png" href="../assets/img/favicon.png">'.
             '<link href="../assets/libraries/plantillaPDF2.css" rel="stylesheet" />'.
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
              <td width="25%"></td>
              <td><center><span style="font-size: 13px"><b>PLAN DE CUENTAS</b></span><BR>IBNORCA<br></center></td>
              <td width="25%"><center></center></td>
              </tr>
            </table>'.
         '</header>';
$html.= '<table class="table">
  <thead>
    <tr>
      <th class="text-center">#</th>
      <th>Codigo</th>
      <th width="50%">Nombre</th>
      <th>Padre</th>
      <th>Nivel</th>
      <th>Tipo</th>
      <th>Aux</th>
    </tr>
  </thead>
  <tbody>';
  
    $index=1;
    while ($row = $stmt->fetch(PDO::FETCH_BOUND)) {
      $numeroFormateado=formateaPuntosPlanCuenta($numero);

      $numeroPadreX=obtieneNumeroCuenta($codPadre);
      $numeroPadreX=formateaPuntosPlanCuenta($numeroPadreX);
    
      $nombre=formateaPlanCuenta($nombre, $nivel);
      // $linkAdd="";
      // $imgCuentaAuxiliar="";
      
    
    $html.='<tr>
      <td align="center">'.$index.'</td>
      <td>'.$numeroFormateado.'</td>
      <td>'.$nombre.'</td>
      <td>'.$numeroPadreX.'</td>
      <td>'.$nivel.'</td>
      <td>'.$codTipoCuenta.'</td>';
      if($cuentaAuxiliar==1){
        //$html.='<td class="td-actions text-center"><a href="#" rel="tooltip" class="'.$buttonCeleste.'"><i class="material-icons" style="color:blue">check_circle_outline</i></a></td>';
        $html.='<td class="text-center" style="color:blue">OK</td>';  
      }else{
        $html.='<td class="text-center"></td>';  
      }
      

      $html.='</tr>';
  
        $index++;
      }
  
  $html.='</tbody>
</table>';

$html.='</body>'.
      '</html>';

  // echo $html;
  descargarPDF("PLAN DE CUENTAS ",$html);

//descargarPDFHorizontal("Planilla_aguinaldos_".$gestion,$html);