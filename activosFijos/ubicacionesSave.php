<?php

//require_once '../layouts/bodylogin.php';
require_once 'conexion.php';
require_once 'functions.php';
require_once 'configModule.php';

$dbh = new Conexion();
$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);//try

if ($_POST["codigo"] == 0){
    $codigo = $_POST["codigo"];
    $cod_unidades_organizacionales = $_POST["cod_unidades_organizacionales"];
    $edificio = $_POST["edificio"];
    $oficina = $_POST["oficina"];
    $cod_estado = 1;//$_POST["cod_estado"];
    $created_by = $_SESSION["globalUser"];
    $modified_by = $_SESSION["globalUser"];
    $cod_areas = 11;
    try{
        $stmt = $dbh->prepare("INSERT INTO ubicaciones(cod_unidades_organizacionales,edificio,oficina,cod_estado,created_by,modified_by,cod_areas) 
        values (:cod_unidades_organizacionales, :edificio, :oficina, :cod_estado,  :created_by, :modified_by, :cod_areas)");
        //Bind
        $stmt->bindParam(':cod_unidades_organizacionales', $cod_unidades_organizacionales);
        $stmt->bindParam(':edificio', $edificio);
        $stmt->bindParam(':oficina', $oficina);
        $stmt->bindParam(':cod_estado', $cod_estado);
        $stmt->bindParam(':created_by', $created_by);
        $stmt->bindParam(':modified_by', $modified_by);
        $stmt->bindParam(':cod_areas', $cod_areas);
        $flagSuccess=$stmt->execute();
        $tabla_id = $dbh->lastInsertId();
        showAlertSuccessError($flagSuccess,$urlList2);
    } catch(PDOException $ex){
        echo "Un error ocurrio".$ex->getMessage();
    }
} else {//update
    $codigo = $_POST["codigo"];
    $cod_unidades_organizacionales = $_POST["cod_unidades_organizacionales"];
    $edificio = $_POST["edificio"];
    $oficina = $_POST["oficina"];
    $cod_estado = 1;//$_POST["cod_estado"];
    $created_by = $_SESSION["globalUser"];
    $modified_by = $_SESSION["globalUser"];
    $cod_areas = 11;
    //prepare
    try{
        $stmt = $dbh->prepare("UPDATE ubicaciones set cod_unidades_organizacionales=:cod_unidades_organizacionales,edificio=:edificio,oficina=:oficina,cod_estado=:cod_estado,created_by=:created_by,modified_by=:modified_by,cod_areas=:cod_areas where codigo = :codigo");
        //bind
        $stmt->bindParam(':codigo', $codigo);
        $stmt->bindParam(':cod_unidades_organizacionales', $cod_unidades_organizacionales);
        $stmt->bindParam(':edificio', $edificio);
        $stmt->bindParam(':oficina', $oficina);
        $stmt->bindParam(':cod_estado', $cod_estado);
        $stmt->bindParam(':created_by', $created_by);
        $stmt->bindParam(':modified_by', $modified_by);
        $stmt->bindParam(':cod_areas', $cod_areas);
        $flagSuccess=$stmt->execute();
        showAlertSuccessError($flagSuccess,$urlList2);

    } catch(PDOException $ex){
        echo "Un error ocurrio".$ex->getMessage();
    }
}
?>
