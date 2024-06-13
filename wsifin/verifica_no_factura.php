<?php //ESTADO FINALIZADO

function verificaVentaNoFacturada($sucursalId,$pasarelaId,$fechaFactura,$nitciCliente,$razonSocial,$importeTotal,$items,$CodLibretaDetalle,$tipoPago,$normas,$siat_nroTarjeta,$siat_tipoidentificacion,$siat_complemento,$correoCliente,$cod_cliente,$usuario,$pagoCursoSuscripcionId){
    require_once __DIR__.'/../conexion.php';
    require_once __DIR__.'/../functions.php';
    require_once __DIR__.'/../functionsGeneral.php';
    
    date_default_timezone_set('America/La_Paz');

    $dbh = new Conexion();
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);//try
    set_time_limit(300);


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
        // $verf_estado_curso = false;
        // break;
        $sqlBuscar = "SELECT m.IdCurso, 
                            m.IdModulo, 
                            ibnorca.id_estadoobjeto(597, m.IdCurso) as idEstadoCurso, 
                            ibnorca.d_clasificador(ibnorca.id_estadoobjeto(597, m.IdCurso)) AS estadoCurso
                    FROM ibnorca.modulos m
                    WHERE m.IdModulo = '$moduloId'
                    LIMIT 1";
        $stmtBuscar = $dbh->prepare($sqlBuscar);
        $stmtBuscar->execute();
        $registroEncontrado = $stmtBuscar->fetch(PDO::FETCH_ASSOC);
        if($registroEncontrado) {
            $estado_curso = $registroEncontrado['idEstadoCurso'];
            if(in_array($estado_curso, $estadosCurso)){ // Verifica estados de curso para Facturar, toma en cuenta "configuración"
                $verf_estado_curso = true;  // True: FACTURAR
            }else{
                $verf_estado_curso = false; // True: NO FACTURAR
                break;
            }
        }
    }

    if($verf_estado_curso){                    
        return true;// FACTURAR
    }else{
        $created_at = date('Y-m-d H:i:s');
        $sql="INSERT INTO ventas_no_facturadas(sucursalId, pagoCursoSuscripcionId, pasarelaId, fechaFactura, nitciCliente, razonSocial, importeTotal, tipoPago, codLibretaDetalle, usuario, idCliente, idIdentificacion, complementoCiCliente, nroTarjeta, CorreoCliente, estado, created_at) 
                VALUES ('$sucursalId', '$pagoCursoSuscripcionId', '$pasarelaId', '$fechaFactura', '$nitciCliente', '$razonSocial', '$importeTotal', '$tipoPago', '$CodLibretaDetalle', '$usuario', '$cod_cliente', '$siat_tipoidentificacion', '$siat_complemento', '$siat_nroTarjeta', '$correoCliente', '1', '$created_at')";
        //echo $sql;
        $stmtInsertSoliFact   = $dbh->prepare($sql);
        $flagSuccess          = $stmtInsertSoliFact->execute();
        $cod_ventaNoFacturada = $dbh->lastInsertId();                    
        // $flagSuccess=true;
        if($flagSuccess){

            $concepto_contabilizacion = "";
            foreach ($items as $valor) {
                $suscripcionId  = $valor['suscripcionId'];
                $pagoCursoId    = $valor['pagoCursoId'];
                $moduloId       = $valor['moduloId'];
                $codClaServicio = $valor['codClaServicio'];
                $detalle        = $valor['detalle'];
                $precioUnitario = $valor['precioUnitario'];
                $cantidad       = $valor['cantidad'];
                $descuento_bob  = $valor['descuento_bob'];

                $sqlDetalle = "INSERT INTO ventas_no_facturadas_detalle(cod_venta_no_facturada, suscripcionId, pagoCursoId, moduloId, codClaServicio, detalle, precioUnitario, cantidad, descuento_bob) 
                VALUES ('$cod_ventaNoFacturada','$suscripcionId','$pagoCursoId','$moduloId','$codClaServicio','$detalle','$precioUnitario','$cantidad','$descuento_bob')";
                $stmtDetalle = $dbh->prepare($sqlDetalle);
                $flagSuccess = $stmtDetalle->execute();
                
                // * CONCEPTO COMPROBANTE
                $precio_x                  = $cantidad * $precioUnitario;
                $concepto_contabilizacion .= $detalle." / FD ".$cod_ventaNoFacturada." / RS ".$razonSocial."<br>\n";
                $concepto_contabilizacion .= "Cantidad: ".$cantidad." * ".formatNumberDec($precioUnitario)." = ".formatNumberDec($precio_x)."<br>\n";

                // $concepto_contabilizacion .= $detalle."<br>\n";
            }
            /************************************************************************/
            $sqlCliente  = "SELECT c.nombre from clientes c where c.identificacion = '$nitciCliente' OR c.clNit = '$nitciCliente' LIMIT 1";
            $stmtCliente = $dbh->prepare($sqlCliente);
            $stmtCliente->execute(); 
            $nombre_cliente = "";
            while ($rowCliente = $stmtCliente->fetch(PDO::FETCH_ASSOC)){
                $nombre_cliente = $rowCliente['nombre'];
            }
            /**
             * ? GENERA COMPROBANTE
             */
            $descripcion_glosa_cab = 'Contabilizacion de PREFAC. '.$concepto_contabilizacion."<br>\nEstudiante: ".$nombre_cliente; // ? GLOSA CABECERA
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
		    $flagSuccess          = insertarCabeceraComprobante($codComprobante,$codEmpresa,$cod_uo_solicitud,$codAnio,$codMoneda,$codEstadoComprobante,$tipoComprobante,$fechaActual,$numeroComprobante,$descripcion_glosa_cab,1,1);
            // DETALLE
            $ordenDetalle = 1;
            $cod_cuenta        = 361; // CUENTA: Otras Cuentas por Cobrar (Debe: 97,5%)
            $monto_debe        = 0.975 * $importeTotal;
            $monto_haber       = 0;
            $descripcion_glosa = 'Contabilizacion de PREFAC. '.$concepto_contabilizacion."<br>\n Estudiante: ".$nombre_cliente; // ? GLOSA DETALLE
            $flagSuccessDet = insertarDetalleComprobante($codComprobante,$cod_cuenta,0,$cod_uo_solicitud,$cod_area_solicitud,$monto_debe,$monto_haber,$descripcion_glosa,$ordenDetalle);
            $ordenDetalle++;
            $cod_cuenta        = 361; // CUENTA: Otras Cuentas por Cobrar (Debe: 2,5%)
            $monto_debe        = 0.025 * $importeTotal;
            $monto_haber       = 0;
            $descripcion_glosa = 'Contabilizacion de PREFAC. '.$concepto_contabilizacion."<br>\n Estudiante: ".$nombre_cliente; // ? GLOSA DETALLE
            $flagSuccessDet = insertarDetalleComprobante($codComprobante,$cod_cuenta,0,$cod_uo_solicitud,$cod_area_solicitud,$monto_debe,$monto_haber,$descripcion_glosa,$ordenDetalle);
            $ordenDetalle++;
            $cod_cuenta        = 167; // CUENTA: Otros (Haber: 100%)
            $monto_debe        = 0;
            $monto_haber       = $importeTotal;
            $descripcion_glosa = 'Contabilizacion de PREFAC. '.$concepto_contabilizacion."<br>\n Estudiante: ".$nombre_cliente; // ? GLOSA DETALLE
            $flagSuccessDet = insertarDetalleComprobante($codComprobante,$cod_cuenta,0,$cod_uo_solicitud,$cod_area_solicitud,$monto_debe,$monto_haber,$descripcion_glosa,$ordenDetalle);
            /************************************************************************/
            return false; // No permite Factura | Curso en Programado/Planificado, Suspendido
        }else{
            return false; // No permite Factura | Curso en Programado/Planificado, Suspendido
        }
    }
}
?>
