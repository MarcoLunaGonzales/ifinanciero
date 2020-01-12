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
    $abreviatura = $_POST["abreviatura"];    
    $cod_estadoreferencial = 1;
    
    if ($_POST["codigo"] == 0){
        $stmt = $dbh->prepare("INSERT INTO tipos_cargos_personal(nombre,abreviatura,cod_estadoreferencial) 
        values (:nombre,:abreviatura,:cod_estadoreferencial)");
        //Bind
        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':abreviatura', $abreviatura);
        $stmt->bindParam(':cod_estadoreferencial', $cod_estadoreferencial);        
        $flagSuccess=$stmt->execute();
        $tabla_id = $dbh->lastInsertId();
        showAlertSuccessError($flagSuccess,$urlListTiposCargos);
    } else {//update

        $stmt = $dbh->prepare("UPDATE tipos_cargos_personal set nombre=:nombre,abreviatura=:abreviatura
         where codigo = :codigo");
        //bind
        $stmt->bindParam(':codigo', $codigo);
        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':abreviatura', $abreviatura);        
        $flagSuccess=$stmt->execute();    
        showAlertSuccessError($flagSuccess,$urlListTiposCargos);

    }//si es insert o update
    
    } catch(PDOException $ex){
        //manejar error
        echo "Un error ocurrio".$ex->getMessage();
    }
?>