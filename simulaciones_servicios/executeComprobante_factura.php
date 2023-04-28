<?php //ESTADO FINALIZADO

 error_reporting(E_ALL);
 ini_set('display_errors', '1');


function ejecutarComprobanteSolicitud($cod_solicitudfacturacion,$stringFacturas,$stringFacturasCod,$cod_libretas_X,$cod_estado_cuenta_x,$cod_cuentaaux){
	require_once __DIR__.'/../conexion.php';
	require_once '../functions.php';
	require_once '../assets/libraries/CifrasEnLetras.php';
	require_once __DIR__.'/../functionsGeneral.php';
	$dbh = new Conexion();
	$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);//try
	set_time_limit(3000);
	date_default_timezone_set('America/La_Paz');
	// session_start();
	try{
		$cod_uo_unico=5;
		$stmtCajaChica = $dbh->prepare("SELECT cod_cliente,cod_unidadorganizacional, cod_area, observaciones, codigo_alterno, razon_social, tipo_solicitud, observaciones_2 from solicitudes_facturacion where codigo=$cod_solicitudfacturacion");
	    $stmtCajaChica->execute();
	    $resultCCD = $stmtCajaChica->fetch();
	    $cod_uo_solicitud = $resultCCD['cod_unidadorganizacional'];    
	    $cod_area_solicitud = $resultCCD['cod_area'];    
	    $observaciones_solicitud = $resultCCD['observaciones'];    
	    $codigo_alterno = $resultCCD['codigo_alterno'];    
	    $razon_social = $resultCCD['razon_social'];
	    $tipo_solicitud = $resultCCD['tipo_solicitud'];
	    $cod_cliente = $resultCCD['cod_cliente'];
	    $glosa_factura3=$resultCCD['observaciones_2'];

		//datos para el comprbant
		$globalUser=$_SESSION["globalUser"];
		$mesTrabajo=$_SESSION['globalMes'];
		$gestionTrabajo=$_SESSION['globalNombreGestion'];
		$codEmpresa=1;
		$codAnio=date("Y");
		$codMoneda=1;
		$codEstadoComprobante=1;

		$fechaActual=date("Y-m-d H:i:s");		
		$tipoComprobante=4;//facturas
		$nombreTipoComprobante=abrevTipoComprobante($tipoComprobante);
		$numeroComprobante=obtenerCorrelativoComprobante3($tipoComprobante,$codAnio);
		$numeroX=str_pad($numeroComprobante, 5, "0", STR_PAD_LEFT);	
		$nombreComprobante=$nombreTipoComprobante."-".$numeroX;
		//sacamos nombre de los detalles
		$stmtDetalleSol = $dbh->prepare("SELECT cantidad,precio,descripcion_alterna from solicitudes_facturaciondetalle where cod_solicitudfacturacion=$cod_solicitudfacturacion");
		$stmtDetalleSol->execute();
		$stmtDetalleSol->bindColumn('cantidad', $cantidad);	 
		$stmtDetalleSol->bindColumn('precio', $precio_unitario);			
		$stmtDetalleSol->bindColumn('descripcion_alterna', $descripcion_alterna);
		$concepto_contabilizacion="";
		while ($row_det = $stmtDetalleSol->fetch()){
			$precio=$precio_unitario*$cantidad;
			$concepto_contabilizacion.=$descripcion_alterna." / ".$stringFacturas." / ".$razon_social."&#010;";
			$concepto_contabilizacion.="Cantidad: ".$cantidad." * ".formatNumberDec($precio_unitario)." = ".formatNumberDec($precio)."&#010;";
		}
		//insertamos cabecera
		//CAMBIAMOS EL CONCEPTO DE CONTABILIZACION CUANDO LA FACTURA TIENE GLOSA_ESPECIAL
		if($glosa_factura3!=""){
			$concepto_contabilizacion=$stringFacturas." / ".$razon_social." / ".$glosa_factura3;
		}
		$codComprobante=obtenerCodigoComprobante();
		$flagSuccess=insertarCabeceraComprobante($codComprobante,$codEmpresa,$cod_uo_unico,$codAnio,$codMoneda,$codEstadoComprobante,$tipoComprobante,$fechaActual,$numeroComprobante,$concepto_contabilizacion,$globalUser);		
		$ordenDetalle=1;//<--
		if($flagSuccess){
			//listado del detalle tipo pago
			$sql="SELECT t.*,(select p.cod_cuenta from tipos_pago_contabilizacion p where p.cod_tipopago=t.cod_tipopago)as cod_cuenta from solicitudes_facturacion_tipospago t where t.cod_solicitudfacturacion=$cod_solicitudfacturacion order by t.monto desc";			
			$stmtDetalleTipoPago = $dbh->prepare($sql);
			$stmtDetalleTipoPago->execute();
			$stmtDetalleTipoPago->bindColumn('cod_tipopago', $cod_tipopago);	 
			$stmtDetalleTipoPago->bindColumn('porcentaje', $porcentaje);	
			$stmtDetalleTipoPago->bindColumn('monto', $monto_tipopago);	  
			$stmtDetalleTipoPago->bindColumn('cod_cuenta', $cod_cuenta);  
			$monto_tipopago_total=0;
			while ($row_detTipopago = $stmtDetalleTipoPago->fetch()) {				
				$descripcion=$concepto_contabilizacion;
				$monto_tipopago_total+=$monto_tipopago;
				$cod_tipopago_credito=obtenerValorConfiguracion(48);
				$cod_tipopago_deposito_cuenta=obtenerValorConfiguracion(55);
				$cod_tipopago_tarjetas=obtenerValorConfiguracion(59);
				$cod_tipopago_anticipo=obtenerValorConfiguracion(64);				

				//cuenta de libreta bancaria, en cod_libretas_X viene el string de codigos de libreta
				if($cod_tipopago==$cod_tipopago_deposito_cuenta && $cod_libretas_X!=0){
					//Agrupamos los estados de las libretas bancarias
					$sqlTipopago="SELECT codigo,cod_estado,monto, DATE_FORMAT(fecha_hora, '%Y/%m')as fecha_hora from libretas_bancariasdetalle where codigo in ($cod_libretas_X)";
					//echo $sqlTipopago;
					$stmtLibreta = $dbh->prepare($sqlTipopago);
					$stmtLibreta->execute();							
					$stmtLibreta->bindColumn('codigo', $codigo_libreta_det);
					$stmtLibreta->bindColumn('cod_estado', $estado_libreta);  
					$stmtLibreta->bindColumn('monto', $monto_libreta);
					$stmtLibreta->bindColumn('fecha_hora', $fechaLibretaBancaria);
					$monto_libreta_total=0;
					$array_libreta_save="";
					$sw_controlador=0;//contrala la entradas

					while ($row_libreta = $stmtLibreta->fetch()) {		
						$monto_libreta_x=obtenerSaldoLibretaBancariaDetalle($codigo_libreta_det);
						if($monto_libreta_x==0){
							$monto_libreta=$monto_libreta;
						}else{
							$monto_libreta=$monto_libreta_x;
						}
						if($sw_controlador==0){
							$monto_libreta_total+=$monto_libreta;
							if($monto_tipopago>=$monto_libreta_total){
								if($fechaLibretaBancaria==$fechaActualMesAnio){//cueenta de libreta
					                $cod_cuenta_libr=obtenerCuentaLibretaBancaria($codigo_libreta_det);
					                $flagSuccessDet=insertarDetalleComprobante($codComprobante,$cod_cuenta_libr,0,$cod_uo_solicitud,$cod_area_solicitud,$monto_libreta,0,$descripcion,$ordenDetalle,$codigo_libreta_det);
					            }else{//contra cuenta de libreta
					                $cod_contracuenta_libr=obtenerContraCuentaLibretaBancaria($codigo_libreta_det);
									$flagSuccessDet=insertarDetalleComprobante($codComprobante,$cod_contracuenta_libr,0,$cod_uo_solicitud,$cod_area_solicitud,$monto_libreta,0,$descripcion,$ordenDetalle,$codigo_libreta_det);
					            }					            
					            $sqlUpdateLibreta="INSERT into libretas_bancariasdetalle_facturas(cod_libretabancariadetalle,cod_facturaventa) values ($codigo_libreta_det,$stringFacturasCod)";					            
		                        $stmtUpdateLibreta = $dbh->prepare($sqlUpdateLibreta);
		                        $stmtUpdateLibreta->execute();
							}else{
								// $array_libreta_save.=$codigo_libreta_det.",";
								$saldo_libreta=$monto_libreta_total-$monto_libreta;//volvemos al monto anterior
								$monto_libreta_saldo=$monto_tipopago-$saldo_libreta;
								if($fechaLibretaBancaria==$fechaActualMesAnio){//cueenta de libreta
					                $cod_cuenta_libr=obtenerCuentaLibretaBancaria($codigo_libreta_det);
					                $flagSuccessDet=insertarDetalleComprobante($codComprobante,$cod_cuenta_libr,0,$cod_uo_solicitud,$cod_area_solicitud,$monto_libreta_saldo,0,$descripcion,$ordenDetalle,$codigo_libreta_det);
					            }else{//contra cuenta de libreta
					                $cod_contracuenta_libr=obtenerContraCuentaLibretaBancaria($codigo_libreta_det);
									$flagSuccessDet=insertarDetalleComprobante($codComprobante,$cod_contracuenta_libr,0,$cod_uo_solicitud,$cod_area_solicitud,$monto_libreta_saldo,0,$descripcion,$ordenDetalle,$codigo_libreta_det);
					            }
					            $sqlUpdateLibreta="INSERT into libretas_bancariasdetalle_facturas(cod_libretabancariadetalle,cod_facturaventa) values ($codigo_libreta_det,$stringFacturasCod)";
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
					$flagSuccessDet=insertarDetalleComprobante($codComprobante,$cod_cuenta,0,$cod_uo_solicitud,$cod_area_solicitud,$monto_tipopago_1,0,$descripcion,$ordenDetalle,0);
					$ordenDetalle++;
					$flagSuccessDet=insertarDetalleComprobante($codComprobante,$cod_cuenta_tarjetacredito,0,$cod_uo_solicitud,$cod_area_solicitud,$monto_tipopago_2,0,$descripcion,$ordenDetalle,0);
				}elseif($cod_tipopago==$cod_tipopago_anticipo && $cod_estado_cuenta_x!=0){//tipo de pago anticipo en $cod_estado_cuenta_x viene el codigo de estado de cuenta
					$cuenta_auxiliar=obtenerValorConfiguracion(63);//cod cuenta auxiliar por defecto de anulacion de facturas
					$cod_proveedor=obtenerCodigoProveedorCuentaAux($cuenta_auxiliar);
					
					
					//***desde aqui
					$sqlTipopago="SELECT codigo,monto from estados_cuenta where codigo in ($cod_estado_cuenta_x)";					
					// echo $sqlTipopago;
					$stmtEC = $dbh->prepare($sqlTipopago);
					$stmtEC->execute();							
					$stmtEC->bindColumn('codigo', $codigo_ec_det);
					// $stmtEC->bindColumn('cod_estado', $estado_libreta);  
					$stmtEC->bindColumn('monto', $monto_ec);
					$monto_ec_total=0;
					$array_libreta_save="";
					$sw_controlador=0;//contrala la entradas					
					while ($row_EC = $stmtEC->fetch()) {

						
						$datos_array=obtenerDatosComprobanteEstadoCuentas($codigo_ec_det,$cod_uo_solicitud,$cod_area_solicitud);
						$cod_uo_estado=$datos_array[0]; //para cambiar el area y oficina del estado de cuentas a cerrar
						$cod_area_estado=$datos_array[1];
						$cod_compte_origen=$codigo_ec_det;//obtenerCod_comprobanteDetalleorigen($cod_estado_cuenta_x); //codigo estado de cuenta
						// $monto_ec_x=obtenerSaldoLibretaBancariaDetalle($codigo_ec_det);
						// if($monto_ec_x==0){
						// 	$monto_ec=$monto_ec;
						// }else{
						// 	$monto_ec=$monto_ec_x;
						// }
						if($sw_controlador==0){
							$monto_ec_total+=$monto_ec;
							if($monto_tipopago>=$monto_ec_total){
								

								$flagSuccessDet=insertarDetalleComprobante($codComprobante,$cod_cuenta,$cuenta_auxiliar,$cod_uo_estado,$cod_area_estado,$monto_ec,0,$descripcion,$ordenDetalle,0);
								// echo "aqui estado cuenta";
								$stmtdetalleCom = $dbh->prepare("SELECT codigo from comprobantes_detalle where cod_comprobante=$codComprobante and orden=$ordenDetalle");
								$stmtdetalleCom->execute();			
								$stmtdetalleCom->bindColumn('codigo', $cod_comprobante_detalle);
								while ($row = $stmtdetalleCom->fetch()) {						
									$cod_comprobante_detalle=$cod_comprobante_detalle;			
								}

								$sqlEstadoCuenta="INSERT into estados_cuenta(cod_comprobantedetalle,cod_plancuenta,monto,cod_proveedor,fecha,cod_comprobantedetalleorigen,cod_cuentaaux,cod_tipoestadocuenta,glosa_auxiliar) values($cod_comprobante_detalle,$cod_cuenta,$monto_ec,$cod_proveedor,'$fechaActual','$cod_compte_origen',$cuenta_auxiliar,'1','$concepto_contabilizacion')";
								 // echo $sqlEstadoCuenta;
					            $stmtEstadoCuenta = $dbh->prepare($sqlEstadoCuenta);
					            $flagSuccess=$stmtEstadoCuenta->execute();
							}else{
								// $array_libreta_save.=$codigo_ec_det.",";
								$saldo_ec=$monto_ec_total-$monto_ec;//volvemos al monto anterior
								$monto_ec_saldo=$monto_tipopago-$saldo_ec;
												               
								$flagSuccessDet=insertarDetalleComprobante($codComprobante,$cod_cuenta,$cuenta_auxiliar,$cod_uo_estado,$cod_area_estado,$monto_ec_saldo,0,$descripcion,$ordenDetalle,0);
								$stmtdetalleCom = $dbh->prepare("SELECT codigo from comprobantes_detalle where cod_comprobante=$codComprobante and orden=$ordenDetalle");
								$stmtdetalleCom->execute();			
								$stmtdetalleCom->bindColumn('codigo', $cod_comprobante_detalle);
								while ($row = $stmtdetalleCom->fetch()) {						
									$cod_comprobante_detalle=$cod_comprobante_detalle;			
								}
								$sqlEstadoCuenta="INSERT into estados_cuenta(cod_comprobantedetalle,cod_plancuenta,monto,cod_proveedor,fecha,cod_comprobantedetalleorigen,cod_cuentaaux,cod_tipoestadocuenta,glosa_auxiliar) values($cod_comprobante_detalle,$cod_cuenta,$monto_ec_saldo,$cod_proveedor,'$fechaActual','$cod_compte_origen',$cuenta_auxiliar,'1','$concepto_contabilizacion')";
								// echo $sqlEstadoCuenta;
					            $stmtEstadoCuenta = $dbh->prepare($sqlEstadoCuenta);
					            $flagSuccess=$stmtEstadoCuenta->execute();
					            $sw_controlador=1;
							}
						}


						//actualizar glosa comprobante
			            $codComprobanteDetalleOrigen=$cod_compte_origen;
			            $tituloEstadoOrigen="";
	                    if($codComprobanteDetalleOrigen>0){
	                     	$codigoComprobanteOrigen=obtenerComprobanteDetalleRelacionado(obtenerCod_comprobanteDetalleorigen($codComprobanteDetalleOrigen));
	                    	if($codigoComprobanteOrigen>0){
		                        $tituloEstadoOrigen="Cierre de ".nombreComprobante($codigoComprobanteOrigen)." ";  
		                        $sqlDetalleOrigen="UPDATE comprobantes_detalle set glosa=CONCAT('$tituloEstadoOrigen',glosa) WHERE codigo=$cod_comprobante_detalle and glosa NOT LIKE '$tituloEstadoOrigen%'";
		                        $stmtDetalleOrigen = $dbh->prepare($sqlDetalleOrigen);
		                        $stmtDetalleOrigen->execute();
		                    }
	                    } 

					}					


					
		            
				}elseif($cod_tipopago==$cod_tipopago_credito){
					// $cuenta_auxiliar=obtenerCodigoCuentaAuxiliarProveedorCliente(2,$cod_cliente);//tipo cliente
					$cuenta_defecto_cliente=obtenerValorConfiguracion(78);//creidto
                    // $cuenta_auxiliar=obtenerCodigoCuentaAuxiliarProveedorClienteCuenta(2,$cod_cliente,$cuenta_defecto_cliente);//solo par credito 
					$flagSuccessDet=insertarDetalleComprobante($codComprobante,$cod_cuenta,$cod_cuentaaux,$cod_uo_solicitud,$cod_area_solicitud,$monto_tipopago,0,$descripcion,$ordenDetalle,0);
					// $cod_compte_origen=obtenerCod_comprobanteDetalleorigen($cod_estado_cuenta_x);					
					$stmtdetalleCom = $dbh->prepare("SELECT codigo from comprobantes_detalle where cod_comprobante=$codComprobante and orden=$ordenDetalle");
					//en este caso insertamos la contra cuenta del estado de cuenta, para ello necesitamos el codigo del comprobnate detalle
					$stmtdetalleCom->execute();			
					$stmtdetalleCom->bindColumn('codigo', $cod_comprobante_detalle);
					while ($row = $stmtdetalleCom->fetch()) {						
						$cod_comprobante_detalle=$cod_comprobante_detalle;			
					}
					// $cuenta_axiliar=obtenerValorConfiguracion(63);//cod cuenta auxiliar por defecto de anulacion de facturas
					// $cod_proveedor=obtenerCodigoProveedorCuentaAux($cuenta_axiliar);
					// $cod_cuentaaux=obtenerCodigoCuentaAuxiliarProveedorCliente(2,$cod_cliente);//tipo cliente
					// $cod_proveedor=obtenerCodigoProveedorCuentaAux($cod_cuentaaux);
					$sqlEstadoCuenta="INSERT into estados_cuenta(cod_comprobantedetalle,cod_plancuenta,monto,cod_proveedor,fecha,cod_comprobantedetalleorigen,cod_cuentaaux,cod_tipoestadocuenta,glosa_auxiliar) values($cod_comprobante_detalle,$cod_cuenta,$monto_tipopago,$cod_cliente,'$fechaActual',0,$cod_cuentaaux,'1','$concepto_contabilizacion')";
					// echo $sqlEstadoCuenta;
		            $stmtEstadoCuenta = $dbh->prepare($sqlEstadoCuenta);
		            $flagSuccess=$stmtEstadoCuenta->execute(); 
				}else{
					$flagSuccessDet=insertarDetalleComprobante($codComprobante,$cod_cuenta,0,$cod_uo_solicitud,$cod_area_solicitud,$monto_tipopago,0,$descripcion,$ordenDetalle,0);
				}
	            $ordenDetalle++;
			}
			if($ordenDetalle>1){
				//para IT gasto		
				$cod_cuenta_it_gasto=obtenerValorConfiguracion(49);
				$porcentaje_it_gasto=obtenerValorConfiguracion(2);
				$monto_it_gasto=$porcentaje_it_gasto*$monto_tipopago_total/100;
				$descripcion_it_gasto=$concepto_contabilizacion;
				$flagSuccessDet=insertarDetalleComprobante($codComprobante,$cod_cuenta_it_gasto,0,$cod_uo_solicitud,$cod_area_solicitud,$monto_it_gasto,0,$descripcion_it_gasto,$ordenDetalle,0);
		        $ordenDetalle++;
		        //para IT
				$cod_cuenta_debito_iva=obtenerValorConfiguracion(50);
				$porcentaje_debito_iva=obtenerValorConfiguracion(1);
				$monto_debito_iva=$porcentaje_debito_iva*$monto_tipopago_total/100;
				$descripcion_debito_iva=$concepto_contabilizacion;
				$flagSuccessDet=insertarDetalleComprobante($codComprobante,$cod_cuenta_debito_iva,0,$cod_uo_solicitud,$cod_area_solicitud,0,$monto_debito_iva,$descripcion_debito_iva,$ordenDetalle,0);			
		        $ordenDetalle++;
		        //para IT pasivo
				$cod_cuenta_it_pasivo=obtenerValorConfiguracion(51);
				$porcentaje_it_pasivo=obtenerValorConfiguracion(2);
				$monto_it_pasivo=$porcentaje_it_pasivo*$monto_tipopago_total/100;
				$descripcion_it_pasivo=$concepto_contabilizacion;
				$flagSuccessDet=insertarDetalleComprobante($codComprobante,$cod_cuenta_it_pasivo,0,$cod_uo_solicitud,$cod_area_solicitud,0,$monto_it_pasivo,$descripcion_it_pasivo,$ordenDetalle,0);
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
						$flagSuccessDet=insertarDetalleComprobante($codComprobante,$cod_cuenta_areas,0,$cod_uo_areas,$cod_area_areas,0,$monto_areas_uo,$descripcion,$ordenDetalle,0);					
			            $ordenDetalle++;
					}
				}
				return $codComprobante;
			}else{
				$sqldeleteCabecera="DELETE from comprobantes where codigo =$codComprobante";
                $stmtDeleteCAbecera = $dbh->prepare($sqldeleteCabecera);
                $stmtDeleteCAbecera->execute();
				return "0";
			}
			// header('Location: ../comprobantes/imp.php?comp='.$codComprobante.'&mon=1');
		}
	} catch(PDOException $ex){
	    return "0";
	}	
}
function ejecutarComprobanteSolicitud_tiendaVirtual_bk($nitciCliente,$razonSocial,$items,$monto_total,$nro_factura,$tipoPago,$cod_cuenta_libreta,$normas,$cod_facturaventa){
	require_once __DIR__.'/../conexion.php';
	$dbh = new Conexion();
	date_default_timezone_set('America/La_Paz');
	// session_start();
	try{
	    $cod_uo_solicitud = 5;
	    // $cod_uo_unico=5;	   	 
	    if($normas==0){
	    	$cod_area_solicitud = 13;//capacitacion
	    }else{
			$cod_area_solicitud = 12;//normas
	    }
	    $codigo_alterno = '';
	    $razon_social = $razonSocial;
		//datos para el comprbant		
		$codEmpresa=1;
		$codAnio=date('Y');
		$codMoneda=1;
		$codEstadoComprobante=1;
		$fechaActual=date("Y-m-d H:i:s");		
		$tipoComprobante=4;//facturas
		$nombreTipoComprobante=abrevTipoComprobante($tipoComprobante);
		$numeroComprobante=obtenerCorrelativoComprobante3($tipoComprobante,$codAnio);
		$numeroX=str_pad($numeroComprobante, 5, "0", STR_PAD_LEFT);	
		$nombreComprobante=$nombreTipoComprobante."-".$numeroX;
		//sacamos nombre de los detalles
		$concepto_contabilizacion=" ";
		foreach ($items as $valor) {        
            $suscripcionId=$valor['suscripcionId'];
            $pagoCursoId=$valor['pagoCursoId'];
            $detalle=$valor['detalle'];
            $precioUnitario=$valor['precioUnitario'];
            $cantidad=$valor['cantidad'];
            $precio_natural=$precioUnitario;
            $precio_x=$cantidad*$precioUnitario;
            $concepto_contabilizacion.=$detalle." / F ".$nro_factura." / ".$razon_social."<br>\n";
			$concepto_contabilizacion.="Cantidad: ".$cantidad." * ".formatNumberDec($precio_natural)." = ".formatNumberDec($precio_x)."<br>\n";
        }
		$codComprobante=obtenerCodigoComprobante();
		// echo $numeroComprobante;
		// informacion solicitudd en curso
		$flagSuccess=insertarCabeceraComprobante($codComprobante,$codEmpresa,$cod_uo_solicitud,$codAnio,$codMoneda,$codEstadoComprobante,$tipoComprobante,$fechaActual,$numeroComprobante,$concepto_contabilizacion,1,1);		
		$ordenDetalle=1;//<--
		if($flagSuccess){	
			$cod_tipopago=obtenerValorConfiguracion(55);
			$cod_cuenta=obtenerCodCuentaTipoPago($cod_tipopago);
			$descripcion=$concepto_contabilizacion;	
			$sw=0;

			if($tipoPago!=4){//caso payme
				if($cod_cuenta_libreta!='0'){
					$cod_cuenta_libreta=trim($cod_cuenta_libreta,",");
					$sqlTipopago="SELECT codigo,cod_estado,monto,DATE_FORMAT(fecha_hora, '%Y/%m')as fecha_hora from libretas_bancariasdetalle where codigo in ($cod_cuenta_libreta) order by  monto desc";
					$stmtLibreta = $dbh->prepare($sqlTipopago);
					$stmtLibreta->execute();							
					$stmtLibreta->bindColumn('codigo', $codigo_libreta_det);
					$stmtLibreta->bindColumn('cod_estado', $estado_libreta);  
					$stmtLibreta->bindColumn('monto', $monto_libreta);
					$stmtLibreta->bindColumn('fecha_hora', $fechaLibretaBancaria);
					$monto_libreta_total=0;
					$array_libreta_save="";
					$sw_controlador=0;//contrala la entradas
					while ($row_libreta = $stmtLibreta->fetch()) {						
						$monto_libreta_x=obtenerSaldoLibretaBancariaDetalle($codigo_libreta_det);
						if($monto_libreta_x==0){
							$monto_libreta=$monto_libreta;
						}else{
							$monto_libreta=$monto_libreta_x;
						}
						if($sw_controlador==0){
							$monto_libreta_total+=$monto_libreta;
							if($monto_total>=$monto_libreta_total){
								if($fechaLibretaBancaria==$fechaActualMesAnio){//Cuenta de libreta
					                $cod_cuenta_libr=obtenerCuentaLibretaBancaria($codigo_libreta_det);
					                $flagSuccessDet=insertarDetalleComprobante($codComprobante,$cod_cuenta_libr,0,$cod_uo_solicitud,$cod_area_solicitud,$monto_libreta,0,$descripcion,$ordenDetalle,0);
					            }else{//contra cuenta de libreta
					                $cod_contracuenta_libr=obtenerContraCuentaLibretaBancaria($codigo_libreta_det);
									$flagSuccessDet=insertarDetalleComprobante($codComprobante,$cod_contracuenta_libr,0,$cod_uo_solicitud,$cod_area_solicitud,$monto_libreta,0,$descripcion,$ordenDetalle,0);
					            }
					            $sqlUpdateLibreta="INSERT into libretas_bancariasdetalle_facturas(cod_libretabancariadetalle,cod_facturaventa) values ($codigo_libreta_det,$cod_facturaventa)";
					            // echo $sqlUpdateLibreta;
		                        $stmtUpdateLibreta = $dbh->prepare($sqlUpdateLibreta);
		                        $stmtUpdateLibreta->execute();
							}else{
								// $array_libreta_save.=$codigo_libreta_det.",";
								$saldo_libreta=$monto_libreta_total-$monto_libreta;//volvemos al monto anterior
								$monto_libreta_saldo=$monto_total-$saldo_libreta;
								if($fechaLibretaBancaria==$fechaActualMesAnio){//cueenta de libreta
					                $cod_cuenta_libr=obtenerCuentaLibretaBancaria($codigo_libreta_det);
					                $flagSuccessDet=insertarDetalleComprobante($codComprobante,$cod_cuenta_libr,0,$cod_uo_solicitud,$cod_area_solicitud,$monto_libreta_saldo,0,$descripcion,$ordenDetalle,0);
					            }else{//contra cuenta de libreta
					                $cod_contracuenta_libr=obtenerContraCuentaLibretaBancaria($codigo_libreta_det);
									$flagSuccessDet=insertarDetalleComprobante($codComprobante,$cod_contracuenta_libr,0,$cod_uo_solicitud,$cod_area_solicitud,$monto_libreta_saldo,0,$descripcion,$ordenDetalle,0);
					            }
					            $sqlUpdateLibreta="INSERT into libretas_bancariasdetalle_facturas(cod_libretabancariadetalle,cod_facturaventa) values ($codigo_libreta_det,$cod_facturaventa)";
					            // echo $sqlUpdateLibreta;
		                        $stmtUpdateLibreta = $dbh->prepare($sqlUpdateLibreta);
		                        $stmtUpdateLibreta->execute();
					            $sw_controlador=1;
							}
						}
					}				
					if($monto_libreta_total<$monto_total){					
						$sw=1;
						$sqldeletecomprobante="DELETE from comprobantes where codigo=$codComprobante";
                        $stmtDeleteCopmprobante = $dbh->prepare($sqldeletecomprobante);
                        $flagSuccess=$stmtDeleteCopmprobante->execute();
                        $sqldeletecomprobanteDet="DELETE from comprobantes_detalle where cod_comprobante=$codComprobante";
                        $stmtDeleteComprobanteDet = $dbh->prepare($sqldeletecomprobanteDet);
                        $flagSuccess=$stmtDeleteComprobanteDet->execute();
                        return "-1";//error en montos
					}

				}else{
					$flagSuccessDet=insertarDetalleComprobante($codComprobante,$cod_cuenta,0,$cod_uo_solicitud,$cod_area_solicitud,$monto_total,0,$descripcion,$ordenDetalle,0);
				}

			}else{
				$cod_tipopago_tarjetas=obtenerValorConfiguracion(59);
				
				$cod_cuenta=obtenerCodCuentaTipoPago($cod_tipopago_tarjetas);
				
				$cod_cuenta_tarjetacredito=obtenerValorConfiguracion(60);
				$porcentaje_cuenta_transitoria=obtenerValorConfiguracion(61);
				$porcentaje_xyz=100-$porcentaje_cuenta_transitoria;
				$monto_tipopago_1=$monto_total*$porcentaje_xyz/100;
				$monto_tipopago_2=$monto_total*$porcentaje_cuenta_transitoria/100;

				$flagSuccessDet=insertarDetalleComprobante($codComprobante,$cod_cuenta,0,$cod_uo_solicitud,$cod_area_solicitud,$monto_tipopago_1,0,$descripcion,$ordenDetalle,0);
				$ordenDetalle++;
				$flagSuccessDet=insertarDetalleComprobante($codComprobante,$cod_cuenta_tarjetacredito,0,$cod_uo_solicitud,$cod_area_solicitud,$monto_tipopago_2,0,$descripcion,$ordenDetalle,0);
			}
			
			if($sw==0){
				$ordenDetalle++;			
				//para IT gasto
				$cod_cuenta_it_gasto=obtenerValorConfiguracion(49);
				$porcentaje_it_gasto=obtenerValorConfiguracion(2);
				$monto_it_gasto=$porcentaje_it_gasto*$monto_total/100;
				$descripcion_it_gasto=$concepto_contabilizacion;
				$flagSuccessDet=insertarDetalleComprobante($codComprobante,$cod_cuenta_it_gasto,0,$cod_uo_solicitud,$cod_area_solicitud,$monto_it_gasto,0,$descripcion_it_gasto,$ordenDetalle,0);			
		        $ordenDetalle++;
		        //para IVA
				$cod_cuenta_debito_iva=obtenerValorConfiguracion(50);
				$porcentaje_debito_iva=obtenerValorConfiguracion(1);
				$monto_debito_iva=$porcentaje_debito_iva*$monto_total/100;
				$descripcion_debito_iva=$concepto_contabilizacion;
				$flagSuccessDet=insertarDetalleComprobante($codComprobante,$cod_cuenta_debito_iva,0,$cod_uo_solicitud,$cod_area_solicitud,0,$monto_debito_iva,$descripcion_debito_iva,$ordenDetalle,0);			
		        $ordenDetalle++;
		        //para IT pasivo
				$cod_cuenta_it_pasivo=obtenerValorConfiguracion(51);
				$porcentaje_it_pasivo=obtenerValorConfiguracion(2);
				$monto_it_pasivo=$porcentaje_it_pasivo*$monto_total/100;
				$descripcion_it_pasivo=$concepto_contabilizacion;
				$flagSuccessDet=insertarDetalleComprobante($codComprobante,$cod_cuenta_it_pasivo,0,$cod_uo_solicitud,$cod_area_solicitud,0,$monto_it_pasivo,$descripcion_it_pasivo,$ordenDetalle,0);			
		        $ordenDetalle++;	       
		        //ingresos por capacitacion	  
		        $porcentaje_pasivo=100-$porcentaje_debito_iva;
				$monto_areas_format=$monto_total*$porcentaje_pasivo/100;
				$descripcion=$concepto_contabilizacion;
				
				$cod_cuenta_areas=obtenerCodCuentaArea($cod_area_solicitud);
				$flagSuccessDet=insertarDetalleComprobante($codComprobante,$cod_cuenta_areas,0,$cod_uo_solicitud,$cod_area_solicitud,0,$monto_areas_format,$descripcion,$ordenDetalle,0);
	            $ordenDetalle++;				
				return $codComprobante;	
			}else{
				return "-1";//error en montos
			}
            
			// header('Location: ../comprobantes/imp.php?comp='.$codComprobante.'&mon=1');
		}else{
			return "";
		}
	} catch(PDOException $ex){
	    return "";
	}	
}
function ejecutarComprobanteSolicitud_tiendaVirtual($nitciCliente,$razonSocial,$items,$monto_total,$nro_factura,$tipoPago,$cod_libretas_X,$normas,$cod_facturaventa){
	require_once __DIR__.'/../conexion.php';
	require_once '../functions.php';	
	require_once __DIR__.'/../functionsGeneral.php';
	$dbh = new Conexion();
	date_default_timezone_set('America/La_Paz');
	// session_start();
	        //rollback inicia
    $SQLDATOSINSTERT=[];
    $sqlCommit="SET AUTOCOMMIT=0;";
    $stmtCommit = $dbh->prepare($sqlCommit);
    $stmtCommit->execute();
	try{
	    $cod_uo_solicitud = 5;
	    // $cod_uo_unico=5;	   	 
	    if($normas==0){
	    	$cod_area_solicitud = 13;//capacitacion
	    }else{
			$cod_area_solicitud = 12;//normas
	    }
	    $codigo_alterno = '';
	    $razon_social = $razonSocial;
		//datos para el comprbant		
		$codEmpresa=1;
		$codAnio=date('Y');
		$codMoneda=1;
		$codEstadoComprobante=1;
		$fechaActual=date("Y-m-d H:i:s");

		$fechaActualMesAnio=date("Y/m");	

		$tipoComprobante=4;//facturas
		$nombreTipoComprobante=abrevTipoComprobante($tipoComprobante);
		$numeroComprobante=obtenerCorrelativoComprobante3($tipoComprobante,$codAnio);
		$numeroX=str_pad($numeroComprobante, 5, "0", STR_PAD_LEFT);	
		$nombreComprobante=$nombreTipoComprobante."-".$numeroX;
		//sacamos nombre de los detalles
		$concepto_contabilizacion=" ";
		foreach ($items as $valor) {        
            $suscripcionId=$valor['suscripcionId'];
            $pagoCursoId=$valor['pagoCursoId'];
            $detalle=$valor['detalle'];
            $precioUnitario=$valor['precioUnitario'];
            $cantidad=$valor['cantidad'];
            $precio_natural=$precioUnitario;
            $precio_x=$cantidad*$precioUnitario;
            $concepto_contabilizacion.=$detalle." / F ".$nro_factura." / ".$razon_social."<br>\n";
			$concepto_contabilizacion.="Cantidad: ".$cantidad." * ".formatNumberDec($precio_natural)." = ".formatNumberDec($precio_x)."<br>\n";
        }
		$codComprobante=obtenerCodigoComprobante();		
		$flagSuccess=insertarCabeceraComprobante($codComprobante,$codEmpresa,$cod_uo_solicitud,$codAnio,$codMoneda,$codEstadoComprobante,$tipoComprobante,$fechaActual,$numeroComprobante,$concepto_contabilizacion,1,1);	
		$ordenDetalle=1;//<--
		array_push($SQLDATOSINSTERT,$flagSuccess);
		if($flagSuccess){	
			$cod_tipopago=obtenerValorConfiguracion(55);//deposito  en cuenta
			$cod_cuenta=obtenerCodCuentaTipoPago($cod_tipopago);
			$descripcion=$concepto_contabilizacion;	
			$sw=0;
			if($tipoPago==4){//caso payme tarjetas
				$cod_tipopago_tarjetas=obtenerValorConfiguracion(59);
				$cod_cuenta=obtenerCodCuentaTipoPago($cod_tipopago_tarjetas);
				$cod_cuenta_tarjetacredito=obtenerValorConfiguracion(60);
				$porcentaje_cuenta_transitoria=obtenerValorConfiguracion(61);
				$porcentaje_xyz=100-$porcentaje_cuenta_transitoria;
				$monto_tipopago_1=$monto_total*$porcentaje_xyz/100;
				$monto_tipopago_2=$monto_total*$porcentaje_cuenta_transitoria/100;
				$flagSuccessDet=insertarDetalleComprobante($codComprobante,$cod_cuenta,0,$cod_uo_solicitud,$cod_area_solicitud,$monto_tipopago_1,0,$descripcion,$ordenDetalle);
				$ordenDetalle++;
				array_push($SQLDATOSINSTERT,$flagSuccessDet);
				$flagSuccessDet=insertarDetalleComprobante($codComprobante,$cod_cuenta_tarjetacredito,0,$cod_uo_solicitud,$cod_area_solicitud,$monto_tipopago_2,0,$descripcion,$ordenDetalle,0);
				array_push($SQLDATOSINSTERT,$flagSuccessDet);
			}else{
				if($cod_libretas_X!='0'){
					$cod_libretas_X=trim($cod_libretas_X,",");
					$sqlTipopago="SELECT codigo,cod_estado,monto, DATE_FORMAT(fecha_hora, '%Y/%m')as fecha_hora from libretas_bancariasdetalle where codigo in ($cod_libretas_X) order by  monto desc";
					$stmtLibreta = $dbh->prepare($sqlTipopago);
					$stmtLibreta->execute();							
					$stmtLibreta->bindColumn('codigo', $codigo_libreta_det);
					$stmtLibreta->bindColumn('cod_estado', $estado_libreta);  
					$stmtLibreta->bindColumn('monto', $monto_libreta);
					$stmtLibreta->bindColumn('fecha_hora', $fechaLibretaBancaria);
					$monto_libreta_total=0;
					$array_libreta_save="";
					$sw_controlador=0;//contrala la entradas
					while ($row_libreta = $stmtLibreta->fetch()) {						
						$monto_libreta_x=obtenerSaldoLibretaBancariaDetalle($codigo_libreta_det);
						if($monto_libreta_x==0){
							$monto_libreta=$monto_libreta;
						}else{
							$monto_libreta=$monto_libreta_x;
						}
						if($sw_controlador==0){
							$monto_libreta_total+=$monto_libreta;
							if($monto_total>=$monto_libreta_total){								
								//Aqui Controlamos si es deposito del mes o de otro mes para utilizar Cuenta y ContraCuenta
								if($fechaLibretaBancaria==$fechaActualMesAnio){
					                $cod_cuenta_libr=obtenerCuentaLibretaBancaria($codigo_libreta_det);
					                $flagSuccessDet=insertarDetalleComprobante($codComprobante,$cod_cuenta_libr,0,$cod_uo_solicitud,$cod_area_solicitud,$monto_libreta,0,$descripcion,$ordenDetalle,$codigo_libreta_det);
					                array_push($SQLDATOSINSTERT,$flagSuccessDet);
					            }else{//contra cuenta de libreta
					                $cod_contracuenta_libr=obtenerContraCuentaLibretaBancaria($codigo_libreta_det);
									$flagSuccessDet=insertarDetalleComprobante($codComprobante,$cod_contracuenta_libr,0,$cod_uo_solicitud,$cod_area_solicitud,$monto_libreta,0,$descripcion,$ordenDetalle,$codigo_libreta_det);
									array_push($SQLDATOSINSTERT,$flagSuccessDet);
					            }
					            $sqlUpdateLibreta="INSERT into libretas_bancariasdetalle_facturas(cod_libretabancariadetalle,cod_facturaventa) values ($codigo_libreta_det,$cod_facturaventa)";
		                        $stmtUpdateLibreta = $dbh->prepare($sqlUpdateLibreta);
		                        $flagSuccessLibreta=$stmtUpdateLibreta->execute();
		                        array_push($SQLDATOSINSTERT,$flagSuccessLibreta);
							}else{
								// $array_libreta_save.=$codigo_libreta_det.",";
								$saldo_libreta=$monto_libreta_total-$monto_libreta;//volvemos al monto anterior
								$monto_libreta_saldo=$monto_total-$saldo_libreta;
								if($fechaLibretaBancaria==$fechaActualMesAnio){//cuenta de libreta
					                $cod_cuenta_libr=obtenerCuentaLibretaBancaria($codigo_libreta_det);
					                $flagSuccessDet=insertarDetalleComprobante($codComprobante,$cod_cuenta_libr,0,$cod_uo_solicitud,$cod_area_solicitud,$monto_libreta_saldo,0,$descripcion,$ordenDetalle,$codigo_libreta_det);
					                array_push($SQLDATOSINSTERT,$flagSuccessDet);
					            }else{//contra cuenta de libreta
					                $cod_contracuenta_libr=obtenerContraCuentaLibretaBancaria($codigo_libreta_det);
									$flagSuccessDet=insertarDetalleComprobante($codComprobante,$cod_contracuenta_libr,0,$cod_uo_solicitud,$cod_area_solicitud,$monto_libreta_saldo,0,$descripcion,$ordenDetalle,$codigo_libreta_det);
									array_push($SQLDATOSINSTERT,$flagSuccessDet);
					            }
					            $sqlUpdateLibreta="INSERT into libretas_bancariasdetalle_facturas(cod_libretabancariadetalle,cod_facturaventa) values ($codigo_libreta_det,$cod_facturaventa)";
		                        $stmtUpdateLibreta = $dbh->prepare($sqlUpdateLibreta);
		                        $flagSuccessLibreta=$stmtUpdateLibreta->execute();
					            $sw_controlador=1;
					            array_push($SQLDATOSINSTERT,$flagSuccessLibreta);
							}
						}
					}				
					if($monto_libreta_total<$monto_total){	
						$sw=1;		
						$sqldeletecomprobanteDet="DELETE from comprobantes_detalle where cod_comprobante=$codComprobante";
                        $stmtDeleteComprobanteDet = $dbh->prepare($sqldeletecomprobanteDet);
                        $flagSuccess=$stmtDeleteComprobanteDet->execute();		
						$sqldeletecomprobante="DELETE from comprobantes where codigo=$codComprobante";
                        $stmtDeleteCopmprobante = $dbh->prepare($sqldeletecomprobante);
                        $flagSuccess=$stmtDeleteCopmprobante->execute();  
                        $sw_controlador="1";//hubo algun error
		                $sqlRolBack="ROLLBACK;";
		                $stmtRolBack = $dbh->prepare($sqlRolBack);
		                $stmtRolBack->execute();
		                $sqlCommit="COMMIT;SET AUTOCOMMIT=1;";
		                $stmtCommit = $dbh->prepare($sqlCommit);
		                $stmtCommit->execute();
                        return "-1";//error en montos
					}

				}else{
					$flagSuccessDet=insertarDetalleComprobante($codComprobante,$cod_cuenta,0,$cod_uo_solicitud,$cod_area_solicitud,$monto_total,0,$descripcion,$ordenDetalle,0);
					array_push($SQLDATOSINSTERT,$flagSuccessDet);
				}
			}
			if($sw==0){			
	            $ordenDetalle++;			
				//para IT gasto
				$cod_cuenta_it_gasto=obtenerValorConfiguracion(49);
				$porcentaje_it_gasto=obtenerValorConfiguracion(2);
				$monto_it_gasto=$porcentaje_it_gasto*$monto_total/100;
				$descripcion_it_gasto=$concepto_contabilizacion;
				$flagSuccessDet=insertarDetalleComprobante($codComprobante,$cod_cuenta_it_gasto,0,$cod_uo_solicitud,$cod_area_solicitud,$monto_it_gasto,0,$descripcion_it_gasto,$ordenDetalle,0);
				array_push($SQLDATOSINSTERT,$flagSuccessDet);
		        $ordenDetalle++;
		        //para IVA
				$cod_cuenta_debito_iva=obtenerValorConfiguracion(50);
				$porcentaje_debito_iva=obtenerValorConfiguracion(1);
				$monto_debito_iva=$porcentaje_debito_iva*$monto_total/100;
				$descripcion_debito_iva=$concepto_contabilizacion;
				$flagSuccessDet=insertarDetalleComprobante($codComprobante,$cod_cuenta_debito_iva,0,$cod_uo_solicitud,$cod_area_solicitud,0,$monto_debito_iva,$descripcion_debito_iva,$ordenDetalle,0);	
				array_push($SQLDATOSINSTERT,$flagSuccessDet);		
		        $ordenDetalle++;
		        //para IT pasivo
				$cod_cuenta_it_pasivo=obtenerValorConfiguracion(51);
				$porcentaje_it_pasivo=obtenerValorConfiguracion(2);
				$monto_it_pasivo=$porcentaje_it_pasivo*$monto_total/100;
				$descripcion_it_pasivo=$concepto_contabilizacion;
				$flagSuccessDet=insertarDetalleComprobante($codComprobante,$cod_cuenta_it_pasivo,0,$cod_uo_solicitud,$cod_area_solicitud,0,$monto_it_pasivo,$descripcion_it_pasivo,$ordenDetalle,0);
				array_push($SQLDATOSINSTERT,$flagSuccessDet);
		        $ordenDetalle++;	       
		        //ingresos por capacitacion	  
		        $porcentaje_pasivo=100-$porcentaje_debito_iva;
				$monto_areas_format=$monto_total*$porcentaje_pasivo/100;
				$descripcion=$concepto_contabilizacion;
				$cod_cuenta_areas=obtenerCodCuentaArea($cod_area_solicitud);
				$flagSuccessDet=insertarDetalleComprobante($codComprobante,$cod_cuenta_areas,0,$cod_uo_solicitud,$cod_area_solicitud,0,$monto_areas_format,$descripcion,$ordenDetalle,0);
				array_push($SQLDATOSINSTERT,$flagSuccessDet);
	            $ordenDetalle++;
	            $sw_controlador="0";//verifica si todo esta okey
                $errorInsertar=0;                
                for ($flag=0; $flag < count($SQLDATOSINSTERT); $flag++) { 
                    if($SQLDATOSINSTERT[$flag]==false){
                        $errorInsertar++;
                        // echo $flag;
                        break;
                    }
                } 
                if($errorInsertar!=0){
                    $sw_controlador="1";//hubo algun error
                    $sqlRolBack="ROLLBACK;";
                    $stmtRolBack = $dbh->prepare($sqlRolBack);
                    $stmtRolBack->execute();
                }
                $sqlCommit="COMMIT;SET AUTOCOMMIT=1;";
                $stmtCommit = $dbh->prepare($sqlCommit);
                $stmtCommit->execute();
                if($sw_controlador==0)
					return $codComprobante;
				else
					return "" ;
			}else{
				$sw_controlador="1";//hubo algun error
                $sqlRolBack="ROLLBACK;";
                $stmtRolBack = $dbh->prepare($sqlRolBack);
                $stmtRolBack->execute();
                $sqlCommit="COMMIT;SET AUTOCOMMIT=1;";
                $stmtCommit = $dbh->prepare($sqlCommit);
                $stmtCommit->execute();
				return "-1";//error en montos
			}	
		}else{
			$sw_controlador="1";//hubo algun error
            $sqlRolBack="ROLLBACK;";
            $stmtRolBack = $dbh->prepare($sqlRolBack);
            $stmtRolBack->execute();
            $sqlCommit="COMMIT;SET AUTOCOMMIT=1;";
            $stmtCommit = $dbh->prepare($sqlCommit);
            $stmtCommit->execute();
			return "";
		}
	} catch(PDOException $ex){
		$sw_controlador="1";//hubo algun error
        $sqlRolBack="ROLLBACK;";
        $stmtRolBack = $dbh->prepare($sqlRolBack);
        $stmtRolBack->execute();
        $sqlCommit="COMMIT;SET AUTOCOMMIT=1;";
        $stmtCommit = $dbh->prepare($sqlCommit);
        $stmtCommit->execute();
	    return "";
	}	
}
?>


