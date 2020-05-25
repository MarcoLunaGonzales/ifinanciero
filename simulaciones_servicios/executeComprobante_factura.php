<?php //ESTADO FINALIZADO

function ejecutarComprobanteSolicitud($cod_solicitudfacturacion,$nro_factura){
	require_once __DIR__.'/../conexion.php';
	require_once '../functions.php';
	require_once '../assets/libraries/CifrasEnLetras.php';
	require_once __DIR__.'/../functionsGeneral.php';
	$dbh = new Conexion();
	$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);//try
	set_time_limit(3000);
	// session_start();
	try{    	
		$stmtCajaChica = $dbh->prepare("SELECT cod_unidadorganizacional,cod_area,observaciones,codigo_alterno,razon_social from solicitudes_facturacion where codigo=$cod_solicitudfacturacion");
	    $stmtCajaChica->execute();
	    $resultCCD = $stmtCajaChica->fetch();
	    $cod_uo_solicitud = $resultCCD['cod_unidadorganizacional'];    
	    $cod_area_solicitud = $resultCCD['cod_area'];    
	    $observaciones_solicitud = $resultCCD['observaciones'];    
	    $codigo_alterno = $resultCCD['codigo_alterno'];    
	    $razon_social = $resultCCD['razon_social'];    
	   
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
		$stmtDetalleSol->bindColumn('precio', $precio);			
		$stmtDetalleSol->bindColumn('descripcion_alterna', $descripcion_alterna);  
		$concepto_contabilizacion=$codigo_alterno." - ";
		while ($row_det = $stmtDetalleSol->fetch()){
			$precio_natural=$precio/$cantidad;
			$concepto_contabilizacion.=$descripcion_alterna." / ".$nombreComprobante." - F ".$nro_factura." / ".$razon_social."<br>\n";
			$concepto_contabilizacion.="Cantidad: ".$cantidad." * ".formatNumberDec($precio_natural)." = ".formatNumberDec($precio)."<br>\n";
		}
		$codComprobante=obtenerCodigoComprobante();		
		// echo $numeroComprobante;
		// informacion solicitudd en curso
		$sqlInsertCabecera="INSERT INTO comprobantes (codigo, cod_empresa, cod_unidadorganizacional, cod_gestion, cod_moneda, cod_estadocomprobante, cod_tipocomprobante, fecha, numero, glosa,created_by,modified_by) values ('$codComprobante','$codEmpresa','$cod_uo_solicitud','$codAnio','$codMoneda','$codEstadoComprobante','$tipoComprobante','$fechaActual','$numeroComprobante','$concepto_contabilizacion','$globalUser','$globalUser')";
		$stmtInsertCab = $dbh->prepare($sqlInsertCabecera);
		$flagSuccess=$stmtInsertCab->execute();
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
				$sqlInsertDet="INSERT INTO comprobantes_detalle (cod_comprobante, cod_cuenta, cod_cuentaauxiliar, cod_unidadorganizacional, cod_area, debe, haber, glosa, orden) VALUES ('$codComprobante','$cod_cuenta','0','$cod_uo_solicitud','$cod_area_solicitud','$monto_tipopago','0','$descripcion','$ordenDetalle')";
	            $stmtInsertDet = $dbh->prepare($sqlInsertDet);
	            $flagSuccessDet=$stmtInsertDet->execute();
	            $ordenDetalle++;
			}			
			//para IT gasto
			$cod_cuenta_it_gasto=obtenerValorConfiguracion(49);
			$porcentaje_it_gasto=obtenerValorConfiguracion(2);
			$monto_it_gasto=$porcentaje_it_gasto*$monto_tipopago_total/100;
			$descripcion_it_gasto=$concepto_contabilizacion;
			$sqlInsertDet="INSERT INTO comprobantes_detalle (cod_comprobante, cod_cuenta, cod_cuentaauxiliar, cod_unidadorganizacional, cod_area, debe, haber, glosa, orden) VALUES ('$codComprobante','$cod_cuenta_it_gasto','0','$cod_uo_solicitud','$cod_area_solicitud','$monto_it_gasto','0','$descripcion_it_gasto','$ordenDetalle')";
	        $stmtInsertDet = $dbh->prepare($sqlInsertDet);
	        $flagSuccessDet=$stmtInsertDet->execute();
	        $ordenDetalle++;
	        //para IT
			$cod_cuenta_debito_iva=obtenerValorConfiguracion(50);
			$porcentaje_debito_iva=obtenerValorConfiguracion(1);
			$monto_debito_iva=$porcentaje_debito_iva*$monto_tipopago_total/100;
			$descripcion_debito_iva=$concepto_contabilizacion;
			$sqlInsertDet="INSERT INTO comprobantes_detalle (cod_comprobante, cod_cuenta, cod_cuentaauxiliar, cod_unidadorganizacional, cod_area, debe, haber, glosa, orden) VALUES ('$codComprobante','$cod_cuenta_debito_iva','0','$cod_uo_solicitud','$cod_area_solicitud','0','$monto_debito_iva','$descripcion_debito_iva','$ordenDetalle')";
	        $stmtInsertDet = $dbh->prepare($sqlInsertDet);
	        $flagSuccessDet=$stmtInsertDet->execute();
	        $ordenDetalle++;
	        //para IT pasivo
			$cod_cuenta_it_pasivo=obtenerValorConfiguracion(51);
			$porcentaje_it_pasivo=obtenerValorConfiguracion(2);
			$monto_it_pasivo=$porcentaje_it_pasivo*$monto_tipopago_total/100;
			$descripcion_it_pasivo=$concepto_contabilizacion;
			$sqlInsertDet="INSERT INTO comprobantes_detalle (cod_comprobante, cod_cuenta, cod_cuentaauxiliar, cod_unidadorganizacional, cod_area, debe, haber, glosa, orden) VALUES ('$codComprobante','$cod_cuenta_it_pasivo','0','$cod_uo_solicitud','$cod_area_solicitud','0','$monto_it_pasivo','$descripcion_it_pasivo','$ordenDetalle')";
	        $stmtInsertDet = $dbh->prepare($sqlInsertDet);
	        $flagSuccessDet=$stmtInsertDet->execute();
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
					$sqlInsertDet="INSERT INTO comprobantes_detalle (cod_comprobante, cod_cuenta, cod_cuentaauxiliar, cod_unidadorganizacional, cod_area, debe, haber, glosa, orden) VALUES ('$codComprobante','$cod_cuenta_areas','0','$cod_uo_areas','$cod_area_areas','0','$monto_areas_uo','$descripcion','$ordenDetalle')";
		            $stmtInsertDet = $dbh->prepare($sqlInsertDet);
		            $flagSuccessDet=$stmtInsertDet->execute();
		            $ordenDetalle++;
				}
			}
			return $codComprobante;

			// header('Location: ../comprobantes/imp.php?comp='.$codComprobante.'&mon=1');
		}
	} catch(PDOException $ex){
	    echo "Un error ocurrio".$ex->getMessage();
	}	
}

?>


