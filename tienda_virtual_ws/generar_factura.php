<?php //ESTADO FINALIZADO

// $IdSucursal=5; // ID Sucursal
// $FechaFactura='2020-05-09'; // fecha a factura
// $Identificacion=1020113024; //nit o ci de cliente
// $RazonSocial='Juan Gabriel'; //razon social
// $ImporteTotal=260.5; //importe total

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
// $Detalle= array($Objeto_detalle,$Objeto_detalle2);


// ejecutarGenerarFactura($IdSucursal,$FechaFactura,$Identificacion,$RazonSocial,$ImporteTotal,$Detalle);

function ejecutarGenerarFactura($IdSucursal,$FechaFactura,$Identificacion,$RazonSocial,$ImporteTotal,$Detalle){
    require_once __DIR__.'/../conexion.php';
    require '../assets/phpqrcode/qrlib.php';
    include '../assets/controlcode/sin/ControlCode.php';

    //require_once 'configModule.php';
    require_once __DIR__.'/../functions.php';
    require_once __DIR__.'/../functionsGeneral.php';
    // require_once 'executeComprobante_factura.php';

    $dbh = new Conexion();
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);//try
    set_time_limit(300);
    session_start();

    //$globalUser=$_SESSION["globalUser"];
    //RECIBIMOS LAS VARIABLES    
    try{
        //verificamos si se registró las cuentas en los tipos de pago 
        $stmtVerif_tipopago = $dbh->prepare("SELECT (select c.cod_cuenta from tipos_pago_contabilizacion c where c.cod_tipopago=t.codigo) as cuenta from tipos_pago t where t.cod_estadoreferencial=1");
        $stmtVerif_tipopago->execute();
        $cont_tipopago=0;
        while ($row = $stmtVerif_tipopago->fetch())     
        {
            $cod_cuenta=$row['cuenta'];
            if($cod_cuenta==null){
                $cont_tipopago++;
            }
        }
        $stmtVerif_area = $dbh->prepare("SELECT cod_cuenta_ingreso from areas a where a.cod_estado=1 and areas_ingreso=1");
        $stmtVerif_area->execute();
        $cont_areas=0;
        while ($row = $stmtVerif_area->fetch())    
        {
            $cod_cuenta=$row['cod_cuenta_ingreso'];
            if($cod_cuenta==null){
                $cont_areas++;
            }
        }
        if($cont_tipopago!=0){//falta asociar cuenta a tipos de pago
            //echo 2;
            return "2###";
        }elseif($cont_areas!=0){//falta asociar alguna cuenta en areas
            //echo 3;
            return "3###";
        }else{//cuando todo esta en orden
            // verificamos si ya se registro la factura
            // $sqlVerf="SELECT codigo from facturas_venta where cod_sucursal=$IdSucursal and fecha_factura like '$FechaFactura%' and nit=$Identificacion and razon_social like '%$RazonSocial%'";
            // // echo $sqlVerf;
            // $stmtVerif = $dbh->prepare($sqlVerf);
            // $stmtVerif->execute();
            // $resultVerif = $stmtVerif->fetch();    
            // $codigo_facturacion = $resultVerif['codigo'];
            $codigo_facturacion=null;
            if($codigo_facturacion==null){//no se registró
                               
                $cod_solicitudfacturacion = 0;
                $cod_unidadorganizacional = $IdSucursal;
                $cod_area = 0;
                $cod_tipoobjeto = 0;
                $cod_tipopago = 0;
                $cod_cliente = 0;
                $cod_personal = 0;
                $razon_social = $RazonSocial;
                $nitCliente = $Identificacion;
                $observaciones = '';
                $nombre_cliente = $RazonSocial;


                // echo "uo:",$cod_unidadorganizacional."<br>";
                $fecha_actual=$FechaFactura;
                $fecha_actual_cH=$FechaFactura;
                
                // $fecha_actual_cH=date('Y-m-d H:i:s');
                // $fecha_actual=date($fecha_actual_cH,'Y-m-d');

                $sqlInfo="SELECT d.codigo,d.nro_autorizacion, d.llave_dosificacion,d.fecha_limite_emision
                from dosificaciones_facturas d where d.cod_sucursal='$cod_unidadorganizacional' order by codigo";
                $stmtInfo = $dbh->prepare($sqlInfo);
                // echo $sqlInfo;
                $stmtInfo->execute();
                $resultInfo = $stmtInfo->fetch();  
                $cod_dosificacionfactura = $resultInfo['codigo'];  
                $nroAutorizacion = $resultInfo['nro_autorizacion'];
                $llaveDosificacion = $resultInfo['llave_dosificacion'];
                $fecha_limite_emision = $resultInfo['fecha_limite_emision'];
                if($nroAutorizacion==null || $nroAutorizacion=='' || $nroAutorizacion==' '){
                    // $error = "No tiene registrado La dosificación para la facturación."; 
                    return "6###";//No tiene registrado La dosificación para la facturación                    
                }else{
                    //monto total redondeado
                    $monto_total= $ImporteTotal;
                    $totalFinalRedondeado=round($monto_total,0);
                    // echo "monto total:".$totalFinalRedondeado;
                    //NUMERO CORRELATIVO DE FACTURA
                    $stmtNroFac = $dbh->prepare("SELECT IFNULL(nro_factura+1,1)as correlativo from facturas_venta where cod_sucursal=$cod_unidadorganizacional order by codigo desc LIMIT 1");
                    $stmtNroFac->execute();
                    $resultNroFact = $stmtNroFac->fetch();    
                    $nro_correlativo = $resultNroFact['correlativo'];
                    if($nro_correlativo==null)$nro_correlativo=1;   

                    //generamos el comprobante
                    // $cod_comprobante=ejecutarComprobanteSolicitud($codigo,$nro_correlativo);
                    $cod_comprobante=0;
                    //echo "auto:".$nroAutorizacion." - nro_corr:".$nro_correlativo." - nitCliente:".$nitCliente." - fecha_actual:".$fecha_actual." - totalFinalRedondeado:".$totalFinalRedondeado." - llaveDosificacion:".$llaveDosificacion;
                    $controlCode = new ControlCode();
                    $code = $controlCode->generate($nroAutorizacion,//Numero de autorizacion
                    $nro_correlativo,//Numero de factura
                    $nitCliente,//Número de Identificación Tributaria o Carnet de Identidad
                    str_replace('-','',$fecha_actual),//fecha de transaccion de la forma AAAAMMDD
                    $totalFinalRedondeado,//Monto de la transacción
                    $llaveDosificacion//Llave de dosificación
                    );
                    //echo "cod:".$code;
                    $sql="INSERT INTO facturas_venta(cod_sucursal,cod_solicitudfacturacion,cod_unidadorganizacional,cod_area,fecha_factura,fecha_limite_emision,cod_tipopago,cod_cliente,cod_personal,razon_social,nit,cod_dosificacionfactura,nro_factura,nro_autorizacion,codigo_control,importe,observaciones,cod_estadofactura,cod_comprobante) 
                      values ('$cod_unidadorganizacional','$cod_solicitudfacturacion','$cod_unidadorganizacional','$cod_area','$fecha_actual_cH','$fecha_limite_emision','$cod_tipopago','$cod_cliente','$cod_personal','$razon_social','$nitCliente','$cod_dosificacionfactura','$nro_correlativo','$nroAutorizacion','$code','$totalFinalRedondeado','$observaciones','1','$cod_comprobante')";
                      // echo $sql;
                    $stmtInsertSoliFact = $dbh->prepare($sql);
                    $flagSuccess=$stmtInsertSoliFact->execute();
                    // $flagSuccess=true;

                    if($flagSuccess){
                        //obtenemos el registro del ultimo insert
                        $stmtNroFac = $dbh->prepare("SELECT codigo from facturas_venta where cod_sucursal=$IdSucursal and fecha_factura like '$FechaFactura%' and nit=$Identificacion and razon_social like '%$RazonSocial%'");
                        $stmtNroFac->execute();
                        $resultNroFact = $stmtNroFac->fetch();    
                        $cod_facturaVenta = $resultNroFact['codigo'];
                        ////ahora el detalle de la factura
                        // $cod_facturaVenta=0;
                        
                        foreach ($Detalle as $valor) {                         

                            // $suscripcionId=$valor->suscripcionId;
                            // $pagoCursoId=$valor->pagoCursoId;
                            // $detalle_x=$valor->detalle;
                            // $precioUnitario=$valor->precioUnitario;
                            // $cantidad=$valor->cantidad;

                            $suscripcionId=$valor['suscripcionId'];
                            $pagoCursoId=$valor['pagoCursoId'];
                            $detalle_x=$valor['detalle'];
                            $precioUnitario=$valor['precioUnitario'];
                            $cantidad=$valor['cantidad'];
                            $precio_x=$cantidad*$precioUnitario;
                            $cod_claservicio_x=$pagoCursoId;
                            $stmtInsertSoliFactDet = $dbh->prepare("INSERT INTO facturas_ventadetalle(cod_facturaventa,cod_claservicio,cantidad,precio,descripcion_alterna,descuento_bob,suscripcionId) 
                             values ('$cod_facturaVenta','$cod_claservicio_x','$cantidad','$precio_x','$detalle_x',0,$suscripcionId)");
                             $flagSuccess=$stmtInsertSoliFactDet->execute(); 

                            //echo "suscripcionId:".$suscripcionId." - pagoCursoId:".$pagoCursoId." - detalle:".$detalle_x." - Precio:".$precio_x." -Canti:".$cantidad."<br>";
                        }
                        if($flagSuccess){
                            // echo 1;
                            return "1###".$cod_facturaVenta;
                        }
                    }
                }
            }else{//ya se registro
                //echo 4;
                return "4###";//ya se genero la factura
            }
        }
        ?>

    <?php 
    } catch(PDOException $ex){
        echo "Un error ocurrio".$ex->getMessage();
        echo "Error : ".$error;
    }

}
?>
