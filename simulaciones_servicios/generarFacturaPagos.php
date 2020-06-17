<?php //ESTADO FINALIZADO

require_once __DIR__.'/../conexion.php';
require '../assets/phpqrcode/qrlib.php';
include '../assets/controlcode/sin/ControlCode.php';
require_once __DIR__.'/../functions.php';
require_once __DIR__.'/../functionsGeneral.php';
require_once 'executeComprobante_factura.php';

require_once '../layouts/bodylogin.php';
require_once 'configModule.php';

$dbh = new Conexion();
$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);//try
set_time_limit(300);
session_start();

$globalUser=$_SESSION["globalUser"];
$estado_ibnorca=0;
//RECIBIMOS LAS VARIABLES

// $codigo = $_GET["codigo"];
$cod_solicitudfacturacion = $_POST["cod_solicitudfacturacion_factpagos"];
$cantidad_items = $_POST["cantidad_items"];
$total_importe = $_POST["total_importe_pagar"];
try{    
    //verificamos si se registró las cuentas en los tipos de pago 
    $cont_tipopago=verificamos_cuentas_tipos_pagos();
    //verificamos si se registró las cuentas en LAS AREAS DE INGRESO 
    $cont_areas=verificamos_cuentas_areas();
    if($cont_tipopago!=0){//falta asociar cuenta a tipos de pago ?>
        <script>            
            Swal.fire("Error!","Por favor verifique que los tipos de pago estén asociados a una cuenta!.", "error");
        </script>
    <?php }elseif($cont_areas!=0){//falta asociar alguna cuenta en areas ?>
        <script>            
            Swal.fire("Error!","Por favor verifique que las areas de ingreso estén asociadas a una cuenta!.", "error");
        </script>
    <?php }else{//cuando todo esta en orden

        // verificamos si ya se registro la factura
        // $stmtVerif = $dbh->prepare("SELECT codigo,importe FROM facturas_venta where cod_solicitudfacturacion=$codigo and cod_estadofactura=1");
        // $stmtVerif->execute();
        // $resultVerif = $stmtVerif->fetch();    
        // $codigo_facturacion = $resultVerif['codigo'];
        // $importe_facturacion = $resultVerif['importe'];
        
        
        //datos de la solicitud de facturacion
        $stmtInfo = $dbh->prepare("SELECT sf.*,t.nombre as nombre_cliente FROM solicitudes_facturacion sf,clientes t  where sf.cod_cliente=t.codigo and sf.codigo=$cod_solicitudfacturacion");
        $stmtInfo->execute();
        $resultInfo = $stmtInfo->fetch();    
        $cod_simulacion_servicio = $resultInfo['cod_simulacion_servicio'];
        $cod_unidadorganizacional = $resultInfo['cod_unidadorganizacional'];
        $cod_area = $resultInfo['cod_area'];
        $cod_tipoobjeto = $resultInfo['cod_tipoobjeto'];
        $cod_tipopago = $resultInfo['cod_tipopago'];
        $cod_cliente = $resultInfo['cod_cliente'];
        $cod_personal = $resultInfo['cod_personal'];
        $razon_social = $resultInfo['razon_social'];
        $nitCliente = $resultInfo['nit'];
        $observaciones = $resultInfo['observaciones'];
        $nombre_cliente = $resultInfo['nombre_cliente'];
        $tipo_solicitud = $resultInfo['tipo_solicitud'];//1 tcp - 2 capacitacion - 3 servicios - 4 manual - 5 venta de normas
        if($nombre_cliente==null || $nombre_cliente==''){//no hay registros con ese dato
            $stmtInfo = $dbh->prepare("SELECT sf.* FROM solicitudes_facturacion sf where sf.codigo=$cod_solicitudfacturacion");
            $stmtInfo->execute();
            $resultInfo = $stmtInfo->fetch();    
            $cod_simulacion_servicio = $resultInfo['cod_simulacion_servicio'];
            $cod_unidadorganizacional = $resultInfo['cod_unidadorganizacional'];
            $cod_area = $resultInfo['cod_area'];
            $cod_tipoobjeto = $resultInfo['cod_tipoobjeto'];
            $cod_tipopago = $resultInfo['cod_tipopago'];
            $cod_cliente = $resultInfo['cod_cliente'];
            $cod_personal = $resultInfo['cod_personal'];
            $razon_social = $resultInfo['razon_social'];
            $nitCliente = $resultInfo['nit'];
            $observaciones = $resultInfo['observaciones'];
            $nombre_cliente = $resultInfo['razon_social'];
            $tipo_solicitud = $resultInfo['tipo_solicitud'];//1 tcp - 2 capacitacion - 3 servicios - 4 manual - 5 venta de normas
        }
        $cod_sucursal=obtenerSucursalCodUnidad($cod_unidadorganizacional);
        if($cod_sucursal==null || $cod_sucursal==''){//sucursal no encontrado?>
            <script>                
                Swal.fire("Error!","Por favor verifique la existencia de sucursales!.", "error");
            </script><?php 
        }else{
            // echo "uo:",$cod_unidadorganizacional."<br>";
            $fecha_actual=date('Y-m-d');
            $fecha_actual_cH=date('Y-m-d H:i:s');
            $sqlInfo="SELECT d.codigo,d.nro_autorizacion, d.llave_dosificacion,d.fecha_limite_emision
            from dosificaciones_facturas d where d.cod_sucursal='$cod_sucursal' and d.fecha_limite_emision>='$fecha_actual' order by codigo";
            $stmtInfo = $dbh->prepare($sqlInfo);
            // echo $sqlInfo;
            $stmtInfo->execute();
            $resultInfo = $stmtInfo->fetch();  
            $cod_dosificacionfactura = $resultInfo['codigo'];  
            $nroAutorizacion = $resultInfo['nro_autorizacion'];
            $llaveDosificacion = $resultInfo['llave_dosificacion'];
            $fecha_limite_emision = $resultInfo['fecha_limite_emision'];
            if($nroAutorizacion==null || $nroAutorizacion=='' || $nroAutorizacion==' '){
                $error = "No tiene registrado La dosificación para la facturación."; 
                ?>
                <script>                    
                    Swal.fire("Error!","DOSIFICACION de sucursal No encontrada.", "error");
                </script>
                <?php                
            }else{                
                //monto total redondeado
                // $stmtMontoTotal = $dbh->prepare("SELECT sum(sf.precio) as monto from solicitudes_facturaciondetalle sf 
                // where sf.cod_solicitudfacturacion=$codigo");
                // $stmtMontoTotal->execute();
                // $resultMontoTotal = $stmtMontoTotal->fetch();   
                // $monto_total= $resultMontoTotal['monto'];                    
                $totalFinalRedondeado=round($total_importe,0);
                // echo "monto total:".$totalFinalRedondeado;
                //NUMERO CORRELATIVO DE FACTURA
                $nro_correlativo = nro_correlativo_facturas($cod_sucursal);
                if($nro_correlativo==0){
                    ?>
                    <script>                        
                        Swal.fire("Error!","DOSIFICACION de sucursal incorrecta!.", "error");    
                    </script>
                    <?php
                }else{
                    //generamos el comprobante
                    $cod_comprobante=ejecutarComprobanteSolicitud($cod_solicitudfacturacion,$nro_correlativo);
                    // echo "auto:".$nroAutorizacion." - nro_corr:".$nro_correlativo." - nitCliente:".$nitCliente." - fecha_actual:".$fecha_actual." - totalFinalRedondeado:".$totalFinalRedondeado." - llaveDosificacion:".$llaveDosificacion;
                    $controlCode = new ControlCode();
                    $cod_control = $controlCode->generate($nroAutorizacion,//Numero de autorizacion
                    $nro_correlativo,//Numero de factura
                    $nitCliente,//Número de Identificación Tributaria o Carnet de Identidad
                    str_replace('-','',$fecha_actual),//fecha de transaccion de la forma AAAAMMDD
                    $totalFinalRedondeado,//Monto de la transacción
                    $llaveDosificacion//Llave de dosificación
                    );
                    // echo "cod:".$cod_control;
                    $sql="INSERT INTO facturas_venta(cod_sucursal,cod_solicitudfacturacion,cod_unidadorganizacional,cod_area,fecha_factura,fecha_limite_emision,cod_tipoobjeto,cod_tipopago,cod_cliente,cod_personal,razon_social,nit,cod_dosificacionfactura,nro_factura,nro_autorizacion,codigo_control,importe,observaciones,cod_estadofactura,cod_comprobante) 
                      values ('$cod_sucursal','$cod_solicitudfacturacion','$cod_unidadorganizacional','$cod_area','$fecha_actual_cH','$fecha_limite_emision','$cod_tipoobjeto','$cod_tipopago','$cod_cliente','$cod_personal','$razon_social','$nitCliente','$cod_dosificacionfactura','$nro_correlativo','$nroAutorizacion','$cod_control','$total_importe','$observaciones','1','$cod_comprobante')";
                      // echo $sql;
                    $stmtInsertSoliFact = $dbh->prepare($sql);
                    $flagSuccess=$stmtInsertSoliFact->execute();
                    if($flagSuccess){
                        //obtenemos el registro del ultimo insert
                        $stmtNroFac = $dbh->prepare("SELECT codigo from facturas_venta where cod_solicitudfacturacion=$cod_solicitudfacturacion order by codigo desc LIMIT 1");
                        $stmtNroFac->execute();
                        $resultNroFact = $stmtNroFac->fetch();    
                        $cod_facturaVenta = $resultNroFact['codigo'];
                        if($_POST["cod_libreta_pagos"]>0){
                            $cod_libreta=$_POST["cod_libreta_pagos"];
                            //si es de tipo deposito en cuenta insertamos en libreta bancaria
                            $sqlUpdateLibreta="UPDATE libretas_bancariasdetalle SET cod_factura=$cod_facturaVenta where codigo=$cod_libreta";
                            $stmtUpdateLibreta = $dbh->prepare($sqlUpdateLibreta);
                            $flagSuccess=$stmtUpdateLibreta->execute(); 
                        }
                        for($i=0;$i<$cantidad_items;$i++){
                            $codigo_clax = $_POST["codigo_x".$i];
                            $importe_pagar = $_POST["importe_a_pagar".$i];    
                            
                            $sqlDetalles="SELECT sf.* from solicitudes_facturaciondetalle sf where sf.cod_solicitudfacturacion=$cod_solicitudfacturacion and cod_claservicio=$codigo_clax";
                            // echo $sqlDetalles;
                            $stmt = $dbh->prepare($sqlDetalles);
                            $stmt->execute();
                            while ($row = $stmt->fetch()) 
                            { 
                                $cod_claservicio_x=$row['cod_claservicio'];
                                $cantidad_x=$row['cantidad'];
                                $precio_x=$row['precio'];
                                $descuento_bob_x=$row['descuento_bob'];
                                $precio_x=$importe_pagar;//formula para obtener el monto con el porcentaje a pagar
                                if($tipo_solicitud==2){// la solicitud pertence capacitacion
                                    $datos=resgistrar_pago_curso($cod_cliente,$cod_simulacion_servicio,$cod_claservicio_x,$precio_x,$cod_solicitudfacturacion);
                                    $estado_x=$datos["estado"];
                                    $mensaje_x=$datos["mensaje"];
                                    if(!$estado_x){//registro correcto webservice
                                        $estado_ibnorca++;
                                        $stmtDelte = $dbh->prepare("DELETE from facturas_venta where codigo=$cod_facturaVenta");
                                        $stmtDelte->execute();
                                        $estado_ibnorca++;
                                        break;
                                    }
                                }
                                if($estado_ibnorca==0){//sin errores en el servicio web
                                    $descripcion_alterna_x=$row['descripcion_alterna'];            
                                    $stmtInsertSoliFactDet = $dbh->prepare("INSERT INTO facturas_ventadetalle(cod_facturaventa,cod_claservicio,cantidad,precio,descripcion_alterna,descuento_bob,suscripcionId) 
                                    values ('$cod_facturaVenta','$cod_claservicio_x','$cantidad_x','$precio_x','$descripcion_alterna_x',$descuento_bob_x,0)");
                                    $flagSuccess=$stmtInsertSoliFactDet->execute();
                                }
                                // $precio_x=$precio_x+$descuento_bob_x;//se registró el precio total incluido el descuento, para la factura necesitamos el precio unitario
                            }
                            if($estado_ibnorca==0){//sin errores en el servicio web
                                $sqlUpdate="UPDATE solicitudes_facturacion SET  cod_estadosolicitudfacturacion=5 where codigo=$cod_solicitudfacturacion";
                                $stmtUpdate = $dbh->prepare($sqlUpdate);
                                $flagSuccess=$stmtUpdate->execute(); 
                            }
                        }
                    }else{?>
                        <script>Swal.fire("Error!","A ocurrido un error al generar la factura, contáctese con el administrador!.", "error");</script>
                    <?php }
                }
            }
        }
    }
    if($estado_ibnorca==0){//sin errores en el servicio web
            //enviar propuestas para la actualizacion de ibnorca
        $fechaHoraActual=date("Y-m-d H:i:s");
        $idTipoObjeto=2709;
        $idObjeto=2729; //regristado
        $obs="Solicitud Facturada a Pagos";
        if(isset($_GET['u'])){
        $u=$_GET['u'];
            actualizarEstadosObjetosIbnorca($idTipoObjeto,$idObjeto,$u,$cod_solicitudfacturacion,$fechaHoraActual,$obs);
        }else{
            actualizarEstadosObjetosIbnorca($idTipoObjeto,$idObjeto,$globalUser,$cod_solicitudfacturacion,$fechaHoraActual,$obs);
        }
        if($flagSuccess){
            showAlertSuccessError($flagSuccess,"../".$urlListSol_conta);
        }else{
            showAlertSuccessError($flagSuccess,"../".$urlListSol_conta);
        }               
        $dbhU=null;     
    }else{        
          showAlertSuccessErrorPagosCapacitacion(false,$$urlListSol_conta);        
    }

}catch(PDOException $ex){
    echo "Un error ocurrio".$ex->getMessage();
    
}
?>
