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
    $porcentaje_aporte = $_POST["porcentaje_aporte"];
    $porcentaje_riesgoprofesional = $_POST["porcentaje_riesgoprofesional"];
    $porcentaje_provivienda = $_POST["porcentaje_provivienda"];
 
    if ($_POST["codigo"] == 0){
        $stmt = $dbh->prepare("INSERT INTO tipos_aporteafp(nombre,abreviatura,cod_estadoreferencial,porcentaje_aporte,porcentaje_riesgoprofesional,porcentaje_provivienda) values (:nombre, :abreviatura, :cod_estadoreferencial, :porcentaje_aporte, :porcentaje_riesgoprofesional, :porcentaje_provivienda)");
        //Bind
        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':abreviatura', $abreviatura);
        $stmt->bindParam(':cod_estadoreferencial', $cod_estadoreferencial);
        $stmt->bindParam(':porcentaje_aporte', $porcentaje_aporte);
        $stmt->bindParam(':porcentaje_riesgoprofesional', $porcentaje_riesgoprofesional);
        $stmt->bindParam(':porcentaje_provivienda', $porcentaje_provivienda);
        $flagSuccess=$stmt->execute();
        
        $tabla_id = $dbh->lastInsertId();
    
        //$stmt3->debugDumpParams();

        //$arr = $stmt->errorInfo();
        //print_r($arr);
        //$tabla_id = $dbh->lastInsertId();;
        
        showAlertSuccessError($flagSuccess,$urlListTipos_aporteafp);

        //$stmt->debugDumpParams();
    } else {//update

        $stmt = $dbh->prepare("UPDATE tipos_aporteafp set nombre=:nombre,abreviatura=:abreviatura,cod_estadoreferencial=:cod_estadoreferencial,porcentaje_aporte=:porcentaje_aporte,porcentaje_riesgoprofesional=:porcentaje_riesgoprofesional,porcentaje_provivienda=:porcentaje_provivienda where codigo = :codigo");
        //bind
        $stmt->bindParam(':codigo', $codigo);
        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':abreviatura', $abreviatura);
        $stmt->bindParam(':cod_estadoreferencial', $cod_estadoreferencial);
        $stmt->bindParam(':porcentaje_aporte', $porcentaje_aporte);
        $stmt->bindParam(':porcentaje_riesgoprofesional', $porcentaje_riesgoprofesional);
        $stmt->bindParam(':porcentaje_provivienda', $porcentaje_provivienda);
        $flagSuccess=$stmt->execute();
        
            
        showAlertSuccessError($flagSuccess,$urlListTipos_aporteafp);

    }//si es insert o update
    
    } catch(PDOException $ex){
        //manejar error
        echo "Un error ocurrio".$ex->getMessage();
    }
?>