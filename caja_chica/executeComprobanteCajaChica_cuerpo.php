<?php

	$stmtCajaChicaDet = $dbh->prepare("SELECT codigo,nro_recibo,cod_tipodoccajachica,observaciones,monto,cod_uo,cod_area,cod_cuenta,(select p.nombre from af_proveedores p where p.codigo=cod_proveedores)as proveedor,(select c.nombre from plan_cuentas c where c.codigo=cod_cuenta)nombre_cuenta,
    (select c2.numero from plan_cuentas c2 where c2.codigo=cod_cuenta)numero_cuenta,
    (select CONCAT_WS(' ',p.primer_nombre,p.paterno,p.materno) from personal p where p.codigo=cod_personal)as personal,
    (select u.abreviatura from unidades_organizacionales u where u.codigo=cod_uo)as nombre_uo,
    (select a.abreviatura from areas a where a.codigo=cod_area)as nombre_area
    from caja_chicadetalle where cod_estadoreferencial=1 and cod_cajachica=$cod_cajachica ORDER BY 1");
    $stmtCajaChicaDet->execute();
    $stmtCajaChicaDet->bindColumn('codigo', $codigo_ccdetalle);
    $stmtCajaChicaDet->bindColumn('cod_cuenta', $cod_cuenta);
    $stmtCajaChicaDet->bindColumn('nro_recibo', $nro_recibo);
    $stmtCajaChicaDet->bindColumn('nombre_cuenta', $nombre_cuenta);
    $stmtCajaChicaDet->bindColumn('numero_cuenta', $numero_cuenta);    
    $stmtCajaChicaDet->bindColumn('personal', $personal);
    $stmtCajaChicaDet->bindColumn('proveedor', $proveedor);
    $stmtCajaChicaDet->bindColumn('cod_uo', $cod_uo);
    $stmtCajaChicaDet->bindColumn('cod_area', $cod_area);
    $stmtCajaChicaDet->bindColumn('nombre_uo', $nombre_uo);
    $stmtCajaChicaDet->bindColumn('nombre_area', $nombre_area);
    $stmtCajaChicaDet->bindColumn('cod_tipodoccajachica', $cod_retencioncajachica);
    $stmtCajaChicaDet->bindColumn('observaciones', $observaciones_dcc);
    $stmtCajaChicaDet->bindColumn('monto', $monto_dcc);
    $ordenDetalle=1;//<--
    while ($rowCajaChicaDet = $stmtCajaChicaDet->fetch()) 
    {
    	//el porcentaje origen de tipo de retencion
        $stmtRetencionOrigen = $dbh->prepare("SELECT porcentaje_cuentaorigen from configuracion_retenciones where codigo=$cod_retencioncajachica");
        $stmtRetencionOrigen->execute();
        $resultRetencionOrgine = $stmtRetencionOrigen->fetch();
        $porcentaje_cuentaorigen = $resultRetencionOrgine['porcentaje_cuentaorigen']; 
        // verificamos si el porcentaje es mayor a 100%
        if($porcentaje_cuentaorigen>100){
        	$monto_recalculado=$monto_dcc*$porcentaje_cuentaorigen/100;
        }else{
        	$monto_recalculado=$monto_dcc;
        }			        
        //Listamos los gastos que tengan factura y los contabilizamos
        $sw_facturas=contador_facturas_cajachica($codigo_ccdetalle);//contador de facturas
        if($sw_facturas>0){
        	$cadena_facturas = cadena_facturas_cajachica($codigo_ccdetalle);
        	$importe_total_facturas = importe_total_facturas($codigo_ccdetalle);
        	$importe_total_gastos=importe_total_gastos_directos($codigo_ccdetalle);
        	// $importe_mas_gastos=$importe_total_facturas+$importe_total_gastos;
        	if($porcentaje_cuentaorigen>100){
	        	$monto_recalculado=$importe_total_facturas*$porcentaje_cuentaorigen/100;
	        	$monto_recalculado_iva=$importe_total_facturas*$porcentaje_cuentaorigen/100;//para el credito iva, no contar el gasto directo
	        }else{
	        	$monto_recalculado=$importe_total_facturas;
	        	$monto_recalculado_iva=$importe_total_facturas;
	        }				        
            //buscamos el tipo de retencion
            $stmtRetenciones = $dbh->prepare("SELECT cod_cuenta,porcentaje,debe_haber from configuracion_retencionesdetalle where cod_configuracionretenciones=$cod_retencioncajachica");
            $stmtRetenciones->execute();
            $stmtRetenciones->bindColumn('cod_cuenta', $cod_cuenta_retencion);
            $stmtRetenciones->bindColumn('porcentaje', $porcentaje_retencion);
            $stmtRetenciones->bindColumn('debe_haber', $debe_haber);
            while ($rowFac = $stmtRetenciones->fetch()) 
            {
            	// $descripcion=$nombre_uo.' '.$cadena_facturas.' (R-'.$nro_recibo.'),'.$personal.', '.$observaciones_dcc;
            	$descripcion=$nombre_uo." ".$cadena_facturas.' (R-'.$nro_recibo.'),'.$personal.', '.$observaciones_dcc;
            	// $monto=$monto_recalculado*$porcentaje_retencion/100;
                if($cod_cuenta_retencion>0){
                    // $nro_cuenta_retencion=obtieneNumeroCuenta($cod_cuenta_retencion);
                    // $nombre_cuenta_retencion=nameCuenta($cod_cuenta_retencion);
                    $monto=$monto_recalculado_iva*$porcentaje_retencion/100;
                    
                    if($debe_haber==1){ //preguntamps si pertenece a la columna debe o haber
                    	$sqlInsertDet="INSERT INTO comprobantes_detalle (cod_comprobante, cod_cuenta, cod_cuentaauxiliar, cod_unidadorganizacional, cod_area, debe, haber, glosa, orden) VALUES ('$codComprobante','$cod_cuenta_retencion','0','$cod_uo','$cod_area','$monto','0','$descripcion','$ordenDetalle')";
			            $stmtInsertDet = $dbh->prepare($sqlInsertDet);
			            $flagSuccessDet=$stmtInsertDet->execute();
			            $ordenDetalle++;
                    }else{
                    	$sqlInsertDet="INSERT INTO comprobantes_detalle (cod_comprobante, cod_cuenta, cod_cuentaauxiliar, cod_unidadorganizacional, cod_area, debe, haber, glosa, orden) VALUES ('$codComprobante','$cod_cuenta_retencion','0','$cod_uo','$cod_area','0','$monto','$descripcion','$ordenDetalle')";
			            $stmtInsertDet = $dbh->prepare($sqlInsertDet);
			            $flagSuccessDet=$stmtInsertDet->execute();
			            $ordenDetalle++;
                    }
                    //insertamos las facturas a factuas_compra para el libro de compras
                    insertar_facturas_compra($codComprobante,$ordenDetalle,$codigo_ccdetalle);
                }else{             
                	if($porcentaje_cuentaorigen>100){
			        	$monto_restante=$monto_recalculado+$importe_total_gastos;
			        }else{
			        	// echo $monto_recalculado."--";
			        	$monto_restante=($monto_recalculado*$porcentaje_cuentaorigen/100)+$importe_total_gastos;
			        	// echo $monto_restante."<br>";       
			        }
                	//Desde aqui las distribuciones por area y/o oficina
					$cont_tipo_distribucion=0;//verificará si se registró alguna distribucion
					$stmtTipoDistri=$dbh->prepare("SELECT codigo from distribucion_gastos_caja_chica where cod_cajachica_detalle=$codigo_ccdetalle GROUP BY tipo_distribucion");
	                $stmtTipoDistri->execute();
					while ($rowTipoDistr = $stmtTipoDistri->fetch()){
						$cont_tipo_distribucion++;
					}	
					if($cont_tipo_distribucion==1){//distribucion solo area u oficina
						$stmtTipoDistribucion = $dbh->prepare("SELECT tipo_distribucion,oficina_area,porcentaje from distribucion_gastos_caja_chica where porcentaje>0 and cod_cajachica_detalle=$codigo_ccdetalle order by tipo_distribucion");
		                $stmtTipoDistribucion->execute();
		                while ($rowTipoDistribucion = $stmtTipoDistribucion->fetch()){
		                	$tipo=$rowTipoDistribucion['tipo_distribucion'];
		                	$oficina_area=$rowTipoDistribucion['oficina_area'];
		                	$porcentaje=$rowTipoDistribucion['porcentaje'];
		                	if($tipo==1){//oficina
		                		$name_oficina_dis=abrevUnidad($oficina_area);
		                		$descripcion_distribucion=$nombre_uo.'/'.$name_oficina_dis.' '.$cadena_facturas.' (R-'.$nro_recibo.'),'.$personal.', '.$observaciones_dcc;
	                            $monto_of=$monto_restante*$porcentaje/100;
	                            if($debe_haber==1){ //preguntamps si pertenece a la columna debe o haber
	                            	$sqlInsertDet="INSERT INTO comprobantes_detalle (cod_comprobante, cod_cuenta, cod_cuentaauxiliar, cod_unidadorganizacional, cod_area, debe, haber, glosa, orden) VALUES ('$codComprobante','$cod_cuenta','0','$oficina_area','$cod_area','0','$monto_of','$descripcion_distribucion','$ordenDetalle')";
						            $stmtInsertDet = $dbh->prepare($sqlInsertDet);
						            $flagSuccessDet=$stmtInsertDet->execute();
						            $ordenDetalle++;
	                            }else{
	                                $sqlInsertDet="INSERT INTO comprobantes_detalle (cod_comprobante, cod_cuenta, cod_cuentaauxiliar, cod_unidadorganizacional, cod_area, debe, haber, glosa, orden) VALUES ('$codComprobante','$cod_cuenta','0','$oficina_area','$cod_area','$monto_of','0','$descripcion_distribucion','$ordenDetalle')";
						            $stmtInsertDet = $dbh->prepare($sqlInsertDet);
						            $flagSuccessDet=$stmtInsertDet->execute();
						            $ordenDetalle++;                               
	                            }
		                	}elseif($tipo==2){//area				                		
		                		$name_area_dis=abrevArea($oficina_area);
		                		$descripcion_distribucion=$nombre_area.'/'.$name_area_dis.' '.$cadena_facturas.' (R-'.$nro_recibo.'),'.$personal.', '.$observaciones_dcc;
	                            $monto_of=$monto_restante*$porcentaje/100;
	                            if($debe_haber==1){ //preguntamps si pertenece a la columna debe o haber
	                            	$sqlInsertDet="INSERT INTO comprobantes_detalle (cod_comprobante, cod_cuenta, cod_cuentaauxiliar, cod_unidadorganizacional, cod_area, debe, haber, glosa, orden) VALUES ('$codComprobante','$cod_cuenta','0','$cod_uo','$oficina_area','0','$monto_of','$descripcion_distribucion','$ordenDetalle')";
						            $stmtInsertDet = $dbh->prepare($sqlInsertDet);
						            $flagSuccessDet=$stmtInsertDet->execute();
						            $ordenDetalle++;
	                            }else{
	                                $sqlInsertDet="INSERT INTO comprobantes_detalle (cod_comprobante, cod_cuenta, cod_cuentaauxiliar, cod_unidadorganizacional, cod_area, debe, haber, glosa, orden) VALUES ('$codComprobante','$cod_cuenta','0','$cod_uo','$oficina_area','$monto_of','0','$descripcion_distribucion','$ordenDetalle')";
						            $stmtInsertDet = $dbh->prepare($sqlInsertDet);
						            $flagSuccessDet=$stmtInsertDet->execute();
						            $ordenDetalle++;                               
	                            }
		                	}				                	
		                }
					}elseif($cont_tipo_distribucion==2){//distribucion area y oficina
						$monto_uo_distribuido=$monto_restante*40/100;
						$monto_area_distribuido=$monto_restante*60/100;

						$stmtTipoDistribucion = $dbh->prepare("SELECT tipo_distribucion,oficina_area,porcentaje from distribucion_gastos_caja_chica where porcentaje>0 and cod_cajachica_detalle=$codigo_ccdetalle order by tipo_distribucion");
		                $stmtTipoDistribucion->execute();
		                while ($rowTipoDistribucion = $stmtTipoDistribucion->fetch()){
		                	$tipo=$rowTipoDistribucion['tipo_distribucion'];
		                	$oficina_area=$rowTipoDistribucion['oficina_area'];
		                	$porcentaje=$rowTipoDistribucion['porcentaje'];
		                	if($tipo==1){//oficina
		                		$name_oficina_dis=abrevUnidad($oficina_area);
		                		$descripcion_distribucion=$nombre_uo.'/'.$name_oficina_dis.' '.$cadena_facturas.' (R-'.$nro_recibo.'),'.$personal.', '.$observaciones_dcc;
	                            
	                            $monto_of=$monto_uo_distribuido*$porcentaje/100;
	                            if($debe_haber==1){ //preguntamps si pertenece a la columna debe o haber
	                            	$sqlInsertDet="INSERT INTO comprobantes_detalle (cod_comprobante, cod_cuenta, cod_cuentaauxiliar, cod_unidadorganizacional, cod_area, debe, haber, glosa, orden) VALUES ('$codComprobante','$cod_cuenta','0','$oficina_area','$cod_area','0','$monto_of','$descripcion_distribucion','$ordenDetalle')";
						            $stmtInsertDet = $dbh->prepare($sqlInsertDet);
						            $flagSuccessDet=$stmtInsertDet->execute();
						            $ordenDetalle++;
	                            }else{
	                                $sqlInsertDet="INSERT INTO comprobantes_detalle (cod_comprobante, cod_cuenta, cod_cuentaauxiliar, cod_unidadorganizacional, cod_area, debe, haber, glosa, orden) VALUES ('$codComprobante','$cod_cuenta','0','$oficina_area','$cod_area','$monto_of','0','$descripcion_distribucion','$ordenDetalle')";
						            $stmtInsertDet = $dbh->prepare($sqlInsertDet);
						            $flagSuccessDet=$stmtInsertDet->execute();
						            $ordenDetalle++;                               
	                            }
		                	}elseif($tipo==2){//area				                		
		                		$name_area_dis=abrevArea($oficina_area);
		                		$descripcion_distribucion=$nombre_area.'/'.$name_area_dis.' '.$cadena_facturas.' (R-'.$nro_recibo.'),'.$personal.', '.$observaciones_dcc;
	                            $monto_of=$monto_area_distribuido*$porcentaje/100;
	                            if($debe_haber==1){ //preguntamps si pertenece a la columna debe o haber
	                            	$sqlInsertDet="INSERT INTO comprobantes_detalle (cod_comprobante, cod_cuenta, cod_cuentaauxiliar, cod_unidadorganizacional, cod_area, debe, haber, glosa, orden) VALUES ('$codComprobante','$cod_cuenta','0','$cod_uo','$oficina_area','0','$monto_of','$descripcion_distribucion','$ordenDetalle')";
						            $stmtInsertDet = $dbh->prepare($sqlInsertDet);
						            $flagSuccessDet=$stmtInsertDet->execute();
						            $ordenDetalle++;
	                            }else{
	                                $sqlInsertDet="INSERT INTO comprobantes_detalle (cod_comprobante, cod_cuenta, cod_cuentaauxiliar, cod_unidadorganizacional, cod_area, debe, haber, glosa, orden) VALUES ('$codComprobante','$cod_cuenta','0','$cod_uo','$oficina_area','$monto_of','0','$descripcion_distribucion','$ordenDetalle')";
						            $stmtInsertDet = $dbh->prepare($sqlInsertDet);
						            $flagSuccessDet=$stmtInsertDet->execute();
						            $ordenDetalle++;                               
	                            }
		                	}				                	
		                }
					}elseif($cont_tipo_distribucion==0){//distribucion normal
						$cod_uo_config=obtenerValorConfiguracion(15);
	                    if($cod_uo==$cod_uo_config){
	                        //desde aqui repartimos la contabilizacion a las oficinas si es DN
	                        $stmtOficina = $dbh->prepare("SELECT dgd.cod_unidadorganizacional,dgd.porcentaje,
	                           (SELECT uo.abreviatura FROM unidades_organizacionales uo WHERE uo.codigo=dgd.cod_unidadorganizacional) as oficina
	                        from distribucion_gastosporcentaje_detalle dgd,distribucion_gastosporcentaje dg
	                        where dgd.cod_distribucion_gastos=dg.codigo and dg.estado=1 and porcentaje>0");
	                        $stmtOficina->execute();
	                        $stmtOficina->bindColumn('cod_unidadorganizacional', $cod_unidadorganizacional);
	                        $stmtOficina->bindColumn('porcentaje', $porcentaje);
	                        $stmtOficina->bindColumn('oficina', $oficinaFac);
	                        while ($rowOf = $stmtOficina->fetch()) 
	                        {                                    
	                            $descripcion_of=$oficinaFac.'/'.$centroCostosDN.' '.$cadena_facturas.' (R-'.$nro_recibo.'),'.$personal.', '.$observaciones_dcc;
	                            $monto_of=$monto_restante*$porcentaje/100;
	                            if($debe_haber==1){ //preguntamps si pertenece a la columna debe o haber
	                            	$sqlInsertDet="INSERT INTO comprobantes_detalle (cod_comprobante, cod_cuenta, cod_cuentaauxiliar, cod_unidadorganizacional, cod_area, debe, haber, glosa, orden) VALUES ('$codComprobante','$cod_cuenta','0','$cod_unidadorganizacional','$centroCostosDN','0','$monto_of','$descripcion_of','$ordenDetalle')";
						            $stmtInsertDet = $dbh->prepare($sqlInsertDet);
						            $flagSuccessDet=$stmtInsertDet->execute();
						            $ordenDetalle++;
	                            }else{
	                            	$sqlInsertDet="INSERT INTO comprobantes_detalle (cod_comprobante, cod_cuenta, cod_cuentaauxiliar, cod_unidadorganizacional, cod_area, debe, haber, glosa, orden) VALUES ('$codComprobante','$cod_cuenta','0','$cod_unidadorganizacional','$centroCostosDN','$monto_of','0','$descripcion_of','$ordenDetalle')";
						            $stmtInsertDet = $dbh->prepare($sqlInsertDet);
						            $flagSuccessDet=$stmtInsertDet->execute();
						            $ordenDetalle++;
	                            }                                     
	                        }
	                    }else{
	                        $descripcion_of=$nombre_uo.'/'.$nombre_area.' '.$cadena_facturas.' (R-'.$nro_recibo.'),'.$personal.', '.$observaciones_dcc;	
	                        if($debe_haber==1){ //preguntamps si pertenece a la columna debe o haber
	                    		$sqlInsertDet="INSERT INTO comprobantes_detalle (cod_comprobante, cod_cuenta, cod_cuentaauxiliar, cod_unidadorganizacional, cod_area, debe, haber, glosa, orden) VALUES ('$codComprobante','$cod_cuenta','0','$cod_uo','$cod_area','0','$monto_restante','$descripcion_of','$ordenDetalle')";
					            $stmtInsertDet = $dbh->prepare($sqlInsertDet);
					            $flagSuccessDet=$stmtInsertDet->execute();
					            $ordenDetalle++;
	                        }else{
	                        	$sqlInsertDet="INSERT INTO comprobantes_detalle (cod_comprobante, cod_cuenta, cod_cuentaauxiliar, cod_unidadorganizacional, cod_area, debe, haber, glosa, orden) VALUES ('$codComprobante','$cod_cuenta','0','$cod_uo','$cod_area','$monto_restante','0','$descripcion_of','$ordenDetalle')";
					            $stmtInsertDet = $dbh->prepare($sqlInsertDet);
					            $flagSuccessDet=$stmtInsertDet->execute();
					            $ordenDetalle++;
	                        }       
	                    }
					}
                    // aqui la contra cuenta
                    $descripcion_contra_cuenta='CONTABILIZACIÓN CAJA CHICA. '.$personal.', '.$observaciones_dcc;
                    $monto_contracuenta=($monto_recalculado*$porcentaje_retencion/100)+$importe_total_gastos;
                    if($debe_haber==1){//si es debe, pondremos en haber
                    	$sqlInsertDet="INSERT INTO comprobantes_detalle (cod_comprobante, cod_cuenta, cod_cuentaauxiliar, cod_unidadorganizacional, cod_area, debe, haber, glosa, orden) VALUES ('$codComprobante','$cod_contra_cuenta','0', '$cod_uo_tcc','$cod_area_tcc','$monto_contracuenta','0','$descripcion_contra_cuenta','$ordenDetalle')";
			            $stmtInsertDet = $dbh->prepare($sqlInsertDet);
			            $flagSuccessDet=$stmtInsertDet->execute();
			            $ordenDetalle++;
                    }else{
                    	$sqlInsertDet="INSERT INTO comprobantes_detalle (cod_comprobante, cod_cuenta, cod_cuentaauxiliar, cod_unidadorganizacional, cod_area, debe, haber, glosa, orden) VALUES ('$codComprobante','$cod_contra_cuenta','0','$cod_uo_tcc','$cod_area_tcc','0','$monto_contracuenta','$descripcion_contra_cuenta','$ordenDetalle')";
			            $stmtInsertDet = $dbh->prepare($sqlInsertDet);
			            $flagSuccessDet=$stmtInsertDet->execute();
			            $ordenDetalle++;
                    }
                }
                $sw_facturas++;//contador de facturas incrementa
            }
        }else{//compra no tiene factura registrada
	        //buscamos el tipo de retencion
	        $stmtRetenciones = $dbh->prepare("SELECT cod_cuenta,porcentaje,debe_haber from configuracion_retencionesdetalle where cod_configuracionretenciones=$cod_retencioncajachica");
	        $stmtRetenciones->execute();
	        $stmtRetenciones->bindColumn('cod_cuenta', $cod_cuenta_retencion);
	        $stmtRetenciones->bindColumn('porcentaje', $porcentaje_retencion);
	        $stmtRetenciones->bindColumn('debe_haber', $debe_haber);
	        while ($rowFac = $stmtRetenciones->fetch()) 
	        {                            
	            //recalculando monto
	            $descripcionIT=$nombre_uo.' SF (R-'.$nro_recibo.'),'.$personal.', '.$observaciones_dcc;                                
	            $monto_it=$monto_recalculado*$porcentaje_retencion/100;            
	            //las retenciones
	            if($cod_cuenta_retencion>0){
	                
	                if($debe_haber==1){ //preguntamos si pertenece a la columna debe o haber
	                	$sqlInsertDet="INSERT INTO comprobantes_detalle (cod_comprobante, cod_cuenta, cod_cuentaauxiliar, cod_unidadorganizacional, cod_area, debe, haber, glosa, orden) VALUES ('$codComprobante','$cod_cuenta_retencion','0','$cod_uo','$cod_area','$monto_it','0','$descripcionIT','$ordenDetalle')";
			            $stmtInsertDet = $dbh->prepare($sqlInsertDet);
			            $flagSuccessDet=$stmtInsertDet->execute();
			            $ordenDetalle++;
	                }else{
	                	$sqlInsertDet="INSERT INTO comprobantes_detalle (cod_comprobante, cod_cuenta, cod_cuentaauxiliar, cod_unidadorganizacional, cod_area, debe, haber, glosa, orden) VALUES ('$codComprobante','$cod_cuenta_retencion','0','$cod_uo','$cod_area','0','$monto_it','$descripcionIT','$ordenDetalle')";
			            $stmtInsertDet = $dbh->prepare($sqlInsertDet);
			            $flagSuccessDet=$stmtInsertDet->execute();
			            $ordenDetalle++;
	                }
	            }else{                
	                $monto_restante=$monto_recalculado;//completo
	                //buscamos si tiene alguna contra cuenta registrada en estados_cuenta
	                $stmtContraCuenta = $dbh->prepare("SELECT cod_plancuenta from estados_cuenta where cod_cajachicadetalle=$codigo_ccdetalle");
	                $stmtContraCuenta->execute();
	                $resultContraCuenta = $stmtContraCuenta->fetch();
	                $cod_plancuenta = $resultContraCuenta['cod_plancuenta']; 
	                // echo "llego: ".$cod_plancuenta;
	                if($cod_plancuenta>0){
	                    //buscamos el nombre y el numero de la contra cuenta                    
	                    $descripcionIT=$nombre_uo.'/'.$nombre_area.' '.$proveedor.' SF, '.$observaciones_dcc;
	                    if($debe_haber==1){ //debe=1
	                    	$sqlInsertDet="INSERT INTO comprobantes_detalle (cod_comprobante, cod_cuenta, cod_cuentaauxiliar, cod_unidadorganizacional, cod_area, debe, haber, glosa, orden) VALUES ('$codComprobante','$cod_plancuenta','0','$cod_uo','$cod_area','0','$monto_restante','$descripcionIT','$ordenDetalle')";
				            $stmtInsertDet = $dbh->prepare($sqlInsertDet);
				            $flagSuccessDet=$stmtInsertDet->execute();
				            $ordenDetalle++;
	                    }else{//haber=2
	                    	
				            $sqlInsertDet="INSERT INTO comprobantes_detalle (cod_comprobante, cod_cuenta, cod_cuentaauxiliar, cod_unidadorganizacional, cod_area, debe, haber, glosa, orden) VALUES ('$codComprobante','$cod_plancuenta','0','$cod_uo','$cod_area','$monto_restante','0','$descripcionIT','$ordenDetalle')";
				            $stmtInsertDet = $dbh->prepare($sqlInsertDet);
				            $flagSuccessDet=$stmtInsertDet->execute();
				            $ordenDetalle++;
	                    }
	                    
	                    //sacamos el codigo del comprobante detalle 
	                    $stmtEstadoCuenta = $dbh->prepare("SELECT codigo from comprobantes_detalle where cod_comprobante=$codComprobante order by codigo desc LIMIT 1");
		                $stmtEstadoCuenta->execute();
		                $resultEstadoCuenta = $stmtEstadoCuenta->fetch();
		                $cod_compro_det = $resultEstadoCuenta['codigo'];
		                //actualizamos el campo comporbante del estado_cuentas 
		                $stmtUpdateEstadoCuenta = $dbh->prepare("UPDATE estados_cuenta set cod_comprobantedetalle=$cod_compro_det where cod_cajachicadetalle=$codigo_ccdetalle");
		                $stmtUpdateEstadoCuenta->execute();

	                }else{		                	
                    	if($porcentaje_cuentaorigen>100){
				        	$monto_restante=$monto_recalculado;
				        }else{
				        	// $monto_restante=$monto_recalculado*$porcentaje_cuentaorigen/100;       
				        	$monto_restante=$monto_recalculado;
				        }
				        //Desde aqui las distribuciones por area y/o oficina
						$cont_tipo_distribucion=0;//verificará si se registró alguna distribucion
						$stmtTipoDistri=$dbh->prepare("SELECT codigo from distribucion_gastos_caja_chica where cod_cajachica_detalle=$codigo_ccdetalle GROUP BY tipo_distribucion");
		                $stmtTipoDistri->execute();
						while ($rowTipoDistr = $stmtTipoDistri->fetch()){
							$cont_tipo_distribucion++;
						}								
						if($cont_tipo_distribucion==1){//distribucion solo area u oficina
							$stmtTipoDistribucion = $dbh->prepare("SELECT tipo_distribucion,oficina_area,porcentaje from distribucion_gastos_caja_chica where porcentaje>0 and cod_cajachica_detalle=$codigo_ccdetalle order by tipo_distribucion");
			                $stmtTipoDistribucion->execute();
			                while ($rowTipoDistribucion = $stmtTipoDistribucion->fetch()){
			                	$tipo=$rowTipoDistribucion['tipo_distribucion'];
			                	$oficina_area=$rowTipoDistribucion['oficina_area'];
			                	$porcentaje=$rowTipoDistribucion['porcentaje'];
			                	if($tipo==1){//oficina
			                		$name_oficina_dis=abrevUnidad($oficina_area);
			                		$descripcion_distribucion=$nombre_uo.'/'.$name_oficina_dis.' SF (R-'.$nro_recibo.'),'.$personal.', '.$observaciones_dcc;
		                            
		                            $monto_of=$monto_restante*$porcentaje/100;
		                            if($debe_haber==1){ //preguntamps si pertenece a la columna debe o haber
		                            	$sqlInsertDet="INSERT INTO comprobantes_detalle (cod_comprobante, cod_cuenta, cod_cuentaauxiliar, cod_unidadorganizacional, cod_area, debe, haber, glosa, orden) VALUES ('$codComprobante','$cod_cuenta','0','$oficina_area','$cod_area','0','$monto_of','$descripcion_distribucion','$ordenDetalle')";
							            $stmtInsertDet = $dbh->prepare($sqlInsertDet);
							            $flagSuccessDet=$stmtInsertDet->execute();
							            $ordenDetalle++;
		                            }else{
		                                $sqlInsertDet="INSERT INTO comprobantes_detalle (cod_comprobante, cod_cuenta, cod_cuentaauxiliar, cod_unidadorganizacional, cod_area, debe, haber, glosa, orden) VALUES ('$codComprobante','$cod_cuenta','0','$oficina_area','$cod_area','$monto_of','0','$descripcion_distribucion','$ordenDetalle')";
							            $stmtInsertDet = $dbh->prepare($sqlInsertDet);
							            $flagSuccessDet=$stmtInsertDet->execute();
							            $ordenDetalle++;                               
		                            }
			                	}elseif($tipo==2){//area					                		
			                		$name_area_dis=abrevArea($oficina_area);					                	
			                		$descripcion_distribucion=$nombre_area.'/'.$name_area_dis.' SF (R-'.$nro_recibo.'),'.$personal.', '.$observaciones_dcc;
		                            $monto_of=$monto_restante*$porcentaje/100;
		                            if($debe_haber==1){ //preguntamps si pertenece a la columna debe o haber
		                            	$sqlInsertDet="INSERT INTO comprobantes_detalle (cod_comprobante, cod_cuenta, cod_cuentaauxiliar, cod_unidadorganizacional, cod_area, debe, haber, glosa, orden) VALUES ('$codComprobante','$cod_cuenta','0','$cod_uo','$oficina_area','0','$monto_of','$descripcion_distribucion','$ordenDetalle')";
							            $stmtInsertDet = $dbh->prepare($sqlInsertDet);
							            $flagSuccessDet=$stmtInsertDet->execute();
							            $ordenDetalle++;
		                            }else{
		                                $sqlInsertDet="INSERT INTO comprobantes_detalle (cod_comprobante, cod_cuenta, cod_cuentaauxiliar, cod_unidadorganizacional, cod_area, debe, haber, glosa, orden) VALUES ('$codComprobante','$cod_cuenta','0','$cod_uo','$oficina_area','$monto_of','0','$descripcion_distribucion','$ordenDetalle')";
							            $stmtInsertDet = $dbh->prepare($sqlInsertDet);
							            $flagSuccessDet=$stmtInsertDet->execute();
							            $ordenDetalle++;                               
		                            }
		                            // echo "hasta aqui--";
			                	}				                	
			                }
						}elseif($cont_tipo_distribucion==2){//distribucion area y oficina
							$monto_uo_distribuido=$monto_restante*40/100;
							$monto_area_distribuido=$monto_restante*60/100;

							$stmtTipoDistribucion = $dbh->prepare("SELECT tipo_distribucion,oficina_area,porcentaje from distribucion_gastos_caja_chica where porcentaje>0 and cod_cajachica_detalle=$codigo_ccdetalle order by tipo_distribucion");
			                $stmtTipoDistribucion->execute();
			                while ($rowTipoDistribucion = $stmtTipoDistribucion->fetch()){
			                	$tipo=$rowTipoDistribucion['tipo_distribucion'];
			                	$oficina_area=$rowTipoDistribucion['oficina_area'];
			                	$porcentaje=$rowTipoDistribucion['porcentaje'];
			                	if($tipo==1){//oficina
			                		$name_oficina_dis=abrevUnidad($oficina_area);
			                		$descripcion_distribucion=$nombre_uo.'/'.$name_oficina_dis.' SF (R-'.$nro_recibo.'),'.$personal.', '.$observaciones_dcc;
		                            
		                            $monto_of=$monto_uo_distribuido*$porcentaje/100;
		                            if($debe_haber==1){ //preguntamps si pertenece a la columna debe o haber
		                            	$sqlInsertDet="INSERT INTO comprobantes_detalle (cod_comprobante, cod_cuenta, cod_cuentaauxiliar, cod_unidadorganizacional, cod_area, debe, haber, glosa, orden) VALUES ('$codComprobante','$cod_cuenta','0','$oficina_area','$cod_area','0','$monto_of','$descripcion_distribucion','$ordenDetalle')";
							            $stmtInsertDet = $dbh->prepare($sqlInsertDet);
							            $flagSuccessDet=$stmtInsertDet->execute();
							            $ordenDetalle++;
		                            }else{
		                                $sqlInsertDet="INSERT INTO comprobantes_detalle (cod_comprobante, cod_cuenta, cod_cuentaauxiliar, cod_unidadorganizacional, cod_area, debe, haber, glosa, orden) VALUES ('$codComprobante','$cod_cuenta','0','$oficina_area','$cod_area','$monto_of','0','$descripcion_distribucion','$ordenDetalle')";
							            $stmtInsertDet = $dbh->prepare($sqlInsertDet);
							            $flagSuccessDet=$stmtInsertDet->execute();
							            $ordenDetalle++;                               
		                            }
			                	}elseif($tipo==2){//area				                		
			                		$name_area_dis=abrevArea($oficina_area);
			                		$descripcion_distribucion=$nombre_area.'/'.$name_area_dis.' SF (R-'.$nro_recibo.'),'.$personal.', '.$observaciones_dcc;
		                            $monto_of=$monto_area_distribuido*$porcentaje/100;
		                            if($debe_haber==1){ //preguntamps si pertenece a la columna debe o haber
		                            	$sqlInsertDet="INSERT INTO comprobantes_detalle (cod_comprobante, cod_cuenta, cod_cuentaauxiliar, cod_unidadorganizacional, cod_area, debe, haber, glosa, orden) VALUES ('$codComprobante','$cod_cuenta','0','$cod_uo','$oficina_area','0','$monto_of','$descripcion_distribucion','$ordenDetalle')";
							            $stmtInsertDet = $dbh->prepare($sqlInsertDet);
							            $flagSuccessDet=$stmtInsertDet->execute();
							            $ordenDetalle++;
		                            }else{
		                                $sqlInsertDet="INSERT INTO comprobantes_detalle (cod_comprobante, cod_cuenta, cod_cuentaauxiliar, cod_unidadorganizacional, cod_area, debe, haber, glosa, orden) VALUES ('$codComprobante','$cod_cuenta','0','$cod_uo','$oficina_area','$monto_of','0','$descripcion_distribucion','$ordenDetalle')";
							            $stmtInsertDet = $dbh->prepare($sqlInsertDet);
							            $flagSuccessDet=$stmtInsertDet->execute();
							            $ordenDetalle++;                               
		                            }
			                	}				                	
			                }
						}elseif($cont_tipo_distribucion==0){//distribucion normal
							$cod_uo_config=obtenerValorConfiguracion(15);
		                    if($cod_uo==$cod_uo_config){
		                        //desde aqui repartimos la contabilizacion a las oficinas 
		                        $stmtOficina = $dbh->prepare("SELECT dgd.cod_unidadorganizacional,dgd.porcentaje,
		                           (SELECT uo.abreviatura FROM unidades_organizacionales uo WHERE uo.codigo=dgd.cod_unidadorganizacional) as oficina
		                        from distribucion_gastosporcentaje_detalle dgd,distribucion_gastosporcentaje dg
		                        where dgd.cod_distribucion_gastos=dg.codigo and dg.estado=1 and porcentaje>0");
		                        $stmtOficina->execute();
		                        $stmtOficina->bindColumn('cod_unidadorganizacional', $cod_unidadorganizacional);
		                        $stmtOficina->bindColumn('porcentaje', $porcentaje);
		                        $stmtOficina->bindColumn('oficina', $oficinaFac);
		                        while ($rowOf = $stmtOficina->fetch()) 
		                        {                                                                            
		                            $descripcion_of=$oficinaFac.'/'.$nombre_uo.' SF (R-'.$nro_recibo.'),'.$personal.', '.$observaciones_dcc;
		                            $monto_of=$monto_restante*$porcentaje/100;

		                            if($debe_haber==1){ //preguntamps si pertenece a la columna debe o haber
		                            	$sqlInsertDet="INSERT INTO comprobantes_detalle (cod_comprobante, cod_cuenta, cod_cuentaauxiliar, cod_unidadorganizacional, cod_area, debe, haber, glosa, orden) VALUES ('$codComprobante','$cod_cuenta','0','$cod_unidadorganizacional','$centroCostosDN','0','$monto_of','$descripcion_of','$ordenDetalle')";
							            $stmtInsertDet = $dbh->prepare($sqlInsertDet);
							            $flagSuccessDet=$stmtInsertDet->execute();
							            $ordenDetalle++;
		                            }else{
		                                $sqlInsertDet="INSERT INTO comprobantes_detalle (cod_comprobante, cod_cuenta, cod_cuentaauxiliar, cod_unidadorganizacional, cod_area, debe, haber, glosa, orden) VALUES ('$codComprobante','$cod_cuenta','0','$cod_unidadorganizacional','$centroCostosDN','$monto_of','0','$descripcion_of','$ordenDetalle')";
							            $stmtInsertDet = $dbh->prepare($sqlInsertDet);
							            $flagSuccessDet=$stmtInsertDet->execute();
							            $ordenDetalle++;                               
		                            }
		                        }
		                    }else{
		                        $descripcion_of=$nombre_uo.'/'.$nombre_area.' SF (R-'.$nro_recibo.'),'.$personal.', '.$observaciones_dcc;
		                        if($debe_haber==1){ //preguntamps si pertenece a la columna debe o haber
		                        	$sqlInsertDet="INSERT INTO comprobantes_detalle (cod_comprobante, cod_cuenta, cod_cuentaauxiliar, cod_unidadorganizacional, cod_area, debe, haber, glosa, orden) VALUES ('$codComprobante','$cod_cuenta','0','$cod_uo','$cod_area','0','$monto_restante','$descripcion_of','$ordenDetalle')";
						            $stmtInsertDet = $dbh->prepare($sqlInsertDet);
						            $flagSuccessDet=$stmtInsertDet->execute();
						            $ordenDetalle++;
		                        }else{
		                            $sqlInsertDet="INSERT INTO comprobantes_detalle (cod_comprobante, cod_cuenta, cod_cuentaauxiliar, cod_unidadorganizacional, cod_area, debe, haber, glosa, orden) VALUES ('$codComprobante','$cod_cuenta','0','$cod_uo','$cod_area','$monto_restante','0','$descripcion_of','$ordenDetalle')";
						            $stmtInsertDet = $dbh->prepare($sqlInsertDet);
						            $flagSuccessDet=$stmtInsertDet->execute();
						            $ordenDetalle++;
		                        }
		                    }
						}				                    
	                }
	                //contra cuenta
	                $descripcion_contra_cuenta='CONTABILIZACIÓN CAJA CHICA.'.$personal.'/'.$proveedor.', '.$observaciones_dcc;
	                $monto_contracuenta=$monto_recalculado*$porcentaje_retencion/100;
	                if($debe_haber==1){//si es debe, pondremos en haber
	                	$sqlInsertDet="INSERT INTO comprobantes_detalle (cod_comprobante, cod_cuenta, cod_cuentaauxiliar, cod_unidadorganizacional, cod_area, debe, haber, glosa, orden) VALUES ('$codComprobante','$cod_contra_cuenta','0','$cod_uo_tcc','$cod_area_tcc','$monto_contracuenta','0','$descripcion_contra_cuenta','$ordenDetalle')";
			            $stmtInsertDet = $dbh->prepare($sqlInsertDet);
			            $flagSuccessDet=$stmtInsertDet->execute();
			            $ordenDetalle++;
	                }else{
	                	$sqlInsertDet="INSERT INTO comprobantes_detalle (cod_comprobante, cod_cuenta, cod_cuentaauxiliar, cod_unidadorganizacional, cod_area, debe, haber, glosa, orden) VALUES ('$codComprobante','$cod_contra_cuenta','0','$cod_uo_tcc','$cod_area_tcc','0','$monto_contracuenta','$descripcion_contra_cuenta','$ordenDetalle')";
			            $stmtInsertDet = $dbh->prepare($sqlInsertDet);
			            $flagSuccessDet=$stmtInsertDet->execute();
			            $ordenDetalle++;
	                }
	            }            
	        }
        
    	}
    }

?>