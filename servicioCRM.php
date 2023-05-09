<?php
    require_once 'conexion.php';
    date_default_timezone_set('America/La_Paz');

    /**************************************************/
    // $cod_facturaventa = 40891;
    // var_dump(searchLeadsFactura($cod_facturaventa));
    /**************************************************/

    /**
     * Función de Busqueda y Cierre de LEADS
     * Servicio CRM
     * @author api: Samuel
     * @author function: Ronald
     */
    function searchLeadsFactura($cod_facturaventa){
        try {
            $dbh = new Conexion();
            $stmt = $dbh->prepare("SELECT fvd.cod_claservicio, fvd.ci_estudiante from facturas_ventadetalle fvd where fvd.cod_facturaventa='$cod_facturaventa'");
            $stmt->execute();
            
            // Cantidad de LEADS
            $count_lead = 0;
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $ci         = $row['ci_estudiante'];
                $idServicio = $row['cod_claservicio'];
                
                /********************************************************/
                /*              SERVICIO DE BUSQUEDA LEADS              */
                /********************************************************/
                $api_key = "cd77c5d7ef268ea79a4573222258effbd782b358";
                $datos   = array("data"=>"");
                $datos   = json_encode($datos);
                $headers = array(
                    'Content-Type: application/json',
                    'api_key: ' . $api_key
                );
                    
                $fieds = "name,email_from,phone,partner_id,product_id,stage_id";
                $url = "http://intranet.ibnorca.org:8008/api/v1/crm.lead/find?partner_id.ci_dni=" . $ci . "&product_id.id_curso=" . $idServicio . "&fields=".$fieds;
        
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
                curl_setopt($ch, CURLOPT_POSTFIELDS, $datos);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                $remote_server_output = curl_exec ($ch);
                curl_close ($ch);
                $obj=json_decode($remote_server_output);
        
                /************************************************/
                /*              LOG BUSQUEDA LEADS              */
                /************************************************/
                $observaciones = "cod_facturaventa: $cod_facturaventa, ci: $ci, idModulo: $idServicio";
                $response      = json_encode($obj);
                $fecha_hora    = date('Y-m-d H:i:s');
                $stmtSave = $dbh->prepare("INSERT INTO log_leads (observaciones,response,fecha_hora,tipo) VALUES ('$observaciones','$response','$fecha_hora', 0)");
                $stmtSave->execute();
                
                foreach($obj->result->data as $data){
                    $count_lead++;
                    /******************************************************/
                    /*              SERVICIO DE CIERRE LEADS              */
                    /******************************************************/
                    $datos   = array("data"=>"");
                    $datos   = json_encode($datos);
                    $url = "http://intranet.ibnorca.org:8008/api/v1/crm/".$data->id."/lead";
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
                    $observaciones = "cod_facturaventa: $cod_factura, ci: $ci, idModulo: $idServicio, Cod_lead: ".$data->id;
                    $response      = json_encode($remote_server_output);
                    $fecha_hora    = date('Y-m-d H:i:s');
                    $stmtSave = $dbh->prepare("INSERT INTO log_leads (observaciones,response,fecha_hora,tipo) VALUES ('$observaciones','$response','$fecha_hora', 1)");
                    $stmtSave->execute();
                }
            }
            // Resultado
            if($count_lead > 0){
                return [
                    'message' => 'Se cerro leads',
                    'status'  => true
                ];
            }else{
                return [
                    'message' => 'No hay leads',
                    'status'  => false
                ];
            }
        } catch (\Throwable $th) {
            return [
                'message' => 'Error de servicio',
                'status'  => false
            ];
        }
        // Generar una bandera: No hay leads activos, Se cerro leads, Error de servicio
    }