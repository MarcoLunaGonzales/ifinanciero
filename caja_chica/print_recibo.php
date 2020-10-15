<?php //ESTADO FINALIZADO

require_once __DIR__.'/../conexion.php';
require '../assets/phpqrcode/qrlib.php';

//require_once 'configModule.php';
require_once __DIR__.'/../functions.php';
require_once __DIR__.'/../functionsGeneral.php';

$dbh = new Conexion();
$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);//try
//RECIBIMOS LAS VARIABLES

$codigo = $_GET["codigo"];//codigoactivofijo
try{
  $datos=encuentraDatosCajaChicaDetalle($codigo);
  $oficina=abrevUnidad_solo($datos['cod_uo']); 
  $area=abrevArea_solo($datos['cod_area']); 
  $monto=$datos['monto'];

  $codigoSolicitud=encuentraDatosSolicitudRecursosDesdeCajaChica($datos['codigo']);
                      
  if($codigoSolicitud[0]>0){
     $importeSolX=$monto;
     $retencionX=$codigoSolicitud[2];
     if($retencionX!=0){
           $tituloImporte=abrevRetencion($retencionX);
           $porcentajeRetencion=100-porcentRetencionSolicitud($retencionX);
           $montoImporte=$importeSolX*($porcentajeRetencion/100);       
           if(($retencionX==8)||($retencionX==10)){ //validacion del descuento por retencion
             $montoImporte=$importeSolX;
           }
           $montoImporteRes=$importeSolX-$montoImporte;
          }else{
           $tituloImporte="Ninguno";
           $montoImporte=$importeSolX;
           $montoImporteRes=0; 
       }
      $monto=$montoImporte; 
  }
$html = '';
$html.='<html>'.
            '<head>'.
                '<!-- CSS Files -->'.
                '<link rel="icon" type="image/png" href="../assets/img/favicon.png">'.
                '<link href="../assets/libraries/plantillaPDFCajaChica.css" rel="stylesheet" />'.
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
            '<img class="imagen-logo-izq2" src="../assets/img/ibnorca2.jpg">'.

             '<div>
              <center><h2>Recibo de Caja Chica</h2></center><center><h3>Of/Area: '.$oficina.'/'.$area.'</h3></center>
              <table width="100%"><tr><td align="center"><h3></h3></td><td align="right"><h3>Nro.: '.$datos['nro_recibo'].'</h3></td></tr></table>'. 
            '</div>'.
            '</header>';

        $html.='<table class="" width="100%">'.
           '<tbody>';
            $html.='<tr>'.
                '<td class="text-left" width="20%"><b>Proveedor/Beneficiario:</b> </td>'.
                '<td class="text-left">'.namePersonal($datos['cod_personal']).'</td>'.
                '<td class="text-left " width="20%"><b></b></td>'.
                '<td class="text-left"></td>'.
            '</tr>';
            $html.='<tr>'.
                '<td class="text-left " width="20%"><b>La Suma De:</b></td>'.
                '<td class="text-left">'.number_format($monto, 2, '.', ',').'</td>'.
                '<td class="text-left " width="20%"><b></b></td>'.
                '<td class="text-left"></td>'.
            '</tr>';
            $html.='<tr>'.
                '<td class="text-left " width="20%"><b>Por Concepto De:</b></td>'.
                '<td class="text-left" width="70%">'.$datos['observaciones'].'</td>'.
                '<td class="text-left " width="5%"><b></b></td>'.
                '<td class="text-left"></td>'.
            '</tr>';
            $html.='</tbody>';
            $html.='</table>';
            $html.='<br><br><br><table class="" width="100%">'.
           '<tbody>';
            $html.='<tr>'.
                '<td class="text-left " width="20%"><b>Elaborado por:</b></td>'.
                '<td class="text-left"></td>'.
                '<td class="text-left " width="20%"><b>Recibido por:</b></td>'.
                '<td class="text-left"></td>'.
            '</tr>';
            $html.='<tr>'.
                '<td class="text-left " width="20%"><b>Autorizado por:</b></td>'.
                '<td class="text-left"></td>'.
                '<td class="text-left " width="20%"><b></b></td>'.
                '<td class="text-left"></td>'.
            '</tr>';
            $html.='<tr>'.
                '<td class="text-left " width="20%"><b>Pagado por:</b></td>'.
                '<td class="text-left"></td>'.
                '<td class="text-left " width="20%"><b></b></td>'.
                '<td class="text-left"></td>'.
            '</tr>';
      $html.='</tbody>';
$html.=    '</table>';
            
            

$html.='</body>'.
      '</html>';           
descargarPDFCajaChicaHorizontal("IBNORCA - Recibo Caja Chica ".$datos['nro_recibo']." (".$oficina.", ".$area.")",$html);

?>

<?php 
} catch(PDOException $ex){
    echo "Un error ocurrio".$ex->getMessage();
}
?>
