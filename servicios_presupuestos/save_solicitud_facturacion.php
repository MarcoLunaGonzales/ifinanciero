<?php
//require_once '../layouts/bodylogin.php';
require_once 'conexion.php';
require_once 'functions.php';
require_once 'configModule.php';
ini_set('display_errors',1);


$globalUser=$_SESSION["globalUser"];

$dbh = new Conexion();

$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);//para mostrar errores en la ejecucion

try {
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
    $persona_contacto = $_POST["persona_contacto"];

    $modal_totalmontos = $_POST["modal_totalmontos"];
    $modal_numeroservicio = $_POST["modal_numeroservicio"];
    if(isset($_POST["cod_tipoobjeto"])){
        $cod_tipoobjeto = $_POST["cod_tipoobjeto"];
    }else $cod_tipoobjeto=0;
    if(isset($_POST["cod_tipopago"])){
        $cod_tipopago = $_POST["cod_tipopago"];
    }else $cod_tipopago=0;
    

    if ($cod_facturacion == 0){//insertamos        

       $nro_correlativo=obtenerCorrelativoSolicitud();//correlativo
        $stmt = $dbh->prepare("INSERT INTO solicitudes_facturacion(cod_simulacion_servicio,cod_unidadorganizacional,cod_area,fecha_registro,fecha_solicitudfactura,cod_tipoobjeto,cod_tipopago,cod_cliente,cod_personal,razon_social,nit,observaciones,nro_correlativo,cod_estado,persona_contacto,cod_estadosolicitudfacturacion) 
        values ('$cod_simulacion','$cod_unidadorganizacional','$cod_area','$fecha_registro','$fecha_solicitudfactura','$cod_tipoobjeto','$cod_tipopago','$cod_cliente','$cod_personal','$razon_social','$nit','$observaciones','$nro_correlativo',1,'$persona_contacto',1)");

        $flagSuccess=$stmt->execute();
        $flagSuccess=true;
        if($flagSuccess){
            //sacamos el codigo insertado
            $stmt = $dbh->prepare("SELECT codigo from solicitudes_facturacion where cod_simulacion_servicio=$cod_simulacion ORDER BY codigo desc LIMIT 1");
            $stmt->execute();           
            while ($rowPre = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $cod_facturacion=$rowPre['codigo'];
            }
            for ($i=1;$i<=$modal_numeroservicio-1;$i++){
                $servicioInsert="";
                $CantidadInsert="";
                $importeInsert="";
                $DescricpionInsert="";
                // echo "i:".$i;

                if(isset($_POST["servicio".$i])){
                    $servicioInsert=$_POST["servicio_a".$i];
                    $CantidadInsert=$_POST["cantidad_a".$i];
                    $importeInsert=$_POST["modal_importe".$i];
                    $DescricpionInsert=$_POST["descripcion_alterna".$i];
                    $descuento_por_Insert=$_POST["descuento_por".$i];
                    $descuento_bob_Insert=$_POST["descuento_bob".$i]; 
                }
                if($servicioInsert!=0 || $servicioInsert!=""){
                    // echo " servicio:".$servicioInsert."<br>";
                    // echo " cantida:".$CantidadInsert."<br>";
                    // echo " importe:".$importeInsert."<br>";
                    // echo " Descricpion:".$DescricpionInsert."<br>";
                    // echo " descuento_por_:".$descuento_por_Insert."<br>";
                    // echo " descuento_bob_:".$descuento_bob_Insert."<br>";

                    $stmt = $dbh->prepare("INSERT INTO solicitudes_facturaciondetalle(cod_solicitudfacturacion,cod_claservicio,cantidad,precio,descripcion_alterna,descuento_por,descuento_bob,tipo_item) 
                    values ('$cod_facturacion','$servicioInsert','$CantidadInsert','$importeInsert','$DescricpionInsert','$descuento_por_Insert','$descuento_bob_Insert',1)");
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
                values ('$cod_facturacion','$servicioInsert_ajax','$CantidadInsert_ajax','$importeInsert_ajax','$DescricpionInsert_ajax','$descuento_por_Insert_ajax','$descuento_bob_Insert_ajax'2)";
                $stmt = $dbh->prepare($sql);                
                $flagSuccess=$stmt->execute();                
            }           
        }        
         
        
        //enviar propuestas para la actualizacion de ibnorca
         $fechaHoraActual=date("Y-m-d H:i:s");
         $idTipoObjeto=2709;
         $idObjeto=2726; //regristado
         $obs="Registro de Solicitud Facturación";
         if(isset($_POST['u'])){
            $u=$_POST['u'];
            actualizarEstadosObjetosIbnorca($idTipoObjeto,$idObjeto,$u,$cod_facturacion,$fechaHoraActual,$obs);
         }else{
           actualizarEstadosObjetosIbnorca($idTipoObjeto,$idObjeto,$globalUser,$cod_facturacion,$fechaHoraActual,$obs);
         }
        if(isset($_POST['usuario_ibnored'])){
          $q=$_POST['usuario_ibnored'];
          $s=$_POST['usuario_ibnored_s'];
          $u=$_POST['usuario_ibnored_u'];
          $v=$_POST['usuario_ibnored_v'];
          showAlertSuccessError($flagSuccess,$url_list_Solicitudfactura."&q=".$q."&s=".$s."&u=".$u."&v=".$v);
        }else{
          showAlertSuccessError($flagSuccess,$url_list_Solicitudfactura); 
        } 

        //$stmt->debugDumpParams();
    } else {//update
        //actualizamos los campos estaticos
        $stmt = $dbh->prepare("UPDATE solicitudes_facturacion set cod_unidadorganizacional='$cod_unidadorganizacional',cod_area='$cod_area',fecha_registro='$fecha_registro',fecha_solicitudfactura='$fecha_solicitudfactura',cod_tipoobjeto='$cod_tipoobjeto',cod_tipopago='$cod_tipopago',cod_cliente='$cod_cliente',cod_personal='$cod_personal',razon_social='$razon_social',nit='$nit',observaciones='$observaciones',persona_contacto='$persona_contacto'
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
                    values ('$cod_facturacion','$servicioInsert','$CantidadInsert','$importeInsert','$DescricpionInsert','$descuento_por_Insert','$descuento_bob_Insert',1)");
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
           
        }    
        if(isset($_POST['usuario_ibnored'])){
          $q=$_POST['usuario_ibnored'];
          $s=$_POST['usuario_ibnored_s'];
          $u=$_POST['usuario_ibnored_u'];
          $v=$_POST['usuario_ibnored_v'];
          showAlertSuccessError($flagSuccess,$url_list_Solicitudfactura."&q=".$q."&s=".$s."&u=".$u."&v=".$v);
        }else{
          showAlertSuccessError($flagSuccess,$url_list_Solicitudfactura); 
        }     
              
    }//si es insert o update
    
    } catch(PDOException $ex){
        //manejar error
        echo "Un error ocurrio".$ex->getMessage();

    }
?>