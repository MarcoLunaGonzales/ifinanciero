<?php //ESTADO FINALIZADO

function verificaVentaNoFacturada($sucursalId,$pasarelaId,$fechaFactura,$nitciCliente,$razonSocial,$importeTotal,$items,$CodLibretaDetalle,$tipoPago,$normas,$siat_nroTarjeta,$siat_tipoidentificacion,$siat_complemento,$correoCliente,$cod_cliente,$usuario){
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
        $sql="INSERT INTO ventas_no_facturadas(sucursalId, pasarelaId, fechaFactura, nitciCliente, razonSocial, importeTotal, tipoPago, codLibretaDetalle, usuario, idCliente, idIdentificacion, complementoCiCliente, nroTarjeta, CorreoCliente, created_at) 
                VALUES ('$sucursalId', '$pasarelaId', '$fechaFactura', '$nitciCliente', '$razonSocial', '$importeTotal', '$tipoPago', '$CodLibretaDetalle', '$usuario', '$cod_cliente', '$siat_tipoidentificacion', '$siat_complemento', '$siat_nroTarjeta', '$correoCliente', '$created_at')";
        //echo $sql;
        $stmtInsertSoliFact   = $dbh->prepare($sql);
        $flagSuccess          = $stmtInsertSoliFact->execute();
        $cod_ventaNoFacturada = $dbh->lastInsertId();                    
        // $flagSuccess=true;
        if($flagSuccess){

            //obtenemos el registro del ultimo insert
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
            }
            return false; // No permite Factura | Curso en Programado/Planificado, Suspendido
        }else{
            return false; // No permite Factura | Curso en Programado/Planificado, Suspendido
        }
    }
}
?>
