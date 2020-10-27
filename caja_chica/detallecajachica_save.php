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
  if(!isset($_POST["sr"])){

    $cod_cuenta=trim($_POST["cuenta_auto_id"]);    
    $cod_comprobante_ec=trim($_POST["comprobante"]);
    $cuenta_auxiliar1=trim($_POST["cuenta_auxiliar1"]);
    $cod_retencion = trim($_POST["tipo_retencion"]);
    $cod_tipopago = trim($_POST["tipo_pago"]);
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

    }//fin if isset sr

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
        $observaciones = str_replace("'","",$observaciones);

        $stmt = $dbh->prepare("INSERT INTO caja_chicadetalle(codigo,cod_cajachica,cod_cuenta,fecha,cod_tipodoccajachica,nro_documento,cod_personal,monto,observaciones,cod_estado,cod_estadoreferencial,cod_area,cod_uo,nro_recibo,cod_proveedores,cod_actividad_sw,created_at,created_by,cod_tipopago) 
        values ($codigo,$cod_cc,$cod_cuenta,'$fecha',$cod_retencion,$numero,'$cod_personal',$monto,'$observaciones',$cod_estado,$cod_estadoreferencial,'$cod_area','$cod_uo',$nro_recibo,'$cod_proveedores','$cod_actividad_sw',NOW(),$globalUser,$cod_tipopago)");
        $flagSuccess=$stmt->execute();
        if($flagSuccess){//registramos rendiciones
            $stmtReembolso = $dbh->prepare("UPDATE caja_chica set monto_reembolso=$monto_reembolso where codigo=$cod_cc");
            $stmtReembolso->execute();
            $stmtrendiciones = $dbh->prepare("INSERT INTO rendiciones(codigo,numero,cod_tipodoc,monto_a_rendir,monto_rendicion,cod_personal,observaciones,cod_estado,cod_cajachicadetalle,cod_estadoreferencial,fecha_dcc) values ($codigo,$numero,$cod_retencion,$monto,$monto_rendicion,'$cod_personal','$observaciones',$cod_estado,$codigo,$cod_estadoreferencial,'$fecha')");
            $flagSuccess=$stmtrendiciones->execute();
            //insertamos estado_de_cuentas y comprobantes
            if($cod_comprobante_ec>0){//llega el cod de estado de cuenta                     
                //sacamos las cuentas auxiliares
                $nomProveedor=nameProveedor($cod_proveedores);
                //CREAR CUENTA AUXILIAR SI NO EXISTE 
                if(obtenerCodigoCuentaAuxiliarProveedorClienteCuenta(1,$cod_proveedores,$cod_cuenta)==0){
                    $codEstado="1";
                    $stmtInsertAux = $dbh->prepare("INSERT INTO cuentas_auxiliares (nombre, cod_estadoreferencial, cod_cuenta,  cod_tipoauxiliar, cod_proveedorcliente) 
                    VALUES ('$nomProveedor', $codEstado,$cod_cuenta, 1, $cod_proveedores)");
                    $stmtInsertAux->execute();
                }
                $cuenta_auxiliar1=obtenerCodigoCuentaAuxiliarProveedorClienteCuenta(1,$cod_proveedores,$cod_cuenta); 
                $stmtContraCuenta = $dbh->prepare("INSERT INTO estados_cuenta(cod_comprobantedetalle,cod_plancuenta,monto,cod_proveedor,fecha,cod_comprobantedetalleorigen,cod_cuentaaux,cod_cajachicadetalle,glosa_auxiliar)values('0','$cod_cuenta','$monto','$cod_proveedores','$fecha','$cod_comprobante_ec','$cuenta_auxiliar1','$codigo','$observaciones')");
                $flagSuccess=$stmtContraCuenta->execute();
                if($flagSuccess){
                    $codigo_sr=0;                    
                    // $sqlDetalleX="SELECT codigo,cod_solicitudrecurso,cod_solicitudrecursodetalle,cod_proveedor,cod_tipopagoproveedor from solicitud_recursosdetalle where cod_estadocuenta=$cod_comprobante_ec";
                    $sqlDetalleX="SELECT sd.codigo,sd.cod_solicitudrecurso,sd.cod_proveedor,sd.cod_tipopagoproveedor 
                    FROM solicitud_recursos s,solicitud_recursosdetalle sd
                    WHERE s.codigo=sd.cod_solicitudrecurso and s.cod_comprobante in (select cd.cod_comprobante from estados_cuenta e,comprobantes_detalle cd where e.cod_comprobantedetalle=cd.codigo and e.codigo=$cod_comprobante_ec)";

                    $stmtDetalleX = $dbh->prepare($sqlDetalleX);
                    $stmtDetalleX->execute();                    
                    $stmtDetalleX->bindColumn('codigo', $codigo_sr);
                    $stmtDetalleX->bindColumn('cod_solicitudrecurso', $cod_solicitudrecurso_sr);
                    $stmtDetalleX->bindColumn('cod_proveedor', $cod_proveedor_sr);
                    $stmtDetalleX->bindColumn('cod_tipopagoproveedor', $cod_tipopagoproveedor_sr);
                    while ($rowDetalleX = $stmtDetalleX->fetch(PDO::FETCH_BOUND)){ 
                        $codigo_sr=$codigo_sr;
                        $cod_solicitudrecurso_sr=$cod_solicitudrecurso_sr;
                        $cod_proveedor_sr=$cod_proveedor_sr;
                        $cod_tipopagoproveedor_sr=$cod_tipopagoproveedor_sr;
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

                        $stmtCambioEstadoSR = $dbh->prepare("UPDATE solicitud_recursos set cod_estadosolicitudrecurso=9 where codigo=:codigo");
                        $stmtCambioEstadoSR->bindParam(':codigo', $cod_solicitudrecurso_sr);
                        $flagSuccess=$stmtCambioEstadoSR->execute();
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
                $array3=json_decode($_POST['d_areas_global']);
                $array4=json_decode($_POST['d_oficinas_global']);
                if($valorDist==1){
                    guardarDatosDistribucion($array1,0,$codigo); //dist x Oficina
                }else{
                    if($valorDist==2){
                      guardarDatosDistribucion(0,$array2,$codigo); //dist x Area
                    }else{
                        if($valorDist==3){
                           guardarDatosDistribucion($array1,$array2,$codigo); //dist x Oficina y Area
                        }else{
                            guardarDatosDistribucionGeneral($array3,$array4,$codigo); //dist area y Oficina 
                        }

                      
                    }
                }   
            }
        }

        //insertamos archivos adjuntos
        $nArchivosCabecera=$_POST["cantidad_archivosadjuntos"];
        for ($ar=1; $ar <= $nArchivosCabecera ; $ar++) { 
          if(isset($_POST['codigo_archivo'.$ar])){
            if($_FILES['documentos_cabecera'.$ar]["name"]){
              $filename = $_FILES['documentos_cabecera'.$ar]["name"]; //Obtenemos el nombre original del archivos
              $source = $_FILES['documentos_cabecera'.$ar]["tmp_name"]; //Obtenemos un nombre temporal del archivos    
              $directorio = 'assets/archivos-respaldo/archivos_cajachicadetalle/GASTO_CC-'.$codigo; //Declaramos una  variable con la ruta donde guardaremos los archivoss
              //Validamos si la ruta de destino existe, en caso de no existir la creamos
              if(!file_exists($directorio)){
                        mkdir($directorio, 0777,true) or die("No se puede crear el directorio de extracci&oacute;n");    
              }
              $target_path = $directorio.'/'.$filename; //Indicamos la ruta de destino, así como el nombre del archivos
              //Movemos y validamos que el archivos se haya cargado correctamente
              //El primer campo es el origen y el segundo el destino
              if(move_uploaded_file($source, $target_path)) { 
                echo "ok";
                $tipo=$_POST['codigo_archivo'.$ar];
                $descripcion=$_POST['nombre_archivo'.$ar];
                // $tipoPadre=2708;

                // $sqlInsert="INSERT INTO archivos_adjuntos_cajachica (cod_tipoarchivo,descripcion,direccion_archivo,cod_tipopadre,cod_padre,cod_objeto) 
                // VALUES ('$tipo','$descripcion','$target_path','$tipoPadre',0,'$codSolicitud')";
                $sqlInsert="INSERT INTO archivos_adjuntos_cajachica(cod_tipoarchivo,descripcion,direccion_archivo,cod_cajachica_detalle) 
                VALUES ('$tipo','$descripcion','$target_path','$codigo')";
                $stmtInsert = $dbh->prepare($sqlInsert);
                $stmtInsert->execute();    
                // print_r($sqlInsert);
              } else {    
                  echo "error";
              } 
            }
          }
        }


        showAlertSuccessError($flagSuccess,$urlListDetalleCajaChica.'&codigo='.$cod_cc.'&cod_tcc='.$cod_tcc);
    } else {//update
      if(!isset($_POST["sr"])){  
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
        

        $stmtCCD = $dbh->prepare("UPDATE caja_chicadetalle set cod_cuenta='$cod_cuenta',fecha='$fecha',cod_tipodoccajachica='$cod_retencion',nro_documento='$numero',cod_personal='$cod_personal',monto='$monto',observaciones='$observaciones',cod_area='$cod_area',cod_uo='$cod_uo',nro_recibo='$nro_recibo',cod_proveedores='$cod_proveedores',cod_actividad_sw='$cod_actividad_sw',modified_by=$globalUser,modified_at=NOW(),cod_tipopago=$cod_tipopago
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
            if($cod_comprobante_ec>0){
                $sql="DELETE from estados_cuenta where cod_cajachicadetalle=$codigo";
                $stmtdelete_x = $dbh->prepare($sql);
                $stmtdelete_x->execute();
                $sql="UPDATE pagos_proveedores set cod_estadopago='2' where cod_cajachicadetalle=$codigo";
                $stmtupdate_x = $dbh->prepare($sql);
                $stmtupdate_x->execute();

                //sacamos las cuentas auxiliares
                $nomProveedor=nameProveedor($cod_proveedores);
                //CREAR CUENTA AUXILIAR SI NO EXISTE 
                if(obtenerCodigoCuentaAuxiliarProveedorClienteCuenta(1,$cod_proveedores,$cod_cuenta)==0){
                    $codEstado="1";
                    $stmtInsertAux = $dbh->prepare("INSERT INTO cuentas_auxiliares (nombre, cod_estadoreferencial, cod_cuenta,  cod_tipoauxiliar, cod_proveedorcliente) 
                    VALUES ('$nomProveedor', $codEstado,$cod_cuenta, 1, $cod_proveedores)");
                    $stmtInsertAux->execute();
                }
                $cuenta_auxiliar1=obtenerCodigoCuentaAuxiliarProveedorClienteCuenta(1,$cod_proveedores,$cod_cuenta);
                $stmtContraCuenta = $dbh->prepare("INSERT INTO estados_cuenta(cod_comprobantedetalle,cod_plancuenta,monto,cod_proveedor,fecha,cod_comprobantedetalleorigen,cod_cuentaaux,cod_cajachicadetalle,glosa_auxiliar)values('0','$cod_cuenta','$monto','$cod_proveedores','$fecha','$cod_comprobante_ec','$cuenta_auxiliar1','$codigo','$observaciones')");
                $flagSuccess=$stmtContraCuenta->execute();
                if($flagSuccess){
                    $codigo_sr=0;                    
                    // $sqlDetalleX="SELECT codigo,cod_solicitudrecurso,cod_solicitudrecursodetalle,cod_proveedor,cod_tipopagoproveedor from solicitud_recursosdetalle where cod_estadocuenta=$cod_comprobante_ec";
                    $sqlDetalleX="SELECT sd.codigo,sd.cod_solicitudrecurso,sd.cod_proveedor,sd.cod_tipopagoproveedor 
                    FROM solicitud_recursos s,solicitud_recursosdetalle sd
                    WHERE s.codigo=sd.cod_solicitudrecurso and s.cod_comprobante in (select cd.cod_comprobante from estados_cuenta e,comprobantes_detalle cd where e.cod_comprobantedetalle=cd.codigo and e.codigo=$cod_comprobante_ec)";

                    $stmtDetalleX = $dbh->prepare($sqlDetalleX);
                    $stmtDetalleX->execute();                    
                    $stmtDetalleX->bindColumn('codigo', $codigo_sr);
                    $stmtDetalleX->bindColumn('cod_solicitudrecurso', $cod_solicitudrecurso_sr);
                    $stmtDetalleX->bindColumn('cod_proveedor', $cod_proveedor_sr);
                    $stmtDetalleX->bindColumn('cod_tipopagoproveedor', $cod_tipopagoproveedor_sr);
                    while ($rowDetalleX = $stmtDetalleX->fetch(PDO::FETCH_BOUND)){ 
                        $codigo_sr=$codigo_sr;
                        $cod_solicitudrecurso_sr=$cod_solicitudrecurso_sr;
                        $cod_proveedor_sr=$cod_proveedor_sr;
                        $cod_tipopagoproveedor_sr=$cod_tipopagoproveedor_sr;
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

                        $stmtCambioEstadoSR = $dbh->prepare("UPDATE solicitud_recursos set cod_estadosolicitudrecurso=8 where codigo=:codigo");
                        $stmtCambioEstadoSR->bindParam(':codigo', $cod_solicitudrecurso_sr);
                        $flagSuccess=$stmtCambioEstadoSR->execute();
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
                $array3=json_decode($_POST['d_areas_global']);
                $array4=json_decode($_POST['d_oficinas_global']);
                if($valorDist==1){
                    guardarDatosDistribucion($array1,0,$codigo); //dist x Oficina
                }else{
                    if($valorDist==2){
                      guardarDatosDistribucion(0,$array2,$codigo); //dist x Area
                    }else{
                        if($valorDist==3){
                           guardarDatosDistribucion($array1,$array2,$codigo); //dist x Oficina y Area
                        }else{
                            guardarDatosDistribucionGeneral($array3,$array4,$codigo); //dist x area y Oficina 
                        }
                    }
                }   
            }
        }

     }else{
        //desde sr
        $flagSuccess=true;
     }//fin if isset sr
        //insertamos archivos adjuntos
        $nArchivosCabecera=$_POST["cantidad_archivosadjuntos"];
        for ($ar=1; $ar <= $nArchivosCabecera ; $ar++) { 
          if(isset($_POST['codigo_archivo'.$ar])){
            if($_FILES['documentos_cabecera'.$ar]["name"]){
              $filename = $_FILES['documentos_cabecera'.$ar]["name"]; //Obtenemos el nombre original del archivos
              $source = $_FILES['documentos_cabecera'.$ar]["tmp_name"]; //Obtenemos un nombre temporal del archivos    
              $directorio = 'assets/archivos-respaldo/archivos_cajachicadetalle/GASTO_CC-'.$codigo; //Declaramos una  variable con la ruta donde guardaremos los archivoss
              //Validamos si la ruta de destino existe, en caso de no existir la creamos
              if(!file_exists($directorio)){
                        mkdir($directorio, 0777,true) or die("No se puede crear el directorio de extracci&oacute;n");    
              }
              $target_path = $directorio.'/'.$filename; //Indicamos la ruta de destino, así como el nombre del archivos
              //Movemos y validamos que el archivos se haya cargado correctamente
              //El primer campo es el origen y el segundo el destino
              if(move_uploaded_file($source, $target_path)) { 
                echo "ok";
                $tipo=$_POST['codigo_archivo'.$ar];
                $descripcion=$_POST['nombre_archivo'.$ar];
                // $tipoPadre=2708;

                // $sqlInsert="INSERT INTO archivos_adjuntos_cajachica (cod_tipoarchivo,descripcion,direccion_archivo,cod_tipopadre,cod_padre,cod_objeto) 
                // VALUES ('$tipo','$descripcion','$target_path','$tipoPadre',0,'$codSolicitud')";
                $sqlInsert="INSERT INTO archivos_adjuntos_cajachica(cod_tipoarchivo,descripcion,direccion_archivo,cod_cajachica_detalle) 
                VALUES ('$tipo','$descripcion','$target_path','$codigo')";
                $stmtInsert = $dbh->prepare($sqlInsert);
                $stmtInsert->execute();    
                // print_r($sqlInsert);
              } else {    
                  echo "error";
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

function guardarDatosDistribucionGeneral($array3,$array4,$codigo_cajachica_det){
  $dbh = new Conexion();
    if($array3!=0){
        for ($i=0; $i < count($array3); $i++) { 
            $area=$array3[$i]->area;
            $porcentaje=$array3[$i]->porcentaje;
            $fila=$array3[$i]->fila;

            $sqlInsert="INSERT INTO distribucion_gastos_caja_chica (tipo_distribucion,oficina_area,porcentaje,cod_cajachica_detalle,padre_oficina_area) 
            VALUES ('2','$area','$porcentaje','$codigo_cajachica_det',0)";
            // echo $sqlInsert;       
            $stmtInsert = $dbh->prepare($sqlInsert);
            $stmtInsert->execute();
            // echo $sqlInsert;
            for ($k=0; $k < count($array4) ; $k++) { 
              if($fila==$array4[$k]->cod_fila){
                $unidad=$array4[$k]->unidad;
                $porcentaje=$array4[$k]->porcentaje;
                if($porcentaje>0){
                  $sqlInsert="INSERT INTO distribucion_gastos_caja_chica (tipo_distribucion,oficina_area,porcentaje,cod_cajachica_detalle,padre_oficina_area) 
                  VALUES ('1','$unidad','$porcentaje','$codigo_cajachica_det','$fila')";
                  $stmtInsert = $dbh->prepare($sqlInsert);
                  $stmtInsert->execute();   
                }
              }
            }
        }   
    }
} 
?>