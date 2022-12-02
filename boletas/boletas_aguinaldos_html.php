<?php

	$html.='<div  style="height: 49.4%">';
			$html.='<table width="100%" class="table" style="font-size:12px;">
				<tr><td colspan="2" >
					<table width="100%">
					<tr ><td width="25%" style="border: 0;" ><b>CORPORACION BOLIVIANA DE FARMACIAS</b><br>Av.Landaeta Nro 836<br>La Paz - Bolivia<br>NIT:1022039027</td>
						<td width="25%" style="border: 0;"><center><span style="font-size: 13px"><b>PAPELETA DE AGUINALDO</b></span><br><b>EXPRESADA EN BOLIVIANOS</b></center></td>
						<td width="25%" style="border: 0;"><center><table width="100%"><tr><td style="border: 0;align:left" width="70%">N° PAT. 651-1-956</td><td style="border: 0;" width="30%"><img class="" width="50" height="50" src="../assets/img/pastilla_nav.png"></td></tr></table></center></td>
					</tr>
					</table>
				</td></tr>
				<tr><td>
					<b>GESTION:</b> '.$gestion.'<br>
					<b>NOMBRE:</b> '.$result['apellidos'].' '.$result['nombres'].'<BR>
					<b>CARGO:</b> '.$result['cargo'].'<BR>
					<b>PROM. TOT. GANADO 3 ULTIMOS:</b> '.formatNumberDec($result['promedio_ganado']).' BS <br>
					<b>MESES TRABAJADOS</b> '.$result['dias_360'].' 
				</td>
				<td class="text-right"><b>Nro. Pla:</b> '.$index_planilla.'</td></tr>
				<tr><td width="50%" valign="top">
					<table width="100%">
						<tr><td colspan="2" style="background:#F2F2F2;border: 0;"><center><b>INGRESOS</b></center></td></tr>
						<tr>
							<td class=text-left" style="border: 0;font-family:Arial, sans-serif;" valign="top">AGUINALDO<br></td>
							<td class="text-right" style="border: 0;font-family:Arial, sans-serif;" valign="top">'.formatNumberDec($total_aguinaldo).'<br>&nbsp;<BR>&nbsp;</td>
						</tr>
						<tr>
							<td  colspan="2" class=text-left" style="border: 0;"><center><BR><BR>¡FELICES FIESTAS!</center></td>
						</tr>
					</table>
				</td>
				<td width="50%" valign="top">
					<table width="100%">
						<tr><td style="background:#F2F2F2;border: 0;"><center><b></b></center></td></tr>
						<tr>
							<td class="text-left" style="border: 0;font-family:Arial, sans-serif;" valign="top"></td>
						</tr>
						<tr>
						<td class=text-left" style="border: 0;"><center><img class="" width="120" height="150" src="../assets/img/arbol_navideno.png"></center><br></td>
						</tr>
					</table>
				</td></tr>
				<tr><td colspan="2" class="text-right" style="background:#F2F2F2;">
					<b>Liquido Pagable: '.formatNumberDec($liquido_pagable).'
				</b></td></tr>
			</table>';
			$html.='<table width="100%">
				<tr>
					<td><center><p>______________________________<br>Recibí Conforme</p></center></td>
				</tr>
			</table>';
			 
	$html.='</div>';


?>