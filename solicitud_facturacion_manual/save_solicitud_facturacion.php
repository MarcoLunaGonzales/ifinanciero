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
    $nit = $_POST["nit"];
    $observaciones = $_POST["observaciones"];
    $observaciones_2 = $_POST["observaciones_2"];
    
    if(isset($_POST["persona_contacto"]))$persona_contacto = $_POST["persona_contacto"];
    else $persona_contacto = 0;
    // $modal_totalmontos = $_POST["modal_totalmontos"];
    if(isset($modal_numeroservicio)) $modal_numeroservicio= $_POST["modal_numeroservicio"];
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
    if ($cod_facturacion == 0){//insertamos    
        $nro_correlativo=obtenerCorrelativoSolicitud();//correlativo
        $stmt = $dbh->prepare("INSERT INTO solicitudes_facturacion(cod_simulacion_servicio,cod_unidadorganizacional,cod_area,fecha_registro,fecha_solicitudfactura,cod_tipoobjeto,cod_tipopago,cod_cliente,cod_personal,razon_social,nit,observaciones,observaciones_2,nro_correlativo,cod_estado,persona_contacto,cod_estadosolicitudfacturacion,codigo_alterno,tipo_solicitud) 
        values ('$cod_simulacion','$cod_unidadorganizacional','$cod_area','$fecha_registro','$fecha_solicitudfactura','$cod_tipoobjeto','$cod_tipopago','$cod_cliente','$cod_personal','$razon_social','$nit','$observaciones','$observaciones_2','$nro_correlativo',1,'$persona_contacto',1,'$codigo_alterno',4)");//4 tipo solicitud manual
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
                //$importeInsert_ajax=$_POST["modal_montoserv".$i];
                $DescricpionInsert_ajax=$_POST["descripcion".$i];  
                $importeInsert_ajax=$_POST["modal_importe_add".$i];                
                $descuento_por_Insert_ajax=$_POST["descuento_por_add".$i];
                $descuento_bob_Insert_ajax=$_POST["descuento_bob_add".$i]; 
                $sql="INSERT INTO solicitudes_facturaciondetalle(cod_solicitudfacturacion,cod_claservicio,cantidad,precio,descripcion_alterna,descuento_por,descuento_bob,tipo_item) 
                values ('$cod_facturacion','$servicioInsert_ajax','$CantidadInsert_ajax','$importeInsert_ajax','$DescricpionInsert_ajax','$descuento_por_Insert_ajax','$descuento_bob_Insert_ajax',2)";
                $stmt = $dbh->prepare($sql);                
                $flagSuccess=$stmt->execute();                
            }
            //======================================
            //para tipo de pagos
            $sqlDeleteTiposPago="DELETE from solicitudes_facturacion_tipospago where cod_solicitudfacturacion=$cod_facturacion";
            $stmtDelTiposPago = $dbh->prepare($sqlDeleteTiposPago);
            $stmtDelTiposPago->execute();
            //si existe array de objetos tipopago
            if(isset($_POST['tiposPago_facturacion'])){
                $tiposPago_facturacion= json_decode($_POST['tiposPago_facturacion']);
                $nF=cantidadF($tiposPago_facturacion[0]);
                for($j=0;$j<$nF;$j++){
                    $codigo_tipopago=$tiposPago_facturacion[0][$j]->codigo_tipopago;
                    $monto_porcentaje=$tiposPago_facturacion[0][$j]->monto_porcentaje;
                    $monto_bob=$tiposPago_facturacion[0][$j]->monto_bob;                                
                    // echo "codigo_tipopago:".$codigo_tipopago."<br>";
                    // echo "monto_porcentaje:".$monto_porcentaje."<br>";        
                    // echo "monto_bob:".$monto_bob."<br>";          
                    $sqlTiposPago="INSERT INTO solicitudes_facturacion_tipospago(cod_solicitudfacturacion, cod_tipopago, porcentaje, monto) VALUES ('$cod_facturacion','$codigo_tipopago','$monto_porcentaje','$monto_bob')";
                    $stmtTiposPago = $dbh->prepare($sqlTiposPago);
                    $stmtTiposPago->execute();
                }
            }else{
                $codigo_tipopago=$cod_tipopago;
                $monto_porcentaje=100;
                $monto_bob=$_POST["monto_total_a"];
                // echo "codigo_tipopago:".$codigo_tipopago."<br>";
                // echo "monto_porcentaje:".$monto_porcentaje."<br>";        
                // echo "monto_bob:".$monto_bob."<br>";    
                $sqlTiposPago="INSERT INTO solicitudes_facturacion_tipospago(cod_solicitudfacturacion, cod_tipopago, porcentaje, monto) VALUES ('$cod_facturacion','$codigo_tipopago','$monto_porcentaje','$monto_bob')";
                $stmtTiposPago = $dbh->prepare($sqlTiposPago);
                $stmtTiposPago->execute();
            }
            //para porcetnaje de areas
            $sqlDeleteAreas="DELETE from solicitudes_facturacion_areas where cod_solicitudfacturacion=$cod_facturacion";
            $stmtDelAreas = $dbh->prepare($sqlDeleteAreas);
            $stmtDelAreas->execute();
            $sqlDeleteAreasUO="DELETE from solicitudes_facturacion_areas_uo where cod_solicitudfacturacion=$cod_facturacion";
            $stmtDelAreasUO = $dbh->prepare($sqlDeleteAreasUO);
            $stmtDelAreasUO->execute();
            //si existe array de objetos areas
            if(isset($_POST['areas_facturacion'])){
                $areas_facturacion= json_decode($_POST['areas_facturacion']);
                $nF=cantidadF($areas_facturacion[0]);
                for($j=0;$j<$nF;$j++){
                    $codigo_area=$areas_facturacion[0][$j]->codigo_areas;
                    $monto_porcentaje=$areas_facturacion[0][$j]->monto_porcentaje;
                    $monto_bob=$areas_facturacion[0][$j]->monto_bob;                                
                    if($monto_porcentaje>0){
                        // echo "codigo_area:".$codigo_area."<br>";
                        // echo "monto_porcentaje:".$monto_porcentaje."<br>";        
                        // echo "monto_bob:".$monto_bob."<br>";          
                        $sqlTiposPago="INSERT INTO solicitudes_facturacion_areas(cod_solicitudfacturacion, cod_area, porcentaje, monto) VALUES ('$cod_facturacion','$codigo_area','$monto_porcentaje','$monto_bob')";
                        $stmtTiposPago = $dbh->prepare($sqlTiposPago);
                        $stmtTiposPago->execute();
                        //si existe array de unidades
                        if(isset($_POST['unidades_facturacion'])){
                            $unidades_facturacion=json_decode($_POST['unidades_facturacion']);
                            $nFU=cantidadF($unidades_facturacion[$j]);
                            if($nFU>0){
                                for($u=0;$u<$nFU;$u++){                                
                                    $codigo_unidad=$unidades_facturacion[$j][$u]->codigo_unidad;
                                    $monto_porcentaje_uo=$unidades_facturacion[$j][$u]->monto_porcentaje;
                                    $monto_bob_uo=$unidades_facturacion[$j][$u]->monto_bob;                                
                                    // echo "codigo_unidad:".$codigo_unidad."<br>";
                                    // echo "monto_porcentaje:".$monto_porcentaje."<br>";        
                                    // echo "monto_bob:".$monto_bob."<br>";    
                                    $sqlUnidades="INSERT INTO solicitudes_facturacion_areas_uo(cod_solicitudfacturacion,cod_area, cod_uo, porcentaje, monto) VALUES ('$cod_facturacion','$codigo_area','$codigo_unidad','$monto_porcentaje_uo','$monto_bob_uo')";
                                    $stmtUnidades = $dbh->prepare($sqlUnidades);
                                    $stmtUnidades->execute();                               
                                }
                            }else{                            
                                $sqlUnidades="INSERT INTO solicitudes_facturacion_areas_uo(cod_solicitudfacturacion,cod_area, cod_uo, porcentaje, monto) VALUES ('$cod_facturacion','$codigo_area','$cod_unidadorganizacional',100,'$monto_bob')";
                                $stmtUnidades = $dbh->prepare($sqlUnidades);
                                $stmtUnidades->execute();                               
                            }                        
                        }else{                        
                            $sqlUnidades="INSERT INTO solicitudes_facturacion_areas_uo(cod_solicitudfacturacion,cod_area, cod_uo, porcentaje, monto) VALUES ('$cod_facturacion','$codigo_area','$cod_unidadorganizacional',100,'$monto_bob')";
                            $stmtUnidades = $dbh->prepare($sqlUnidades);
                            $stmtUnidades->execute();
                        }
                    }
                }
            }else{
                $codigo_area=$cod_area;
                $monto_porcentaje=100;
                $monto_bob=$_POST["monto_total_a"];
                // echo "codigo_area:".$codigo_area."<br>";
                // echo "monto_porcentaje:".$monto_porcentaje."<br>";        
                // echo "monto_bob:".$monto_bob."<br>";    
                $sqlTiposPago="INSERT INTO solicitudes_facturacion_areas(cod_solicitudfacturacion, cod_area, porcentaje, monto) VALUES ('$cod_facturacion','$codigo_area','$monto_porcentaje','$monto_bob')";
                $stmtTiposPago = $dbh->prepare($sqlTiposPago);
                $stmtTiposPago->execute();
                $sqlUnidades="INSERT INTO solicitudes_facturacion_areas_uo(cod_solicitudfacturacion,cod_area, cod_uo, porcentaje, monto) VALUES ('$cod_facturacion','$codigo_area','$cod_unidadorganizacional','$monto_porcentaje','$monto_bob')";
                $stmtUnidades = $dbh->prepare($sqlUnidades);
                $stmtUnidades->execute();
            }

            //borramos los archivos
            $sqlDel="DELETE FROM archivos_adjuntos_solicitud_facturacion where cod_solicitud_facturacion=$cod_facturacion";
            $stmtDel = $dbh->prepare($sqlDel);
            $stmtDel->execute();
            //subir archivos al servidor
            //Como el elemento es un arreglos utilizamos foreach para extraer todos los valores
            $nArchivosCabecera=$_POST["cantidad_archivosadjuntos"];;
            for ($ar=1; $ar <= $nArchivosCabecera ; $ar++) { 
                if(isset($_POST['codigo_archivo'.$ar])){
                    if($_FILES['documentos_cabecera'.$ar]["name"]){
                      $filename = $_FILES['documentos_cabecera'.$ar]["name"]; //Obtenemos el nombre original del archivos
                      $source = $_FILES['documentos_cabecera'.$ar]["tmp_name"]; //Obtenemos un nombre temporal del archivos    
                      $directorio = '../assets/archivos-respaldo/archivos_solicitudes_facturacion/SOLFAC-'.$cod_facturacion; //Declaramos una  variable con la ruta donde guardaremos los archivoss
                      //Validamos si la ruta de destino existe, en caso de no existir la creamos
                      if(!file_exists($directorio)){
                                mkdir($directorio, 0777,true) or die("No se puede crear el directorio de extracci&oacute;n");    
                      }
                      $target_path = $directorio.'/'.$filename; //Indicamos la ruta de destino, así como el nombre del archivos
                      //Movemos y validamos que el archivos se haya cargado correctamente
                      //El primer campo es el origen y el segundo el destino
                      if(move_uploaded_file($source, $target_path)) { 
                        echo "Archivo guargado.";
                        $tipo=$_POST['codigo_archivo'.$ar];
                        $descripcion=$_POST['nombre_archivo'.$ar];
                        // $tipoPadre=2708;
                        $sqlInsert="INSERT INTO archivos_adjuntos_solicitud_facturacion (cod_tipoarchivo,descripcion,direccion_archivo,cod_solicitud_facturacion) 
                        VALUES ('$tipo','$descripcion','$target_path','$cod_facturacion')";
                        $stmtInsert = $dbh->prepare($sqlInsert);
                        $stmtInsert->execute();    
                        // print_r($sqlInsert);
                      }else {    
                          echo "Error al guardar archivo.";
                      } 
                    }
                }
            }
        }
        //enviar propuestas para la actualizacion de ibnorca
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
        $stmt = $dbh->prepare("UPDATE solicitudes_facturacion set cod_unidadorganizacional='$cod_unidadorganizacional',cod_area='$cod_area',fecha_registro='$fecha_registro',fecha_solicitudfactura='$fecha_solicitudfactura',cod_tipoobjeto='$cod_tipoobjeto',cod_tipopago='$cod_tipopago',cod_cliente='$cod_cliente',cod_personal='$cod_personal',razon_social='$razon_social',nit='$nit',observaciones='$observaciones',observaciones_2='$observaciones_2',persona_contacto='$persona_contacto'
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
                    $importeInsert=$_POST["modal_importe".$i];
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
                //$importeInsert_ajax=$_POST["modal_montoserv".$i];
                $DescricpionInsert_ajax=$_POST["descripcion".$i];  
                $importeInsert_ajax=$_POST["modal_importe_add".$i];                
                $descuento_por_Insert_ajax=$_POST["descuento_por_add".$i];
                $descuento_bob_Insert_ajax=$_POST["descuento_bob_add".$i]; 
                $sql="INSERT INTO solicitudes_facturaciondetalle(cod_solicitudfacturacion,cod_claservicio,cantidad,precio,descripcion_alterna,descuento_por,descuento_bob,tipo_item) 
                values ('$cod_facturacion','$servicioInsert_ajax','$CantidadInsert_ajax','$importeInsert_ajax','$DescricpionInsert_ajax','$descuento_por_Insert_ajax','$descuento_bob_Insert_ajax',2)";
                $stmt = $dbh->prepare($sql);                
                $flagSuccess=$stmt->execute();                
            }
            //======================================
            //para tipo de pagos
            $sqlDeleteTiposPago="DELETE from solicitudes_facturacion_tipospago where cod_solicitudfacturacion=$cod_facturacion";
            $stmtDelTiposPago = $dbh->prepare($sqlDeleteTiposPago);
            $stmtDelTiposPago->execute();
            //si existe array de objetos tipopago
            if(isset($_POST['tiposPago_facturacion'])){
                $tiposPago_facturacion= json_decode($_POST['tiposPago_facturacion']);
                $nF=cantidadF($tiposPago_facturacion[0]);
                for($j=0;$j<$nF;$j++){
                    $codigo_tipopago=$tiposPago_facturacion[0][$j]->codigo_tipopago;
                    $monto_porcentaje=$tiposPago_facturacion[0][$j]->monto_porcentaje;
                    $monto_bob=$tiposPago_facturacion[0][$j]->monto_bob;                                
                    // echo "codigo_tipopago:".$codigo_tipopago."<br>";
                    // echo "monto_porcentaje:".$monto_porcentaje."<br>";        
                    // echo "monto_bob:".$monto_bob."<br>";          
                    $sqlTiposPago="INSERT INTO solicitudes_facturacion_tipospago(cod_solicitudfacturacion, cod_tipopago, porcentaje, monto) VALUES ('$cod_facturacion','$codigo_tipopago','$monto_porcentaje','$monto_bob')";
                    $stmtTiposPago = $dbh->prepare($sqlTiposPago);
                    $stmtTiposPago->execute();
                }
            }else{
                $codigo_tipopago=$cod_tipopago;
                $monto_porcentaje=100;
                $monto_bob=$_POST["monto_total_a"];
                // echo "codigo_tipopago:".$codigo_tipopago."<br>";
                // echo "monto_porcentaje:".$monto_porcentaje."<br>";        
                // echo "monto_bob:".$monto_bob."<br>";    
                $sqlTiposPago="INSERT INTO solicitudes_facturacion_tipospago(cod_solicitudfacturacion, cod_tipopago, porcentaje, monto) VALUES ('$cod_facturacion','$codigo_tipopago','$monto_porcentaje','$monto_bob')";
                $stmtTiposPago = $dbh->prepare($sqlTiposPago);
                $stmtTiposPago->execute();
            }
            //para porcetnaje de areas
            $sqlDeleteAreas="DELETE from solicitudes_facturacion_areas where cod_solicitudfacturacion=$cod_facturacion";
            $stmtDelAreas = $dbh->prepare($sqlDeleteAreas);
            $stmtDelAreas->execute();
            $sqlDeleteAreasUO="DELETE from solicitudes_facturacion_areas_uo where cod_solicitudfacturacion=$cod_facturacion";
            $stmtDelAreasUO = $dbh->prepare($sqlDeleteAreasUO);
            $stmtDelAreasUO->execute();
            //si existe array de objetos areas            
            if(isset($_POST['areas_facturacion'])){
                $areas_facturacion= json_decode($_POST['areas_facturacion']);
                $nF=cantidadF($areas_facturacion[0]);
                for($j=0;$j<$nF;$j++){
                    $codigo_area=$areas_facturacion[0][$j]->codigo_areas;
                    $monto_porcentaje=$areas_facturacion[0][$j]->monto_porcentaje;
                    $monto_bob=$areas_facturacion[0][$j]->monto_bob;                                
                    if($monto_porcentaje>0){
                        // echo "codigo_area:".$codigo_area."<br>";
                        // echo "monto_porcentaje:".$monto_porcentaje."<br>";        
                        // echo "monto_bob:".$monto_bob."<br>";          
                        $sqlTiposPago="INSERT INTO solicitudes_facturacion_areas(cod_solicitudfacturacion, cod_area, porcentaje, monto) VALUES ('$cod_facturacion','$codigo_area','$monto_porcentaje','$monto_bob')";
                        $stmtTiposPago = $dbh->prepare($sqlTiposPago);
                        $stmtTiposPago->execute();
                        //si existe array de unidades
                        if(isset($_POST['unidades_facturacion'])){
                            $unidades_facturacion=json_decode($_POST['unidades_facturacion']);
                            $nFU=cantidadF($unidades_facturacion[$j]);
                            if($nFU>0){
                                for($u=0;$u<$nFU;$u++){                                
                                    $codigo_unidad=$unidades_facturacion[$j][$u]->codigo_unidad;
                                    $monto_porcentaje_uo=$unidades_facturacion[$j][$u]->monto_porcentaje;
                                    $monto_bob_uo=$unidades_facturacion[$j][$u]->monto_bob;                                
                                    // echo "codigo_unidad:".$codigo_unidad."<br>";
                                    // echo "monto_porcentaje:".$monto_porcentaje."<br>";        
                                    // echo "monto_bob:".$monto_bob."<br>";    
                                    $sqlUnidades="INSERT INTO solicitudes_facturacion_areas_uo(cod_solicitudfacturacion,cod_area, cod_uo, porcentaje, monto) VALUES ('$cod_facturacion','$codigo_area','$codigo_unidad','$monto_porcentaje_uo','$monto_bob_uo')";
                                    $stmtUnidades = $dbh->prepare($sqlUnidades);
                                    $stmtUnidades->execute();                               
                                }
                            }else{                            
                                $sqlUnidades="INSERT INTO solicitudes_facturacion_areas_uo(cod_solicitudfacturacion,cod_area, cod_uo, porcentaje, monto) VALUES ('$cod_facturacion','$codigo_area','$cod_unidadorganizacional',100,'$monto_bob')";
                                $stmtUnidades = $dbh->prepare($sqlUnidades);
                                $stmtUnidades->execute();                               
                            }                        
                        }else{                        
                            $sqlUnidades="INSERT INTO solicitudes_facturacion_areas_uo(cod_solicitudfacturacion,cod_area, cod_uo, porcentaje, monto) VALUES ('$cod_facturacion','$codigo_area','$cod_unidadorganizacional',100,'$monto_bob')";
                            $stmtUnidades = $dbh->prepare($sqlUnidades);
                            $stmtUnidades->execute();
                        }
                    }
                }
            }else{
                $codigo_area=$cod_area;
                $monto_porcentaje=100;
                $monto_bob=$_POST["monto_total_a"];
                // echo "codigo_area:".$codigo_area."<br>";
                // echo "monto_porcentaje:".$monto_porcentaje."<br>";        
                // echo "monto_bob:".$monto_bob."<br>";    
                $sqlTiposPago="INSERT INTO solicitudes_facturacion_areas(cod_solicitudfacturacion, cod_area, porcentaje, monto) VALUES ('$cod_facturacion','$codigo_area','$monto_porcentaje','$monto_bob')";
                $stmtTiposPago = $dbh->prepare($sqlTiposPago);
                $stmtTiposPago->execute();
                $sqlUnidades="INSERT INTO solicitudes_facturacion_areas_uo(cod_solicitudfacturacion,cod_area, cod_uo, porcentaje, monto) VALUES ('$cod_facturacion','$codigo_area','$cod_unidadorganizacional','$monto_porcentaje','$monto_bob')";
                $stmtUnidades = $dbh->prepare($sqlUnidades);
                $stmtUnidades->execute();
            }
            //borramos los archivos
            // $sqlDel="DELETE FROM archivos_adjuntos_solicitud_facturacion where cod_solicitud_facturacion=$cod_facturacion";
            // $stmtDel = $dbh->prepare($sqlDel);
            // $stmtDel->execute();
            //subir archivos al servidor
            //Como el elemento es un arreglos utilizamos foreach para extraer todos los valores
            $nArchivosCabecera=$_POST["cantidad_archivosadjuntos"];;
            for ($ar=1; $ar <= $nArchivosCabecera ; $ar++) { 
                if(isset($_POST['codigo_archivo'.$ar])){
                    if($_FILES['documentos_cabecera'.$ar]["name"]){
                      $filename = $_FILES['documentos_cabecera'.$ar]["name"]; //Obtenemos el nombre original del archivos
                      $source = $_FILES['documentos_cabecera'.$ar]["tmp_name"]; //Obtenemos un nombre temporal del archivos    
                      $directorio = '../assets/archivos-respaldo/archivos_solicitudes_facturacion/SOLFAC-'.$cod_facturacion; //Declaramos una  variable con la ruta donde guardaremos los archivoss
                      //Validamos si la ruta de destino existe, en caso de no existir la creamos
                      if(!file_exists($directorio)){
                                mkdir($directorio, 0777,true) or die("No se puede crear el directorio de extracci&oacute;n");    
                      }
                      $target_path = $directorio.'/'.$filename; //Indicamos la ruta de destino, así como el nombre del archivos
                      //Movemos y validamos que el archivos se haya cargado correctamente
                      //El primer campo es el origen y el segundo el destino
                      if(move_uploaded_file($source, $target_path)) { 
                        echo "Archivo guargado.";
                        $tipo=$_POST['codigo_archivo'.$ar];
                        $descripcion=$_POST['nombre_archivo'.$ar];
                        // $tipoPadre=2708;
                        $sqlInsert="INSERT INTO archivos_adjuntos_solicitud_facturacion (cod_tipoarchivo,descripcion,direccion_archivo,cod_solicitud_facturacion) 
                        VALUES ('$tipo','$descripcion','$target_path','$cod_facturacion')";
                        $stmtInsert = $dbh->prepare($sqlInsert);
                        $stmtInsert->execute();    
                        // print_r($sqlInsert);
                      }else {    
                          echo "Error al guardar archivo.";
                      } 
                    }
                }
            }
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
} catch(PDOException $ex){
    //manejar error
    echo "Un error ocurrio".$ex->getMessage();
}
?>