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

    $cod_cuenta=$_POST["cuenta_auto_id"];    
    // $cod_cuenta = $_POST["cod_cuenta"];
    $cod_tipo_documento = $_POST["tipo_documento"];
    $numero = $_POST["numero"];
    $monto = $_POST["monto"];
    $fecha = $_POST["fecha"];
    $cod_personal = $_POST["cod_personal"];
    $observaciones = $_POST["observaciones"];

    $cod_uo = $_POST["cod_uo"];
    $cod_area = $_POST["cod_area"];
    $nro_recibo = $_POST["nro_recibo"];
    if($cod_area=='')$cod_area=0;
    if($cod_uo=='')$cod_uo=0;
    

    

    //sacamos monto de caja chica
    $stmtMCC = $dbh->prepare("SELECT monto_reembolso from caja_chica where  codigo =$cod_cc");
    $stmtMCC->execute();
    $resultMCC=$stmtMCC->fetch();
    $monto_reembolso_x=$resultMCC['monto_reembolso'];

    if ($codigo == 0){//insertamos
        $monto_reembolso=$monto_reembolso_x-$monto;
        

        //para el codigo del detalle
        $stmtCC = $dbh->prepare("SELECT codigo from caja_chicadetalle order by codigo desc LIMIT 1");
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
        $stmt = $dbh->prepare("INSERT INTO caja_chicadetalle(codigo,cod_cajachica,cod_cuenta,fecha,cod_tipodoccajachica,nro_documento,cod_personal,monto,observaciones,cod_estado,cod_estadoreferencial,cod_area,cod_uo,nro_recibo) 
        values ($codigo,$cod_cc,$cod_cuenta,'$fecha',$cod_tipo_documento,$numero,$cod_personal,$monto,'$observaciones',$cod_estado,$cod_estadoreferencial,$cod_area,$cod_uo,$nro_recibo)");
        $flagSuccess=$stmt->execute();
        if($flagSuccess){//registramos rendiciones
            $stmtReembolso = $dbh->prepare("UPDATE caja_chica set monto_reembolso=$monto_reembolso where codigo=$cod_cc");
            $stmtReembolso->execute();

            $stmtrendiciones = $dbh->prepare("INSERT INTO rendiciones(codigo,numero,cod_tipodoc,monto_a_rendir,monto_rendicion,cod_personal,observaciones,cod_estado,cod_cajachicadetalle,cod_estadoreferencial,fecha_dcc) 
            values ($codigo,$numero,$cod_tipo_documento,$monto,$monto_rendicion,$cod_personal,'$observaciones',$cod_estado,$codigo,$cod_estadoreferencial,'$fecha')");
            $flagSuccess=$stmtrendiciones->execute();
        }
        showAlertSuccessError($flagSuccess,$urlListDetalleCajaChica.'&codigo='.$cod_cc.'&cod_tcc='.$cod_tcc);

        //$stmt->debugDumpParams();
    } else {//update
        //actualizamos monto reeembolso
        //sacamos monto anterior de detalle
        $stmtMontoAnterior = $dbh->prepare("SELECT monto from caja_chicadetalle where codigo=$codigo");
        $stmtMontoAnterior->execute();
        $resultMontoAnterior = $stmtMontoAnterior->fetch();
        $monto_anterior = $resultMontoAnterior['monto'];
        
        $monto_reembolso=$monto_reembolso_x+$monto_anterior-$monto;
       
        //================================================================
        $monto_rendicion=0;

        // echo 'cod_cuenta:'.$cod_cuenta."<br>";
        // echo 'fecha:'.$fecha."<br>";
        // echo 'cod_tipo_documento:'.$cod_tipo_documento."<br>";
        // echo 'numero:'.$numero."<br>";
        // echo 'cod_personal:'.$cod_personal."<br>";
        // echo 'monto:'.$monto."<br>";
        // echo 'observaciones:'.$observaciones."<br>";
        // echo 'cod_area:'.$cod_area."<br>";
        // echo 'nro_recibo:'.$nro_recibo."<br>";

        $stmtCCD = $dbh->prepare("UPDATE caja_chicadetalle set cod_cuenta=$cod_cuenta,fecha='$fecha',cod_tipodoccajachica=$cod_tipo_documento,nro_documento=$numero,cod_personal=$cod_personal,monto=$monto,observaciones='$observaciones',cod_area=$cod_area,cod_uo=$cod_uo,nro_recibo=$nro_recibo
         where codigo = $codigo");      
        $flagSuccess=$stmtCCD->execute();        
        
        if($flagSuccess){
             //acctualiazmos reembolso
            $stmtReembolso = $dbh->prepare("UPDATE caja_chica set monto_reembolso=$monto_reembolso where codigo=$cod_cc");
            $stmtReembolso->execute();
            
            $stmtrendiciones = $dbh->prepare("UPDATE rendiciones set cod_tipodoc=$cod_tipo_documento,monto_a_rendir=$monto,monto_rendicion=$monto_rendicion,cod_personal=$cod_personal,observaciones='$observaciones',fecha_dcc='$fecha'
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