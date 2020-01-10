<?php

//require_once '../layouts/bodylogin.php';
require_once 'conexion.php';
require_once 'functions.php';
require_once 'rrhh/configModule.php';
ini_set('display_errors',1);
$dbh = new Conexion();
$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);//para mostrar errores en la ejecucion

try {
    $codigo = $_POST["codigo"];
    $nombre = $_POST["nombre"];
    $duracion_meses = $_POST["duracion_meses"];
    $meses_alerta = $_POST["meses_alerta"];
    $cod_estadoreferencial = 1;
    
    if ($_POST["codigo"] == 0){
        $stmt = $dbh->prepare("INSERT INTO tipos_contrato_personal(nombre,duracion_meses,cod_estadoreferencial,meses_alerta) 
        values (:nombre, :duracion_meses,:cod_estadoreferencial,:meses_alerta)");
        //Bind
        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':duracion_meses', $duracion_meses);
        $stmt->bindParam(':cod_estadoreferencial', $cod_estadoreferencial);
        $stmt->bindParam(':meses_alerta', $meses_alerta);
        $flagSuccess=$stmt->execute();
        $tabla_id = $dbh->lastInsertId();
        showAlertSuccessError($flagSuccess,$urlListTiposContratos);
    } else {//update

        $stmt = $dbh->prepare("UPDATE tipos_contrato_personal set nombre=:nombre,duracion_meses=:duracion_meses,cod_estadoreferencial=:cod_estadoreferencial,
        meses_alerta=:meses_alerta where codigo = :codigo");
        //bind
        $stmt->bindParam(':codigo', $codigo);
        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':duracion_meses', $duracion_meses);
        $stmt->bindParam(':cod_estadoreferencial', $cod_estadoreferencial);
        $stmt->bindParam(':meses_alerta', $meses_alerta);        
        $flagSuccess=$stmt->execute();    
        showAlertSuccessError($flagSuccess,$urlListTiposContratos);

    }//si es insert o update
    
    } catch(PDOException $ex){
        //manejar error
        echo "Un error ocurrio".$ex->getMessage();
    }
?>