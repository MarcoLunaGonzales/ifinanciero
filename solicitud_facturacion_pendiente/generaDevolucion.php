<?php
    require_once '../conexion.php';
    require_once '../functions.php';
    require_once '../functionsGeneral.php';
    date_default_timezone_set('America/La_Paz');
    // Configurar el informe y visualización de errores
    // error_reporting(E_ALL);
    // ini_set('display_errors', 1);

    $json=file_get_contents("php://input");
    $datos = json_decode($json, true);  

    $codigo = $datos['codigo'];
    
    $dbh = new Conexion();
    /**
     * Captura ID ventas_no_facturas
     **/
    $sqlDatos = "SELECT vnf.codigo, vnf.fechaFactura, vnf.razonSocial, vnf.importeTotal, vnf.cod_comprobante1
            FROM ventas_no_facturadas vnf
            WHERE vnf.codigo = '$codigo'
            ORDER BY vnf.codigo DESC LIMIT 1";
    $stmt = $dbh->prepare($sqlDatos);
    $stmt->execute();
    $registroPrincipal = $stmt->fetch(PDO::FETCH_ASSOC);

    $nombreComprobante        = '';
    $concepto_contabilizacion = '';
    $pref_importe_total       = 0;
    if ($registroPrincipal) {
        $pref_codigo        = $registroPrincipal['codigo'];
        $pref_razonSocial   = $registroPrincipal['razonSocial'];
        $pref_importe_total = $registroPrincipal['importeTotal'];
        $pref_cod_comprobante1 = $registroPrincipal['cod_comprobante1'];

        /**
         * Obtiene Nombre de Comprobante
         */
        $nombreComprobante = nombreComprobante($pref_cod_comprobante1);

        // Consulta para obtener los detalles relacionados
        $sqlDetalles = "SELECT * FROM ventas_no_facturadas_detalle WHERE cod_venta_no_facturada = '$pref_codigo'";
        $stmtDetalles = $dbh->prepare($sqlDetalles);
        $stmtDetalles->execute();
        $detalles = $stmtDetalles->fetchAll(PDO::FETCH_ASSOC);

        foreach ($detalles as $detalle) {
            $Objeto_detalle = new stdClass();
            $Objeto_detalle->suscripcionId  = $detalle['suscripcionId'];
            $Objeto_detalle->pagoCursoId    = $detalle['pagoCursoId'];
            $Objeto_detalle->detalle        = $detalle['detalle'];
            $Objeto_detalle->precioUnitario = $detalle['precioUnitario'];
            $Objeto_detalle->cantidad       = $detalle['cantidad'];
            $Objeto_detalle->moduloId       = $detalle['moduloId'];
            $Objeto_detalle->codClaServicio = $detalle['codClaServicio'];
            $Objeto_detalle->descuentoProducto = $detalle['descuento_bob'];
            
            // * CONCEPTO COMPROBANTE
            $precio_x                  = $detalle['cantidad'] * $detalle['precioUnitario'];
            $concepto_contabilizacion .= $detalle['detalle'] .
                                        " / PREF $pref_codigo 
                                        / RS $pref_razonSocial<br>\n";
            $concepto_contabilizacion .= "Cantidad: " . $detalle['cantidad'] . " * " . 
                                        formatNumberDec($detalle['precioUnitario']) . " = " . 
                                        formatNumberDec($precio_x);
        }
    }
    /**
     * Actualiza Prefactura
     */
    $sqlUpdate = "UPDATE ventas_no_facturadas 
                SET estado = 3
                WHERE codigo = '$codigo'";
    $stmtUpdate = $dbh->prepare($sqlUpdate);
    
    if ($stmtUpdate->execute()) {
        /************************************************************************/
        /**
         * ? GENERA COMPROBANTE DE DEVOLUCIÓN
         */
        $codComprobante        = obtenerCodigoComprobante();	
        $importeTotal          = $pref_importe_total;
        $descripcion_glosa_cab = "Regulariza ".$nombreComprobante." ".$concepto_contabilizacion; // ? GLOSA CABECERA
        $cod_area_solicitud   = 13;//capacitacion
        $codEmpresa           = 1;
        $cod_uo_solicitud     = 5;
        $codAnio              = date('Y');
        $codMes               = date('m');
        $codMoneda            = 1;
        $codEstadoComprobante = 1;
        $tipoComprobante      = 3; // Traspaso
        $fechaActual          = date("Y-m-d H:i:s");
        // $numeroComprobante    = obtenerCorrelativoComprobante3($tipoComprobante,$codAnio);
        $numeroComprobante    = numeroCorrelativoComprobante($codAnio, $cod_uo_solicitud,$tipoComprobante,$codMes);
        $flagSuccess          = insertarCabeceraComprobante($codComprobante,$codEmpresa,$cod_uo_solicitud,$codAnio,$codMoneda,$codEstadoComprobante,$tipoComprobante,$fechaActual,$numeroComprobante,$descripcion_glosa_cab,1,1);
        // DETALLE
        $ordenDetalle      = 1;
        $cod_cuenta        = 167; // CUENTA: Otros (Debe: 100%)
        $monto_debe        = $importeTotal;
        $monto_haber       = 0;
        $descripcion_glosa = "Regulariza ".$nombreComprobante." ".$concepto_contabilizacion; // ? GLOSA DETALLE
        $flagSuccessDet = insertarDetalleComprobante($codComprobante,$cod_cuenta,0,$cod_uo_solicitud,$cod_area_solicitud,$monto_debe,$monto_haber,$descripcion_glosa,$ordenDetalle);
        
        $ordenDetalle      = 2;
        $cod_cuenta        = 161; // CUENTA: Anticipo de Clientes (Haber: 100%)
        $monto_debe        = 0;
        $monto_haber       = $importeTotal;
        $descripcion_glosa = "Regulariza ".$nombreComprobante." ".$concepto_contabilizacion; // ? GLOSA DETALLE
        $flagSuccessDet = insertarDetalleComprobante($codComprobante,$cod_cuenta,0,$cod_uo_solicitud,$cod_area_solicitud,$monto_debe,$monto_haber,$descripcion_glosa,$ordenDetalle);
        
        // Éxito al actualizar el estado
        echo json_encode([
            "estado"  => true,
            "mensaje" => "Se generó la devolución correctamente"
        ]);
    } else {
        // Error al actualizar el estado
        echo json_encode([
            "estado"  => false,
            "mensaje" => "Error al actualizar el estado"
        ]);
    }
?>