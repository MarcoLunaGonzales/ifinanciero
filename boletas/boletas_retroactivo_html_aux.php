<?php

	$html.='<div  style="height: 49.4%">';
			$html.='<table width="100%" class="table" style="font-size:12px;">
				<tr><td colspan="2" >
					<table width="100%">
					<tr ><td width="29%" style="border: 0;" ><b>CORPORACION BOLIVIANA DE FARMACIAS</b><br>Av.Landaeta Nro 836<br>La Paz - Bolivia<br>NIT:1022039027</td>
						<td width="25%" style="border: 0;"><center><span style="font-size: 13px"><b>PAPELETA DE SUELDOS</b></span><b><br>RETROACTIVOS GESTION '.$gestion.'<br>(EN BOLIVIANOS)</b></center></td>
						<td width="25%" style="border: 0;"><center><table width="100%"><tr><td style="border: 0;align:left" width="70%">N° PAT. 651-1-956</td><td style="border: 0;" width="30%"><img class="" width="50" height="40" src="../assets/img/favicon.png"></td></tr></table></center></td>
					</tr>
					</table>
				</td></tr>
				<tr><td>
					<b>NOMBRE:</b> '.$result['paterno'].' '.$result['materno'].' '.$result['primer_nombre'].'<BR>
					<b>CARGO:</b> '.$result['cargo'].' 
				</td>
				<td class="text-left">
					<b>HABER BASICO:</b> '.formatNumberDec($result['haber_basico_nuevo']).' BS <br>
					<b>Nro. Pla:</b> '.$index_planilla.'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>F. Ingreso: </b>'.strftime('%d/%m/%Y',strtotime($result['ing_planilla'])).'</td></tr>

				<tr><td width="50%" valign="top">
					<table width="100%">
						<tr><td colspan="2" style="background:#F2F2F2;border: 0;"><center><b>INGRESOS</b></center></td></tr>
						<tr>
							<td class=text-left" style="border: 0;font-family:Arial, sans-serif;" valign="top">Reintegros</td>
							<td class="text-right" style="border: 0;font-family:Arial, sans-serif;" valign="top">'.formatNumberDec($suma_ingresos).'<BR>&nbsp;<BR>&nbsp;<BR>&nbsp;</td>
						</tr>
						<tr>
							<td class=text-left" style="border: 0;"><b>Total Ingresos:</b></td>
							<td class="text-right" style="border: 0;">'.formatNumberDec($suma_ingresos).'</td>
						</tr>
					</table>
				</td>
				<td width="50%" valign="top">
					<table width="100%">
						<tr><td colspan="2" style="background:#F2F2F2;border: 0;"><center><b>DEDUCCIONES</b></center></td></tr>
						<tr>
							<td class="text-left" style="border: 0;font-family:Arial, sans-serif;" valign="top">Ap. Vejez 10%<br>Riesgo Prof. 1.71%<br>Com.AFP 0.5%<br>Apo.Sol 0.5%</td>
							<td class="text-right" style="border: 0;font-family:Arial, sans-serif;" valign="top">'.formatNumberDec($ap_vejez).'<br>'.formatNumberDec($riesgo_prof).'<br>'.formatNumberDec($com_afp).'<br>'.formatNumberDec($aporte_sol).'</td>
						</tr>
						<tr>
							<td class=text-left" style="border: 0;"><b>Total Egresos:</b></td>
							<td class="text-right" style="border: 0;">'.formatNumberDec($suma_egresos).'</td>
						</tr>
					</table>
				</td></tr>
				<tr>
				
				<td style="background:#F2F2F2;border-right: 0;">';
						//GENERANDO QR
                        $dir = 'qr_temp/';
                        if(!file_exists($dir)){
                            mkdir ($dir);}
                        $fileName = $dir.$codigo_generado.'.png';
                        $tamanio = 2; //tamaño de imagen que se creará
                        $level = 'M'; //tipo de precicion Baja L, mediana M, alta Q, maxima H
                        $frameSize = 1; //marco de qr
                        // $codigo_generado2 = md5($codigo_generado);
                        $contenido = 'farmaciasbolivia.com.bo/icobofar/blts/vb2.php?ws='.$codigo_generado;
                        QRcode::png($contenido, $fileName, $level, $tamanio,$frameSize);
                        $html.='<img src="'.$fileName.'"/>';
					$html.='</td><td class="text-right" style="background:#F2F2F2;border-left: 0;"><b>Liquido Pagable: '.formatNumberDec($liquido_pagable).'
				</b></td></tr>
			</table>';
	$html.='</div>';


?>