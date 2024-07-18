<?php
    require_once '../conexion.php';
    require_once '../functions.php';
    require_once '../functionsGeneral.php';
    date_default_timezone_set('America/La_Paz');
    // Configurar el informe y visualización de errores
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    
    $direccion     = obtenerValorConfiguracion(112);
    $estadosConfig = obtenerValorConfiguracion(114);       
    $estadosCurso  = !empty($estadosConfig) ? explode(',', $estadosConfig) : []; // Estados de Curso para FACTURAR

    $dbh = new Conexion();

    $sqlDatos = "SELECT codigo, sucursalId, pasarelaId, fechaFactura, nitciCliente, razonSocial, importeTotal, tipoPago, codLibretaDetalle, usuario, idCliente, idIdentificacion, complementoCiCliente, nroTarjeta, CorreoCliente, estado, created_at
            FROM ventas_no_facturadas vnf
            WHERE vnf.estado = 1
            ORDER BY vnf.codigo ASC";
    $stmt = $dbh->prepare($sqlDatos);
    $stmt->execute();
    $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($resultados as $row) {
        // echo "ventas_no_facturadas <br>";
        $sqlDetalle = "SELECT vnf.moduloId
                        FROM ventas_no_facturadas_detalle vnf
                        WHERE vnf.cod_venta_no_facturada = '".$row['codigo']."' 
                        ORDER BY vnf.codigo DESC";
        $stmtDetalle = $dbh->prepare($sqlDetalle);
        $stmtDetalle->execute();
        $respDetalle = $stmtDetalle->fetchAll(PDO::FETCH_ASSOC); // Cambio aquí

        $verf_estado_curso = false;
        foreach ($respDetalle as $rowDetalle) {
            // echo "ventas_no_facturadas_detalle <br>";
            $verf_moduloId = $rowDetalle['moduloId'];
            $sqlBuscar = "SELECT m.IdCurso, 
                                m.IdModulo, 
                                ibnorca.id_estadoobjeto(597, m.IdCurso) as idEstadoCurso, 
                                ibnorca.d_clasificador(ibnorca.id_estadoobjeto(597, m.IdCurso)) AS estadoCurso
                        FROM ibnorca.modulos m
                        WHERE m.IdModulo = '$verf_moduloId'
                        LIMIT 1";
            // echo $sqlBuscar;
            $stmtBuscar = $dbh->prepare($sqlBuscar);
            $stmtBuscar->execute();
            $registroEncontrado = $stmtBuscar->fetch(PDO::FETCH_ASSOC);
            if($registroEncontrado) {
                $estado_curso = $registroEncontrado['idEstadoCurso'];
                if(in_array($estado_curso, $estadosCurso)){ // Verifica estados de curso para Facturar, toma en cuenta "configuración"
                    // echo "estadosCurso <br>";
                    $verf_estado_curso = true;  // True: FACTURAR
                }else{
                    $verf_estado_curso = false; // True: NO FACTURAR
                    break;
                }
            }
        }
        
        // Verifica boton para facturar
        if($verf_estado_curso){
            // echo "FACTURA ".$row['codigo']."<br>";        
            // Creación del array de parámetros
            $parametros = array(
                "codigo" => $row['codigo'],
            );
    
            // Convertir a JSON
            $parametros = json_encode($parametros);
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL,$direccion."solicitud_facturacion_pendiente/generaVenta.php");
            curl_setopt($ch, CURLOPT_POST, TRUE);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $parametros);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            $remote_server_output = curl_exec ($ch);
            curl_close ($ch);
            var_dump($remote_server_output);
            echo "<br>";
        }
    }
?>



