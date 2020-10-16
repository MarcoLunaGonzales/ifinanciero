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
    $stmt = $dbh->prepare("SELECT codigo,monto,DATE_FORMAT(fecha,'%d/%m/%Y')as fecha_x,observaciones,nro_recibo,cod_uo,cod_area,cod_estadoreferencial from caja_chicadetalle where  cod_cajachica=$codigo 
UNION
SELECT codigo,monto,DATE_FORMAT(fecha,'%d/%m/%Y')as fecha_x,observaciones,0 as nro_recibo,0 as cod_uo,0 as cod_area,cod_estadoreferencial from caja_chicareembolsos where cod_estadoreferencial=1 and cod_cajachica=$codigo ORDER BY nro_recibo");
    $stmt->execute();    
        //==================================================================================================================
    //datos caja chica
    $stmtInfo = $dbh->prepare("SELECT tc.nombre,tc.cod_uo,c.monto_inicio,c.observaciones,c.numero,c.fecha,c.fecha_cierre from caja_chica c,tipos_caja_chica tc where c.cod_tipocajachica=tc.codigo and c.codigo=$codigo");
    $stmtInfo->execute();
    $resultInfo = $stmtInfo->fetch();
    //$codigo = $result['codigo'];
    $nombre_tcc = $resultInfo['nombre'];
    $cod_uo = $resultInfo['cod_uo'];
    $monto_inicio_cc = $resultInfo['monto_inicio'];
    $detalle_cc = $resultInfo['observaciones'];
    $numero_cc = $resultInfo['numero'];
    $fecha_inicio_cc = $resultInfo['fecha'];
    $fecha_cierre_cc = $resultInfo['fecha_cierre'];
    $cod_uo_x=$resultInfo['cod_uo'];
    $nombre_uo_x=nameUnidad($cod_uo_x);
    // $contenido='CAJA CHICA N° '.$numero_cc." De Fecha: ".$fecha_inicio_cc." a ".$fecha_cierre_cc;
    $contenido='CAJA CHICA N° '.$numero_cc; 
    //DAtos de reembolso
    // $stmtInfoReembolso = $dbh->prepare("SELECT observaciones from caja_chicareembolsos where cod_cajachica=$codigo order by codigo desc limit 1");
    // $stmtInfoReembolso->execute();
    // $resultInfoReembolso = $stmtInfoReembolso->fetch();    
    // $observaciones_reembolso = $resultInfoReembolso['observaciones'];
    // if($observaciones_reembolso==null || $observaciones_reembolso=='' || $observaciones_reembolso==' '){
    //   $observaciones_reembolso ="REPOSICION";
    // }

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
            '<img class="imagen-logo-der2" src="../assets/img/ibnorca2.jpg">'.

             '<div>
              <h4 >
              '.obtenerValorConfiguracionEmpresa(3).'<br>
              OFICINA: '.$nombre_uo_x.'<br>
              DIRECCION: '.obtenerValorConfiguracionEmpresa(6).'<br>
              <b>NIT:</b>'.obtenerValorConfiguracionEmpresa(4).'<br>            
              </h4> 
            </div>'.
            '</header>';
        $html.='<table class="table">'.
            '<thead>'.
            '<tr class="bold table-title text-center">'.
              '<td colspan="8"><small>'.$nombre_tcc.'</td>
            </tr>'.
            '<tr class="bold table-title text-center">'.
              '<td colspan="8"><small>'.$contenido.'</td>
            </tr>'.
            '<tr class="bold table-title text-center">'.
              '<td width="10%"><small>Fecha</small</td> 
              <td width="6%"><small>Área</small></td>                   
              <td width="40%"><small>Descripción</small></td>
              <td width="10%"><small>N° Recibo</small></td>
              <td width="10%"><small>Factura</small></td>                    
              <td width="8%"><small>Ingreso</small></td>                    
              <td width="8%"><small>Egreso</small></td>
              <td width="8%"><small>Saldo</small></td>
            </tr>'.
           '</thead>'.
           '<tbody>';
            $index=1;
            $saldo_inicial=$monto_inicio_cc;
            $html.='<tr>'.
                '<td class="text-left small"></td>'.
                '<td class="text-left small"></td>'.
                '<td class="text-center small"><b>ASIGNACION DE FONDO</b></td>'.
                '<td class="text-center small"></td>'.
                '<td class="text-center small"></td>'.
                '<td class="text-center small">'.formatNumberDec($monto_inicio_cc).'</td>'.
                '<td class="text-center small"></td>'.
                '<td class="text-right small"><b>'.formatNumberDec($monto_inicio_cc).'</b></td>
            </tr>';
            // '<tr>'.
            //     '<td class="text-left small"></td>'.
            //     '<td class="text-left small"></td>'.
            //     '<td class="text-left small"><b>'.$observaciones_reembolso.'</b></td>'.
            //     '<td class="text-center small"></td>'.
            //     '<td class="text-center small"></td>'.
            //     '<td class="text-center small"></td>'.
            //     '<td class="text-center small"></td>'.
            //     '<td class="text-right small">'.formatNumberDec($monto_inicio_cc).'</td>
            // </tr>';
            $ingresos='';
            $total_ingresos=$monto_inicio_cc;
            $total_egresos=0;
            while ($row = $stmt->fetch()) 
            {
              $sw_rembolso=false;//indicamos que es un reembolso
              if($row['nro_recibo']==0 && $row['cod_uo']==0 && $row['cod_area']==0 ){
                $sw_rembolso=true;
              }
              $cod_cajachicadetalle=$row['codigo'];
              $nombre_uo=abrevUnidad($row['cod_uo']);
              $nombre_area=abrevArea($row['cod_area']);
              $nro_recibo=$row['nro_recibo'];
              $monto_detalle =$row['monto'];

              //detalle sr
              $codigoSolicitud=encuentraDatosSolicitudRecursosDesdeCajaChica($cod_cajachicadetalle);
              $estiloBoton="btn-default";
              $descripcionSR="list";
              if($codigoSolicitud[0]>0){
                  $estiloBoton="btn-info";
                  $descripcionSR="SR";
                  $importeSolX=$monto_detalle;
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
                  $monto_detalle=$montoImporte; 
              }

              $cod_estadoreferencial_x=$row['cod_estadoreferencial'];
              $observaciones=$row['observaciones'];
              if($cod_estadoreferencial_x==2){
                $monto_detalle=0;
                $observaciones="***ANULADO***";
              }
              //nro factura
              if(!$sw_rembolso){
                $stmtFactura = $dbh->prepare("SELECT nro_factura from facturas_detalle_cajachica where cod_cajachicadetalle=$cod_cajachicadetalle");
                $stmtFactura->execute();
                $cont_facturas=0;
                $nro_factura='';
                while ($rowFacturas = $stmtFactura->fetch()) 
                {
                  $nro_factura=$rowFacturas['nro_factura'];
                  $cont_facturas++;
                }
                if($cont_facturas>1)$nro_factura="VARIOS";
                $saldo_inicial=$saldo_inicial-$monto_detalle;                  
                $total_egresos+=$monto_detalle;
              }else{
                $nombre_uo="";
                $nombre_area="";
                $total_ingresos+=$monto_detalle;
                $saldo_inicial=$saldo_inicial+$monto_detalle;                
                $nro_recibo='';
              }

              

              $html.='<tr>'.                      
                            '<td class="text-center small">'.$row['fecha_x'].'</td>';
                            if(!$sw_rembolso){
                              $html.='<td class="text-left small">'.$nombre_uo.'/'.$nombre_area.'</td>';
                            }else{
                              $html.='<td class="text-left small"></td>';
                            }
                            $html.='<td class="text-left small">'.$observaciones.'</td>'.
                            '<td class="text-center small">'.$nro_recibo.'</td>'.
                            '<td class="text-center small">'.$nro_factura.'</td>';
                            if(!$sw_rembolso){
                              $html.='<td class="text-right small">'.$ingresos.'</td>'.
                              '<td class="text-right small">'.formatNumberDec($monto_detalle).'</td>';
                            }else{
                              $html.='<td class="text-right small">'.formatNumberDec($monto_detalle).'</td>'.
                              '<td class="text-right small"></td>';
                            }
                            $html.='<td class="text-right small">'.formatNumberDec($saldo_inicial).'</td>
                    </tr>';
              }
              $html.='<tr>'.                      
                            '<td class="text-left small"></td>'.
                            '<td class="text-center small"></td>'.
                            '<td class="text-left small"></td>'.
                            '<td class="text-center small"></td>'.
                            '<td class="text-center small"></td>'.
                            '<td class="text-right small"></td>'.
                            '<td class="text-right small"></td>'.
                            '<td class="text-right small">'.formatNumberDec($saldo_inicial).'</td>
                    </tr>';
      $html.='</tbody>';
$html.=    '</table>';
            
            $html.='<table class="table2">'.
                        '<tbody>'.                        
                        '<tr>'.
                          '<td width="10%"></td> 
                          <td width="6%"></td>                   
                          <td width="40%" class="text-left small"><b>SUBTOTALES</b></td>
                          <td width="10%"></td>
                          <td width="10%"></td>                    
                          <td width="8%" class="text-right small">'.formatNumberDec($total_ingresos).'</td>
                          <td width="8%" class="text-right small">'.formatNumberDec($total_egresos).'</td>
                          <td width="8%"></td>
                        </tr>'.
                        '<tr>'.
                          '<td width="10%"></td> 
                          <td width="6%"></td>                   
                          <td width="40%" class="text-left small"><b>TOTAL RENDICIÓN DE FONDO</b></td>
                          <td width="10%"></td>
                          <td width="10%"></td>                    
                          <td width="8%"></td>
                          <td width="8%" class="text-right small"><b>'.formatNumberDec($total_egresos).'</b></td>
                          <td width="8%"></td>
                        </tr>'.
                        '<tr>'.
                          '<td width="10%"></td> 
                          <td width="6%"></td>                   
                          <td width="40%"><b>SALDO A RESPONDER</b></td>
                          <td width="10%"></td>
                          <td width="10%"></td>                    
                          <td width="8%"></td>
                          <td width="8%" class="text-right small"><b>'.formatNumberDec($saldo_inicial).'</b></td>
                          <td width="8%"></td>
                        </tr>'.
                       '</tbody>'.                        
                    '</table>'; 

$html.='</body>'.
      '</html>';           
descargarPDFCajaChica("IBNORCA - ".$unidadC." (".$tipoC.", ".$numeroC.")",$html);

?>

<?php 
} catch(PDOException $ex){
    echo "Un error ocurrio".$ex->getMessage();
}
?>
