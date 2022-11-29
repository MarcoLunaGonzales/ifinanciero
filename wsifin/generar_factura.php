<?php //ESTADO FINALIZADO

function ejecutarGenerarFactura($sucursalId,$pasarelaId,$fechaFactura,$nitciCliente,$razonSocial,$importeTotal,$items,$CodLibretaDetalle,$tipoPago,$normas,$siat_nroTarjeta,$siat_tipoidentificacion,$siat_complemento,$correoCliente,$cod_cliente,$usuario)
{
    require_once __DIR__.'/../conexion.php';
    require '../assets/phpqrcode/qrlib.php';
    include '../assets/controlcode/sin/ControlCode.php';

    //require_once 'configModule.php';
    require_once __DIR__.'/../functions.php';
    require_once __DIR__.'/../functionsGeneral.php';
    require_once '../simulaciones_servicios/executeComprobante_factura.php';

    $dbh = new Conexion();
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);//try
    set_time_limit(300);
    session_start();

    
    
        // date_default_timezone_set('America/La_Paz');

        $cod_solicitudfacturacion = -100;//desde la tienda usamos el -100
        $cod_uo_solicitud = 5;        
        if($normas==0){
            $cod_area_solicitud = 13;//capacitacion
            foreach ($items as $valor) {
                $pagoCursoId=$valor['pagoCursoId'];
            }
            // $curso_id=obtenerCodigoCurso_pagoid($pagoCursoId);
            $curso_id=0;
            if($curso_id==0){
                $Codigo_alterno="";
            }else{
                $Codigo_alterno=obtenerCodigoExternoCurso($curso_id);    
            }
            $observaciones = 'Tienda Virtual - Curso: '.$Codigo_alterno.' - RS: '.$razonSocial;
        }else{
            $cod_area_solicitud = 12;//normas
            $observaciones = 'Tienda Virtual - Venta de Normas - RS: '.$razonSocial;
        }

        if($pasarelaId==1){
            $cod_tipoobjeto = 1933;
        }else{
            $cod_tipoobjeto = 0;
        }

        if($tipoPago==5 || $tipoPago==6){            
            $cod_tipopago =obtenerValorConfiguracion(55);//deposito en cuenta
        }elseif($tipoPago==4){
            $cod_tipopago = obtenerValorConfiguracion(59);//tarjetas
        }else{
            $cod_tipopago = 0;
        }
        // echo $tipoPago."tipo";

        // $cod_cliente = 0;
        $cod_personal = 0;
        $razon_social = $razonSocial;
        $nitCliente = $nitciCliente;
        
        $nombre_cliente = $razonSocial;                
        $fechaFactura=$fechaFactura;
        $fecha_actual=date('Y-m-d');
        $fechaFactura_x=date('Y-m-d H:i:s');
        // $fechaFactura_xy=date('Y-m-d');            
        
        //Para la facturacion con el SIAT ya no se usa las dosificaciones
        $cod_dosificacionfactura = 0;
        $nroAutorizacion = 1;
        $llaveDosificacion = null;
        $fecha_limite_emision = null;

        // $sqlInfo="SELECT d.codigo,d.nro_autorizacion, d.llave_dosificacion,d.fecha_limite_emision
        //         from dosificaciones_facturas d where d.cod_sucursal='$sucursalId' and d.fecha_limite_emision>='$fecha_actual' and d.cod_estado=1 order by codigo";
        // $stmtInfo = $dbh->prepare($sqlInfo);
        // // echo $sqlInfo;
        // $stmtInfo->execute();
        // $resultInfo = $stmtInfo->fetch();  
        // $cod_dosificacionfactura = $resultInfo['codigo'];  
        // $nroAutorizacion = $resultInfo['nro_autorizacion'];
        // $llaveDosificacion = $resultInfo['llave_dosificacion'];
        // $fecha_limite_emision = $resultInfo['fecha_limite_emision'];
        if($nroAutorizacion==null || $nroAutorizacion=='' || $nroAutorizacion==' '){                    
            return "11###";//No tiene registrado La dosificación para la facturación
        }else{
            //monto total redondeado
            $monto_total= $importeTotal;
            $totalFinalRedondeado=round($monto_total,0);                    
            //NUMERO CORRELATIVO DE FACTURA
            // echo $sucursalId;
            $nro_correlativo=0;
            // $nro_correlativo = nro_correlativo_facturas($sucursalId);
            //SACAMOS EL NRO CORRELATIVO DEL CORREO
            // $nro_correlativoCorreo = nro_correlativo_correocredito($sucursalId,$cod_tipopago);
            $nro_correlativoCorreo = nro_correlativo_correocredito($sucursalId,$cod_tipopago);

            // if($nro_correlativo==0){                
            //     return "11###";//No tiene registrado La dosificación para la facturación

            // }else{                                   
                    //echo "auto:".$nroAutorizacion." - nro_corr:".$nro_correlativo." - nitCliente:".$nitCliente." - fechaFactura:".$fechaFactura." - totalFinalRedondeado:".$totalFinalRedondeado." - llaveDosificacion:".$llaveDosificacion;
                $code=0;// para el siat ya no se usa codigo de control
                // $controlCode = new ControlCode();
                // $code = $controlCode->generate($nroAutorizacion,//Numero de autorizacion
                // $nro_correlativo,//Numero de factura
                // $nitCliente,//Número de Identificación Tributaria o Carnet de Identidad
                // str_replace('-','',$fechaFactura),//fecha de transaccion de la forma AAAAMMDD
                // $totalFinalRedondeado,//Monto de la transacción
                // $llaveDosificacion//Llave de dosificación
                // );
                //echo "cod:".$code;
                $sql="INSERT INTO facturas_venta(cod_sucursal,cod_solicitudfacturacion,cod_unidadorganizacional,cod_area,fecha_factura,fecha_limite_emision,cod_tipoobjeto,cod_tipopago,cod_cliente,cod_personal,razon_social,nit,cod_dosificacionfactura,nro_factura,nro_autorizacion,codigo_control,importe,observaciones,cod_estadofactura,cod_comprobante,created_at,created_by, nro_correlativocorreo) 
                  values ('$sucursalId','$cod_solicitudfacturacion','$cod_uo_solicitud','$cod_area_solicitud',NOW(),'$fecha_limite_emision','$cod_tipoobjeto','$cod_tipopago','$cod_cliente','$cod_personal','$razon_social','$nitCliente','$cod_dosificacionfactura','$nro_correlativo','$nroAutorizacion','$code','$monto_total','$observaciones','1','0',NOW(),1,'$nro_correlativoCorreo')";
                //echo $sql;
                $stmtInsertSoliFact = $dbh->prepare($sql);
                $flagSuccess=$stmtInsertSoliFact->execute();
                $cod_facturaVenta = $dbh->lastInsertId();                    
                // $flagSuccess=true;
                if($flagSuccess){

                    //add de estas lineas para facturacion con SIAT
                    $monto_totalCab=0;
                    $arrayDetalle=[];
                    $contadoDetalle=1;
                    //hasta aqui SIAT

                    //obtenemos el registro del ultimo insert
                    foreach ($items as $valor) {
                        $suscripcionId=$valor['suscripcionId'];
                        $pagoCursoId=$valor['pagoCursoId'];
                        $moduloId=$valor['moduloId'];
                        $codClaServicio2=$valor['codClaServicio'];
                        if(!is_numeric($moduloId)){
                            $moduloId=0;
                        }
                        if(!is_numeric($codClaServicio2)){
                            $codClaServicio2=0;
                        }

                        $detalle=$valor['detalle'];
                        $precioUnitario=$valor['precioUnitario'];
                        $cantidad=$valor['cantidad'];
                        $precio_x=$precioUnitario;
                        $cod_claservicio_x=$pagoCursoId;

                        if( $normas!=0 && ($codClaServicio2==428 || $codClaServicio2==488) ){
                            $cod_claservicio_x=488;
                        }else{
                            $cod_claservicio_x=$codClaServicio2;
                        }

                        $stmtInsertSoliFactDet = $dbh->prepare("INSERT INTO facturas_ventadetalle(cod_facturaventa,cod_claservicio,cantidad,precio,descripcion_alterna,descuento_bob,suscripcionId,cod_modulo,cod_claservicio2) 
                         values ('$cod_facturaVenta','$cod_claservicio_x','$cantidad','$precio_x','$detalle',0,'$suscripcionId','$moduloId','$codClaServicio2')");
                        $flagSuccess=$stmtInsertSoliFactDet->execute();

                        //add de estas lineas para facturacion con SIAT
                        $Objeto_detalle = new stdClass();
                        $Objeto_detalle->codDetalle = $contadoDetalle;
                        $Objeto_detalle->cantidadUnitaria = $cantidad;
                        $Objeto_detalle->precioUnitario = $precio_x;
                        $Objeto_detalle->descuentoProducto = 0;
                        $Objeto_detalle->conceptoProducto = $detalle;
                        $arrayDetalle[] = $Objeto_detalle;
                        $monto_totalCab+=$precio_x*$cantidad;
                        $contadoDetalle++;
                        //hasta aqui SIAT
                    }

                    if($flagSuccess){
                        $cod_comprobante=ejecutarComprobanteSolicitud_tiendaVirtual($nitciCliente,$razonSocial,$items,$monto_total,$nro_correlativo,$tipoPago,$CodLibretaDetalle,$normas,$cod_facturaVenta);
                        if($cod_comprobante==null || $cod_comprobante==''){
                            $sqldeleteCabeceraFactura="DELETE from facturas_venta where codigo=$cod_facturaVenta";
                            $stmtDeleteCAbeceraFactura = $dbh->prepare($sqldeleteCabeceraFactura);
                            $stmtDeleteCAbeceraFactura->execute();
                            $sqldeleteDetalleFactura="DELETE from facturas_ventadetalle where cod_facturaventa=$cod_facturaVenta";
                            $stmtDeleteDetalleFactura = $dbh->prepare($sqldeleteDetalleFactura);
                            $stmtDeleteDetalleFactura->execute();
                            return "12###";
                        }else{
                            if($cod_comprobante==-1){
                                $sqldeleteCabeceraFactura="DELETE from facturas_venta where codigo=$cod_facturaVenta";
                                $stmtDeleteCAbeceraFactura = $dbh->prepare($sqldeleteCabeceraFactura);
                                $stmtDeleteCAbeceraFactura->execute();
                                $sqldeleteDetalleFactura="DELETE from facturas_ventadetalle where cod_facturaventa=$cod_facturaVenta";
                                $stmtDeleteDetalleFactura = $dbh->prepare($sqldeleteDetalleFactura);
                                $stmtDeleteDetalleFactura->execute();
                                return "18###";
                            }else{
                                $sqlUpdateLibreta="UPDATE facturas_venta SET cod_comprobante=$cod_comprobante where codigo=$cod_facturaVenta";
                                $stmtUpdateLibreta = $dbh->prepare($sqlUpdateLibreta);
                                $stmtUpdateLibreta->execute();
                                
                                //********
                                //preparamos datos para facturacion con el SIAT
                                $sqlInfo="SELECT (select stp.codigoClasificador from siat_tipos_pago stp where stp.cod_tipopago=f.cod_tipopago)as siat_tipoPago from facturas_venta f where f.codigo=$cod_facturaVenta";
                                $stmtInfo = $dbh->prepare($sqlInfo);
                                $stmtInfo->execute();
                                $resultInfo = $stmtInfo->fetch();
                                $siat_tipoPago = $resultInfo['siat_tipoPago'];

                                //datos cabecera
                                $descuentoCab=0;
                                $monto_finalCab=$monto_totalCab-$descuentoCab;
                                $id_usuario=$cod_personal;//ID usuario quien facturó
                                //$usuario=namePersonal_2($id_usuario);//Usuario quien facturó

                                $cod_sucursal=$sucursalId;
                                $datosWS=enviar_factura_minkasiat($cod_sucursal,$cod_solicitudfacturacion,$fecha_actual,$cod_cliente,$monto_totalCab,$descuentoCab,$monto_finalCab,$id_usuario,$usuario,$nitCliente,$razon_social,$siat_tipoPago,$siat_nroTarjeta,$siat_tipoidentificacion,$siat_complemento,$arrayDetalle,$correoCliente,$cod_facturaVenta);
                                $banderaSW=false;
                                if(isset($datosWS->estado) && isset($datosWS->idTransaccion)){//el servicio respondio
                                    $idTransaccion_x=$datosWS->idTransaccion;
                                    $nroFactura_x=$datosWS->nroFactura;
                                    $mensaje_x=$datosWS->mensaje;
                                    $banderaSW=true;
                                    if($datosWS->estado==1){//Todo ok con el servicio
                                        $titulo="Correcto!";
                                        $estado="success";
                                    }else{
                                        $titulo="Informativo!";
                                        $estado="warning";
                                    }
                                }else{
                                    //timepo de respuesta solo 3 segundos. avences se lanza
                                    $datosWS_consulta=verificarExistenciaFacturaSiat($cod_facturaVenta);

                                    $titulo="Error!";
                                    $estado="error";
                                    $mensaje_x="Hubo un error con el Servicio MinkaSiat.";
                                    if(isset($datosWS_consulta->estado)){
                                        if($datosWS_consulta->estado==1){
                                            $idTransaccion_x=$datosWS_consulta->idTransaccion;
                                            $nroFactura_x=$datosWS_consulta->nroFactura;
                                            $mensaje_x=$datosWS_consulta->mensaje;
                                            $banderaSW=true;
                                            $titulo="Correcto!";
                                            $estado="success";
                                        }
                                    }   
                                }
                                if($banderaSW){
                                    $sqlUpdateFact="UPDATE facturas_venta set idTransaccion_siat='$idTransaccion_x',nro_factura='$nroFactura_x' where codigo in ($cod_facturaVenta)";
                                    $stmtUpdateFact = $dbh->prepare($sqlUpdateFact);
                                    $stmtUpdateFact->execute();
                                }
                                return "0###".$cod_facturaVenta;    
                            }
                        }
                        
                    }else{
                        return "12###";      
                    }
                }else{
                  return "12###";
                }   
            // }
        }    
  

}
?>
