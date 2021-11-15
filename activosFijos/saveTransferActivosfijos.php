<?php

//require_once '../layouts/bodylogin.php';
require_once 'conexion.php';
require_once 'functions.php';
require_once 'configModule.php';
ini_set('display_errors',1);

$dbh = new Conexion();

$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);//para mostrar errores en la ejecucion

try {

    
    $codigoactivo=$_POST["codigoactivo"];
    //echo $codigoactivo;
    $cod_unidadorganizacional=$_POST["cod_uo"];
    $cod_area = $_POST['cod_area'];
    $cod_responsable = $_POST["cod_responsables_responsable"];

    $cod_personal_anterior = $_POST["cod_personal_anterior"];

    // echo $cod_personal_anterior;


    //obtener unos datos antes de actualizar...
    $stmtPREVIO = $dbh->prepare("SELECT cod_ubicaciones,estadobien_asig FROM activofijos_asignaciones where cod_activosfijos=:codigo order by codigo limit 1");
    //Ejecutamos;
    $stmtPREVIO->bindParam(':codigo',$codigoactivo);
    $stmtPREVIO->execute();
    $resultPREVIO = $stmtPREVIO->fetch();
    //$codigo = $result['codigo'];
    $cod_ubicaciones = $resultPREVIO['cod_ubicaciones'];
    $estadobien_asig = $resultPREVIO['estadobien_asig'];
    $fechaasignacion=date("Y-m-d H:i:s");
    $cod_estadoasignacionaf = 1;
    
    $stmt = $dbh->prepare("INSERT INTO activofijos_asignaciones(cod_activosfijos,fechaasignacion,
            cod_ubicaciones,cod_unidadorganizacional,cod_area,cod_personal, estadobien_asig,cod_estadoasignacionaf,cod_personal_anterior)
            values (:codigoactivo, :fechaasignacion,
            :cod_ubicaciones, :cod_unidadorganizacional,:cod_area,:cod_personal, :estadobien_asig,:cod_estadoasignacionaf,:cod_personal_anterior)");

        //necesito guardar en una segunda tabla: activofijos_asignaciones

        //Bind
        //$stmt->bindParam(':codigo', $codigo);
        $stmt->bindParam(':codigoactivo', $codigoactivo);
        $stmt->bindParam(':fechaasignacion', $fechaasignacion);
        $stmt->bindParam(':cod_ubicaciones', $cod_ubicaciones);
        $stmt->bindParam(':cod_unidadorganizacional', $cod_unidadorganizacional);        
        $stmt->bindParam(':cod_area', $cod_area);
        $stmt->bindParam(':cod_personal', $cod_responsable);
        $stmt->bindParam(':cod_personal_anterior', $cod_personal_anterior);
        $stmt->bindParam(':estadobien_asig', $estadobien_asig);
        $stmt->bindParam(':cod_estadoasignacionaf', $cod_estadoasignacionaf);

        //$stmt->bindParam(':created_at', $fechaasignacion);
        $flagSuccess=$stmt->execute();



        $stmtSA = $dbh->prepare("UPDATE activosfijos set cod_responsables_responsable=:cod_responsable where codigo=:codigo_activo");
        $stmtSA->bindParam(':cod_responsable', $cod_responsable);
        $stmtSA->bindParam(':codigo_activo', $codigoactivo);
        $flagSuccess2=$stmtSA->execute();
        
        showAlertSuccessError($flagSuccess,$urlList6);

    
    
    } catch(PDOException $ex){
        //manejar error
        echo "Un error ocurrio".$ex->getMessage();
    }
?>