<?php
    // Funciones
    require_once 'conexion.php';

    date_default_timezone_set('America/La_Paz');

    $cod_planilla = $_POST['cod_planilla'];
    $dbh = new Conexion();
    /**
     * Lista de planilla de pagos MES - PLANILLAS
     * @autor: Ronald Mollericona
    **/
    $sql="SELECT ppm.codigo, CONCAT(p.primer_nombre, ' ', p.paterno) as nombre_personal, p.email
            FROM planillas_personal_mes ppm
            LEFT JOIN personal p ON p.codigo = ppm.cod_personalcargo
            WHERE ppm.cod_planilla = '$cod_planilla' 
            ORDER BY ppm.codigo DESC
            LIMIT 2";
    $stmt= $dbh->prepare($sql);
    $stmt->execute();

    $rows = $stmt->fetchAll();

    $response = true;
    foreach($rows as $row){
        
        $personal       = $row['nombre_personal'];
        // $personal_email = $row['email'];
        $personal_email = 'roalmollericona@gmail.com';
        $fecha          = date('Y-m-d H:i:s');
        $ruta_boleta    = $row['codigo'];

        // $personal       = 'Ronald Mollericona';
        // $personal_email = 'roalmirandadark@gmail.com';
        // $fecha          = '01/02/2023';
        // $ruta_boleta    = '4233';

        $ruta = __DIR__ . "/sendEmailPlanilla.html";
        try {
            $message = file_get_contents($ruta);
            $message = str_replace('%personal%', $personal, $message);
            $message = str_replace('%fecha%', $fecha, $message);
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
                                    "Body"          => $message);
            $datos = json_encode($datos);
            
            $ch    = curl_init();
            curl_setopt($ch, CURLOPT_URL,"http://ibnored.ibnorca.org/wsibno/correo/ws-correotoken.php");
            curl_setopt($ch, CURLOPT_POST, TRUE);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $datos);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $remote_server_output = curl_exec ($ch);
            curl_close ($ch);
            $obj   = json_decode($remote_server_output);

            // Seguimiento de EMAIL
            $sqlInsert="INSERT into planillas_email (cod_planilla_mes, created_at, nro_vista) values(:cod_planilla_mes,:created_at, 1)";
            $stmtInsert = $dbh->prepare($sqlInsert);
            $stmtInsert->bindParam(':cod_planilla_mes', $ruta_boleta);
            $stmtInsert->bindParam(':created_at', $fecha);
            $stmtInsert->execute();

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