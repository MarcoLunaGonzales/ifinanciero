<?php //ESTADO FINALIZADO

function verificaVentaNoFacturada($sucursalId,$pasarelaId,$fechaFactura,$nitciCliente,$razonSocial,$importeTotal,$items,$CodLibretaDetalle,$tipoPago,$normas,$siat_nroTarjeta,$siat_tipoidentificacion,$siat_complemento,$correoCliente,$cod_cliente,$usuario){
    require_once __DIR__.'/../conexion.php';
    require '../assets/phpqrcode/qrlib.php';
    include '../assets/controlcode/sin/ControlCode.php';

    //require_once 'configModule.php';
    require_once __DIR__.'/../functions.php';
    require_once __DIR__.'/../functionsGeneral.php';
    require_once '../simulaciones_servicios/executeComprobante_factura.php';

    $dbh = new Conexion();
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);//try
    set_time_limit(300);
    session_start();    
    // date_default_timezone_set('America/La_Paz');

    $cod_solicitudfacturacion = -100;//desde la tienda usamos el -100
    $cod_uo_solicitud = 5;        
    if($normas==0){
        $cod_area_solicitud = 13;//capacitacion
        foreach ($items as $valor) {
            $pagoCursoId=$valor['pagoCursoId'];
        }
        // $curso_id=obtenerCodigoCurso_pagoid($pagoCursoId);
        $curso_id=0;
        if($curso_id==0){
            $Codigo_alterno="";
        }else{
            $Codigo_alterno=obtenerCodigoExternoCurso($curso_id);    
        }
        $observaciones = 'Tienda Virtual - Curso: '.$Codigo_alterno.' - RS: '.$razonSocial;
    }else{
        $cod_area_solicitud = 12;//normas
        $observaciones = 'Tienda Virtual - Venta de Normas - RS: '.$razonSocial;
    }

    if($pasarelaId==1){
        $cod_tipoobjeto = 1933;
    }else{
        $cod_tipoobjeto = 0;
    }

    if($tipoPago==5 || $tipoPago==6){            
        $cod_tipopago =obtenerValorConfiguracion(55);//deposito en cuenta
    }elseif($tipoPago==4){
        $cod_tipopago = obtenerValorConfiguracion(59);//tarjetas
    }else{
        $cod_tipopago = 0;
    }

    $cod_personal = 0;
    $razon_social = $razonSocial;
    $nitCliente = $nitciCliente;
    
    $nombre_cliente = $razonSocial;                
    $fechaFactura=$fechaFactura;
    $fecha_actual=date('Y-m-d');
    $fechaFactura_x=date('Y-m-d H:i:s');
    
    //Para la facturacion con el SIAT ya no se usa las dosificaciones
    $cod_dosificacionfactura = 0;
    $nroAutorizacion = 1;
    $llaveDosificacion = null;
    $fecha_limite_emision = null;

    /**
     * ? VERIFICACIÓN DE CURSO
     */
    $verf_estado_curso = false; // False: NO facturar
    $estadosConfig = obtenerValorConfiguracion(114);       
    $estadosCurso  = !empty($estadosConfig) ? explode(',', $estadosConfig) : []; // Estados de Curso para FACTURAR
    foreach ($items as $valor) {
        $suscripcionId  = $valor['suscripcionId'];
        $pagoCursoId    = $valor['pagoCursoId'];
        $moduloId       = $valor['moduloId'];
        $codClaServicio = $valor['codClaServicio'];
        // ! VERIFICA ESTADO DE CURSO
        $verf_estado_curso = false;
        break;
        $sqlBuscar = "SELECT ibnorca.id_estadoobjeto(597, $codClaServicio) as estado_curso";
        $stmtBuscar = $dbh->prepare($sqlBuscar);
        $stmtBuscar->execute();
        $registroEncontrado = $stmtBuscar->fetch(PDO::FETCH_ASSOC);
        if($registroEncontrado) {
            $estado_curso = $registroEncontrado['estado_curso'];
            if(in_array($estado_curso, $estadosCurso)){ // Verifica estados de curso para Facturar, toma en cuenta "configuración"
                $verf_estado_curso = true; // True: FACTURAR
            }else{
                $verf_estado_curso = false; // True: NO FACTURAR
                break;
            }
        }
    }

    if($verf_estado_curso){                    
        return true;// FACTURAR
    }else{
        //monto total redondeado
        $monto_total= $importeTotal;
        $totalFinalRedondeado=round($monto_total,0);                    
        $nro_correlativo=0;
        $nro_correlativoCorreo = nro_correlativo_correocredito($sucursalId,$cod_tipopago);

        $code=0;// para el siat ya no se usa codigo de control  

        $sql="INSERT INTO ventas_no_facturadas(cod_sucursal,cod_solicitudfacturacion,cod_unidadorganizacional,cod_area,fecha_factura,fecha_limite_emision,cod_tipoobjeto,cod_tipopago,cod_cliente,cod_personal,razon_social,nit,cod_dosificacionfactura,nro_factura,nro_autorizacion,codigo_control,importe,observaciones,cod_estadofactura,cod_comprobante,created_at,created_by, nro_correlativocorreo) 
                values ('$sucursalId','$cod_solicitudfacturacion','$cod_uo_solicitud','$cod_area_solicitud',NOW(),'$fecha_limite_emision','$cod_tipoobjeto','$cod_tipopago','$cod_cliente','$cod_personal','$razon_social','$nitCliente','$cod_dosificacionfactura','$nro_correlativo','$nroAutorizacion','$code','$monto_total','$observaciones','1','0',NOW(),1,'$nro_correlativoCorreo')";
        //echo $sql;
        $stmtInsertSoliFact = $dbh->prepare($sql);
        $flagSuccess=$stmtInsertSoliFact->execute();
        $cod_ventaNoFacturada = $dbh->lastInsertId();                    
        // $flagSuccess=true;
        if($flagSuccess){
            //add de estas lineas para facturacion con SIAT
            $monto_totalCab = 0;
            $arrayDetalle   = [];
            $contadoDetalle = 1;
            //hasta aqui SIAT

            //obtenemos el registro del ultimo insert
            foreach ($items as $valor) {
                $suscripcionId   = $valor['suscripcionId'];
                $pagoCursoId     = $valor['pagoCursoId'];
                $moduloId        = $valor['moduloId'];
                $codClaServicio2 = $valor['codClaServicio'];
                if(!is_numeric($moduloId)){
                    $moduloId = 0;
                }
                if(!is_numeric($codClaServicio2)){
                    $codClaServicio2 = 0;
                }

                $detalle        = $valor['detalle'];
                $precioUnitario = $valor['precioUnitario'];
                $cantidad       = $valor['cantidad'];
                $precio_x       = $precioUnitario;
                $cod_claservicio_x = $pagoCursoId;

                if( $normas!=0 && ($codClaServicio2==428 || $codClaServicio2==488) ){
                    $cod_claservicio_x = 488;
                }else{
                    $cod_claservicio_x = $codClaServicio2;
                }

                $stmtInsertSoliFactDet = $dbh->prepare("INSERT INTO ventas_no_facturadas_detalle(cod_venta_no_facturada,cod_claservicio,cantidad,precio,descripcion_alterna,descuento_bob,suscripcionId,cod_modulo,cod_claservicio2) 
                    values ('$cod_ventaNoFacturada','$cod_claservicio_x','$cantidad','$precio_x','$detalle',0,'$suscripcionId','$moduloId','$codClaServicio2')");
                $flagSuccess=$stmtInsertSoliFactDet->execute();
            }
            return false; // No permite Factura | Curso en Programado/Planificado, Suspendido
        }else{
            return false; // No permite Factura | Curso en Programado/Planificado, Suspendido
        }
    }
}
?>
