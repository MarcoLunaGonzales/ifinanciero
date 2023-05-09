<?php
    require_once '../conexion.php';
    date_default_timezone_set('America/La_Paz');

    $cod_cliente = $_POST['cod_cliente'];
    // $cod_cliente = 66492;
    
    try{
        /******************************************************/
        /*       SERVICIO DE BUSQUEDA POR CLIENTE LEADS       */
        /******************************************************/
        $api_key = "cd77c5d7ef268ea79a4573222258effbd782b358";
        $datos   = array("data"=>"");
        $datos   = json_encode($datos);
        $headers = array(
            'Content-Type: application/json',
            'api_key: ' . $api_key
        );
            
        $fieds = "name,email_from,phone,partner_id,product_id,stage_id";
        $url   = "http://intranet.ibnorca.org:8008/api/v1/crm.lead/find?partner_id.i_registro=" . $cod_cliente . "&fields=".$fieds;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $datos);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $remote_server_output = curl_exec ($ch);
        curl_close ($ch);
        $obj = json_decode($remote_server_output);

        echo json_encode(array(
            'status' => true,
            'data'   => $obj->result->data
        ));
    } catch (Exception $e) {
        echo json_encode(array(
            'status' => false
        ));
    }