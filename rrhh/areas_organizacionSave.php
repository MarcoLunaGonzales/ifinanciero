<?php

//require_once '../layouts/bodylogin.php';
require_once 'conexion.php';
require_once 'functions.php';
require_once 'configModule.php';
ini_set('display_errors',1);

$dbh = new Conexion();

$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);//para mostrar errores en la ejecucion

try {
    $codigo = $_POST["codigo"];
    $cod_unidad = $_POST["cod_unidad"];
    $cod_area = $_POST["cod_area"];
    $cod_areaorganizacion_padre = $_POST["cod_areaorganizacion_padre"];
    $cod_estadoreferencial = 1;
    //$created_at = $_POST["created_at"];
    $created_by = 1;//$_POST["created_by"];
    //$modified_at = $_POST["modified_at"];
    $modified_by = 1;//$_POST["modified_by"];
 
    if ($_POST["codigo"] == 0){
        $stmt = $dbh->prepare("INSERT INTO areas_organizacion(cod_unidad,cod_area,cod_areaorganizacion_padre,cod_estadoreferencial,created_by,modified_by)
         values (:cod_unidad, :cod_area, :cod_areaorganizacion_padre, :cod_estadoreferencial, :created_by, :modified_by)");
        //Bind
        $stmt->bindParam(':cod_unidad', $cod_unidad);
        $stmt->bindParam(':cod_area', $cod_area);
        $stmt->bindParam(':cod_areaorganizacion_padre', $cod_areaorganizacion_padre);
        $stmt->bindParam(':cod_estadoreferencial', $cod_estadoreferencial);
        //$stmt->bindParam(':created_at', $created_at);
        $stmt->bindParam(':created_by', $created_by);
        //$stmt->bindParam(':modified_at', $modified_at);
        $stmt->bindParam(':modified_by', $modified_by);
        $flagSuccess=$stmt->execute();
        $tabla_id = $dbh->lastInsertId();
        showAlertSuccessError($flagSuccess,$urlListAreas_organizacion);

        //$stmt->debugDumpParams();
    } else {//update

        $stmt = $dbh->prepare("UPDATE areas_organizacion set cod_unidad=:cod_unidad,cod_area=:cod_area,cod_areaorganizacion_padre=:cod_areaorganizacion_padre,
        cod_estadoreferencial=:cod_estadoreferencial,created_by=:created_by,modified_by=:modified_by where codigo = :codigo");
        //bind
        $stmt->bindParam(':codigo', $codigo);
        $stmt->bindParam(':cod_unidad', $cod_unidad);
        $stmt->bindParam(':cod_area', $cod_area);
        $stmt->bindParam(':cod_areaorganizacion_padre', $cod_areaorganizacion_padre);
        $stmt->bindParam(':cod_estadoreferencial', $cod_estadoreferencial);
        
        $stmt->bindParam(':created_by', $created_by);
       
        $stmt->bindParam(':modified_by', $modified_by);
        $flagSuccess=$stmt->execute();
        
            
        showAlertSuccessError($flagSuccess,$urlListAreas_organizacion);

    }//si es insert o update
    
    } catch(PDOException $ex){
        //manejar error
        echo "Un error ocurrio".$ex->getMessage();
    }
?>