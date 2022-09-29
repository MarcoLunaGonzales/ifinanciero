<?php
require_once '../layouts/bodylogin.php';
require_once '../conexion.php';
require_once '../functions.php';
require_once '../functionsGeneral.php';
require_once 'configModule.php';

ini_set('display_errors',1);
session_start();
$globalUser=$_SESSION["globalUser"];
$dbh = new Conexion();

$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);//para mostrar errores en la ejecucion

try {
    $codigo_alterno = $_POST["Codigo_alterno"];
    $cod_simulacion = $_POST["cod_simulacion"];
    $cod_facturacion = $_POST["cod_facturacion"];    
    $cod_unidadorganizacional = $_POST["cod_uo"];
    $cod_area = $_POST["cod_area"];
    $fecha_registro = $_POST["fecha_registro"];
    $fecha_solicitudfactura = $_POST["fecha_solicitudfactura"];
        
    $cod_cliente = $_POST["cod_cliente"];
    $cod_personal = $_POST["cod_personal"];
    $razon_social = $_POST["razon_social"];

    $patron15 = "[a-zA-Z áéíóúÁÉÍÓÚñÑ]";//solo numeros,letras M y m, tildes y la ñ
    $patron1="[\n|\r|\n\r]";//quitamos salto de linea
    $razon_social = preg_replace($patron1, " ", $razon_social);
    $razon_social = preg_replace($patron15, "", $razon_social);
    $razon_social = str_replace('"', " ", $razon_social);//quitamos comillas dobles
    $razon_social = str_replace("'", " ", $razon_social);//quitamos comillas simples

    $nit = $_POST["nit"];
    
    if(isset($_POST["complemento"])){
        $complemento = $_POST["complemento"];
    }else{
        $complemento=null;
    }

    if(isset($_POST["fecha_facturacion"])){
        $fecha_facturacion = $_POST["fecha_facturacion"];
    }else{
        $fecha_facturacion=$fecha_solicitudfactura;
    }
    

    if(isset($_POST["tipo_documento"])){
        $tipo_documento = $_POST["tipo_documento"];
    }else{
        $tipo_documento = 1; //CI
    }
    if (isset($_POST['nro_tarjeta'])) {
        $siat_nroTarjeta=$_POST['nro_tarjeta'];
        $siat_nroTarjeta=str_replace("*","0",$siat_nroTarjeta);
    }else{
        $siat_nroTarjeta=null;
    }
    

    $observaciones = $_POST["observaciones"];
    $observaciones_2 = $_POST["observaciones_2"];
    $correo_contacto = $_POST["correo_contacto"];
    
    if(isset($_POST["persona_contacto"]))$persona_contacto = $_POST["persona_contacto"];
    else $persona_contacto = 0;
    // $modal_totalmontos = $_POST["modal_totalmontos"];
    if(isset($_POST["modal_numeroservicio"])) $modal_numeroservicio= $_POST["modal_numeroservicio"];
    else $modal_numeroservicio=0;
    if(isset($_POST["cod_tipoobjeto"])){
        $cod_tipoobjeto = $_POST["cod_tipoobjeto"];
    }else $cod_tipoobjeto=0;
    if(isset($_POST["cod_tipopago"])){
        $cod_tipopago = $_POST["cod_tipopago"];
    }else $cod_tipopago=0;
    
    if(isset($_POST['q'])){
        $cod_personal=$_POST['q'];
    }
    if (isset($_POST["dias_credito"]))$dias_credito = $_POST["dias_credito"];
    else $dias_credito = '';
    if(!isset($_POST["cod_uo"]) && !isset($_POST["cod_area"])){
        $cod_area=0;
        $cod_unidadorganizacional=0;
    }
    if($cod_area!=null && $cod_area!=0 && $cod_area!="" && $cod_unidadorganizacional!=null && $cod_unidadorganizacional!=0 && $cod_unidadorganizacional!=""){
        if ($cod_facturacion == 0){//insertamos    
            $nro_correlativo=obtenerCorrelativoSolicitud();//correlativo
            $stmt = $dbh->prepare("INSERT INTO solicitudes_facturacion(cod_simulacion_servicio,cod_unidadorganizacional,cod_area,fecha_registro,fecha_solicitudfactura,cod_tipoobjeto,cod_tipopago,cod_cliente,cod_personal,razon_social,nit,observaciones,observaciones_2,nro_correlativo,persona_contacto,cod_estadosolicitudfacturacion,codigo_alterno,tipo_solicitud,dias_credito,correo_contacto,siat_tipoidentificacion,siat_complemento,fecha_facturacion,siat_nroTarjeta) 
            values ('$cod_simulacion','$cod_unidadorganizacional','$cod_area','$fecha_registro','$fecha_solicitudfactura','$cod_tipoobjeto','$cod_tipopago','$cod_cliente','$cod_personal','$razon_social','$nit','$observaciones','$observaciones_2','$nro_correlativo','$persona_contacto',1,'$codigo_alterno',4,'$dias_credito','$correo_contacto','$tipo_documento','$complemento','$fecha_facturacion','$siat_nroTarjeta')");//4 tipo solicitud manual
            $flagSuccess=$stmt->execute();
            // $flagSuccess=true;
            if($flagSuccess){
                //sacamos el codigo insertado
               $stmt = $dbh->prepare("SELECT codigo from solicitudes_facturacion where cod_simulacion_servicio=$cod_simulacion ORDER BY codigo desc LIMIT 1");
               $stmt->execute();           
               while ($rowPre = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $cod_facturacion=$rowPre['codigo'];
                }           
                //los agregados con  ajax
                $cantidad_filas = $_POST["cantidad_filas"];
                for ($i=1;$i<=$cantidad_filas;$i++){                
                    $servicioInsert_ajax=$_POST["modal_editservicio".$i];
                    $CantidadInsert_ajax=$_POST["cantidad_servicios".$i];
                    // $importeInsert_ajax=$_POST["modal_importe_add".$i];
                    // $importeInsert_ajax=$_POST["modal_montoserv".$i];
                    $DescricpionInsert_ajax=$_POST["descripcion".$i];  
                    $importeInsert_ajax=$_POST["modal_importe_add".$i]/$CantidadInsert_ajax;
                    $importeInsert_ajax=round($importeInsert_ajax,2);
                    $descuento_por_Insert_ajax=$_POST["descuento_por_add".$i];
                    $descuento_bob_Insert_ajax=$_POST["descuento_bob_add".$i]; 
                    $sql="INSERT INTO solicitudes_facturaciondetalle(cod_solicitudfacturacion,cod_claservicio,cantidad,precio,descripcion_alterna,descuento_por,descuento_bob,tipo_item) 
                    values ('$cod_facturacion','$servicioInsert_ajax','$CantidadInsert_ajax','$importeInsert_ajax','$DescricpionInsert_ajax','$descuento_por_Insert_ajax','$descuento_bob_Insert_ajax',2)";
                    $stmt = $dbh->prepare($sql);                
                    $flagSuccess=$stmt->execute();                
                }
                //======================================
                //para distribucion de tipo de pagos y areas
                $tipo_solicitud=4;
                require_once '../simulaciones_servicios/save_distribucion_montos_solfac.php';
                //borramos los archivos
                $sqlDel="DELETE FROM archivos_adjuntos_solicitud_facturacion where cod_solicitud_facturacion=$cod_facturacion";
                $stmtDel = $dbh->prepare($sqlDel);
                $stmtDel->execute();
                require_once '../simulaciones_servicios/insertar_archivosadjuntos.php';
            }
            //enviar propuestas para la actualizacion de ibnorca
            date_default_timezone_set('America/La_Paz');
            $fechaHoraActual=date("Y-m-d H:i:s");
            $idTipoObjeto=2709;
            $idObjeto=2726; //regristado
            $obs="Registro de Solicitud Facturación";
            if(isset($_POST['usuario_ibnored_u'])){
                $u=$_POST['usuario_ibnored_u'];
                actualizarEstadosObjetosIbnorca($idTipoObjeto,$idObjeto,$u,$cod_facturacion,$fechaHoraActual,$obs);
            }else{
                actualizarEstadosObjetosIbnorca($idTipoObjeto,$idObjeto,$globalUser,$cod_facturacion,$fechaHoraActual,$obs);
            }
            if(isset($_POST['usuario_ibnored'])){
                $q=$_POST['usuario_ibnored'];
                $v=$_POST['usuario_ibnored_v'];
                $s=$_POST['usuario_ibnored_s'];
                $u=$_POST['usuario_ibnored_u'];
                showAlertSuccessError($flagSuccess,"../".$urlSolicitudfactura_2."&q=".$q."&v=".$v."&s=".$s."&u=".$u);
            }else{
                showAlertSuccessError($flagSuccess,"../".$urlSolicitudfactura_2);  
            }
            

            //$stmt->debugDumpParams();
        }else{//update
            //actualizamos los campos estaticos
            $stmt = $dbh->prepare("UPDATE solicitudes_facturacion set cod_unidadorganizacional='$cod_unidadorganizacional',cod_area='$cod_area',fecha_registro='$fecha_registro',fecha_solicitudfactura='$fecha_solicitudfactura',cod_tipoobjeto='$cod_tipoobjeto',cod_tipopago='$cod_tipopago',cod_cliente='$cod_cliente',cod_personal='$cod_personal',razon_social='$razon_social',nit='$nit',observaciones='$observaciones',observaciones_2='$observaciones_2',persona_contacto='$persona_contacto',dias_credito = '$dias_credito',correo_contacto='$correo_contacto',siat_tipoidentificacion='$tipo_documento',siat_complemento='$complemento',fecha_facturacion='$fecha_facturacion',siat_nroTarjeta='$siat_nroTarjeta'
            where codigo = $cod_facturacion");      
            $flagSuccess=$stmt->execute();
            if($flagSuccess){
                //borramos los datos y insertados en detalle
                $stmtDelete = $dbh->prepare("DELETE from solicitudes_facturaciondetalle where cod_solicitudfacturacion= $cod_facturacion");      
                $stmtDelete->execute();
                for ($i=1;$i<=$modal_numeroservicio-1;$i++){
                    $servicioInsert="";
                    $CantidadInsert="";
                    $importeInsert="";
                    $DescricpionInsert="";                
                    if(isset($_POST["servicio".$i])){
                        $servicioInsert=$_POST["servicio_a".$i];
                        $CantidadInsert=$_POST["cantidad_a".$i];
                        // $importeInsert=$_POST["modal_importe".$i];
                        $importeInsert=$_POST["modal_importe".$i]/$CantidadInsert;
                        $importeInsert=round($importeInsert,2);
                        
                        $DescricpionInsert=$_POST["descripcion_alterna".$i];
                        $descuento_por_Insert=$_POST["descuento_por".$i];
                        $descuento_bob_Insert=$_POST["descuento_bob".$i];
                    }
                    if($servicioInsert!=0 || $servicioInsert!=""){
                        $stmt = $dbh->prepare("INSERT INTO solicitudes_facturaciondetalle(cod_solicitudfacturacion,cod_claservicio,cantidad,precio,descripcion_alterna,descuento_por,descuento_bob,tipo_item) 
                        values ('$cod_facturacion','$servicioInsert','$CantidadInsert','$importeInsert','$DescricpionInsert','$descuento_por_Insert','$descuento_bob_Insert',2)");//porque fue introducido desde servicios adicionales
                        $flagSuccess=$stmt->execute();
                    }
                }
                 //los agregados con  ajax
                $cantidad_filas = $_POST["cantidad_filas"];
                for ($i=1;$i<=$cantidad_filas;$i++){                
                    $servicioInsert_ajax=$_POST["modal_editservicio".$i];
                    $CantidadInsert_ajax=$_POST["cantidad_servicios".$i];
                    // $importeInsert_ajax=$_POST["modal_montoserv".$i];
                    $DescricpionInsert_ajax=$_POST["descripcion".$i];  
                    $importeInsert_ajax=$_POST["modal_importe_add".$i]/$CantidadInsert_ajax;
                    $importeInsert_ajax=round($importeInsert_ajax,2);
                    // $importeInsert=$_POST["monto_precio".$i];
                    $descuento_por_Insert_ajax=$_POST["descuento_por_add".$i];
                    $descuento_bob_Insert_ajax=$_POST["descuento_bob_add".$i]; 
                    $sql="INSERT INTO solicitudes_facturaciondetalle(cod_solicitudfacturacion,cod_claservicio,cantidad,precio,descripcion_alterna,descuento_por,descuento_bob,tipo_item) 
                    values ('$cod_facturacion','$servicioInsert_ajax','$CantidadInsert_ajax','$importeInsert_ajax','$DescricpionInsert_ajax','$descuento_por_Insert_ajax','$descuento_bob_Insert_ajax',2)";
                    $stmt = $dbh->prepare($sql);                
                    $flagSuccess=$stmt->execute();                
                }
                //======================================
                //para distribucion de tipo de pagos y areas
                $tipo_solicitud=4;
                require_once '../simulaciones_servicios/save_distribucion_montos_solfac.php';
                //borramos los archivos
                // $sqlDel="DELETE FROM archivos_adjuntos_solicitud_facturacion where cod_solicitud_facturacion=$cod_facturacion";
                // $stmtDel = $dbh->prepare($sqlDel);
                // $stmtDel->execute();
                //subir archivos al servidor
                //Como el elemento es un arreglos utilizamos foreach para extraer todos los valores
                require_once '../simulaciones_servicios/insertar_archivosadjuntos.php';
            }
            if(isset($_POST['usuario_ibnored'])){
                $q=$_POST['usuario_ibnored'];
                $v=$_POST['usuario_ibnored_v'];
                $s=$_POST['usuario_ibnored_s'];
                $u=$_POST['usuario_ibnored_u'];
                showAlertSuccessError($flagSuccess,"../".$urlSolicitudfactura_2."&q=".$q."&v=".$v."&s=".$s."&u=".$u);
            }else{
                showAlertSuccessError($flagSuccess,"../".$urlSolicitudfactura_2);  
            }
        }
    }else{
        if(isset($_POST['usuario_ibnored'])){
            $q=$_POST['usuario_ibnored'];
            $v=$_POST['usuario_ibnored_v'];
            $s=$_POST['usuario_ibnored_s'];
            $u=$_POST['usuario_ibnored_u'];
            showAlertSuccessError(false,"../".$urlSolicitudfactura_2."&q=".$q."&v=".$v."&s=".$s."&u=".$u);
        }else{
            showAlertSuccessError(false,"../".$urlSolicitudfactura_2);  
        }
    }
} catch(PDOException $ex){
    //manejar error
    echo "Un error ocurrio".$ex->getMessage();
}
?>