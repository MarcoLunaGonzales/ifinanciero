<?php //ESTADO FINALIZADO

require_once __DIR__.'/../conexion.php';
require_once __DIR__.'/../functions.php';
require_once __DIR__.'/../servicioCRM.php';
require_once __DIR__.'/../functionsGeneral.php';
require_once 'executeComprobante_factura.php';
require_once '../layouts/bodylogin.php';
require_once 'generar_facturas2_divididas.php';

 error_reporting(E_ALL);
 ini_set('display_errors', '1');

// PROCESO - INICIO de tiempo
$tiempoInicio_proceso = microtime(true);

$dbh = new Conexion();
$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);//try
set_time_limit(0);
ini_set("default_socket_timeout", 6000);
session_start();
date_default_timezone_set('America/La_Paz');
$globalUser=$_SESSION["globalUser"];

$urlSIAT=obtenerValorConfiguracion(103);

// Ruta WS tienda
// $urlTienda=obtenerValorConfiguracion(109);

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
    }else{ 
        //cuando todo esta en orden
        // verificamos si ya se registro la factura
        $codigo_facturacion=verificamosFacturaDuplicada($codigo);
        if($codigo_facturacion==null){//no se registró            
            //datos de la solicitud de facturacion           
            $stmtInfo = $dbh->prepare("SELECT sf.cod_simulacion_servicio,sf.cod_unidadorganizacional,sf.cod_area,sf.cod_tipoobjeto,sf.cod_tipopago,sf.cod_cliente,sf.cod_personal,sf.nit,sf.observaciones,sf.observaciones_2,sf.razon_social,sf.tipo_solicitud,sf.ci_estudiante,sf.siat_tipoidentificacion,IFNULL(sf.siat_complemento,'')as siat_complemento,IFNULL(sf.siat_nroTarjeta,'')as siat_nroTarjeta,sf.fecha_facturacion,sf.correo_contacto,(select stp.codigoClasificador from siat_tipos_pago stp where stp.cod_tipopago=sf.cod_tipopago)as siat_tipoPago
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

            $correoCliente = $resultInfo['correo_contacto'];

            $cod_personal=$globalUser;
            $cod_sucursal=obtenerSucursalCodUnidad($cod_unidadorganizacional);
            if($cod_sucursal==null || $cod_sucursal==''){//sucursal no encontrado ?>
                <script>                    
                    Swal.fire("Error!","Ocurrio un error: Por favor verifique la existencia de sucursales!.", "error");
                </script><?php 
            }else{
                $fecha_actual=date('Y-m-d');
                //Para la facturacion con el SIAT ya no se usa las dosificaciones
                $cod_dosificacionfactura = 0;
                $nroAutorizacion = 1;
                $llaveDosificacion = null;
                $fecha_limite_emision = null;

                $nro_correlativo=0;    
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
                
                /********  INICIA FACTURACION SIAT  ********/
                //Verificamos si la glosa especial es distinta de vacio para el formato de impresion
                if($observaciones_2<>""){
                    $adminImpresion=2;  //IMPRIME GLOSA ESPECIAL
                }else{
                    $adminImpresion=1;  //IMPRIME NORMAL
                }                                    
                $monto_totalCab=0;
                $stmt5 = $dbh->prepare("SELECT sf.codigo, sf.cantidad,sf.precio,sf.descuento_bob,sf.descripcion_alterna
                    from solicitudes_facturaciondetalle sf where sf.cod_solicitudfacturacion=$codigo");
                $stmt5->execute();
                $arrayDetalle=[];
                $contadoDetalle=1;
                while ($row = $stmt5->fetch()) {   
                    $sf_codigo = $row['codigo'];
                    // $cod_claservicio_x=$row['cod_claservicio'];
                    $cantidad_x=$row['cantidad'];
                    $precio_x=$row['precio'];
                    $descuento_bob_x=$row['descuento_bob'];
                    //HACEMOS DESCUENTO EN 0 PARA NO TENER EL RECALCULO
                    $descuento_bob_x=0;     
                    //$precio_x=$precio_x+$descuento_bob_x/$cantidad_x;
                    //se registró el precio total incluido el descuento, para la factura necesitamos el precio unitario y tambien el descuetno unitario, ya que se registro el descuento total * cantidad ----- DESCUENTO EN 0 
                    $descripcion_alterna_x=$row['descripcion_alterna'];

                    if($adminImpresion==1){
                        $Objeto_detalle = new stdClass();
                        $Objeto_detalle->codDetalle = $contadoDetalle;
                        $Objeto_detalle->cantidadUnitaria = $cantidad_x;
                        $Objeto_detalle->precioUnitario = $precio_x;
                        $Objeto_detalle->descuentoProducto = $descuento_bob_x;
                        $Objeto_detalle->conceptoProducto = $descripcion_alterna_x;
                        $arrayDetalle[] = $Objeto_detalle;
                    }
                    $monto_totalCab+=($precio_x*$cantidad_x)-$descuento_bob_x;
                    $contadoDetalle++;
                }
                $descuentoCab=0;
                $monto_finalCab=$monto_totalCab-$descuentoCab;   
                if($adminImpresion==2){
                    $Objeto_detalle = new stdClass();
                    $Objeto_detalle->codDetalle = 1;
                    $Objeto_detalle->cantidadUnitaria = 1;
                    $Objeto_detalle->precioUnitario = $monto_finalCab;
                    $Objeto_detalle->descuentoProducto = $descuentoCab;
                    $Objeto_detalle->conceptoProducto = $observaciones_2;
                    $arrayDetalle[] = $Objeto_detalle;
                }                                    

                $id_usuario=$globalUser;//ID usuario quien facturó
                $usuario=namePersonal_2($id_usuario);//Usuario quien facturó
                $stringFacturasCod = '';
                $datosWS=enviar_factura_minkasiat($cod_sucursal,$codigo,$fecha_actual,$cod_cliente,$monto_totalCab,$descuentoCab,$monto_finalCab,$id_usuario,$usuario,$nitCliente,$razon_social,$siat_tipoPago,$siat_nroTarjeta,$siat_tipoidentificacion,$siat_complemento,$arrayDetalle,$correoCliente,$stringFacturasCod);
                $banderaSW=false;
                if(isset($datosWS->estado) && isset($datosWS->idTransaccion)){//el servicio respondio
                    $idTransaccion_x=$datosWS->idTransaccion;
                    $nroFactura_x=$datosWS->nroFactura;
                    $mensaje_x=$datosWS->mensaje;
                    $banderaSW=true;
                    if($datosWS->estado==1){//Todo ok con el servicio
                        $titulo="Correcto!";
                        $estado="success";
                    }else{
                        $titulo="Informativo!";
                        $estado="warning";
                    }
                }else{
                    //timepo de respuesta solo 3 segundos. avences se lanza
                    $datosWS_consulta=verificarExistenciaFacturaSiat($stringFacturasCod);
                    $titulo="Error!";
                    $estado="error";
                    $mensaje_x="Hay un error con el servicio de la generacion de la factura en el SIAT.";
                    if(isset($datosWS_consulta->estado)){
                        if($datosWS_consulta->estado==1){
                            $idTransaccion_x=$datosWS_consulta->idTransaccion;
                            $nroFactura_x=$datosWS_consulta->nroFactura;
                            $mensaje_x=$datosWS_consulta->mensaje;
                            $banderaSW=true;
                            $titulo="Correcto!";
                            $estado="success";
                        }
                    }   
                }

                /*SI $banderaSW es TRUE GENERAMOS LA FACTURA Y TAMBIEN LA CONTABILIZACION */
                if($banderaSW){
                    /*aqui entra todo lo demas*/
                    $codigo_error=0;
                    $array_codigo_detalle=obtenerCodigoDetalleSolFac($codigo);
                    // var_dump($array_codigo_detalle);
                    $cantidad_por_defecto=100;//cantidad de items por defecto **** YA NO SE UTILIZA
                    $nro_facturas = 1;
                    $cant_items_sfd=sizeof($array_codigo_detalle);
                    
                    $contador_aux_items=0;//controla el final del array
                    $contador_aux_items_y=0;//controla el inicio del array
                    $variable_controlador=1;//indica la vez que entra a la funcion       

                    // WS SIAT - TIEMPO TOTAL
                    $tiempo_ws_siat = 0;
                    if($codigo_error==0){    //*******GENERAMOS FACTURA****                                         
                        $cadena_cod_facdet_1='';
                        $contador_aux_items=$cant_items_sfd;
                        for($i=$contador_aux_items_y;$i<$contador_aux_items;$i++){
                            $cadena_cod_facdet_1.=$array_codigo_detalle[$i].",";
                        }
                        //  WS SIAT - INICIO de tiempo
                        $tiempoInicio_ws_siat = microtime(true);
                        $codigo_error=generar_factura($codigo,trim($cadena_cod_facdet_1,','),$cod_tipopago,$cod_sucursal,$cod_libreta,$cod_estadocuenta,$nroAutorizacion,$nitCliente,$fecha_actual,$llaveDosificacion,$cod_unidadorganizacional,$cod_area,$fecha_limite_emision,$cod_tipoobjeto,$cod_cliente,$cod_personal,$razon_social,$cod_dosificacionfactura,$observaciones,$observaciones_2,$globalUser,$tipo_solicitud,$cod_simulacion_servicio,$variable_controlador,$ci_estudiante);
                        $contador_aux_items_y+=$cantidad_por_defecto;
                        //  WS SIAT - FIN de tiempo
                        $tiempoFin_ws_siat = microtime(true);
                        // WS SIAT - TIEMPO TOTAL
                        $tiempo_ws_siat    = conversionTiempo($tiempoFin_ws_siat - $tiempoInicio_ws_siat);
                    }
                    $stringFacturasCod = '';
                    if($codigo_error==0){
                        /*******CONTABILIZACION DE LA FACTURA********/    
                        //$stringFacturas=obtenerStringFacturas($codigo);
                        $stringFacturas="F ".$nroFactura_x;
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
                        } 
                    }else{?>
                        <script>Swal.fire("Error!","Hubo un error durante el proceso de generar la factura.", "error");
                        </script> 
                        <?php
                    }
                    // PROCESO - FIN de tiempo
                    $tiempoFin_proceso = microtime(true);
                    // PROCESO - TIEMPO TOTAL
                    $tiempo_proceso    = conversionTiempo($tiempoFin_proceso - $tiempoInicio_proceso);
                    
                    $sqlUpdateFact="UPDATE facturas_venta 
                                    SET idTransaccion_siat='$idTransaccion_x',
                                        nro_factura='$nroFactura_x',
                                        tiempo_proceso='$tiempo_proceso',
                                        tiempo_ws_siat='$tiempo_ws_siat'
                                    WHERE codigo IN ($stringFacturasCod)";
                    $stmtUpdateFact = $dbh->prepare($sqlUpdateFact);
                    $stmtUpdateFact->execute();
                }
                $response_lead = '';
                if($banderaSW){
                    // Generación de Suscripción 
                    generarSuscripcion($codigo, $stringFacturasCod);
                    // Generación de Busqueda y Cierre de LEAD
                    $response_lead = searchLeadsFactura($stringFacturasCod);
                    $urlSIATCompleta=$urlSIAT."formatoFacturaOnLine.php?codVenta=".$idTransaccion_x;
                    echo "<script>
                    Swal.fire('".$titulo."','".$mensaje_x."', '".$estado."');
                    location.href='".$urlSIATCompleta."';
                    </script>";
                }else{
                    $mensaje_x="Hubo un error en la generacion de la factura SIAT.";
                    $estado="error";
                    $titulo="Error!";
                    echo "<script>
                    Swal.fire('".$titulo."','".$mensaje_x."', '".$estado."');
                    </script>";
                }
                /****  FIN $banderaSW SIAT  *****/
            }        
        }else{    // ******* SF YA FACTURADA ******
            echo "La SF ya fue facturada. Consulte con el administrador.";  
            $sqlUpdate="UPDATE solicitudes_facturacion SET  cod_estadosolicitudfacturacion=5 where codigo=$codigo";
            $stmtUpdate = $dbh->prepare($sqlUpdate);
            $stmtUpdate->execute(); 
            $sqlCommit="COMMIT;SET AUTOCOMMIT=1;";
            $stmtCommit = $dbh->prepare($sqlCommit);
            $stmtCommit->execute();
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

function generarSuscripcion($codigo, $stringFacturasCod){
    $dbh = new Conexion();
	/*****************************************************************************************/
	$stmtDetalleFact = $dbh->prepare("SELECT sf.codigo, sf.cantidad,sf.precio,sf.descuento_bob,sf.descripcion_alterna
		from solicitudes_facturaciondetalle sf where sf.cod_solicitudfacturacion='$codigo'");
	$stmtDetalleFact->execute();

	$sw_token = '';
	while ($rowDetallefact = $stmtDetalleFact->fetch()) {   
		$sf_codigo       = $rowDetallefact['codigo'];
		$cantidad_x      = $rowDetallefact['cantidad'];
		$precio_x        = $rowDetallefact['precio'];
		$descuento_bob_x = $rowDetallefact['descuento_bob'];
		$monto_totalCab  = ($precio_x*$cantidad_x)-$descuento_bob_x;
		// SUBSCRIPCIÓN TIENDA
		$stmtSuscripcion = $dbh->prepare("SELECT fs.codigo, fs.cod_factura, fs.cod_facturadetalle, fs.cod_suscripcion, fs.glosa, 
										fs.cod_solicitudfacturacion, fs.catalogo, fs.id_cliente, fs.id_opcion_suscripcion, 
										fs.id_promocion, fs.id_tipo_venta, fs.idioma, fs.fecha_inicio_suscripcion, fs.id_norma
			from facturas_suscripcionestienda fs where fs.cod_facturadetalle = '$sf_codigo'");
		$stmtSuscripcion->execute();
		$detail_codigo                     = ''; 
		$detail_cod_factura                = ''; 
		$detail_cod_facturadetalle         = ''; 
		$detail_cod_suscripcion            = ''; 
		$detail_glosa                      = ''; 
		$detail_cod_solicitudfacturacion   = ''; 
		$detail_catalogo                   = ''; 
		$detail_id_cliente                 = ''; 
		$detail_id_opcion_suscripcion      = ''; 
		$detail_id_promocion               = ''; 
		$detail_id_tipo_venta              = ''; 
		$detail_idioma                     = ''; 
		$detail_fecha_inicio_suscripcion   = '';
		$detail_id_norma                   = '';
		while ($rowSuscripcion = $stmtSuscripcion->fetch()) {
			$detail_codigo                     = $rowSuscripcion['codigo']; 
			$detail_cod_factura                = $rowSuscripcion['cod_factura']; 
			$detail_cod_facturadetalle         = $rowSuscripcion['cod_facturadetalle']; 
			$detail_cod_suscripcion            = $rowSuscripcion['cod_suscripcion']; 
			$detail_glosa                      = $rowSuscripcion['glosa']; 
			$detail_cod_solicitudfacturacion   = $rowSuscripcion['cod_solicitudfacturacion']; 
			$detail_catalogo                   = $rowSuscripcion['catalogo']; 
			$detail_id_cliente                 = $rowSuscripcion['id_cliente']; 
			$detail_id_opcion_suscripcion      = $rowSuscripcion['id_opcion_suscripcion']; 
			$detail_id_promocion               = $rowSuscripcion['id_promocion']; 
			$detail_id_tipo_venta              = $rowSuscripcion['id_tipo_venta']; 
			$detail_idioma                     = $rowSuscripcion['idioma']; 
			$detail_fecha_inicio_suscripcion   = $rowSuscripcion['fecha_inicio_suscripcion'];
			$detail_id_norma                   = $rowSuscripcion['id_norma'];
		}
		// Se genera la suscripcion solo cuando la norma es DIGITAL
		if($detail_id_tipo_venta==2){
			/**
			 * GENERACIÓN DE TOKEN
			 */
			if(empty($sw_token)){
				$url_ecommerce = obtenerValorConfiguracion(109);;
				$direccion = $url_ecommerce.'usuario/login.php';
                
				$user     = 'juan.quenallata@ibnorca.org';
				$password = md5('juanito2020');
                
				// $user     = $_SESSION['globalCredUser'];
				// $password = $_SESSION['globalCredPassword'];

                $parametros=array(
						"c"   => 'IBNTOK', 
						"md5" => 1, 
						"a"   => $user, 
						"b"   => $password);
				$parametros=json_encode($parametros);
				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL,$direccion);
				curl_setopt($ch, CURLOPT_POST, TRUE);
				curl_setopt($ch, CURLOPT_POSTFIELDS, $parametros);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				$remote_server_output = json_decode(curl_exec ($ch));
				curl_close ($ch); 
                
                // Verificación de Credenciales de Acceso
                if($remote_server_output->error == 'NOK'){
                    $stmtIbnorca        = $dbh->prepare("UPDATE facturas_suscripcionestienda 
                                SET glosa = '$remote_server_output->detail'
                                WHERE cod_facturadetalle = '$sf_codigo'");
                    $flagSuccess = $stmtIbnorca->execute();
                    $sw_token = '';
                }else{
                    $sw_token = $remote_server_output->value->valor->token;
                }
                // var_dump($sw_token);
			}
            // Verificación de TOKEN
            if(!empty($sw_token)){
                // Codigo Factura
                $stmtCodfactura = $dbh->prepare("SELECT fv.codigo from facturas_venta fv where fv.cod_solicitudfacturacion='$detail_cod_solicitudfacturacion'");
                $stmtCodfactura->execute();
                $stringFacturasCod = $stmtCodfactura->fetch(PDO::FETCH_ASSOC)['codigo'];

                /**
                 * GENERACIÓN DE SUSCRIPCIÓN
                 **/
                $direccion = $url_ecommerce.'tienda/generarSuscripcion.php';
                
                $parametros=array(
                    "token"       => $sw_token,
                    "idNorma"     => $detail_id_norma, 
                    "catalogo"    => $detail_catalogo,
                    "idCliente"   => $detail_id_cliente,
                    "configuracionOpcionSuscripcionId" => $detail_id_opcion_suscripcion,
                    "promocionId" => $detail_id_promocion,
                    "precio"      => $monto_totalCab,
                    "tipo"        => "digital",
                    "idioma"      => $detail_idioma,
                    "desde"       => $detail_fecha_inicio_suscripcion,
                    "facturaId"   => $stringFacturasCod,
                    "sistema"     => "Ifinanciero",
                    "oficinaId"   => 0,
                    "app"         => "FRONTIBNT"
                );
                // var_dump($parametros);
                $parametros=json_encode($parametros);
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL,$direccion);
                curl_setopt($ch, CURLOPT_POST, TRUE);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $parametros);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                $remote_server_output = json_decode(curl_exec ($ch));
                curl_close ($ch); 
                // Resultado de Servicio SUSCRIPCIÓN
                // var_dump($remote_server_output);
                
                // Fecha de envio Suscripción
                $fecha_envio   = date('Y-m-d H:i:s');
                // ENVIADO - JSON LOCAL
                $json_enviado  = $parametros;
                // RECIBIDO - JSON SUCRIPCIÓN
                $json_recibido = json_encode($remote_server_output);

                $sw_error = $remote_server_output->error;
                $sw_cod_suscripcion = ($sw_error == "OK" ? $remote_server_output->suscripcionId : 0);
                $sw_glosa           = ($sw_error == "OK" ? 'REGISTRO CORRECTO!' : $remote_server_output->detail);
                $stmtIbnorca        = $dbh->prepare("UPDATE facturas_suscripcionestienda 
                                    SET cod_suscripcion = '$sw_cod_suscripcion',
                                    glosa = '$sw_glosa',
                                    cod_factura = '$stringFacturasCod',
                                    json_enviado = '$json_enviado',
                                    json_recibido = '$json_recibido',
                                    fecha_envio = '$fecha_envio'
                                    WHERE cod_facturadetalle = '$sf_codigo'");
                $flagSuccess=$stmtIbnorca->execute();
            }else{
                $stmtIbnorca = $dbh->prepare("UPDATE facturas_suscripcionestienda 
                                            SET glosa = 'Hubo un error en el proceso de Autenticación.',
                                            cod_factura = '$stringFacturasCod' 
                                            WHERE cod_facturadetalle = '$sf_codigo'");
                $flagSuccess=$stmtIbnorca->execute();
            }
		}
	}
	/*****************************************************************************************/
}
?>