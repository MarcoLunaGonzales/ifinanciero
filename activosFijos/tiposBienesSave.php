<?php
require_once 'conexion.php';//archivo tablaSave.php
require_once 'functions.php';
require_once 'configModule.php';

$dbh = new Conexion();
$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);//try
//echo "n".$_POST["codigo"]."n"; 
if ($_POST["codigo"] == 0){
    $codigo = $_POST["codigo"];
    $cod_depreciaciones = $_POST["cod_depreciaciones"];
    $tipo_bien = $_POST["tipo_bien"];
    $codEstado=1;
    try {
        //$stmt = $dbh->prepare("INSERT INTO TABLA(cod_depreciaciones,tipo_bien) values (:cod_depreciaciones, :tipo_bien)");
        $stmt = $dbh->prepare("INSERT INTO tiposbienes (cod_depreciaciones, tipo_bien, cod_estado) values (:cod_depreciaciones, :tipo_bien, :cod_estado)");
        //Bind
        $stmt->bindParam(':cod_depreciaciones', $cod_depreciaciones);
        $stmt->bindParam(':tipo_bien', $tipo_bien);
        $stmt->bindParam(':cod_estado', $codEstado);
        $flagSuccess=$stmt->execute();
        $tabla_id = $dbh->lastInsertId();

        showAlertSuccessError($flagSuccess,$urlList5);
    } catch(PDOException $ex){
        echo "Un error ocurrio".$ex->getMessage();
    }
} else {//update
    try {
        //echo "entra";
        $codigo = $_POST["codigo"];
        $cod_depreciaciones = $_POST["cod_depreciaciones"];
        $tipo_bien = $_POST["tipo_bien"];
        //prepare
        $stmt = $dbh->prepare("UPDATE tiposbienes set cod_depreciaciones=:cod_depreciaciones,tipo_bien=:tipo_bien where codigo = :codigo");
        //bind
        $stmt->bindParam(':codigo', $codigo);
        $stmt->bindParam(':cod_depreciaciones', $cod_depreciaciones);
        $stmt->bindParam(':tipo_bien', $tipo_bien);
        //$flagSuccess=$stmt->execute();
        $flagSuccess=$stmt->execute();
        showAlertSuccessError($flagSuccess,$urlList5);
    } catch(PDOException $ex){
        echo "Un error ocurrio".$ex->getMessage();
    }
}    
?>