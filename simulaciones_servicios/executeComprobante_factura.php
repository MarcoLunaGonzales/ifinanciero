<?php //ESTADO FINALIZADO

function ejecutarComprobanteSolicitud($cod_solicitudfacturacion,$nro_factura,$cod_libretas_X,$cod_estado_cuenta_x){
	require_once __DIR__.'/../conexion.php';
	require_once '../functions.php';
	require_once '../assets/libraries/CifrasEnLetras.php';
	require_once __DIR__.'/../functionsGeneral.php';
	$dbh = new Conexion();
	$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);//try
	set_time_limit(3000);
	// session_start();
	try{
		$cod_uo_unico=5;
		$stmtCajaChica = $dbh->prepare("SELECT cod_unidadorganizacional,cod_area,observaciones,codigo_alterno,razon_social,tipo_solicitud from solicitudes_facturacion where codigo=$cod_solicitudfacturacion");
	    $stmtCajaChica->execute();
	    $resultCCD = $stmtCajaChica->fetch();
	    $cod_uo_solicitud = $resultCCD['cod_unidadorganizacional'];    
	    $cod_area_solicitud = $resultCCD['cod_area'];    
	    $observaciones_solicitud = $resultCCD['observaciones'];    
	    $codigo_alterno = $resultCCD['codigo_alterno'];    
	    $razon_social = $resultCCD['razon_social'];
	    $tipo_solicitud = $resultCCD['tipo_solicitud'];
		//datos para el comprbant
		$globalUser=$_SESSION["globalUser"];
		$mesTrabajo=$_SESSION['globalMes'];
		$gestionTrabajo=$_SESSION['globalNombreGestion'];
		$codEmpresa=1;
		$codAnio=$_SESSION["globalNombreGestion"];
		$codMoneda=1;
		$codEstadoComprobante=1;
		$fechaActual=date("Y-m-d H:i:s");		
		$tipoComprobante=4;//facturas
		$nombreTipoComprobante=abrevTipoComprobante($tipoComprobante);
		$numeroComprobante=obtenerCorrelativoComprobante2($tipoComprobante);
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
			$concepto_contabilizacion.=$descripcion_alterna." / F ".$nro_factura." / ".$razon_social."&#010;";
			$concepto_contabilizacion.="Cantidad: ".$cantidad." * ".formatNumberDec($precio_unitario)." = ".formatNumberDec($precio)."&#010;";
		}
		$codComprobante=obtenerCodigoComprobante();
		//insertamos cabecera
		$flagSuccess=insertarCabeceraComprobante($codComprobante,$codEmpresa,$cod_uo_unico,$codAnio,$codMoneda,$codEstadoComprobante,$tipoComprobante,$fechaActual,$numeroComprobante,$concepto_contabilizacion,$globalUser,$globalUser);		
		$ordenDetalle=1;//<--
		if($flagSuccess){
			//listado del detalle tipo pago
			$stmtDetalleTipoPago = $dbh->prepare("SELECT t.*,(select p.cod_cuenta from tipos_pago_contabilizacion p where p.cod_tipopago=t.cod_tipopago)as cod_cuenta from solicitudes_facturacion_tipospago t where t.cod_solicitudfacturacion=$cod_solicitudfacturacion");
			$stmtDetalleTipoPago->execute();
			$stmtDetalleTipoPago->bindColumn('cod_tipopago', $cod_tipopago);	 
			$stmtDetalleTipoPago->bindColumn('porcentaje', $porcentaje);	
			$stmtDetalleTipoPago->bindColumn('monto', $monto_tipopago);	  
			$stmtDetalleTipoPago->bindColumn('cod_cuenta', $cod_cuenta);  
			$monto_tipopago_total=0;
			while ($row_detTipopago = $stmtDetalleTipoPago->fetch()) {
				$descripcion=$concepto_contabilizacion;
				$monto_tipopago_total+=$monto_tipopago;
				$cod_tipopago_deposito_cuenta=obtenerValorConfiguracion(55);
				$cod_tipopago_tarjetas=obtenerValorConfiguracion(59);
				$cod_tipopago_anticipo=obtenerValorConfiguracion(64);

				//cuenta de libreta bancaria, en cod_libretas_X viene el string de codigos de libreta
				if($cod_tipopago==$cod_tipopago_deposito_cuenta && $cod_libretas_X!=0){
					//Agrupamos los estados de las libretas bancarias 
					$sqlTipopago="SELECT codigo,cod_estado from libretas_bancariasdetalle where codigo in ($cod_libretas_X) GROUP BY cod_estado";
					$stmtDetalleTipoPago = $dbh->prepare($sqlTipopago);
					$stmtDetalleTipoPago->execute();							
					$stmtDetalleTipoPago->bindColumn('codigo', $codigo_libreta_det);
					$stmtDetalleTipoPago->bindColumn('cod_estado', $estado_libreta);  
					// $monto_tipopago_total=0;
					while ($row_detTipopago = $stmtDetalleTipoPago->fetch()) {								
						if($estado_libreta==0){//cueenta de libreta
			                $cod_cuenta_libr=obtenerCuentaLibretaBancaria($codigo_libreta_det);
			                $flagSuccessDet=insertarDetalleComprobante($codComprobante,$cod_cuenta_libr,0,$cod_uo_solicitud,$cod_area_solicitud,$monto_tipopago,0,$descripcion,$ordenDetalle);
			            }elseif($estado_libreta==1){//contra cuenta de libreta
			                $cod_contracuenta_libr=obtenerContraCuentaLibretaBancaria($codigo_libreta_det);
							$flagSuccessDet=insertarDetalleComprobante($codComprobante,$cod_contracuenta_libr,0,$cod_uo_solicitud,$cod_area_solicitud,$monto_tipopago,0,$descripcion,$ordenDetalle);					                
			            }
					}
				}elseif($cod_tipopago==$cod_tipopago_tarjetas){//cuenta de tarjeta de credito
					$cod_cuenta_tarjetacredito=obtenerValorConfiguracion(60);
					$porcentaje_cuenta_transitoria=obtenerValorConfiguracion(61);
					$porcentaje_xyz=100-$porcentaje_cuenta_transitoria;
					$monto_tipopago_1=$monto_tipopago*$porcentaje_xyz/100;
					$monto_tipopago_2=$monto_tipopago*$porcentaje_cuenta_transitoria/100;
					$flagSuccessDet=insertarDetalleComprobante($codComprobante,$cod_cuenta,0,$cod_uo_solicitud,$cod_area_solicitud,$monto_tipopago_1,0,$descripcion,$ordenDetalle);
					$flagSuccessDet=insertarDetalleComprobante($codComprobante,$cod_cuenta_tarjetacredito,0,$cod_uo_solicitud,$cod_area_solicitud,$monto_tipopago_2,0,$descripcion,$ordenDetalle);
				}elseif($cod_tipopago==$cod_tipopago_anticipo && $cod_estado_cuenta_x!=0){//tipo de pago anticipo en $cod_estado_cuenta_x viene el codigo de estado de cuenta
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
					$sqlEstadoCuenta="INSERT into estados_cuenta(cod_comprobantedetalle,cod_plancuenta,monto,cod_proveedor,fecha,cod_comprobantedetalleorigen,cod_cuentaaux,cod_tipoestadocuenta,glosa_auxiliar) values($cod_comprobante_detalle,$cod_cuenta,$monto_tipopago_total,$cod_proveedor,'$fechaActual','$cod_estado_cuenta_x',$cuenta_axiliar,'1','$concepto_contabilizacion')";
					// echo $sqlEstadoCuenta;
		            $stmtEstadoCuenta = $dbh->prepare($sqlEstadoCuenta);
		            $flagSuccess=$stmtEstadoCuenta->execute(); 
				}else{
					$flagSuccessDet=insertarDetalleComprobante($codComprobante,$cod_cuenta,0,$cod_uo_solicitud,$cod_area_solicitud,$monto_tipopago,0,$descripcion,$ordenDetalle);
				}
	            $ordenDetalle++;
			}			
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
			return $codComprobante;

			// header('Location: ../comprobantes/imp.php?comp='.$codComprobante.'&mon=1');
		}
	} catch(PDOException $ex){
	    return "0";
	}	
}
function ejecutarComprobanteSolicitud_tiendaVirtual($nitciCliente,$razonSocial,$items,$monto_total,$nro_factura,$cod_estado,$cod_cuenta_libreta,$normas){
	
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
		$numeroComprobante=obtenerCorrelativoComprobante2($tipoComprobante);
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
			switch ($cod_estado) {
				case 0://cuenta normal
					$flagSuccessDet=insertarDetalleComprobante($codComprobante,$cod_cuenta,0,$cod_uo_solicitud,$cod_area_solicitud,$monto_total,0,$descripcion,$ordenDetalle);					
					break;
				case 1://cuenta de libreta bancaria					
					$flagSuccessDet=insertarDetalleComprobante($codComprobante,$cod_cuenta_libreta,0,$cod_uo_solicitud,$cod_area_solicitud,$monto_total,0,$descripcion,$ordenDetalle);					
				break;
				case 2://cuenta de tarjeta de credito
					$cod_tipopago_tarjetas=obtenerValorConfiguracion(59);
					$cod_cuenta=obtenerCodCuentaTipoPago($cod_tipopago_tarjetas);
					
					$cod_cuenta_tarjetacredito=obtenerValorConfiguracion(60);
					$porcentaje_cuenta_transitoria=obtenerValorConfiguracion(61);
					$porcentaje_xyz=100-$porcentaje_cuenta_transitoria;
					$monto_tipopago_1=$monto_total*$porcentaje_xyz/100;
					$monto_tipopago_2=$monto_total*$porcentaje_cuenta_transitoria/100;

					$flagSuccessDet=insertarDetalleComprobante($codComprobante,$cod_cuenta,0,$cod_uo_solicitud,$cod_area_solicitud,$monto_tipopago_1,0,$descripcion,$ordenDetalle);
					
					$flagSuccessDet=insertarDetalleComprobante($codComprobante,$cod_cuenta_tarjetacredito,0,$cod_uo_solicitud,$cod_area_solicitud,$monto_tipopago_2,0,$descripcion,$ordenDetalle);
				break;	
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
			return $codComprobante;
			// header('Location: ../comprobantes/imp.php?comp='.$codComprobante.'&mon=1');
		}else{
			return "";
		}
	} catch(PDOException $ex){
	    return "";
	}	
}
?>


