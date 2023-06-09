<?php
    require_once '../conexion.php';
    require_once '../functions.php';
    date_default_timezone_set('America/La_Paz');

    // $cod_cliente = $_POST['cod_cliente'];
    $cod_cliente = 67735;
    
    try{
        /******************************************************/
        /*       SERVICIO DE BUSQUEDA POR CLIENTE LEADS       */
        /******************************************************/
        // $url_init = obtenerValorConfiguracion(110);
        $url_init = "http://intranet.ibnorca.org:8008/api/v1/";
        $api_key = "cd77c5d7ef268ea79a4573222258effbd782b358";
        $datos   = array("data"=>"");
        $datos   = json_encode($datos);
        $headers = array(
            'Content-Type: application/json',
            'api_key: ' . $api_key
        );
        
        $fieds = "name,email_from,phone,partner_id,product_id,stage_id";
        // $url   = $url_init."crm.lead/find?partner_id.i_registro=" . $cod_cliente . "&fields=".$fieds."&stage_id=Reserva%20de%20cupo";
        $url   = $url_init."crm.lead/find?partner_id.i_registro=" . $cod_cliente . "&fields=".$fieds;
        // $url = 'http://intranet.ibnorca.org:8008/api/v1/crm.lead/find?fields=name%2Cemail_from%2Cphone%2Cpartner_id%2Cproduct_id%2Cstage_id&partner_id.i_registro=67735';

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $datos);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $remote_server_output = curl_exec ($ch);
        curl_close ($ch);
        $obj = json_decode($remote_server_output);
        
        // if ($remote_server_output === false) {
        //     $error_code = curl_errno($ch);
        //     $error_message = curl_error($ch);
        //     // Manejar el error de la petición cURL
        //     echo "Error en la petición cURL. Código: $error_code, Mensaje: $error_message";
        // } else {
        //     // La petición se realizó con éxito
        //     $obj = json_decode($remote_server_output);
        //     // Resto del código...
        // }

        echo json_encode(array(
            'status' => true,
            'data'   => (empty($obj->result->data)? [] : $obj->result->data)
        ));
    } catch (Exception $e) {
        echo json_encode(array(
            'status' => false
        ));
    }