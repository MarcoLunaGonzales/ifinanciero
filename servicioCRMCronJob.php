<?php
    require_once 'conexion.php';
    require_once 'functions.php';
    date_default_timezone_set('America/La_Paz');

    // Configurar el informe y visualización de errores
    // error_reporting(E_ALL);
    // ini_set('display_errors', 1);

    /**
     * Servicio CRM
     * Función de Busqueda de Facturas generadas en el DIA
     * @param fecha_actual
     * @param Search: CI y IdModulo
     * @author CronJob: Ronald
     */
    $fecha_inicio = "2023-05-01";
    $fecha_fin    = "2023-05-31";
    
    /* Obtenemos Configuración RUTA LEADS */
    // $url_init = obtenerValorConfiguracion(110);           // OFICIAL
    $url_init = "http://intranet.ibnorca.org:8008/api/v1/";  // TEST
    $api_key  = "cd77c5d7ef268ea79a4573222258effbd782b358";
    $datos    = json_encode(array("data"=>""));
    $headers  = array(
        'Content-Type: application/json',
        'api_key: ' . $api_key
    );
    
    try {
        $dbh = new Conexion();
        $sql = "SELECT fvd.codigo as cod_facturaventa_detalle, 
                        fvd.cod_facturaventa, 
                        fvd.ci_estudiante,
                        (SELECT codigo FROM clientes WHERE identificacion = fvd.ci_estudiante AND fvd.ci_estudiante != '' LIMIT 1) as cod_cliente,
                        SUBSTRING_INDEX(fv.observaciones, ' - ', 1) as codigo_curso,
                        fvd.cod_claservicio,
                        fvd.descripcion_alterna
                FROM facturas_venta fv
                LEFT JOIN facturas_ventadetalle fvd ON fvd.cod_facturaventa = fv.codigo
                WHERE DATE(fv.fecha_factura) BETWEEN '$fecha_inicio' AND '$fecha_fin'
                AND fv.cod_area = 13
                ORDER BY fvd.codigo ASC
                ";
        $stmt = $dbh->prepare($sql);
        $stmt->execute();
        // Cantidad de LEADS
        $count_lead = 0;
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $cod_facturaventa         = $row['cod_facturaventa'];
            $cod_facturaventa_detalle = $row['cod_facturaventa_detalle'];
            $cod_cliente              = $row['cod_cliente'];
            $ci_estudiante            = $row['ci_estudiante'];
            $descripcion_alterna      = $row['descripcion_alterna'];
            $cod_claservicio          = $row['cod_claservicio'];
            // Verificación de COD_CLIENTE
            if(!empty($cod_cliente)){
                /********************************************************/
                /*              SERVICIO DE BUSQUEDA LEADS              */
                /********************************************************/
                $fieds = "name,email_from,phone,partner_id,product_id,stage_id";
                // $url = $url_init."crm.lead/find?partner_id.i_registro=" . $cod_cliente . "&fields=".$fieds."&stage_id=Reserva%20de%20cupo";
                $url = $url_init."crm.lead/find?partner_id.i_registro=" . $cod_cliente . "&product_id.id_curso=" . $cod_claservicio . "&fields=" . $fieds;
                
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
                curl_setopt($ch, CURLOPT_POSTFIELDS, $datos);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                $remote_server_output = curl_exec ($ch);
                curl_close ($ch);
                $obj = json_decode($remote_server_output);
                // Resultado
                // echo "-----------------------------";
                // var_dump($obj->result);
                // echo "-----------------------------";
                /********************************************************/
                /*              ERROR - LOG BUSQUEDA LEADS              */
                /********************************************************/
                // if(false){
                if(!empty($obj->error)){
                    $observaciones = json_encode([
                        'cod_facturaventa'         => $cod_facturaventa,
                        'cod_facturaventa_detalle' => $cod_facturaventa_detalle,
                        'cod_cliente'              => $cod_cliente,
                        'ci_estudiante'            => $ci_estudiante,
                        'descripcion_alterna'      => $descripcion_alterna,
                        'cod_claservicio'          => $cod_claservicio
                    ]);
                    $response      = json_encode($obj);
                    $fecha_hora    = date('Y-m-d H:i:s');
                    $stmtSave = $dbh->prepare("INSERT INTO log_leads (observaciones,response,fecha_hora,tipo,cod_facturaventa,estado_lead) VALUES ('$observaciones','$response','$fecha_hora', 0, '$cod_facturaventa', 'NO HAY LEAD')");
                    $stmtSave->execute();
                }
                
                // LEADS encontrados, se procede a CERRAR
                // if(false){
                if(!empty($obj->result->data)){
                    foreach($obj->result->data as $data){
                        /*************************************************************/
                        /*              ENCONTRADO - LOG BUSQUEDA LEADS              */
                        /*************************************************************/
                        $response      = json_encode($data);
                        $fecha_hora    = date('Y-m-d H:i:s');
                        $stmtSave = $dbh->prepare("INSERT INTO log_leads (observaciones,response,fecha_hora,tipo,cod_facturaventa,cod_lead,estado_lead) VALUES ('$observaciones', '$response', '$fecha_hora', 0, $cod_facturaventa, '".$data->id."', '".$data->stage_id[1]."')");
                        $stmtSave->execute();

                        // Cierre de LEADS
                        // if($data->stage_id[1] == "Reserva de cupo"){ 
                        if(false){
                            $count_lead++;
                            /******************************************************/
                            /*              SERVICIO DE CIERRE LEADS              */
                            /******************************************************/
                            // $datos   = array("data"=>"");
                            // $datos   = json_encode($datos);
                            // $url = $url_init."crm/".$data->id."/lead";
                            // $ch = curl_init();
                            // curl_setopt($ch, CURLOPT_URL, $url);
                            // curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                            // curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PATCH");
                            // curl_setopt($ch, CURLOPT_POSTFIELDS, $datos);
                            // curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                            // $remote_server_output = curl_exec ($ch);
                            // curl_close ($ch);
                            /**********************************************/
                            /*              LOG CIERRE LEADS              */
                            /**********************************************/
                            $observaciones = json_encode([
                                'cod_facturaventa'         => $cod_facturaventa,
                                'cod_facturaventa_detalle' => $cod_facturaventa_detalle,
                                'cod_cliente'              => $cod_cliente,
                                'ci_estudiante'            => $ci_estudiante,
                                'descripcion_alterna'      => $descripcion_alterna,
                                'cod_claservicio'          => $cod_claservicio,
                                'cod_lead'                 => $data->id
                            ]);
                            $response      = json_encode($remote_server_output);
                            $fecha_hora    = date('Y-m-d H:i:s');
                            $stmtSave = $dbh->prepare("INSERT INTO log_leads (observaciones,response,fecha_hora,tipo,cod_facturaventa,cod_lead,estado_lead) VALUES ('$observaciones','$response','$fecha_hora', 1,'$cod_facturaventa','".$data->id."', 'Cierre de venta')");
                            // $stmtSave->execute();
                        }
                    }
                }
            }
        }
        // Resultado
        if($count_lead > 0){
            echo json_encode(array(
                'message' => 'Leads Cerrados: '.$count_lead,
                'status'  => true
            ));
        }else{
            echo json_encode(array(
                'message' => 'No hay leads para cerrar',
                'status'  => false
            ));
        }
    } catch (\Throwable $th) {
        echo json_encode(array(
            'message' => 'Error de servicio',
            'status'  => false
        ));
    }
    // Generar una bandera: No hay leads activos, Se cerro leads, Error de servicio