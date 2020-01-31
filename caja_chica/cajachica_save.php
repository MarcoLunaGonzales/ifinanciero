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
    $cod_tipocajachica = $_POST["cod_tipocajachica"];
    $fecha = $_POST["fecha"];
    $numero = $_POST["numero"];
    $monto_inicio = $_POST["monto_inicio"];
    $monto_reembolso = $_POST["monto_reembolso"];
    $cod_personal = $_POST["cod_personal"];
    $observaciones = $_POST["observaciones"];
    if($monto_reembolso==null)$monto_reembolso=0;
    $cod_estadoreferencial =   1;    
    $created_by = 1;//$_POST["created_by"];
    $modified_by = 1;//$_POST["modified_by"];


    
    if ($codigo == 0){//insertamos
        //echo "entra";
        
        // echo $cod_uo;
        $stmt = $dbh->prepare("INSERT INTO caja_chica(cod_tipocajachica,fecha,numero,monto_inicio,monto_reembolso,observaciones,cod_personal,cod_estadoreferencial) 
        values ($cod_tipocajachica,'$fecha',$numero,$monto_inicio,$monto_reembolso,'$observaciones',$cod_personal,$cod_estadoreferencial)");
        $flagSuccess=$stmt->execute();
        showAlertSuccessError($flagSuccess,$urlListCajaChica);

        //$stmt->debugDumpParams();
    } else {//update

        $stmt = $dbh->prepare("UPDATE caja_chica set cod_tipocajachica=$cod_tipocajachica,fecha='$fecha',numero=$numero,monto_inicio=$monto_inicio,monto_reembolso=$monto_reembolso,observaciones='$observaciones',cod_personal=$cod_personal
         where codigo = $codigo");      
        $flagSuccess=$stmt->execute();        
        showAlertSuccessError($flagSuccess,$urlListCajaChica."&codigo=".$cod_tipocajachica);

    }//si es insert o update
    
    } catch(PDOException $ex){
        //manejar error
        echo "Un error ocurrio".$ex->getMessage();
    }
?>