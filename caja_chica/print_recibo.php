<?php //ESTADO FINALIZADO

require_once __DIR__.'/../conexion.php';
require '../assets/phpqrcode/qrlib.php';

//require_once 'configModule.php';
require_once __DIR__.'/../functions.php';
require_once __DIR__.'/../functionsGeneral.php';
require_once '../assets/libraries/CifrasEnLetras.php';

$dbh = new Conexion();
$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);//try
//RECIBIMOS LAS VARIABLES
$usd=6.96;
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

          $objeto_sol=2708;
          $codPersonalX=obtenerPersonalSolicitanteRecursos($codigoSolicitud[0]);
          $nombreEstado_registro=obtenerNombreEstadoSol(1);
          $personal_registro=namePersonal($codPersonalX);
          $fecha_registro=obtenerFechaSinHoraCambioEstado($objeto_sol,$codigoSolicitud[0],2721);//estado registro

          
          $userRevisado=obtenerPersonaCambioEstado($objeto_sol,$codigoSolicitud[0],2722);  //autorizado       
          $nombreEstado_revisado=obtenerNombreEstadoSol(4);
          if($userRevisado==0){
             $fecha_revisado="";    
             $personal_revisado="";    
          }else{
             $personal_revisado=namePersonal($userRevisado);
             $fecha_revisado=obtenerFechaSinHoraCambioEstado($objeto_sol,$codigoSolicitud[0],2722);
          }


          $userprocesado=obtenerPersonaCambioEstado($objeto_sol,$codigoSolicitud[0],2725);//contabiliado        
          $nombreEstado_procesado=obtenerNombreEstadoSol(9);
          if($userprocesado==0){
             $personal_procesado="";    
             $fecha_procesado="";
          }else{
             $personal_procesado=namePersonal($userprocesado);    
             $fecha_procesado=obtenerFechaSinHoraCambioEstado($objeto_sol,$codigoSolicitud[0],2725);
          }

          
      $entero=floor((float)number_format($monto, 2, '.', ''));
      $decimal=(float)number_format($monto, 2, '.', '')-$entero;
      $centavos=round($decimal*100);
      if($centavos<10){
        $centavos="0".$centavos;
      }

$html = '';
$html.='<html>'.
            '<head>'.
                '<!-- CSS Files -->'.
                '<link rel="icon" type="image/png" href="../assets/img/favicon.png">'.
                '<link href="../assets/libraries/plantillaPDFCajaChicaRecibo.css" rel="stylesheet" />'.
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
$html.=  '';
       $html.='<div style="padding:15px !important;border:2px solid #000000;border-radius:20px;" ><img class="imagen-logo-izq2" style="position: absolute;padding-top: -2px;left: 30px;top: 20px;width:70px;height:70px;" src="../assets/img/ibnorca2.jpg">'.

             '<div>
              <center><h2>RECIBO DE CAJA CHICA</h2></center><center><h3>Of/Area: '.$oficina.'/'.$area.'</h3></center>
              <table style="padding-top:-10px;" width="100%"><tr><td align="center"><label></label></td><td align="right"><label style="color:#F31111; font-weight:bold; font-size:16px;">Nº &nbsp;&nbsp;&nbsp;&nbsp;'.$datos['nro_recibo'].'</label></td></tr>
                <tr><td align="center" width="67%"><label></label></td><td align="right"><span style="display:inline-block; width:35px;"><label style="font-weight:bold; font-size:12px;">Bs. &nbsp;&nbsp;&nbsp;&nbsp;</label></span><span style="width:115px;display:inline-block;height:14px; border:1px solid #000000;border-radius:5px; text-align:right;padding:4px;font-size:14px;">'.number_format($monto, 2, '.', ',').'</span></td></tr>
                <tr><td align="center" width="67%"><label></label></td><td align="right"><span style="display:inline-block; width:35px;"><label style="font-weight:bold; font-size:12px;">$us. &nbsp;&nbsp;&nbsp;&nbsp;</label></span><span style="width:115px;display:inline-block;height:14px; border:1px solid #000000;border-radius:5px; text-align:right;padding:4px;font-size:14px;">'.number_format($monto/$usd, 2, '.', ',').'</span></td></tr>
              </table>'. 
            '</div>';
        $html.='<table class="" width="100%" style="font-size:12px;">'.
           '<tbody>';
            $html.='<tr>'.
                '<td class="text-left" width="20%"><b>Proveedor/Beneficiario</b> </td>'.
                '<td class="text-left">: '.nameProveedor($datos["cod_proveedores"]).'</td>'.
                '<td class="text-left " width="20%"><b></b></td>'.
                '<td class="text-left"></td>'.
            '</tr>';
            $html.='<tr>'.
                '<td class="text-left " width="20%"><b>La Suma De:</b></td>'.
                '<td class="text-left">: '.ucfirst(CifrasEnLetras::convertirNumeroEnLetras($entero)).'      '.$centavos.'/100 '.' Bolivianos</td>'.
                '<td class="text-left " width="20%"><b></b></td>'.
                '<td class="text-left"></td>'.
            '</tr>';
            $html.='<tr>'.
                '<td class="text-left " width="20%"><b>Por Concepto De</b></td>'.
                '<td class="text-left small" width="70%">:<small> '.$datos['observaciones'].'</small></td>'.
                '<td class="text-left " width="5%"><b></b></td>'.
                '<td class="text-left"></td>'.
            '</tr>';
            $html.='</tbody>';
            $html.='</table>';
            $html.='<br><br><br><table class="" width="100%" style="font-size:12px;">'.
           '<tbody>';
            $html.='<tr>'.
                '<td class="text-left " style="padding:5px;" width="20%"><b>Elaborado por</b></td>'.
                '<td class="text-left small" style="padding:5px;" width="50%">: '.$personal_registro.'</td>'.
                '<td class="text-left " style="padding:5px;" width="10%"><b>Fecha</b></td>'.
                '<td class="text-left small" style="padding:5px;" width="20%">: '.$fecha_registro.'</td>'.
            '</tr>';
            $html.='<tr>'.
                '<td class="text-left " style="padding:5px;" width="20%"><b>Autorizado por</b></td>'.
                '<td class="text-left small" style="padding:5px;" width="50%">: '.$personal_revisado.'</td>'.
                '<td class="text-left " style="padding:5px;" width="10%"><b>Fecha</b></td>'.
                '<td class="text-left small" style="padding:5px;" width="20%">: '.$fecha_revisado.'</td>'.
            '</tr>';
            $html.='<tr>'.
                '<td class="text-left " style="padding:5px;" width="20%"><b>Pagado por</b></td>'.
                '<td class="text-left small" style="padding:5px;" width="50%">: '.$personal_procesado.'</td>'.
                '<td class="text-left " style="padding:5px;" width="10%"><b>Fecha</b></td>'.
                '<td class="text-left small" style="padding:5px;" width="20%">: '.$fecha_procesado.'</td>'.
            '</tr>';
            $html.='<tr>'.
                '<td class="text-left " style="padding:5px;" width="20%"><b>Recibido por</b></td>'.
                '<td class="text-left small" style="padding:5px;" width="50%">: </td>'.
                '<td class="text-left " style="padding:5px;" width="10%"><b>Fecha</b></td>'.
                '<td class="text-left small" style="padding:5px;" width="20%">: </td>'.
            '</tr>';
      $html.='</tbody>';
$html.=    '</table>';
            
            

$html.='</div></body>'.
      '</html>';           
descargarPDFCajaChicaHorizontal("IBNORCA - Recibo Caja Chica ".$datos['nro_recibo']." (".$oficina.", ".$area.")",$html);

?>

<?php 
} catch(PDOException $ex){
    echo "Un error ocurrio".$ex->getMessage();
}
?>
