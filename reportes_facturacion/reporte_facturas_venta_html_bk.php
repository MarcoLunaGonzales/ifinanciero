if($glosa_factura3==null){
              	        	while ($row = $stmtDesCli->fetch()) 							
							{
								$html.='<tr>';
								$html.='<td class="text-right" valign="top" style="padding-top: 0px;padding-bottom: 0px; border-bottom: hidden;border-top: hidden; font-size: 8px;">';
								$html.=formatNumberDec($row["cantidad"]);
								$html.='</td> 
								<td valign="top" colspan="2" style="padding-top: 0px;padding-bottom: 0px; border-bottom: hidden; border-top: hidden; font-size: 8px;">';
								$html.=mb_strtoupper($row["descripcion_alterna"]);
								$html.='</td>                   
								<td class="text-right" valign="top" style="padding-top: 0px;padding-bottom: 0px; border-bottom: hidden; border-top: hidden; font-size: 8px;">';
								$precio=$row["precio"];
								$descuento_bob=$row["descuento_bob"];
								$cantidad=$row["cantidad"];
								$precio=$precio*$cantidad-$descuento_bob;

								$html.=formatNumberDec($precio);
								$suma_total+=$precio;
								$html.='</td>';
								$html.='</tr>';
								$contador_items++;
							}
							
              	        }else{
              	        	$html.='<tr>';
							$html.='<td class="text-right" valign="top" style="padding-top: 0px;padding-bottom: 0px; border-bottom: hidden;border-top: hidden; font-size: 8px;">';
							$html.=formatNumberDec($cantidad_x);
							$html.='</td> 
							<td valign="top" colspan="2" style="padding-top: 0px;padding-bottom: 0px; border-bottom: hidden; border-top: hidden; font-size: 8px;">';
							$html.=mb_strtoupper($glosa_factura3);
							$html.='</td>                   
							<td class="text-right" valign="top" style="padding-top: 0px;padding-bottom: 0px; border-bottom: hidden; border-top: hidden; font-size: 8px;">';
							$precio=$monto_factura;
							$html.=formatNumberDec($precio);
							$suma_total=$precio;
							$html.='</td>';
							$html.='</tr>';
							$contador_items++;
              	        }