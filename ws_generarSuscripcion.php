<?php
require_once 'conexion.php';
require_once 'functions.php';
session_start();
date_default_timezone_set('America/La_Paz');

try {
    // Data
    $data = json_decode(file_get_contents('php://input'), true);

    // Variables de entrada
    $codigo        = empty($data['codigo']) ? $_GET['codigo'] : $data['codigo']; // (solicitudes_facturacion: codigo) | cod_solicitudfacturacion

    $url_ecommerce = obtenerValorConfiguracion(109);

    $dbh = new Conexion();
    /*****************************************************************************************/
    // DETALLE DE SOLICITUD DE FACTURACIÓN | SUBSCRIPCIÓN TIENDA
    $stmtDetalleFact = $dbh->prepare("SELECT sf.codigo, 
                                            sf.cantidad,
                                            sf.precio,
                                            sf.descuento_bob,
                                            sf.descripcion_alterna,
                                            fs.codigo as codigo_suscripcion,
                                            fs.cod_factura,
                                            fs.cod_facturadetalle,
                                            fs.cod_suscripcion,
                                            fs.glosa,
                                            fs.cod_solicitudfacturacion,
                                            fs.catalogo,
                                            fs.id_cliente,
                                            fs.id_opcion_suscripcion,
                                            fs.id_promocion,
                                            fs.id_tipo_venta,
                                            fs.idioma,
                                            fs.fecha_inicio_suscripcion,
                                            fs.id_norma,
                                            fs.cod_facturadetalle_real,
                                            fs.IdVentaNormas
                                    FROM solicitudes_facturaciondetalle sf
                                    LEFT JOIN facturas_suscripcionestienda fs ON fs.cod_facturadetalle = sf.codigo
                                    WHERE sf.cod_solicitudfacturacion ='$codigo'");
    $stmtDetalleFact->execute();

    $sw_token = '';
    while ($rowDetallefact = $stmtDetalleFact->fetch()) {
        // DETALLE SOLICITUD FACTURACIÓN
        $sf_codigo       = $rowDetallefact['codigo'];
        $cantidad_x      = $rowDetallefact['cantidad'];
        $precio_x        = $rowDetallefact['precio'];
        $descuento_bob_x = $rowDetallefact['descuento_bob'];
        $monto_totalCab  = ($precio_x * $cantidad_x) - $descuento_bob_x;
        // SUSCRIPCIONES
        $detail_codigo                     = $rowDetallefact['codigo_suscripcion']; 
        $detail_cod_factura                = $rowDetallefact['cod_factura']; 
        $detail_cod_facturadetalle         = $rowDetallefact['cod_facturadetalle']; 
        $detail_cod_suscripcion            = $rowDetallefact['cod_suscripcion']; 
        $detail_glosa                      = $rowDetallefact['glosa']; 
        $detail_cod_solicitudfacturacion   = $rowDetallefact['cod_solicitudfacturacion']; 
        $detail_catalogo                   = $rowDetallefact['catalogo']; 
        $detail_id_cliente                 = $rowDetallefact['id_cliente']; 
        $detail_id_opcion_suscripcion      = $rowDetallefact['id_opcion_suscripcion']; 
        $detail_id_promocion               = $rowDetallefact['id_promocion']; 
        $detail_id_tipo_venta              = $rowDetallefact['id_tipo_venta']; 
        $detail_idioma                     = $rowDetallefact['idioma']; 
        $detail_fecha_inicio_suscripcion   = $rowDetallefact['fecha_inicio_suscripcion'];
        $detail_id_norma                   = $rowDetallefact['id_norma'];

        $detail_cod_facturadetalle_real    = $rowDetallefact['cod_facturadetalle_real'];
        $detail_IdVentaNormas              = $rowDetallefact['IdVentaNormas'];
        
        // Fecha de envio Suscripción
        $fecha_envio   = date('Y-m-d H:i:s');
        /* ACTUALIZACIÓN cod_facturaventa_detalle en la TABLA: VENTANORMAS */
        $sqlVN = "UPDATE ibnorca.ventanormas 
                SET idFacturaDetalle = '$detail_cod_facturadetalle_real',
                    regularizado = 'UPD iFinanciero |$fecha_envio|'
                WHERE IdVentaNormas = '$detail_IdVentaNormas'";
        // echo $sqlVN;
        $stmtVN = $dbh->prepare($sqlVN);
        $stmtVN->execute();

        // Se genera la suscripcion solo cuando la norma es DIGITAL
        if($detail_id_tipo_venta==2){
            /**
             * GENERACIÓN DE TOKEN
             */
            if(empty($sw_token)){
                $direccion = $url_ecommerce.'usuario/login.php';
                
                $user     = 'mluna@minkasoftware.com';
                $password = md5('4868422');
                
                // $user     = $_SESSION['globalCredUser'];
                // $password = $_SESSION['globalCredPassword'];

                $parametros=array(
                        "c"   => 'IBNTOK', 
                        "md5" => 1, 
                        "a"   => $user, 
                        "b"   => $password);
                $parametros=json_encode($parametros);
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL,$direccion);
                curl_setopt($ch, CURLOPT_POST, TRUE);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $parametros);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                $remote_server_output = json_decode(curl_exec ($ch));
                curl_close ($ch); 
                
                // Verificación de Credenciales de Acceso
                if($remote_server_output->error == 'NOK'){
                    $stmtIbnorca        = $dbh->prepare("UPDATE facturas_suscripcionestienda 
                                SET glosa = '$remote_server_output->detail'
                                WHERE cod_facturadetalle = '$sf_codigo'");
                    $flagSuccess = $stmtIbnorca->execute();
                    $sw_token = '';
                }else{
                    $sw_token = $remote_server_output->value->valor->token;
                }
                // var_dump($sw_token);
            }
            // Verificación de TOKEN
            if(!empty($sw_token)){
                // Codigo Factura
                $stmtCodfactura = $dbh->prepare("SELECT fv.codigo from facturas_venta fv where fv.cod_solicitudfacturacion='$detail_cod_solicitudfacturacion'");
                $stmtCodfactura->execute();
                $stringFacturasCod = $stmtCodfactura->fetch(PDO::FETCH_ASSOC)['codigo'];

                /**
                 * GENERACIÓN DE SUSCRIPCIÓN
                 **/
                $direccion = $url_ecommerce.'tienda/generarSuscripcion.php';
                
                $parametros=array(
                    "token"       => $sw_token,
                    "idNorma"     => $detail_id_norma, 
                    "catalogo"    => $detail_catalogo,
                    "idCliente"   => $detail_id_cliente,
                    "configuracionOpcionSuscripcionId" => $detail_id_opcion_suscripcion,
                    "promocionId" => $detail_id_promocion,
                    "precio"      => $monto_totalCab,
                    "tipo"        => "digital",
                    "idioma"      => $detail_idioma,
                    "desde"       => $detail_fecha_inicio_suscripcion,
                    "facturaId"   => $stringFacturasCod,
                    "sistema"     => "Ifinanciero",
                    "oficinaId"   => 0,
                    "app"         => "FRONTIBNT"
                );
                // var_dump($parametros);
                $parametros=json_encode($parametros);
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL,$direccion);
                curl_setopt($ch, CURLOPT_POST, TRUE);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $parametros);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                $remote_server_output = json_decode(curl_exec ($ch));
                curl_close ($ch); 
                // Resultado de Servicio SUSCRIPCIÓN
                // var_dump($remote_server_output);
                
                // ENVIADO - JSON LOCAL
                $json_enviado  = $parametros;
                // RECIBIDO - JSON SUCRIPCIÓN
                $json_recibido = json_encode($remote_server_output);

                $sw_error = $remote_server_output->error;
                $sw_cod_suscripcion = ($sw_error == "OK" ? $remote_server_output->suscripcionId : 0);
                $sw_glosa           = ($sw_error == "OK" ? 'REGISTRO CORRECTO!' : $remote_server_output->detail);
                $stmtIbnorca        = $dbh->prepare("UPDATE facturas_suscripcionestienda 
                                    SET cod_suscripcion = '$sw_cod_suscripcion',
                                    glosa = '$sw_glosa',
                                    json_enviado = '$json_enviado',
                                    json_recibido = '$json_recibido',
                                    fecha_envio = '$fecha_envio'
                                    WHERE cod_facturadetalle = '$sf_codigo'");
                $flagSuccess=$stmtIbnorca->execute();
            }else{
                $stmtIbnorca = $dbh->prepare("UPDATE facturas_suscripcionestienda 
                                            SET glosa = 'Hubo un error en el proceso de Autenticación.'
                                            WHERE cod_facturadetalle = '$sf_codigo'");
                $flagSuccess=$stmtIbnorca->execute();
            }
        }
    }
    // Finalización de proceso de suscripción
    $response = [
        'message' => 'Se finalizo el proceso de suscripción.',
        'status'  => true
    ];
    // Enviar respuesta JSON
    // header('Content-Type: application/json');
    // echo json_encode($response);
    exit;
    /*****************************************************************************************/

} catch (Exception $e) {
    // Manejar la excepción aquí, por ejemplo, registrarla en un archivo de registro o responder al cliente con un mensaje de error.
    $response = [
        'message' => "Error en el servicio: " . $e->getMessage(),
        'status'  => false
    ];
    // Enviar respuesta JSON
    // header('Content-Type: application/json');
    // echo json_encode($response);
    exit;
}