<?php //ESTADO FINALIZADO

// $sucursalId=5; // ID Sucursal
// $fechaFactura='2020-05-09'; // fecha a factura
// $nitciCliente=1020113024; //nit o ci de cliente
// $razonSocial='Juan Gabriel'; //razon social
// $importeTotal=260.5; //importe total

// $Objeto_detalle = new stdClass();
// $Objeto_detalle->suscripcionId = 1;
// $Objeto_detalle->pagoCursoId = 1;
// $Objeto_detalle->detalle = "detalle del item";
// $Objeto_detalle->precioUnitario = 100;
// $Objeto_detalle->cantidad = 1;

// $Objeto_detalle2 = new stdClass();
// $Objeto_detalle2->suscripcionId = 2;
// $Objeto_detalle2->pagoCursoId = 2;
// $Objeto_detalle2->detalle = "detalle del item2";
// $Objeto_detalle2->precioUnitario = 100;
// $Objeto_detalle2->cantidad = 1;
// $items= array($Objeto_detalle,$Objeto_detalle2);


// ejecutarGenerarFactura($sucursalId,$fechaFactura,$nitciCliente,$razonSocial,$importeTotal,$items);

function ejecutarGenerarFactura($sucursalId,$pasarelaId,$fechaFactura,$nitciCliente,$razonSocial,$importeTotal,$items,$CodLibretaDetalle){
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

    //$globalUser=$_SESSION["globalUser"];
    //RECIBIMOS LAS VARIABLES    
    try{
        $cod_solicitudfacturacion = 0;
        $cod_uo_solicitud = 5;
        $cod_area_solicitud = 13;
        if($pasarelaId==1){
            $cod_tipoobjeto = 1933;
        }else{
            $cod_tipoobjeto = 0;
        }
        $cod_tipopago = 0;
        $cod_cliente = 0;
        $cod_personal = 0;
        $razon_social = $razonSocial;
        $nitCliente = $nitciCliente;
        $observaciones = 'Tienda virtual - RZ: '.$razonSocial;
        $nombre_cliente = $razonSocial;                
        $fechaFactura=$fechaFactura;
        $sqlInfo="SELECT d.codigo,d.nro_autorizacion, d.llave_dosificacion,d.fecha_limite_emision
        from dosificaciones_facturas d where d.cod_sucursal='$sucursalId' order by codigo";
        $stmtInfo = $dbh->prepare($sqlInfo);
        // echo $sqlInfo;
        $stmtInfo->execute();
        $resultInfo = $stmtInfo->fetch();  
        $cod_dosificacionfactura = $resultInfo['codigo'];  
        $nroAutorizacion = $resultInfo['nro_autorizacion'];
        $llaveDosificacion = $resultInfo['llave_dosificacion'];
        $fecha_limite_emision = $resultInfo['fecha_limite_emision'];
        if($nroAutorizacion==null || $nroAutorizacion=='' || $nroAutorizacion==' '){                    
            return "11###";//No tiene registrado La dosificación para la facturación
        }else{
            //monto total redondeado
            $monto_total= $importeTotal;
            $totalFinalRedondeado=round($monto_total,0);                    
            //NUMERO CORRELATIVO DE FACTURA
            // echo $sucursalId;
            $nro_correlativo = nro_correlativo_facturas($sucursalId);
            if($nro_correlativo==0){                
                return "11###";//No tiene registrado La dosificación para la facturación

            }else{
                $fechaFactura_x=date('Y-m-d H:i:s');
                    
                if($CodLibretaDetalle>0){
                    $cod_libreta=$CodLibretaDetalle;
                    $estado_libreta=obtenerEstadoLibretaBancaria($cod_libreta);
                    if($estado==0 || $estado==1){
                        $cod_cuenta=obtenerCuentaLibretaBancaria($cod_libreta);
                        //generamos el comprobante estado 1 es que va con cod_cuenta para matar o 0 será el por defecto
                        $cod_comprobante=ejecutarComprobanteSolicitud_tiendaVirtual($nitciCliente,$razonSocial,$items,$monto_total,$nro_correlativo,1,$cod_cuenta);                            
                    }elseif($estado==3){
                        $cod_contracuenta=obtenerContraCuentaLibretaBancaria($cod_libreta);
                        //generamos el comprobante
                        $cod_comprobante=ejecutarComprobanteSolicitud_tiendaVirtual($nitciCliente,$razonSocial,$items,$monto_total,$nro_correlativo,1,$cod_contracuenta);
                    }else{
                        //generamos el comprobante
                        $cod_comprobante=ejecutarComprobanteSolicitud_tiendaVirtual($nitciCliente,$razonSocial,$items,$monto_total,$nro_correlativo,0,0);    
                    }
                }else{
                    //generamos el comprobante
                    $cod_comprobante=ejecutarComprobanteSolicitud_tiendaVirtual($nitciCliente,$razonSocial,$items,$monto_total,$nro_correlativo,0,0);
                }
                if($cod_comprobante==null || $cod_comprobante==''){
                    return "12###";
                }else{
                    // $cod_comprobante=0;
                    //echo "auto:".$nroAutorizacion." - nro_corr:".$nro_correlativo." - nitCliente:".$nitCliente." - fechaFactura:".$fechaFactura." - totalFinalRedondeado:".$totalFinalRedondeado." - llaveDosificacion:".$llaveDosificacion;
                    $controlCode = new ControlCode();
                    $code = $controlCode->generate($nroAutorizacion,//Numero de autorizacion
                    $nro_correlativo,//Numero de factura
                    $nitCliente,//Número de Identificación Tributaria o Carnet de Identidad
                    str_replace('-','',$fechaFactura),//fecha de transaccion de la forma AAAAMMDD
                    $totalFinalRedondeado,//Monto de la transacción
                    $llaveDosificacion//Llave de dosificación
                    );
                    //echo "cod:".$code;
                    $sql="INSERT INTO facturas_venta(cod_sucursal,cod_solicitudfacturacion,cod_unidadorganizacional,cod_area,fecha_factura,fecha_limite_emision,cod_tipoobjeto,cod_tipopago,cod_cliente,cod_personal,razon_social,nit,cod_dosificacionfactura,nro_factura,nro_autorizacion,codigo_control,importe,observaciones,cod_estadofactura,cod_comprobante) 
                      values ('$sucursalId','$cod_solicitudfacturacion','$cod_uo_solicitud','$cod_area_solicitud','$fechaFactura_x','$fecha_limite_emision','$cod_tipoobjeto','$cod_tipopago','$cod_cliente','$cod_personal','$razon_social','$nitCliente','$cod_dosificacionfactura','$nro_correlativo','$nroAutorizacion','$code','$monto_total','$observaciones','1','$cod_comprobante')";
                      // echo $sql;
                    $stmtInsertSoliFact = $dbh->prepare($sql);
                    $flagSuccess=$stmtInsertSoliFact->execute();
                    // $flagSuccess=true;

                    if($flagSuccess){
                        //obtenemos el registro del ultimo insert
                        $stmtNroFac = $dbh->prepare("SELECT codigo from facturas_venta where cod_sucursal=$sucursalId and fecha_factura like '$fechaFactura_x%' and nit=$nitciCliente and razon_social like '%$razonSocial%' order by codigo desc");
                        $stmtNroFac->execute();
                        $resultNroFact = $stmtNroFac->fetch();    
                        $cod_facturaVenta = $resultNroFact['codigo'];
                        if($CodLibretaDetalle>0){
                            $cod_libreta=$CodLibretaDetalle;
                            //si es de tipo deposito en cuenta insertamos en libreta bancaria
                            $sqlUpdateLibreta="UPDATE libretas_bancariasdetalle SET cod_factura=$cod_facturaVenta where codigo=$cod_libreta";
                            $stmtUpdateLibreta = $dbh->prepare($sqlUpdateLibreta);                            
                            $stmtUpdateLibreta->execute();
                            $number_of_rows  = $stmtUpdateLibreta->rowCount();

                            $sqlUpdateFac="UPDATE facturas_venta SET cod_libretabancariadetalle=$cod_libreta where codigo=$cod_facturaVenta";
                            $stmtUpdateFac = $dbh->prepare($sqlUpdateFac);
                            $flagSuccessFac=$stmtUpdateFac->execute(); 
                            
                            if($number_of_rows==0 || $number_of_rows==''){
                                $sqldeleteCabeceraFactura="DELETE from facturas_venta where codigo=$cod_facturaVenta";
                                $stmtDeleteCAbeceraFactura = $dbh->prepare($sqldeleteCabeceraFactura);
                                $flagSuccess=$stmtDeleteCAbeceraFactura->execute();
                                return "17###";
                            }
                        }
                        foreach ($items as $valor) {
                            $suscripcionId=$valor['suscripcionId'];
                            $pagoCursoId=$valor['pagoCursoId'];
                            $detalle=$valor['detalle'];
                            $precioUnitario=$valor['precioUnitario'];
                            $cantidad=$valor['cantidad'];
                            $precio_x=$cantidad*$precioUnitario;
                            $cod_claservicio_x=$pagoCursoId;
                            $stmtInsertSoliFactDet = $dbh->prepare("INSERT INTO facturas_ventadetalle(cod_facturaventa,cod_claservicio,cantidad,precio,descripcion_alterna,descuento_bob,suscripcionId) 
                             values ('$cod_facturaVenta','$cod_claservicio_x','$cantidad','$precio_x','$detalle',0,$suscripcionId)");
                             $flagSuccess=$stmtInsertSoliFactDet->execute();                         
                        }
                        if($flagSuccess){                            
                            return "0###".$cod_facturaVenta;
                        }
                    }    
                }
                
            }
        }    
    } catch(PDOException $ex){
        // echo "Un error ocurrio".$ex->getMessage();
        // echo "Error : ".$error;
        return "12###";
    }

}
?>
