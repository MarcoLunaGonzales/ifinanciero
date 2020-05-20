<?php //ESTADO FINALIZADO
function generarHTMLFacCliente($codigo){
require_once __DIR__.'/../conexion.php';
require '../assets/phpqrcode/qrlib.php';
require_once '../assets/libraries/CifrasEnLetras.php';
//require_once 'configModule.php';
require_once __DIR__.'/../functions.php';
require_once __DIR__.'/../functionsGeneral.php';

$dbh = new Conexion();
$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);//try
set_time_limit(300);
//RECIBIMOS LAS VARIABLES
$tipo_impresion = 2;
try{

    $stmtInfo = $dbh->prepare("SELECT sf.*,t.nombre as nombre_cliente FROM facturas_venta sf,clientes t  
      where sf.cod_cliente=t.codigo and sf.codigo=$codigo");
    $stmtInfo->execute();
    $resultInfo = $stmtInfo->fetch();   
    $cod_factura = $resultInfo['codigo']; 

    $cod_solicitudfacturacion = $resultInfo['cod_solicitudfacturacion'];
    $cod_unidadorganizacional = $resultInfo['cod_unidadorganizacional'];
    $cod_area = $resultInfo['cod_area'];
    $fecha_factura = $resultInfo['fecha_factura'];
    $fecha_limite_emision = $resultInfo['fecha_limite_emision'];
    $cod_cliente = $resultInfo['cod_cliente'];
    $cod_personal = $resultInfo['cod_personal'];
    $razon_social = $resultInfo['razon_social'];
    $nit = $resultInfo['nit'];
    $nro_factura = $resultInfo['nro_factura'];

    $nro_autorizacion = $resultInfo['nro_autorizacion'];
    $codigo_control = $resultInfo['codigo_control'];
    $importe = $resultInfo['importe'];
    $observaciones = $resultInfo['observaciones'];
    $nombre_cliente = $resultInfo['nombre_cliente'];
    if($cod_factura==null || $cod_factura==''){
      $stmtInfo = $dbh->prepare("SELECT sf.* FROM facturas_venta sf  where sf.codigo=$codigo");
      $stmtInfo->execute();
      $resultInfo = $stmtInfo->fetch();   
      $cod_factura = $resultInfo['codigo']; 

      $cod_solicitudfacturacion = $resultInfo['cod_solicitudfacturacion'];
      $cod_unidadorganizacional = $resultInfo['cod_unidadorganizacional'];
      $cod_area = $resultInfo['cod_area'];
      $fecha_factura = $resultInfo['fecha_factura'];
      $fecha_limite_emision = $resultInfo['fecha_limite_emision'];
      $cod_cliente = $resultInfo['cod_cliente'];
      $cod_personal = $resultInfo['cod_personal'];
      $razon_social = $resultInfo['razon_social'];
      $nit = $resultInfo['nit'];
      $nro_factura = $resultInfo['nro_factura'];

      $nro_autorizacion = $resultInfo['nro_autorizacion'];
      $codigo_control = $resultInfo['codigo_control'];
      $importe = $resultInfo['importe'];
      // $descuento_bob = $resultInfo['descuento_bob'];
      $importe=$importe;
      $observaciones = $resultInfo['observaciones'];
      // $nombre_cliente = $resultInfo['nombre_cliente'];
      $nombre_cliente = $razon_social;

    }
    $nombre_ciudad =  obtenerCiudadDeUnidad($cod_unidadorganizacional);

      

    $cantidad=1;

    //para generar factura
     $stmtDesCli = $dbh->prepare("SELECT sf.cantidad from facturas_ventadetalle sf where sf.cod_facturaventa=$cod_factura");
    $stmtDesCli->execute();
    $stmt2DesCli = $dbh->prepare("SELECT sf.descripcion_alterna from facturas_ventadetalle sf where sf.cod_facturaventa=$cod_factura");
    $stmt2DesCli->execute();
     $stmt3DesCli = $dbh->prepare("SELECT sf.precio,sf.descuento_bob from facturas_ventadetalle sf where sf.cod_facturaventa=$cod_factura");
    $stmt3DesCli->execute();
    //copia cliente
    $stmt = $dbh->prepare("SELECT sf.cantidad from facturas_ventadetalle sf where sf.cod_facturaventa=$cod_factura");
    $stmt->execute();
    $stmt2 = $dbh->prepare("SELECT sf.descripcion_alterna from facturas_ventadetalle sf where sf.cod_facturaventa=$cod_factura");
    $stmt2->execute();
     $stmt3 = $dbh->prepare("SELECT sf.precio,sf.descuento_bob from facturas_ventadetalle sf where sf.cod_facturaventa=$cod_factura");
    $stmt3->execute();
    //copia contabilidad
    $stmt_conta = $dbh->prepare("SELECT sf.cantidad from facturas_ventadetalle sf where sf.cod_facturaventa=$cod_factura");
    $stmt_conta->execute();
    $stmt_conta2 = $dbh->prepare("SELECT sf.descripcion_alterna from facturas_ventadetalle sf where sf.cod_facturaventa=$cod_factura");
    $stmt_conta2->execute();
     $stmt_conta3 = $dbh->prepare("SELECT sf.precio,sf.descuento_bob from facturas_ventadetalle sf 
    where sf.cod_facturaventa=$cod_factura");
    $stmt_conta3->execute();

//primero guardamos la factura del cliente
  $nit_empresa=obtenerValorConfiguracionFactura(9);
$html = '';
$html.='<html>'.
            '<head>'.
                '<!-- CSS Files -->'.
                '<link rel="icon" type="image/png" href="../assets/img/favicon.png">'.
                '<link href="../assets/libraries/plantillaPDFFActura.css" rel="stylesheet" />'.
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
            '<table  style="width: 100%;">
              <thead>
                <tr>
                  <td align="center" width="37%">
                    <img class="imagen-logo-izq_2" src="../assets/img/ibnorca2.jpg">
                    <small><h4 >
                      '.obtenerValorConfiguracionFactura(1).'<br>
                      '.obtenerValorConfiguracionFactura(2).'<br>
                      '.obtenerValorConfiguracionFactura(3  ).'<br>
                      Teléfonos:'.obtenerValorConfiguracionFactura(4).' * Fax: '.obtenerValorConfiguracionFactura(12).'<br>
                      Web:'.obtenerValorConfiguracionFactura(10).' * E-mail:'.obtenerValorConfiguracionFactura(11).' * '.obtenerValorConfiguracionFactura(13).' <br>
                      '.obtenerValorConfiguracionFactura(14).'<br>
                    </h4></small> 
                  </td>
                  <td  valign="top" width="26%"><div id="header_titulo_texto_grande" >FACTURA</div></td>
                  <td width="37%">
                    <table style="width: 100%;border: black 1px solid;text-align: left;">
                        <tr align="left">
                          <td>
                              NIT:<br>
                              FACTURA N°:<br>
                              AUTORIZACIÓN N°:
                          </td>
                          <td>
                              '.$nit_empresa.'<br>
                              '.$nro_factura.'<br>
                              '.$nro_autorizacion.'
                          </td>
                        </tr>
                    </table>
                    <small><h4>
                      ORIGINAL: CLIENTE<br><br>
                      * '.obtenerValorConfiguracionFactura(6).'<br><br>
                      '.$nombre_ciudad.', '.obtenerFechaEnLetra($fecha_factura).'<br>
                    </h4></small>
                  </td>
                </tr>
              </thead>
            </table>';
            $html.='<table class="table">'.
              '<tr class="bold table-title text-left">'.
                  '<td  class="td-border-none" width="9%"><b>Señor(es):</b></td>'.
                  '<td  colspan="2" class="td-border-none" ><h4><b>'.$nombre_cliente.'</b></h4></td>'.                
                  '<td  colspan="2" class="td-border-none" width="30%"><h4><b>NIT/CI:</b> &nbsp;&nbsp;'.$nit.'</h4></td>'.                
                '</tr>'.
            '</table>';
     $html.='<table class="table2">'.
              '<thead>'.                
                '<tr>'.
                  '<td width="10%" align="center">CANTIDAD</td> 
                  <td width="80%" align="center">DESCRIPCIÓN</td>                   
                  <td width="10%" align="center"><b>SUBTOTAL</b></td>
                </tr>
              </thead>';

              $suma_total=0;
              $html.='<tbody>
                <tr>';
                if($tipo_impresion==1){//tipo de impresion normal
                  $html.='<td valign="top" height="8%" class="text-right"><h5>'.formatNumberDec($cantidad).'</h5></td>'.
                  '<td valign="top" height="8%"><h5>'.$observaciones.'</h5></td>'.
                  '<td valign="top" height="8%" class="text-right"><h5>'.formatNumberDec($importe).'</h5></td>';
                  $suma_total+=$importe;
                }else{//imporesion detallada
                  $html.='<td valign="top" height="8%" class="text-right"><h5>';
                  while ($row = $stmtDesCli->fetch()) 
                  {
                    $html.=formatNumberDec($row["cantidad"]).'<br>';
                  }
                  $html.='</h5></td> 
                  <td valign="top" height="8%"><h5>';
                  while ($row = $stmt2DesCli->fetch()) 
                  {
                    $html.=$row["descripcion_alterna"].'<br>';
                  }
                  $html.='</h5></td>                   
                  <td valign="top" height="8%" class="text-right"><h5>';
                  while ($row = $stmt3DesCli->fetch()) 
                  {
                    $precio=$row["precio"];
                    $descuento_bob=$row["descuento_bob"];
                    $precio=$precio-$descuento_bob;
                    $html.=formatNumberDec($precio).'<br>';
                    $suma_total+=$precio;                    
                  }
                  $html.='</h5></td>';
                } 
                  
                $html.='</tr>';
              

              $html.='<tr>'.
                '<td height="8%" colspan="3">
                  <table class="table" style="border-top: hidden;border-bottom: hidden;border-right: hidden;border-left: hidden;">
                    <thead>
                      <tr>
                        <td rowspan="3" width="20%" height="8%">';                          
                            //GENERAMOS LA CADENA DEL QR
                            $contenidoQr=$nit_empresa."|".$nro_factura."|".$nro_autorizacion."|".$fecha_factura."|".$importe."|".$importe."|".$codigo_control."|".$nit."|0|0|0|0";
                            $dir = 'qr_temp/';
                            if(!file_exists($dir)){
                                mkdir ($dir);}
                            $fileName = $dir.$cod_factura.'.png';
                            $tamanio = 2; //tamaño de imagen que se creará
                            $level = 'Q'; //tipo de precicion Baja L, mediana M, alta Q, maxima H
                            $frameSize = 1; //marco de qr
                            $contenido = $contenidoQr;
                            QRcode::png($contenido, $fileName, $level,$tamanio,$frameSize);
                            $html.= '<img src="'.$fileName.'"/>';
                            // echo '<img src="'.$fileName.'"/>';        
                        $html.='</td>
                        <td width="60%" style="border-right: hidden" rowspan="2">';

                          $entero=floor($importe);
                          $decimal=$importe-$entero;
                          $centavos=round($decimal*100);
                          if($centavos<10){
                            $centavos="0".$centavos;
                          }
                          $html.='<p class="bold table-title">Son: '.ucfirst(CifrasEnLetras::convertirNumeroEnLetras($entero)).'      '.$centavos.'/100 Bolivianos</p>'; 
                          $html.='</td>
                        <td width="25%" align="right" style="border-left: hidden" rowspan="2"><b>Total Bs &nbsp;&nbsp;&nbsp;&nbsp;'.formatNumberDec($suma_total).'</b></td>
                      </tr>
                      <tr>
                        
                      </tr>
                      <tr>
                        <td width="50%" style="border-right: hidden"><b>CODIGO DE CONTROL:&nbsp;&nbsp;&nbsp;&nbsp;</b> '.$codigo_control.'</td>
                        <td width="50%" align="right" style="border-left: hidden"><b>FECHA LÍMITE DE EMISIÓN:&nbsp;&nbsp;&nbsp;&nbsp;</b>'.$fecha_limite_emision.'</td> 
                      </tr>
                    </thead>
                  </table>
                </td>
              </tr>'.
             
             '</tbody>'.                        
          '</table>'; 
          $html.='<table class="table3" >
            <tr align="center"><td>&quot;'.obtenerValorConfiguracionFactura(7).'&quot;<br>&quot;'.obtenerValorConfiguracionFactura(8).'&quot;</td></tr>
          </table><br><br><hr>';          
        '</header>';
$html.='</body>'.
      '</html>'; 

  } catch(PDOException $ex){
    $html="ERROR";
  }
  return $html;
}
?>
