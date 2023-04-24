<?php

    require_once '../conexion.php';

    date_default_timezone_set('America/La_Paz');
    session_start();

    $dbh = new Conexion();

    $codigo              = $_POST['codigo'];
    $cod_personal        = $_SESSION['globalUser'];
    $fecha_actualizacion = date('Y-m-d H:i:s');
    try {
        $sqlUpd = "UPDATE planillas_documentos 
                SET cod_estado = 2,
                cod_personal_eliminacion = $cod_personal,
                fecha_actualizacion = '$fecha_actualizacion' 
                WHERE codigo = '".$codigo."'";
        $stmt        = $dbh->prepare($sqlUpd);
        $stmt->execute();
        echo json_encode(array(
            'status'  => true,
        ));
    } catch (\Throwable $th) {
        echo json_encode(array(
            'status' => false
        ));
    }

?>