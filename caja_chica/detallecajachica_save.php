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

    $cod_cuenta=trim($_POST["cuenta_auto_id"]);    
    $cod_comprobante=trim($_POST["comprobante"]);
    $cuenta_auxiliar1=trim($_POST["cuenta_auxiliar1"]);
    $cod_retencion = trim($_POST["tipo_retencion"]);
    $numero = trim($_POST["numero"]);
    $monto = trim($_POST["monto"]);
    $fecha = trim($_POST["fecha"]);
    $cod_personal = trim($_POST["cod_personal"]);
    $observaciones = trim($_POST["observaciones"]);

    $cod_uo = trim($_POST["cod_uo"]);
    $cod_area = trim($_POST["cod_area"]);
    $nro_recibo = trim($_POST["nro_recibo"]);
    $cod_proveedores = trim($_POST["proveedores"]);
    if(isset($_POST["cod_actividad"]))$cod_actividad_sw = $_POST["cod_actividad"];
    else $cod_actividad_sw=0;
    
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
        $patron = "/[^a-zA-Z0-9]+/";
        $observaciones = preg_replace($patron, "", $observaciones);        

        $stmt = $dbh->prepare("INSERT INTO caja_chicadetalle(codigo,cod_cajachica,cod_cuenta,fecha,cod_tipodoccajachica,nro_documento,cod_personal,monto,observaciones,cod_estado,cod_estadoreferencial,cod_area,cod_uo,nro_recibo,cod_proveedores,cod_actividad_sw) 
        values ($codigo,$cod_cc,$cod_cuenta,'$fecha',$cod_retencion,$numero,'$cod_personal',$monto,'$observaciones',$cod_estado,$cod_estadoreferencial,'$cod_area','$cod_uo',$nro_recibo,'$cod_proveedores','$cod_actividad_sw')");
        $flagSuccess=$stmt->execute();
        if($flagSuccess){//registramos rendiciones
            $stmtReembolso = $dbh->prepare("UPDATE caja_chica set monto_reembolso=$monto_reembolso where codigo=$cod_cc");
            $stmtReembolso->execute();
            $stmtrendiciones = $dbh->prepare("INSERT INTO rendiciones(codigo,numero,cod_tipodoc,monto_a_rendir,monto_rendicion,cod_personal,observaciones,cod_estado,cod_cajachicadetalle,cod_estadoreferencial,fecha_dcc) values ($codigo,$numero,$cod_retencion,$monto,$monto_rendicion,'$cod_personal','$observaciones',$cod_estado,$codigo,$cod_estadoreferencial,'$fecha')");
            $flagSuccess=$stmtrendiciones->execute();
            //insertamos estado_de_cuentas y comprobantes
            if($cod_comprobante>0){                
                $stmtContraCuenta = $dbh->prepare("INSERT INTO estados_cuenta(cod_comprobantedetalle,cod_plancuenta,monto,cod_proveedor,fecha,cod_comprobantedetalleorigen,cod_cuentaaux,cod_cajachicadetalle)values('0','$cod_cuenta','$monto','$cod_proveedores','$fecha','$cod_comprobante','$cuenta_auxiliar1','$codigo')");
                $flagSuccess=$stmtContraCuenta->execute();
                if($flagSuccess){
                    //busacmos el codigo de estado de cuenta
                    $sqlEstadoCuenta="SELECT e.codigo From estados_cuenta e where e.cod_comprobantedetalle=$codigo_comprobante limit 1"; 
                    $stmtEstadoCuenta = $dbh->prepare($sqlEstadoCuenta);
                    $stmtEstadoCuenta->execute();                    
                    $resultado=$stmtEstadoCuenta->fetch();
                    $codigo_estadoCuenta=$resultado['codigo'];
                    $codigo_sr=0;
                    $sqlDetalleX="SELECT codigo,cod_solicitudrecurso,cod_solicitudrecursodetalle,cod_proveedor,cod_tipopagoproveedor from solicitud_recursosdetalle where cod_estadocuenta=$codigo_estadoCuenta";
                    $stmtDetalleX = $dbh->prepare($sqlDetalleX);
                    $stmtDetalleX->execute();                    
                    $stmtDetalleX->bindColumn('codigo', $codigo_sr);
                    $stmtDetalleX->bindColumn('cod_solicitudrecurso', $cod_solicitudrecurso_sr);
                    $stmtDetalleX->bindColumn('cod_solicitudrecursodetalle', $cod_solicitudrecursodetalle_sr);
                    $stmtDetalleX->bindColumn('cod_proveedor', $cod_proveedor_sr);
                    $stmtDetalleX->bindColumn('cod_tipopagoproveedor', $cod_tipopagoproveedor_sr);
                    while ($rowDetalleX = $stmtDetalleX->fetch(PDO::FETCH_BOUND)){ 

                    }
                    if($codigo_sr>0){
                        $cod_pagoproveedor=obtenerCodigoPagoProveedor();
                        $sqlInsert="INSERT INTO pagos_proveedores (codigo, fecha,observaciones,cod_comprobante,cod_estadopago,cod_ebisa,cod_cajachicadetalle) 
                        VALUES ('".$cod_pagoproveedor."','".$fecha."','".$observaciones."','0',3,0,'$codigo')";
                        $stmtInsert = $dbh->prepare($sqlInsert);
                        $stmtInsert->execute();
                        $cod_pagoproveedordetalle=obtenerCodigoPagoProveedorDetalle();
                        $sqlInsert2="INSERT INTO pagos_proveedoresdetalle (codigo,cod_pagoproveedor,cod_proveedor,cod_solicitudrecursos,cod_solicitudrecursosdetalle,cod_tipopagoproveedor,monto,observaciones,fecha) 
                         VALUES ('".$cod_pagoproveedordetalle."','".$cod_pagoproveedor."','".$cod_proveedor_sr."','".$cod_solicitudrecurso_sr."','".$codigo_sr."','".$cod_tipopagoproveedor_sr."','".$monto."','".$observaciones."','".$fecha."')";
                        $stmtInsert2 = $dbh->prepare($sqlInsert2);
                        $flagSuccess=$stmtInsert2->execute();
                    }
                }                
            }
            //Proceso de la distribucion
            $sqlDel="DELETE FROM distribucion_gastos_caja_chica where cod_cajachica_detalle=$codigo";
            $stmtDel = $dbh->prepare($sqlDel);
            $stmtDel->execute();
            $valorDist=$_POST['n_distribucion'];
            if($valorDist!=0){
                $array1=json_decode($_POST['d_oficinas']);
                $array2=json_decode($_POST['d_areas']);
                if($valorDist==1){
                    guardarDatosDistribucion($array1,0,$codigo); //dist x Oficina
                }else{
                    if($valorDist==2){
                      guardarDatosDistribucion(0,$array2,$codigo); //dist x Area
                    }else{
                      guardarDatosDistribucion($array1,$array2,$codigo); //dist x Oficina y Area
                    }
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
            //insertamos estado_de_cuentas y comprobantes
            if($cod_comprobante>0){                
                $stmtContraCuenta = $dbh->prepare("UPDATE estados_cuenta set cod_plancuenta='$cod_cuenta',monto='$monto',cod_proveedor='$cod_proveedores',fecha='$fecha',cod_cuentaaux='$cuenta_auxiliar1' where cod_cajachicadetalle=$codigo ");
                $flagSuccess=$stmtContraCuenta->execute();
            }
            //Proceso de la distribucion
            $sqlDel="DELETE FROM distribucion_gastos_caja_chica where cod_cajachica_detalle=$codigo";
            $stmtDel = $dbh->prepare($sqlDel);
            $stmtDel->execute();
            $valorDist=$_POST['n_distribucion'];
            if($valorDist!=0){
                $array1=json_decode($_POST['d_oficinas']);
                $array2=json_decode($_POST['d_areas']);
                if($valorDist==1){
                    guardarDatosDistribucion($array1,0,$codigo); //dist x Oficina
                }else{
                    if($valorDist==2){
                      guardarDatosDistribucion(0,$array2,$codigo); //dist x Area
                    }else{
                      guardarDatosDistribucion($array1,$array2,$codigo); //dist x Oficina y Area
                    }
                }   
            }
        }
        showAlertSuccessError($flagSuccess,$urlListDetalleCajaChica.'&codigo='.$cod_cc.'&cod_tcc='.$cod_tcc);        
    }//si es insert o update
} catch(PDOException $ex){
    //manejar error
    echo "Un error ocurrio".$ex->getMessage();
}

function guardarDatosDistribucion($array1,$array2,$codigo_cajachica_det){
    $dbh = new Conexion();
    if($array1!=0){
      for ($i=0; $i < count($array1); $i++) { 
        $unidad=$array1[$i]->unidad;
        $porcentaje=$array1[$i]->porcentaje;
        $sqlInsert="INSERT INTO distribucion_gastos_caja_chica (tipo_distribucion,oficina_area,porcentaje,cod_cajachica_detalle) 
        VALUES ('1','$unidad','$porcentaje','$codigo_cajachica_det')";
        $stmtInsert = $dbh->prepare($sqlInsert);
        $stmtInsert->execute();
      }   
    }
    if($array2!=0){
        for ($i=0; $i < count($array2); $i++) { 
            $area=$array2[$i]->area;
            $porcentaje=$array2[$i]->porcentaje;
            $sqlInsert="INSERT INTO distribucion_gastos_caja_chica (tipo_distribucion,oficina_area,porcentaje,cod_cajachica_detalle) 
            VALUES ('2','$area','$porcentaje','$codigo_cajachica_det')";
            $stmtInsert = $dbh->prepare($sqlInsert);
            $stmtInsert->execute();
        }
    } 
}
?>