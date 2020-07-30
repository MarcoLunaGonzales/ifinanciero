<?php
if(isset($_GET['numero'])){
	$nro_factura=$_GET['numero'];
	GenerarComprobanteExistente($nro_factura);
}else{
	echo "Sin Nro de factura";
}


function GenerarComprobanteExistente($codigo_factura){
	require_once __DIR__.'/../conexion.php';
	require_once '../functions.php';
	require_once __DIR__.'/../functionsGeneral.php';
	try{
		$dbh = new Conexion();

		$sql="SELECT f.codigo,f.cod_solicitudfacturacion,f.cod_unidadorganizacional,f.cod_area,f.cod_tipopago,f.razon_social,f.nit,f.nro_factura,f.cod_comprobante,f.cod_libretabancariadetalle from facturas_venta f where f.nro_factura=$codigo_factura";		
		$stmtFactura = $dbh->prepare($sql);
		$stmtFactura->execute();
		$result=$stmtFactura->fetch();
		$codigo_x=$result['codigo'];
		$nitciCliente=$result['nit'];
		$razon_social=$result['razon_social'];
		$monto_total=$result['nit'];
		$nro_factura=$result['nro_factura'];
		$cod_tipoPago=$result['cod_tipopago'];
		$cod_cuenta_libreta=$result['cod_libretabancariadetalle'];		
		$cod_area_solicitud=$result['cod_area'];
		$cod_uo_solicitud = $result['cod_unidadorganizacional'];
		$codComprobante=$result['cod_comprobante'];		
		$cod_solicitudfacturacion=$result['cod_solicitudfacturacion'];		
		if($codComprobante==0 || $codComprobante=='' || $codComprobante==null){
			echo "factura sin codigo de comprobante";
		}else{
			if($cod_cuenta_libreta=='' || $cod_cuenta_libreta==null)$cod_cuenta_libreta=0;
			$sqlDelete="DELETE from comprobantes_detalle where cod_comprobante=$codComprobante";			
            $stmtDelete = $dbh->prepare($sqlDelete);
            $stmtDelete->execute();

		    $codigo_alterno = '';	    			
			$sqlDetalle="SELECT cantidad,precio,descripcion_alterna from facturas_ventadetalle where cod_facturaventa=$codigo_factura";
			$stmtDetalle = $dbh->prepare($sqlDetalle);
			$stmtDetalle->execute();							
			$stmtDetalle->bindColumn('cantidad', $cantidad);
			$stmtDetalle->bindColumn('precio', $precio);
			$stmtDetalle->bindColumn('descripcion_alterna', $descripcion_alterna);
			$concepto_contabilizacion=" ";
			$monto_total=0;
			while ($row_detTipopago = $stmtDetalle->fetch()) {
				$precio_x=$cantidad*$precio;
				$monto_total+=$precio_x;
				$concepto_contabilizacion.=$descripcion_alterna." / F ".$nro_factura." / ".$razon_social."<br>\n";
				$concepto_contabilizacion.="Cantidad: ".$cantidad." * ".formatNumberDec($precio)." = ".formatNumberDec($precio_x)."<br>\n";
			}
			//actualizamos la cabecera
			$sqlUpdateCabecera="UPDATE comprobantes set glosa='$concepto_contabilizacion' where codigo=$codComprobante";
            $stmtUpdateCAbecera = $dbh->prepare($sqlUpdateCabecera);
            $stmtUpdateCAbecera->execute();
            // echo $cod_solicitudfacturacion;
	        if($cod_solicitudfacturacion!=-100){
				//listado del detalle tipo pago
				$sql="SELECT t.*,(select p.cod_cuenta from tipos_pago_contabilizacion p where p.cod_tipopago=t.cod_tipopago)as cod_cuenta from solicitudes_facturacion_tipospago t where t.cod_solicitudfacturacion=$cod_solicitudfacturacion order by t.monto desc";			
				$stmtDetalleTipoPago_x = $dbh->prepare($sql);
				$stmtDetalleTipoPago_x->execute();
				$stmtDetalleTipoPago_x->bindColumn('cod_tipopago', $cod_tipopago);	 
				$stmtDetalleTipoPago_x->bindColumn('porcentaje', $porcentaje);	
				$stmtDetalleTipoPago_x->bindColumn('monto', $monto_tipopago);	  
				$stmtDetalleTipoPago_x->bindColumn('cod_cuenta', $cod_cuenta);  
				$monto_tipopago_total=0;
				$ordenDetalle=1;
				$sw=0;
				while ($row_detTipopago = $stmtDetalleTipoPago_x->fetch()) {
					$descripcion=$concepto_contabilizacion;
					$monto_tipopago_total+=$monto_tipopago;
					$cod_tipopago_deposito_cuenta=obtenerValorConfiguracion(55);
					$cod_tipopago_tarjetas=obtenerValorConfiguracion(59);
					$cod_tipopago_anticipo=obtenerValorConfiguracion(64);

					//cuenta de libreta bancaria, en cod_libretas_X viene el string de codigos de libreta
					if($cod_tipopago==$cod_tipopago_deposito_cuenta){
						//Agrupamos los estados de las libretas bancarias 
						$sql="SELECT cod_libretabancariadetalle from libretas_bancariasdetalle_facturas where cod_facturaventa=$codigo_x";
						// echo $sql;
						$stmtLibretaCadena = $dbh->prepare($sql);
						$stmtLibretaCadena->execute();							
						$stmtLibretaCadena->bindColumn('cod_libretabancariadetalle', $cod_libretabancariadetalle);
						$cod_libretas_X="";
						while ($row_libreta = $stmtLibretaCadena->fetch()) {
							$cod_libretas_X.=$cod_libretabancariadetalle.",";
						}
						$cod_libretas_X=trim($cod_libretas_X,",");
						if($cod_libretas_X=="")$sw=1;

						$sqlTipopago="SELECT codigo,cod_estado,monto from libretas_bancariasdetalle where codigo in ($cod_libretas_X)";
						// echo $sqlTipopago;
						$stmtLibreta = $dbh->prepare($sqlTipopago);
						$stmtLibreta->execute();							
						$stmtLibreta->bindColumn('codigo', $codigo_libreta_det);
						$stmtLibreta->bindColumn('cod_estado', $estado_libreta);  
						$stmtLibreta->bindColumn('monto', $monto_libreta);
						$monto_libreta_total=0;
						$array_libreta_save="";
						$sw_controlador=0;//contrala la entradas

						// echo $cod_libretas_X."--";
						while ($row_libreta = $stmtLibreta->fetch()) {		
							// $monto_facturas=obtenerTotalFacturasLibreta($codigo_libreta_det);
							$monto_libreta_x=obtenerSaldoLibretaBancariaDetalle($codigo_libreta_det);
							if($monto_libreta_x==0){
								$monto_libreta=$monto_libreta;
							}else{
								$monto_libreta=$monto_libreta_x;
							}
							// echo $monto_libreta."-";
							// $monto_libreta=$monto_libreta-$monto_facturas;
							if($sw_controlador==0){
								$monto_libreta_total+=$monto_libreta;
								if($monto_tipopago>=$monto_libreta_total){
									// $array_libreta_save.=$codigo_libreta_det.",";
									if($estado_libreta==0){//cueenta de libreta
						                $cod_cuenta_libr=obtenerCuentaLibretaBancaria($codigo_libreta_det);
						                $flagSuccessDet=insertarDetalleComprobante($codComprobante,$cod_cuenta_libr,0,$cod_uo_solicitud,$cod_area_solicitud,$monto_libreta,0,$descripcion,$ordenDetalle);
						            }elseif($estado_libreta==1){//contra cuenta de libreta
						                $cod_contracuenta_libr=obtenerContraCuentaLibretaBancaria($codigo_libreta_det);
										$flagSuccessDet=insertarDetalleComprobante($codComprobante,$cod_contracuenta_libr,0,$cod_uo_solicitud,$cod_area_solicitud,$monto_libreta,0,$descripcion,$ordenDetalle);
						            }					            
						            $sqlUpdateLibreta="INSERT into libretas_bancariasdetalle_facturas(cod_libretabancariadetalle,cod_facturaventa) values ($codigo_libreta_det,$codigo_x)";					            
			                        $stmtUpdateLibreta = $dbh->prepare($sqlUpdateLibreta);
			                        $stmtUpdateLibreta->execute();
								}else{
									// $array_libreta_save.=$codigo_libreta_det.",";
									$saldo_libreta=$monto_libreta_total-$monto_libreta;//volvemos al monto anterior
									$monto_libreta_saldo=$monto_tipopago-$saldo_libreta;
									if($estado_libreta==0){//cueenta de libreta								
						                $cod_cuenta_libr=obtenerCuentaLibretaBancaria($codigo_libreta_det);
						                $flagSuccessDet=insertarDetalleComprobante($codComprobante,$cod_cuenta_libr,0,$cod_uo_solicitud,$cod_area_solicitud,$monto_libreta_saldo,0,$descripcion,$ordenDetalle);
						            }elseif($estado_libreta==1){//contra cuenta de libreta
						                $cod_contracuenta_libr=obtenerContraCuentaLibretaBancaria($codigo_libreta_det);
										$flagSuccessDet=insertarDetalleComprobante($codComprobante,$cod_contracuenta_libr,0,$cod_uo_solicitud,$cod_area_solicitud,$monto_libreta_saldo,0,$descripcion,$ordenDetalle);
						            }
						            $sqlUpdateLibreta="INSERT into libretas_bancariasdetalle_facturas(cod_libretabancariadetalle,cod_facturaventa) values ($codigo_libreta_det,$codigo_x)";
			                        $stmtUpdateLibreta = $dbh->prepare($sqlUpdateLibreta);
			                        $stmtUpdateLibreta->execute();
						            $sw_controlador=1;
								}
							}
						}
					}elseif($cod_tipopago==$cod_tipopago_tarjetas){//cuenta de tarjeta de credito
						$cod_cuenta_tarjetacredito=obtenerValorConfiguracion(60);
						$porcentaje_cuenta_transitoria=obtenerValorConfiguracion(61);
						$porcentaje_xyz=100-$porcentaje_cuenta_transitoria;
						$monto_tipopago_1=$monto_tipopago*$porcentaje_xyz/100;
						$monto_tipopago_2=$monto_tipopago*$porcentaje_cuenta_transitoria/100;
						$flagSuccessDet=insertarDetalleComprobante($codComprobante,$cod_cuenta,0,$cod_uo_solicitud,$cod_area_solicitud,$monto_tipopago_1,0,$descripcion,$ordenDetalle);
						$ordenDetalle++;
						$flagSuccessDet=insertarDetalleComprobante($codComprobante,$cod_cuenta_tarjetacredito,0,$cod_uo_solicitud,$cod_area_solicitud,$monto_tipopago_2,0,$descripcion,$ordenDetalle);
					}elseif($cod_tipopago==$cod_tipopago_anticipo ){//tipo de pago anticipo en $cod_estado_cuenta_x viene el codigo de estado de cuenta
						$cod_compte_origen=obtenerCod_comprobanteDetalleorigen($cod_estado_cuenta_x);
						$flagSuccessDet=insertarDetalleComprobante($codComprobante,$cod_cuenta,0,$cod_uo_solicitud,$cod_area_solicitud,$monto_tipopago,0,$descripcion,$ordenDetalle);
						$stmtdetalleCom = $dbh->prepare("SELECT codigo from comprobantes_detalle where cod_comprobante=$codComprobante and orden=$ordenDetalle");
						//en este caso insertamos la contra cuenta del estado de cuenta, para ello necesitamos el codigo del comprobnate detalle
						$stmtdetalleCom->execute();			
						$stmtdetalleCom->bindColumn('codigo', $cod_comprobante_detalle);
						while ($row = $stmtdetalleCom->fetch()) {						
							$cod_comprobante_detalle=$cod_comprobante_detalle;			
						}
						$cuenta_axiliar=obtenerValorConfiguracion(63);//cod cuenta auxiliar por defecto para la anulacion de facturas		
						$cod_proveedor=obtenerCodigoProveedorCuentaAux($cuenta_axiliar);	
						$sqlEstadoCuenta="INSERT into estados_cuenta(cod_comprobantedetalle,cod_plancuenta,monto,cod_proveedor,fecha,cod_comprobantedetalleorigen,cod_cuentaaux,cod_tipoestadocuenta,glosa_auxiliar) values($cod_comprobante_detalle,$cod_cuenta,$monto_tipopago,$cod_proveedor,'$fechaActual','$cod_compte_origen',$cuenta_axiliar,'1','$concepto_contabilizacion')";
						// echo $sqlEstadoCuenta;
			            $stmtEstadoCuenta = $dbh->prepare($sqlEstadoCuenta);
			            $flagSuccess=$stmtEstadoCuenta->execute(); 
					}else{
						$flagSuccessDet=insertarDetalleComprobante($codComprobante,$cod_cuenta,0,$cod_uo_solicitud,$cod_area_solicitud,$monto_tipopago,0,$descripcion,$ordenDetalle);
					}
		            $ordenDetalle++;
				}
				if($sw==0){
					//para IT gasto		
					$cod_cuenta_it_gasto=obtenerValorConfiguracion(49);
					$porcentaje_it_gasto=obtenerValorConfiguracion(2);
					$monto_it_gasto=$porcentaje_it_gasto*$monto_tipopago_total/100;
					$descripcion_it_gasto=$concepto_contabilizacion;
					$flagSuccessDet=insertarDetalleComprobante($codComprobante,$cod_cuenta_it_gasto,0,$cod_uo_solicitud,$cod_area_solicitud,$monto_it_gasto,0,$descripcion_it_gasto,$ordenDetalle);
			        $ordenDetalle++;
			        //para IT
					$cod_cuenta_debito_iva=obtenerValorConfiguracion(50);
					$porcentaje_debito_iva=obtenerValorConfiguracion(1);
					$monto_debito_iva=$porcentaje_debito_iva*$monto_tipopago_total/100;
					$descripcion_debito_iva=$concepto_contabilizacion;
					$flagSuccessDet=insertarDetalleComprobante($codComprobante,$cod_cuenta_debito_iva,0,$cod_uo_solicitud,$cod_area_solicitud,0,$monto_debito_iva,$descripcion_debito_iva,$ordenDetalle);			
			        $ordenDetalle++;
			        //para IT pasivo
					$cod_cuenta_it_pasivo=obtenerValorConfiguracion(51);
					$porcentaje_it_pasivo=obtenerValorConfiguracion(2);
					$monto_it_pasivo=$porcentaje_it_pasivo*$monto_tipopago_total/100;
					$descripcion_it_pasivo=$concepto_contabilizacion;
					$flagSuccessDet=insertarDetalleComprobante($codComprobante,$cod_cuenta_it_pasivo,0,$cod_uo_solicitud,$cod_area_solicitud,0,$monto_it_pasivo,$descripcion_it_pasivo,$ordenDetalle);
			        $ordenDetalle++;
			        //listado del detalle area
					$stmtDetalleAreas = $dbh->prepare("SELECT t.*,(select a.cod_cuenta_ingreso from areas a where a.codigo=t.cod_area)as cod_cuenta from solicitudes_facturacion_areas t where t.cod_solicitudfacturacion=$cod_solicitudfacturacion");
					$stmtDetalleAreas->execute();
					$stmtDetalleAreas->bindColumn('cod_area', $cod_area_areas);	 
					$stmtDetalleAreas->bindColumn('porcentaje', $porcentaje);	
					$stmtDetalleAreas->bindColumn('monto', $monto_areas);
					$stmtDetalleAreas->bindColumn('cod_cuenta', $cod_cuenta_areas);
					$porcentaje_pasivo=100-$porcentaje_debito_iva;			
					while ($row_detAreas = $stmtDetalleAreas->fetch()) {
						$monto_areas_format=$monto_areas*$porcentaje_pasivo/100;
						$descripcion=$concepto_contabilizacion;
						//listado del detalle uo
						$stmtDetalleUO = $dbh->prepare("SELECT * from solicitudes_facturacion_areas_uo where cod_solicitudfacturacion=$cod_solicitudfacturacion and cod_area=$cod_area_areas");
						$stmtDetalleUO->execute();
						$stmtDetalleUO->bindColumn('cod_uo', $cod_uo_areas);	 
						$stmtDetalleUO->bindColumn('porcentaje', $porcentaje_uo);	
						$stmtDetalleUO->bindColumn('monto', $monto_areas);				
						while ($row_detAreas = $stmtDetalleUO->fetch()) {
							$monto_areas_uo=$monto_areas_format*$porcentaje_uo/100;					
							$flagSuccessDet=insertarDetalleComprobante($codComprobante,$cod_cuenta_areas,0,$cod_uo_areas,$cod_area_areas,0,$monto_areas_uo,$descripcion,$ordenDetalle);					
				            $ordenDetalle++;
						}
					}
					echo "Comprobante generado correctamente";
				}elseif($sw==1){echo "Codigo de libreta no registrado";}

				

				// header('Location: ../comprobantes/imp.php?comp='.$codComprobante.'&mon=1');
			}else{
				// echo $monto_total."---";
				$ordenDetalle=1;//<--		
				$cod_tipopago=obtenerValorConfiguracion(55);//deposito en cuenta
				$cod_cuenta=obtenerCodCuentaTipoPago($cod_tipopago);
				$descripcion=$concepto_contabilizacion;
				if($cod_tipopago=49){
					$tipoPago=4;
				}else{
					$tipoPago=5;
				}
				$sw=0;			
				if($tipoPago==4){//caso payme				
					if($cod_cuenta_libreta!='0'){					
						$cod_cuenta_libreta=trim($cod_cuenta_libreta,",");
						$sqlTipopago="SELECT codigo,cod_estado,monto from libretas_bancariasdetalle where codigo in ($cod_cuenta_libreta) order by  monto desc";
						// echo $sqlTipopago;
						$stmtDetalleTipoPago = $dbh->prepare($sqlTipopago);
						$stmtDetalleTipoPago->execute();							
						$stmtDetalleTipoPago->bindColumn('codigo', $codigo_libreta_det);
						$stmtDetalleTipoPago->bindColumn('cod_estado', $estado_libreta);  
						$stmtDetalleTipoPago->bindColumn('monto', $monto_libreta);
						$monto_libreta_total=0;
						// $array_libreta_save="";
						$sw_controlador=0;//contrala la entradas
						while ($row_detTipopago = $stmtDetalleTipoPago->fetch()) {						
							$monto_libreta_x=obtenerSaldoLibretaBancariaDetalle($codigo_libreta_det);
							if($monto_libreta_x==0){
								$monto_libreta=$monto_libreta;
							}else{
								$monto_libreta=$monto_libreta_x;
							}
							if($sw_controlador==0){
								$monto_libreta_total+=$monto_libreta;
								if($monto_total>=$monto_libreta_total){
									// $array_libreta_save.=$codigo_libreta_det.",";
									if($estado_libreta==0){//cueenta de libreta
						                $cod_cuenta_libr=obtenerCuentaLibretaBancaria($codigo_libreta_det);
						                $flagSuccessDet=insertarDetalleComprobante($codComprobante,$cod_cuenta_libr,0,$cod_uo_solicitud,$cod_area_solicitud,$monto_libreta,0,$descripcion,$ordenDetalle);
						            }elseif($estado_libreta==1){//contra cuenta de libreta
						                $cod_contracuenta_libr=obtenerContraCuentaLibretaBancaria($codigo_libreta_det);
										$flagSuccessDet=insertarDetalleComprobante($codComprobante,$cod_contracuenta_libr,0,$cod_uo_solicitud,$cod_area_solicitud,$monto_libreta,0,$descripcion,$ordenDetalle);
						            }
						            $sqlUpdateLibreta="INSERT into libretas_bancariasdetalle_facturas(cod_libretabancariadetalle,cod_facturaventa) values ($codigo_libreta_det,$cod_facturaventa)";
			                        $stmtUpdateLibreta = $dbh->prepare($sqlUpdateLibreta);
			                        $stmtUpdateLibreta->execute();
								}else{
									// $array_libreta_save.=$codigo_libreta_det.",";
									$saldo_libreta=$monto_libreta_total-$monto_libreta;//volvemos al monto anterior
									$monto_libreta_saldo=$monto_total-$saldo_libreta;
									if($estado_libreta==0){//cueenta de libreta								
						                $cod_cuenta_libr=obtenerCuentaLibretaBancaria($codigo_libreta_det);
						                $flagSuccessDet=insertarDetalleComprobante($codComprobante,$cod_cuenta_libr,0,$cod_uo_solicitud,$cod_area_solicitud,$monto_libreta_saldo,0,$descripcion,$ordenDetalle);
						            }elseif($estado_libreta==1){//contra cuenta de libreta
						                $cod_contracuenta_libr=obtenerContraCuentaLibretaBancaria($codigo_libreta_det);
										$flagSuccessDet=insertarDetalleComprobante($codComprobante,$cod_contracuenta_libr,0,$cod_uo_solicitud,$cod_area_solicitud,$monto_libreta_saldo,0,$descripcion,$ordenDetalle);
						            }
						            $sqlUpdateLibreta="INSERT into libretas_bancariasdetalle_facturas(cod_libretabancariadetalle,cod_facturaventa) values ($codigo_libreta_det,$cod_facturaventa)";
			                        $stmtUpdateLibreta = $dbh->prepare($sqlUpdateLibreta);
			                        $stmtUpdateLibreta->execute();
						            $sw_controlador=1;
								}
							}
						}	
						echo $monto_libreta_total."-".$monto_total;
						if($monto_libreta_total<$monto_total){
							$sw=1;
							$sqldeletecomprobante="DELETE from comprobantes where codigo=$codComprobante";
	                        $stmtDeleteCopmprobante = $dbh->prepare($sqldeletecomprobante);
	                        $flagSuccess=$stmtDeleteCopmprobante->execute();
	                        $sqldeletecomprobanteDet="DELETE from comprobantes_detalle where cod_comprobante=$codComprobante";
	                        $stmtDeleteComprobanteDet = $dbh->prepare($sqldeletecomprobanteDet);
	                        $flagSuccess=$stmtDeleteComprobanteDet->execute();
	                        
						}

					}else{
						$flagSuccessDet=insertarDetalleComprobante($codComprobante,$cod_cuenta,0,$cod_uo_solicitud,$cod_area_solicitud,$monto_total,0,$descripcion,$ordenDetalle);
					}
				}else{
					$cod_tipopago_tarjetas=obtenerValorConfiguracion(59);
					$cod_cuenta=obtenerCodCuentaTipoPago($cod_tipopago_tarjetas);
					$cod_cuenta_tarjetacredito=obtenerValorConfiguracion(60);
					$porcentaje_cuenta_transitoria=obtenerValorConfiguracion(61);
					$porcentaje_xyz=100-$porcentaje_cuenta_transitoria;
					$monto_tipopago_1=$monto_total*$porcentaje_xyz/100;
					$monto_tipopago_2=$monto_total*$porcentaje_cuenta_transitoria/100;

					$flagSuccessDet=insertarDetalleComprobante($codComprobante,$cod_cuenta,0,$cod_uo_solicitud,$cod_area_solicitud,$monto_tipopago_1,0,$descripcion,$ordenDetalle);
					$ordenDetalle++;
					$flagSuccessDet=insertarDetalleComprobante($codComprobante,$cod_cuenta_tarjetacredito,0,$cod_uo_solicitud,$cod_area_solicitud,$monto_tipopago_2,0,$descripcion,$ordenDetalle);
				}
				if($sw==0){
		            $ordenDetalle++;			
					//para IT gasto
					$cod_cuenta_it_gasto=obtenerValorConfiguracion(49);
					$porcentaje_it_gasto=obtenerValorConfiguracion(2);
					$monto_it_gasto=$porcentaje_it_gasto*$monto_total/100;
					$descripcion_it_gasto=$concepto_contabilizacion;
					$flagSuccessDet=insertarDetalleComprobante($codComprobante,$cod_cuenta_it_gasto,0,$cod_uo_solicitud,$cod_area_solicitud,$monto_it_gasto,0,$descripcion_it_gasto,$ordenDetalle);			
			        $ordenDetalle++;
			        //para IVA
					$cod_cuenta_debito_iva=obtenerValorConfiguracion(50);
					$porcentaje_debito_iva=obtenerValorConfiguracion(1);
					$monto_debito_iva=$porcentaje_debito_iva*$monto_total/100;
					$descripcion_debito_iva=$concepto_contabilizacion;
					$flagSuccessDet=insertarDetalleComprobante($codComprobante,$cod_cuenta_debito_iva,0,$cod_uo_solicitud,$cod_area_solicitud,0,$monto_debito_iva,$descripcion_debito_iva,$ordenDetalle);			
			        $ordenDetalle++;
			        //para IT pasivo
					$cod_cuenta_it_pasivo=obtenerValorConfiguracion(51);
					$porcentaje_it_pasivo=obtenerValorConfiguracion(2);
					$monto_it_pasivo=$porcentaje_it_pasivo*$monto_total/100;
					$descripcion_it_pasivo=$concepto_contabilizacion;
					$flagSuccessDet=insertarDetalleComprobante($codComprobante,$cod_cuenta_it_pasivo,0,$cod_uo_solicitud,$cod_area_solicitud,0,$monto_it_pasivo,$descripcion_it_pasivo,$ordenDetalle);			
			        $ordenDetalle++;	       
			        //ingresos por capacitacion	  
			        $porcentaje_pasivo=100-$porcentaje_debito_iva;
					$monto_areas_format=$monto_total*$porcentaje_pasivo/100;
					$descripcion=$concepto_contabilizacion;
					$cod_cuenta_areas=obtenerCodCuentaArea($cod_area_solicitud);
					$flagSuccessDet=insertarDetalleComprobante($codComprobante,$cod_cuenta_areas,0,$cod_uo_solicitud,$cod_area_solicitud,0,$monto_areas_format,$descripcion,$ordenDetalle);
		            $ordenDetalle++;
		            echo "Comprobante generado correctamente(Tienda)";			
		        }else{
		        	echo "El saldo de la libreta es menor al monto de la factura";//
		        }	
			}
			
			
			
		}
	} catch(PDOException $ex){
	    echo "Error al generar Comprobante";
	}	
}
?>