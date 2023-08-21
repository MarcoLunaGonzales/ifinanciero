<?php
    require_once '../conexion.php';
    require_once '../functions.php';
    date_default_timezone_set('America/La_Paz');

    $cod_simulacionservicio = $_POST['cod_simulacionservicio'];
    
    try{
        $dbh = new Conexion();
        // Lista de servicios
        $sql = "SELECT
            s.codigo,
            s.numero,
            s.fecha 
        FROM solicitud_recursos s 
        WHERE s.cod_simulacionservicio = '$cod_simulacionservicio' 
        AND s.cod_estadoreferencial = 1";

        $stmt = $dbh->prepare($sql);
        $stmt->execute();
        $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode(array(
            'status' => true,
            'data'   => (empty($resultados)? [] : $resultados)
        ));
    } catch (Exception $e) {
        echo json_encode(array(
            'status' => false,
            'data'   => ''
        ));
    }