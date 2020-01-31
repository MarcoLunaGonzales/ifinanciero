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
    $cod_uo = $_POST["cod_uo"];
    $cod_area = $_POST["cod_area"];
    $cod_personal = $_POST["cod_personal"];
    $cod_estadoreferencial =   1;    
    $created_by = 1;//$_POST["created_by"];
    $modified_by = 1;//$_POST["modified_by"];

    
    if ($_POST["codigo"] == 0){//insertamos
        // echo $cod_uo;
        $stmt = $dbh->prepare("INSERT INTO tipos_caja_chica(nombre,cod_uo,cod_area,cod_estadoreferencial,cod_personal) 
        values ('$nombre',$cod_uo,$cod_area,$cod_estadoreferencial,$cod_personal)");
        $flagSuccess=$stmt->execute();
        $tabla_id = $dbh->lastInsertId();
        showAlertSuccessError($flagSuccess,$urlListTiposCajaChica);

        //$stmt->debugDumpParams();
    } else {//update

        $stmt = $dbh->prepare("UPDATE tipos_caja_chica set nombre='$nombre',cod_uo=$cod_uo,cod_area=$cod_area,cod_personal=$cod_personal
         where codigo = $codigo");      
        $flagSuccess=$stmt->execute();        
        showAlertSuccessError($flagSuccess,$urlListTiposCajaChica);

    }//si es insert o update
    
    } catch(PDOException $ex){
        //manejar error
        echo "Un error ocurrio".$ex->getMessage();
    }
?>