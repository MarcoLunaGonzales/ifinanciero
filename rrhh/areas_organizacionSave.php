<?php

//require_once '../layouts/bodylogin.php';
require_once 'conexion.php';
require_once 'functions.php';
require_once 'configModule.php';
ini_set('display_errors',1);

$dbh = new Conexion();
try {
    $areas=$_POST["areas"];
    $cod_unidad = $_POST["codUnidad"];    
    //$cod_areaorganizacion_padre = $_POST["cod_areaorganizacion_padre"];
    $cod_areaorganizacion_padre = $_POST["cod_areaorganizacion_padre"];

    $cod_estadoreferencial = 1;
    //$created_at = $_POST["created_at"];
    $created_by = 1;//$_POST["created_by"];
    //$modified_at = $_POST["modified_at"];
    $modified_by = 1;//$_POST["modified_by"];

    
    // for ($i=0;$i<count($areas);$i++){
    //     echo $areas[$i]."<br>";
    // }
 
    $stmtDel = $dbh->prepare("DELETE FROM areas_organizacion where cod_unidad='$cod_unidad'");
    $stmtDel->execute();
    $flagSuccessDetail=true;
    for ($i=0;$i<count($areas);$i++){
        $stmt = $dbh->prepare("INSERT INTO areas_organizacion(cod_unidad,cod_area,cod_areapadre,cod_estadoreferencial,created_by,modified_by)
             values (:cod_unidad, :cod_area, :cod_areaorganizacion_padre, :cod_estadoreferencial, :created_by, :modified_by)");
        $stmt->bindParam(':cod_unidad', $cod_unidad);
        $stmt->bindParam(':cod_area', $areas[$i]);
        $stmt->bindParam(':cod_areaorganizacion_padre', $cod_areaorganizacion_padre[$i]);
        $stmt->bindParam(':cod_estadoreferencial', $cod_estadoreferencial);    
        $stmt->bindParam(':created_by', $created_by);
        $stmt->bindParam(':modified_by', $modified_by);

        $flagSuccess=$stmt->execute();
        if($flagSuccess==false){
            $flagSuccessDetail=false;
        }        
    }

    if($flagSuccessDetail==true){
        showAlertSuccessError(true,$urlListUO);   
    }else{
        showAlertSuccessError(false,$urlListUO);
    }
    
    } catch(PDOException $ex){
        //manejar error
        echo "Un error ocurrio".$ex->getMessage();
    }
?>