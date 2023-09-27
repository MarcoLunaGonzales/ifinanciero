<?php
    require_once '../functions.php';
    require_once '../layouts/bodylogin.php';
    require_once '../conexion.php';
    require_once '../functionsGeneral.php';

    $dbh = new Conexion();
    session_start();
    date_default_timezone_set('America/La_Paz');
    $globalUser=$_SESSION["globalUser"];

    // Ruta WS tienda
    $urlTienda=obtenerValorConfiguracion(109);

    //RECIBIMOS LAS VARIABLES
    $codigo = $_GET["codigo"];

    // SUBSCRIPCIÓN TIENDA
    $stmtSuscripcion = $dbh->prepare("SELECT fs.codigo, fs.cod_factura, fs.cod_facturadetalle, fs.cod_suscripcion, fs.glosa, 
                                    fs.cod_solicitudfacturacion, fs.catalogo, fs.id_cliente, fs.id_opcion_suscripcion, 
                                    fs.id_promocion, fs.id_tipo_venta, fs.idioma, fs.fecha_inicio_suscripcion, fs.id_norma, fs.cod_factura
                                    FROM facturas_suscripcionestienda fs 
                                    WHERE fs.codigo = '$codigo'");
    $stmtSuscripcion->execute();
    $detail_codigo                     = ''; 
    $detail_cod_factura                = ''; 
    $detail_cod_facturadetalle         = ''; 
    $detail_cod_suscripcion            = ''; 
    $detail_glosa                      = ''; 
    $detail_cod_solicitudfacturacion   = ''; 
    $detail_catalogo                   = ''; 
    $detail_id_cliente                 = ''; 
    $detail_id_opcion_suscripcion      = ''; 
    $detail_id_promocion               = ''; 
    $detail_id_tipo_venta              = ''; 
    $detail_idioma                     = ''; 
    $detail_fecha_inicio_suscripcion   = '';
    $detail_id_norma                   = '';
    $stringFacturasCod                 = '';
    // var_dump($stmtSuscripcion->fetch());
    while ($rowSuscripcion = $stmtSuscripcion->fetch()) {
        $detail_codigo                     = $rowSuscripcion['codigo']; 
        $detail_cod_factura                = $rowSuscripcion['cod_factura']; 
        $detail_cod_facturadetalle         = $rowSuscripcion['cod_facturadetalle']; 
        $detail_cod_suscripcion            = $rowSuscripcion['cod_suscripcion']; 
        $detail_glosa                      = $rowSuscripcion['glosa']; 
        $detail_cod_solicitudfacturacion   = $rowSuscripcion['cod_solicitudfacturacion']; 
        $detail_catalogo                   = $rowSuscripcion['catalogo']; 
        $detail_id_cliente                 = $rowSuscripcion['id_cliente']; 
        $detail_id_opcion_suscripcion      = $rowSuscripcion['id_opcion_suscripcion']; 
        $detail_id_promocion               = $rowSuscripcion['id_promocion']; 
        $detail_id_tipo_venta              = $rowSuscripcion['id_tipo_venta']; 
        $detail_idioma                     = $rowSuscripcion['idioma']; 
        $detail_fecha_inicio_suscripcion   = $rowSuscripcion['fecha_inicio_suscripcion'];
        $detail_id_norma                   = $rowSuscripcion['id_norma'];

        $stringFacturasCod                 = $rowSuscripcion['cod_factura'];
    }
    // Solicitud de Facturación Detalle
    $stmtDetalleFact = $dbh->prepare("SELECT sf.codigo, sf.cantidad,sf.precio,sf.descuento_bob,sf.descripcion_alterna
                                    FROM solicitudes_facturaciondetalle sf 
                                    WHERE sf.codigo = '$detail_cod_facturadetalle'");
    $stmtDetalleFact->execute();
    
    // var_dump($stmtDetalleFact->fetch());
    $sw_token = '';
    while ($rowDetallefact = $stmtDetalleFact->fetch()) {   
        $sf_codigo       = $rowDetallefact['codigo'];
        $cantidad_x      = $rowDetallefact['cantidad'];
        $precio_x        = $rowDetallefact['precio'];
        $descuento_bob_x = $rowDetallefact['descuento_bob'];
        $monto_totalCab  = ($precio_x*$cantidad_x)-$descuento_bob_x;
    }
    /*****************************************************
     * Obtener codigo de cliente de la tabla VentaNormas
     *****************************************************/
    // $stmtIdCliente = $dbh->prepare("SELECT vn.IdVentaNormas, vn.idCliente
    //                                 FROM ibnorca.ventanormas vn 
    //                                 WHERE vn.IdVentaNormas = '$detail_id_norma'");
    // $stmtIdCliente->execute();
    // $detail_id_cliente = '';
    // while ($row_id_cliente = $stmtIdCliente->fetch()) {   
    //     $detail_id_cliente       = $row_id_cliente['idCliente'];
    // }
    // var_dump($detail_id_cliente);
    // Se genera la suscripcion solo cuando la norma es DIGITAL
    $flagSuccess = false;
    if($detail_id_tipo_venta==2){
        /**
         * GENERACIÓN DE TOKEN
         */
        if(empty($sw_token)){
            $url_ecommerce = $urlTienda;
            $direccion = $url_ecommerce.'usuario/login.php';
            
            $user     = 'juan.quenallata@ibnorca.org';
            $password = md5('juanito2020');
            
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
                $stmtIbnorca->execute();
                $sw_token = '';
            }else{
                $sw_token = $remote_server_output->value->valor->token;
            }
            // echo "#################################################";
            // var_dump($sw_token);
        }
        // Verificación de Credenciales de Acceso
        if(!empty($sw_token)){
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
            
            // Fecha de envio Suscripción
            $fecha_envio   = date('Y-m-d H:i:s');
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
                                WHERE codigo = '$codigo'");
            $flagSuccess=$stmtIbnorca->execute();
        }
    }
    
    showAlertSuccessError($flagSuccess,"../index.php?opcion=reportesSuscripcion");
    // Mensaje de Resultado
    // $mensaje_x= $flagSuccess ? "Registro de suscripción realizado correctamente." : "Hubo un error en la suscripción.";
    // $estado=$flagSuccess ? "success" : "error";
    // $titulo=$flagSuccess ? "Exito" : "Error!";
    // echo "<script>
    // Swal.fire('".$titulo."','".$mensaje_x."', '".$estado."');
    // </script>";
    
	/*****************************************************************************************/
?>