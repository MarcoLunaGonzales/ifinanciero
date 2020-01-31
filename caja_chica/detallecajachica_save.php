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
    $cod_tcc = $_POST["cod_tcc"];
    $cod_cc = $_POST["cod_cc"];

    $cod_cuenta = $_POST["cod_cuenta"];
    $cod_tipo_documento = $_POST["tipo_documento"];
    $numero = $_POST["numero"];
    $monto = $_POST["monto"];
    $fecha = $_POST["fecha"];
    $cod_personal = $_POST["cod_personal"];
    $observaciones = $_POST["observaciones"];    
    
    if ($codigo == 0){//insertamos

        //para el codigo del detalle
        $stmtCC = $dbh->prepare("SELECT codigo from caja_chicadetalle where cod_estadoreferencial=1 order by codigo desc");
        $stmtCC->execute();
        $resultCC = $stmtCC->fetch();
        $codigo_caja_chica_aux = $resultCC['codigo'];
        if($codigo_caja_chica_aux==null){
            $codigo_caja_chica_aux=0;
        }
        $codigo=$codigo_caja_chica_aux+1;
        $cod_estado=1;        
        $cod_estadoreferencial=1;
        $monto_rendicion=0;
        $stmt = $dbh->prepare("INSERT INTO caja_chicadetalle(codigo,cod_cajachica,cod_cuenta,fecha,cod_tipodoccajachica,nro_documento,cod_personal,monto,observaciones,cod_estado,cod_estadoreferencial) 
        values ($codigo,$cod_cc,$cod_cuenta,'$fecha',$cod_tipo_documento,$numero,$cod_personal,$monto,'$observaciones',$cod_estado,$cod_estadoreferencial)");
        $flagSuccess=$stmt->execute();
        if($flagSuccess){//registramos rendiciones
            $stmtrendiciones = $dbh->prepare("INSERT INTO rendiciones(codigo,numero,cod_tipodoc,monto_a_rendir,monto_rendicion,cod_personal,observaciones,cod_estado,cod_cajachicadetalle,cod_estadoreferencial) 
            values ($codigo,$numero,$cod_tipo_documento,$monto,$monto_rendicion,$cod_personal,'$observaciones',$cod_estado,$codigo,$cod_estadoreferencial)");
            $flagSuccess=$stmtrendiciones->execute();
        }
        showAlertSuccessError($flagSuccess,$urlListDetalleCajaChica.'&codigo='.$cod_cc.'&cod_tcc='.$cod_tcc);

        //$stmt->debugDumpParams();
    } else {//update
        $monto_rendicion=0;
        $stmt = $dbh->prepare("UPDATE caja_chicadetalle set cod_cuenta=$cod_cuenta,fecha='$fecha',cod_tipodoccajachica=$cod_tipo_documento,nro_documento=$numero,cod_personal=$cod_personal,monto=$monto,observaciones='$observaciones'
         where codigo = $codigo");      
        $flagSuccess=$stmt->execute();        
        if($flagSuccess){
            $stmtrendiciones = $dbh->prepare("UPDATE rendiciones set cod_tipodoc=$cod_tipo_documento,monto_a_rendir=$monto,monto_rendicion=$monto_rendicion,cod_personal=$cod_personal,observaciones='$observaciones'
            where codigo = $codigo");   
            $flagSuccess=$stmtrendiciones->execute(); 
        }
        showAlertSuccessError($flagSuccess,$urlListDetalleCajaChica.'&codigo='.$cod_cc.'&cod_tcc='.$cod_tcc);

    }//si es insert o update
    
    } catch(PDOException $ex){
        //manejar error
        echo "Un error ocurrio".$ex->getMessage();
    }
?>