<?php
    require_once '../conexion.php';
    require_once '../functions.php';

    $dbh = new Conexion();

    $codigo = $_POST['codigo'];
    // Consulta para obtener el registro principal
    $sqlDatos = "SELECT codigo, sucursalId, pasarelaId, fechaFactura, nitciCliente, razonSocial, importeTotal, tipoPago, codLibretaDetalle, usuario, idCliente, idIdentificacion, complementoCiCliente, nroTarjeta, CorreoCliente, estado, created_at
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
        "pasarelaId"           => $registroPrincipal['pasarelaId'],
        "fechaFactura"         => $registroPrincipal['fechaFactura'],
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



