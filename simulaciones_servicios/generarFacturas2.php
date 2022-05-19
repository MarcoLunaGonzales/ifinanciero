<?php //ESTADO FINALIZADO

require_once __DIR__.'/../conexion.php';
require_once __DIR__.'/../functions.php';
require_once __DIR__.'/../functionsGeneral.php';
require_once 'executeComprobante_factura.php';
require_once '../layouts/bodylogin.php';
require_once 'generar_facturas2_divididas.php';

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
            $observaciones_2 = $resultInfo['observaciones_2'];
            $nombre_cliente = $resultInfo['razon_social'];
            $tipo_solicitud = $resultInfo['tipo_solicitud'];//1 tcp - 2 capacitacion - 3 servicios - 4 manual - 5 venta de normas
            $ci_estudiante = $resultInfo['ci_estudiante'];  

            $cod_personal=$globalUser;
            $cod_sucursal=obtenerSucursalCodUnidad($cod_unidadorganizacional);
            if($cod_sucursal==null || $cod_sucursal==''){//sucursal no encontrado ?>
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
                if($nroAutorizacion==null || $nroAutorizacion=='' || $nroAutorizacion==' '){?>
                    <script>
                        Swal.fire("Error!","DOSIFICACION de sucursal No encontrada.", "error");
                    </script><?php                    
                }else{                                    
                    //NUMERO CORRELATIVO DE FACTURA                    
                    $nro_correlativo = nro_correlativo_facturas($cod_sucursal);//solo para verificar
                    if($nro_correlativo==0){ ?>
                        <script>                            
                            Swal.fire("Error!","DOSIFICACION de sucursal incorrecta.", "error");
                        </script> <?php
                    }else{
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
                                    actualizarEstadosObjetosIbnorca($idTipoObjeto,$idObjeto,$globalUser,$codigo,$fechaHoraActual,$obs);
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
                                    $sqlCommit="COMMIT;SET AUTOCOMMIT=1;";
                                    $stmtCommit = $dbh->prepare($sqlCommit);
                                    $stmtCommit->execute();

                                    //Verificamos si la glosa especial es distinta de vacio para el formato de impresion
                                    if($observaciones_2<>""){
                                        $adminImpresion=7;
                                    }else{
                                        $adminImpresion=5;
                                    }

                                    header('Location: ../simulaciones_servicios/generarFacturasPrint.php?codigo='.$codigo.'&tipo=2&admin='.$adminImpresion);
                                }                            
                            }else{?>
                                <script>Swal.fire("Error!","Hubo un error durante el proceso de generar la factura.", "error");
                                </script> <?php
                            }
                        }
                    }
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

            //DESCOMENTAR ESTO
            header('Location: ../simulaciones_servicios/generarFacturasPrint.php?codigo='.$codigo.'&tipo=2');            
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
