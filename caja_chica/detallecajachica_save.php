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
    $cod_contra_cuenta=$_POST["contra_cuenta"];
    $cod_contra_cuenta_aux=$_POST["contra_cuenta_auxiliar"];
    // $cod_cuenta = $_POST["cod_cuenta"];
    $cod_retencion = $_POST["tipo_retencion"];
    $numero = $_POST["numero"];
    $monto = $_POST["monto"];
    $fecha = $_POST["fecha"];
    $cod_personal = $_POST["cod_personal"];
    $observaciones = $_POST["observaciones"];

    $cod_uo = $_POST["cod_uo"];
    $cod_area = $_POST["cod_area"];
    $nro_recibo = $_POST["nro_recibo"];
    $cod_proveedores = $_POST["proveedores"];
    $cod_actividad_sw = $_POST["cod_actividad"];
    if($cod_area=='' || $cod_area==0)$cod_area=null;
    if($cod_uo=='' || $cod_uo==0)$cod_uo=null;
    if($cod_proveedores=='' || $cod_proveedores==0)$cod_proveedores=null;
    if($cod_personal=='' || $cod_personal==0)$cod_personal=null;
    if($cod_actividad_sw=='' || $cod_actividad_sw==0)$cod_actividad_sw=null;

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
        
        // if($cod_retencion==5){
        //     $cod_estado=2;
        // }else $cod_estado=1;
        $cod_estado=1;
        $cod_estadoreferencial=1;
        $monto_rendicion=0;

        //     echo 'cod_cuenta:'.$cod_cuenta."<br>";
        // echo 'fecha:'.$fecha."<br>";
        // echo 'cod_retencion:'.$cod_retencion."<br>";
        // echo 'numero:'.$numero."<br>";
        // echo 'cod_personal:'.$cod_personal."<br>";
        // echo 'cod_area:'.$cod_area."<br>";
        // echo 'cod_uo:'.$cod_uo."<br>";
        // echo 'monto:'.$monto."<br>";
        // echo 'observaciones:'.$observaciones."<br>";
        // echo 'cod_area:'.$cod_area."<br>";
        // echo 'nro_recibo:'.$nro_recibo."<br>";


        $stmt = $dbh->prepare("INSERT INTO caja_chicadetalle(codigo,cod_cajachica,cod_cuenta,fecha,cod_tipodoccajachica,nro_documento,cod_personal,monto,observaciones,cod_estado,cod_estadoreferencial,cod_area,cod_uo,nro_recibo,cod_proveedores,cod_actividad_sw) 
        values ($codigo,$cod_cc,$cod_cuenta,'$fecha',$cod_retencion,$numero,'$cod_personal',$monto,'$observaciones',$cod_estado,$cod_estadoreferencial,'$cod_area','$cod_uo',$nro_recibo,'$cod_proveedores','$cod_actividad_sw')");
        $flagSuccess=$stmt->execute();
        if($flagSuccess){//registramos rendiciones
            $stmtReembolso = $dbh->prepare("UPDATE caja_chica set monto_reembolso=$monto_reembolso where codigo=$cod_cc");
            $stmtReembolso->execute();

            $stmtrendiciones = $dbh->prepare("INSERT INTO rendiciones(codigo,numero,cod_tipodoc,monto_a_rendir,monto_rendicion,cod_personal,observaciones,cod_estado,cod_cajachicadetalle,cod_estadoreferencial,fecha_dcc) 
            values ($codigo,$numero,$cod_retencion,$monto,$monto_rendicion,'$cod_personal','$observaciones',$cod_estado,$codigo,$cod_estadoreferencial,'$fecha')");
            $flagSuccess=$stmtrendiciones->execute();
            //insertamos estado_de_cuentas para la contra cuenta.
            if($cod_contra_cuenta>0){
                $stmtContraCuenta = $dbh->prepare("INSERT INTO estados_cuenta(cod_comprobantedetalle,cod_plancuenta,monto,cod_proveedor,fecha,cod_comprobantedetalleorigen,cod_cuentaaux,cod_cajachicadetalle)  
                values (0,'$cod_contra_cuenta','$monto',0,'$fecha',0,'$cod_contra_cuenta_aux','$codigo')");
                $flagSuccess=$stmtContraCuenta->execute();
            }
            

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
        // echo 'cod_retencion:'.$cod_retencion."<br>";
        // echo 'numero:'.$numero."<br>";
        // echo 'cod_personal:'.$cod_personal."<br>";
        // echo 'monto:'.$monto."<br>";
        // echo 'observaciones:'.$observaciones."<br>";
        // echo 'cod_uo:'.$cod_uo."<br>";
        // echo 'cod_area:'.$cod_area."<br>";
        // echo 'nro_recibo:'.$nro_recibo."<br>";
        // echo 'cod_contra_cuenta:'.$cod_contra_cuenta."<br>";
        

        $stmtCCD = $dbh->prepare("UPDATE caja_chicadetalle set cod_cuenta='$cod_cuenta',fecha='$fecha',cod_tipodoccajachica='$cod_retencion',nro_documento='$numero',cod_personal='$cod_personal',monto='$monto',observaciones='$observaciones',cod_area='$cod_area',cod_uo='$cod_uo',nro_recibo='$nro_recibo',cod_proveedores='$cod_proveedores',cod_actividad_sw='$cod_actividad_sw'
         where codigo = $codigo");      
        $flagSuccess=$stmtCCD->execute();        
        
        if($flagSuccess){
             //acctualiazmos reembolso
            $stmtReembolso = $dbh->prepare("UPDATE caja_chica set monto_reembolso=$monto_reembolso where codigo=$cod_cc");
            $stmtReembolso->execute();
            
            $stmtrendiciones = $dbh->prepare("UPDATE rendiciones set cod_tipodoc='$cod_retencion',monto_a_rendir='$monto',monto_rendicion='$monto_rendicion',cod_personal='$cod_personal',observaciones='$observaciones',fecha_dcc='$fecha'
            where codigo = $codigo");   
            $flagSuccess=$stmtrendiciones->execute();

            //para la parte de la contra cuenta
            //borramos la contra cuenta registrada
            if($cod_contra_cuenta>0){
                $stmtDeleteContraCuenta = $dbh->prepare("DELETE from estados_cuenta where cod_cajachicadetalle = $codigo");
                $flagSuccess=$stmtDeleteContraCuenta->execute();
                //insertamos estado_de_cuentas para la contra cuenta.
                $stmtContraCuenta = $dbh->prepare("INSERT INTO estados_cuenta(cod_comprobantedetalle,cod_plancuenta,monto,cod_proveedor,fecha,cod_comprobantedetalleorigen,cod_cuentaaux,cod_cajachicadetalle)  
                values (0,'$cod_contra_cuenta','$monto',0,'$fecha',0,'$cod_contra_cuenta_aux','$codigo')");
                $flagSuccess=$stmtContraCuenta->execute();
            }
            
        }
        showAlertSuccessError($flagSuccess,$urlListDetalleCajaChica.'&codigo='.$cod_cc.'&cod_tcc='.$cod_tcc);
        

    }//si es insert o update
    
    } catch(PDOException $ex){
        //manejar error
        echo "Un error ocurrio".$ex->getMessage();
    }
?>