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
    $nro_autorizacion = $_POST["nro_autorizacion"];
    $llave_dosificacion = $_POST["llave_dosificacion"];
    $fecha_limite_emision = $_POST["fecha_limite_emision"];
    $cod_sucursal = $_POST["cod_sucursal"];
    $leyenda = $_POST["leyenda"];
    $cod_estado =0;
    $fecha_actual=date('Y-m-d');
    if ($_POST["codigo"] == 0){//insertamos
        // echo $cod_uo;
        $stmt = $dbh->prepare("INSERT INTO dosificaciones_facturas(fecha,cod_sucursal,nro_autorizacion,llave_dosificacion,fecha_limite_emision,leyenda,cod_estado) 
        values ('$fecha_actual',$cod_sucursal,$nro_autorizacion,'$llave_dosificacion','$fecha_limite_emision','$leyenda',$cod_estado)");
        $flagSuccess=$stmt->execute();
        $tabla_id = $dbh->lastInsertId();
        showAlertSuccessError($flagSuccess,$urlListDosificacion);
        //$stmt->debugDumpParams();
    } else {//update
        $stmt = $dbh->prepare("UPDATE dosificaciones_facturas set cod_sucursal='$cod_sucursal',nro_autorizacion=$nro_autorizacion,llave_dosificacion='$llave_dosificacion',fecha_limite_emision='$fecha_limite_emision',leyenda='$leyenda'
         where codigo = $codigo");      
        $flagSuccess=$stmt->execute();        
        showAlertSuccessError($flagSuccess,$urlListDosificacion);
    }//si es insert o update
    
    } catch(PDOException $ex){
        //manejar error
        echo "Un error ocurrio".$ex->getMessage();
    }
?>