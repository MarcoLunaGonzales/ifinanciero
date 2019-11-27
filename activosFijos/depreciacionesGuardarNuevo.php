<?php

//require_once '../layouts/bodylogin.php';
require_once 'conexion.php';
require_once 'functions.php';
require_once 'configModule.php';

$dbh = new Conexion();
$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);//try
if ($_POST["codigo"] == 0){
    //echo "por insert";
    //$cod_empresa=$_POST["cod_empresa"];
    $cod_empresa=1;
    $nombre=$_POST["nombre"];
    $vida_util=$_POST["vida_util"];
    //$coeficiente=$_POST["coeficiente"];
    //$deprecia=$_POST["deprecia"];
    //$actualiza=$_POST["actualiza"];
    //$cod_estado=$_POST["cod_estado"];
    $cod_cuentacontable=$_POST["cod_cuentacontable"];
    $cod_estado=1;
    //Prepare
    try{
        $stmt = $dbh->prepare("INSERT INTO depreciaciones(cod_empresa,nombre,vida_util,cod_estado, cod_cuentacontable) 
        values (:cod_empresa, :nombre, :vida_util, :cod_estado, :cod_cuentacontable);");
        //Bind
        //$stmt->bindParam(':codigo', $codigo);
        $stmt->bindParam(':cod_empresa', $cod_empresa);
        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':vida_util', $vida_util);
        $stmt->bindParam(':cod_cuentacontable', $cod_cuentacontable);
        $stmt->bindParam(':cod_estado', $cod_estado);
        $flagSuccess=$stmt->execute();
        //$tabla_id = $dbh->lastInsertId();;

        showAlertSuccessError($flagSuccess,$urlList4);
    } catch(PDOException $ex){
        echo "Un error ocurrio".$ex->getMessage();
    }
} else {
    //echo "por update";
    $codigo=$_POST["codigo"];
    $cod_empresa=1;
    $nombre=$_POST["nombre"];
    $vida_util=$_POST["vida_util"];
    $cod_estado = 1;
    $cod_cuentacontable=$_POST["cod_cuentacontable"];
    try{
        
        //prepare
        $stmt = $dbh->prepare("UPDATE depreciaciones set cod_empresa=:cod_empresa,nombre=:nombre,vida_util=:vida_util,
            cod_estado=:cod_estado,cod_cuentacontable=:cod_cuentacontable where codigo = :codigo");
        //bind
        $stmt->bindParam(':codigo', $codigo);
        $stmt->bindParam(':cod_empresa', $cod_empresa);
        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':vida_util', $vida_util);
        $stmt->bindParam(':cod_estado', $cod_estado);
        $stmt->bindParam(':cod_cuentacontable', $cod_cuentacontable);
        $flagSuccess=$stmt->execute();
        showAlertSuccessError($flagSuccess,$urlList4);
    } catch(PDOException $ex){
        echo "Un error ocurrio".$ex->getMessage();
    }
}
?>