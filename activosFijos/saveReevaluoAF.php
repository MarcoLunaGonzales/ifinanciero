<?php

//require_once '../layouts/bodylogin.php';
require_once 'conexion.php';
require_once 'functions.php';
require_once 'configModule.php';
ini_set('display_errors',1);

$dbh = new Conexion();
$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);//para mostrar errores en la ejecucion
try {

    $codigoactivo=$_POST["codigoactivo"];
    $monto_reevaluo=$_POST["monto_reevaluo"];
    $meses_restantes = $_POST['meses_restantes'];    
    $fecha_reevaluo=date("Y-m-d H:i:s");
    $bandera_depreciar="NO";        
    

    $stmt = $dbh->prepare("UPDATE activosfijos set fecha_reevaluo=:fecha_reevaluo,bandera_depreciar=:bandera_depreciar,vidautilmeses_restante=:meses_restantes,valorresidual=:monto_reevaluo
    where codigo=:codigoactivo");

    $stmt->bindParam(':codigoactivo', $codigoactivo);
    $stmt->bindParam(':fecha_reevaluo', $fecha_reevaluo);
    $stmt->bindParam(':bandera_depreciar', $bandera_depreciar);
    $stmt->bindParam(':meses_restantes', $meses_restantes);        
    $stmt->bindParam(':monto_reevaluo', $monto_reevaluo);

    $flagSuccess=$stmt->execute();        
    showAlertSuccessError($flagSuccess,$urlList6);

    } catch(PDOException $ex){
        //manejar error
        echo "Un error ocurrio".$ex->getMessage();
    }
?>