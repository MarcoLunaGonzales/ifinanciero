<?php
function generarHTMLFacCliente($codigo,$auxiliar,$tipo_admin){
	require_once __DIR__.'/../conexion.php';
	if($tipo_admin==1){
		require '../assets/phpqrcode/qrlib.php';
		require_once '../assets/libraries/CifrasEnLetras.php';
	}
	//require_once 'configModule.php';
	require_once __DIR__.'/../functions.php';
	require_once __DIR__.'/../functionsGeneral.php';

	$dbh = new Conexion();
	$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);//try
	set_time_limit(300);
	//RECIBIMOS LAS VARIABLES
	// $codigo = $_GET["codigo"];
	// $auxiliar = $_GET["tipo"];

	$codigo = $codigo;
	$auxiliar =$auxiliar; //de dónde llega la solicitud para impresión 1=lista facturas (cod_factura) / 2=lista solicitudes (cod_sol_Fact)//3=lista facturas (cod_factura)tienda virtual
	$tipo_admin=$tipo_admin;//1 original cliente completo(abre y cierra de html), 2 original cliente (abre html), 3 copia contabilidad (cierra html), 4 original (abre y cierra html), 5 copia(abre y cierra html)
	$tipo_impresion=2;//tipo de impresión 1 sin detalles, 2 detalladamente
	try {
		if($auxiliar==1){//
		    $stmtInfo = $dbh->prepare("SELECT sf.* FROM facturas_venta sf where sf.codigo=$codigo");
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
		    $cod_dosificacionfactura = $resultInfo['cod_dosificacionfactura'];
		    $observaciones = $resultInfo['observaciones'];
		    $nombre_cliente = $razon_social;
		}elseif($auxiliar==2){
		    $stmtInfo = $dbh->prepare("SELECT sf.* FROM facturas_venta sf  where sf.cod_solicitudfacturacion=$codigo");
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
		    $cod_dosificacionfactura = $resultInfo['cod_dosificacionfactura'];		    
		    $importe=$importe;
		    $observaciones = $resultInfo['observaciones'];
		    // $nombre_cliente = $resultInfo['nombre_cliente'];
		    $nombre_cliente = $razon_social;
		}else{//para la tiendA
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
			$cod_dosificacionfactura = $resultInfo['cod_dosificacionfactura'];
			$importe=$importe;
			$observaciones = $resultInfo['observaciones'];
			// $nombre_cliente = $resultInfo['nombre_cliente'];
			$nombre_cliente = $razon_social;
		}		
		$leyenda=obtener_dato_dosificacion($cod_dosificacionfactura);//sacmos la leyenda
		$nombre_ciudad =  obtenerCiudadDeUnidad($cod_unidadorganizacional);
		$cantidad=1;
		//para generar factura
		$stmtDesCli = $dbh->prepare("SELECT sf.cantidad from facturas_ventadetalle sf where sf.cod_facturaventa=$cod_factura");
		$stmtDesCli->execute();
		$stmt2DesCli = $dbh->prepare("SELECT sf.descripcion_alterna from facturas_ventadetalle sf where sf.cod_facturaventa=$cod_factura");
		$stmt2DesCli->execute();
		$stmt3DesCli = $dbh->prepare("SELECT sf.precio,sf.descuento_bob from facturas_ventadetalle sf where sf.cod_facturaventa=$cod_factura");
		$stmt3DesCli->execute();
		//primero guardamos la factura del cliente
		$nit_empresa=obtenerValorConfiguracionFactura(9);
		$html = '';
		if($tipo_admin==1 || $tipo_admin==2 || $tipo_admin==4 || $tipo_admin==5){
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
			// $html.=  '<header class="header">';
		}else{
			$html.='<hr>';
		}
		$html.='<div  style="height: 49%">';
    		$html.='<table  style="width: 100%;">
              	<thead>
	                <tr>
	                  	<td align="center" width="37%">
		                    <img class="imagen-logo-izq_2" src="../assets/img/logo_ibnorca_origen_2.png">
		                    <br><br>
		                    <span><b><u>
		                      '.obtenerValorConfiguracionFactura(1).'</u><br>
		                      '.obtenerValorConfiguracionFactura(2).'</b></span><br>
		                      <span><small><small><b>
		                      '.obtenerValorConfiguracionFactura(3).'<br>
		                      Teléfonos:'.obtenerValorConfiguracionFactura(4).'<br>
		                      Web:'.obtenerValorConfiguracionFactura(10).' * E-mail:'.obtenerValorConfiguracionFactura(11).'<br>'.obtenerValorConfiguracionFactura(13).' <br>
		                      '.obtenerValorConfiguracionFactura(14).'
		                    </b></span></small></small>
		                </td>
	                    <td  valign="top" width="26%"><div id="header_titulo_texto_grande" ><br>FACTURA</div></td>
	                    <td width="37%">
		                    <table style="width: 100%;border: black 1px solid;text-align: left;">
		                        <tr align="left">
		                          <td width="40%">
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
		                    <br>
		                    <small><span><b>';
		                    if($tipo_admin==1 || $tipo_admin==2 || $tipo_admin==4){
		                    	$html.='ORIGINAL: CLIENTE<br><br>';
		                    }else{
		                    	$html.='COPIA: CONTABILIDAD<br><br>';
		                    }
		                    $html.='* '.obtenerValorConfiguracionFactura(6).'<br><br>
		                      '.$nombre_ciudad.', '.obtenerFechaEnLetra($fecha_factura).'<br>
		                    </b></span></small>
	                  	</td>
	                </tr>
	            </thead>
            </table>';
            $html.='<table class="table">'.
              '<tr class="bold table-title text-left">'.
                  '<td  class="td-border-none text-right" width="12%"><b>Señor(es):</b></td>'.
                  '<td  class="td-border-none" ><b>'.$nombre_cliente.'</b></td>'.
                  '<td  class="td-border-none" width="18%"><b>NIT/CI:</b>&nbsp;'.$nit.'</td>'.
                '</tr>'.
            '</table>';
    		$html.='<table class="table2">'.
				'<thead>'.                
					'<tr>'.
					  '<td width="10%" align="center">CANTIDAD</td> 
					  <td align="center" colspan="2">DESCRIPCIÓN</td>                   
					  <td width="5%" align="center"><b>SUBTOTAL</b></td>
					</tr>
				</thead>';
              	$suma_total=0;
              	$html.='<tbody>
               		<tr>';
						// if($tipo_impresion==1){//tipo de impresion normal
						// 	$html.='<td valign="top" height="8%" class="text-right"><h5 style="padding: 0px;margin: 0px;">'.formatNumberDec($cantidad).'</h5></td>'.
						// 	'<td valign="top" height="8%" colspan="2"><h5 style="padding: 0px;margin: 0px;">'.$observaciones.'</h5></td>'.
						// 	'<td valign="top" height="8%" class="text-right"><h5 style="padding: 0px;margin: 0px;">'.formatNumberDec($importe).'</h5></td>';
						// 	$suma_total+=$importe;
						// }else{//imporesion detallada
               				$contador_items=0;
							$html.='<td class="text-right" valign="top" height="10%"><h5 style="padding: 0px;margin: 0px;">';
							while ($row = $stmtDesCli->fetch()) 
							{
							$html.=formatNumberDec($row["cantidad"]).'<br>';
								$contador_items++;
							}
							
							$html.='</h5></td> 
							<td valign="top" height="10%" colspan="2"><h5 style="padding: 0px;margin: 0px;" >';
							while ($row = $stmt2DesCli->fetch()) 
							{
							$html.=$row["descripcion_alterna"].'<br>';
							}
							$html.='</h5></td>                   
							<td class="text-right" valign="top" height="10%"><h5 style="padding: 0px;margin: 0px;">';
							while ($row = $stmt3DesCli->fetch()) 
							{
							$precio=$row["precio"];
							$descuento_bob=$row["descuento_bob"];
							$precio=$precio-$descuento_bob;

							$html.=formatNumberDec($precio).'<br>';
							$suma_total+=$precio;
							}
							$html.='</h5></td>';
						// } 
                	$html.='</tr>';            
                	$html.='<tr>
                        <td rowspan="3" align="center" style="padding: 0px;margin: 0px;"> ';
                            //GENERAMOS LA CADENA DEL QR
                            $contenidoQr=$nit_empresa."|".$nro_factura."|".$nro_autorizacion."|".$fecha_factura."|".$importe."|".$importe."|".$codigo_control."|".$nit."|0|0|0|0";
                            $dir = 'qr_temp/';
                            if(!file_exists($dir)){
                                mkdir ($dir);}
                            $fileName = $dir.$cod_factura.'.png';
                            $tamanio = 2; //tamaño de imagen que se creará
                            $level = 'M'; //tipo de precicion Baja L, mediana M, alta Q, maxima H
                            $frameSize = 1; //marco de qr
                            $contenido = $contenidoQr;
                            QRcode::png($contenido, $fileName, $level,$tamanio,$frameSize);
                            $html.= '<img src="'.$fileName.'"/>';
                            // echo '<img src="'.$fileName.'"/>';        
                        $html.='</td>
                        <td style="border-right: hidden" rowspan="2">';
                          $entero=floor($importe);
                          $decimal=$importe-$entero;
                          $centavos=round($decimal*100);
                          if($centavos<10){
                            $centavos="0".$centavos;
                          }
                          $html.='<p class="bold table-title">Son: '.ucfirst(CifrasEnLetras::convertirNumeroEnLetras($entero)).'      '.$centavos.'/100 Bolivianos</p>'; 
                          $html.='</td>
                        <td align="right" style="border-left: hidden" rowspan="2" colspan="2"><b>Total Bs &nbsp;&nbsp;&nbsp;&nbsp;'.formatNumberDec($suma_total).'</b></td>
                    </tr>
                    <tr></tr>
                    <tr>
                        <td style="border-right: hidden"><b>CÓDIGO DE CONTROL:&nbsp;&nbsp;&nbsp;&nbsp;</b> '.$codigo_control.'</td>
                        <td align="right" style="border-left: hidden" colspan="2"><b>FECHA LÍMITE DE EMISIÓN:&nbsp;&nbsp;&nbsp;&nbsp;</b>'.$fecha_limite_emision.'</td> 
                    </tr>'.
             
            	'</tbody>'.                        
        	'</table>'; 
	        $html.='<table class="table3" >
	            <tr align="center"><td>&quot;'.obtenerValorConfiguracionFactura(7).'&quot;<br>&quot;'.$leyenda.'&quot;</td></tr>
	        </table>';
		$html.='</div>';
        if($tipo_admin==1 || $tipo_admin==3 || $tipo_admin==4 || $tipo_admin==5){
        	// $html.='</header>';
			$html.='</body>'.
			      '</html>';   
        }

	    return $html."@@@@@@".$cod_factura."@@@@@@".$nro_factura;
	} catch (Exception $e) {
		$html="ERROR@@@@@@0@@@@@@0";
		return $html;		
	}
}

?>