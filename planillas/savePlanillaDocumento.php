<?php

    require_once '../conexion.php';

    date_default_timezone_set('America/La_Paz');
    session_start();

    $dbh = new Conexion();

    $cod_planilla  = $_POST['cod_planilla'];
    $cod_personal  = $_SESSION['globalUser'];
    $descripcion   = $_POST['descripcion'];
    // Preparación de archivo
    $folder        = 'documentos_planilla';
    $dir           = dirname(__DIR__, 1) . "/" . $folder . "/";
    $fecha_registro= date('Y-m-d H:i:s');
    $time_detail   = date('YmdHis', strtotime($fecha_registro));
    $file_name     = $time_detail . basename($_FILES["file"]["name"]);
    $name          = $dir . $file_name;
    if (move_uploaded_file($_FILES["file"] ["tmp_name"], $name)) {
        $sqlInsert = "INSERT INTO planillas_documentos (cod_planilla, cod_personal, descripcion, archivo, fecha_registro)
                        VALUES ('$cod_planilla', '$cod_personal', '$descripcion', '$file_name', '$fecha_registro')";
        $stmt      = $dbh->prepare($sqlInsert);
        $stmt->execute();
        
        echo json_encode(array(
            'status'  => true,
        ));
    } else {
        echo json_encode(array(
            'status' => false
        ));
    }

?>