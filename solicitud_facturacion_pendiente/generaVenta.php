<?php
    require_once '../conexion.php';
    require_once '../functions.php';
    date_default_timezone_set('America/La_Paz');

    $dbh = new Conexion();

    $codigo = $_POST['codigo'];
    // Consulta para obtener el registro principal
    $sqlDatos = "SELECT codigo, sucursalId, pagoCursoSuscripcionId, pasarelaId, fechaFactura, nitciCliente, razonSocial, importeTotal, tipoPago, codLibretaDetalle, usuario, idCliente, idIdentificacion, complementoCiCliente, nroTarjeta, CorreoCliente, estado, created_at
                FROM ventas_no_facturadas vnf
                WHERE vnf.codigo = '$codigo'
                ORDER BY vnf.codigo DESC LIMIT 1";

    $stmt = $dbh->prepare($sqlDatos);
    $stmt->execute();
    $registroPrincipal = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Verificamos si se encontró el registro
    if (!$registroPrincipal) {
        $respuesta = array(
            "estado"  => false,
            "mensaje" => "Error al emitir la factura, contactese con el administrador"
        );
        header('Content-type: application/json');
        echo json_encode($respuesta);
        exit;
    }

    // Capturamos el codigo de la tabla
    $codigoVenta = $registroPrincipal['codigo'];

    // Consulta para obtener los detalles relacionados
    $sqlDetalles = "SELECT * FROM ventas_no_facturadas_detalle WHERE cod_venta_no_facturada = '$codigoVenta'";
    $stmtDetalles = $dbh->prepare($sqlDetalles);
    $stmtDetalles->execute();
    $detalles = $stmtDetalles->fetchAll(PDO::FETCH_ASSOC);

    // Creación del objeto detalle
    $ArrayDetalles = [];
    foreach ($detalles as $detalle) {
        $Objeto_detalle = new stdClass();
        $Objeto_detalle->suscripcionId  = $detalle['suscripcionId'];
        $Objeto_detalle->pagoCursoId    = $detalle['pagoCursoId'];
        $Objeto_detalle->moduloId       = $detalle['moduloId'];
        $Objeto_detalle->codClaServicio = $detalle['codClaServicio'];
        $Objeto_detalle->detalle        = $detalle['detalle'];
        $Objeto_detalle->precioUnitario = $detalle['precioUnitario'];
        $Objeto_detalle->cantidad       = $detalle['cantidad'];
        $Objeto_detalle->descuento_bob  = $detalle['descuento_bob'];
        
        $ArrayDetalles[] = $Objeto_detalle;
    }

    $sIde = "facifin";
    $sKey = "AX546321asbhy347bhas191001bn0rc4654";
    // Creación del array de parámetros
    $parametros = array(
        "sIdentificador"       => $sIde,
        "sKey"                 => $sKey,
        "accion"               => "NewGenerateInvoice",
        "sucursalId"           => $registroPrincipal['sucursalId'],
        "pagoCursoSuscripcionId" => $registroPrincipal['pagoCursoSuscripcionId'],
        "pasarelaId"           => $registroPrincipal['pasarelaId'],
        "fechaFactura"         => date('Y-m-d'),
        // "fechaFactura"         => $registroPrincipal['fechaFactura'],
        "nitciCliente"         => $registroPrincipal['nitciCliente'],
        "razonSocial"          => $registroPrincipal['razonSocial'],
        "importeTotal"         => $registroPrincipal['importeTotal'],
        "tipoPago"             => $registroPrincipal['tipoPago'],
        "codLibretaDetalle"    => $registroPrincipal['codLibretaDetalle'],
        "usuario"              => $registroPrincipal['usuario'],
        "idCliente"            => $registroPrincipal['idCliente'],
        "idIdentificacion"     => $registroPrincipal['idIdentificacion'],
        "complementoCiCliente" => $registroPrincipal['complementoCiCliente'],
        "nroTarjeta"           => $registroPrincipal['nroTarjeta'],
        "CorreoCliente"        => $registroPrincipal['CorreoCliente'],
        "items"                => $ArrayDetalles
    );

    $direccion  = obtenerValorConfiguracion(112);
    $parametros = json_encode($parametros);
    // abrimos la sesiรณn cURL
    $ch = curl_init();
    
    curl_setopt($ch, CURLOPT_URL,$direccion."wsifin/ws_generate_invoice.php");
    curl_setopt($ch, CURLOPT_POST, TRUE);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $parametros);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $remote_server_output = curl_exec ($ch);
    curl_close ($ch);
    
    $respuesta=json_decode($remote_server_output);
    // print_r($respuesta);
    // Verificar el estado de la respuesta
    if (isset($respuesta->estado)) {
        if ($respuesta->estado == "0") {
            // Cambiar el estado en la base de datos
            $cod_facturaventa = $respuesta->IdFactura;
            $sqlUpdate = "UPDATE ventas_no_facturadas SET estado = '2', cod_facturaventa = '$cod_facturaventa' WHERE codigo = '$codigo'";
            $stmtUpdate = $dbh->prepare($sqlUpdate);
            
            if ($stmtUpdate->execute()) {
                /************************************************************************/
                /**
                 * ? GENERA COMPROBANTE
                 */
                $importeTotal             = $registroPrincipal['importeTotal'];
                $concepto_contabilizacion = ''; // ? GLOSA CABECERA
                $cod_area_solicitud   = 13;//capacitacion
                $codEmpresa           = 1;
                $cod_uo_solicitud     = 5;
                $codAnio              = date('Y');
                $codMoneda            = 1;
                $codEstadoComprobante = 1;
                $tipoComprobante      = 5;//Factura Diferida FDIF
                $fechaActual          = date("Y-m-d H:i:s");
                $numeroComprobante    = obtenerCorrelativoComprobante3($tipoComprobante,$codAnio);
                $codComprobante       = obtenerCodigoComprobante();		
                $flagSuccess          = insertarCabeceraComprobante($codComprobante,$codEmpresa,$cod_uo_solicitud,$codAnio,$codMoneda,$codEstadoComprobante,$tipoComprobante,$fechaActual,$numeroComprobante,$concepto_contabilizacion,1,1);
                // DETALLE
                $ordenDetalle = 1;
                $cod_cuenta        = 167; // CUENTA: Otros (Debe: 100%)
                $monto_debe        = $importeTotal;
                $monto_haber       = 0;
                $descripcion_glosa = ""; // ? GLOSA DETALLE
                $flagSuccessDet = insertarDetalleComprobante($codComprobante,$cod_cuenta,0,$cod_uo_solicitud,$cod_area_solicitud,$monto_debe,$monto_haber,$descripcion_glosa,$ordenDetalle);
                $ordenDetalle++;
                $cod_cuenta        = 261; // CUENTA: Impuesto a las Transacciones | gasto (Debe: 3%)
                $monto_debe        = 0.03 * $importeTotal;
                $monto_haber       = 0;
                $descripcion_glosa = ""; // ? GLOSA DETALLE
                $flagSuccessDet = insertarDetalleComprobante($codComprobante,$cod_cuenta,0,$cod_uo_solicitud,$cod_area_solicitud,$monto_debe,$monto_haber,$descripcion_glosa,$ordenDetalle);
                $ordenDetalle++;
                $cod_cuenta        = 142; // CUENTA: Débito Fiscal IVA (Haber: 13%)
                $monto_debe        = 0;
                $monto_haber       = 0.13 * $importeTotal;
                $descripcion_glosa = ""; // ? GLOSA DETALLE
                $flagSuccessDet = insertarDetalleComprobante($codComprobante,$cod_cuenta,0,$cod_uo_solicitud,$cod_area_solicitud,$monto_debe,$monto_haber,$descripcion_glosa,$ordenDetalle);
                $ordenDetalle++;
                $cod_cuenta        = 136; // CUENTA: Impuesto a las Transacciones | pasivo (Haber: 3%)
                $monto_debe        = 0;
                $monto_haber       = 0.03 * $importeTotal;
                $descripcion_glosa = ""; // ? GLOSA DETALLE
                $flagSuccessDet = insertarDetalleComprobante($codComprobante,$cod_cuenta,0,$cod_uo_solicitud,$cod_area_solicitud,$monto_debe,$monto_haber,$descripcion_glosa,$ordenDetalle);
                $ordenDetalle++;
                $cod_cuenta        = 279; // CUENTA: Ingresos por Formación (Haber: 87%)
                $monto_debe        = 0;
                $monto_haber       = 0.87 * $importeTotal;
                $descripcion_glosa = ""; // ? GLOSA DETALLE
                $flagSuccessDet = insertarDetalleComprobante($codComprobante,$cod_cuenta,0,$cod_uo_solicitud,$cod_area_solicitud,$monto_debe,$monto_haber,$descripcion_glosa,$ordenDetalle);
                /*************************
                 * ? SETEAR FACTURAS CURSOS
                 *************************/
                $url_ecommerce          = obtenerValorConfiguracion(109);
                $pagoCursoSuscripcionId = $registroPrincipal['pagoCursoSuscripcionId'];
                $fecha_hora             = date('Y-m-d H:i:s');
                $url_ws                 = $url_ecommerce.'tienda/setearFacturasCursos.php';
                $sw_token               = "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiIzMDAxMiIsImlkVXN1YXJpbyI6IjMwMDEyIiwiY29ycmVvIjoid2VibWFzdGVyQGNvZS1lamVyY2l0by5jb20uYm8iLCJhdWQiOiJpYm5vcmNhIiwiaWF0IjoxMzU2OTk5NTI0LCJuYmYiOjEzNTcwMDAwMDB9.9VR9tWgtfSi_s9ix8qkZSl1fPYzCExJKMOPii_quXEE";
                $parametros = array(
                    "token"                  => $sw_token,
                    "pagoCursoSuscripcionId" => $pagoCursoSuscripcionId, 
                    "facturaId"              => $cod_facturaventa,
                    "app"                    => "FRONTIBNT"
                );         
                // ENVIADO - JSON LOCAL
                $json_enviado = json_encode($parametros);
                $ch           = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url_ws);
                curl_setopt($ch, CURLOPT_POST, TRUE);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $json_enviado);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                $remote_server_output = json_decode(curl_exec($ch));
                curl_close ($ch);
                // RECIBIDO - JSON RESPUESTA
                $json_respuesta = json_encode($remote_server_output);
                /************************************************************************/
                $sqlLog  = "INSERT INTO log_setearfacturascursos(json_enviado, json_respuesta, fecha_hora) 
                            VALUES ('$json_enviado','$json_respuesta','$fecha_hora')";
                $stmtLog = $dbh->prepare($sqlLog);
                $flagSuccess = $stmtLog->execute();
                /************************************************************************/
                // Éxito al actualizar el estado
                echo json_encode([
                    "estado"  => true,
                    "mensaje" => "Se generó la facturación correctamente"
                ]);
            } else {
                // Error al actualizar el estado
                echo json_encode([
                    "estado"  => false,
                    "mensaje" => "Error al actualizar el estado"
                ]);
            }
        } else {
            // El estado no es "0", no se realiza ninguna acción adicional
            echo json_encode([
                "estado"  => false,
                "mensaje" => $respuesta->mensaje
            ]);
        }
    } else {
        // No se pudo obtener el estado de la respuesta
        echo json_encode([
            "estado" => false,
            "mensaje" => "No se pudo obtener el estado de la respuesta"
        ]);
    }
?>



