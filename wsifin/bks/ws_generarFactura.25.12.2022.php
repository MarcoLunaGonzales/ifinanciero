<?php

function insertarlogFacturas_entrada($json,$mensaje,$enlaceCon){
    $start_time = microtime(true);
    // $dbh = new Conexion();    
    // $sql="INSERT INTO log_facturas(fecha,detalle_error,json) values(NOW(),'$mensaje','$json')";
    // $stmt = $dbh->prepare($sql);
    // $stmt->execute();

    $jsonString=json_encode($json);
    $fechaActualX=date('Y-m-d H:i:s');
    $sql="INSERT INTO log_facturas(fecha,detalle_error,json) values('$fechaActualX','$mensaje','$json')";
    // echo $sqlUpdate;
    // echo "<br>";
    $resp=mysqli_query($enlaceCon,$sql);
}

function InsertlogFacturas_salida($cod_error,$detalle_error,$json,$enlaceCon){  
    $jsonString=json_encode($json);
    $fechaActualX=date('Y-m-d H:i:s');
    $sqlUpdate="INSERT INTO log_facturas(fecha,cod_error,detalle_error,json) values('$fechaActualX','$cod_error','$detalle_error','$jsonString')";
    // echo $sqlUpdate;
    // echo "<br>";
    $resp=mysqli_query($enlaceCon,$sqlUpdate);
}
// SERVICIO WEB PARA FACTURAS
if ($_SERVER['REQUEST_METHOD'] == 'POST') {//verificamos  metodo conexion
    // $datos = json_decode(file_get_contents("php://input"), true); 
    require_once '../conexionmysqli2.php';    
    $json=file_get_contents("php://input");
    insertarlogFacturas_entrada($json,'Entrada json',$enlaceCon);
    $datos = json_decode($json, true);
    //Parametros de consulta
    $accion=NULL;
    if(isset($datos['accion'])&&isset($datos['sIdentificador'])&&isset($datos['sKey'])){//verificamos existencia de datos de conexion
        if($datos['sIdentificador']=="MinkaSw123*"&&$datos['sKey']=="rrf656nb2396k6g6x44434h56jzx5g6"){//verificamos datos de conexion
            $accion=$datos['accion']; //recibimos la accion
            // $codPersonal=$datos['codPersonal'];//recibimos el codigo personal
            $estado=0;
            $mensaje="";
            if($accion=="generarFacturaMinka"){//obtenemos las ciudades del cliente
                    // require_once '../conexionmysqli2.php';
                    require_once '../siat_folder/funciones_servicios.php';
                    // if(isset($datos['idEmpresa'])){
                        // $idEmpresa=$datos['idEmpresa'];//
                        // $nitEmpresa=$datos['nitEmpresa'];//
                        // if(verificarExistenciaEmpresa($idEmpresa,$nitEmpresa,$enlaceCon)){

                            if(isset($datos['sucursalId']) && isset($datos['fechaFactura'])  && isset($datos['id_usuario']) && isset($datos['usuario']) && isset($datos['nitciCliente']) && isset($datos['razonSocial']) && isset($datos['tipoPago']) && isset($datos['idIdentificacion']) && isset($datos['complementoCiCliente']) && isset($datos['items']) && isset($datos['importeTotal']) && isset($datos['descuento']) && isset($datos['importeFinal'])){ 

                                $sucursal=$datos['sucursalId'];
                                // $tipoTabla=$datos['tipoTabla'];
                                $fecha=$datos['fechaFactura'];
                                $idPersona=$datos['id_usuario'];
                                
                                $monto_total=$datos['importeTotal'];
                                $descuento=$datos['descuento'];
                                $monto_final=$datos['importeFinal'];

                                $id_usuario=$datos['id_usuario'];
                                $usuario=$datos['usuario'];
                                $nitCliente=$datos['nitciCliente'];
                                $nombreFactura=$datos['razonSocial'];
                                // $Concepto=$datos['Concepto'];
                                $tipoPago=$datos['tipoPago'];
                                $nroTarjeta=$datos['nroTarjeta'];
                                $tipoDocumento=$datos['idIdentificacion'];
                                $complementoDocumento=$datos['complementoCiCliente'];
                                if (isset($datos['CorreoCliente'])) {
                                    $correo_destino=$datos['CorreoCliente'];          
                                }else{
                                    // $correo_destino="bsullcamani@gmail.com";
                                    $correo_destino="";
                                }
                                if (isset($datos['codFacturaIbno'])) {
                                    $codFacturaIbno=$datos['codFacturaIbno'];          
                                }else{
                                    // $correo_destino="bsullcamani@gmail.com";
                                    $codFacturaIbno=0;
                                }

                                
                                // $periodoFacturado=$datos['periodoFacturado'];
                                // $NombreEstudiante=$datos['NombreEstudiante'];
                                $periodoFacturado=null;
                                $NombreEstudiante=null;

                                $items=$datos['items'];//recibimos array de detalle
                                $datosFactura=generarFacturaSiat($sucursal,0,$fecha,$idPersona,$monto_total,$descuento,$monto_final,$id_usuario,$usuario,$nitCliente,$nombreFactura,$NombreEstudiante,$tipoPago,$nroTarjeta,$tipoDocumento,$complementoDocumento,$periodoFacturado,$items,$correo_destino,$codFacturaIbno);

                                $estado=$datosFactura[0];//estado
                                $mensaje=$datosFactura[1];//mensaje
                                $codigo_transaccion=$datosFactura[2];//codigo
                                $nro_factura=$datosFactura[3];//nro factura
                                switch ($estado) {
                                    case 0://factura online
                                        $estado_ws=1;
                                    break;
                                    case 1://factura offline
                                        $estado_ws=1;
                                    break;
                                    case 2://error en factura
                                        # code...
                                        $estado_ws=5;//error al generar factura
                                    break;
                                }
                                $resultado=array("estado"=>$estado_ws,
                                    "mensaje"=>$mensaje, 
                                    "idTransaccion"=>$codigo_transaccion, 
                                    "nroFactura"=>$nro_factura, 
                                    );

                            }else{
                                $mensaje="ERROR. Variables incompletas";
                                $resultado=array("estado"=>4,
                                "mensaje"=>$mensaje);
                                //InsertlogFacturas_salida(4,$mensaje,null,$enlaceCon);
                            }
                                                        
                        // }else{
                        //     $resultado=array("estado"=>4,
                        //     "mensaje"=>"ERROR. IdEmpresa o nitEmpresa inexistente");
                        // }
                    // }else{
                    //     $resultado=array("estado"=>4,
                    //     "mensaje"=>"ERROR. Variables incompletas");
                    // }
            }else{
                $mensaje="ERROR. No existe la Accion Solicitada.";
                $resultado=array("estado"=>4,
                    "mensaje"=>$mensaje);
                //InsertlogFacturas_salida(4,$mensaje,null,$enlaceCon);
            }
        }else{
            $mensaje="ACCESO DENEGADO!. Credenciales Incorrectos.";
            $resultado=array("estado"=>3,"mensaje"=>$mensaje);
            //InsertlogFacturas_salida(3,$mensaje,null,$enlaceCon);
        }
    }else{
        $resultado=array(
                "estado"=>3,
                "mensaje"=>"ACCESO DENEGADO!. Usted no tiene permiso para ver este contenido.");
    }
    InsertlogFacturas_salida(3,"Salida Json",$resultado,$enlaceCon);
    header('Content-type: application/json');
    echo json_encode($resultado); 
}else{
    $resultado=array(
                "estado"=>3,
                "mensaje"=>"ACCESO DENEGADO!. Usted no tiene permiso para ver este contenido.");
    header('Content-type: application/json');
    echo json_encode($resultado);
}

function generarFacturaSiat($sucursal,$idRecibo,$fecha,$idPersona,$monto_total,$descuento,$monto_final,$id_usuario,$siat_usuario,$nitCliente,$nombreFactura,$NombreEstudiante,$tipoPago,$nroTarjeta,$tipoDocumento,$complementoDocumento,$periodoFacturado,$items,$correo_destino,$codFacturaIbno){
    // $correo_destino="bsullcamani@gmail.com";
    $start_time = microtime(true);
    require_once "../conexionmysqli2.php";
    // require_once "../estilos_almacenes.inc";
    require_once "../funciones.php";
    require_once "../funciones_inventarios.php";
    require_once "../enviar_correo/php/send-email_anulacion.php";
    require_once "../siat_folder/funciones_siat.php";

    $usuarioVendedor=$id_usuario;//codigo usuario
    
    // $globalSucursal=1;//cod ciudad //encontrar dato
    // $almacenOrigen=1000;//cod almacen
    // $globalSucursal=$sucursal;//cod ciudad //encontrar dato
    
    $datosCiudad=obtenerAlmacen($sucursal,$enlaceCon);
    $globalSucursal=$datosCiudad[0];
    $almacenOrigen=$datosCiudad[1];
    $cod_impuestos=$datosCiudad[2];
    $cod_entidad=$datosCiudad[3];

    $errorProducto="";
    $totalFacturaMonto=0;
    // $tipoSalida=$_POST['tipoSalida'];
    $tipoSalida=1001;
    // $tipoDoc=$_POST['tipoDoc']; 
    $tipoDoc=1;//tipo factura automatica, nota emision =2, factura manual= 4 
    $almacenDestino=0;
    $cod_tipopreciogeneral=0;
    $cod_tipoVenta2=1;
    $cod_tipodelivery=0;
    $monto_bs=$monto_final; //monto cancelado
    $monto_usd=0;
    $tipo_cambio=0;
    $codCliente=$idPersona;
    $tipoPrecio=0;
    $razonSocial=$nombreFactura;
    if($razonSocial==""){
        $razonSocial="SN";
    }
    $razonSocial=addslashes($razonSocial);
    $nitCliente=$nitCliente;
    if((int)$nitCliente==123){
        $razonSocial="SN";
    }
    /*VALIDACION MANUAL CASOS ESPECIALES IMPUESTOS*/
    if((int)$nitCliente=='99001' || (int)$nitCliente=='99002' || (int)$nitCliente=='99003'){
        $siat_codigotipodocumentoidentidad=5;//nit
    }

    $fecha_emision_manual="";

    $tipoVenta=$tipoPago;
    $observaciones="";
    $cuf="";
    $totalVenta=$monto_total;
    $descuentoVenta=$descuento;
    $totalFinal=$monto_final;
    $totalEfectivo=0;
    $totalCambio=0;
    $complemento=$complementoDocumento;

    $cantidad_material=1;
    if($descuentoVenta==""){
        $descuentoVenta=0;
    }
    $vehiculo=0;

    //fecha emision
    $fecha=date("Y-m-d");
    $hora=date("H:i:s");

    //SACAMOS LA CONFIGURACION PARA EL DOCUMENTO POR DEFECTO
    $sqlConf="select valor_configuracion from configuraciones where id_configuracion=1";
    $respConf=mysqli_query($enlaceCon,$sqlConf);
    $datConf=mysqli_fetch_array($respConf);
    $tipoDocDefault=$datConf[0];
    //SACAMOS LA CONFIGURACION PARA CONOCER SI LA FACTURACION ESTA ACTIVADA
    $sqlConf="select valor_configuracion from configuraciones where id_configuracion=3";
    $respConf=mysqli_query($enlaceCon,$sqlConf);
    $datConf=mysqli_fetch_array($respConf);
    $facturacionActivada=$datConf[0];// 1 inserta en facturas venta

    $sqlConf="select valor_configuracion from configuraciones where id_configuracion=4";
    $respConf=mysqli_query($enlaceCon,$sqlConf);
    $datConf=mysqli_fetch_array($respConf);
    $banderaValidacionStock=$datConf[0];
    //$banderaValidacionStock=mysql_result($respConf,0,0);

    //variables para envio de correo
    $siat_estado_facturacion="";

    //SI TIPO DE DOCUMENTO ES 1 == FACTURA INGRESAMOS A LOS PROCESOS SIAT y 4 facturas de contigencia
    if($tipoDoc==1 || $tipoDoc==4){
        //ALEATORIAMENTE SON DOS PORQUE AL PRIMER RAND SIEMPRE RETORNA EL MISMO
        // $sqlConf="SELECT codigo FROM siat_sincronizarlistaleyendasfactura where codigoActividad=$codigoActividadSIAT and estado=1 ORDER BY rand() LIMIT 1;";
        $sqlConf="SELECT codigo FROM siat_sincronizarlistaleyendasfactura where codigoActividad in (SELECT siat_codigoActividad from ciudades where cod_ciudad='$globalSucursal') and estado=1 ORDER BY rand() LIMIT 1;";
        $respConf=mysqli_query($enlaceCon,$sqlConf);
        // $cod_leyenda=mysqli_result($respConf,0,0);
        $datConf=mysqli_fetch_array($respConf);
        $cod_leyenda=$datConf[0];
        // $sqlConf="SELECT codigo FROM siat_sincronizarlistaleyendasfactura where codigoActividad=$codigoActividadSIAT and estado=1 ORDER BY rand() LIMIT 1;";
        $sqlConf="SELECT codigo FROM siat_sincronizarlistaleyendasfactura where codigoActividad in (SELECT siat_codigoActividad from ciudades where cod_ciudad='$globalSucursal') and estado=1 ORDER BY rand() LIMIT 1;";
        $respConf=mysqli_query($enlaceCon,$sqlConf);
        // $cod_leyenda=mysqli_result($respConf,0,0);
        $datConf=mysqli_fetch_array($respConf);
        $cod_leyenda=$datConf[0];
        // $siat_codigotipodocumentoidentidad=$_POST["tipo_documento"]; 
        $siat_codigotipodocumentoidentidad=$tipoDocumento;
    }
    

    $created_by=$usuarioVendedor;
    $contador = 0;
    do {
        // echo "1<br>";
        $anio=date("Y");
        $created_at=date("Y-m-d H:i:s");
        $sql="SELECT IFNULL(max(cod_salida_almacenes)+1,1) FROM salida_almacenes";
        $resp=mysqli_query($enlaceCon,$sql);
        // $codigo=mysqli_result($resp,0,0);
        $datCodSalida=mysqli_fetch_array($resp);
        $codigo=$datCodSalida[0];
        // echo "2*".$codigo."<br>";
        //PARA CUANDO ES FACTURA Y ACTIVAMOS PROCESOS SIAT
        // if($tipoDoc==1 || $tipoDoc==4){      
            $sqlCuis="select cuis FROM siat_cuis where cod_ciudad='$globalSucursal' and estado=1 and cod_gestion='$anio' LIMIT 1";
            // echo $sqlCuis;
            $respCuis=mysqli_query($enlaceCon,$sqlCuis);
            // $cuis=mysqli_result($respCuis,0,0);
            $datConf=mysqli_fetch_array($respCuis);
            $cuis=$datConf[0];    
            // echo "3*".$cuis."<br>";
            $sqlPV="SELECT codigoPuntoVenta FROM siat_puntoventa where cod_ciudad='$globalSucursal' LIMIT 1";
            $respPV=mysqli_query($enlaceCon,$sqlPV);
            // $codigoPuntoVenta=mysqli_result($respPV,0,0);
            $datPV=mysqli_fetch_array($respPV);
            $codigoPuntoVenta=$datPV[0];
            // echo "4*".$codigoPuntoVenta."<br>";
            $sqlCufd="SELECT codigo,cufd,codigo_control FROM siat_cufd where cod_ciudad='$globalSucursal' and estado=1 and fecha='$fecha' and cuis='$cuis' LIMIT 1";        
            // echo $sqlCufd;
            $respCufd=mysqli_query($enlaceCon,$sqlCufd);
            $datCufd=mysqli_fetch_array($respCufd);
            $codigoCufd=$datCufd[0];
            $cufd=$datCufd[1];
            $controlCodigo=$datCufd[2];
            if($codigoCufd==null || $codigoCufd==""){                
                generarCufd($globalSucursal,$cod_impuestos,$codigoPuntoVenta,$cod_entidad);
                $sqlCufd="SELECT codigo,cufd,codigo_control FROM siat_cufd where cod_ciudad='$globalSucursal' and estado=1 and fecha='$fecha' and cuis='$cuis' LIMIT 1";        
                // echo $sqlCufd;
                $respCufd=mysqli_query($enlaceCon,$sqlCufd);
                $datCufd=mysqli_fetch_array($respCufd);
                $codigoCufd=$datCufd[0];
                $cufd=$datCufd[1];
                $controlCodigo=$datCufd[2];
            }
            
            $vectorNroCorrelativo=numeroCorrelativoCUFD($enlaceCon,$tipoDoc,$globalSucursal,$almacenOrigen);
            $nro_correlativo=$vectorNroCorrelativo[0];      
            $cod_dosificacion=0;    
            // $nro_correlativo=$nro_factura;
            $excepcion=0;
            //verificamos existencia de nit en impuestos}       
            $data=verificarNitClienteSiat($nitCliente,$globalSucursal);
            if(!isset($data->RespuestaVerificarNit)){
                $siat_error_valor=0;//no es nit
            }else{
                $datos=$data->RespuestaVerificarNit;
                if(isset($datos->mensajesList->codigo)){
                    if ($datos->mensajesList->codigo==1) {//todo ok
                        $siat_error_valor=1;//es nit
                    }else{
                        $siat_error_valor=0;//no es nit
                    }
                }else{
                    $siat_error_valor=0;//no es nit
                }
            }       

            if($siat_error_valor==0 && $siat_codigotipodocumentoidentidad==5){
                $excepcion=1;
            }
            if($siat_codigotipodocumentoidentidad==5){
                $complemento="";
            }

            $sql_insert="INSERT INTO `salida_almacenes`(`cod_salida_almacenes`, `cod_almacen`,`cod_tiposalida`, 
                `cod_tipo_doc`, `fecha`, `hora_salida`, `territorio_destino`, 
                `almacen_destino`, `observaciones`, `estado_salida`, `nro_correlativo`, `salida_anulada`, 
                `cod_cliente`, `monto_total`, `descuento`, `monto_final`, razon_social, nit, cod_chofer, cod_vehiculo, monto_cancelado, cod_dosificacion, monto_efectivo,
                monto_cambio,cod_tipopago,created_by,created_at,cod_tipopreciogeneral,cod_tipoventa2,monto_cancelado_bs,monto_cancelado_usd,tipo_cambio,cod_delivery,
                siat_cuis,siat_cuf,siat_codigotipodocumentoidentidad,siat_complemento,siat_codigoPuntoVenta,siat_excepcion,siat_codigocufd,siat_cod_leyenda,siat_nombreEstudiante,siat_periodoFacturado,siat_usuario,cod_factura_ibno)
                values ('$codigo', '$almacenOrigen', '$tipoSalida', '$tipoDoc', '$fecha', '$hora', '0', '$almacenDestino', 
                '$observaciones', '1', '$nro_correlativo', 0, '$codCliente', '$totalVenta', '$descuentoVenta', '$totalFinal', '$razonSocial', 
                '$nitCliente', '$usuarioVendedor', '$vehiculo',0,'$cod_dosificacion','$totalEfectivo','$totalCambio','$tipoVenta','$created_by','$created_at','$cod_tipopreciogeneral','$cod_tipoVenta2','$monto_bs','$monto_usd','$tipo_cambio','$cod_tipodelivery','$cuis','$cuf','$siat_codigotipodocumentoidentidad','$complemento','$codigoPuntoVenta',$excepcion,'$codigoCufd','$cod_leyenda','$NombreEstudiante','$periodoFacturado','$siat_usuario','$codFacturaIbno')";         
            $sql_inserta=mysqli_query($enlaceCon,$sql_insert);
        // }
        $contador++;
    } while ($sql_inserta<>1 && $contador <= 100);


    if($sql_inserta==1){
        $code="";
        //TARJETA INSERTAR

       if($nroTarjeta!=0&&$nroTarjeta!=""){//&&$tipoVenta==2 
           $nro_tarjeta=$nroTarjeta;
           $monto_tarjeta=$totalFinal;
           $banco_tarjeta=0;
           $nro_tarjeta=str_replace("*","0",$nro_tarjeta);
           $sql_tarjeta="INSERT INTO tarjetas_salidas (nro_tarjeta,monto,cod_banco,cod_salida_almacen,estado) VALUES('$nro_tarjeta','$monto_tarjeta','$banco_tarjeta','$codigo',1)";
           $sql_tarjeta=mysqli_query($enlaceCon,$sql_tarjeta);
       }
        if($facturacionActivada==1 && $tipoDoc==1){
            //insertamos la factura
            $sqlInsertFactura="insert into facturas_venta (cod_dosificacion, cod_sucursal, nro_factura, cod_estado, razon_social, nit, fecha, importe, 
            codigo_control, cod_venta) values ('$cod_dosificacion','$globalSucursal','$nro_correlativo','1','$razonSocial','$nitCliente','$fecha','$totalFinal',
            '$code','$codigo')";
            // echo $sqlInsertFactura;
            $respInsertFactura=mysqli_query($enlaceCon,$sqlInsertFactura);  
        }
        // print_r($items);
        $i=1;
        foreach ($items as $valor) {
            
            //$codDetalle=$valor['codDetalle'];
            $codDetalle=$i;
            $cantidadUnitaria=$valor['cantidad'];
            $precioUnitario=$valor['precioUnitario'];
            $descuentoProducto=$valor['descuentoProducto'];
            $conceptoProducto=$valor['detalle'];            
            //SE DEBE CALCULAR EL MONTO DEL MATERIAL POR CADA UNO PRECIO*CANTIDAD - EL DESCUENTO ES UN DATO ADICIONAL
            $montoMaterial=$precioUnitario*$cantidadUnitaria;
            // $montoMaterialConDescuento=($precioUnitario*$cantidadUnitaria)-$descuentoProducto;
            //$montoTotalVentaDetalle=$montoTotalVentaDetalle+$montoMaterialConDescuento;
            $respuesta=insertar_detalleSalidaVenta($enlaceCon,$codigo, $almacenOrigen,$codDetalle,$cantidadUnitaria,$precioUnitario,$descuentoProducto,$montoMaterial,$banderaValidacionStock, $i,$conceptoProducto);
            $i++;
        }

        
        if($tipoSalida==1001){
            //servicios siat
            // if($tipoDoc==1){
                $sqlRecep="select siat_codigoRecepcion from salida_almacenes where cod_salida_almacenes='$codigo'";
                $respRecep=mysqli_query($enlaceCon,$sqlRecep);
                // $recepcion=mysqli_result($respRecep,0,0);
                $datPV=mysqli_fetch_array($respRecep);
                $recepcion=$datPV[0];

                $errorFacturaXml=0;
                $json=null;
                if($recepcion==""){         
                    // require_once "../siat_folder/funciones_siat.php";
                    $errorConexion=verificarConexion()[0];
                    if($_POST['siat_error_valor']==0&&$_POST['tipo_documento']==5){
                        $facturaImpuestos=generarFacturaVentaImpuestos($codigo,true,$errorConexion);            
                    }else{                  
                        $facturaImpuestos=generarFacturaVentaImpuestos($codigo,false,$errorConexion);   
                    }
                    // echo $facturaImpuestos."**";
                    $fechaEmision=$facturaImpuestos[1];
                    $cuf=$facturaImpuestos[2];      
                    if(isset($facturaImpuestos[0]->RespuestaServicioFacturacion->codigoRecepcion)){
                        $codigoRecepcion=$facturaImpuestos[0]->RespuestaServicioFacturacion->codigoRecepcion;
                        $sqlUpdMonto="update salida_almacenes set siat_fechaemision='$fechaEmision',siat_estado_facturacion='1',siat_codigoRecepcion='$codigoRecepcion',siat_cuf='$cuf',siat_codigocufd='$codigoCufd',siat_codigotipoemision='1' 
                                where cod_salida_almacenes='$codigo' ";
                        $respUpdMonto=mysqli_query($enlaceCon,$sqlUpdMonto);
                        $siat_estado_facturacion=1;
                    }else{
                        $sqlUpdMonto="update salida_almacenes set siat_codigotipoemision=2,siat_fechaemision='$fechaEmision',siat_codigocufd='$codigoCufd',siat_cuf='$cuf'
                            where cod_salida_almacenes='$codigo' ";
                        $respUpdMonto=mysqli_query($enlaceCon,$sqlUpdMonto);
                        $errorFacturaXml=1;
                        // echo $sqlUpdMonto;
                    }          
                    $json=$facturaImpuestos[0];           
                }
                if($errorFacturaXml==0){
                    $estado_facturado=0;
                    $mensaje="Transacción Exitosa :)"; 
                    $url="location.href='formatoFacturaOnLine.php?codVenta=$codigo';";
                }else{ //ESTO ES CUANDO HAY ERROR FACTURA
                    $estado_facturado=1;
                    $mensaje="Factura emitida fuera de línea :(";               
                    $url="location.href='dFacturaElectronica.php?codigo_salida=$codigo';";
                }
                InsertlogFacturas_salida($estado_facturado,"Resp.SIAT",$json,$enlaceCon);
                //SACAMOS LA VARIABLE PARA ENVIAR EL CORREO O NO SI ES 1 ENVIAMOS CORREO DESPUES DE LA TRANSACCION
                 $banderaCorreo=obtenerValorConfiguracion($enlaceCon,10);
                // $banderaCorreo=1;
                if($banderaCorreo==1){
                    //para correo solo en caso de offline y online
                    $enviar_correo=true;
                    // $correo_destino=obtenerCorreosListaCliente($codCliente);
                    if($correo_destino==null || $correo_destino=="" || $correo_destino==" "){
                        $enviar_correo=false;
                        //$texto_correo="<span style=\"border:1px;font-size:18px;color:orange;\"><b>EL CLIENTE NO TIENE UN CORREO REGISTRADO</b></span>";
                    }
                }else{
                    $enviar_correo=false;
                    //$texto_correo="<span style=\"border:1px;font-size:18px;color:orange;\"><b>CORREO NO ENVIADO</b></span>";
                }
                if($enviar_correo){
                    $sw_correo=true;
                    $codigoVenta=$codigo;
                    require_once "../descargarFacturaXml.php";
                    $codigoVenta=$codigo;
                    require_once "../descargarFacturaPDF.php";

                    $estado_envio=envio_factura($codigoVenta,$correo_destino,$enlaceCon);
                    if($estado_envio==1){
                        //$texto_correo="<span style=\"border:1px;font-size:18px;color:#91d167;\"><b>SE ENVIÓ EL CORREO CON EXITO.</b></span>";
                    }elseif($estado_envio==0){
                        //$texto_correo="<span style=\"border:1px;font-size:18px;color:orange;\"><b>EL CLIENTE NO TIENE UN CORREO REGISTRADO</b></span>";
                    }else{
                        //$texto_correo="<span style=\"border:1px;font-size:18px;color:red;\"><b>Ocurrio un error al enviar el correo, vuelva a intentarlo.</b></span>";
                    }
                    // echo "<script language='Javascript'>
                    //     Swal.fire({
                    //     title: 'SIAT: ".$mensaje."',
                    //     html: '".$texto_correo."',
                    //     type: 'success'
                    //     }).then(function() {
                    //        location.href='navegadorVentas.php'; 
                    //     });
                    //     </script>";
                    
                    return array($estado_facturado,$mensaje,$codigo,$nro_correlativo);
                    // $texto_correo="<span style=\"border:1px;font-size:18px;color:#91d167;\"><b>¿DESEAS ENVIAR CORREO?</b></span>";
                    // echo "<script language='Javascript'>
                }else{
                    // echo "<script language='Javascript'>
                    //  Swal.fire({
                    //     title: 'SIAT: ".$mensaje."',
                    //     html: '".$texto_correo."',
                    //     type: 'success'
                    //  }).then(function() {
                    //      location.href='navegadorVentas.php';
                    //  });
                    //  </script>";
                    
                    return array($estado_facturado,$mensaje,$codigo,$nro_correlativo);
                }

            // }
        }   
    }else{
            // echo "<script type='text/javascript' language='javascript'>
            //  alert('Ocurrio un error en la transaccion. Contacte con el administrador del sistema.');
                
            // </script>";//location.href='navegador_salidamateriales.php';
        // $errorEmision = 1;

        $mensaje="Ocurrio un error en la transaccion. Contacte con el administrador del sistema";
        $estado_facturado=2;//error
        //InsertlogFacturas_salida($estado_facturado,$mensaje,null,$enlaceCon);
        return array($estado_facturado,$mensaje,$codigo,$nro_correlativo);
    }   
}


function obtenerAlmacen($cod_ciudad_externo,$enlaceCon){
    //require("conexionmysqli2.php");
    $sql="SELECT c.cod_ciudad,a.cod_almacen,c.cod_impuestos,c.cod_entidad
        from ciudades c join almacenes a on c.cod_ciudad=a.cod_ciudad
        where c.cod_externo=$cod_ciudad_externo";
    $resp=mysqli_query($enlaceCon,$sql);
    $cod_ciudad=0;
    $cod_almacen=0;
    $cod_impuestos=0;
    $cod_entidad=0;
    while ($dat = mysqli_fetch_array($resp)) {
        $cod_ciudad=$dat['cod_ciudad'];
        $cod_almacen=$dat['cod_almacen'];
        $cod_impuestos=$dat['cod_impuestos'];
        $cod_entidad=$dat['cod_entidad'];
    }
    return array($cod_ciudad,$cod_almacen,$cod_impuestos,$cod_entidad);
}