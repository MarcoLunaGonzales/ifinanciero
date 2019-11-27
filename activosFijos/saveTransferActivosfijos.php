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

    //echo $cod_responsable;


    //obtener unos datos antes de actualizar...
    $stmtPREVIO = $dbh->prepare("SELECT * FROM activofijos_asignaciones where cod_activosfijos=:codigo");
    //Ejecutamos;
    $stmtPREVIO->bindParam(':codigo',$codigoactivo);
    $stmtPREVIO->execute();
    $resultPREVIO = $stmtPREVIO->fetch();
    //$codigo = $result['codigo'];
    $cod_ubicaciones = $resultPREVIO['cod_ubicaciones'];
    $estadobien_asig = $resultPREVIO['estadobien_asig'];
    
    $fechaasignacion=date("Y-m-d H:i:s");
    $cod_estadoasignacionaf = 1;


        //now()
        
        $stmt = $dbh->prepare("INSERT INTO activofijos_asignaciones(cod_activosfijos,fechaasignacion,
            cod_ubicaciones,cod_unidadorganizacional,cod_area,cod_personal, estadobien_asig,cod_estadoasignacionaf)
            values (:codigoactivo, :fechaasignacion,
            :cod_ubicaciones, :cod_unidadorganizacional,:cod_area,:cod_personal, :estadobien_asig,:cod_estadoasignacionaf)");

        //necesito guardar en una segunda tabla: activofijos_asignaciones

        //$stmt->debugDumpParams();

        //Bind
        //$stmt->bindParam(':codigo', $codigo);
        $stmt->bindParam(':codigoactivo', $codigoactivo);
        $stmt->bindParam(':fechaasignacion', $fechaasignacion);
        $stmt->bindParam(':cod_ubicaciones', $cod_ubicaciones);

        $stmt->bindParam(':cod_unidadorganizacional', $cod_unidadorganizacional);//no sirve
        //
        $stmt->bindParam(':cod_area', $cod_area);//no sirve
        $stmt->bindParam(':cod_personal', $cod_responsable);//no sirve

        $stmt->bindParam(':estadobien_asig', $estadobien_asig);
        $stmt->bindParam(':cod_estadoasignacionaf', $cod_estadoasignacionaf);
        //$stmt->bindParam(':created_at', $fechaasignacion);


        $flagSuccess=$stmt->execute();
        ///////////////////////////////////////////////////////////////////////////////////////////////

        
        //$stmt3->debugDumpParams();

        //$arr = $stmt->errorInfo();
        //print_r($arr);
        //$tabla_id = $dbh->lastInsertId();;
        
        showAlertSuccessError($flagSuccess,$urlList6);

    
    
    } catch(PDOException $ex){
        //manejar error
        echo "Un error ocurrio".$ex->getMessage();
    }
?>