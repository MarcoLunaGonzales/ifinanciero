<?php
    require_once '../conexion.php';
    require_once '../functions.php';
    date_default_timezone_set('America/La_Paz');

    $plantilla = $_POST['plantilla'];
    
    try{
        $dbh = new Conexion();
        $sql = '';
        // Realizar la consulta para obtener la lista de tipo de servicio
        if($plantilla == 2){
            // TCP
            $sql = "SELECT DISTINCT(codigo_n2), descripcion_n2, abreviatura_n2 FROM cla_servicios WHERE codigo_n1=108 AND vigente = 1 AND Aprobado = 1 ORDER BY 1;";
        }else if($plantilla == 3){
            // TCS
            $sql = "SELECT DISTINCT(codigo_n2), descripcion_n2, abreviatura_n2 FROM cla_servicios WHERE codigo_n1=109 AND vigente = 1 AND Aprobado = 1 ORDER BY 1;";
        }else if($plantilla == 10){
            // TVR
            $sql = "SELECT DISTINCT(codigo_n2), descripcion_n2, abreviatura_n2 FROM cla_servicios WHERE codigo_n1=5290 AND vigente = 1 AND Aprobado = 1 ORDER BY 1;";
        }

        $stmt = $dbh->prepare($sql);
        $stmt->execute();
        $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode(array(
            'status' => true,
            'data'   => (empty($resultados)? [] : $resultados)
        ));
    } catch (Exception $e) {
        echo json_encode(array(
            'status' => false
        ));
    }