<?php

//require_once '../layouts/bodylogin.php';
require_once 'conexion.php';
require_once 'functions.php';
require_once 'configModule.php';
ini_set('display_errors',1);

$dbh = new Conexion();

$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);//para mostrar errores en la ejecucion

try {
    $codigo = trim($_POST["codigo"]);
    $cod_tcc = trim($_POST["cod_tcc"]);
    $cod_cc = trim($_POST["cod_cc"]);

    $monto = trim($_POST["monto"]);
    $fecha = trim($_POST["fecha"]);
    $cod_personal = trim($_POST["cod_personal"]);
    $observaciones = trim($_POST["observaciones"]);
    $cod_comprobante = trim($_POST["cod_comprobante"]);
    $cod_comprobante_detalle = trim($_POST["cod_comprobante_detalle"]);

    //sacamos monto de caja chica
    $stmtMCC = $dbh->prepare("SELECT monto_reembolso from caja_chica where  codigo =$cod_cc");
    $stmtMCC->execute();
    $resultMCC=$stmtMCC->fetch();
    $monto_saldo_anterior_cc=$resultMCC['monto_reembolso'];

    if ($codigo == 0){//insertamos
        $monto_saldo=$monto_saldo_anterior_cc+$monto;
        $cod_estadoreferencial=1;        
        $stmt = $dbh->prepare("INSERT INTO caja_chicareembolsos(cod_cajachica,fecha,cod_personal,monto,observaciones,cod_estadoreferencial,cod_comprobante,cod_comprobante_detalle) 
        values ($cod_cc,'$fecha','$cod_personal',$monto,'$observaciones',$cod_estadoreferencial,'$cod_comprobante','$cod_comprobante_detalle')");
        $flagSuccess=$stmt->execute();
        if($flagSuccess){//actualizamos el saldo
            $stmtReembolso = $dbh->prepare("UPDATE caja_chica set monto_reembolso=$monto_saldo,monto_reembolso_nuevo=$monto where codigo=$cod_cc");           
            $flagSuccess=$stmtReembolso->execute();
        }
        showAlertSuccessError($flagSuccess,$urlListDetalleCajaChica.'&codigo='.$cod_cc.'&cod_tcc='.$cod_tcc);
    } else {//update
        
        //actualizamos monto reeembolso
        //sacamos monto anterior de reembolso
        $stmtMontoAnterior = $dbh->prepare("SELECT monto from caja_chicareembolsos where codigo=$codigo order by codigo desc LIMIT 1");
        $stmtMontoAnterior->execute();
        $resultMontoAnterior = $stmtMontoAnterior->fetch();
        $monto_anterior_reembolso = $resultMontoAnterior['monto'];
        
        
        $monto_saldo=$monto_saldo_anterior_cc-$monto_anterior_reembolso+$monto;
       
        //================================================================
        // echo 'fecha:'.$fecha."<br>";
        // echo 'cod_tipo_documento:'.$cod_tipo_documento."<br>";
        // echo 'numero:'.$numero."<br>";
        // echo 'cod_personal:'.$cod_personal."<br>";
        // echo 'monto:'.$monto."<br>";
        // echo 'observaciones:'.$observaciones."<br>";
        // echo 'cod_area:'.$cod_area."<br>";
        // echo 'nro_recibo:'.$nro_recibo."<br>";

        $stmtCCD = $dbh->prepare("UPDATE caja_chicareembolsos set fecha='$fecha',monto=$monto,observaciones='$observaciones',cod_comprobante='$cod_comprobante',cod_comprobante_detalle='$cod_comprobante_detalle'
         where codigo = $codigo");
        $flagSuccess=$stmtCCD->execute();
        if($flagSuccess){
             //acctualiazmos reembolso
            $stmtReembolso = $dbh->prepare("UPDATE caja_chica set monto_reembolso=$monto_saldo,monto_reembolso_nuevo=$monto where codigo=$cod_cc");
            $flagSuccess=$stmtReembolso->execute();
        }
        showAlertSuccessError($flagSuccess,$urlListDetalleCajaChica.'&codigo='.$cod_cc.'&cod_tcc='.$cod_tcc);
        

    }
    
    } catch(PDOException $ex){
        //manejar error
        echo "Un error ocurrio".$ex->getMessage();
    }
?>