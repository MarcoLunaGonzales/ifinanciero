<?php

//require_once '../layouts/bodylogin.php';
require_once 'conexion.php';
require_once 'functions.php';
require_once 'rrhh/configModule.php';
ini_set('display_errors',1);

$dbh = new Conexion();

$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);//para mostrar errores en la ejecucion

try {
    // $codigo = $_POST["codigo"];
    $codigo = $_POST["codigo"];
    $nombre_personal=$_POST["nombre_personal"];
    $cod_estadopersonal = $_POST["cod_estadopersonal"];
    
    $created_by = 1;//$_POST["created_by"];
    //$modified_at = $_POST["modified_at"];
    $modified_by = 1;//$_POST["modified_by"];
    $cod_estadoreferencial=2;
    $cod_estadoreferencial_2=1;
    $bandera=1;
    $ing_contr=date('Y-m-d');
    $sql="UPDATE personal set 
    cod_estadopersonal='$cod_estadopersonal',bandera='$bandera',cod_estadoreferencial='$cod_estadoreferencial_2',ing_contr='$ing_contr'
    where codigo = $codigo";
    // echo $sql;
    $stmt = $dbh->prepare($sql);
    //bind
    $flagSuccess=$stmt->execute();
    if($flagSuccess){
        $stmt2 = $dbh->prepare("UPDATE personal_retiros set 
        cod_estadoreferencial=$cod_estadoreferencial
        where cod_personal = $codigo");
        $stmt2->execute();    
    }
    showAlertSuccessError($flagSuccess,$urlListPersonalRetirado);

    
} catch(PDOException $ex){
    //manejar error
    echo "Un error ocurrio".$ex->getMessage();
}
?>