<?php //ESTADO FINALIZADO

require_once __DIR__.'/../conexion.php';
require '../assets/phpqrcode/qrlib.php';
include '../assets/controlcode/sin/ControlCode.php';

//require_once 'configModule.php';
require_once __DIR__.'/../functions.php';
require_once __DIR__.'/../functionsGeneral.php';
require_once 'executeComprobante_factura.php';
require_once '../layouts/bodylogin.php';

$dbh = new Conexion();
$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);//try
set_time_limit(300);
session_start();

$globalUser=$_SESSION["globalUser"];

//RECIBIMOS LAS VARIABLES
$estado_ibnorca=0;
$codigo = $_GET["codigo"];
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
        $stmtVerif = $dbh->prepare("SELECT codigo FROM facturas_venta where cod_solicitudfacturacion=$codigo and cod_estadofactura=1");
        $stmtVerif->execute();
        $resultVerif = $stmtVerif->fetch();    
        $codigo_facturacion = $resultVerif['codigo'];        
        if($codigo_facturacion==null){//no se registró
            $stmt = $dbh->prepare("SELECT sf.*,(select t.Descripcion from cla_servicios t where t.IdClaServicio=sf.cod_claservicio) as nombre_serv from solicitudes_facturaciondetalle sf where sf.cod_solicitudfacturacion=$codigo");
            $stmt->execute();

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
            $cod_personal = $resultInfo['cod_personal'];
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
                    Swal.fire("Error!","A ocurrido un error: Por favor verifique la existencia de sucursales!.", "error");
                </script><?php 
            }else{
                // echo "uo:",$cod_unidadorganizacional."<br>";
                $fecha_actual=date('Y-m-d');
                $fecha_actual_cH=date('Y-m-d H:i:s');
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
                    //monto total redondeado
                    $stmtMontoTotal = $dbh->prepare("SELECT sum(sf.precio) as monto from solicitudes_facturaciondetalle sf 
                    where sf.cod_solicitudfacturacion=$codigo");
                    $stmtMontoTotal->execute();
                    $resultMontoTotal = $stmtMontoTotal->fetch();   
                    $monto_total= $resultMontoTotal['monto'];

                    $totalFinalRedondeado=round($monto_total,0);
                    // echo "monto total:".$totalFinalRedondeado;
                    //NUMERO CORRELATIVO DE FACTURA                    
                    $nro_correlativo = nro_correlativo_facturas($cod_sucursal);
                    if($nro_correlativo==0){
                        ?>
                        <script>                            
                            Swal.fire("Error!","DOSIFICACION de sucursal incorrecta.", "error");
                        </script>
                        <?php
                    }else{
                        //generamos el comprobante
                        $cod_comprobante=ejecutarComprobanteSolicitud($codigo,$nro_correlativo);
                        // echo "auto:".$nroAutorizacion." - nro_corr:".$nro_correlativo." - nitCliente:".$nitCliente." - fecha_actual:".$fecha_actual." - totalFinalRedondeado:".$totalFinalRedondeado." - llaveDosificacion:".$llaveDosificacion;
                        $controlCode = new ControlCode();
                        $code = $controlCode->generate($nroAutorizacion,//Numero de autorizacion
                        $nro_correlativo,//Numero de factura
                        $nitCliente,//Número de Identificación Tributaria o Carnet de Identidad
                        str_replace('-','',$fecha_actual),//fecha de transaccion de la forma AAAAMMDD
                        $totalFinalRedondeado,//Monto de la transacción
                        $llaveDosificacion//Llave de dosificación
                        );
                        // echo "cod:".$code;
                        $sql="INSERT INTO facturas_venta(cod_sucursal,cod_solicitudfacturacion,cod_unidadorganizacional,cod_area,fecha_factura,fecha_limite_emision,cod_tipoobjeto,cod_tipopago,cod_cliente,cod_personal,razon_social,nit,cod_dosificacionfactura,nro_factura,nro_autorizacion,codigo_control,importe,observaciones,cod_estadofactura,cod_comprobante) 
                          values ('$cod_sucursal','$codigo','$cod_unidadorganizacional','$cod_area','$fecha_actual_cH','$fecha_limite_emision','$cod_tipoobjeto','$cod_tipopago','$cod_cliente','$cod_personal','$razon_social','$nitCliente','$cod_dosificacionfactura','$nro_correlativo','$nroAutorizacion','$code','$monto_total','$observaciones','1','$cod_comprobante')";
                          // echo $sql;
                        $stmtInsertSoliFact = $dbh->prepare($sql);
                        $flagSuccess=$stmtInsertSoliFact->execute();

                        if($flagSuccess){
                          //obtenemos el registro del ultimo insert
                          $stmtNroFac = $dbh->prepare("SELECT codigo from facturas_venta where cod_solicitudfacturacion=$codigo order by codigo desc LIMIT 1");
                            $stmtNroFac->execute();
                            $resultNroFact = $stmtNroFac->fetch();    
                            $cod_facturaVenta = $resultNroFact['codigo'];
                            while ($row = $stmt->fetch()) 
                            {                 
                                $cod_claservicio_x=$row['cod_claservicio'];
                                $cantidad_x=$row['cantidad'];
                                $precio_x=$row['precio'];
                                $descuento_bob_x=$row['descuento_bob'];

                                if($tipo_solicitud==2){// la solicitud pertence capacitacion
                                    $datos=resgistrar_pago_curso($cod_cliente,$cod_simulacion_servicio,$cod_claservicio_x,$precio_x,$codigo);
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
                                    $precio_x=$precio_x+$descuento_bob_x;//se registró el precio total incluido el descuento, para la factura necesitamos el precio unitario
                                    $descripcion_alterna_x=$row['descripcion_alterna'];            
                                    $stmtInsertSoliFactDet = $dbh->prepare("INSERT INTO facturas_ventadetalle(cod_facturaventa,cod_claservicio,cantidad,precio,descripcion_alterna,descuento_bob,suscripcionId) 
                                    values ('$cod_facturaVenta','$cod_claservicio_x','$cantidad_x','$precio_x','$descripcion_alterna_x',$descuento_bob_x,0)");
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
                                $obs="Solicitud Facturada";
                                if(isset($_GET['u'])){
                                $u=$_GET['u'];
                                    actualizarEstadosObjetosIbnorca($idTipoObjeto,$idObjeto,$u,$codigo,$fechaHoraActual,$obs);
                                }else{
                                    actualizarEstadosObjetosIbnorca($idTipoObjeto,$idObjeto,$globalUser,$codigo,$fechaHoraActual,$obs);
                                } 
                                if(isset($_GET['q'])){
                                  $q=$_GET['q'];
                                  $s=$_GET['s'];
                                  $u=$_GET['u'];
                                  $v=$_GET['v'];
                                  header('Location: ../simulaciones_servicios/generarFacturasPrint.php?codigo='.$codigo.'&tipo=2'."&q=".$q."&s=".$s."&u=".$u."&v=".$v);
                                }else{
                                  header('Location: ../simulaciones_servicios/generarFacturasPrint.php?codigo='.$codigo.'&tipo=2');
                                }
                            }else{
                                ?>
                                <script>                                    
                                    Swal.fire("Error!","No se tiene conexión al servicio de capacitación.", "error");
                                </script>
                                <?php                                
                            }
                            
                        }else{?>
                            <script>                                
                                Swal.fire("Error!","Hubo Un error al generar la Factura!.", "error");
                            </script>
                            <?php                            
                        }
                    }
                }
            }        
        }else{//ya se registro
            echo "ya se registró la factura.";
            if(isset($_GET['q'])){
               $q=$_GET['q'];
               $s=$_GET['s'];
               $u=$_GET['u'];
               $v=$_GET['v'];
               header('Location: ../simulaciones_servicios/generarFacturasPrint.php?codigo='.$codigo.'&tipo=2'."&q=".$q."&s=".$s."&u=".$u."&v=".$v);
            }else{
               header('Location: ../simulaciones_servicios/generarFacturasPrint.php?codigo='.$codigo.'&tipo=2');
            }        
        }
    }
} catch(PDOException $ex){
    echo "Un error ocurrio".$ex->getMessage();
    echo "Error : ".$error;
}
?>
