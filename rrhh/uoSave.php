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
    $nombre = $_POST["nombre"];
    $abreviatura = $_POST["abreviatura"];
    $observaciones = $_POST["observaciones"];
    $cod_estadoreferencial = $_POST["cod_estadoreferencial"];
    //$created_at = $_POST["created_at"];
    $created_by = 1;//$_POST["created_by"];
    //$modified_at = $_POST["modified_at"];
    $modified_by = 1;//$_POST["modified_by"];

    if ($_POST["codigo"] == 0){
        $stmt = $dbh->prepare("INSERT INTO unidades_organizacionales(nombre,abreviatura,observaciones,cod_estadoreferencial,created_by,modified_by) 
        values (:nombre, :abreviatura, :observaciones, :cod_estadoreferencial, :created_by, :modified_by)");
        //Bind
        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':abreviatura', $abreviatura);
        $stmt->bindParam(':observaciones', $observaciones);
        $stmt->bindParam(':cod_estadoreferencial', $cod_estadoreferencial);
        //$stmt->bindParam(':created_at', $created_at);
        $stmt->bindParam(':created_by', $created_by);
        //$stmt->bindParam(':modified_at', $modified_at);
        $stmt->bindParam(':modified_by', $modified_by);
        $flagSuccess=$stmt->execute();
        $tabla_id = $dbh->lastInsertId();
    
        //$stmt3->debugDumpParams();

        //$arr = $stmt->errorInfo();
        //print_r($arr);
        //$tabla_id = $dbh->lastInsertId();;
        
        showAlertSuccessError($flagSuccess,$urlListUO);

        //$stmt->debugDumpParams();
    } else {//update

        $stmt = $dbh->prepare("UPDATE unidades_organizacionales set nombre=:nombre,abreviatura=:abreviatura,observaciones=:observaciones,cod_estadoreferencial=:cod_estadoreferencial,
        created_by=:created_by,modified_by=:modified_by where codigo = :codigo");
        //bind
        $stmt->bindParam(':codigo', $codigo);
        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':abreviatura', $abreviatura);
        $stmt->bindParam(':observaciones', $observaciones);
        $stmt->bindParam(':cod_estadoreferencial', $cod_estadoreferencial);
        //$stmt->bindParam(':created_at', $created_at);
        $stmt->bindParam(':created_by', $created_by);
        //$stmt->bindParam(':modified_at', $modified_at);
        $stmt->bindParam(':modified_by', $modified_by);
        $flagSuccess=$stmt->execute();
            
        showAlertSuccessError($flagSuccess,$urlListUO);

    }//si es insert o update
    
    } catch(PDOException $ex){
        //manejar error
        echo "Un error ocurrio".$ex->getMessage();
    }
?>