<?php
	function generar_factura($codigo,$string_cod_Det,$cod_tipopago,$cod_sucursal,$cod_libreta,$cod_estadocuenta,$nroAutorizacion,$nitCliente,$fecha_actual,$llaveDosificacion,$cod_unidadorganizacional,$cod_area,$fecha_limite_emision,$cod_tipoobjeto,$cod_cliente,$cod_personal,$razon_social,$cod_dosificacionfactura,$observaciones,$observaciones_2,$globalUser,$tipo_solicitud,$cod_simulacion_servicio,$variable_controlador,$ci_estudiante){
        require_once __DIR__.'/../conexion.php';
        if($variable_controlador==1){
    	    require '../assets/phpqrcode/qrlib.php';
    	    include '../assets/controlcode/sin/ControlCode.php';
        }
        //require_once 'configModule.php';
        require_once __DIR__.'/../functions.php';
        require_once __DIR__.'/../functionsGeneral.php';    
    	// require_once 'executeComprobante_factura.php';
    	$dbh = new Conexion();
    	$fecha_actual_cH=date('Y-m-d H:i:s');
    	$estado_ibnorca=0;
        //rollback inicia
        $SQLDATOSINSTERT=[];
        $sqlCommit="SET AUTOCOMMIT=0;";
        $stmtCommit = $dbh->prepare($sqlCommit);
        $stmtCommit->execute();
        try{
        	//monto total redondeado
        	$sqlMontoTotal="SELECT sum(sf.precio*sf.cantidad) as monto from solicitudes_facturaciondetalle sf where sf.cod_solicitudfacturacion=$codigo and codigo in ($string_cod_Det)";
        	// echo $sqlMontoTotal;
            $stmtMontoTotal = $dbh->prepare($sqlMontoTotal);
            $stmtMontoTotal->execute();
            $resultMontoTotal = $stmtMontoTotal->fetch();   
            $monto_total= $resultMontoTotal['monto'];//total para el comprobante comprobante que ya esta aplicado el descuetno
            $totalFinalRedondeado=round($monto_total,0);
            $nro_correlativo = nro_correlativo_facturas($cod_sucursal);//el que introduciremos
            $cod_tipopago_deposito=obtenerValorConfiguracion(55);//tipo de pago deposito en cuenta
            $cod_tipopago_tarjetas=obtenerValorConfiguracion(59);
            $cod_tipopago_anticipo=obtenerValorConfiguracion(64);//tipo de pago anticipo
            
        	    // echo "auto:".$nroAutorizacion." - nro_corr:".$nro_correlativo." - nitCliente:".$nitCliente." - fecha_actual:".$fecha_actual." - totalFinalRedondeado:".$totalFinalRedondeado." - llaveDosificacion:".$llaveDosificacion;
            $code=0;// para el siat ya no se usa codigo de control


            // $controlCode = new ControlCode();
            // $code = $controlCode->generate($nroAutorizacion,//Numero de autorizacion
            // $nro_correlativo,//Numero de factura
            // $nitCliente,//Número de Identificación Tributaria o Carnet de Identidad
            // str_replace('-','',$fecha_actual),//fecha de transaccion de la forma AAAAMMDD
            // $totalFinalRedondeado,//Monto de la transacción
            // $llaveDosificacion//Llave de dosificación
            // );

            //SACAMOS EL NRO CORRELATIVO DEL CORREO
            $nro_correlativoCorreo = nro_correlativo_correocredito($cod_sucursal,$cod_tipopago);


            $sql="INSERT INTO facturas_venta(cod_sucursal,cod_solicitudfacturacion,cod_unidadorganizacional,cod_area,fecha_factura,fecha_limite_emision,cod_tipoobjeto,cod_tipopago,cod_cliente,cod_personal,razon_social,nit,cod_dosificacionfactura,nro_factura,nro_autorizacion,codigo_control,importe,observaciones,cod_estadofactura,cod_comprobante,ci_estudiante,glosa_factura3,created_at,created_by,nro_correlativocorreo) 
                values ('$cod_sucursal','$codigo','$cod_unidadorganizacional','$cod_area',NOW(),'$fecha_limite_emision','$cod_tipoobjeto','$cod_tipopago','$cod_cliente','$cod_personal','$razon_social','$nitCliente','$cod_dosificacionfactura','$nro_correlativo','$nroAutorizacion','$code','$monto_total','$observaciones','1','0','$ci_estudiante','$observaciones_2',NOW(),$globalUser,'$nro_correlativoCorreo')";
            
            //echo $sql;
            
            $stmtInsertSoliFact = $dbh->prepare($sql);
            $flagSuccess=$stmtInsertSoliFact->execute();
            array_push($SQLDATOSINSTERT,$flagSuccess);
            if($flagSuccess){
                //obtenemos el registro del ultimo insert
                $stmtNroFac = $dbh->prepare("SELECT codigo from facturas_venta where cod_solicitudfacturacion=$codigo order by codigo desc LIMIT 1");
                $stmtNroFac->execute();
                $resultNroFact = $stmtNroFac->fetch();    
                $cod_facturaVenta = $resultNroFact['codigo'];
                //insertamos detalle
                $stmt5 = $dbh->prepare("SELECT sf.* from solicitudes_facturaciondetalle sf where sf.cod_solicitudfacturacion=$codigo and codigo in ($string_cod_Det)");
                $stmt5->execute();
                while ($row = $stmt5->fetch()) 
                {   
                    $cod_claservicio_x=$row['cod_claservicio'];
                    $cantidad_x=$row['cantidad'];
                    $precio_x=$row['precio'];
                    $descuento_bob_x=$row['descuento_bob'];
                    $cod_curso_x=$row['cod_curso'];//solo se guarda este campo cuando es grupal
                    $ci_estudiante_x=$row['ci_estudiante'];
                    $estado_x=true;
                    if($tipo_solicitud==2){// la solicitud pertence capacitacion estudiantes
                        //echo $ci_estudiante_x."-".$cod_simulacion_servicio."-".$cod_claservicio_x."-".$precio_x."-".$codigo;
                        $ci_estudiante_x=$ci_estudiante;
                        $datos=resgistrar_pago_curso($ci_estudiante,$cod_simulacion_servicio,$cod_claservicio_x,$precio_x,$codigo);
                        if(isset($datos["estado"])){
                            $estado_x=$datos["estado"];
                            $mensaje_x=$datos["mensaje"];                    
                        }else{
                            $estado_x=false;
                            $mensaje_x="";
                        }
                        
                    }elseif($tipo_solicitud==7){//pago grupal                     
                        $datos=resgistrar_pago_curso($ci_estudiante_x,$cod_curso_x,$cod_claservicio_x,$precio_x,$codigo);
                        // $estado_x=$datos["estado"];
                        // $mensaje_x=$datos["mensaje"];                        
                        if(isset($datos["estado"])){
                            $estado_x=$datos["estado"];
                            $mensaje_x=$datos["mensaje"];                    
                        }else{
                            $estado_x=false;
                            $mensaje_x="";
                        }
                    }
                    if(!$estado_x){//registro correcto webservice                            
                        $stmtDelte = $dbh->prepare("DELETE from facturas_venta where codigo=$cod_facturaVenta");
                        $stmtDelte->execute();                            
                        $estado_ibnorca++;
                        break;
                    }
                    if($estado_ibnorca==0){//sin errores en el servicio web
                        $precio_x=$precio_x+$descuento_bob_x/$cantidad_x;//se registró el precio total incluido el descuento, para la factura necesitamos el precio unitario y tambien el descuetno unitario, ya que se registro el descuento total * cantidad
                        $descripcion_alterna_x=$row['descripcion_alterna'];            
                        $sqlDetalleVenta="INSERT INTO facturas_ventadetalle(cod_facturaventa,cod_claservicio,cantidad,precio,descripcion_alterna,descuento_bob,suscripcionId,ci_estudiante) 
                        values ('$cod_facturaVenta','$cod_claservicio_x','$cantidad_x','$precio_x','$descripcion_alterna_x',$descuento_bob_x,0,'$ci_estudiante_x')";
                        //echo $sqlDetalleVenta;
                        $stmtInsertSoliFactDet = $dbh->prepare($sqlDetalleVenta);
                        $flagSuccess=$stmtInsertSoliFactDet->execute();
                        array_push($SQLDATOSINSTERT,$flagSuccess);
                    }
                }
                $sw_controlador="0";//verifica si todo esta okey
                $errorInsertar=0;                
                for ($flag=0; $flag < count($SQLDATOSINSTERT); $flag++) { 
                    if($SQLDATOSINSTERT[$flag]==false){
                        $errorInsertar++;
                        // echo $flag;
                        break;
                    }
                } 
                if($errorInsertar!=0){
                    $sw_controlador="1";//hubo algun error
                    $sqlRolBack="ROLLBACK;";
                    $stmtRolBack = $dbh->prepare($sqlRolBack);
                    $stmtRolBack->execute();
                }
                $sqlCommit="COMMIT;SET AUTOCOMMIT=1;";
                $stmtCommit = $dbh->prepare($sqlCommit);
                $stmtCommit->execute();
                if($estado_ibnorca==0){
                    $sw_controlador="0";//todo okey
                }else{
                    $sw_controlador="1";//hubo algun error
                }
            }else{
                $sw_controlador="1";//hubo algun error
            }
            return $sw_controlador;
        }
        catch(PDOException $ex){         
            $sqlRolBack="ROLLBACK;";
            $stmtRolBack = $dbh->prepare($sqlRolBack);
            $stmtRolBack->execute();
            $sqlCommit="COMMIT;SET AUTOCOMMIT=1;";
            $stmtCommit = $dbh->prepare($sqlCommit);
            $stmtCommit->execute();
            return "1";//hubo algun error
        }
	}
?>