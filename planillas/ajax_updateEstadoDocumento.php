<?php

    require_once '../conexion.php';

    date_default_timezone_set('America/La_Paz');
    session_start();

    $dbh = new Conexion();

    $codigo = $_POST['codigo'];
    
    try {
        $sqlUpd = "UPDATE planillas 
                SET cod_estado_documento = 2
                WHERE codigo = '".$codigo."'";
        $stmt   = $dbh->prepare($sqlUpd);
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