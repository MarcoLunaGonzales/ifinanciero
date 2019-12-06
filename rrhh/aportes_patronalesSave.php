<?php

//require_once '../layouts/bodylogin.php';
require_once 'conexion.php';
require_once 'functions.php';
require_once 'configModule.php';
ini_set('display_errors',1);

$dbh = new Conexion();

$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);//para mostrar errores en la ejecucion

try {
    //$codigo=$_POST["codigo"];
    $seguro_riesgo_profesional=$_POST["seguro_riesgo_profesional"];
    $provivienda=$_POST["provivienda"];
    $infocal=$_POST["infocal"];
    $cns=$_POST["cns"];
    $aporte_patronal_solidario=$_POST["aporte_patronal_solidario"];
    $estado=$_POST["estado"];
    //$created_at=$_POST["created_at"];
    $created_by=1;//$_POST["created_by"];
    //$modified_at=$_POST["modified_at"];
    $modified_by=1;//$_POST["modified_by"];
    

        $stmt = $dbh->prepare("UPDATE aportes_patronales set seguro_riesgo_profesional=:seguro_riesgo_profesional,
        provivienda=:provivienda,infocal=:infocal,cns=:cns,aporte_patronal_solidario=:aporte_patronal_solidario,estado=:estado,
        created_by=:created_by,modified_by=:modified_by");
        //bind
        //$stmt->bindParam(':codigo', $codigo);
        $stmt->bindParam(':seguro_riesgo_profesional', $seguro_riesgo_profesional);
        $stmt->bindParam(':provivienda', $provivienda);
        $stmt->bindParam(':infocal', $infocal);
        $stmt->bindParam(':cns', $cns);
        $stmt->bindParam(':aporte_patronal_solidario', $aporte_patronal_solidario);
        $stmt->bindParam(':estado', $estado);
        //$stmt->bindParam(':created_at', $created_at);
        $stmt->bindParam(':created_by', $created_by);
        //$stmt->bindParam(':modified_at', $modified_at);
        $stmt->bindParam(':modified_by', $modified_by);
        $flagSuccess=$stmt->execute();
        
        showAlertSuccessError($flagSuccess,$urlFormaportes_patronales);
    
    } catch(PDOException $ex){
        //manejar error
        echo "Un error ocurrio".$ex->getMessage();
    }
?>