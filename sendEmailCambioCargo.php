<?php
    // Funciones
    require_once 'conexion.php';
	require_once 'functions.php';

    date_default_timezone_set('America/La_Paz');

    $cod_personal    = $_POST['cod_personal'];
    $cod_cargo_ant   = $_POST['cod_cargo_ant'];  // No se utiliza aún
    $cod_cargo_nuevo = $_POST['cod_cargo_nuevo'];
    $dbh = new Conexion();
    /**
     * Notifica modificación del cargo a Personal
     * @autor: Ronald Mollericona
    **/

    // Cargo Nuevo
    $sql = "SELECT c.nombre, 
                    c.abreviatura, 
                    (SELECT codigo_doc FROM control_versiones WHERE cod_cargo = c.codigo AND estado = 1 ORDER BY codigo DESC LIMIT 1) as codigo_doc, 
                    (SELECT descripcion_cambios FROM control_versiones WHERE cod_cargo = c.codigo AND estado = 1 ORDER BY codigo DESC LIMIT 1) as descripcion_cambios, 
                    (SELECT fecha FROM control_versiones WHERE cod_cargo = c.codigo AND estado = 1 ORDER BY codigo DESC LIMIT 1) as fecha_registro_version
            FROM cargos c
            WHERE c.cod_estadoreferencial = 1 
            AND c.codigo = :cod_cargo_nuevo";
    $stmt = $dbh->prepare($sql);
    $stmt->execute([':cod_cargo_nuevo' => $cod_cargo_nuevo]);
    $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
    $cargo_nuevo_nombre     = (($resultado) ? $resultado['codigo_doc'] : '').' '.(($resultado) ? $resultado['nombre'] : '');

    // Personal
    $sqlPersonal = "SELECT CONCAT(p.primer_nombre, ' ', p.paterno, ' ', p.materno) as nombre_completo,
                            p.email_empresa,
                            p.cod_cargo
                    FROM personal p 
                    WHERE p.codigo = :cod_personal";
    $stmtPersonal = $dbh->prepare($sqlPersonal);
    $stmtPersonal->execute([':cod_personal' => $cod_personal]);
    $resultadoPersonal = $stmtPersonal->fetch(PDO::FETCH_ASSOC);
    $personal_nombre = ($resultadoPersonal) ? $resultadoPersonal['nombre_completo'] : '';
    $personal_correo = ($resultadoPersonal) ? $resultadoPersonal['email_empresa'] : '';
    // $personal_correo = 'roalmollericona@gmail.com';
    $personal_cod_cargo_actual = ($resultadoPersonal) ? $resultadoPersonal['cod_cargo'] : '';

    $response = false;
    try {
        $ruta = __DIR__ . "/sendEmailCambioCargo.html";
        $message = file_get_contents($ruta);
        $message = str_replace('%nombre_personal%', $personal_nombre, $message);
        
        /* DETALLE DE MENSAJE */
        if(!empty($personal_cod_cargo_actual) && $personal_cod_cargo_actual !== 0){
            // Modificación de Cargo
            $detalle_mensaje = "Informar que de acuerdo a movimiento de personal, las  responsabilidades de su nuevo cargo están en el Manual de Cargo: <b>".$cargo_nuevo_nombre.".</b>";
        }else{
            // Nuevo personal
            $detalle_mensaje = "Nos complace informarle que las responsabilidades su cargo se encuentran en <b>".$cargo_nuevo_nombre.".</b>";
        }
        $message = str_replace('%detalle_mensaje%', $detalle_mensaje, $message);

        $sIdentificador = "ifinanciero";
        $sKey           ="ce94a8dabdf0b112eafa27a5aa475751";
        $datos			= array("sIdentificador"=> $sIdentificador, 
                                "sKey"          => $sKey, 
                                "accion"        => "EnviarCorreoCtaIbnoredPEI", 
                                "NombreEnvia"   => "SISTEMA - IFINANCIERO", 
                                "CorreoDestino" => $personal_correo,
                                "NombreDestino" => $personal_nombre,
                                "Asunto"        => 'Modificación de Cargo',
                                "Body"          => $message,

                                "CorreoCopia"   => ''
                                // "CorreoCopia"   => 'janis.solares@ibnorca.org'
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
            $message  = 'Se ha notificado al personal del cambio de cargo.';
        }else{
            $response = false;
            $message  = 'Fallo envió de correo :(';
        }
    } catch (Exception $e) {
        $response = false;
        $message  = 'Fallo envió de correo, comuniquese con el administrador.';
    }
    echo json_encode(array(
        'status'  => $response,
        'message' => $message
    ));
    exit;
?>