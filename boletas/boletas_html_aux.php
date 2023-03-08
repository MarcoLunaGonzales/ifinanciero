<?php

	$html.='<div  style="height: 49.9%">';
			$html.='<table width="100%" class="table" style="font-size:12px;">
				<tr>
					<td colspan="2" >
					
					<table width="100%">
					<tr>
						<td style="border: 1;" align="left" width="50%"><b>INSTITUTO BOLIVIANO DE NORMALIZACIÓN Y CALIDAD</b></td>
						<td style="border: 1;" align="right" width="50%"><b>N° PATRONAL '.$arrayOficinas[$cod_unidad_x][4].'</b></td>
					</tr>
					</table>
					<table width="100%">
						<tr>
							<td width="33%" style="border: 0;" align="left"><b>NIT:1020745020<br>'.$arrayOficinas[$cod_unidad_x][1].'</b><br><br><br></td>
							<td width="33%" style="border: 0;" align="center"><span style="font-size: 15px"><b>BOLETA DE PAGO</b></span><br><b>(Expresado en Bolivianos)</b></td>
							<td width="33%" style="border: 0;" align="right"><img class="" width="50" height="50" src="../assets/img/logo_ibnorca_origen_3.jpg"></td>
						</tr>
					</table>
				
					</td>
				</tr>
				<tr><td>
					<b>NOMBRE: </b><span style="text-transform:capitalize">'.$result['apellidos'].' '.$result['nombres'].'</span><BR>
					<b>CARGO: </b><span>'.$result['cargo'].'</span><br>
					<b>ÁREA: </b><span>'.$result['area'].'</span>
				</td>
				<td class="text-left">
					<b>PERIODO: </b><span style="text-transform:capitalize">'.$mes.' '.$gestion.'</span><br>
					<b>SUELDO BÁSICO: </b><span style="text-transform:capitalize">'.formatNumberDec($result['haber_basico_pactado']).'</span><br>
					<b>DIAS TRABAJADOS: </b><span style="text-transform:capitalize">'.$result['dias_trabajados'].'</span><br>
					<b>FECHA DE INGRESO: </b><span style="text-transform:capitalize">'.strftime('%d/%m/%Y',strtotime($ing_planilla)).'</span></td></tr>

				<tr><td width="50%" valign="top">
					<table width="100%">
						<tr><td colspan="2" style="background:#F2F2F2;border: 0;"><center><b>INGRESOS</b></center></td></tr>
						<tr>
							<td class=text-left" style="border: 0;font-family:Arial, sans-serif;" valign="top">Haber Básico</td>
							<td class="text-right" style="border: 0;font-family:Arial, sans-serif;" valign="top">'.formatNumberDec($haber_basico_dias).'</td>
						</tr>
						<tr>
							<td class=text-left" style="border: 0;font-family:Arial, sans-serif;" valign="top">Bono Antigüedad</td>
							<td class="text-right" style="border: 0;font-family:Arial, sans-serif;" valign="top">'.formatNumberDec($bono_antiguedad).'</td>
						</tr>
						<tr>
							<td class=text-left" style="border: 0;font-family:Arial, sans-serif;" valign="top">Otros Bonos</td>
							<td class="text-right" style="border: 0;font-family:Arial, sans-serif;" valign="top">'.formatNumberDec($otrosBonos).'</td>
						</tr>
						<tr>
							<td class=text-left" style="border: 0;">&nbsp;</td>
							<td class="text-right" style="border: 0;">&nbsp;</td>
						</tr>
						<tr>
							<td class=text-left" style="border: 0;">&nbsp;</td>
							<td class="text-right" style="border: 0;">&nbsp;</td>
						</tr>
						<tr>
							<td class=text-left" style="border: 0;">&nbsp;</td>
							<td class="text-right" style="border: 0;">&nbsp;</td>
						</tr>
						<tr>
							<td class=text-left" style="border: 0;"><b>Total Ganado:</b></td>
							<td class="text-right" style="border: 0;">'.formatNumberDec($suma_ingresos).'</td>
						</tr>
						<tr style="background:#F2F2F2;">
							<td class=text-left" style="border: 0;"><b>SALDO RC-IVA:</b></td>
							<td class="text-right" style="border: 0;border-left: 0;">'.formatNumberDec($saldo_rciva).'</td>
						</tr>
					</table>
				</td>
				<td width="50%" valign="top">
					<table width="100%">
						<tr><td colspan="2" style="background:#F2F2F2;border: 0;"><center><b>DEDUCCIONES</b></center></td></tr>
						<tr>
							<td class="text-left" style="border: 0;font-family:Arial, sans-serif;" valign="top">AFP</td>
							<td class="text-right" style="border: 0;font-family:Arial, sans-serif;" valign="top">'.formatNumberDec($Ap_Vejez+$Riesgo_Prof+$ComAFP+$aposol+$aposol13+$aposol25+$aposol35).'</td>
						</tr>
						<tr>
							<td class="text-left" style="border: 0;font-family:Arial, sans-serif;" valign="top">RC IVA</td>
							<td class="text-right" style="border: 0;font-family:Arial, sans-serif;" valign="top">'.formatNumberDec($RC_IVA).'</td>
						</tr>
						<tr>
							<td class="text-left" style="border: 0;font-family:Arial, sans-serif;" valign="top">Anticipos</td>
							<td class="text-right" style="border: 0;font-family:Arial, sans-serif;" valign="top">'.formatNumberDec($Anticipos).'</td>
						</tr>
						<tr>
							<td class="text-left" style="border: 0;font-family:Arial, sans-serif;" valign="top">Atrasos</td>
							<td class="text-right" style="border: 0;font-family:Arial, sans-serif;" valign="top">'.formatNumberDec($atrasos).'</td>
						</tr>
						<tr>
							<td class="text-left" style="border: 0;font-family:Arial, sans-serif;" valign="top">Otros Descuentos</td>
							<td class="text-right" style="border: 0;font-family:Arial, sans-serif;" valign="top">'.formatNumberDec($descuentos_otrosX).'</td>
						</tr>
						<tr>
							<td class=text-left" style="border: 0;">&nbsp;</td>
							<td class="text-right" style="border: 0;">&nbsp;</td>
						</tr>
						<tr>
							<td class=text-left" style="border: 0;"><b>Total Deducciones:</b></td>
							<td class="text-right" style="border: 0;">'.formatNumberDec($suma_egresos).'</td>
						</tr>
						<tr style="background:#F2F2F2;">
							<td class=text-left" style="border: 0;"><b>Líquido Pagable:</b></td>
							<td class="text-right" style="border: 0;"><b> '.formatNumberDec($liquido_pagable).'</b></td>
						</tr>
					</table>
				</td></tr>';
				// $html.='<tr>
				// <td style="background:#F2F2F2;border-right: 0;" >';
				// 		//GENERANDO QR
                        $dir = 'qr_temp/';
                        if(!file_exists($dir)){
                            mkdir ($dir);}
                        $fileName = $dir.$codigo_generado.'.png';
                        $tamanio = 2; //tamaño de imagen que se creará
                        $level = 'M'; //tipo de precicion Baja L, mediana M, alta Q, maxima H
                        $frameSize = 1; //marco de qr
                        // $codigo_generado2 = md5($codigo_generado);
                        // $contenido = $urlBoletas.'ver_boleta.php?key='.$codigo_generado;
                        $contenido = $urlBoletas.'verf_boletas_print.php?key='.$cod_planilla_mes;
                        QRcode::png($contenido, $fileName, $level, $tamanio,$frameSize);
                        // $html.='<img src="'.$fileName.'"/>';

				// 	$html.='</td>
				// 	<td class="text-right" style="background:#F2F2F2;border-left:0;"></td>
				// 	</tr>';

					$html.='<tr>
					<td style="border-right: 0;" valign="bottom">
						<table width="100%">
							<tr>
							<td style="border: 0;" ><img src="'.$fileName.'"/></td>
							<td style="border: 0;" >
<center><p><img width="100px" src="'.$urlFirma.'"/></p></center>
							</td>
							</tr>
						</table>';
					$html.='</td>
					<td style="border-left:0;padding-top:20px;">
						<center> <b><p>RECIBIDO CONFORME <br> <b style="font-style: italic;">'.$detail_nombre_personal.'<br>'.$detail_primer_vista.'</b></p></b></center>
					</td>
				</tr>
			</table>';
			 // $html.='<div style="page-break-after: always"></div>';
	$html.='</div>';


?>