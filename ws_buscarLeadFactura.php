<?php
require_once 'conexion.php';
require_once 'functions.php';

session_start();

date_default_timezone_set('America/La_Paz');

// Data
$data = json_decode(file_get_contents('php://input'), true);

// Variables de entrada
$cod_facturaventa = empty($data['cod_facturaventa']) ? '' : $data['cod_facturaventa'];

/**************************************************/
// $cod_facturaventa = 40891;
// var_dump(searchLeadsFactura($cod_facturaventa));
/**************************************************/

/**
 * Función que busca y cierra oportunidades de ventas potenciales (LEADS) 
 * Servicio CRM
 * @author api: Samuel
 * @author function: Ronald
 */

$url_init = obtenerValorConfiguracion(110);
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
        // $url_init = "http://intranet.ibnorca.org:8008/api/v1/";
        $api_key = "cd77c5d7ef268ea79a4573222258effbd782b358";
        $datos   = array("data"=>"");
        $datos   = json_encode($datos);
        $headers = array(
            'Content-Type: application/json',
            'api_key: ' . $api_key
        );
            
        $fieds  = "name,email_from,phone,partner_id,product_id,stage_id";
        $url    = $url_init."crm.lead/find?partner_id.ci_dni=" . $ci . "&product_id.id_curso=" . $idServicio . "&fields=".$fieds."&stage_id=Reserva%20de%20cupo";

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
        $stmtSave = $dbh->prepare("INSERT INTO log_leads (observaciones,response,fecha_hora,tipo,cod_facturaventa) VALUES ('$observaciones','$response','$fecha_hora', 0, $cod_facturaventa)");
        $stmtSave->execute();
        
        // En caso existe LEADS, se procede a CERRAR
        if(!empty($obj->result->data)){
            foreach($obj->result->data as $data){
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
                $observaciones = "cod_facturaventa: $cod_factura, ci: $ci, idModulo: $idServicio, cod_lead: ".$data->id;
                $response      = json_encode($remote_server_output);
                $fecha_hora    = date('Y-m-d H:i:s');
                $stmtSave = $dbh->prepare("INSERT INTO log_leads (observaciones,response,fecha_hora,tipo,cod_facturaventa,cod_lead) VALUES ('$observaciones','$response','$fecha_hora', 1,$cod_facturaventa,$data->id)");
                $stmtSave->execute();
            }
        }
    }
    // Resultado
    if ($count_lead > 0) {
        $response = [
            'message' => 'Se cerro leads',
            'status'  => true
        ];
    } else {
        $response = [
            'message' => 'No hay leads',
            'status'  => false
        ];
    }
    // Enviar respuesta JSON
    // header('Content-Type: application/json');
    // echo json_encode($response);
    // exit;
} catch (\Throwable $th) {
    $response = [
        'message' => 'Error de servicio',
        'status'  => false
    ];
    // Enviar respuesta JSON
    // header('Content-Type: application/json');
    // echo json_encode($response);
    // exit;
}
// Generar una bandera: No hay leads activos, Se cerro leads, Error de servicio
