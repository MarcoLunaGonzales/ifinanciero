<?php

//require_once '../layouts/bodylogin.php';
require_once 'conexion.php';
require_once 'functions.php';
require_once 'configModule.php';
ini_set('display_errors',1);

$dbh = new Conexion();

$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);//para mostrar errores en la ejecucion

try {
    $globalUser=$_SESSION["globalUser"];

    $codigo = $_POST["codigo"];
    $cod_tcc = $_POST["cod_tcc"];
    $cod_cc = $_POST["cod_cc"];

    $cod_cuenta=$_POST["cuenta_auto_id"];    
    $cod_comprobante=$_POST["comprobante"];
    $cuenta_auxiliar1=$_POST["cuenta_auxiliar1"];
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

        // echo 'cod_cuenta:'.$cod_cuenta."<br>";
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
            //insertamos estado_de_cuentas y comprobantes
            if($cod_comprobante>0){
                //sacamos informacion del comprobante detalle
                $stmtCompDet = $dbh->prepare("SELECT codigo,cod_comprobante From comprobantes_detalle where codigo=$cod_comprobante");                
                $stmtCompDet->execute();
                $resultCompDet = $stmtCompDet->fetch();                
                $codigo_compr_x = $resultCompDet['codigo'];    
                $cod_comprobante_x = $resultCompDet['cod_comprobante'];    

                //sacamos informacion del comprobante
                $stmtComprob = $dbh->prepare("SELECT cod_gestion,cod_tipocomprobante,numero,glosa from comprobantes where codigo=$cod_comprobante_x");                
                $stmtComprob->execute();
                $resultComprob = $stmtComprob->fetch();                
                $cod_gestion_x = $resultComprob['cod_gestion'];                
                $cod_tipocomprobante_x = $resultComprob['cod_tipocomprobante'];
                $numero_x = $resultComprob['numero'];    
                // $glosa_x = $resultComprob['glosa'];
                // insertamos comprobante
                $codComprobante=obtenerCodigoComprobante();
                $sqlInsert="INSERT INTO comprobantes(codigo, cod_empresa, cod_unidadorganizacional, cod_gestion, cod_moneda, cod_estadocomprobante, cod_tipocomprobante, fecha, numero, glosa, created_at, created_by, modified_at, modified_by) VALUES ('$codComprobante', '1', '$cod_uo', '$cod_gestion_x', '1', '1', '$cod_tipocomprobante_x', '$fecha', '$numero_x', '$observaciones', '$fecha', '$globalUser', '$fecha', '$globalUser')";
                // echo $sqlInsert;
                $stmtInsertCompro = $dbh->prepare($sqlInsert);
                $flagSuccess=$stmtInsertCompro->execute();
                if($flagSuccess){
                    $sqlDelete="DELETE from comprobantes_detalle where cod_comprobante='$codComprobante'";
                    $stmtDel = $dbh->prepare($sqlDelete);
                    $flagSuccess=$stmtDel->execute();

                    $codComprobanteDetalle1=obtenerCodigoComprobanteDetalle();
                    $sqlDetalle1="INSERT INTO comprobantes_detalle (codigo,cod_comprobante, cod_cuenta, cod_cuentaauxiliar, cod_unidadorganizacional, cod_area, debe, haber, glosa, orden) VALUES ('$codComprobanteDetalle1','$codComprobante', '$cod_cuenta', '$cuenta_auxiliar1', '$cod_uo', '$cod_area', '0', '$monto', '$observaciones', '1')";
                    $stmtDetalle2 = $dbh->prepare($sqlDetalle1);
                    $flagSuccessDetalle=$stmtDetalle2->execute();    
                    $codComprobanteDetalle2=obtenerCodigoComprobanteDetalle();
                    $sqlDetalle2="INSERT INTO comprobantes_detalle (codigo,cod_comprobante, cod_cuenta, cod_cuentaauxiliar, cod_unidadorganizacional, cod_area, debe, haber, glosa, orden) VALUES ('$codComprobanteDetalle2','$codComprobante', '$cod_cuenta', '$cuenta_auxiliar1', '$cod_uo', '$cod_area', '$monto', '0', '$observaciones', '2')";
                    $stmtDetalle2 = $dbh->prepare($sqlDetalle2);
                    $flagSuccessDetalle=$stmtDetalle2->execute(); 
                    //insertamos estados de cuenta
                    $stmtContraCuenta = $dbh->prepare("INSERT INTO estados_cuenta(cod_comprobantedetalle,cod_plancuenta,monto,cod_proveedor,fecha,cod_comprobantedetalleorigen,cod_cuentaaux,cod_cajachicadetalle)  
                    values ('$codComprobanteDetalle1','$cod_cuenta','$monto','$cod_proveedores','$fecha',0,'$cuenta_auxiliar1','$codigo')");
                    $flagSuccess=$stmtContraCuenta->execute();
                } 
            }        
        }
        showAlertSuccessError($flagSuccess,$urlListDetalleCajaChica.'&codigo='.$cod_cc.'&cod_tcc='.$cod_tcc);
    
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
            
            // if($cod_comprobante>0){
            //     $stmtDeleteContraCuenta = $dbh->prepare("DELETE from estados_cuenta where cod_cajachicadetalle = $codigo");
            //     $flagSuccess=$stmtDeleteContraCuenta->execute();
            //     //insertamos estado_de_cuentas para la contra cuenta.
            //     $stmtContraCuenta = $dbh->prepare("INSERT INTO estados_cuenta(cod_comprobantedetalle,cod_plancuenta,monto,cod_proveedor,fecha,cod_comprobantedetalleorigen,cod_cuentaaux,cod_cajachicadetalle)  
            //     values ('$cod_comprobante','$cod_cuenta','$monto','$cod_proveedores','$fecha',0,'$cuenta_auxiliar1','$codigo')");
            //     $flagSuccess=$stmtContraCuenta->execute();
            // }
            //insertamos estado_de_cuentas y comprobantes
            if($cod_comprobante>0){
                //sacamos informacion del comprobante detalle
                $stmtCompDet = $dbh->prepare("SELECT codigo,cod_comprobante From comprobantes_detalle where codigo=$cod_comprobante");                
                $stmtCompDet->execute();
                $resultCompDet = $stmtCompDet->fetch();                
                $codigo_compr_x = $resultCompDet['codigo'];    
                $cod_comprobante_x = $resultCompDet['cod_comprobante'];    
                $cod_comprobante_x2=$cod_comprobante_x+1;
                // //sacamos informacion del comprobante
                // $stmtComprob = $dbh->prepare("SELECT cod_gestion,cod_tipocomprobante,numero,glosa from comprobantes where codigo=$cod_comprobante_x");                
                // $stmtComprob->execute();
                // $resultComprob = $stmtComprob->fetch();                
                // $cod_gestion_x = $resultComprob['cod_gestion'];                
                // $cod_tipocomprobante_x = $resultComprob['cod_tipocomprobante'];
                // $numero_x = $resultComprob['numero'];    
                // $glosa_x = $resultComprob['glosa'];
                // insertamos comprobante
                // $codComprobante=obtenerCodigoComprobante();
                $sqlInsert="UPDATE comprobantes set cod_unidadorganizacional='$cod_uo',glosa='$observaciones' where codigo=$cod_comprobante_x";
                $stmtInsertCompro = $dbh->prepare($sqlInsert);
                $flagSuccess=$stmtInsertCompro->execute();
                if($flagSuccess){                    
                    $sqlDetalle1="UPDATE comprobantes_detalle set cod_cuenta='$cod_cuenta',cod_cuentaauxiliar='$cuenta_auxiliar1',cod_unidadorganizacional='$cod_uo',cod_area='$cod_area',haber='$monto',glosa='$observaciones' where codigo=$cod_comprobante_x";                    
                    $stmtDetalle2 = $dbh->prepare($sqlDetalle1);
                    $flagSuccessDetalle=$stmtDetalle2->execute();    

                    $sqlDetalle2="UPDATE comprobantes_detalle set cod_cuenta='$cod_cuenta',cod_cuentaauxiliar='$cuenta_auxiliar1',cod_unidadorganizacional='$cod_uo',cod_area='$cod_area',debe='$monto',glosa='$observaciones' where codigo=$cod_comprobante_x2";
                    $stmtDetalle2 = $dbh->prepare($sqlDetalle2);
                    $flagSuccessDetalle=$stmtDetalle2->execute();
                    ////borramos los estados de cuenta registrados
                    $stmtDeleteContraCuenta = $dbh->prepare("DELETE from estados_cuenta where cod_cajachicadetalle = $codigo");
                    $flagSuccess=$stmtDeleteContraCuenta->execute();
                    //insertamos estados de cuenta
                    $stmtContraCuenta = $dbh->prepare("INSERT INTO estados_cuenta(cod_comprobantedetalle,cod_plancuenta,monto,cod_proveedor,fecha,cod_comprobantedetalleorigen,cod_cuentaaux,cod_cajachicadetalle)  
                    values ('$codComprobanteDetalle1','$cod_cuenta','$monto','$cod_proveedores','$fecha',0,'$cuenta_auxiliar1','$codigo')");
                    $flagSuccess=$stmtContraCuenta->execute();
                } 
            } 
        }
        showAlertSuccessError($flagSuccess,$urlListDetalleCajaChica.'&codigo='.$cod_cc.'&cod_tcc='.$cod_tcc);
        

    }//si es insert o update
    
    } catch(PDOException $ex){
        //manejar error
        echo "Un error ocurrio".$ex->getMessage();
    }
?>