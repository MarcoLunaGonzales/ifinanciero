<?php //ESTADO FINALIZADO

require_once __DIR__.'/../conexion.php';
require_once __DIR__.'/../functions.php';
require_once __DIR__.'/../functionsGeneral.php';
require_once 'executeComprobante_factura.php';
require_once '../layouts/bodylogin.php';
require_once 'generar_facturas2_divididas.php';

 error_reporting(E_ALL);
 ini_set('display_errors', '1');


$dbh = new Conexion();
$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);//try
set_time_limit(300);
session_start();
date_default_timezone_set('America/La_Paz');
$globalUser=$_SESSION["globalUser"];

//RECIBIMOS LAS VARIABLES
$codigo = $_GET["codigo"];
$codigoSolicitud=$codigo;
?>
<script>
    var confirmar_division_factura=0;
</script>

<?php
//rollback inicia
$SQLDATOSINSTERT=[];
$sqlCommit="SET AUTOCOMMIT=0;";
$stmtCommit = $dbh->prepare($sqlCommit);
$stmtCommit->execute();
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
            $stmtInfo = $dbh->prepare("SELECT sf.cod_simulacion_servicio,sf.cod_unidadorganizacional,sf.cod_area,sf.cod_tipoobjeto,sf.cod_tipopago,sf.cod_cliente,sf.cod_personal,sf.nit,sf.observaciones,sf.observaciones_2,sf.razon_social,sf.tipo_solicitud,sf.ci_estudiante,sf.siat_tipoidentificacion,sf.siat_complemento,sf.siat_nroTarjeta,sf.fecha_facturacion,(select stp.codigoClasificador from siat_tipos_pago stp where stp.cod_tipopago=sf.cod_tipopago)as siat_tipoPago
                 FROM solicitudes_facturacion sf where sf.codigo=$codigo");
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
            $observaciones_2 = $resultInfo['observaciones_2'];
            $nombre_cliente = $resultInfo['razon_social'];
            $tipo_solicitud = $resultInfo['tipo_solicitud'];//1 tcp - 2 capacitacion - 3 servicios - 4 manual - 5 venta de normas
            $ci_estudiante = $resultInfo['ci_estudiante'];

            $siat_tipoidentificacion = $resultInfo['siat_tipoidentificacion'];
            $siat_complemento = $resultInfo['siat_complemento'];
            $siat_nroTarjeta = $resultInfo['siat_nroTarjeta'];
            $fecha_facturacion = $resultInfo['fecha_facturacion'];

            $siat_tipoPago = $resultInfo['siat_tipoPago'];

            

            

            $cod_personal=$globalUser;
            $cod_sucursal=obtenerSucursalCodUnidad($cod_unidadorganizacional);
            if($cod_sucursal==null || $cod_sucursal==''){//sucursal no encontrado ?>
                <script>                    
                    Swal.fire("Error!","Ocurrio un error: Por favor verifique la existencia de sucursales!.", "error");
                </script><?php 
            }else{
                // echo "uo:",$cod_unidadorganizacional."<br>";
                $fecha_actual=date('Y-m-d');

                // $sqlInfo="SELECT d.codigo,d.nro_autorizacion, d.llave_dosificacion,d.fecha_limite_emision
                // from dosificaciones_facturas d where d.cod_sucursal='$cod_sucursal' and d.fecha_limite_emision>='$fecha_actual' and d.cod_estado=1 order by codigo";
                // $stmtInfo = $dbh->prepare($sqlInfo);
                // // echo $sqlInfo;
                // $stmtInfo->execute();
                // $resultInfo = $stmtInfo->fetch();  
                // $cod_dosificacionfactura = $resultInfo['codigo'];  
                // $nroAutorizacion = $resultInfo['nro_autorizacion'];
                // $llaveDosificacion = $resultInfo['llave_dosificacion'];
                // $fecha_limite_emision = $resultInfo['fecha_limite_emision'];

                //Para la facturacion con el SIAT ya no se usa las dosificaciones
                $cod_dosificacionfactura = 0;
                $nroAutorizacion = 1;
                $llaveDosificacion = null;
                $fecha_limite_emision = null;
                if($nroAutorizacion==null || $nroAutorizacion=='' || $nroAutorizacion==' '){?>
                    <script>
                        Swal.fire("Error!","DOSIFICACION de sucursal No encontrada.", "error");
                    </script><?php                    
                }else{                                    
                    //NUMERO CORRELATIVO DE FACTURA                    
                    $nro_correlativo=0;//desde el servicio nos enviará el numero de factura
                    //$nro_correlativo = nro_correlativo_facturas($cod_sucursal);//solo para verificar
                    // if($nro_correlativo==0){             
                            // Swal.fire("Error!","DOSIFICACION de sucursal incorrecta.", "error");
                    // }else{
                        if(isset($_GET["cod_libreta"])){
                            $cod_libreta=$_GET["cod_libreta"];
                        }else{
                            $cod_libreta=0;
                        }
                        if(isset($_GET["cod_estadocuenta"])){
                            $cod_estadocuenta=$_GET["cod_estadocuenta"];
                        }else{
                            $cod_estadocuenta=0;
                        }
                        if(isset($_GET["cod_cuentaaux"])){
                            $cod_cuentaaux=$_GET["cod_cuentaaux"];
                        }else{
                            $cod_cuentaaux=0;
                        }
                        $codigo_error=0;
                        $array_codigo_detalle=obtenerCodigoDetalleSolFac($codigo);
                        // var_dump($array_codigo_detalle);
                         $cantidad_por_defecto=100;//cantidad de items por defecto
                        //$cantidad_por_defecto=obtenerValorConfiguracion(66);//cantidad de items por defecto
                        $cant_items_sfd=sizeof($array_codigo_detalle);
                        $nro_facturas = ceil($cant_items_sfd/$cantidad_por_defecto);
                        // $nro_facturas=2;                        
                        if($nro_facturas>1 && !isset($_GET['cargar_pagina'])){ ?>
                            <script>
                                Swal.fire({
                                    title: 'Advertencia!',
                                    text: "La Solicitud se Dividirá en 2 Facturas ¿Desea Continuar?",
                                    type: 'warning',
                                    showCancelButton: true,
                                    confirmButtonClass: 'btn btn-info',
                                    cancelButtonClass: 'btn btn-danger',
                                    confirmButtonText: 'Si',
                                    cancelButtonText: 'No',
                                    buttonsStyling: false
                                    }).then((result) => {
                                    if (result.value) {
                                       location.href="generarFacturas2.php?codigo=<?=$codigo?>&cod_libreta=<?=$cod_libreta?>&cod_estadocuenta=<?=$cod_estadocuenta?>&cod_cuentaaux=<?=$cod_cuentaaux?>&cargar_pagina=1";
                                        return(true);
                                    } else if (result.dismiss === Swal.DismissReason.cancel) {
                                        window.close();
                                        return(false);
                                    }
                                });    
                            </script> <?php                        
                        }else{                            
                            $contador_aux_items=0;//controla el final del array
                            $contador_aux_items_y=0;//controla el inicio del array
                            $variable_controlador=1;//indica la vez que entra a la funcion                        
                            for($p=0;$p<$nro_facturas;$p++){
                                if($codigo_error==0){//codigo de error al generar factura;
                                    if($variable_controlador==$nro_facturas){
                                        $contador_aux_items=$cant_items_sfd;
                                    }else{
                                        $contador_aux_items+=$cantidad_por_defecto;
                                    }                        
                                    $cadena_cod_facdet_1='';
                                    for($i=$contador_aux_items_y;$i<$contador_aux_items;$i++){
                                        $cadena_cod_facdet_1.=$array_codigo_detalle[$i].",";
                                    }                                
                                    $codigo_error=generar_factura($codigo,trim($cadena_cod_facdet_1,','),$cod_tipopago,$cod_sucursal,$cod_libreta,$cod_estadocuenta,$nroAutorizacion,$nitCliente,$fecha_actual,$llaveDosificacion,$cod_unidadorganizacional,$cod_area,$fecha_limite_emision,$cod_tipoobjeto,$cod_cliente,$cod_personal,$razon_social,$cod_dosificacionfactura,$observaciones,$observaciones_2,$globalUser,$tipo_solicitud,$cod_simulacion_servicio,$variable_controlador,$ci_estudiante);
                                    $contador_aux_items_y+=$cantidad_por_defecto;
                                    $variable_controlador++;
                                }else{
                                    break;
                                }
                            }
                            // echo "a1ui";
                            if($codigo_error==0){
                                $stringFacturas=obtenerStringFacturas($codigo);
                                $stringFacturasCod=obtenerStringCodigoFacturas($codigo);
                                $cod_comprobante=ejecutarComprobanteSolicitud($codigo,$stringFacturas,$stringFacturasCod,$cod_libreta,$cod_estadocuenta,$cod_cuentaaux);                            
                                if($cod_comprobante==null || $cod_comprobante=='' || $cod_comprobante==0){
                                    $sqldeleteCabeceraFactura="DELETE from facturas_venta where codigo in ($stringFacturasCod)";
                                    $stmtDeleteCAbeceraFactura = $dbh->prepare($sqldeleteCabeceraFactura);
                                    $stmtDeleteCAbeceraFactura->execute();
                                    $sqldeleteDetalleFactura="DELETE from facturas_ventadetalle where cod_facturaventa in ($stringFacturasCod)";
                                    $stmtDeleteDetalleFactura = $dbh->prepare($sqldeleteDetalleFactura);
                                    $stmtDeleteDetalleFactura->execute(); ?>
                                    <script>Swal.fire("Error!","Hubo un error Al generar el comprobante.", "error");
                                    </script> <?php
                                }else{
                                    $sqlUpdateLibreta="UPDATE facturas_venta SET cod_comprobante=$cod_comprobante where codigo in ($stringFacturasCod)";
                                    $stmtUpdateLibreta = $dbh->prepare($sqlUpdateLibreta);
                                    $flagSuccess=$stmtUpdateLibreta->execute();
                                    array_push($SQLDATOSINSTERT,$flagSuccess);
                                    $codigo_facturacion=verificamosFacturaDuplicada($codigo);
                                    if($codigo_facturacion!=null){
                                        $sqlUpdate="UPDATE solicitudes_facturacion SET  cod_estadosolicitudfacturacion=5 where codigo=$codigo";
                                        $stmtUpdate = $dbh->prepare($sqlUpdate);
                                        $flagSuccess=$stmtUpdate->execute(); 
                                        array_push($SQLDATOSINSTERT,$flagSuccess);
                                    }
                                    
                                    //enviar propuestas para la actualizacion de ibnorca
                                    $fechaHoraActual=date("Y-m-d H:i:s");
                                    $idTipoObjeto=2709;
                                    $idObjeto=2729; //facturado
                                    $obs="Solicitud Facturada";
                                    //Descomentar esto
                                    //actualizarEstadosObjetosIbnorca($idTipoObjeto,$idObjeto,$globalUser,$codigo,$fechaHoraActual,$obs);
                                    //finalizando en rollback
                                    $errorInsertar=0;
                                    for ($flag=0; $flag < count($SQLDATOSINSTERT); $flag++) { 
                                        if($SQLDATOSINSTERT[$flag]==false){
                                         $errorInsertar++;
                                         // echo $flag;
                                         break;
                                        }
                                    }
                                    if($errorInsertar!=0){//si hay errores deshace todo
                                      $sqlRolBack="ROLLBACK;";
                                      $stmtRolBack = $dbh->prepare($sqlRolBack);
                                      $stmtRolBack->execute();
                                    }
                                    // $sqlCommit="COMMIT;SET AUTOCOMMIT=1;";
                                    // $stmtCommit = $dbh->prepare($sqlCommit);
                                    // $stmtCommit->execute();

                                    //Verificamos si la glosa especial es distinta de vacio para el formato de impresion
                                    if($observaciones_2<>""){
                                        $adminImpresion=2;
                                    }else{
                                        $adminImpresion=2;
                                    }                                    
                                    //enviamos los datos para la facturacion con el SIAT
                                    $monto_totalCab=0;
                                    //armamos el detalle
                                    $stmt5 = $dbh->prepare("SELECT sf.cantidad,sf.precio,sf.descuento_bob,sf.descripcion_alterna
                                        from solicitudes_facturaciondetalle sf where sf.cod_solicitudfacturacion=$codigo");
                                    $stmt5->execute();
                                    $arrayDetalle=[];
                                    while ($row = $stmt5->fetch()) 
                                    {   
                                        // $cod_claservicio_x=$row['cod_claservicio'];
                                        $cantidad_x=$row['cantidad'];
                                        $precio_x=$row['precio'];
                                        $descuento_bob_x=$row['descuento_bob'];     
                                        $precio_x=$precio_x+$descuento_bob_x/$cantidad_x;//se registró el precio total incluido el descuento, para la factura necesitamos el precio unitario y tambien el descuetno unitario, ya que se registro el descuento total * cantidad
                                        $descripcion_alterna_x=$row['descripcion_alterna'];

                                        $Objeto_detalle = new stdClass();
                                        $Objeto_detalle->codDetalle = 1;
                                        $Objeto_detalle->cantidadUnitaria = $cantidad_x;
                                        $Objeto_detalle->precioUnitario = $precio_x;
                                        $Objeto_detalle->descuentoProducto = $descuento_bob_x;
                                        $Objeto_detalle->conceptoProducto = $descripcion_alterna_x;
                                        $arrayDetalle[] = $Objeto_detalle;

                                        $monto_totalCab+=$precio_x*$cantidad_x;
                                    }

                                    //datos cabecera
                                    // $monto_totalCab=34.8;
                                    $descuentoCab=0;
                                    $monto_finalCab=$monto_totalCab-$descuentoCab;   

                                    // print_r($arrayDetalle);
                                    $id_usuario=$globalUser;//usuario quien atendió
                                    $usuario="";
                                    $datos=enviar_factura_minkasiat($cod_sucursal,$codigo,$fecha_actual,$cod_cliente,$monto_totalCab,$descuentoCab,$monto_finalCab,$id_usuario,$usuario,$nitCliente,$razon_social,$siat_tipoPago,$siat_nroTarjeta,$siat_tipoidentificacion,$siat_complemento,$arrayDetalle);
                                    if(isset($datos["estado"]) && isset($datos["idTransaccion"])){//el servicio respondio
                                        $idTransaccion_x=$datos["idTransaccion"];
                                        $nroFactura_x=$datos["nroFactura"];
                                        $mensaje_x=$datos["mensaje"];

                                        $sqlUpdateFact="UPDATE facturas_venta set idTransaccion_siat='$idTransaccion_x',nro_factura='$nroFactura_x' where codigo in ($stringFacturasCod)";
                                        $stmtUpdateFact = $dbh->prepare($sqlUpdateFact);
                                        $stmtUpdateFact->execute();

                                        if($datos["estado"]==1){//Todo ok con el servicio
                                            echo "<script>Swal.fire('Correcto!','".$mensaje_x."', 'success');
                                            </script>";
                                        }else{
                                            // $estado_x=$datos["estado"];
                                            $mensaje_x=$datos["mensaje"];
                                            echo "<script>Swal.fire('Informativo!','".$mensaje_x."', 'warning');
                                            </script>";
                                        }
                                    }else{
                                        echo '<script>Swal.fire("Error!","Hubo un error con Servicio MinkaSiat.", "error");
                                        </script>';
                                    }
                                    // print_r($datos);
                                    // header('Location: ../simulaciones_servicios/generarFacturasPrint.php?codigo='.$stringFacturasCod.'&tipo=1&admin='.$adminImpresion);
                                    
                                    
                                }                            
                            }else{?>
                                <script>Swal.fire("Error!","Hubo un error durante el proceso de generar la factura.", "error");
                                </script> <?php
                            }
                        }
                    //}
                }
            }        
        }else{//ya se registro
            echo "ya se registró la factura.";  
            $sqlUpdate="UPDATE solicitudes_facturacion SET  cod_estadosolicitudfacturacion=5 where codigo=$codigo";
            $stmtUpdate = $dbh->prepare($sqlUpdate);
            $stmtUpdate->execute(); 
            // $sqlRolBack="ROLLBACK;";
            // $stmtRolBack = $dbh->prepare($sqlRolBack);
            // $stmtRolBack->execute();          
            $sqlCommit="COMMIT;SET AUTOCOMMIT=1;";
            $stmtCommit = $dbh->prepare($sqlCommit);
            $stmtCommit->execute();

            //DESCOMENTAR ESTO -- EN TODOS LOS CASOS ENVIA EL ORIGINAL CLIENTE
            if($observaciones_2<>""){
                $adminImpresion=2;
            }else{
                $adminImpresion=2;
            }
            header('Location: ../simulaciones_servicios/generarFacturasPrint.php?codigo='.$stringFacturasCod.'&tipo=1&admin='.$adminImpresion);      
        }
        $codigo_facturacion=verificamosFacturaDuplicada($codigo);
        if($codigo_facturacion!=null){
            //verificar el estado y actualizar a 5
            $stmtSol = $dbh->prepare("SELECT s.codigo from solicitudes_facturacion s where s.cod_estadosolicitudfacturacion in (3,4) and s.codigo = $codigoSolicitud");
            $stmtSol->execute();
            while ($rowSol = $stmtSol->fetch(PDO::FETCH_ASSOC)) {  
                $sqlUpdateNew="UPDATE solicitudes_facturacion SET  cod_estadosolicitudfacturacion=5 where codigo=$codigoSolicitud";
                $stmtUpdateNew = $dbh->prepare($sqlUpdateNew);
                $stmtUpdateNew->execute(); 
            }
        }
    }
} catch(PDOException $ex){
    // echo "Un error ocurrio".$ex->getMessage();
    // echo "Error : ";
    $sqlRolBack="ROLLBACK;";
    $stmtRolBack = $dbh->prepare($sqlRolBack);
    $stmtRolBack->execute();
    $sqlCommit="COMMIT;SET AUTOCOMMIT=1;";
    $stmtCommit = $dbh->prepare($sqlCommit);
    $stmtCommit->execute();
}
?>
