<?php
	function generar_factura($codigo,$string_cod_Det,$cod_tipopago,$cod_sucursal,$cod_libreta,$cod_estadocuenta,$nroAutorizacion,$nitCliente,$fecha_actual,$llaveDosificacion,$cod_unidadorganizacional,$cod_area,$fecha_limite_emision,$cod_tipoobjeto,$cod_cliente,$cod_personal,$razon_social,$cod_dosificacionfactura,$observaciones,$globalUser,$tipo_solicitud,$cod_simulacion_servicio,$variable_controlador,$ci_estudiante){


		// echo $cod_unidadorganizacional."-".$cod_area."-".$cod_tipoobjeto."-".$cod_cliente."-".$razon_social."-".$cod_dosificacionfactura."-".$cod_personal;
    require_once __DIR__.'/../conexion.php';
    if($variable_controlador==1){
	    require '../assets/phpqrcode/qrlib.php';
	    include '../assets/controlcode/sin/ControlCode.php';
    }

    //require_once 'configModule.php';
    require_once __DIR__.'/../functions.php';
    require_once __DIR__.'/../functionsGeneral.php';    
	require_once 'executeComprobante_factura.php';
	$dbh = new Conexion();
	$fecha_actual_cH=date('Y-m-d H:i:s');
	$estado_ibnorca=0;
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
        //verificamos si tiene estado de cuenta u otros
        // $cont_tipospago=0
        // if($cod_libreta!=0){
        //     $cont_tipospago++;
        // }if($cod_libreta!=0){
        //     $cont_tipospago++;
        // }
        // $cod_comprobante=ejecutarComprobanteSolicitud($codigo,$nro_correlativo,$cod_libreta,$cod_estadocuenta);

     //    if($cod_tipopago==$cod_tipopago_deposito){//deposito en cuenta?        
     //        if($cod_libreta!=0){//si viene sin cod libreta no se toma en cuetna el deposito en cuenta
     //            $cod_comprobante=ejecutarComprobanteSolicitud($codigo,$nro_correlativo,1,$cod_libreta,0);
	    //     }else{
	    //         $cod_comprobante=ejecutarComprobanteSolicitud($codigo,$nro_correlativo,0,0,0);
	    //     }
	    // }elseif($cod_tipopago==$cod_tipopago_tarjetas){
	    //     $cod_comprobante=ejecutarComprobanteSolicitud($codigo,$nro_correlativo,2,0,0);
	    // }elseif($cod_tipopago==$cod_tipopago_anticipo){
     //        $cod_comprobante=ejecutarComprobanteSolicitud($codigo,$nro_correlativo,3,0,$cod_estadocuenta);
     //    }else{	    	
	    //     $cod_comprobante=ejecutarComprobanteSolicitud($codigo,$nro_correlativo,0,0,0);
	    // }
	    // echo "auto:".$nroAutorizacion." - nro_corr:".$nro_correlativo." - nitCliente:".$nitCliente." - fecha_actual:".$fecha_actual." - totalFinalRedondeado:".$totalFinalRedondeado." - llaveDosificacion:".$llaveDosificacion;
        // if($cod_comprobante!=0 && $cod_comprobante!=-1){
            $controlCode = new ControlCode();
            $code = $controlCode->generate($nroAutorizacion,//Numero de autorizacion
            $nro_correlativo,//Numero de factura
            $nitCliente,//Número de Identificación Tributaria o Carnet de Identidad
            str_replace('-','',$fecha_actual),//fecha de transaccion de la forma AAAAMMDD
            $totalFinalRedondeado,//Monto de la transacción
            $llaveDosificacion//Llave de dosificación
            );
            $sql="INSERT INTO facturas_venta(cod_sucursal,cod_solicitudfacturacion,cod_unidadorganizacional,cod_area,fecha_factura,fecha_limite_emision,cod_tipoobjeto,cod_tipopago,cod_cliente,cod_personal,razon_social,nit,cod_dosificacionfactura,nro_factura,nro_autorizacion,codigo_control,importe,observaciones,cod_estadofactura,cod_comprobante,ci_estudiante) 
            values ('$cod_sucursal','$codigo','$cod_unidadorganizacional','$cod_area','$fecha_actual_cH','$fecha_limite_emision','$cod_tipoobjeto','$cod_tipopago','$cod_cliente','$cod_personal','$razon_social','$nitCliente','$cod_dosificacionfactura','$nro_correlativo','$nroAutorizacion','$code','$monto_total','$observaciones','1','0','$ci_estudiante')";
            // echo $sql;
            $stmtInsertSoliFact = $dbh->prepare($sql);
            $flagSuccess=$stmtInsertSoliFact->execute();
            if($flagSuccess){
                //obtenemos el registro del ultimo insert
                $stmtNroFac = $dbh->prepare("SELECT codigo from facturas_venta where cod_solicitudfacturacion=$codigo order by codigo desc LIMIT 1");
                $stmtNroFac->execute();
                $resultNroFact = $stmtNroFac->fetch();    
                $cod_facturaVenta = $resultNroFact['codigo'];
                if($cod_libreta!=0){
                    // $array_libreta=explode(',',$cod_libreta);
                    // for($i=0;$i<sizeof($array_libreta);$i++){
                    //     $cod_libreta_x= $array_libreta[$i];
                    //     $sqlUpdateLibreta="INSERT into libretas_bancariasdetalle_facturas(cod_libretabancariadetalle,cod_facturaventa) values ($cod_libreta_x,$cod_facturaVenta)";
                    //     $stmtUpdateLibreta = $dbh->prepare($sqlUpdateLibreta);
                    //     $stmtUpdateLibreta->execute();
                    // }
                    // $cod_libreta=$_GET["cod_libreta"];
                    //si es de tipo deposito en cuenta insertamos en libreta bancaria
                    // $sqlUpdateLibreta="UPDATE libretas_bancariasdetalle SET cod_factura=$cod_facturaVenta where codigo=$cod_libreta";
                    // $stmtUpdateLibreta = $dbh->prepare($sqlUpdateLibreta);
                    // $flagSuccess=$stmtUpdateLibreta->execute();
                    // $sqlUpdateFac="UPDATE facturas_venta SET cod_libretabancariadetalle=$cod_libreta where codigo=$cod_facturaVenta";
                    // $stmtUpdateFac = $dbh->prepare($sqlUpdateFac);
                    // $flagSuccessFac=$stmtUpdateFac->execute(); 
                }           
                //insertamos detalle
                $stmt = $dbh->prepare("SELECT sf.* from solicitudes_facturaciondetalle sf where sf.cod_solicitudfacturacion=$codigo and codigo in ($string_cod_Det)");
                $stmt->execute();
                while ($row = $stmt->fetch()) 
                {   
                    $cod_claservicio_x=$row['cod_claservicio'];
                    $cantidad_x=$row['cantidad'];
                    $precio_x=$row['precio'];
                    $descuento_bob_x=$row['descuento_bob'];
                    $cod_curso_x=$row['cod_curso'];//solo se guarda este campo cuando es grupal
                    $ci_estudiante_x=$row['ci_estudiante'];//solo se guarda este campo cuando es grupal

                    if($tipo_solicitud==2){// la solicitud pertence capacitacion estudiantes
                        $datos=resgistrar_pago_curso($cod_cliente,$cod_simulacion_servicio,$cod_claservicio_x,$precio_x,$codigo);
                        $estado_x=$datos["estado"];
                        $mensaje_x=$datos["mensaje"];
                        if(!$estado_x){//registro correcto webservice
                            $estado_ibnorca++;
                            $stmtDelte = $dbh->prepare("DELETE from facturas_venta where codigo=$cod_facturaVenta");
                            $stmtDelte->execute();
                            $estado_ibnorca++;
                            // $sqldeletecomprobante="DELETE from comprobantes where codigo=$cod_comprobante";
                            // $stmtDeleteCopmprobante = $dbh->prepare($sqldeletecomprobante);
                            // $flagSuccess=$stmtDeleteCopmprobante->execute();
                            // $sqldeletecomprobanteDet="DELETE from comprobantes_detalle where cod_comprobante=$cod_comprobante";
                            // $stmtDeleteComprobanteDet = $dbh->prepare($sqldeletecomprobanteDet);
                            // $flagSuccess=$stmtDeleteComprobanteDet->execute();
                            break;
                        }
                    }elseif($tipo_solicitud==7){//pago grupal                     
                        $datos=resgistrar_pago_curso($ci_estudiante_x,$cod_curso_x,$cod_claservicio_x,$precio_x,$codigo);
                        $estado_x=$datos["estado"];
                        $mensaje_x=$datos["mensaje"];                        
                        if(!$estado_x){//registro correcto webservice
                            $estado_ibnorca++;
                            $stmtDelte = $dbh->prepare("DELETE from facturas_venta where codigo=$cod_facturaVenta");
                            $stmtDelte->execute();
                            // $sqldeletecomprobante="DELETE from comprobantes where codigo=$cod_comprobante";
                            // $stmtDeleteCopmprobante = $dbh->prepare($sqldeletecomprobante);
                            // $flagSuccess=$stmtDeleteCopmprobante->execute();
                            // $sqldeletecomprobanteDet="DELETE from comprobantes_detalle where cod_comprobante=$cod_comprobante";
                            // $stmtDeleteComprobanteDet = $dbh->prepare($sqldeletecomprobanteDet);
                            // $flagSuccess=$stmtDeleteComprobanteDet->execute();
                            $estado_ibnorca++;
                            break;
                        }
                    }
                    if($estado_ibnorca==0){//sin errores en el servicio web
                        $precio_x=$precio_x+$descuento_bob_x/$cantidad_x;//se registró el precio total incluido el descuento, para la factura necesitamos el precio unitario y tambien el descuetno unitario, ya que se registro el descuento total * cantidad
                        $descripcion_alterna_x=$row['descripcion_alterna'];            
                        $stmtInsertSoliFactDet = $dbh->prepare("INSERT INTO facturas_ventadetalle(cod_facturaventa,cod_claservicio,cantidad,precio,descripcion_alterna,descuento_bob,suscripcionId,ci_estudiante) 
                        values ('$cod_facturaVenta','$cod_claservicio_x','$cantidad_x','$precio_x','$descripcion_alterna_x',$descuento_bob_x,0,'$ci_estudiante')");
                        $flagSuccess=$stmtInsertSoliFactDet->execute();
                    }
                }
                if($estado_ibnorca==0){
                    return "0";
                }else{
                    return "1";
                }
            }else{
                return "1";
            }
        // }else{
        //     if($cod_comprobante==0){
        //         return "1";    
        //     }else{
        //         return "-1";
        //     }
        // }
	}
	



?>