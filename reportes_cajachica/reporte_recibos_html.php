<?php
function generarHTMLReciboCajaChica($codigo,$auxiliar,$tipo_admin,$estado_factura){
    require_once __DIR__.'/../conexion.php';
//require_once 'configModule.php';
require_once __DIR__.'/../functions.php';
require_once __DIR__.'/../functionsGeneral.php';
require_once '../assets/libraries/CifrasEnLetras.php';

$dbh = new Conexion();
$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);//try
//RECIBIMOS LAS VARIABLES
$usd=6.96;
try{
  $datos=encuentraDatosCajaChicaDetalle($codigo);
  $oficina=abrevUnidad_solo($datos['cod_uo']); 
  $area=abrevArea_solo($datos['cod_area']); 
  $monto=$datos['monto'];
  $formaPago=nameTipoPago($datos['cod_tipopago']); 
  
  $tituloComprobante="";
  $codComprobanteDevengado=obtenerComprobanteDevengadoEstadoCuentasCajaChicaDetalle($codigo);
  if($codComprobanteDevengado!=0){
    $tituloComprobante="   (".abrevUnidad_solo(obtenerCodigoUnidadComprobante($codComprobanteDevengado))."  ".nombreComprobante($codComprobanteDevengado).")";
  }
  switch ($datos['cod_tipopago']) {
    case 2:
      $checkboxTrans="checked";
      $checkboxEfectivo="";
      break;
    case 3:
      $checkboxTrans="";
      $checkboxEfectivo="checked";
      break;
    default:
      $checkboxTrans="";
      $checkboxEfectivo="";
      break;
  }
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

      if($fecha_registro==0){
        $fecha_registro="";
      }
      if($fecha_revisado==0){
        $fecha_revisado="";
      }
      if($fecha_procesado==0){
        $fecha_procesado="";
      }

      if($codigoSolicitud[0]==0){
         $personal_registro=namePersonal($datos['cod_personal']);
         $fecha_registro=strftime('%d/%m/%Y',strtotime($datos['fecha']));
         $fecha_procesado=$fecha_registro;
         $personal_procesado=$personal_registro;
      }
$html = '';
if($tipo_admin==1){
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
			// $html.=  '<header class="header">';
		}elseif($tipo_admin%2==0){
			$html.='<br>';
		}

$html.=  '';
       $html.='<div style="padding:15px !important;border:2px solid #000000;border-radius:20px;" ><img class="" style="padding-top: -2px;left: 30px;top: 20px;width:130px;height:130px;" src="../assets/img/ibnorca2.jpg">'.

             '<div><br>
              <center><h1 style="padding-top:-120px;">RECIBO DE CAJA CHICA</h1></center><center><h2 style="padding-top:-60px;">Of/Area: '.$oficina.'/'.$area.'</h2></center>
              <table style="padding-top:-70px;" width="100%"><tr><td align="center"><label></label></td><td align="right"><label style="color:#F31111; font-weight:bold; font-size:22px;">Nº &nbsp;&nbsp;&nbsp;&nbsp;'.$datos['nro_recibo'].'</label></td></tr>
              <tr><td><br></td><td></td></tr>
                <tr><td align="center" width="76%"><label></label></td><td align="right"><span style="display:inline-block; width:35px;"><label style="font-weight:bold; font-size:18px;"><p style="margin-top:4px;">Bs.&nbsp;</p> &nbsp;&nbsp;&nbsp;&nbsp;</label></span><span style="width:115px;display:inline-block;height:18px; border:1px solid #000000;border-radius:5px; text-align:right;padding:4px;font-size:18px;">'.number_format($monto, 2, '.', ',').'</span></td></tr>'.

                //<tr><td align="center" width="76%"><label></label></td><td align="right"><span style="display:inline-block; width:35px;"><label style="font-weight:bold; font-size:12px;">$us. &nbsp;&nbsp;&nbsp;&nbsp;</label></span><span style="width:115px;display:inline-block;height:14px; border:1px solid #000000;border-radius:5px; text-align:right;padding:4px;font-size:14px;">'.number_format($monto/$usd, 2, '.', ',').'</span></td></tr>
              '</table>'. 
            '</div>';
        $html.='<table class="" width="100%" style="font-size:12px;">'.
           '<tbody>';
            $html.='<tr>'.
                '<td class="text-left" width="20%"><b>Proveedor/Beneficiario</b> </td>'.
                '<td class="text-left">:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.nameProveedor($datos["cod_proveedores"]).'</td>'.
            '</tr>';
            $html.='<tr><td colspan="2"><br></td></tr><tr>'.
                '<td class="text-left " width="20%"><b>La suma de</b></td>'.
                '<td class="text-left">:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.ucfirst(CifrasEnLetras::convertirNumeroEnLetras($entero)).'      '.$centavos.'/100 '.' Bolivianos</td>'.
            '</tr>';
            $html.='<tr>'.
                '<td class="text-left " width="20%"><b>Por concepto de</b></td>'.
                '<td class="text-left small" width="75%" valign="bottom"><label style="position:absolute;top:25px;">:</label><small><p style="padding-left:20px;">'.$datos['observaciones'].' '.$tituloComprobante.'</p></small></td>'.
            '</tr>';
            $html.='</tbody>';
            $html.='</table>';
            $html.='<br><table class="" width="100%" style="font-size:12px;">'.
           '<tbody>';
            $html.='<tr>'.
                '<td class="text-left " style="padding:5px;" width="20%"><b>Elaborado por</b></td>'.
                '<td class="text-left small" style="padding:5px;" width="50%">:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$personal_registro.'</td>'.
                '<td class="text-left " style="padding:5px;" width="10%"><b>Fecha</b></td>'.
                '<td class="text-left small" style="padding:5px;" width="20%">:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$fecha_registro.'</td>'.
            '</tr>';
            if($codigoSolicitud[0]>0){
            $html.='<tr>'.
                '<td class="text-left " style="padding:5px;" width="20%"><b>Autorizado por</b></td>'.
                '<td class="text-left small" style="padding:5px;" width="50%">:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$personal_revisado.'</td>'.
                '<td class="text-left " style="padding:5px;" width="10%"><b>Fecha</b></td>'.
                '<td class="text-left small" style="padding:5px;" width="20%">:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$fecha_revisado.'</td>'.
            '</tr>';
              
            }
            $html.='<tr>'.
                '<td class="text-left " style="padding:5px;" width="20%"><b>Pagado por</b></td>'.
                '<td class="text-left small" style="padding:5px;" width="50%">:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$personal_procesado.'</td>'.
                '<td class="text-left " style="padding:5px;" width="10%"><b>Fecha</b></td>'.
                '<td class="text-left small" style="padding:5px;" width="20%">:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$fecha_procesado.'</td>'.
            '</tr>';
            $html.='</tbody>';
            $html.='</table><table class="" width="100%" style="font-size:12px;"><tr><td colspan="4"><br></td></tr><tr>'.
                '<td class="text-left " style="padding:5px;" width="20%"><b>Forma de pago</b></td>'.
                '<td class="text-left small" style="padding:5px;" width="30%">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Transferencia <input type="checkbox" '.$checkboxTrans.'></td>'.
                '<td class="text-left " style="padding:5px;" width="10%">Efectivo <input type="checkbox" '.$checkboxEfectivo.'></td>'.
                '<td class="text-left small" style="padding:5px;" width="40%">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;________________________________</td>'.
            '</tr>';
      $html.='</tbody>';
$html.=    '</table>';
            
            

$html.='</div><br><br>';
if($tipo_admin%2==0){
			$html.='<div style="page-break-after:always;"></div>';
}
	    return $html."@@@@@@".$area."@@@@@@".$monto;
	} catch (Exception $e) {
		$html="ERROR@@@@@@0@@@@@@0";
		return $html;		
	}
}

?>