<?php //ESTADO FINALIZADO

function ejecutarComprobanteSolicitud($nitciCliente,$razonSocial,$items,$monto_total,$nro_factura){
	require_once __DIR__.'/../conexion.php';
	require_once '../functions.php';
	require_once '../assets/libraries/CifrasEnLetras.php';
	require_once __DIR__.'/../functionsGeneral.php';
	$dbh = new Conexion();
	$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);//try
	set_time_limit(3000);
	// session_start();
	try{
	    $cod_uo_solicitud = 5;
	    $cod_area_solicitud = 13;
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
		$concepto_contabilizacion=$codigo_alterno." - ";
		foreach ($items as $valor) {        
            $suscripcionId=$valor['suscripcionId'];
            $pagoCursoId=$valor['pagoCursoId'];
            $detalle=$valor['detalle'];
            $precioUnitario=$valor['precioUnitario'];
            $cantidad=$valor['cantidad'];
            $precio_natural=$precioUnitario;
            $precio_x=$cantidad*$precioUnitario;
            $concepto_contabilizacion.=$detalle." / ".$nombreComprobante." - F ".$nro_factura." / ".$razon_social."<br>\n";
			$concepto_contabilizacion.="Cantidad: ".$cantidad." * ".formatNumberDec($precio_natural)." = ".formatNumberDec($precio_x)."<br>\n";
        }
		$codComprobante=obtenerCodigoComprobante();		
		// echo $numeroComprobante;
		// informacion solicitudd en curso
		$sqlInsertCabecera="INSERT INTO comprobantes (codigo, cod_empresa, cod_unidadorganizacional, cod_gestion, cod_moneda, cod_estadocomprobante, cod_tipocomprobante, fecha, numero, glosa,created_by,modified_by) values ('$codComprobante','$codEmpresa','$cod_uo_solicitud','$codAnio','$codMoneda','$codEstadoComprobante','$tipoComprobante','$fechaActual','$numeroComprobante','$concepto_contabilizacion',1,1)";
		$stmtInsertCab = $dbh->prepare($sqlInsertCabecera);
		$flagSuccess=$stmtInsertCab->execute();
		$ordenDetalle=1;//<--
		if($flagSuccess){	
			$cod_tipopago=obtenerValorConfiguracion(55);
			$cod_cuenta=obtenerCodCuentaTipoPago($cod_tipopago);
			$descripcion=$concepto_contabilizacion;				
			$sqlInsertDet="INSERT INTO comprobantes_detalle (cod_comprobante, cod_cuenta, cod_cuentaauxiliar, cod_unidadorganizacional, cod_area, debe, haber, glosa, orden) VALUES ('$codComprobante','$cod_cuenta','0','$cod_uo_solicitud','$cod_area_solicitud','$monto_total','0','$descripcion','$ordenDetalle')";
            $stmtInsertDet = $dbh->prepare($sqlInsertDet);
            $flagSuccessDet=$stmtInsertDet->execute();
            $ordenDetalle++;			
			//para IT gasto
			$cod_cuenta_it_gasto=obtenerValorConfiguracion(49);
			$porcentaje_it_gasto=obtenerValorConfiguracion(2);
			$monto_it_gasto=$porcentaje_it_gasto*$monto_total/100;
			$descripcion_it_gasto=$concepto_contabilizacion;
			$sqlInsertDet="INSERT INTO comprobantes_detalle (cod_comprobante, cod_cuenta, cod_cuentaauxiliar, cod_unidadorganizacional, cod_area, debe, haber, glosa, orden) VALUES ('$codComprobante','$cod_cuenta_it_gasto','0','$cod_uo_solicitud','$cod_area_solicitud','$monto_it_gasto','0','$descripcion_it_gasto','$ordenDetalle')";
	        $stmtInsertDet = $dbh->prepare($sqlInsertDet);
	        $flagSuccessDet=$stmtInsertDet->execute();
	        $ordenDetalle++;
	        //para IVA
			$cod_cuenta_debito_iva=obtenerValorConfiguracion(50);
			$porcentaje_debito_iva=obtenerValorConfiguracion(1);
			$monto_debito_iva=$porcentaje_debito_iva*$monto_total/100;
			$descripcion_debito_iva=$concepto_contabilizacion;
			$sqlInsertDet="INSERT INTO comprobantes_detalle (cod_comprobante, cod_cuenta, cod_cuentaauxiliar, cod_unidadorganizacional, cod_area, debe, haber, glosa, orden) VALUES ('$codComprobante','$cod_cuenta_debito_iva','0','$cod_uo_solicitud','$cod_area_solicitud','0','$monto_debito_iva','$descripcion_debito_iva','$ordenDetalle')";
	        $stmtInsertDet = $dbh->prepare($sqlInsertDet);
	        $flagSuccessDet=$stmtInsertDet->execute();
	        $ordenDetalle++;
	        //para IT pasivo
			$cod_cuenta_it_pasivo=obtenerValorConfiguracion(51);
			$porcentaje_it_pasivo=obtenerValorConfiguracion(2);
			$monto_it_pasivo=$porcentaje_it_pasivo*$monto_total/100;
			$descripcion_it_pasivo=$concepto_contabilizacion;
			$sqlInsertDet="INSERT INTO comprobantes_detalle (cod_comprobante, cod_cuenta, cod_cuentaauxiliar, cod_unidadorganizacional, cod_area, debe, haber, glosa, orden) VALUES ('$codComprobante','$cod_cuenta_it_pasivo','0','$cod_uo_solicitud','$cod_area_solicitud','0','$monto_it_pasivo','$descripcion_it_pasivo','$ordenDetalle')";
	        $stmtInsertDet = $dbh->prepare($sqlInsertDet);
	        $flagSuccessDet=$stmtInsertDet->execute();
	        $ordenDetalle++;
	        //ingresos por capacitacion	  
	        $porcentaje_pasivo=100-$porcentaje_debito_iva;
			$monto_areas_format=$monto_total*$porcentaje_pasivo/100;
			$descripcion=$concepto_contabilizacion;

			$cod_cuenta_areas=obtenerCodCuentaArea($cod_area_solicitud);
			$sqlInsertDet="INSERT INTO comprobantes_detalle (cod_comprobante, cod_cuenta, cod_cuentaauxiliar, cod_unidadorganizacional, cod_area, debe, haber, glosa, orden) VALUES ('$codComprobante','$cod_cuenta_areas','0','$cod_uo_solicitud','$cod_area_solicitud','0','$monto_areas_format','$descripcion','$ordenDetalle')";
            $stmtInsertDet = $dbh->prepare($sqlInsertDet);
            $flagSuccessDet=$stmtInsertDet->execute();
            $ordenDetalle++;				
			return $codComprobante;

			// header('Location: ../comprobantes/imp.php?comp='.$codComprobante.'&mon=1');
		}
	} catch(PDOException $ex){
	    echo "Un error ocurrio".$ex->getMessage();
	}	
}

?>


