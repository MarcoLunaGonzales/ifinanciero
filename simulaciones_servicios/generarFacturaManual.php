<?php
require_once __DIR__.'/../conexion.php';
require '../assets/phpqrcode/qrlib.php';
include '../assets/controlcode/sin/ControlCode.php';

//require_once 'configModule.php';
require_once __DIR__.'/../functions.php';
require_once __DIR__.'/../functionsGeneral.php';
require_once 'executeComprobante_factura.php';

$dbh = new Conexion();
$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);//try
set_time_limit(300);
session_start();
$globalUser=$_SESSION["globalUser"];
//RECIBIMOS LAS VARIABLES
$codigo=$_POST['cod_solicitudfacturacion'];
$nro_factura=$_POST['nro_factura'];

$nroAutorizacion=$_POST['nro_autorizacion'];
// $llaveDosificacion=$_POST['llave_dosificacion'];
// $fecha_limite_emision=$_POST['fecha_limite_emision'];
$fecha_factura=$_POST['fecha_factura'];
$nit_cliente=$_POST['nit_cliente'];
$razon_social=$_POST['razon_social'];

$estado_ibnorca=0;
// $cod_control=$_POST['cod_control'];
try{    
    //verificamos si se registró las cuentas en los tipos de pago 
    $cont_tipopago=verificamos_cuentas_tipos_pagos();
    //verificamos si se registró las cuentas en LAS AREAS DE INGRESO 
    $cont_areas=verificamos_cuentas_areas();
    if($cont_tipopago!=0){//falta asociar cuenta a tipos de pago 
    	echo 2;
    }elseif($cont_areas!=0){//falta asociar alguna cuenta en areas 
    	echo 3;
    }else{//cuando todo esta en orden
        // verificamos si ya se registro la factura
        // echo $codigo;
        // verificamos si ya se registro la factura
        $codigo_facturacion=verificamosFacturaDuplicada($codigo);
        // if($codigo_facturacion==null){//no se registró
            //datos de la solicitud de facturacion
            // $stmtInfo = $dbh->prepare("SELECT sf.*,t.nombre as nombre_cliente FROM solicitudes_facturacion sf,clientes t  where sf.cod_cliente=t.codigo and sf.codigo=$codigo");
            // $stmtInfo->execute();
            // $resultInfo = $stmtInfo->fetch();    
            // $cod_simulacion_servicio = $resultInfo['cod_simulacion_servicio'];
            // $cod_unidadorganizacional = $resultInfo['cod_unidadorganizacional'];
            // $cod_area = $resultInfo['cod_area'];
            // $cod_tipoobjeto = $resultInfo['cod_tipoobjeto'];
            // $cod_tipopago = $resultInfo['cod_tipopago'];
            // $cod_cliente = $resultInfo['cod_cliente'];
            // $cod_personal = $resultInfo['cod_personal'];
            // $razon_social = $razon_social;
            // $nitCliente = $nit_cliente;
            // $observaciones = $resultInfo['observaciones'];
            // $nombre_cliente = $resultInfo['nombre_cliente'];
            // $tipo_solicitud = $resultInfo['tipo_solicitud'];//1 tcp - 2 capacitacion - 3 servicios - 4 manual - 5 venta de normas
            // $ci_estudiante_p = $resultInfo['ci_estudiante'];
            // if($nombre_cliente==null || $nombre_cliente==''){//no hay registros con ese dato
                $stmtInfo = $dbh->prepare("SELECT sf.* FROM solicitudes_facturacion sf where sf.codigo=$codigo");
                $stmtInfo->execute();
                $resultInfo = $stmtInfo->fetch();    
                $cod_simulacion_servicio = $resultInfo['cod_simulacion_servicio'];
                $cod_unidadorganizacional = $resultInfo['cod_unidadorganizacional'];
                $cod_area = $resultInfo['cod_area'];
                $cod_tipoobjeto = $resultInfo['cod_tipoobjeto'];
                $cod_tipopago = $resultInfo['cod_tipopago'];
                $cod_cliente = $resultInfo['cod_cliente'];
                $cod_personal = $resultInfo['cod_personal'];
                $razon_social = $razon_social;
                $nitCliente = $nit_cliente;
                $observaciones = $resultInfo['observaciones'];
                $nombre_cliente = $resultInfo['razon_social'];
                $tipo_solicitud = $resultInfo['tipo_solicitud'];//1 tcp - 2 capacitacion - 3 servicios - 4 manual - 5 venta de normas
                $ci_estudiante_p = $resultInfo['ci_estudiante'];
            // }
            $cod_sucursal=obtenerSucursalCodUnidad($cod_unidadorganizacional);
            if($cod_sucursal==null || $cod_sucursal==''){//sucursal no encontrada
            	echo 5;
            }else{                
                // $fecha_actual=date('Y-m-d');
                $fecha_actual_cH=$fecha_factura;
                $cod_dosificacionfactura = 0;
                // $nroAutorizacion = $nro_autorizacion;
                // $llaveDosificacion = $resultInfo['llave_dosificacion'];
                // $fecha_limite_emision = $resultInfo['fecha_limite_emision'];

				//monto total redondeado
                $sqlMontos="SELECT codigo,importe,nro_factura from facturas_venta where cod_solicitudfacturacion=$codigo and cod_estadofactura=1 ORDER BY codigo desc";
                // echo $sqlMontos;
                $stmtFactMontoTotal = $dbh->prepare($sqlMontos);
                $stmtFactMontoTotal->execute();
                $importe_fact_x=0;$cont_facturas=0;$cadenaFacturas="";$cadenaCodFacturas="";
                while ($row_montos = $stmtFactMontoTotal->fetch()){
                  $importe_fact_x+=$row_montos['importe'];
                  // $cadenaFacturas.=$row_montos['nro_factura']." - ";
                  // $cadenaCodFacturas.=$row_montos['codigo'].",";
                  // $cont_facturas++;
                }
                if($cadenaCodFacturas==""){
                    $cadenaCodFacturas=0;
                }
                $stmtMontoTotal = $dbh->prepare("SELECT sum(sf.precio*sf.cantidad) as monto from solicitudes_facturaciondetalle sf 
                where sf.cod_solicitudfacturacion=$codigo");
                $stmtMontoTotal->execute();
                $resultMontoTotal = $stmtMontoTotal->fetch();   
                $monto_total=$resultMontoTotal['monto']-$importe_fact_x;
                $totalFinalRedondeado=round($monto_total,0);
				//NUMERO CORRELATIVO DE FACTURA
				// $stmtNroFac = $dbh->prepare("SELECT IFNULL(nro_factura+1,1)as correlativo from facturas_venta where cod_sucursal='$cod_sucursal' order by codigo desc LIMIT 1");
				// $stmtNroFac->execute();
				// $resultNroFact = $stmtNroFac->fetch();    
				// $nro_correlativo = $resultNroFact['correlativo'];

				// if($nro_correlativo==null || $nro_correlativo=='')$nro_correlativo=1;   
				//generamos el comprobante
                if(isset($_POST["cod_libreta"])){
                    $cod_libreta=$_POST["cod_libreta"];
                }else{
                    $cod_libreta=0;
                }
                if(isset($_POST["cod_estadocuenta"])){
                    $cod_estadocuenta=$_POST["cod_estadocuenta"];
                }else{
                    $cod_estadocuenta=0;
                }
                if(isset($_POST["cuenta_auxiliar"])){
                    $cuenta_auxiliar=$_POST["cuenta_auxiliar"];
                }else{
                    $cuenta_auxiliar=0;
                }
                $cadena_factura="F ".$nro_factura;                
                $cod_comprobante=ejecutarComprobanteSolicitud($codigo,$cadena_factura,$nro_factura,$cod_libreta,$cod_estadocuenta,$cuenta_auxiliar);
                if($cod_comprobante!=0){
                    $sql="INSERT INTO facturas_venta(cod_sucursal,cod_solicitudfacturacion,cod_unidadorganizacional,cod_area,fecha_factura,fecha_limite_emision,cod_tipoobjeto,cod_tipopago,cod_cliente,cod_personal,razon_social,nit,cod_dosificacionfactura,nro_factura,nro_autorizacion,codigo_control,importe,observaciones,cod_estadofactura,cod_comprobante,ci_estudiante) 
                    values ('$cod_sucursal','$codigo','$cod_unidadorganizacional','$cod_area','$fecha_actual_cH',null,'$cod_tipoobjeto','$cod_tipopago','$cod_cliente','$cod_personal','$razon_social','$nitCliente','$cod_dosificacionfactura','$nro_factura','$nroAutorizacion',null,'$monto_total','$observaciones','4',$cod_comprobante,'$ci_estudiante_p')";
                    // echo $sql;
                    $stmtInsertSoliFact = $dbh->prepare($sql);
                    $flagSuccess=$stmtInsertSoliFact->execute();
                    if($flagSuccess){
                        //obtenemos el registro del ultimo insert
                        // $stmtNroFac = $dbh->prepare("SELECT codigo from facturas_venta where cod_solicitudfacturacion=$codigo order by codigo desc LIMIT 1");
                        // $stmtNroFac->execute();
                        // $resultNroFact = $stmtNroFac->fetch();    
                        // $cod_facturaVenta = $resultNroFact['codigo'];

                        //obtenemos el registro del ultimo insert
                        $stmtNroFac = $dbh->prepare("SELECT codigo from facturas_venta where cod_solicitudfacturacion=$codigo order by codigo desc LIMIT 1");
                        $stmtNroFac->execute();
                        $resultNroFact = $stmtNroFac->fetch();    
                        $cod_facturaVenta = $resultNroFact['codigo'];
                        // if($cod_libreta!=0){
                        //     $array_libreta=explode(',',$cod_libreta);
                        //     for($i=0;$i<sizeof($array_libreta);$i++){
                        //         $cod_libreta_x= $array_libreta[$i];
                        //         $sqlUpdateLibreta="INSERT into libretas_bancariasdetalle_facturas(cod_libretabancariadetalle,cod_facturaventa) values ($cod_libreta_x,$cod_facturaVenta)";
                        //         $stmtUpdateLibreta = $dbh->prepare($sqlUpdateLibreta);
                        //         $stmtUpdateLibreta->execute();
                        //     }                            
                        // } 
                        
                        $stmt = $dbh->prepare("SELECT sf.* from solicitudes_facturaciondetalle sf where sf.cod_solicitudfacturacion=$codigo");
                        $stmt->execute();                    
                        while ($row = $stmt->fetch()) 
                        {                
                            $importe_facturato=0;
                            $cod_claservicio_x=$row['cod_claservicio'];
                            //busacmos el monto ya pagado;
                            $cadenaCodFacturas_x=trim($cadenaCodFacturas,',');                        
                            $sqlMontoFact="SELECT sum(precio) as precio_x from facturas_ventadetalle where cod_facturaventa in ($cadenaCodFacturas_x) and cod_claservicio=$cod_claservicio_x";
                            // echo $sqlMontoFact;
                            $stmtFactMontoFacturado = $dbh->prepare($sqlMontoFact);
                            $stmtFactMontoFacturado->execute();
                            // echo "llegue";
                            $resultMontoFAC = $stmtFactMontoFacturado->fetch();

                            $importe_facturato = $resultMontoFAC['precio_x'];//monto del importe ya pagado
                            // $cod_claservicio_x=$row['cod_claservicio'];
                            $cantidad_x=$row['cantidad'];
                            $precio_x=$row['precio']-$importe_facturato;
                            $descuento_bob_x=$row['descuento_bob'];
                            $cod_curso_x=$row['cod_curso'];
                            //isertamos al servicio web en caso de que sea de capacitacion

                            if($tipo_solicitud==2){// la solicitud pertence capacitacion
                                $datos=resgistrar_pago_curso($cod_cliente,$cod_simulacion_servicio,$cod_claservicio_x,$precio_x,$codigo);
                                $estado_x=$datos["estado"];
                                $mensaje_x=$datos["mensaje"];
                                if(!$estado_x){//registro correcto webservice
                                    $estado_ibnorca++;
                                    $stmtDelte = $dbh->prepare("DELETE from facturas_venta where codigo=$cod_facturaVenta");
                                    $stmtDelte->execute();
                                    $sqldeletecomprobante="DELETE from comprobantes where codigo=$cod_comprobante";
                                    $stmtDeleteCopmprobante = $dbh->prepare($sqldeletecomprobante);
                                    $flagSuccess=$stmtDeleteCopmprobante->execute();
                                    $sqldeletecomprobanteDet="DELETE from comprobantes_detalle where cod_comprobante=$cod_comprobante";
                                    $stmtDeleteComprobanteDet = $dbh->prepare($sqldeletecomprobanteDet);
                                    $flagSuccess=$stmtDeleteComprobanteDet->execute();
                                    break;
                                }
                            }elseif($tipo_solicitud==7){//pago grupal
                                //sacamos el lisgtadl de estudantes
                                $sqlGrupal="SELECT cod_curso,ci_estudiante from solicitudes_facturacion_grupal where cod_solicitudfacturacion=$codigo and cod_curso=$cod_curso_x limit 1";
                                $stmtGrupal->execute();
                                while ($rowGrupal = $stmtGrupal->fetch()) 
                                {
                                    $cod_curso_x=$rowGrupal['cod_curso'];
                                    $ci_estudiante_x=$rowGrupal['ci_estudiante'];
                                    $datos=resgistrar_pago_curso($ci_estudiante_x,$cod_curso_x,$cod_claservicio_x,$precio_x,$codigo);
                                    $estado_x=$datos["estado"];
                                    $mensaje_x=$datos["mensaje"];
                                    if(!$estado_x){//registro correcto webservice
                                        break;
                                    }
                                }
                                if(!$estado_x){//registro correcto webservice
                                    $estado_ibnorca++;
                                    $stmtDelte = $dbh->prepare("DELETE from facturas_venta where codigo=$cod_facturaVenta");
                                    $stmtDelte->execute();
                                    $sqldeletecomprobante="DELETE from comprobantes where codigo=$cod_comprobante";
                                    $stmtDeleteCopmprobante = $dbh->prepare($sqldeletecomprobante);
                                    $flagSuccess=$stmtDeleteCopmprobante->execute();
                                    $sqldeletecomprobanteDet="DELETE from comprobantes_detalle where cod_comprobante=$cod_comprobante";
                                    $stmtDeleteComprobanteDet = $dbh->prepare($sqldeletecomprobanteDet);
                                    $flagSuccess=$stmtDeleteComprobanteDet->execute();
                                    break;
                                }
                            }
                            if($estado_ibnorca==0){//sin errores en el servicio web
                                $precio_x=$precio_x+$descuento_bob_x;//se registró el precio total incluido el descuento, para la factura necesitamos el precio unitario
                                $descripcion_alterna_x=$row['descripcion_alterna'];            
                                $stmtInsertSoliFactDet = $dbh->prepare("INSERT INTO facturas_ventadetalle(cod_facturaventa,cod_claservicio,cantidad,precio,descripcion_alterna,descuento_bob,suscripcionId,ci_estudiante) 
                                values ('$cod_facturaVenta','$cod_claservicio_x','$cantidad_x','$precio_x','$descripcion_alterna_x',$descuento_bob_x,0,'$ci_estudiante_p')");
                                $flagSuccess=$stmtInsertSoliFactDet->execute();
                            }
                        }
                        if($estado_ibnorca==0){
                            $sqlUpdate="UPDATE solicitudes_facturacion SET  cod_estadosolicitudfacturacion=5 where codigo=$codigo";
                            $stmtUpdate = $dbh->prepare($sqlUpdate);
                            $flagSuccess=$stmtUpdate->execute(); 
                            //enviar propuestas para la actualizacion de ibnorca
                            $fechaHoraActual=date("Y-m-d H:i:s");
                            $idTipoObjeto=2709;
                            $idObjeto=2729; //regristado
                            $obs="Solicitud Facturada Manualmente";                            
                            actualizarEstadosObjetosIbnorca($idTipoObjeto,$idObjeto,$globalUser,$codigo,$fechaHoraActual,$obs);
                            if($flagSuccess){
                                echo 1;
                            }else{
                                echo 0;
                            }
                            $dbhU=null;
                        }else{
                            echo -1;    
                        }
                    }else{
                        echo 0;
                    }
                }else{
                    echo 0;
                }
            }
        // }else{//ya se registro
        //     echo 4;            
        // }
    }   
} catch(PDOException $ex){
    echo 0;
}

?>
