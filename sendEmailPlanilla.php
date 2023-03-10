<?php
    // Funciones
    require_once 'conexion.php';
	require_once 'functions.php';

    date_default_timezone_set('America/La_Paz');

    $cod_planilla = $_POST['cod_planilla'];
    $dbh = new Conexion();
    /**
     * Lista de planilla de pagos MES - PLANILLAS
     * @autor: Ronald Mollericona
    **/
    $sql="SELECT ppm.codigo, CONCAT(p.primer_nombre, ' ', p.paterno) as nombre_personal, p.email_empresa,
        (CASE
            WHEN pl.cod_mes = '1' THEN 'ENERO'
            WHEN pl.cod_mes = '2' THEN 'FEBRERO'
            WHEN pl.cod_mes = '3' THEN 'MARZO'
            WHEN pl.cod_mes = '4' THEN 'ABRIL'
            WHEN pl.cod_mes = '5' THEN 'MAYO'
            WHEN pl.cod_mes = '6' THEN 'JUNIO'
            WHEN pl.cod_mes = '7' THEN 'JULIO'
            WHEN pl.cod_mes = '8' THEN 'AGOSTO'
            WHEN pl.cod_mes = '9' THEN 'SEPTIEMBRE'
            WHEN pl.cod_mes = '10' THEN 'OCTUBRE'
            WHEN pl.cod_mes = '11' THEN 'NOVIEMBRE'
            WHEN pl.cod_mes = '12' THEN 'DICIEMBRE'
        END) as mes,
        g.nombre as anio,
        DATE_FORMAT(pl.created_at,'%d-%m-%Y %H:%i:%s') as created
        FROM planillas_personal_mes ppm
        LEFT JOIN personal p ON p.codigo = ppm.cod_personalcargo
        LEFT JOIN planillas pl ON pl.codigo = ppm.cod_planilla
        LEFT JOIN gestiones g ON g.codigo = pl.cod_gestion
        WHERE ppm.cod_planilla = '$cod_planilla' 
        /*AND ppm.cod_personalcargo = 58*/
        ORDER BY ppm.codigo DESC";
    $stmt= $dbh->prepare($sql);
    $stmt->execute();

    $rows = $stmt->fetchAll();

    $response = true;
    foreach($rows as $row){
        
        $personal       = $row['nombre_personal'];
        $personal_email = $row['email_empresa'];
        // $personal_email = 'roalmollericona@gmail.com';
        // $personal_email = 'lunagonzalesmarco@gmail.com';

        $fecha          = $row['mes'].' '.$row['anio'];
        $fecha_envio    = date('Y-m-d H:i:s');
        $ruta_boleta    = $row['codigo'];

        $ruta = __DIR__ . "/sendEmailPlanilla.html";

        $ruta_vista = obtenerValorConfiguracion(104);
        // $ruta_vista = "https://ibnored.ibnorca.org/ifinanciero";

        try {
            $message = file_get_contents($ruta);
            $message = str_replace('%ruta_vista%', $ruta_vista, $message);
            $message = str_replace('%personal%', $personal, $message);
            $message = str_replace('%fecha%', $fecha, $message);
            $message = str_replace('%fecha_envio%', $fecha_envio, $message);
            $message = str_replace('%key%', $ruta_boleta, $message);

            $sIdentificador = "ifinanciero";
            $sKey           ="ce94a8dabdf0b112eafa27a5aa475751";
            $datos			= array("sIdentificador"=> $sIdentificador, 
                                    "sKey"          => $sKey, 
                                    "accion"        => "EnviarCorreoCtaIbnoredPEI", 
                                    "NombreEnvia"   => "SISTEMA - IFINANCIERO", 
                                    "CorreoDestino" => $personal_email,
                                    "NombreDestino" => $personal,
                                    "Asunto"        => 'Boleta de Pago',
                                    "Body"          => $message,
                                    "CorreoCopia"   => 'janis.solares@ibnorca.org'
                                );
            $datos = json_encode($datos);
            
            $ch    = curl_init();
            curl_setopt($ch, CURLOPT_URL,"http://ibnored.ibnorca.org/wsibno/correo/ws-correotoken.php");
            curl_setopt($ch, CURLOPT_POST, TRUE);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $datos);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $remote_server_output = curl_exec ($ch);
            curl_close ($ch);
            $obj   = json_decode($remote_server_output);

            if($obj->estado){
                $response = true;
            }else{
                $response = false;
            }
        } catch (Exception $e) {
            $response = false;
        }
    
    }
    echo json_encode(array(
        'status' => $response
    ));
?>