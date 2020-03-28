<?php
session_start();
require_once '../conexion.php';
require_once '../functionsGeneral.php';
require_once '../functions.php';
require_once '../assets/libraries/CifrasEnLetras.php';

$dbh = new Conexion();
$fechaActual=date("Y-m-d");
$fecha=$_POST['fecha'];
$moneda=1; //$_POST["moneda"];
$unidades=$_POST['unidad'];
$tituloOficinas="";
for ($i=0; $i < count($unidades); $i++) { 
  $tituloOficinas.=abrevUnidad_solo($unidades[$i]).",";
}
$areas=$_POST['area_costo'];
$html = '';
$html.='<html>'.
         '<head>'.
             '<!-- CSS Files -->'.
             '<link rel="icon" type="image/png" href="../assets/img/favicon.png">'.
             '<link href="../assets/libraries/plantillaPDF.css" rel="stylesheet" />'.
           '</head>';
$html.='<body>';
$html.=  '<header class="header">'.            
            '<img class="imagen-logo-izq" src="../assets/img/ibnorca2.jpg">'.
            '<div id="header_titulo_texto">Balance General</div>'.
         '<div id="header_titulo_texto_inf">Practicado al '.$fecha.'</div>'.
         '<table class="table pt-2">'.
            '<tr class="bold table-title">'.
              '<td width="22%">Entidad: IBNORCA</td>'.
              '<td width="33%">Expresado:Bolivianos</td>'.            
            '</tr>'.
            '<tr>'.
            '<td colspan="2">Oficinas: '.$tituloOficinas.'</td>'.
            '</tr>'.
         '</table>'.
         '</header>';

$html.='<br><table class="table">'.
            '<thead>'.
            '<tr class="bold table-title text-center">'.
              '<td>Numero</td>'.
              '<td>Cuenta</td>'.
              '<td></td>'.
              '<td></td>'.
              '<td></td>'.
              '<td></td>'.
            '</tr>'.
           '</thead>'.
           '<tbody>'; 
           $index=1;
           $tDebeBol=0;

           
 $detallesReporte=listaSumaMontosDebeHaberComprobantesDetalle($fecha,1,$unidades,$areas);                  
while ($rowComp = $detallesReporte->fetch(PDO::FETCH_ASSOC)) {
    $numeroX=$rowComp['numero'];
    $nombreX=formateaPlanCuenta($rowComp['nombre'], $rowComp['nivel']);
    $montoX=$rowComp['total_debe'];
     $tDebeBol+=$montoX;
    $html.='<tr>'.
                '<td class="text-left">'.$numeroX.'</td>'.
                '<td class="text-left">'.$nombreX.'</td>'.
                '<td class="text-right">'.number_format($montoX, 2, '.', ',').'</td>'.
                '<td class="text-right"></td>'.
                '<td class="text-right"></td>'.
                '<td class="text-right"></td>';   
     $html.='</tr>';              
$index++;          
}/* Fin del primer while*/
     
      $entero=floor($tDebeBol);
      $decimal=$tDebeBol-$entero;
      $centavos=floor($decimal*100);
      if($centavos<10){
        $centavos="0".$decimal;
      }
      $html.='<tr class="bold table-title">'.
                  '<td colspan="2" class="text-center">Total:</td>'.
                  '<td class="text-right">'.number_format($tDebeBol, 2, '.', ',').'</td>'.
                  '<td class="text-right"></td>'. 
                  '<td class="text-right"></td>'. 
                  '<td class="text-right"></td>'.     
              '</tr>';

$html.=    '</tbody></table>';
/*$html.='<p class="bold table-title">Son: '.ucfirst(CifrasEnLetras::convertirNumeroEnLetras($entero)).'      '.$centavos.'/100 Bolivianos</p>';*/
$html.='</body>'.
      '</html>';
                    
descargarPDF("IBNORCA - ".$unidadC." (".$tipoC.", ".$numeroC.")",$html);
?>
