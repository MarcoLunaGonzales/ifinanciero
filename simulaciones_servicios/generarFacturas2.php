<?php //ESTADO FINALIZADO

require_once __DIR__.'/../conexion.php';
// require '../assets/phpqrcode/qrlib.php';
// include '../assets/controlcode/sin/ControlCode.php';

//require_once 'configModule.php';
require_once __DIR__.'/../functions.php';
require_once __DIR__.'/../functionsGeneral.php';
// require_once 'executeComprobante_factura.php';
require_once '../layouts/bodylogin.php';


require_once 'generar_facturas2_divididas.php';

$dbh = new Conexion();
$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);//try
set_time_limit(300);
session_start();

$globalUser=$_SESSION["globalUser"];

//RECIBIMOS LAS VARIABLES
$codigo = $_GET["codigo"];
try{
    
    //verificamos si se registró las cuentas en los tipos de pago 
    $cont_tipopago=verificamos_cuentas_tipos_pagos();
    //verificamos si se registró las cuentas en LAS AREAS DE INGRESO
    $cont_areas=verificamos_cuentas_areas();
    if($cont_tipopago!=0){//falta asociar cuenta a tipos de pago ?>
        <script>            
            Swal.fire("Error!","Por favor verifique que los tipos de pago estén asociados a una cuenta!.", "error");
        </script><?php 
    }elseif($cont_areas!=0){//falta asociar alguna cuenta en areas ?>
        <script>            
            Swal.fire("Error!","Por favor verifique que las areas de ingreso estén asociadas a una cuenta!.", "error");
        </script><?php 
    }else{ //cuando todo esta en orden
        // verificamos si ya se registro la factura
        $codigo_facturacion=verificamosFacturaDuplicada($codigo);
        if($codigo_facturacion==null){//no se registró            
            //datos de la solicitud de facturacion
            $stmtInfo = $dbh->prepare("SELECT sf.*,t.nombre as nombre_cliente FROM solicitudes_facturacion sf,clientes t  where sf.cod_cliente=t.codigo and sf.codigo=$codigo");
            $stmtInfo->execute();
            $resultInfo = $stmtInfo->fetch();    
            $cod_simulacion_servicio = $resultInfo['cod_simulacion_servicio'];
            $cod_unidadorganizacional = $resultInfo['cod_unidadorganizacional'];
            $cod_area = $resultInfo['cod_area'];
            $cod_tipoobjeto = $resultInfo['cod_tipoobjeto'];
            $cod_tipopago = $resultInfo['cod_tipopago'];
            $cod_cliente = $resultInfo['cod_cliente'];
            
            // $cod_personal = $resultInfo['cod_personal'];
            $razon_social = $resultInfo['razon_social'];
            $nitCliente = $resultInfo['nit'];
            $observaciones = $resultInfo['observaciones'];
            $nombre_cliente = $resultInfo['nombre_cliente'];
            $tipo_solicitud = $resultInfo['tipo_solicitud'];//1 tcp - 2 capacitacion - 3 servicios - 4 manual - 5 venta de normas
            if($nombre_cliente==null || $nombre_cliente==''){//no hay registros con ese dato
                $stmtInfo = $dbh->prepare("SELECT sf.* FROM solicitudes_facturacion sf where sf.codigo=$codigo");
                $stmtInfo->execute();
                $resultInfo = $stmtInfo->fetch();    
                $cod_simulacion_servicio = $resultInfo['cod_simulacion_servicio'];
                $cod_unidadorganizacional = $resultInfo['cod_unidadorganizacional'];
                $cod_area = $resultInfo['cod_area'];
                $cod_tipoobjeto = $resultInfo['cod_tipoobjeto'];
                $cod_tipopago = $resultInfo['cod_tipopago'];
                $cod_cliente = $resultInfo['cod_cliente'];
                // $cod_personal = $resultInfo['cod_personal'];
                $razon_social = $resultInfo['razon_social'];
                $nitCliente = $resultInfo['nit'];
                $observaciones = $resultInfo['observaciones'];
                $nombre_cliente = $resultInfo['razon_social'];
                $tipo_solicitud = $resultInfo['tipo_solicitud'];//1 tcp - 2 capacitacion - 3 servicios - 4 manual - 5 venta de normas
            }
            $cod_personal=$globalUser;
            $cod_sucursal=obtenerSucursalCodUnidad($cod_unidadorganizacional);
            if($cod_sucursal==null || $cod_sucursal==''){//sucursal no encontrado?>
                <script>                    
                    Swal.fire("Error!","A ocurrido un error: Por favor verifique la existencia de sucursales!.", "error");
                </script><?php 
            }else{
                // echo "uo:",$cod_unidadorganizacional."<br>";
                $fecha_actual=date('Y-m-d');                
                $sqlInfo="SELECT d.codigo,d.nro_autorizacion, d.llave_dosificacion,d.fecha_limite_emision
                from dosificaciones_facturas d where d.cod_sucursal='$cod_sucursal' and d.fecha_limite_emision>='$fecha_actual' and d.cod_estado=1 order by codigo";
                $stmtInfo = $dbh->prepare($sqlInfo);
                // echo $sqlInfo;
                $stmtInfo->execute();
                $resultInfo = $stmtInfo->fetch();  
                $cod_dosificacionfactura = $resultInfo['codigo'];  
                $nroAutorizacion = $resultInfo['nro_autorizacion'];
                $llaveDosificacion = $resultInfo['llave_dosificacion'];
                $fecha_limite_emision = $resultInfo['fecha_limite_emision'];
                if($nroAutorizacion==null || $nroAutorizacion=='' || $nroAutorizacion==' '){                    
                    ?>
                    <script>
                        Swal.fire("Error!","DOSIFICACION de sucursal No encontrada.", "error");
                    </script>
                    <?php
                    //header('Location: ../index.php?opcion=listFacturasServicios');
                }else{                                    
                    //NUMERO CORRELATIVO DE FACTURA                    
                    $nro_correlativo = nro_correlativo_facturas($cod_sucursal);//solo para verificar
                    if($nro_correlativo==0){
                        ?>
                        <script>                            
                            Swal.fire("Error!","DOSIFICACION de sucursal incorrecta.", "error");
                        </script>
                        <?php
                    }else{
                        if(isset($_GET["cod_libreta"])){
                            $cod_libreta=$_GET["cod_libreta"];
                        }else{
                            $cod_libreta=0;
                        }
                        $codigo_error=1;
                        $array_codigo_detalle=obtenerCodigoDetalleSolFac($codigo);
                        // var_dump($array_codigo_detalle);
                        $cant_items_sfd=sizeof($array_codigo_detalle);
                        $nro_facturas = ceil($cant_items_sfd/20);
                        switch ($nro_facturas) {
                            case 1:
                                $cadena_cod_facdet_1='';
                                for($j=0;$j<$cant_items_sfd;$j++){
                                    $cadena_cod_facdet_1.=$array_codigo_detalle[$j].",";                                    
                                }
                                $variable_controlador=1;//indica la vez que entra a la funcion
                                $codigo_error=generar_factura($codigo,trim($cadena_cod_facdet_1,','),$cod_tipopago,$cod_sucursal,$cod_libreta,$nroAutorizacion,$nitCliente,$fecha_actual,$llaveDosificacion,$cod_unidadorganizacional,$cod_area,$fecha_limite_emision,$cod_tipoobjeto,$cod_cliente,$cod_personal,$razon_social,$cod_dosificacionfactura,$observaciones,$globalUser,$tipo_solicitud,$cod_simulacion_servicio,$variable_controlador);
                                
                            break;
                            case 2:
                                $cadena_cod_facdet_1='';
                                for($i=0;$i<20;$i++){
                                    $cadena_cod_facdet_1.=$array_codigo_detalle[$i].",";
                                }
                                $variable_controlador=1;//indica la vez que entra a la funcion
                                $codigo_error=generar_factura($codigo,trim($cadena_cod_facdet_1,','),$cod_tipopago,$cod_sucursal,$cod_libreta,$nroAutorizacion,$nitCliente,$fecha_actual,$llaveDosificacion,$cod_unidadorganizacional,$cod_area,$fecha_limite_emision,$cod_tipoobjeto,$cod_cliente,$cod_personal,$razon_social,$cod_dosificacionfactura,$observaciones,$globalUser,$tipo_solicitud,$cod_simulacion_servicio,$variable_controlador);
                                $cadena_cod_facdet_2="";
                                for($j=$i;$j<$cant_items_sfd;$j++){
                                    $cadena_cod_facdet_2.=$array_codigo_detalle[$j].",";
                                }
                                if($codigo_error==0){
                                    $variable_controlador=2;//indica la vez que entra a la funcion
                                    $codigo_error=generar_factura($codigo,trim($cadena_cod_facdet_2,','),$cod_tipopago,$cod_sucursal,$cod_libreta,$nroAutorizacion,$nitCliente,$fecha_actual,$llaveDosificacion,$cod_unidadorganizacional,$cod_area,$fecha_limite_emision,$cod_tipoobjeto,$cod_cliente,$cod_personal,$razon_social,$cod_dosificacionfactura,$observaciones,$globalUser,$tipo_solicitud,$cod_simulacion_servicio,$variable_controlador);
                                }
                            break;
                            case 3:
                                $cadena_cod_facdet_1='';
                                for($i=0;$i<20;$i++){
                                    $cadena_cod_facdet_1.=$array_codigo_detalle[$i].",";                                    
                                }
                                $variable_controlador=1;//indica la vez que entra a la funcion
                                $codigo_error=generar_factura($codigo,trim($cadena_cod_facdet_1,','),$cod_tipopago,$cod_sucursal,$cod_libreta,$nroAutorizacion,$nitCliente,$fecha_actual,$llaveDosificacion,$cod_unidadorganizacional,$cod_area,$fecha_limite_emision,$cod_tipoobjeto,$cod_cliente,$cod_personal,$razon_social,$cod_dosificacionfactura,$observaciones,$globalUser,$tipo_solicitud,$cod_simulacion_servicio,$variable_controlador);
                                $cadena_cod_facdet_2="";
                                for($j=$i;$j<40;$j++){
                                    $cadena_cod_facdet_2.=$array_codigo_detalle[$j].",";                                    
                                }
                                if($codigo_error==0){
                                    $variable_controlador=2;//indica la vez que entra a la funcion
                                    $codigo_error=generar_factura($codigo,trim($cadena_cod_facdet_2,','),$cod_tipopago,$cod_sucursal,$cod_libreta,$nroAutorizacion,$nitCliente,$fecha_actual,$llaveDosificacion,$cod_unidadorganizacional,$cod_area,$fecha_limite_emision,$cod_tipoobjeto,$cod_cliente,$cod_personal,$razon_social,$cod_dosificacionfactura,$observaciones,$globalUser,$tipo_solicitud,$cod_simulacion_servicio,$variable_controlador);
                                }
                                $cadena_cod_facdet_3="";
                                for($k=$j;$k<$cant_items_sfd;$k++){
                                    $cadena_cod_facdet_3.=$array_codigo_detalle[$k].",";
                                }
                                if($codigo_error==0){
                                    $variable_controlador=3;//indica la vez que entra a la funcion
                                    $codigo_error=generar_factura($codigo,trim($cadena_cod_facdet_3,','),$cod_tipopago,$cod_sucursal,$cod_libreta,$nroAutorizacion,$nitCliente,$fecha_actual,$llaveDosificacion,$cod_unidadorganizacional,$cod_area,$fecha_limite_emision,$cod_tipoobjeto,$cod_cliente,$cod_personal,$razon_social,$cod_dosificacionfactura,$observaciones,$globalUser,$tipo_solicitud,$cod_simulacion_servicio,$variable_controlador);
                                }

                            break;
                        }
                        if($codigo_error==0){                                    
                            $sqlUpdate="UPDATE solicitudes_facturacion SET  cod_estadosolicitudfacturacion=5 where codigo=$codigo";
                            $stmtUpdate = $dbh->prepare($sqlUpdate);
                            $flagSuccess=$stmtUpdate->execute(); 
                            //enviar propuestas para la actualizacion de ibnorca
                            $fechaHoraActual=date("Y-m-d H:i:s");
                            $idTipoObjeto=2709;
                            $idObjeto=2729; //regristado
                            $obs="Solicitud Facturada";                                    
                            actualizarEstadosObjetosIbnorca($idTipoObjeto,$idObjeto,$globalUser,$codigo,$fechaHoraActual,$obs);
                            header('Location: ../simulaciones_servicios/generarFacturasPrint.php?codigo='.$codigo.'&tipo=2');
                        }else{ ?>
                            <script>Swal.fire("Error!","No se tiene conexión al servicio de capacitación.", "error");
                            </script> <?php
                        }
                    }
                }
            }        
        }else{//ya se registro
            echo "ya se registró la factura.";            
            header('Location: ../simulaciones_servicios/generarFacturasPrint.php?codigo='.$codigo.'&tipo=2');            
        }
    }
} catch(PDOException $ex){
    echo "Un error ocurrio".$ex->getMessage();
    echo "Error : ";
}
?>
