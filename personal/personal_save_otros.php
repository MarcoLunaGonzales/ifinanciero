<?php

//require_once '../layouts/bodylogin.php';
require_once 'conexion.php';
require_once 'functions.php';
require_once 'rrhh/configModule.php';
ini_set('display_errors',1);

$dbh = new Conexion();

$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);//para mostrar errores en la ejecucion

try {
    $codigo_item = $_POST["codigo_item"];

    if($codigo_item==1){
        $codigo_personal = $_POST["codigo_personal"];
        $cod_uo = $_POST["cod_uo"];
        $cod_area = $_POST["cod_area"];
        $stmt = $dbh->prepare("UPDATE personal set cod_unidadorganizacional=:cod_uo,cod_area=:cod_area where codigo=:codigo");
        $stmt->bindParam(':codigo', $codigo_personal);
        $stmt->bindParam(':cod_uo', $cod_uo);
        $stmt->bindParam(':cod_area', $cod_area);    
        $flagSuccess=$stmt->execute();        
        showAlertSuccessError($flagSuccess,$urlListPersonal);
    }elseif($codigo_item==2){
        $codigo_personal = $_POST["codigo_personal"];
        $cod_cargo = $_POST["cod_cargo"];        
        $stmt = $dbh->prepare("UPDATE personal set cod_cargo=:cod_cargo where codigo = :codigo");
        $stmt->bindParam(':codigo', $codigo_personal);
        $stmt->bindParam(':cod_cargo', $cod_cargo);
        $flagSuccess=$stmt->execute();        
        showAlertSuccessError($flagSuccess,$urlListPersonal);
    }elseif($codigo_item==3){
        $codigo_personal = $_POST["codigo_personal"];
        $grado_academico = $_POST["grado_academico"];        
        $stmt = $dbh->prepare("UPDATE personal set cod_grado_academico=:grado_academico where codigo = :codigo");
        $stmt->bindParam(':codigo', $codigo_personal);
        $stmt->bindParam(':grado_academico', $grado_academico);
        $flagSuccess=$stmt->execute();        
        showAlertSuccessError($flagSuccess,$urlListPersonal);
    }elseif($codigo_item==4){
        $codigo_personal = $_POST["codigo_personal"];
        $haber_basico = $_POST["haber_basico"];        
        $stmt = $dbh->prepare("UPDATE personal set haber_basico=:haber_basico where codigo = :codigo");
        $stmt->bindParam(':codigo', $codigo_personal);
        $stmt->bindParam(':haber_basico', $haber_basico);
        $flagSuccess=$stmt->execute();        
        showAlertSuccessError($flagSuccess,$urlListPersonal);
    }
    
    
        
   
    
    } catch(PDOException $ex){
        //manejar error
        echo "Un error ocurrio".$ex->getMessage();
    }
?>