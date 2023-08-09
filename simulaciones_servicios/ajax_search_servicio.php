<?php
    require_once '../conexion.php';
    require_once '../functions.php';
    date_default_timezone_set('America/La_Paz');

    $cod_tipo_servicio = $_POST['cod_tipo_servicio'];
    
    try{
        $dbh = new Conexion();
        $sql = '';
        // Lista de servicios
        $sql = "SELECT c.IdClasificador as codigo, c.Descripcion as descripcion
        FROM ibnorca.clasificador c
        INNER JOIN ibnorca.tiposervicio_servicio ts ON ts.idServicio=c.IdClasificador
        WHERE ts.idTipoServicio = '$cod_tipo_servicio'";

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