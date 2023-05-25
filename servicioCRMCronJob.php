<?php
    require_once 'conexion.php';
    require_once 'functions.php';
    date_default_timezone_set('America/La_Paz');

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
    $url_init = obtenerValorConfiguracion(110);
    
    $dbh      = new Conexion();
    try {
        
        // WHERE DATE(fv.fecha_factura) = '$fecha_actual'
        $dbh = new Conexion();
        $stmt = $dbh->prepare("SELECT fvd.codigo, fvd.cod_facturaventa, fvd.cod_claservicio, fvd.ci_estudiante, 
        (SELECT IdCurso FROM ibnorca.modulos WHERE IdModulo=fvd.cod_claservicio LIMIT 1) as idCurso 
        FROM facturas_venta fv
        LEFT JOIN facturas_ventadetalle fvd ON fvd.cod_facturaventa = fv.codigo
        WHERE DATE(fv.fecha_factura) BETWEEN '$fecha_inicio' AND '$fecha_fin'
	    AND fv.cod_area = 13
        ORDER BY fvd.codigo LIMIT 1");
        $stmt->execute();
        // Cantidad de LEADS
        $count_lead = 0;
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            var_dump($row);
            $ci               = $row['ci_estudiante'];
            $idServicio       = $row['cod_claservicio'];
            $cod_facturaventa = $row['cod_facturaventa'];
            $idCurso          = $row['idCurso'];
            // echo "cod_facturaventa: $cod_facturaventa, ci: $ci, idModulo: $idServicio, idCurso: $idCurso ### ";
            if(!empty($ci) && !empty($idServicio)){
                /********************************************************/
                /*              SERVICIO DE BUSQUEDA LEADS              */
                /********************************************************/
                // $url_init = "http://192.168.30.40/api/v1/";
                $api_key = "cd77c5d7ef268ea79a4573222258effbd782b358";
                $datos   = json_encode(array("data"=>""));
                $headers = array(
                    'Content-Type: application/json',
                    'api_key: ' . $api_key
                );
                    
                $fieds = "name,email_from,phone,partner_id,product_id,stage_id";
                // $url = $url_init."crm.lead/find?partner_id.ci_dni=" . $ci . "&product_id.id_curso=" . $idServicio . "&fields=".$fieds."&stage_id=Reserva%20de%20cupo";
                $url = $url_init."crm.lead/find?partner_id.ci_dni=" . $ci . "&product_id.id_curso=" . $idCurso . "&fields=".$fieds . "&exclude_stage_id=Cierre%20de%20venta";
                echo $url."############";
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
                curl_setopt($ch, CURLOPT_POSTFIELDS, $datos);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                $remote_server_output = curl_exec ($ch);
                curl_close ($ch);
                $obj=json_decode($remote_server_output);
        
                /********************************************************/
                /*              ERROR - LOG BUSQUEDA LEADS              */
                /********************************************************/
                if(!empty($obj->error)){
                    $observaciones = "cod_facturaventa: $cod_facturaventa, ci: $ci, idModulo: $idServicio, idCurso: $idCurso";
                    $response      = json_encode($obj);
                    $fecha_hora    = date('Y-m-d H:i:s');
                    $stmtSave = $dbh->prepare("INSERT INTO log_leads (observaciones,response,fecha_hora,tipo,cod_facturaventa,estado_lead) VALUES ('$observaciones','$response','$fecha_hora', 0, $cod_facturaventa, 'No hay lead')");
                    // $stmtSave->execute();
                }
                
                // En caso existe LEADS, se procede a CERRAR
                if(!empty($obj->result->data)){
                    foreach($obj->result->data as $data){
                        
                        /*************************************************************/
                        /*              ENCONTRADO - LOG BUSQUEDA LEADS              */
                        /*************************************************************/
                        if(!empty($obj->error)){
                            $observaciones = "cod_facturaventa: $cod_facturaventa, ci: $ci, idModulo: $idServicio, idCurso: $idCurso";
                            $response      = json_encode($data);
                            $fecha_hora    = date('Y-m-d H:i:s');
                            $stmtSave = $dbh->prepare("INSERT INTO log_leads (observaciones,response,fecha_hora,tipo,cod_facturaventa,cod_lead,estado_lead) VALUES ('$observaciones','$response','$fecha_hora', 0, $cod_facturaventa,".$data->id.",".$data->stage_id[1].")");
                            // $stmtSave->execute();
                        }
                        // Cierre de LEADS
                        // if($data->stage_id[1] == "Reserva de cupo"){ 
                        if(false){                            
                            $count_lead++;
                            /******************************************************/
                            /*              SERVICIO DE CIERRE LEADS              */
                            /******************************************************/
                            $datos   = array("data"=>"");
                            $datos   = json_encode($datos);
                            $url = $url_init."crm/".$data->id."/lead";
                            $ch = curl_init();
                            curl_setopt($ch, CURLOPT_URL, $url);
                            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PATCH");
                            curl_setopt($ch, CURLOPT_POSTFIELDS, $datos);
                            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                            $remote_server_output = curl_exec ($ch);
                            curl_close ($ch);
                            /**********************************************/
                            /*              LOG CIERRE LEADS              */
                            /**********************************************/
                            $observaciones = "cod_facturaventa: $cod_factura, ci: $ci, idModulo: $idServicio, idCurso: $idCurso, cod_lead: ".$data->id;
                            $response      = json_encode($remote_server_output);
                            $fecha_hora    = date('Y-m-d H:i:s');
                            $stmtSave = $dbh->prepare("INSERT INTO log_leads (observaciones,response,fecha_hora,tipo,cod_facturaventa,cod_lead) VALUES ('$observaciones','$response','$fecha_hora', 1,$cod_facturaventa,".$data->id.", 'Cierre de venta')");
                            // $stmtSave->execute();
                        }
                    }
                }
            }
        }
        // Resultado
        if($count_lead > 0){
            echo json_encode(array(
                'message' => 'Se cerro leads',
                'status'  => true
            ));
        }else{
            // echo json_encode(array(
            //     'message' => 'No hay leads para cerrar',
            //     'status'  => false
            // ));
        }
    } catch (\Throwable $th) {
        echo json_encode(array(
            'message' => 'Error de servicio',
            'status'  => false
        ));
    }
    // Generar una bandera: No hay leads activos, Se cerro leads, Error de servicio