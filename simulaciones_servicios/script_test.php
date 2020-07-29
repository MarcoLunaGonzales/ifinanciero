<?php
$nro_factura=498;
GenerarComprobanteExistente($nro_factura);

function GenerarComprobanteExistente($codigo_factura){
	require_once __DIR__.'/../conexion.php';
	require_once '../functions.php';
	require_once __DIR__.'/../functionsGeneral.php';
	try{
		$dbh = new Conexion();

		$sql="SELECT f.cod_unidadorganizacional,f.cod_area,f.cod_tipopago,f.razon_social,f.nit,f.nro_factura,f.cod_comprobante,f.cod_libretabancariadetalle from facturas_venta f where f.nro_factura=$codigo_factura";
		$stmtFactura = $dbh->prepare($sql);
		$stmtFactura->execute();
		$result=$stmtFactura->fetch();


		$nitciCliente=$result['nit'];
		$razon_social=$result['razon_social'];
		$monto_total=$result['nit'];
		$nro_factura=$result['nro_factura'];
		$cod_tipoPago=$result['cod_tipopago'];
		$cod_cuenta_libreta=$result['cod_libretabancariadetalle'];
		$cod_area_solicitud=$result['cod_area'];
		$cod_uo_solicitud = 5;
		$codComprobante=$result['cod_comprobante'];
		if($codComprobante==0 || $codComprobante=='' || $codComprobante==null){
			echo "factura sin codigo de comprobante";
		}else{
			$sqlDelete="DELETE from comprobantes_detalle where cod_comprobante=$codComprobante)";
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
			if($tipoPago==4){//caso payme
				if($cod_cuenta_libreta!='0'){					
					$cod_cuenta_libreta=trim($cod_cuenta_libreta,",");
					$sqlTipopago="SELECT codigo,cod_estado,monto from libretas_bancariasdetalle where codigo in (cod_cuenta_libreta) order by  monto desc";
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
					if($monto_libreta_total<$monto_total){					
						$sqldeletecomprobante="DELETE from comprobantes where codigo=$codComprobante";
                        $stmtDeleteCopmprobante = $dbh->prepare($sqldeletecomprobante);
                        $flagSuccess=$stmtDeleteCopmprobante->execute();
                        $sqldeletecomprobanteDet="DELETE from comprobantes_detalle where cod_comprobante=$codComprobante";
                        $stmtDeleteComprobanteDet = $dbh->prepare($sqldeletecomprobanteDet);
                        $flagSuccess=$stmtDeleteComprobanteDet->execute();
                        return "-1";//error en montos
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
			
			echo "Comprobante generado correctamente";			
		}
	} catch(PDOException $ex){
	    echo "Error al generar Comprobante";
	}	
}
?>