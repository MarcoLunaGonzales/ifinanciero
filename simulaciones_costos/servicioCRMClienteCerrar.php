<?php
    require_once '../conexion.php';
    date_default_timezone_set('America/La_Paz');

    $cod_lead = $_POST['cod_lead'];
    
    try{
        /******************************************************/
        /*              SERVICIO DE CIERRE LEADS              */
        /******************************************************/
        $api_key = "cd77c5d7ef268ea79a4573222258effbd782b358";
        $datos   = array("data"=>"");
        $datos   = json_encode($datos);
        $headers = array(
            'Content-Type: application/json',
            'api_key: ' . $api_key
        );
            
        $url = "http://intranet.ibnorca.org:8008/api/v1/crm/".$cod_lead."/lead";
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
        $observaciones = "Propuesta/Cod_lead: ".$data->id;
        $response      = json_encode($remote_server_output);
        $fecha_hora    = date('Y-m-d H:i:s');
        $stmtSave = $dbh->prepare("INSERT INTO log_leads (observaciones,response,fecha_hora,tipo) VALUES ('$observaciones','$response','$fecha_hora', 1)");
        $stmtSave->execute();

        echo json_encode(array(
            'status' => true,
            'data'   => 'Cierre de lead correcto!'
        ));
    } catch (Exception $e) {
        echo json_encode(array(
            'status' => false
        ));
    }