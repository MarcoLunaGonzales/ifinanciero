<?php

//require_once '../layouts/bodylogin.php';
require_once 'conexion.php';
require_once 'functions.php';
require_once 'configModule.php';
ini_set('display_errors',1);

$dbh = new Conexion();

$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);//para mostrar errores en la ejecucion

try {
    $codigo         = $_POST["codigo"];
    $nombre         = $_POST["nombre"];
    $abreviatura    = $_POST["abreviatura"];
    $cod_tipo_cargo = $_POST["cod_tipo_cargo"]; // Nivel del Cargo
    $cod_padre      = $_POST["cod_padre"];
    $objetivo       = $_POST["objetivo"];
    $cod_estadoreferencial =   1;    
    $created_by     = 1;//$_POST["created_by"];
    $modified_by    = 1;//$_POST["modified_by"];
    
    if ($_POST["codigo"] == 0){
        $stmt = $dbh->prepare("INSERT INTO cargos(nombre,abreviatura,cod_tipo_cargo,cod_estadoreferencial,created_by,modified_by,objetivo,cod_padre) 
        values (:nombre, :abreviatura,:cod_tipo_cargo, :cod_estadoreferencial,:created_by,:modified_by,:objetivo,:cod_padre)");
        //Bind
        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':abreviatura', $abreviatura);
        $stmt->bindParam(':cod_tipo_cargo', $cod_tipo_cargo);
        $stmt->bindParam(':cod_estadoreferencial', $cod_estadoreferencial);
        $stmt->bindParam(':created_by', $created_by);
        $stmt->bindParam(':modified_by', $modified_by);
        $stmt->bindParam(':objetivo', $objetivo);
        $stmt->bindParam(':cod_padre', $cod_padre);
        $flagSuccess=$stmt->execute();
        $tabla_id = $dbh->lastInsertId();
        showAlertSuccessError($flagSuccess,$urlListCargos);

        //$stmt->debugDumpParams();
    } else {//update

        $stmt = $dbh->prepare("UPDATE cargos 
            SET nombre=:nombre,abreviatura=:abreviatura,cod_tipo_cargo=:cod_tipo_cargo,cod_estadoreferencial=:cod_estadoreferencial,modified_by=:modified_by,objetivo=:objetivo,cod_padre=:cod_padre
            WHERE codigo = :codigo");
        //bind
        $stmt->bindParam(':codigo', $codigo);
        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':abreviatura', $abreviatura);
        $stmt->bindParam(':cod_tipo_cargo', $cod_tipo_cargo);
        $stmt->bindParam(':cod_estadoreferencial', $cod_estadoreferencial);        
        $stmt->bindParam(':modified_by', $modified_by);
        $stmt->bindParam(':objetivo', $objetivo);
        $stmt->bindParam(':cod_padre', $cod_padre);
        
        $flagSuccess=$stmt->execute();
        
        showAlertSuccessError($flagSuccess,$urlListCargos);

    }//si es insert o update
    
    } catch(PDOException $ex){
        //manejar error
        echo "Un error ocurrio".$ex->getMessage();
    }
?>