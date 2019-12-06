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
    $cod_estadoreferencial = 1;//$_POST["cod_estadoreferencial"];

    if ($_POST["codigo"] == 0){
        $stmt = $dbh->prepare("INSERT INTO tipos_personal(nombre,abreviatura,cod_estadoreferencial) values (:nombre, :abreviatura, :cod_estadoreferencial)");
        //Bind
        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':abreviatura', $abreviatura);
        $stmt->bindParam(':cod_estadoreferencial', $cod_estadoreferencial);
        $flagSuccess=$stmt->execute();
        $tabla_id = $dbh->lastInsertId();
    
        //$stmt3->debugDumpParams();

        //$arr = $stmt->errorInfo();
        //print_r($arr);
        //$tabla_id = $dbh->lastInsertId();;
        
        showAlertSuccessError($flagSuccess,$urlListTipospersonal);

        //$stmt->debugDumpParams();
    } else {//update

        $stmt = $dbh->prepare("UPDATE tipos_personal set nombre=:nombre,abreviatura=:abreviatura,cod_estadoreferencial=:cod_estadoreferencial where codigo = :codigo");
        //bind
        $stmt->bindParam(':codigo', $codigo);
        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':abreviatura', $abreviatura);
        $stmt->bindParam(':cod_estadoreferencial', $cod_estadoreferencial);
        $flagSuccess=$stmt->execute();
            
        showAlertSuccessError($flagSuccess,$urlListTipospersonal);

    }//si es insert o update
    
    } catch(PDOException $ex){
        //manejar error
        echo "Un error ocurrio".$ex->getMessage();
    }
?>